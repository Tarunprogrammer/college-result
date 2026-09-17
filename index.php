<?php
session_start();
// Database connection
$host = 'localhost';
$dbname = 'drk_syllabus';
$user = 'root';
$password = '';


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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>College Syllabus</title>
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

        .user-name i {
            font-size: 1rem;
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

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color), #b71c1c);
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #d32f2f, #c62828);
            transform: translateY(-1px);
        }

        .btn-accent {
            background: linear-gradient(135deg, var(--accent-color), #004d40);
            color: white;
        }

        .btn-accent:hover {
            background: linear-gradient(135deg, #00897b, #00695c);
            transform: translateY(-1px);
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

        .select-wrapper {
            position: relative;
            margin-bottom: 2rem;
            perspective: 1000px;
        }

        .select-wrapper::after {
            content: '\f107';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #ffffff;
            pointer-events: none;
            transition: transform 0.3s ease;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
        }

        .select-wrapper.active::after {
            transform: translateY(-50%) rotate(180deg);
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
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(8px);
            appearance: none;
            -webkit-appearance: none;
        }

        .select-box:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .select-box:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 20px rgba(46, 204, 113, 0.3);
            transform: translateY(-2px);
        }

        .select-box option {
            background: var(--dark-color);
            color: var(--light-color);
            padding: 12px;
        }

        .card {
            background: linear-gradient(135deg, rgba(41, 128, 185, 0.85), rgba(44, 62, 80, 0.85));
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            padding: 3rem;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
            background: linear-gradient(135deg, rgba(41, 128, 185, 0.9), rgba(44, 62, 80, 0.9));
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
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(46, 204, 113, 0.3);
            position: relative;
            overflow: hidden;
        }

        .submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                120deg,
                transparent,
                rgba(255, 255, 255, 0.3),
                transparent
            );
            transition: 0.6s;
        }

        .submit-btn:hover::before {
            left: 100%;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #27ae60, #229954);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(46, 204, 113, 0.4);
        }

        .syllabus-banners {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 3rem;
            flex-wrap: wrap;
        }

        .banner {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
            padding: 1.5rem 2.5rem;
            border-radius: 15px;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
            text-align: center;
        }

        .banner:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 30px rgba(0,0,0,0.3);
        }

        .banner i {
            margin-right: 0.8rem;
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
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

        .select-wrapper {
            opacity: 0;
            animation: fadeIn 0.4s ease-out forwards;
        }
        
        /* Animation delays for select wrappers */
        .select-wrapper:nth-child(1) { animation-delay: 0.2s; }
        .select-wrapper:nth-child(2) { animation-delay: 0.4s; }
        .select-wrapper:nth-child(3) { animation-delay: 0.6s; }
        .select-wrapper:nth-child(4) { animation-delay: 0.8s; }
        .select-wrapper:nth-child(5) { animation-delay: 1.0s; }

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

        .footer-links li {
            margin-bottom: 0.5rem;
        }

        .footer-links a {
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            padding: 0.3rem 0;
        }

        .footer-links a:hover {
            color: #2ecc71;
            transform: translateX(5px);
        }

        .copyright {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.9rem;
            color: #95a5a6;
        }

        @media (max-width: 768px) {
            .header {
                padding: 1rem;
                flex-direction: column;
                text-align: center;
            }

            .header-left {
                margin-bottom: 1rem;
            }

            .college-name {
                font-size: 1.2rem;
            }

            .card {
                padding: 2rem;
                margin: 1rem;
            }

            .page-title {
                font-size: 2rem;
            }

            .footer-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .footer-title::after {
                left: 50%;
                transform: translateX(-50%);
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
    <h2 class="page-title animated">Select Syllabus</h2>
    <form action="view_pdfs.php" method="get" class="card">
        
        <!-- Regulation Dropdown -->
        <div class="select-wrapper">
            <select name="regulation" id="regulation" class="select-box" required>
                <option value="">Select Regulation</option>
                <option value="R22">R22</option>
                <option value="R25">R25</option>
            </select>
        </div>

        <div class="select-wrapper">
            <select name="year" id="year" class="select-box" required>
                <option value="">Select Year</option>
                <option value="1st Year">1st Year</option>
                <option value="2nd Year">2nd Year</option>
                <option value="3rd Year">3rd Year</option>
                <option value="4th Year">4th Year</option>
            </select>
        </div>

        <div class="select-wrapper">
            <select name="semester" id="semester" class="select-box" required>
                <option value="">Select Semester</option>
                <option value="1st Semester">1st Semester</option>
                <option value="2nd Semester">2nd Semester</option>
            </select>
        </div>

        <div class="select-wrapper">
            <select name="department" id="department" class="select-box" required>
                <option value="">Select Department</option>
                <option value="CSE">CSE</option>
                <option value="CSC">CSC</option>
                <option value="CSD">CSD</option>
                <option value="CSM">CSM</option>
                <option value="ECE">ECE</option>
                <option value="MECH">MECH</option>
                <option value="CIVIL">CIVIL</option>
            </select>
        </div>

        <div class="select-wrapper" id="subject-group" style="display:none;">
            <select name="subject" id="subject" class="select-box" required>
                <option value="">Select Subject</option>
            </select>
        </div>

        <button class="submit-btn" type="submit">
            <i class="fas fa-book-open"></i> View Syllabus
        </button>
    </form>

    <div class="syllabus-banners">
        <a href="syllabus.php?regulation=R22" class="banner">
            <i class="fas fa-file-pdf"></i> R22 Syllabus
        </a>
        <a href="syllabus.php?regulation=R25" class="banner">
            <i class="fas fa-file-pdf"></i> R25 Syllabus
        </a>
    </div>
</div>

<script>
// Add active class to select wrapper when select is focused
document.querySelectorAll('.select-box').forEach(select => {
    select.addEventListener('focus', () => {
        select.parentElement.classList.add('active');
    });
    select.addEventListener('blur', () => {
        select.parentElement.classList.remove('active');
    });
});

// Helper to fetch subjects when any of the filters change
function fetchSubjects() {
    var regulation = document.getElementById('regulation').value;
    var year = document.getElementById('year').value;
    var semester = document.getElementById('semester').value;
    var department = document.getElementById('department').value;
    var subjectGroup = document.getElementById('subject-group');
    var subjectSelect = document.getElementById('subject');
    
    // Only fetch subjects if all filters have a value
    if (regulation && year && semester && department) {
        var xhr = new XMLHttpRequest();
        // Append all filter values to the request URL
        var url = 'get_subjects.php?year=' + encodeURIComponent(year) + 
                  '&semester=' + encodeURIComponent(semester) + 
                  '&department=' + encodeURIComponent(department) +
                  '&regulation=' + encodeURIComponent(regulation);
        
        xhr.open('GET', url, true);
        
        xhr.onload = function() {
            if (xhr.status === 200) {
                try {
                    var subjects = JSON.parse(xhr.responseText);
                    subjectSelect.innerHTML = '<option value="">Select Subject</option>'; // Reset subjects
                    
                    if (subjects.length > 0) {
                        subjects.forEach(function(subj) {
                            var opt = document.createElement('option');
                            opt.value = subj;
                            opt.textContent = subj;
                            subjectSelect.appendChild(opt);
                        });
                        subjectGroup.style.display = 'block'; // Show subject dropdown
                    } else {
                        subjectGroup.style.display = 'none'; // Hide if no subjects found
                    }
                } catch (e) {
                    console.error("Error parsing JSON response: ", e);
                    subjectGroup.style.display = 'none';
                }
            } else {
                console.error("Request failed with status: ", xhr.status);
                subjectGroup.style.display = 'none';
            }
        };
        xhr.send();
    } else {
        subjectGroup.style.display = 'none'; // Hide if not all filters are selected
        subjectSelect.innerHTML = '<option value="">Select Subject</option>'; // Reset
    }
}

// Add event listeners to all filter dropdowns
document.getElementById('regulation').addEventListener('change', fetchSubjects);
document.getElementById('year').addEventListener('change', fetchSubjects);
document.getElementById('semester').addEventListener('change', fetchSubjects);
document.getElementById('department').addEventListener('change', fetchSubjects);
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

