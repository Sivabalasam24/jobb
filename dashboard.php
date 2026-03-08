<?php
session_start();
include "db.php";

// Check if user is logged in
if(!isset($_SESSION['student']) && !isset($_SESSION['student_name']) && !isset($_SESSION['student_id'])){
    header("Location: login.php");
    exit();
}

// Get student info from session
$student_name = $_SESSION['student'] ?? $_SESSION['student_name'] ?? 'Student';
$student_id = $_SESSION['student_id'] ?? '';

// Fetch student data from database
if($student_id){
    $sql = "SELECT * FROM students WHERE id = '$student_id'";
} else {
    $sql = "SELECT * FROM students WHERE name = '$student_name'";
}

$result = $conn->query($sql);
$student_data = null;
if($result && $result->num_rows > 0){
    $student_data = $result->fetch_assoc();
}

// Get current date and time
$current_time = date('H');
$greeting = '';
if($current_time < 12){
    $greeting = "Good Morning";
} elseif($current_time < 17){
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}

// Get random motivational quote
$quotes = [
    "The future belongs to those who believe in the beauty of their dreams. - Eleanor Roosevelt",
    "Success is not final, failure is not fatal: it is the courage to continue that counts. - Winston Churchill",
    "Your time is limited, don't waste it living someone else's life. - Steve Jobs",
    "The only way to do great work is to love what you do. - Steve Jobs",
    "Don't watch the clock; do what it does. Keep going. - Sam Levenson",
    "The harder you work for something, the greater you'll feel when you achieve it.",
    "Dream it. Wish it. Do it. - Unknown",
    "Success doesn't come from what you do occasionally, but what you do consistently."
];
$random_quote = $quotes[array_rand($quotes)];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Academic Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        /* Navbar Styles */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 24px;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo i {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 28px;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 5px 15px;
            background: #f8f9fa;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .user-profile:hover {
            background: #e9ecef;
            transform: translateY(-2px);
        }

        .avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .user-info {
            text-align: left;
        }

        .user-name {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .user-role {
            font-size: 11px;
            color: #666;
        }

        .logout-btn {
            padding: 10px 20px;
            background: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            font-weight: 500;
        }

        .logout-btn:hover {
            background: #d32f2f;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(244, 67, 54, 0.3);
        }

        /* Main Container */
        .main-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Welcome Section */
        .welcome-section {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent 30%, rgba(102, 126, 234, 0.03) 50%, transparent 70%);
            animation: shine 8s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        .welcome-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 30px;
            position: relative;
            z-index: 1;
        }

        .welcome-text {
            flex: 1;
        }

        .welcome-text h1 {
            font-size: 36px;
            color: #333;
            margin-bottom: 10px;
        }

        .welcome-text h1 span {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-text p {
            color: #666;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .quote-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            border-left: 4px solid #667eea;
            max-width: 400px;
        }

        .quote-box i {
            color: #667eea;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .quote-box p {
            font-style: italic;
            color: #555;
            line-height: 1.6;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s;
            animation: fadeInUp 0.5s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .stat-icon i {
            font-size: 24px;
            color: white;
        }

        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .stat-progress {
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            overflow: hidden;
        }

        .stat-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 2px;
            transition: width 1s ease;
        }

        /* Menu Grid */
        .menu-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .menu-title h2 {
            color: white;
            font-size: 24px;
        }

        .menu-title i {
            color: white;
            font-size: 28px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .menu-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            text-decoration: none;
            color: inherit;
            transition: all 0.3s;
            animation: slideInRight 0.5s ease;
            position: relative;
            overflow: hidden;
        }

        .menu-card:nth-child(1) { animation-delay: 0.1s; }
        .menu-card:nth-child(2) { animation-delay: 0.2s; }
        .menu-card:nth-child(3) { animation-delay: 0.3s; }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .menu-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .menu-card::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(102, 126, 234, 0.1);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .menu-card:hover::after {
            width: 300px;
            height: 300px;
        }

        .card-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            position: relative;
            z-index: 1;
        }

        .card-icon i {
            font-size: 30px;
            color: white;
        }

        .menu-card h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #333;
            position: relative;
            z-index: 1;
        }

        .menu-card p {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .card-badge {
            padding: 5px 10px;
            background: #e8f5e9;
            color: #4caf50;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .card-arrow {
            color: #667eea;
            font-size: 20px;
            transition: transform 0.3s;
        }

        .menu-card:hover .card-arrow {
            transform: translateX(5px);
        }

        /* Quick Actions */
        .quick-actions {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease;
        }

        .quick-actions h3 {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #333;
            margin-bottom: 20px;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 15px;
            background: #f8f9fa;
            border: 2px solid transparent;
            border-radius: 10px;
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .action-btn:hover {
            border-color: #667eea;
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .action-btn i {
            color: #667eea;
            font-size: 18px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                padding: 15px;
                flex-direction: column;
                gap: 15px;
            }

            .user-menu {
                width: 100%;
                justify-content: space-between;
            }

            .welcome-content {
                flex-direction: column;
                text-align: center;
            }

            .quote-box {
                max-width: 100%;
            }

            .menu-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-graduation-cap"></i>
            <span>Student Portal</span>
        </div>
        <div class="user-menu">
            <div class="user-profile" onclick="toggleProfileMenu()">
                <div class="avatar">
                    <?php echo strtoupper(substr($student_name, 0, 1)); ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($student_name); ?></div>
                    <div class="user-role">Student</div>
                </div>
                <i class="fas fa-chevron-down" style="color: #666; font-size: 12px;"></i>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>

    <div class="main-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-content">
                <div class="welcome-text">
                    <h1><?php echo $greeting; ?>, <span><?php echo htmlspecialchars($student_name); ?></span>!</h1>
                    <p>Welcome back to your academic dashboard. Track your progress and explore opportunities.</p>
                    <div class="quote-box">
                        <i class="fas fa-quote-left"></i>
                        <p><?php echo $random_quote; ?></p>
                    </div>
                </div>
                <div style="text-align: center;">
                    <i class="fas fa-chart-line" style="font-size: 120px; color: rgba(102, 126, 234, 0.2);"></i>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-pdf"></i>
                </div>
                <div class="stat-value">1</div>
                <div class="stat-label">Resume Uploaded</div>
                <div class="stat-progress">
                    <div class="stat-progress-bar" style="width: 100%"></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div class="stat-value">
                    <?php 
                    if($student_data && isset($student_data['overall_mark'])){
                        echo round($student_data['overall_mark']) . '%';
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </div>
                <div class="stat-label">Overall Marks</div>
                <div class="stat-progress">
                    <div class="stat-progress-bar" style="width: <?php echo ($student_data['overall_mark'] ?? 0); ?>%"></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <div class="stat-value">3</div>
                <div class="stat-label">Pending Tasks</div>
                <div class="stat-progress">
                    <div class="stat-progress-bar" style="width: 60%"></div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="stat-value">5</div>
                <div class="stat-label">Upcoming Exams</div>
                <div class="stat-progress">
                    <div class="stat-progress-bar" style="width: 75%"></div>
                </div>
            </div>
        </div>

        <!-- Main Menu Grid -->
        <div class="menu-title">
            <i class="fas fa-th-large"></i>
            <h2>Quick Access Menu</h2>
        </div>

        <div class="menu-grid">
            <!-- Resume Upload Card -->
            <a href="upload.php" class="menu-card">
                <div class="card-icon">
                    <i class="fas fa-file-upload"></i>
                </div>
                <h3>Upload Resume</h3>
                <p>Upload and manage your resume. Keep it updated for job applications and opportunities.</p>
                <div class="card-footer">
                    <span class="card-badge">
                        <i class="fas fa-cloud-upload-alt"></i> Upload Now
                    </span>
                    <i class="fas fa-arrow-right card-arrow"></i>
                </div>
            </a>

            <!-- Academic Marks Card -->
            <a href="academic_marks.php" class="menu-card">
                <div class="card-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Update Academic Marks</h3>
                <p>Enter your academic marks to get AI-powered career predictions and personalized advice.</p>
                <div class="card-footer">
                    <span class="card-badge">
                        <i class="fas fa-robot"></i> AI Analysis
                    </span>
                    <i class="fas fa-arrow-right card-arrow"></i>
                </div>
            </a>

            <!-- View Analysis Card -->
            <a href="academic_result.php" class="menu-card">
                <div class="card-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <h3>View Academic Analysis</h3>
                <p>Check your performance dashboard with visual charts and detailed insights.</p>
                <div class="card-footer">
                    <span class="card-badge">
                        <i class="fas fa-eye"></i> View Dashboard
                    </span>
                    <i class="fas fa-arrow-right card-arrow"></i>
                </div>
            </a>
        </div>

        <!-- Quick Actions Section -->
        <div class="quick-actions">
            <h3>
                <i class="fas fa-bolt" style="color: #667eea;"></i>
                Quick Actions
            </h3>
            <div class="action-buttons">
                <a href="Updated resume_upload.php" class="action-btn">
                    <i class="fas fa-file-pdf"></i>
                    Update Resume
                </a>
                <a href="academic_marks.php" class="action-btn">
                    <i class="fas fa-pen"></i>
                    Enter Marks
                </a>
                <a href="academic_result.php" class="action-btn">
                    <i class="fas fa-chart-simple"></i>
                    View Results
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-bell"></i>
                    Notifications
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-question-circle"></i>
                    Help Center
                </a>
                <a href="#" class="action-btn">
                    <i class="fas fa-cog"></i>
                    Settings
                </a>
            </div>
        </div>
    </div>

    <!-- Add this before closing body tag -->
    <div class="profile-menu" id="profileMenu" style="display: none;">
        <!-- Profile dropdown content -->
    </div>

    <script>
        // Toggle profile menu
        function toggleProfileMenu() {
            // Implement profile menu toggle if needed
            console.log('Profile menu clicked');
        }

        // Add loading animation for stats
        document.addEventListener('DOMContentLoaded', function() {
            // Animate progress bars
            setTimeout(() => {
                document.querySelectorAll('.stat-progress-bar').forEach(bar => {
                    const width = bar.style.width;
                    bar.style.width = '0';
                    setTimeout(() => {
                        bar.style.width = width;
                    }, 100);
                });
            }, 500);
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>