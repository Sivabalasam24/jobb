<?php
$conn = new mysqli("localhost", "root", "", "student_ai_job");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>