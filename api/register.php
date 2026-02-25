<?php
/**
 * Registration Endpoint — MongoDB Atlas
 * ──────────────────────────────────────
 * Accepts POST JSON { email, password }
 * Validates input, checks for duplicates, stores user in Atlas.
 */

header('Content-Type: application/json');

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
