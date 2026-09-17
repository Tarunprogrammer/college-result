<?php
session_start();

// --- Configuration ---
$host = "sql313.infinityfree.com";
$username = "if0_39822519";
$password = "SivSYWBaR8";
$database = "if0_39822519_drk_syllabus";

// 1. Validate Input
if (!isset($_GET['action']) || !in_array($_GET['action'], ['view', 'download']) || !isset($_GET['pdf_id']) || !isset($_GET['file'])) {
    http_response_code(400);
    die('Error: Invalid or missing parameters.');
}

// 2. Sanitize Input
$action = $_GET['action'];
$pdf_id = (int)$_GET['pdf_id'];
$filename = basename($_GET['file']);
$filepath = __DIR__ . '/uploads/' . $filename;

// 3. Log the Action (Database Interaction)
// This block connects, logs, and immediately disconnects.
if (isset($_SESSION['hall_ticket_no'])) {
    $conn = new mysqli($host, $username, $password, $database);

    if (!$conn->connect_error) {
        $hall_ticket = $_SESSION['hall_ticket_no'];
        $user_id = null;

        $user_query = $conn->prepare("SELECT id FROM users WHERE hall_ticket_no = ?");
        if ($user_query) {
            $user_query->bind_param("s", $hall_ticket);
            $user_query->execute();
            $user_result = $user_query->get_result();
            if ($user_result->num_rows > 0) {
                $user = $user_result->fetch_assoc();
                $user_id = $user['id'];
            }
            $user_query->close();
        }

        if ($user_id) {
            $stmt = $conn->prepare("INSERT INTO history (user_id, pdf_id, action) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("iis", $user_id, $pdf_id, $action);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        // **CRITICAL STEP**: Close the connection BEFORE serving the file.
        $conn->close();
    }
}

// 4. Serve the File
if (!file_exists($filepath) || strtolower(pathinfo($filepath, PATHINFO_EXTENSION)) !== 'pdf') {
    http_response_code(404);
    die('Error: The requested file could not be found or is not a valid PDF.');
}

if (ob_get_level()) {
    ob_end_clean();
}

$disposition = ($action === 'view') ? 'inline' : 'attachment';

header('Content-Type: application/pdf');
header('Content-Disposition: ' . $disposition . '; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($filepath);
exit;

