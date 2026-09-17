<?php
// Set header to return JSON content
header('Content-Type: application/json');

// Database connection details
$host = "localhost";
$username = "root";
$password = "";
$database = "drk_syllabus";

// Establish database connection
$conn = new mysqli($host, $username, $password, $database);

// Check for connection errors
if ($conn->connect_error) {
    // Return a JSON error message and exit
    echo json_encode(['error' => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Check if all required parameters are set in the GET request
if (isset($_GET['regulation'], $_GET['year'], $_GET['semester'], $_GET['department'])) {
    
    // Sanitize input parameters
    $regulation = $_GET['regulation'];
    $year = $_GET['year'];
    $semester = $_GET['semester'];
    $department = $_GET['department'];

    // Prepare SQL query to fetch distinct subjects based on all filters
    $stmt = $conn->prepare("SELECT DISTINCT subject FROM pdf_files WHERE regulation = ? AND year = ? AND semester = ? AND department = ? ORDER BY subject ASC");
    
    // Bind parameters to the prepared statement
    $stmt->bind_param("ssss", $regulation, $year, $semester, $department);
    
    // Execute the query
    $stmt->execute();
    
    // Get the result set
    $result = $stmt->get_result();
    
    $subjects = [];
    // Fetch all matching subjects into an array
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row['subject'];
    }
    
    // Close the statement
    $stmt->close();
    
    // Return the subjects as a JSON array
    echo json_encode($subjects);

} else {
    // If parameters are missing, return an empty JSON array
    echo json_encode([]);
}

// Close the database connection
$conn->close();
?>

