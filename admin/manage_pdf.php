<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "drk_syllabus";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("SELECT file_name FROM pdf_files WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($file);
    $stmt->fetch();
    $stmt->close();

    // Delete file from uploads folder
    if ($file && file_exists("../uploads/$file")) {
        unlink("../uploads/$file");
    }

    // Delete record from database
    $del = $conn->prepare("DELETE FROM pdf_files WHERE id = ?");
    $del->bind_param("i", $id);
    $del->execute();
    header("Location: manage_pdf.php");
    exit();
}

$result = $conn->query("SELECT * FROM pdf_files ORDER BY uploaded_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage PDFs</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 95%;
            margin: 30px auto;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        th, td {
            padding: 14px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        a.btn {
            text-decoration: none;
            padding: 8px 12px;
            margin: 2px;
            color: white;
            border-radius: 6px;
            font-size: 14px;
        }
        
        /* Important to set color for edit button text */
        a.btn.btn-edit {
             color: black;
        }

        .btn-view { background-color: #28a745; }
        .btn-edit { background-color: #ffc107; }
        .btn-delete { background-color: #dc3545; }

        a.btn:hover {
            opacity: 0.85;
        }

        .empty {
            text-align: center;
            padding: 20px;
            font-size: 18px;
            color: #777;
        }
    </style>
</head>
<body>

<h2>📚 Uploaded PDFs</h2>

<?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Regulation</th>
            <th>Department</th>
            <th>Subject</th>
            <th>Year</th>
            <th>Semester</th>
            <th>Uploaded At</th>
            <th>Actions</th>
        </tr>
        <?php $i = 1; while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['regulation']) ?></td>
                <td><?= htmlspecialchars($row['department']) ?></td>
                <td><?= htmlspecialchars($row['subject']) ?></td>
                <td><?= htmlspecialchars($row['year']) ?></td>
                <td><?= htmlspecialchars($row['semester']) ?></td>
                <td><?= date('d M Y, h:i A', strtotime($row['uploaded_at'])) ?></td>
                <td>
                    <a class="btn btn-view" href="../uploads/<?= urlencode($row['file_name']) ?>" target="_blank">View</a>
                    <a class="btn btn-edit" href="edit_pdf.php?id=<?= $row['id'] ?>">Edit</a>
                    <a class="btn btn-delete" href="manage_pdf.php?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this PDF?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <div class="empty">No PDFs uploaded yet.</div>
<?php endif; ?>

</body>
</html>

