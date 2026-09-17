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

$full_name = "";

if (isset($_SESSION['hall_ticket_no'])) {
    $hall_ticket = $_SESSION['hall_ticket_no'];
    $query = $conn->prepare("SELECT full_name FROM users WHERE hall_ticket_no = ?");
    $query->bind_param("s", $hall_ticket);
    $query->execute();
    $result = $query->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $full_name = $user['full_name'];
    }
}

// Get regulation from URL, ensure it's valid
$regulation = isset($_GET['regulation']) ? htmlspecialchars($_GET['regulation']) : '';
if ($regulation !== 'R22' && $regulation !== 'R25') {
    // Redirect to home page if regulation is missing or invalid
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $regulation ?> Syllabus Selection</title>
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
            min-height: 100vh;
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
        .user-name {
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
        }
        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #b71c1c);
            color: white;
        }
        .btn-danger:hover {
            background: linear-gradient(135deg, #d32f2f, #c62828);
        }
        .btn-accent {
            background: linear-gradient(135deg, var(--accent-color), #004d40);
            color: white;
        }
        .btn-accent:hover {
            background: linear-gradient(135deg, #00897b, #00695c);
        }
        .main-content {
            flex: 1;
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
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
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
        .card {
            background: linear-gradient(135deg, rgba(41, 128, 185, 0.85), rgba(44, 62, 80, 0.85));
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            padding: 3rem;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .select-wrapper {
            position: relative;
            margin-bottom: 2rem;
        }
        .select-box {
            width: 100%;
            padding: 1.2rem 1.5rem;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 1.1rem;
            cursor: pointer;
            backdrop-filter: blur(8px);
            appearance: none;
            -webkit-appearance: none;
        }
        .select-box option {
            background: var(--dark-color);
            color: var(--light-color);
        }
        .submit-btn {
            width: 100%;
            padding: 1.2rem;
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            font-size: 1.1rem;
            text-transform: uppercase;
        }
        .footer {
            background: linear-gradient(135deg, rgba(44, 62, 80, 0.97), rgba(26, 38, 52, 0.97));
            color: var(--light-color);
            text-align: center;
            padding: 2rem;
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            text-align: left;
        }
        .footer-column {
            margin-bottom: 1rem;
        }
        .footer-title {
            color: #2ecc71;
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 1.2rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        .footer-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background: linear-gradient(90deg, #2ecc71, transparent);
        }
        .footer-links {
            list-style: none;
        }
        .footer-links li a {
            color: #ecf0f1;
            text-decoration: none;
        }
        .copyright {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.9rem;
            color: #95a5a6;
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
        <?php if ($full_name): ?>
            <span class="user-name"><i class="fas fa-user"></i> <?= htmlspecialchars($full_name) ?></span>
            <a class="btn btn-primary" href="history.php"><i class="fas fa-poll"></i> History</a>
            <a class="btn btn-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a class="btn btn-primary" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
        <?php endif; ?>
        <a class="btn btn-accent" href="admin/login.php"><i class="fas fa-lock"></i> Admin</a>
    </div>
</div>

<div class="main-content">
    <h2 class="page-title">View <?= $regulation ?> Syllabus</h2>
    <div class="card">
        <form id="syllabus-form">
            <div class="select-wrapper">
                <select id="department" class="select-box" required>
                    <option value="">Select Department</option>
                    <option value="CSE">CSE</option>
                    <option value="AIML">AIML</option>
                    <option value="CSC">CSC</option>
                    <option value="CSD">CSD</option>
                    <option value="ECE">ECE</option>
                    <option value="MECH">MECH</option>
                    <option value="EEE">EEE</option>
                </select>
            </div>
            <button class="submit-btn" type="submit">
                <i class="fas fa-book-open"></i> View Syllabus
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('syllabus-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const department = document.getElementById('department').value;
    const regulation = '<?= $regulation ?>'; // Get regulation from PHP
    
    if (department) {
        // NOTE: This assumes your PDF files are named like "r22cse.pdf" or "r25ece.pdf"
        const pdfUrl = `uploads/${regulation.toLowerCase()}${department.toLowerCase()}.pdf`;
        window.open(pdfUrl, '_blank');
    } else {
        // This is a fallback, but the "required" attribute on the select should prevent this.
        alert('Please select a department.');
    }
});
</script>

<div class="footer">
    <div class="footer-content">
        <div class="footer-column">
            <h3 class="footer-title">DRK College of Engineering</h3>
            <p>123 College Road, Hyderabad</p>
            <p>Telangana - 500001</p>
        </div>
        <div class="footer-column">
            <h3 class="footer-title">Quick Links</h3>
            <ul class="footer-links">
                <li><a href="#">Home</a></li>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Academics</a></li>
                <li><a href="#">Admissions</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h3 class="footer-title">Contact</h3>
            <ul class="footer-links">
                <li><a href="mailto:info@drkcet.edu"><i class="fas fa-envelope mr-2"></i> info@drkcet.edu</a></li>
                <li><a href="tel:+919876543210"><i class="fas fa-phone mr-2"></i> +91 98765 43210</a></li>
                <li><a href="#"><i class="fas fa-map-marker-alt mr-2"></i> Campus Map</a></li>
            </ul>
        </div>
    </div>
    <div class="copyright">
        <p>&copy; <?= date('Y') ?> DRK College of Engineering And Technology. All rights reserved.</p>
    </div>
</div>
</body>
</html>


