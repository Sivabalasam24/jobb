<!DOCTYPE html>
<html>
<head>
    <title>AI Resume Job Prediction</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            text-align: center;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            background: white;
            width: 90%;
            max-width: 400px;
            margin: 20px;
            padding: 50px 30px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 28px;
        }
        
        p {
            color: #666;
            margin-bottom: 40px;
            font-size: 14px;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            margin: 15px 0;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .btn-login {
            background: #28a745;
            color: white;
        }
        
        .btn-login:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .btn-register {
            background: #007bff;
            color: white;
        }
        
        .btn-register:hover {
            background: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }
        
        .footer {
            margin-top: 30px;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 AI Resume Job Prediction</h1>
        <p>Upload your resume and get AI-powered government job recommendations</p>
        
        <a href="login.php" style="text-decoration: none;">
            <button class="btn btn-login">🔐 Student Login</button>
        </a>
        
        <a href="register.php" style="text-decoration: none;">
            <button class="btn btn-register">📝 Student Register</button>
        </a>
        
        <div class="footer">
            © 2024 AI Resume Job Prediction System
        </div>
    </div>
</body>
</html>