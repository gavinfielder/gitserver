<!DOCTYPE html>
<?php require_once 'src/master.php'; ?>
<?php require_once 'src/ssh.php'; ?>
<?php require_once 'src/login.php'; ?>
<?php
    if (isset($_POST))
    {
        if (   isset($_POST['old-passwd'])
            && isset($_POST['new-passwd'])
            && isset($_POST['new-passwd-confirm'])
            && isset($_POST['submit'])
            && $_POST['submit'] === "Change Password")
        {
            change_password();
        }
        else if (  isset($_POST['ssh-key'])
                && isset($_POST['submit'])
                && $_POST['submit'] == "Add SSH Key")
        {
            if (add_ssh_key($_POST['ssh-key']))
            {
                message_to_user("SSH Key authorized.");
                redirect('account.php');
            }
            else
            {
                message_to_user("SSH Key could not be added. Check your input and try again.");
                redirect('account.php');
            }
        }
        unset($_POST);
    }
?>
<html>
<head>
    <title>My Account</title>
    <link rel="stylesheet" href="src/style.css">
    <script src="src/read_ssh_pubkey.js"></script>
</head>
<body>
    <?php include 'common/header.php'; ?>
    <?php include 'common/navbar.php'; ?>
    <div id="content">
        <?php handle_messages_html(); ?>
        <!-- Begin login Main Body -->
        <h1>My Account</h1>
        <?php
            if (isset($_SESSION["login"]))
            {
                include 'panels/account_panel.php';
            }
            else
            {
                echo "<p>You must be logged in to view this page.</p>";
            }
        ?>
        <!-- End login Main Body -->
    </div>
    <?php include 'common/footer.php'; ?>
</body>
</html>
