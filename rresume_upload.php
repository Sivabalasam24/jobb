<?php
session_start();
include "db.php";

if(!isset($_SESSION['student'])){
   header("Location: login.php");
    exit();
}

$message = "";
$skills = [];
$jobs = [];

// Skill to job mapping
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

if(isset($_POST['upload'])){
    
    // Check if file was uploaded
    if($_FILES['resume']['error'] == 0){
        
        $file_name = $_FILES['resume']['name'];
        $file_tmp = $_FILES['resume']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Create uploads directory
        $folder = "uploads/";
        if(!is_dir($folder)){
            mkdir($folder, 0777, true);
        }
        
        // Generate unique filename
        $new_file_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", $file_name);
        $target = $folder . $new_file_name;
        
        // Move uploaded file
        if(move_uploaded_file($file_tmp, $target)){
            
            $message = "Resume Uploaded Successfully";
            
            // Run Python script based on file type
            if($file_ext == 'docx'){
                $command = "python extract_keywords.py \"" . $target . "\" 2>&1";
                $output = shell_exec($command);
                
                // Parse skills from Python output
                if($output){
                    $skills = array_filter(explode(",", trim($output)));
                }
            } else {
                // For non-docx files, use sample skills for demo
                $skills = ["python", "java", "sql", "html"];
            }
            
            // Match skills with jobs
            if(!empty($skills)){
                foreach($skills as $skill){
                    $skill = strtolower(trim($skill));
                    if(isset($job_map[$skill])){
                        $jobs[] = [
                            $skill,
                            $job_map[$skill][0],
                            $job_map[$skill][1]
                        ];
                    }
                }
            }
            
        } else {
            $message = "Upload Failed";
        }
    } else {
        $message = "Upload Failed";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>AI Resume Analyzer</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        
        .container {
            background: white;
            width: 750px;
            margin: auto;
            padding: 30px;
            box-shadow: 0 0 10px gray;
            border-radius: 10px;
        }
        
        h2 {
            color: #333;
        }
        
        h3 {
            color: #444;
            margin-top: 30px;
        }
        
        input[type="file"] {
            padding: 10px;
            margin: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        button {
            padding: 10px 20px;
            background: green;
            color: white;
            border: none;
            cursor: pointer;
            margin: 10px;
            border-radius: 5px;
            font-size: 14px;
        }
        
        button:hover {
            opacity: 0.9;
        }
        
        .home {
            background: #007bff;
        }
        
        table {
            margin: auto;
            margin-top: 20px;
            border-collapse: collapse;
            width: 90%;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        
        th {
            background: #4CAF50;
            color: white;
        }
        
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        a {
            color: #007bff;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        .message {
            color: green;
            font-weight: bold;
            margin: 20px 0;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Upload Resume (DOCX Only)</h2>
    
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="resume" accept=".docx" required><br><br>
        <button type="submit" name="upload">Upload Resume</button>
    </form>
    
    <br>
    
    <?php if($message != ""): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <!-- SKILLS TABLE -->
    <?php if(!empty($skills)): ?>
        <h3>Detected Resume Skills</h3>
        <table>
            <tr>
                <th>S.No</th>
                <th>Skill Keyword</th>
            </tr>
            <?php $i=1; foreach($skills as $skill): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo htmlspecialchars(trim($skill)); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    
    <!-- JOB MATCH TABLE -->
    <?php if(!empty($jobs)): ?>
        <h3>Matched Government Jobs</h3>
        <table>
            <tr>
                <th>S.No</th>
                <th>Skill</th>
                <th>Job Role</th>
                <th>Reference Link</th>
            </tr>
            <?php $i=1; foreach($jobs as $job): ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo ucfirst($job[0]); ?></td>
                    <td><?php echo $job[1]; ?></td>
                    <td><a href="<?php echo $job[2]; ?>" target="_blank">View Job</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    
    <br>
    <a href="dashboard.php">
        <button class="home">Back to Dashboard</button>
    </a>
    
</div>

</body>
</html>