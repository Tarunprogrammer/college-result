<?php
// Ensure the filename parameter exists
if (!isset($_GET['file'])) {
    die('No file specified');
}

$filename = basename($_GET['file']); // Get base name to prevent directory traversal
$filepath = __DIR__ . '/uploads/' . $filename;

// Security checks
if (!file_exists($filepath)) {
    die('File not found');
}

// Check if it's actually a PDF
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $filepath);
finfo_close($finfo);

if ($mimeType !== 'application/pdf') {
    die('Invalid file type');
}

// Set headers for PDF display
header('Content-Type: application/pdf');
header('Content-Length: ' . filesize($filepath));
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Cache-Control: public, must-revalidate, max-age=0');
header('Pragma: public');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

// Output the file
readfile($filepath);
exit; 