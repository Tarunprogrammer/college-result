<?php
// Database connection
$host = "sql313.infinityfree.com";
$username = "if0_39822519";
$password = "SivSYWBaR8";
$database = "if0_39822519_drk_syllabus";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['hall_ticket_no'])) {
    header("Location: login.php");
    exit();
}

$hall_ticket = $_SESSION['hall_ticket_no'];
$user_query = $conn->prepare("SELECT id, full_name FROM users WHERE hall_ticket_no = ?");
$user_query->bind_param("s", $hall_ticket);
$user_query->execute();
$user_result = $user_query->get_result();
$user = $user_result->fetch_assoc();
$user_id = $user['id'];
$full_name = $user['full_name'];

// MODIFIED: Added p.file_name to fetch the actual filename for direct linking
$history_query = $conn->prepare("
    SELECT 
        p.id as pdf_id, 
        p.title, 
        p.subject,
        p.file_name, 
        h.action, 
        h.timestamp 
    FROM history h
    JOIN pdf_files p ON h.pdf_id = p.id
    WHERE h.user_id = ? AND p.file_name LIKE '%.pdf'
    ORDER BY h.timestamp DESC
");
$history_query->bind_param("i", $user_id);
$history_query->execute();
$history_result = $history_query->get_result();
$history = $history_result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Activity History - DRK College</title>
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
            --light-color: #ecf0f1;
            --gray-color: #95a5a6;
            --shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            --transition: all 0.3s ease;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-image: linear-gradient(135deg, rgba(32, 32, 32, 0.95), rgba(25, 25, 25, 0.95)),
                            url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-attachment: fixed;
            color: var(--light-color);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .header {
            background: linear-gradient(135deg, rgba(44, 62, 80, 0.97), rgba(26, 38, 52, 0.97));
            color: white;
            padding: 1.2rem 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        .header img {
            height: 55px;
            border-radius: 8px;
        }
        .college-name {
            font-size: 1.5rem;
            font-weight: 700;
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
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
        }
        .main-content {
            flex: 1;
            padding: 2rem;
        }
        .page-title {
            text-align: center;
            margin-bottom: 2.5rem;
            font-size: 2.5rem;
            font-weight: 700;
        }
        .history-table {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem 1.5rem;
            text-align: left;
        }
        th {
            background-color: rgba(255, 255, 255, 0.1);
            font-weight: 600;
        }
        tr:not(:last-child) {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .action-view { color: #3498db; }
        .action-download { color: #2ecc71; }
        
        /* ADDED: Styles to make the table row look clickable */
        .clickable-row {
            transition: background-color 0.2s ease;
        }
        .clickable-row:hover {
            background-color: rgba(255, 255, 255, 0.08);
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="header-left">
        <img src="images/logo.png" alt="College Logo">
        <div class="college-name">DRK College of Engineering And Technology</div>
    </div>
    <a href="index.php" class="btn btn-primary"><i class="fas fa-home"></i> Back to Home</a>
</div>

<div class="main-content">
    <h2 class="page-title">Activity History for <?= htmlspecialchars($full_name) ?></h2>

    <div class="history-table">
        <table>
            <thead>
                <tr>
                    <th>File Title</th>
                    <th>Subject</th>
                    <th>Action</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($history) > 0): ?>
                    <?php foreach ($history as $item): ?>
                        <!-- MODIFIED: Added class and data-href for JavaScript click handling -->
                        <tr class="clickable-row" data-href="uploads/<?= htmlspecialchars($item['file_name']) ?>">
                            <td><?= htmlspecialchars($item['title']) ?></td>
                            <td><?= htmlspecialchars($item['subject']) ?></td>
                            <td class="action-<?= htmlspecialchars($item['action']) ?>">
                                <!-- MODIFIED: Removed the specific link for 'view' action -->
                                <i class="fas fa-<?= $item['action'] === 'view' ? 'eye' : 'download' ?>"></i>
                                <?= ucfirst(htmlspecialchars($item['action'])) ?>
                            </td>
                            <td><?= date('M d, Y, h:i A', strtotime($item['timestamp'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem;">No activity recorded yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ADDED: JavaScript to handle clicking on a table row -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const rows = document.querySelectorAll('.clickable-row');
        rows.forEach(row => {
            row.addEventListener('click', () => {
                const href = row.dataset.href;
                if (href) {
                    window.open(href, '_blank');
                }
            });
        });
    });
</script>

</body>
</html>
