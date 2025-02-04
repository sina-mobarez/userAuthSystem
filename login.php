<?php
require_once 'config.php';
session_start();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token.");
    }    

    if (empty($username)) {
        $errors[] = "Username or email is required";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id, username, email, password FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param('ss', $username, $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $username, $email, $hashed_password);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user'] = json_encode([
                    'id' => $id,
                    'username' => $username,
                    'email' => $email
                ]);
                header('Location: dashboard.php');
            } else {
                $errors[] = "Invalid password";
            }
        } else {
            $errors[] = "Username or email not found";
        }
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php
if (isset($_SESSION['message'])) {
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '" . $_SESSION['message'] . "',
            confirmButtonText: 'OK'
        });
    </script>";
    unset($_SESSION['message']);
}
?>
<div class="wrapper">
    <h2>Login</h2>
    <?php
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color: red;'>$error</p>";
        }
    }
    ?>
    <form action="login.php" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
      <div class="input-box">
        <input type="text" name="username" placeholder="username / email" required >
      </div>
      <div class="input-box">
        <input type="password" name="password" placeholder="password" required>
      </div>
      <div class="input-box button">
        <input type="Submit" value="Login">
      </div>
      <div class="text">
        <h3>Don't have an account? <a href="register.php">Register now</a></h3>
      </div>
    </form>
  </div>

</body>
</html>
