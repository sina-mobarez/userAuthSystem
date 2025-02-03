<?php

$host = "localhost";
$dbname = "my_database";
$username = "my_user";
$password = "my_password";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>