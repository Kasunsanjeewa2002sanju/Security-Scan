<?php
/**
 * Registration Endpoint — MongoDB Atlas
 * ──────────────────────────────────────
 * Accepts POST JSON { email, password }
 * Validates input, checks for duplicates, stores user in Atlas.
 */

// Enable error reporting for debugging, but ensure it doesn't break JSON
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't show HTML errors

header('Content-Type: application/json');

// Global error handler to ensure we always return JSON
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo json_encode(['success' => false, 'message' => "PHP Error [$errno]: $errstr in $errfile:$errline"]);
    exit;
});

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_COMPILE_ERROR)) {
        echo json_encode(['success' => false, 'message' => "Fatal Error: {$error['message']} in {$error['file']}:{$error['line']}"]);
    }
});

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Load DB connection ($collection is available after this)
require_once __DIR__ . '/config.php';

// ─── Read & decode JSON body ────────────────────────────────
$rawInput = file_get_contents('php://input');
$data     = json_decode($rawInput, true);

$email    = trim($data['email']    ?? '');
$password = $data['password']      ?? '';

// ─── Server-side validation ─────────────────────────────────
if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
    exit;
}

// ─── Check for duplicate email ──────────────────────────────
try {
    $existing = $collection->findOne(['email' => $email]);

    if ($existing !== null) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'This email is already registered.']);
        exit;
    }

    // ─── Insert new user ────────────────────────────────────
    // Password is stored as plain text per project requirements.
    $result = $collection->insertOne([
        'email'      => $email,
        'password'   => $password,
        'created_at' => new MongoDB\BSON\UTCDateTime(),
    ]);

    if ($result->getInsertedCount() === 1) {
        http_response_code(201);
        echo json_encode(['success' => true, 'message' => 'Account created successfully!']);
    } else {
        throw new Exception('Insert failed');
    }

} catch (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
    http_response_code(503);
    echo json_encode(['success' => false, 'message' => 'Could not reach the database. Please try again.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error. Please try again later.']);
}
