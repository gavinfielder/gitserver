<!DOCTYPE html>
<?php require_once 'src/master.php'; ?>
<?php require_once 'src/login.php'; ?>
<?php
    if (isset($_POST))
    {

        if (   isset($_POST['username'])
            && isset($_POST['passwd'])
            && isset($_POST['submit'])
            && $_POST['submit'] === "Login")
        {
            login();
        }
        unset($_POST);
    }
?>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="src/style.css">
</head>
<body>
    <?php include 'common/header.php'; ?>
    <?php include 'common/navbar.php'; ?>
    <div id="content">
        <?php handle_messages_html(); ?>
        <!-- Begin login Main Body -->
        <h1>Login</h1>
        <form method="POST" action="login.php">
            <div class="form-label">Username:</div>
            <input class="form-input small-input" type="text" name="username" autocomplete="off" placeholder="Enter username..."><br>
            <div class="form-label">Password:</div>
            <input type="password" class="form-input small-input" placeholder="Enter password..." name="passwd">
            <input class="form-submit" type="submit" name="submit" value="Login"></br>
        </form>
        <p>Don't have an account? <a href="connect_user.php">New Account</a></p>
        <!-- End login Main Body -->
    </div>
    <?php include 'common/footer.php'; ?>
</body>
</html>
