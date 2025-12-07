<?php
/**
 * eJSIS Photo Upload Handler
 * Handles secure photo uploads for JSIS records
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Configuration
$uploadDir = __DIR__ . '/uploads/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$maxFileSize = 10 * 1024 * 1024; // 10MB

// Ensure upload directory exists
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Check for required fields
if (!isset($_POST['record_id']) || !isset($_POST['photo_type'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing record_id or photo_type']);
    exit;
}

$recordId = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['record_id']);
$photoType = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['photo_type']);

// Validate photo_type
$validTypes = ['outdoor', 'indoor', 'additional'];
if (!in_array($photoType, $validTypes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid photo_type']);
    exit;
}

// Check if file was uploaded
if (!isset($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'No file uploaded']);
    exit;
}

$file = $_FILES['photo'];

// Check for upload errors
if ($file['error'] !== UPLOAD_ERR_OK) {
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'File exceeds server limit',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds form limit',
        UPLOAD_ERR_PARTIAL => 'File only partially uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temp folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write to disk',
        UPLOAD_ERR_EXTENSION => 'Upload blocked by extension'
    ];
    $errorMsg = $errorMessages[$file['error']] ?? 'Unknown upload error';
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
    exit;
}

// Validate file size
if ($file['size'] > $maxFileSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'File too large (max 10MB)']);
    exit;
}

// Validate MIME type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid file type. Allowed: JPG, PNG, GIF, WebP']);
    exit;
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$extension = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $extension));
if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
    $extension = 'jpg';
}

// For additional photos, add timestamp to make unique
if ($photoType === 'additional') {
    $filename = "{$recordId}_{$photoType}_" . time() . ".{$extension}";
} else {
    $filename = "{$recordId}_{$photoType}.{$extension}";
}

$destination = $uploadDir . $filename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $destination)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save file']);
    exit;
}

// Return success with file reference
echo json_encode([
    'success' => true,
    'filename' => $filename,
    'photo_type' => $photoType,
    'size' => $file['size']
]);
