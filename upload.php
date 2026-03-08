<?php
$message="";
$skills=[];
$jobs=[];
$missing_skills=[];
$score=0;

/* SKILLS FOR ATS CHECK */

$all_skills=[
"python","sql","java","html","css",
"javascript","php","machine learning",
"data analysis","iot"
];

/* JOB MAP */

$job_map=[

"python"=>["Data Analyst","https://www.ncs.gov.in"],
"sql"=>["Database Assistant","https://www.ncs.gov.in"],
"java"=>["Software Developer","https://www.ncs.gov.in"],
"html"=>["Web Developer","https://www.nielit.gov.in"],
"css"=>["Web Designer","https://www.nielit.gov.in"],
"javascript"=>["Frontend Developer","https://www.nielit.gov.in"],
"php"=>["Backend Developer","https://www.nielit.gov.in"],
"machine learning"=>["AI Engineer","https://www.drdo.gov.in"],
"data analysis"=>["Data Analyst","https://www.ncs.gov.in"],
"iot"=>["IoT Engineer","https://www.drdo.gov.in"]

];

if(isset($_POST['upload'])){

if($_FILES['resume']['error']==0){

$file=$_FILES['resume']['tmp_name'];
$name=$_FILES['resume']['name'];

$folder="uploads/";

if(!is_dir($folder)){
mkdir($folder);
}

$target=$folder.time()."_".$name;

move_uploaded_file($file,$target);

$message="Resume Uploaded Successfully";

/* RUN PYTHON SCRIPT */

$command="python extract_keywords.py \"$target\"";
$output=shell_exec($command);

if($output){

$skills=array_filter(explode(",",trim($output)));

/* ATS SCORE */

$found=0;

foreach($skills as $skill){

$skill=strtolower(trim($skill));

if(in_array($skill,$all_skills)){
$found++;
}

}

$total=count($all_skills);
$score=round(($found/$total)*100);

/* JOB MATCH */

foreach($skills as $skill){

$skill=strtolower(trim($skill));

if(isset($job_map[$skill])){

$jobs[]=[
$skill,
$job_map[$skill][0],
$job_map[$skill][1]
];

}

}

/* SKILL GAP */

foreach($all_skills as $skill){

if(!in_array($skill,$skills)){
$missing_skills[]=$skill;
}

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
font-family:Segoe UI;
background:linear-gradient(135deg,#4facfe,#00f2fe);
margin:0;
}

.container{
width:750px;
margin:80px auto;
background:white;
padding:40px;
border-radius:12px;
box-shadow:0 10px 25px rgba(0,0,0,0.2);
text-align:center;
position:relative;
}

.home-btn{
position:absolute;
right:20px;
top:20px;
background:#007bff;
color:white;
padding:8px 15px;
border-radius:6px;
text-decoration:none;
}

.upload-box{
border:2px dashed #ccc;
padding:30px;
border-radius:10px;
margin-bottom:20px;
}

button{
background:#4CAF50;
color:white;
padding:12px 25px;
border:none;
border-radius:6px;
cursor:pointer;
}

button:hover{
background:#45a049;
}

table{
width:100%;
border-collapse:collapse;
margin-top:25px;
}

th,td{
padding:12px;
border-bottom:1px solid #ddd;
}

th{
background:#4CAF50;
color:white;
}

/* CIRCULAR SCORE */

.score-container{
display:flex;
justify-content:center;
margin-top:30px;
}

.circle{
width:150px;
height:150px;
border-radius:50%;
background:conic-gradient(#4CAF50 calc(var(--score)*1%), #ddd 0);
display:flex;
align-items:center;
justify-content:center;
}

.circle span{
background:white;
width:120px;
height:120px;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
font-size:28px;
font-weight:bold;
box-shadow:0 0 10px rgba(0,0,0,0.2);
}

</style>

</head>

<body>

<div class="container">

<a href="dashboard.php" class="home-btn">Home</a>

<h2>AI Resume Analyzer</h2>

<form method="POST" enctype="multipart/form-data">

<div class="upload-box">
<input type="file" name="resume" accept=".docx" required>
</div>

<button type="submit" name="upload">Upload & Analyze</button>

</form>

<p style="color:green;"><?php echo $message;?></p>

<!-- ATS SCORE -->

<?php if($score>0){ ?>

<div class="score-container">

<div class="circle" style="--score:<?php echo $score; ?>;">
<span><?php echo $score; ?>%</span>
</div>

</div>

<?php } ?>

<!-- SKILLS -->

<?php if(!empty($skills)){ ?>

<h3>Detected Skills</h3>

<table>

<tr>
<th>S.No</th>
<th>Skill</th>
</tr>

<?php $i=1; foreach($skills as $skill){ ?>

<tr>
<td><?php echo $i++; ?></td>
<td><?php echo ucfirst($skill); ?></td>
</tr>

<?php } ?>

</table>

<?php } ?>


<!-- JOBS -->

<?php if(!empty($jobs)){ ?>

<h3>Suggested Jobs</h3>

<table>

<tr>
<th>S.No</th>
<th>Skill</th>
<th>Job Role</th>
<th>Apply Link</th>
</tr>

<?php $i=1; foreach($jobs as $job){ ?>

<tr>
<td><?php echo $i++; ?></td>
<td><?php echo ucfirst($job[0]); ?></td>
<td><?php echo $job[1]; ?></td>
<td><a href="<?php echo $job[2]; ?>" target="_blank">View Job</a></td>
</tr>

<?php } ?>

</table>

<?php } ?>


<!-- MISSING SKILLS -->

<?php if(!empty($missing_skills)){ ?>

<h3>Recommended Skills to Learn</h3>

<table>

<tr>
<th>S.No</th>
<th>Missing Skill</th>
</tr>

<?php $i=1; foreach($missing_skills as $skill){ ?>

<tr>
<td><?php echo $i++; ?></td>
<td><?php echo ucfirst($skill); ?></td>
</tr>

<?php } ?>

</table>

<?php } ?>

</div>

</body>
</html>