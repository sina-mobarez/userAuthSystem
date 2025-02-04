<?php 
session_start();

if(!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}
$timeout_duration = 1800;
if(isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration)) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=true');
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();
$user_data = json_decode($_SESSION['user'], true);
print_r($user_data);

echo "<a href='logout.php'>Logout</a>";
?>