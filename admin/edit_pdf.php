<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "drk_syllabus";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    die("No PDF selected to edit.");
}
$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM pdf_files WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$pdf = $result->fetch_assoc();

if (!$pdf) {
    die("PDF not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve all form fields, including the new regulation field
    $title = $_POST["title"];
    $regulation = $_POST["regulation"]; // Added regulation
    $year = $_POST["year"];
    $semester = $_POST["semester"];
    $department = $_POST["department"];
    $subject = $_POST["subject"];
    $file_name = $pdf['file_name']; // Default: keep old file

    // File upload logic (remains the same)
    if (isset($_FILES["pdf_file"]) && $_FILES["pdf_file"]["error"] === 0) {
        $newFileName = time() . "_" . basename($_FILES["pdf_file"]["name"]);
        $targetFile = "../uploads/" . $newFileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if ($fileType != "pdf") {
            die("Only PDF files are allowed.");
        }

        if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $targetFile)) {
            $oldFile = "../uploads/" . $pdf['file_name'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
            $file_name = $newFileName;
        } else {
            die("Failed to upload new PDF.");
        }
    }

    // Update the SQL query to include the regulation
    $update = $conn->prepare("UPDATE pdf_files SET title = ?, regulation = ?, year = ?, semester = ?, department = ?, subject = ?, file_name = ? WHERE id = ?");
    // Update bind_param to include the new regulation string ('s')
    $update->bind_param("sssssssi", $title, $regulation, $year, $semester, $department, $subject, $file_name, $id);
    $update->execute();

    header("Location: manage_pdf.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit PDF</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --success-color: #2ecc71;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem;
        }

        .form-container {
            width: 100%;
            max-width: 550px;
            background: white;
            padding: 40px 50px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--dark-color);
            font-weight: 600;
            font-size: 1.8rem;
        }

        label {
            font-weight: 500;
            margin-bottom: 8px;
            display: block;
            color: #555;
            font-size: 0.95rem;
        }

        input[type="text"], 
        select, 
        input[type="file"] {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            transition: var(--transition);
            font-size: 1rem;
            background-color: #f9f9f9;
        }

        input[type="text"]:focus,
        select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.2);
            background-color: #fff;
            outline: none;
        }
        
        input[type="file"] {
            cursor: pointer;
        }
        
        input[type="file"]::-webkit-file-upload-button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: var(--transition);
        }
        
        input[type="file"]::-webkit-file-upload-button:hover {
            background: #2980b9;
        }

        input[type="submit"] {
            background: linear-gradient(135deg, var(--success-color), #27ae60);
            color: white;
            border: none;
            margin-top: 15px;
            padding: 15px;
            width: 100%;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
        }

        input[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(46, 204, 113, 0.3);
        }

        a.back {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 25px;
            color: #555;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        a.back:hover {
            color: var(--primary-color);
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit PDF Details</h2>

    <form method="POST" enctype="multipart/form-data">
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($pdf['title']) ?>" required>
        
        <label for="regulation">Select Regulation</label>
        <select id="regulation" name="regulation" required>
            <?php
            $regulations = ["R22", "R25"];
            foreach ($regulations as $reg) {
                $selected = ($pdf['regulation'] == $reg) ? " selected" : "";
                echo "<option value='$reg'$selected>$reg</option>";
            }
            ?>
        </select>

        <label for="year">Select Year</label>
        <select id="year" name="year" required>
            <?php
            $years = ["1st Year", "2nd Year", "3rd Year", "4th Year"];
            foreach ($years as $year) {
                echo "<option value='$year'" . ($pdf['year'] == $year ? " selected" : "") . ">$year</option>";
            }
            ?>
        </select>

        <label for="semester">Select Semester</label>
        <select id="semester" name="semester" required>
            <?php
            $semesters = ["1st Semester", "2nd Semester"];
            foreach ($semesters as $sem) {
                echo "<option value='$sem'" . ($pdf['semester'] == $sem ? " selected" : "") . ">$sem</option>";
            }
            ?>
        </select>

        <label for="department">Select Department</label>
        <select id="department" name="department" required>
            <?php
            $departments = ["CSE", "CSC", "CSD", "CSM", "ECE", "MECH", "CIVIL"];
            foreach ($departments as $dept) {
                echo "<option value='$dept'" . ($pdf['department'] == $dept ? " selected" : "") . ">$dept</option>";
            }
            ?>
        </select>

        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" value="<?= htmlspecialchars($pdf['subject']) ?>" required>

        <label for="pdf_file">Replace PDF (optional)</label>
        <input type="file" id="pdf_file" name="pdf_file" accept="application/pdf">

        <input type="submit" value="Update PDF">
    </form>

    <a href="manage_pdf.php" class="back">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Manage PDFs</span>
    </a>
</div>

</body>
</html>

