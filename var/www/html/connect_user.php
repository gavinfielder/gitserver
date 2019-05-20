<!DOCTYPE html>
<?php require_once 'src/master.php'; ?>
<?php require_once 'src/connect_new_user.php'; ?>
<?php
    if (isset($_POST))
    {

        if (   isset($_POST['username'])
            && isset($_POST['email'])
            && isset($_POST['passwd'])
            && isset($_POST['passwd-confirm'])
            && isset($_POST['ssh-key'])
            && isset($_POST['submit'])
            && $_POST['submit'] === "Create Account")
        {
            connect_new_user();
        }
        unset($_POST);
    }
?>
<html>
<head>
    <title>Connect New User</title>
    <link rel="stylesheet" href="src/style.css">
    <script src="src/read_ssh_pubkey.js"></script>
</head>
<body>
    <?php include 'common/header.php'; ?>
    <?php include 'common/navbar.php'; ?>
    <div id="content">
        <?php handle_messages_html(); ?>
        <!-- Begin connect_user Main Body -->
        <h1>Connect New User</h1>
        <form method="POST" action="connect_user.php">
            <div class="form-label">Username:</div>
            <input class="form-input small-input" type="text" name="username" autocomplete="off" placeholder="Enter username..."><br>
            <div class="form-label">Email:</div>
            <input class="form-input small-input" type="text" name="email" autocomplete="off" placeholder="Enter email..."><br>
            <div class="form-label">Password:</div>
            <input class="form-input small-input" type="password" name="passwd" placeholder="New password..."><br>
            <div class="form-label">Confirm Password:</div>
            <input class="form-input small-input" type="password" name="passwd-confirm" placeholder="New password..."><br>
            <div class="form-label ssh-entry-label">SSH Public Key:</div><br>
            <textarea class="ssh-entry" name="ssh-key" id="connect-new-user-ssh-entry" autocomplete="off" placeholder="(Optional) Enter an SSH public key to get started with git"></textarea>
            <input class="file-selector" type="file" id="add-ssh" name="add-ssh-file">
            <br>
            <input class="form-submit" type="submit" name="submit" value="Create Account">
        </form>
        <p>Already have an account? <a href="login.php">Login</a></p>
        <br>
       <h2>Setup Git SSH (Required before using git with gitserver)</h2>
       <p><i>(This information is also shown on 'My Account' once you have registered.)</i></p>
       <p>Copy this into ~/.ssh/config</p>
       <div class="code-block" id="ssh-setup-code"># [GITSERVER]
	Host gitserver
	Hostname localhost
	User git
	IdentityFile ~/.ssh/id_rsa
	Port 2222
# [END GITSERVER]
       </div>
       <p>If your SSH public/private key pair is in a different location, modify the path given for IdentityFile accordingly.</p><br><br>
        <!-- End connect_user Main Body -->
    </div>
    <?php include 'common/footer.php'; ?>
</body>
</html>
