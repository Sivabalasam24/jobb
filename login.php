<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Include database connection
include "db.php";

// Check if database connection is successful
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$error = '';

if(isset($_POST['login'])){

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if(empty($email) || empty($password)){
        $error = "Please enter both email and password";
    } else {
        
        // Use query to check user
        $sql = "SELECT * FROM students WHERE email = '$email'";
        $result = $conn->query($sql);
        
        // Check if query executed successfully
        if(!$result){
            $error = "Database query error: " . $conn->error;
        }
        elseif($result->num_rows > 0){
            $row = $result->fetch_assoc();
            
            // Debug: Check if password field exists
            if(!isset($row['password'])){
                $error = "Password field not found in database!";
            }
            // Verify password
            elseif(password_verify($password, $row['password'])){
                
                // Set session variables
                $_SESSION['student_id'] = $row['id'];
                $_SESSION['student_name'] = $row['name'];
                $_SESSION['student_email'] = $row['email'];
                
                // Debug: Check if session is set
                error_log("Login successful for: " . $email);
                
                // Redirect to academic marks page
                header("Location: academic_marks.php");
                exit();
            } else {
                $error = "Invalid password! Please try again.";
                // Debug: Log failed password attempt
                error_log("Failed password attempt for: " . $email);
            }
        } else {
            $error = "Email not found! Please register first.";
            error_log("Email not found: " . $email);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - Academic Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Your existing CSS here */
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
            background: white;
            width: 100%;
            max-width: 420px;
            padding: 40px 35px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 0.8s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            color: #333;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 5px;
            text-align: center;
        }

        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .error {
            background: #fee;
            border-left: 4px solid #f44336;
            color: #c62828;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 16px;
        }

        input {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s;
            background: #f8f9fa;
        }

        input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            font-size: 18px;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .register-section {
            text-align: center;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 2px solid #f0f0f0;
        }

        .register-link {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s;
        }

        .register-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(76, 175, 80, 0.4);
        }

        .back-home {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #999;
            text-decoration: none;
            font-size: 13px;
        }

        .back-home:hover {
            color: #667eea;
        }

        /* Debug Info */
        .debug-info {
            background: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔐 Student Login</h2>
        <p class="subtitle">Login to access your academic dashboard</p>

        <?php if($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <div class="form-group">
                <label>Email Address</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope input-icon"></i>
                    <input 
                        type="email" 
                        name="email" 
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                        required 
                        placeholder="Enter your email"
                    >
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock input-icon"></i>
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        required 
                        placeholder="Enter your password"
                    >
                    <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
                </div>
                <div style="text-align: right; margin-top: 5px;">
                    <a href="forgot_password.php" style="color: #667eea; font-size: 13px; text-decoration: none;">Forgot Password?</a>
                </div>
            </div>

            <button type="submit" name="login" class="login-btn">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </button>
        </form>

        <div class="register-section">
            <p>Don't have an account?</p>
            <a href="register.php" class="register-link">
                <i class="fas fa-user-plus"></i>
                Register Now
            </a>
        </div>

        <a href="index.php" class="back-home">
            <i class="fas fa-arrow-left"></i>
            Back to Home
        </a>

        <!-- Debug Information (Remove in production) -->
        <div class="debug-info">
            <strong>Debug Info:</strong><br>
            <?php
            echo "Database Connected: " . ($conn ? "Yes" : "No") . "<br>";
            echo "Session Status: " . (session_status() == PHP_SESSION_ACTIVE ? "Active" : "Not Active") . "<br>";
            echo "PHP Version: " . phpversion() . "<br>";
            ?>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>