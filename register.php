<?php
include "db.php";

$message = "";
$message_type = "";

if(isset($_POST['register'])){

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$qualification = $_POST['qualification'];
$skills = $_POST['skills'];

$sql = "INSERT INTO students(name,email,password,qualification,skills)
VALUES('$name','$email','$password','$qualification','$skills')";

if($conn->query($sql)){
    $message = "✅ Registration Successful! Redirecting to login...";
    $message_type = "success";
}else{
    $message = "❌ Error occurred while registering!";
    $message_type = "error";
}

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Registration</title>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Arial;
}

body{
height:100vh;
display:flex;
justify-content:center;
align-items:center;
background:linear-gradient(135deg,#667eea,#764ba2);
}

.container{
background:white;
padding:40px;
width:420px;
border-radius:12px;
box-shadow:0 10px 25px rgba(0,0,0,0.2);
text-align:center;
}

h2{
margin-bottom:20px;
color:#333;
}

.input-box{
margin-bottom:15px;
text-align:left;
}

label{
font-size:14px;
font-weight:bold;
color:#555;
}

input,textarea{
width:100%;
padding:10px;
margin-top:5px;
border:1px solid #ccc;
border-radius:6px;
outline:none;
transition:0.3s;
}

input:focus,textarea:focus{
border-color:#667eea;
box-shadow:0 0 5px rgba(102,126,234,0.5);
}

button{
width:100%;
padding:12px;
margin-top:15px;
border:none;
border-radius:6px;
background:#667eea;
color:white;
font-size:16px;
cursor:pointer;
transition:0.3s;
}

button:hover{
background:#5a67d8;
}

.login-link{
margin-top:15px;
font-size:14px;
}

.login-link a{
color:#667eea;
text-decoration:none;
font-weight:bold;
}

.login-link a:hover{
text-decoration:underline;
}

/* Success and Error Message */

.message{
padding:12px;
margin-bottom:15px;
border-radius:6px;
font-size:14px;
}

.success{
background:#d4edda;
color:#155724;
border:1px solid #c3e6cb;
}

.error{
background:#f8d7da;
color:#721c24;
border:1px solid #f5c6cb;
}

</style>

</head>

<body>

<div class="container">

<h2>🎓 Student Registration</h2>

<?php if($message!=""){ ?>

<div class="message <?php echo $message_type; ?>">
<?php echo $message; ?>
</div>

<?php } ?>

<form method="POST">

<div class="input-box">
<label>Name</label>
<input type="text" name="name" required>
</div>

<div class="input-box">
<label>Email</label>
<input type="email" name="email" required>
</div>

<div class="input-box">
<label>Password</label>
<input type="password" name="password" required>
</div>

<div class="input-box">
<label>Qualification</label>
<input type="text" name="qualification">
</div>

<div class="input-box">
<label>Skills</label>
<textarea name="skills" rows="3"></textarea>
</div>

<button type="submit" name="register">Register</button>

</form>

<div class="login-link">
Already have an account? <a href="login.php">Login</a>
</div>

</div>

<?php
if($message_type=="success"){
echo "<script>
setTimeout(function(){
window.location='login.php';
},2000);
</script>";
}
?>

</body>
</html>