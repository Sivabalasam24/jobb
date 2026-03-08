<?php
// Remove session_start() if not needed
// session_start();

$jobs = [];
$error = '';

if(isset($_POST['predict'])){
    
    $field = $_POST['field'] ?? "";
    $interest = $_POST['interest'] ?? "";
    
    // Add validation
    if(empty($field) || empty($interest)){
        $error = "Please select both field and interest";
    } else {
        
        // Engineering combinations
        if($field=="engineering" && $interest=="research"){
            $jobs[]=["DRDO Scientist","https://www.drdo.gov.in", "Research & Development"];
            $jobs[]=["ISRO Scientist","https://www.isro.gov.in", "Space Research"];
            $jobs[]=["BARC Scientist","https://www.barc.gov.in", "Nuclear Research"];
        }
        
        if($field=="engineering" && $interest=="technical"){
            $jobs[]=["IES (Engineering Services)","https://www.upsc.gov.in", "UPSC"];
            $jobs[]=["Railway Technical Officer","https://www.rrbcdg.gov.in", "Railways"];
            $jobs[]=["CPWD Engineer","https://www.cpwd.gov.in", "Public Works"];
        }
        
        // Commerce combinations
        if($field=="commerce" && $interest=="banking"){
            $jobs[]=["Bank PO (IBPS)","https://www.ibps.in", "Public Sector Banks"];
            $jobs[]=["RBI Grade B Officer","https://www.rbi.org.in", "Reserve Bank"];
            $jobs[]=["NABARD Grade A","https://www.nabard.org", "Agriculture Banking"];
        }
        
        if($field=="commerce" && $interest=="administration"){
            $jobs[]=["CSS (Central Secretariat Service)","https://www.upsc.gov.in", "Civil Services"];
            $jobs[]=["Indian Audit & Accounts Service","https://www.cag.gov.in", "Audit"];
            $jobs[]=["Indian Revenue Service","https://www.incometax.gov.in", "Taxation"];
        }
        
        // Arts combinations
        if($field=="arts" && $interest=="administration"){
            $jobs[]=["UPSC Civil Services (IAS, IPS, IFS)","https://www.upsc.gov.in", "Civil Services"];
            $jobs[]=["SSC CGL","https://ssc.nic.in", "Staff Selection"];
            $jobs[]=["State PSC","https://www.psc.gov.in", "State Services"];
        }
        
        if($field=="arts" && $interest=="research"){
            $jobs[]=["ASI Archaeologist","https://asi.nic.in", "Archaeology"];
            $jobs[]=["Ministry of Culture","https://www.indiaculture.nic.in", "Cultural Affairs"];
        }
        
        // General technical jobs (applicable to any field with technical interest)
        if($interest=="technical" && empty($jobs)){
            $jobs[]=["Railway Technical Officer","https://www.rrbcdg.gov.in", "Railways"];
            $jobs[]=["BRO Technical Staff","https://www.bro.gov.in", "Border Roads"];
        }
        
        // If no jobs found
        if(empty($jobs)){
            $error = "No specific job suggestions found for your combination. Try different options.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Career Prediction System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            background: white;
            width: 90%;
            max-width: 800px;
            margin: 20px;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        h2 {
            color: #333;
            margin-bottom: 30px;
            font-size: 28px;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 10px;
            display: inline-block;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
            font-size: 16px;
        }
        
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
            background: white;
            cursor: pointer;
        }
        
        select:hover {
            border-color: #4CAF50;
        }
        
        select:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }
        
        button {
            padding: 14px 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            margin-top: 20px;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }
        
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #c62828;
            text-align: left;
        }
        
        .results {
            margin-top: 40px;
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            padding: 15px;
            font-size: 16px;
        }
        
        td {
            padding: 15px;
            border: 1px solid #e0e0e0;
            background: white;
        }
        
        tr:hover td {
            background: #f5f5f5;
        }
        
        .job-title {
            font-weight: 600;
            color: #333;
        }
        
        .department {
            color: #666;
            font-size: 14px;
            display: block;
            margin-top: 5px;
        }
        
        a {
            display: inline-block;
            padding: 8px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 500;
            transition: background 0.3s;
        }
        
        a:hover {
            background: #45a049;
        }
        
        .serial {
            font-weight: 600;
            color: #667eea;
        }
        
        .no-results {
            text-align: center;
            padding: 40px;
            color: #666;
            background: #f9f9f9;
            border-radius: 10px;
        }
        
        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }
            
            table {
                font-size: 14px;
            }
            
            th, td {
                padding: 10px;
            }
            
            a {
                padding: 6px 12px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🎯 AI Career Prediction System</h2>
        
        <form method="POST">
            <div class="form-group">
                <label for="field">Select Your Study Field:</label>
                <select name="field" id="field" required>
                    <option value="">-- Choose your field --</option>
                    <option value="engineering" <?php echo (isset($_POST['field']) && $_POST['field']=='engineering') ? 'selected' : ''; ?>>Engineering</option>
                    <option value="commerce" <?php echo (isset($_POST['field']) && $_POST['field']=='commerce') ? 'selected' : ''; ?>>Commerce</option>
                    <option value="arts" <?php echo (isset($_POST['field']) && $_POST['field']=='arts') ? 'selected' : ''; ?>>Arts</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="interest">Your Interest Area:</label>
                <select name="interest" id="interest" required>
                    <option value="">-- Choose your interest --</option>
                    <option value="research" <?php echo (isset($_POST['interest']) && $_POST['interest']=='research') ? 'selected' : ''; ?>>Research & Development</option>
                    <option value="banking" <?php echo (isset($_POST['interest']) && $_POST['interest']=='banking') ? 'selected' : ''; ?>>Banking & Finance</option>
                    <option value="administration" <?php echo (isset($_POST['interest']) && $_POST['interest']=='administration') ? 'selected' : ''; ?>>Administration</option>
                    <option value="technical" <?php echo (isset($_POST['interest']) && $_POST['interest']=='technical') ? 'selected' : ''; ?>>Technical</option>
                </select>
            </div>
            
            <button type="submit" name="predict">Get AI Job Suggestions</button>
        </form>
        
        <?php if($error): ?>
            <div class="error">⚠️ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(!empty($jobs)): ?>
            <div class="results">
                <h3>📋 Recommended Government Jobs For You</h3>
                <table>
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Job Position</th>
                            <th>Department</th>
                            <th>Apply Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i=1; foreach($jobs as $job): ?>
                            <tr>
                                <td class="serial">#<?php echo $i; ?></td>
                                <td class="job-title"><?php echo htmlspecialchars($job[0]); ?></td>
                                <td><?php echo htmlspecialchars($job[2] ?? 'Government'); ?></td>
                                <td><a href="<?php echo htmlspecialchars($job[1]); ?>" target="_blank" rel="noopener">Apply Now →</a></td>
                            </tr>
                        <?php $i++; endforeach; ?>
                    </tbody>
                </table>
                <p style="margin-top: 20px; color: #666; font-size: 14px;">
                    * Click on "Apply Now" to visit the official website for more details.
                </p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>