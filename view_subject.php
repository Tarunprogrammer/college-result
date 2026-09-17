<?php
session_start();
// Database connection
$host = "sql113.infinityfree.com";
$username = "if0_39827071";
$password = "WQRGj5ycg0";
$database = "if0_39827071_btech_notes";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$year = $_GET['year'] ?? '';
$semester = $_GET['semester'] ?? '';
$department = $_GET['department'] ?? '';

$subjects = [];

// Check if 2nd Year, 2nd Semester, CSE is selected
if ($year == '2nd Year' && $semester == '2nd Semester' && $department == 'CSE') {
    $subjects = ['Database Management System (DBMS)', 'Software Engineering (SE)', 'Business Economics and Financial Analysis (BEFA)', 'Operating Systems (OS)', 'Discrete Mathematics (DM)'];
} else {
    // Otherwise fetch from database
    if ($year && $semester && $department) {
        $stmt = $conn->prepare("SELECT DISTINCT subject FROM pdf_files WHERE year = ? AND semester = ? AND department = ?");
        $stmt->bind_param("sss", $year, $semester, $department);
        $stmt->execute();
        $result = $stmt->get_result();

        while($row = $result->fetch_assoc()) {
            $subjects[] = $row['subject'];
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subjects List</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
<div class="overlay">
    <div class="header">
        <div>DRK College of Engineering And Technology</div>
        <a class="btn" href="index.php"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <div class="main-content">
        <div class="card">
            <h2>Subjects for <?= htmlspecialchars($year) ?> - <?= htmlspecialchars($semester) ?> - <?= htmlspecialchars($department) ?></h2>

            <?php if (count($subjects) > 0): ?>
                <ul>
                    <?php foreach ($subjects as $subject): ?>
                        <li>
                            <i class="fas fa-book"></i>
                            <a href="view_pdf.php?subject=<?= urlencode($subject) ?>&year=<?= urlencode($year) ?>&semester=<?= urlencode($semester) ?>&department=<?= urlencode($department) ?>" style="text-decoration: none; color: inherit;">
                                <?= htmlspecialchars($subject) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p style="text-align:center;">No subjects found for the selected criteria.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer">
        <p>&copy; <?= date('Y') ?> DRK College of Engineering And Technology. All rights reserved.</p>
    </div>
</div>
</body>
</html>
