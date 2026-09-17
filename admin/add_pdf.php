<?php
// Database connection
$host = "sql313.infinityfree.com";
$username = "if0_39822519";
$password = "SivSYWBaR8";
$database = "if0_39822519_drk_syllabus";


$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve all form data
    $title = $_POST["title"];
    $year = $_POST["year"];
    $semester = $_POST["semester"];
    $department = $_POST["department"];
    $regulation = $_POST["regulation"]; // <-- Added regulation
    $subject = $_POST["subject"];
    $pdf = $_FILES["pdf_file"];

    // Check if the uploaded file is a PDF
    if ($pdf["type"] == "application/pdf") {
        $fileName = basename($pdf["name"]);
        $targetDir = "../uploads/";
        $targetFile = $targetDir . $fileName;

        // Create the uploads directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($pdf["tmp_name"], $targetFile)) {
            // Prepare the SQL statement to insert data into the database
            // Updated SQL query to include the 'regulation' column
            $stmt = $conn->prepare("INSERT INTO pdf_files (title, file_name, year, semester, department, regulation, subject) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            // Updated bind_param to include the regulation variable (7 strings)
            $stmt->bind_param("sssssss", $title, $fileName, $year, $semester, $department, $regulation, $subject);
            
            // Execute the statement and set a success message
            if ($stmt->execute()) {
                 $message = "✅ PDF uploaded successfully!";
            } else {
                $message = "❌ Error: " . $stmt->error;
            }
            $stmt->close();

        } else {
            $message = "❌ Failed to upload PDF file.";
        }
    } else {
        $message = "❌ Only PDF files are allowed.";
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Syllabus PDF</title>
    <style>
        /* General body styling */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            animation: fadeIn 1s ease-in-out;
        }

        /* Fade-in animation for the body */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Main container for the form */
        .container {
            max-width: 550px;
            width: 100%;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            padding: 35px 45px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Form title styling */
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 2rem;
            font-weight: 700;
        }

        /* Label styling */
        label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            color: #4a5568;
        }

        /* Styling for text inputs, select dropdowns, and file input */
        input[type="text"],
        select,
        input[type="file"] {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-sizing: border-box;
            transition: all 0.3s ease;
            background-color: #f7fafc;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
            font-size: 16px;
        }

        /* Focus state for inputs */
        input[type="text"]:focus,
        select:focus,
        input[type="file"]:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 0 3px rgba(106, 17, 203, 0.15);
            background-color: #fff;
            outline: none;
        }

        /* Submit button styling */
        input[type="submit"] {
            background: linear-gradient(to right, #ff512f, #dd2476);
            color: white;
            padding: 14px;
            width: 100%;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.3s, background 0.3s;
        }

        /* Hover effect for the submit button */
        input[type="submit"]:hover {
            background: linear-gradient(to right, #dd2476, #ff512f);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        /* Message box for success or error feedback */
        .message {
            text-align: center;
            margin-bottom: 20px;
            padding: 12px 16px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }

        /* Error-specific styling for the message box */
        .message.error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Upload Syllabus PDF</h2>

    <?php if (!empty($message)): ?>
        <div class="message <?= str_contains($message, '❌') ? 'error' : '' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form action="add_pdf.php" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="year">Select Year:</label>
        <select id="year" name="year" required>
            <option value="">-- Select Year --</option>
            <option value="1st Year">1st Year</option>
            <option value="2nd Year">2nd Year</option>
            <option value="3rd Year">3rd Year</option>
            <option value="4th Year">4th Year</option>
        </select>

        <label for="semester">Select Semester:</label>
        <select id="semester" name="semester" required>
            <option value="">-- Select Semester --</option>
            <option value="1st Semester">1st Semester</option>
            <option value="2nd Semester">2nd Semester</option>
        </select>

        <label for="department">Select Department:</label>
        <select id="department" name="department" required>
            <option value="">-- Select Department --</option>
            <option value="CSE">CSE</option>
            <option value="CSC">CSC</option>
            <option value="CSD">CSD</option>
            <option value="CSM">CSM</option>
            <option value="ECE">ECE</option>
            <option value="MECH">MECH</option>
            <option value="CIVIL">CIVIL</option>
        </select>

        <!-- New Regulation Field -->
        <label for="regulation">Select Regulation:</label>
        <select id="regulation" name="regulation" required>
            <option value="">-- Select Regulation --</option>
            <option value="R22">R22</option>
            <option value="R25">R25</option>
        </select>
        <!-- End of New Field -->

        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" required>

        <label for="pdf_file">Select PDF:</label>
        <input type="file" id="pdf_file" name="pdf_file" accept="application/pdf" required>

        <input type="submit" value="Upload PDF">
    </form>
</div>

</body>
</html>
