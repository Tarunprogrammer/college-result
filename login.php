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

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $hall_ticket = $_POST["hall_ticket"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE hall_ticket_no = ?");
    $stmt->bind_param("s", $hall_ticket);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['hall_ticket_no'] = $hall_ticket;
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Hall ticket number not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DRK College</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
            justify-content: center;
            align-items: center;
        }

        .page-title {
            text-align: center;
            margin-bottom: 2.5rem;
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            position: relative;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        
        .card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            padding: 2.5rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 450px;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            color: white;
            text-align: center;
        }

        .alert-error {
            background-color: rgba(231, 76, 60, 0.8);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--light-color);
        }

        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(0, 0, 0, 0.2);
            color: white;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.5);
        }

        .submit-btn {
            width: 100%;
            padding: 0.9rem;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--accent-color), var(--accent-dark));
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.7rem;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, var(--accent-dark), var(--accent-color));
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
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

            .main-content {
                padding: 1rem;
            }

            .page-title {
                font-size: 2rem;
            }
            
            .card {
                padding: 2rem;
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
        <a class="btn btn-primary" href="index.php"><i class="fas fa-home"></i> Home</a>
    </div>
</div>

<div class="main-content">
    <h2 class="page-title">Student Login</h2>
    
    <div class="card">
        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label class="form-label">Hall Ticket Number</label>
                <input type="text" name="hall_ticket" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>

            <div style="margin-top: 20px; text-align: center;">
                <p>Don't have an account? <a href="register.php" style="color: var(--accent-color); text-decoration: none;">Register here</a></p>
            </div>
        </form>
    </div>
</div>

</body>
</html>
