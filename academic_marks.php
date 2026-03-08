<?php
session_start();
include "db.php";

// Check login
if(!isset($_SESSION['student_email'])){
    header("Location: login.php");
    exit();
}

$student_email = $_SESSION['student_email'];
$student_name = $_SESSION['student_name'];

$message="";
$message_type="";
$advice=[];
$show_form=true;

// Fetch student data
$sql="SELECT * FROM students WHERE email='$student_email'";
$result=$conn->query($sql);

if($result && $result->num_rows>0){
    $student_data=$result->fetch_assoc();
    $existing_tenth=$student_data['tenth_mark'];
    $existing_twelfth=$student_data['twelfth_mark'];
    $existing_college=$student_data['college_mark'];
}else{
    $existing_tenth="";
    $existing_twelfth="";
    $existing_college="";
}

// Save marks
if(isset($_POST['save'])){
    $tenth=$_POST['tenth'];
    $twelfth=$_POST['twelfth'];
    $college=$_POST['college'];
    
    $overall=round(($tenth+$twelfth+$college)/3,2);
    
    $stmt=$conn->prepare("UPDATE students SET tenth_mark=?,twelfth_mark=?,college_mark=?,overall_mark=? WHERE email=?");
    $stmt->bind_param("dddds",$tenth,$twelfth,$college,$overall,$student_email);
    
    if($stmt->execute()){
        $message="Marks Updated Successfully!";
        $message_type="success";
        $show_form=false;
        $advice=generateAIAdvice($overall,$tenth,$twelfth,$college);
    }else{
        $message="Error updating marks";
        $message_type="error";
    }
    $stmt->close();
}

// AI advice function
function generateAIAdvice($overall,$tenth,$twelfth,$college){
    $advice=[];
    
    if($overall>=85){
        $advice[]=["🎯 UPSC Civil Services","Excellent marks! You have great potential for civil services. Start preparing for the prestigious UPSC exams.","https://www.upsc.gov.in", "#FF6B6B"];
        $advice[]=["🚀 ISRO Scientist","Your academic excellence makes you perfect for research careers at ISRO.","https://www.isro.gov.in", "#4ECDC4"];
    } elseif($overall>=70) {
        $advice[]=["📊 SSC CGL","Good marks! Prepare for SSC CGL with focus on reasoning and aptitude.","https://ssc.nic.in", "#45B7D1"];
        $advice[]=["🏦 Bank PO","Banking sector offers great opportunities. Start preparing for PO exams.","https://www.ibps.in", "#96CEB4"];
    } elseif($overall>=60) {
        $advice[]=["💼 Bank Clerk","Banking is a stable career option. Start preparing for clerk exams.","https://www.ibps.in", "#FFEAA7"];
        $advice[]=["🚂 Railway Jobs","Railway recruitment exams are a great option. Start your preparation.","https://www.rrbcdg.gov.in", "#DDA0DD"];
    } else {
        $advice[]=["📚 Skill Development","Focus on improving skills through specialized training programs.","https://www.nielit.gov.in", "#F08080"];
        $advice[]=["💡 Vocational Training","Consider vocational courses for better career opportunities.","https://www.msde.gov.in", "#9ACD32"];
    }
    
    return $advice;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Marks | Student Dashboard</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 700px;
        }

        .card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2em;
            margin-bottom: 10px;
        }

        .header i {
            font-size: 3em;
            margin-bottom: 15px;
        }

        .welcome-badge {
            background: rgba(255,255,255,0.2);
            display: inline-block;
            padding: 8px 20px;
            border-radius: 50px;
            margin-top: 10px;
        }

        .welcome-badge i {
            font-size: 1em;
            margin-right: 8px;
        }

        .content {
            padding: 30px;
        }

        .message {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border-left: 5px solid #28a745;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 5px solid #dc3545;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 0.95em;
        }

        .form-group label i {
            margin-right: 8px;
            color: #667eea;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .input-wrapper input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s;
            background: #f8f9fa;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }

        .input-wrapper input:hover {
            border-color: #999;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102,126,234,0.4);
        }

        .btn-primary i {
            font-size: 1.1em;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .advice-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }

        .advice-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 5px solid;
            animation: fadeIn 0.5s ease;
        }

        .advice-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .advice-card h3 {
            margin-bottom: 10px;
            font-size: 1.3em;
        }

        .advice-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .advice-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: gap 0.3s;
        }

        .advice-link:hover {
            gap: 12px;
            color: #764ba2;
        }

        .overall-score {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 25px;
        }

        .overall-score h2 {
            font-size: 2.5em;
            margin-bottom: 5px;
        }

        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #e0e0e0;
        }

        .stats-preview {
            display: flex;
            justify-content: space-around;
            margin-bottom: 25px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 1.5em;
            font-weight: bold;
            color: #667eea;
        }

        .stat-label {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }

        @media (max-width: 600px) {
            .content {
                padding: 20px;
            }
            
            .advice-grid {
                grid-template-columns: 1fr;
            }
            
            .button-group {
                flex-direction: column;
            }
            
            .stats-preview {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <i class="fas fa-graduation-cap"></i>
                <h1>Academic Marks</h1>
                <div class="welcome-badge">
                    <i class="fas fa-user"></i>
                    Welcome, <?php echo htmlspecialchars($student_name); ?>
                </div>
            </div>

            <div class="content">
                <?php if($message != ""): ?>
                    <div class="message <?php echo $message_type; ?>">
                        <i class="fas <?php echo $message_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <?php if(!$show_form && isset($overall)): ?>
                    <div class="overall-score">
                        <i class="fas fa-star" style="font-size: 2em; margin-bottom: 10px;"></i>
                        <h2><?php echo $overall; ?>%</h2>
                        <p>Your Overall Performance</p>
                    </div>
                <?php endif; ?>

                <?php if($show_form): ?>
                    <?php if($existing_tenth || $existing_twelfth || $existing_college): ?>
                        <div class="stats-preview">
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $existing_tenth; ?>%</div>
                                <div class="stat-label">10th Marks</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $existing_twelfth; ?>%</div>
                                <div class="stat-label">12th Marks</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $existing_college; ?>%</div>
                                <div class="stat-label">College Marks</div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="form-group">
                            <label for="tenth">
                                <i class="fas fa-school"></i> 10th Marks (%)
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-percent"></i>
                                <input type="number" id="tenth" name="tenth" 
                                       value="<?php echo $existing_tenth; ?>" 
                                       required min="0" max="100" step="0.01"
                                       placeholder="Enter your 10th percentage">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="twelfth">
                                <i class="fas fa-university"></i> 12th Marks (%)
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-percent"></i>
                                <input type="number" id="twelfth" name="twelfth" 
                                       value="<?php echo $existing_twelfth; ?>" 
                                       required min="0" max="100" step="0.01"
                                       placeholder="Enter your 12th percentage">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="college">
                                <i class="fas fa-graduation-cap"></i> College Marks (%)
                            </label>
                            <div class="input-wrapper">
                                <i class="fas fa-percent"></i>
                                <input type="number" id="college" name="college" 
                                       value="<?php echo $existing_college; ?>" 
                                       required min="0" max="100" step="0.01"
                                       placeholder="Enter your college percentage">
                            </div>
                        </div>

                        <button type="submit" name="save" class="btn-primary">
                            <i class="fas fa-save"></i> Save Marks
                        </button>
                    </form>
                <?php endif; ?>

                <?php if(!empty($advice)): ?>
                    <h2 style="margin: 25px 0 15px; color: #333;">
                        <i class="fas fa-lightbulb" style="color: #ffc107; margin-right: 10px;"></i>
                        Personalized Career Advice
                    </h2>
                    <div class="advice-grid">
                        <?php foreach($advice as $a): ?>
                            <div class="advice-card" style="border-left-color: <?php echo $a[3]; ?>">
                                <h3><?php echo $a[0]; ?></h3>
                                <p><?php echo $a[1]; ?></p>
                                <a href="<?php echo $a[2]; ?>" target="_blank" class="advice-link">
                                    Learn More <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="button-group">
                    <a href="dashboard.php" class="btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                    <?php if(!$show_form): ?>
                        <a href="marks.php" class="btn-secondary">
                            <i class="fas fa-edit"></i> Update Marks
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="footer">
                <p style="color: #666;">
                    <i class="fas fa-chart-line"></i> Track your academic progress and get personalized career advice
                </p>
            </div>
        </div>
    </div>
</body>
</html>