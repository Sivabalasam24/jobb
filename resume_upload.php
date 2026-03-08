<?php
session_start();
include "db.php";

$message = "";
$skills = [];
$jobs = [];

/* JOB SKILL MAP */
$job_map = [
    "python" => ["Data Analyst", "https://www.ncs.gov.in"],
    "sql" => ["Database Assistant", "https://www.ncs.gov.in"],
    "java" => ["Programmer", "https://ssc.nic.in"],
    "c" => ["Programmer", "https://ssc.nic.in"],
    "c++" => ["Software Developer", "https://ssc.nic.in"],
    "machine learning" => ["AI Research Assistant", "https://www.drdo.gov.in"],
    "data analysis" => ["Data Analyst", "https://www.ncs.gov.in"],
    "html" => ["Web Developer", "https://www.nielit.gov.in"],
    "css" => ["Web Designer", "https://www.nielit.gov.in"],
    "javascript" => ["Frontend Developer", "https://www.nielit.gov.in"],
    "php" => ["Web Developer", "https://www.nielit.gov.in"],
    "iot" => ["IoT Specialist", "https://www.drdo.gov.in"]
];


/* LOGIN SYSTEM */
if(isset($_POST['login'])){

$email=$_POST['email'];
$password=md5($_POST['password']);

$sql="SELECT * FROM students WHERE email='$email' AND password='$password'";
$result=$conn->query($sql);

if($result->num_rows>0){

$row=$result->fetch_assoc();
$_SESSION['student']=$row['name'];

}else{
$message="Invalid Login";
}

}


/* RESUME UPLOAD */
if(isset($_POST['upload'])){

if($_FILES['resume']['error']==0){

$file_name=$_FILES['resume']['name'];
$tmp=$_FILES['resume']['tmp_name'];

$folder="uploads/";

if(!is_dir($folder)){
mkdir($folder);
}

$target=$folder.time()."_".$file_name;

move_uploaded_file($tmp,$target);

$message="Resume Uploaded Successfully";

$skills=["python","sql","html"]; // demo

foreach($skills as $skill){

if(isset($job_map[$skill])){

$jobs[]=[
$skill,
$job_map[$skill][0],
$job_map[$skill][1]
];

}

}

}

}
?>

<!DOCTYPE html>
<html>
<head>
<title>AI Resume Analyzer</title>

<style>

body{
font-family:Arial;
background:#f2f2f2;
text-align:center;
}

.container{
background:white;
width:600px;
margin:auto;
margin-top:50px;
padding:30px;
box-shadow:0 0 10px gray;
border-radius:10px;
}

button{
padding:10px 20px;
background:green;
color:white;
border:none;
cursor:pointer;
}

table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}

th,td{
border:1px solid #ddd;
padding:10px;
}

th{
background:green;
color:white;
}

</style>
</head>

<body>

<div class="container">

<?php if(!isset($_SESSION['student'])){ ?>

<h2>Student Login</h2>

<form method="POST">

<input type="email" name="email" placeholder="Enter Email" required><br><br>

<input type="password" name="password" placeholder="Enter Password" required><br><br>

<button type="submit" name="login">Login</button>

</form>

<?php }else{ ?>

<h2>Upload Resume</h2>

<form method="POST" enctype="multipart/form-data">

<input type="file" name="resume" required><br><br>

<button type="submit" name="upload">Upload Resume</button>

</form>

<?php } ?>


<?php if(!empty($jobs)){ ?>

<h3>Matched Jobs</h3>

<table>

<tr>
<th>Skill</th>
<th>Job</th>
<th>Link</th>
</tr>

<?php foreach($jobs as $job){ ?>

<tr>
<td><?php echo $job[0]; ?></td>
<td><?php echo $job[1]; ?></td>
<td><a href="<?php echo $job[2]; ?>" target="_blank">View</a></td>
</tr>

<?php } ?>

</table>

<?php } ?>


<p><?php echo $message; ?></p>

</div>

</body>
</html>