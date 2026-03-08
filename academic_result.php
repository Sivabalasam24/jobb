<?php
session_start();
include "db.php";

// Check if user is logged in
if(!isset($_SESSION['student_id']) && !isset($_SESSION['student'])){
    header("Location: login.php");
    exit();
}

// Get student info from session
$student_id = $_SESSION['student_id'] ?? '';
$student_name = $_SESSION['student_name'] ?? $_SESSION['student'] ?? '';

// Fetch student data
if($student_id){
    $sql = "SELECT * FROM students WHERE id = '$student_id'";
} else {
    $sql = "SELECT * FROM students WHERE name = '$student_name'";
}

$result = $conn->query($sql);

if($result && $result->num_rows > 0){
    $row = $result->fetch_assoc();
    
    // Set default values if marks not set
    $tenth_mark = isset($row['tenth_mark']) ? round($row['tenth_mark']) : 0;
    $twelfth_mark = isset($row['twelfth_mark']) ? round($row['twelfth_mark']) : 0;
    $college_mark = isset($row['college_mark']) ? round($row['college_mark']) : 0;
    $overall_mark = isset($row['overall_mark']) ? round($row['overall_mark']) : 0;
} else {
    // Default values if student not found
    $tenth_mark = 0;
    $twelfth_mark = 0;
    $college_mark = 0;
    $overall_mark = 0;
    $student_name = $student_name ?: 'Student';
}

// Calculate AI prediction based on marks
function getCareerPrediction($marks) {
    if($marks >= 85) return "Excellent! You're eligible for top government exams like UPSC, IES, and scientific positions.";
    if($marks >= 70) return "Good performance! You can target exams like SSC CGL, Banking, and State PSCs.";
    if($marks >= 55) return "Fair performance! Focus on exams like RRB, Banking, and other departmental exams.";
    return "Keep working hard! Start with entry-level government exams and improve your preparation.";
}

$prediction = getCareerPrediction($overall_mark);

// Calculate performance trend
$trend = [];
$trend['tenth_to_twelfth'] = $twelfth_mark - $tenth_mark;
$trend['twelfth_to_college'] = $college_mark - $twelfth_mark;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Dashboard - <?php echo htmlspecialchars($student_name); ?></title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        }
        
        /* Navbar */
        .navbar {
            background: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            flex-wrap: wrap;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-avatar {
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
        
        .user-details {
            text-align: right;
        }
        
        .user-name {
            font-weight: 600;
            color: #333;
        }
        
        .user-role {
            font-size: 12px;
            color: #666;
        }
        
        .logout-btn {
            padding: 8px 20px;
            background: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .logout-btn:hover {
            background: #d32f2f;
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
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            animation: slideDown 0.5s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .welcome-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .welcome-title h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .welcome-title p {
            color: #666;
            font-size: 16px;
        }
        
        .ai-prediction-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 15px;
            max-width: 400px;
        }
        
        .ai-prediction-card i {
            font-size: 30px;
            margin-bottom: 10px;
        }
        
        .ai-prediction-card p {
            font-size: 14px;
            line-height: 1.6;
            opacity: 0.9;
        }
        
        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            animation: fadeIn 0.5s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .card-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }
        
        .card-header h3 {
            color: #333;
            font-size: 18px;
            font-weight: 600;
        }
        
        .card-header p {
            color: #666;
            font-size: 13px;
            margin-top: 5px;
        }
        
        /* Score Gauge Container */
        .gauge-container {
            position: relative;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
        }
        
        /* Progress Bars */
        .marks-grid {
            display: grid;
            gap: 20px;
        }
        
        .mark-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 12px;
        }
        
        .mark-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        
        .progress {
            background: #e0e0e0;
            border-radius: 10px;
            height: 12px;
            overflow: hidden;
        }
        
        .progress-bar {
            height: 100%;
            border-radius: 10px;
            transition: width 1s ease;
            position: relative;
            animation: fillBar 1.5s ease;
        }
        
        @keyframes fillBar {
            from { width: 0; }
        }
        
        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shine 2s infinite;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%); }
            20% { transform: translateX(100%); }
            100% { transform: translateX(100%); }
        }
        
        .mark-value {
            text-align: right;
            margin-top: 5px;
            font-weight: 600;
            color: #333;
        }
        
        /* Trend Indicators */
        .trend-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .trend-up {
            color: #4caf50;
        }
        
        .trend-down {
            color: #f44336;
        }
        
        .trend-stable {
            color: #ff9800;
        }
        
        /* Statistics Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        
        .stat-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 12px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 12px;
        }
        
        /* Recommendation List */
        .recommendation-list {
            list-style: none;
            margin-top: 20px;
        }
        
        .recommendation-list li {
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #555;
        }
        
        .recommendation-list li i {
            color: #4caf50;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar {
                padding: 15px;
            }
            
            .welcome-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .ai-prediction-card {
                max-width: 100%;
            }
            
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo">
            <i class="fas fa-graduation-cap"></i> Academic Portal
        </div>
        <div class="user-menu">
            <div class="user-info">
                <div class="user-details">
                    <div class="user-name"><?php echo htmlspecialchars($student_name); ?></div>
                    <div class="user-role">Student</div>
                </div>
                <div class="user-avatar">
                    <?php echo strtoupper(substr($student_name, 0, 1)); ?>
                </div>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>
    
    <div class="main-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-header">
                <div class="welcome-title">
                    <h1>Welcome back, <?php echo htmlspecialchars($student_name); ?>! 👋</h1>
                    <p>Here's your academic performance overview and career predictions</p>
                </div>
                <div class="ai-prediction-card">
                    <i class="fas fa-robot"></i>
                    <h4>AI Career Prediction</h4>
                    <p><?php echo $prediction; ?></p>
                </div>
            </div>
        </div>
        
        <!-- Dashboard Grid -->
        <div class="dashboard-grid">
            <!-- Score Gauge Card -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div>
                        <h3>AI Academic Score</h3>
                        <p>Overall performance indicator</p>
                    </div>
                </div>
                <div class="gauge-container">
                    <canvas id="scoreGauge"></canvas>
                </div>
            </div>
            
            <!-- Performance Stats Card -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <h3>Performance Stats</h3>
                        <p>Quick statistics overview</p>
                    </div>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $overall_mark; ?>%</div>
                        <div class="stat-label">Overall</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $tenth_mark; ?>%</div>
                        <div class="stat-label">10th</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $twelfth_mark; ?>%</div>
                        <div class="stat-label">12th</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?php echo $college_mark; ?>%</div>
                        <div class="stat-label">College</div>
                    </div>
                </div>
                
                <!-- Trend Indicator -->
                <?php if($trend['tenth_to_twelfth'] != 0 || $trend['twelfth_to_college'] != 0): ?>
                <div class="trend-indicator">
                    <i class="fas <?php 
                        echo ($trend['tenth_to_twelfth'] + $trend['twelfth_to_college']) > 0 ? 'fa-arrow-up trend-up' : 
                            (($trend['tenth_to_twelfth'] + $trend['twelfth_to_college']) < 0 ? 'fa-arrow-down trend-down' : 'fa-minus trend-stable'); 
                    ?>"></i>
                    <span>
                        <?php
                        $total_trend = $trend['tenth_to_twelfth'] + $trend['twelfth_to_college'];
                        if($total_trend > 0){
                            echo "Showing improvement trend (+$total_trend%)";
                        } elseif($total_trend < 0){
                            echo "Showing decline trend ($total_trend%)";
                        } else {
                            echo "Consistent performance";
                        }
                        ?>
                    </span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Marks Comparison Chart -->
        <div class="card" style="margin-bottom: 30px;">
            <div class="card-header">
                <div class="card-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <div>
                    <h3>Academic Performance Comparison</h3>
                    <p>Your marks across different levels</p>
                </div>
            </div>
            <canvas id="marksChart" style="max-height: 400px;"></canvas>
        </div>
        
        <!-- Detailed Marks and Recommendations -->
        <div class="dashboard-grid">
            <!-- Detailed Marks Card -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div>
                        <h3>Detailed Marks</h3>
                        <p>Subject-wise performance</p>
                    </div>
                </div>
                
                <div class="marks-grid">
                    <div class="mark-item">
                        <div class="mark-label">
                            <span>10th Standard</span>
                            <span><?php echo $tenth_mark; ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?php echo $tenth_mark; ?>%; background: <?php echo $tenth_mark >= 75 ? '#4caf50' : ($tenth_mark >= 60 ? '#ff9800' : '#f44336'); ?>"></div>
                        </div>
                    </div>
                    
                    <div class="mark-item">
                        <div class="mark-label">
                            <span>12th Standard</span>
                            <span><?php echo $twelfth_mark; ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?php echo $twelfth_mark; ?>%; background: <?php echo $twelfth_mark >= 75 ? '#4caf50' : ($twelfth_mark >= 60 ? '#ff9800' : '#f44336'); ?>"></div>
                        </div>
                    </div>
                    
                    <div class="mark-item">
                        <div class="mark-label">
                            <span>College/Graduation</span>
                            <span><?php echo $college_mark; ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?php echo $college_mark; ?>%; background: <?php echo $college_mark >= 75 ? '#4caf50' : ($college_mark >= 60 ? '#ff9800' : '#f44336'); ?>"></div>
                        </div>
                    </div>
                    
                    <div class="mark-item">
                        <div class="mark-label">
                            <span>Overall Score</span>
                            <span><?php echo $overall_mark; ?>%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?php echo $overall_mark; ?>%; background: <?php echo $overall_mark >= 75 ? '#4caf50' : ($overall_mark >= 60 ? '#ff9800' : '#f44336'); ?>"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Career Recommendations Card -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <div>
                        <h3>Career Recommendations</h3>
                        <p>Based on your performance</p>
                    </div>
                </div>
                
                <ul class="recommendation-list">
                    <?php if($overall_mark >= 80): ?>
                        <li><i class="fas fa-check-circle"></i> UPSC Civil Services (IAS/IPS/IFS)</li>
                        <li><i class="fas fa-check-circle"></i> Engineering Services (IES/ISS)</li>
                        <li><i class="fas fa-check-circle"></i> Scientific Positions (DRDO/ISRO/BARC)</li>
                        <li><i class="fas fa-check-circle"></i> RBI Grade B Officer</li>
                    <?php elseif($overall_mark >= 70): ?>
                        <li><i class="fas fa-check-circle"></i> SSC CGL (Group A & B)</li>
                        <li><i class="fas fa-check-circle"></i> Bank PO (IBPS/SBI)</li>
                        <li><i class="fas fa-check-circle"></i> State PSC Examinations</li>
                        <li><i class="fas fa-check-circle"></i> Indian Railways (Group A)</li>
                    <?php elseif($overall_mark >= 60): ?>
                        <li><i class="fas fa-check-circle"></i> Banking Exams (Clerk/PO)</li>
                        <li><i class="fas fa-check-circle"></i> RRB NTPC/Group D</li>
                        <li><i class="fas fa-check-circle"></i> SSC CHSL/MTS</li>
                        <li><i class="fas fa-check-circle"></i> State Level Exams</li>
                    <?php else: ?>
                        <li><i class="fas fa-check-circle"></i> Focus on improving marks</li>
                        <li><i class="fas fa-check-circle"></i> Start with entry-level exams</li>
                        <li><i class="fas fa-check-circle"></i> Consider skill development courses</li>
                        <li><i class="fas fa-check-circle"></i> Prepare for competitive exams</li>
                    <?php endif; ?>
                </ul>
                
                <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <p style="color: #666; font-size: 13px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-lightbulb" style="color: #ff9800;"></i>
                        <strong>Pro Tip:</strong> Consistent practice and focused preparation can improve your chances by up to 40%
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    // Score Gauge Chart
    const score = <?php echo $overall_mark; ?>;
    
    // Center text plugin
    const centerText = {
        id: 'centerText',
        beforeDraw(chart) {
            const {width, height, ctx} = chart;
            
            ctx.restore();
            ctx.font = 'bold 32px Arial';
            ctx.textBaseline = 'middle';
            ctx.textAlign = 'center';
            ctx.fillStyle = '#333';
            ctx.fillText(score + '%', width/2, height/2 - 10);
            
            ctx.font = '12px Arial';
            ctx.fillStyle = '#666';
            ctx.fillText('Overall Score', width/2, height/2 + 20);
            ctx.save();
        }
    };
    
    new Chart(document.getElementById('scoreGauge'), {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [score, 100 - score],
                backgroundColor: [
                    score >= 75 ? '#4caf50' : (score >= 60 ? '#ff9800' : '#f44336'),
                    '#e0e0e0'
                ],
                borderWidth: 0,
                borderRadius: 10
            }]
        },
        options: {
            cutout: '70%',
            rotation: -90,
            circumference: 180,
            animation: {
                animateRotate: true,
                duration: 2000
            },
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            }
        },
        plugins: [centerText]
    });
    
    // Marks Comparison Chart
    new Chart(document.getElementById('marksChart'), {
        type: 'bar',
        data: {
            labels: ['10th Standard', '12th Standard', 'College/Graduation', 'Overall'],
            datasets: [{
                label: 'Marks Percentage',
                data: [
                    <?php echo $tenth_mark; ?>,
                    <?php echo $twelfth_mark; ?>,
                    <?php echo $college_mark; ?>,
                    <?php echo $overall_mark; ?>
                ],
                backgroundColor: [
                    'rgba(76, 175, 80, 0.8)',
                    'rgba(33, 150, 243, 0.8)',
                    'rgba(255, 152, 0, 0.8)',
                    'rgba(156, 39, 176, 0.8)'
                ],
                borderColor: [
                    '#4caf50',
                    '#2196f3',
                    '#ff9800',
                    '#9c27b0'
                ],
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Marks: ${context.raw}%`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    },
                    title: {
                        display: true,
                        text: 'Percentage (%)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    
    // Add loading animation for progress bars
    document.addEventListener('DOMContentLoaded', function() {
        const bars = document.querySelectorAll('.progress-bar');
        bars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => {
                bar.style.width = width;
            }, 100);
        });
    });
    </script>
</body>
</html>