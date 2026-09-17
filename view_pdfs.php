<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";
$database = "drk_syllabus";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID if logged in
$user_id = null;
if (isset($_SESSION['hall_ticket_no'])) {
    $hall_ticket = $_SESSION['hall_ticket_no'];
    $user_query = $conn->prepare("SELECT id FROM users WHERE hall_ticket_no = ?");
    $user_query->bind_param("s", $hall_ticket);
    $user_query->execute();
    $user_result = $user_query->get_result();
    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
        $user_id = $user['id'];
    }
}

// Handle view or download actions
if (isset($_GET['action']) && isset($_GET['pdf_id']) && isset($_GET['file'])) {
    $action = $_GET['action'];
    $pdf_id = $_GET['pdf_id'];
    $file = $_GET['file'];

    // Log the action only if the user is logged in
    if ($user_id && ($action === 'view' || $action === 'download')) {
        $stmt = $conn->prepare("INSERT INTO history (user_id, pdf_id, action) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $pdf_id, $action);
        $stmt->execute();
    }

    // Perform the redirect or download for any user
    if ($action === 'view') {
        header("Location: view_pdf.php?file=" . urlencode($file));
        exit();
    }

    if ($action === 'download') {
        $filepath = 'uploads/' . $file;
        if (file_exists($filepath)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            flush(); // Flush system output buffer
            readfile($filepath);
            exit();
        } else {
            die('File not found.');
        }
    }
}


$pdfs = [];
$selectedYear = $_GET['year'] ?? null;
$selectedSemester = $_GET['semester'] ?? null;
$selecteddepartment = $_GET['department'] ?? null;
$searchTerm = $_GET['search'] ?? '';
$selectedSubject = $_GET['subject'] ?? null;

$pageTitle = "Syllabus for " . htmlspecialchars($selecteddepartment) . " - " . htmlspecialchars($selectedYear);

if ($selectedYear && $selectedSemester && $selecteddepartment) {
    // Modified query to also select the PDF ID
    $sql = "SELECT id, title, file_name FROM pdf_files WHERE year = ? AND semester = ? AND department = ?";
    $params = ["sss", $selectedYear, $selectedSemester, $selecteddepartment];

    if ($selectedSubject) {
        $sql .= " AND subject = ?";
        $params[0] .= "s";
        $params[] = $selectedSubject;
    }
    if ($searchTerm) {
        $sql .= " AND title LIKE ?";
        $params[0] .= "s";
        $params[] = "%" . $searchTerm . "%";
    }

    $stmt = $conn->prepare($sql);
    // Use the splat operator to bind params dynamically
    $stmt->bind_param(...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $pdfs = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Syllabus - DRK College</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --primary-dark: #1a2634;
            --secondary-color: #3498db;
            --accent-color: #2ecc71;
            --accent-dark: #27ae60;
            --danger-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
            --gray-color: #95a5a6;
            --gradient-1: linear-gradient(135deg, #00b09b, #96c93d);
            --gradient-2: linear-gradient(135deg, #667eea, #764ba2);
            --gradient-3: linear-gradient(135deg, #2193b0, #6dd5ed);
            --shadow: 0 10px 20px rgba(0, 0, 0, 0.2),
                      0 6px 6px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-image: linear-gradient(135deg, rgba(32, 32, 32, 0.95), rgba(25, 25, 25, 0.95)),
                            url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
            color: var(--light-color);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: linear-gradient(135deg, rgba(44, 62, 80, 0.97), rgba(26, 38, 52, 0.97));
            color: white;
            padding: 1.2rem 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            position: relative;
            z-index: 10;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .header img {
            height: 55px;
            width: auto;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .college-name {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn {
            padding: 0.7rem 1.4rem;
            border-radius: 0.5rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            transition: var(--transition);
            cursor: pointer;
            border: none;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            transform: translateY(-1px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
        }

        .main-content {
            flex: 1 0 auto;
            padding: 2rem;
            display: flex;
            flex-direction: column;
        }

        .page-title {
            text-align: center;
            margin-bottom: 2.5rem;
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            position: relative;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            opacity: 0;
            animation: fadeIn 0.8s ease-out forwards;
        }

        .page-title::after {
            content: '';
            display: block;
            width: 150px;
            height: 4px;
            background: linear-gradient(90deg, #2ecc71, transparent);
            margin: 1rem auto 0;
            border-radius: 2px;
        }

        .card-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .pdf-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .pdf-item {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            width: 100%;
        }

        .pdf-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        .pdf-item-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            color: var(--secondary-color);
        }

        .pdf-item-header .fa-file-pdf {
            font-size: 1.5rem;
        }
        
        .pdf-item-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .pdf-item-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-color);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .pdf-item-actions {
            display: flex;
            gap: 1rem;
            margin-top: auto;
        }

        .action-btn {
            flex: 1;
            text-align: center;
            padding: 0.8rem;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .view-btn {
            background-color: var(--secondary-color);
        }
        .view-btn:hover {
            background-color: #2980b9;
        }

        .download-btn {
            background-color: var(--accent-dark);
        }
        .download-btn:hover {
            background-color: var(--accent-color);
        }

        .empty-state {
            text-align: center;
            padding: 4rem;
            color: var(--gray-color);
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
        }
        .empty-state .fas {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .empty-state p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="header">
    <div class="header-left">
        <img src="images/logo.png" alt="College Logo">
        <div class="college-name">DRK College of Engineering And Technology</div>
    </div>
    <div class="user-info">
        <a href="index.php" class="btn btn-primary">
            <i class="fas fa-home"></i>
            <span>Back to Home</span>
        </a>
    </div>
</div>

<div class="main-content">
    <h2 class="page-title"><?= $pageTitle ?></h2>

    <div class="card-container">
        <?php if (count($pdfs) > 0): ?>
            <div class="pdf-grid">
                <?php foreach ($pdfs as $pdf): ?>
                    <article class="pdf-item">
                        <div>
                            <div class="pdf-item-header">
                                <i class="fas fa-file-pdf"></i>
                                <span>PDF Document</span>
                            </div>
                            <h3 class="pdf-item-title"><?= htmlspecialchars($pdf['title']) ?></h3>
                            <div class="pdf-item-meta">
                                <i class="fas fa-calendar-alt"></i>
                                <time datetime="<?= date('Y-m-d') ?>"><?= date('M d, Y') ?></time>
                            </div>
                        </div>
                        <div class="pdf-item-actions">
                            <!-- Modified Links to include action and pdf_id for logging -->
                            <a href="view_pdfs.php?action=view&pdf_id=<?= $pdf['id'] ?>&file=<?= urlencode($pdf['file_name']) ?>" 
                               class="action-btn view-btn" target="_blank">
                                <i class="fas fa-eye"></i>
                                <span>View</span>
                            </a>
                            <a href="view_pdfs.php?action=download&pdf_id=<?= $pdf['id'] ?>&file=<?= urlencode($pdf['file_name']) ?>" 
                               class="action-btn download-btn">
                                <i class="fas fa-download"></i>
                                <span>Download</span>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-search"></i>
                <p>No syllabus documents found for the selected criteria.</p>
                <a href="index.php" class="btn btn-primary">Return to Home</a>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
