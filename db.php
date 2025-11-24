<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "image_site";

$conn = new mysqli($servername,$username, $password, $dbname);
if($conn ->connect_error){
     die("connection to this database failed due to" . mysqli_connect_error());
 }

 $conn->set_charset("utf8");
?>