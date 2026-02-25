<?php
/**
 * MongoDB Atlas Configuration
 * ──────────────────────────────────────────────────────────
 * // PLACE YOUR DATABASE URL / HOST / CREDENTIALS HERE
 *
 * Fill in the MONGODB_URI with your Atlas connection string.
 * Special characters in the password must be URL-encoded:
 *   @ → %40   $ → %24   # → %23   ! → %21
 * ──────────────────────────────────────────────────────────
 */

// ── MongoDB Atlas connection string ────────────────────────
// Password "$c@n@pp" URL-encoded → "%24c%40n%40pp"
define('MONGODB_URI',   'mongodb+srv://appuser:%24c%40n%40pp@cluster0.uzdoc9v.mongodb.net/?appName=Cluster0');
define('MONGODB_DB',    'hacki_app');           // Database name
define('MONGODB_COLL',  'users');               // Collection name
// ────────────────────────────────────────────────────────────

// Load Composer autoloader (for mongodb/mongodb library)
require_once __DIR__ . '/../vendor/autoload.php';

// Create MongoDB client
try {
    $mongoClient = new MongoDB\Client(MONGODB_URI);
    $db          = $mongoClient->selectDatabase(MONGODB_DB);
    $collection  = $db->selectCollection(MONGODB_COLL);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed. Please check your configuration.'
    ]);
    exit;
}
