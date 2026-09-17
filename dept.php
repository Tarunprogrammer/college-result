<?php
$year = $_GET['year'] ?? null;
if (!$year) {
    // Redirect if year not provided
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Department</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h2>Select Department for <?= htmlspecialchars($year) ?></h2>

<div class="departments">
    <?php
    $departments = ["CSE", "CSC", "CSD", "CSM", "ECE", "MECH", "CIVIL"];
    foreach ($departments as $dept) {
        echo "<a href='semester.php?year=" . urlencode($year) . "&department=$dept'><button>$dept</button></a>";
    }
    ?>
</div>

</body>
</html>
