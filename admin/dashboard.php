<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_name'])) {
    header("Location: login.php");
    exit();
}

$adminName = $_SESSION['admin_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /*
        ============================================
        Modern Admin Dashboard CSS
        ============================================
        */

        /* Root variables for a consistent color palette */
        :root {
            --primary-color: #6a11cb;
            --secondary-color: #2575fc;
            --background-color: #f0f2f5;
            --card-background: rgba(255, 255, 255, 0.9);
            --text-color: #333;
            --header-text-color: #ffffff;
            --shadow-color: rgba(0, 0, 0, 0.1);
            --glow-color: rgba(37, 117, 252, 0.5);
        }

        /* General body styling */
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            background-attachment: fixed;
            color: var(--text-color);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header styles */
        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: var(--header-text-color);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px var(--shadow-color);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header h4 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
        }

        /* Logout button styling */
        .logout-btn {
            background: linear-gradient(45deg, #ff416c, #ff4b2b);
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 50px;
            transition: all 0.3s ease-in-out;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(255, 65, 108, 0.4);
        }

        .logout-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 6px 20px rgba(255, 65, 108, 0.6);
        }

        /* Main content container */
        .container-box {
            max-width: 600px;
            margin: auto; /* Centers the box vertically and horizontally */
            padding: 40px;
            background: var(--card-background);
            box-shadow: 0 8px 32px 0 var(--shadow-color);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.18);
            transform: translateY(-20px);
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        .container-box h2 {
            margin-bottom: 30px;
            font-size: 2.5rem;
            color: var(--primary-color);
            font-weight: 600;
        }

        /* General button styling */
        .btn {
            display: inline-block;
            width: 220px;
            margin: 15px;
            padding: 15px;
            font-size: 1rem;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease-in-out;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .btn:before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300%;
            height: 300%;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            transform: translate(-50%, -50%) scale(0);
            transition: transform 0.5s ease;
            z-index: -1;
        }
        
        .btn:hover:before {
            transform: translate(-50%, -50%) scale(1);
        }

        .btn:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 10px 20px var(--glow-color);
        }

        /* Specific button styles */
        .btn-add {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-manage {
            background: linear-gradient(45deg, #00c6ff, #0072ff);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Responsive design for mobile devices */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                padding: 15px;
            }

            .header h4 {
                margin-bottom: 10px;
            }
            
            .container-box {
                width: 90%;
                margin: 40px auto;
                padding: 25px;
            }

            .container-box h2 {
                font-size: 2rem;
            }

            .btn {
                width: 100%;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>

    <header class="header">
        <h4>Welcome, <?php echo htmlspecialchars($adminName); ?></h4>
        <a href="logout.php" class="logout-btn">Logout</a>
    </header>

    <main class="container-box">
        <h2>Admin Dashboard</h2>
        <div>
            <a href="add_pdf.php" class="btn btn-add">Add PDF</a>
            <a href="manage_pdf.php" class="btn btn-manage">Manage PDF</a>
        </div>
    </main>

</body>
</html>
