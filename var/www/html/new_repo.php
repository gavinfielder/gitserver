<!DOCTYPE html>
<?php require_once 'src/master.php'; ?>
<?php require_once 'src/git.php'; ?>
<?php
    if (isset($_POST))
    {

        if (   isset($_POST['name'])
            && isset($_POST['desc'])
            && isset($_POST['submit'])
            && $_POST['submit'] === "Open Repo"
            && isset($_SESSION['login']))
        {
            if (($ret = create_git_repo
                    ($_POST['name'], $_SESSION['login'], $_POST['desc']))
                    === true)
            {
                message_to_user("Repo \"".$_POST['name']."\" created.");
                redirect('index.php');
            }
            else
            {
                message_to_user($ret);
                redirect('new_repo.php');
            }
        }
        unset($_POST);
    }
?>
<html>
<head>
    <title>Creating New Repo</title>
    <link rel="stylesheet" href="src/style.css">
</head>
<body>
    <?php include 'common/header.php'; ?>
    <?php include 'common/navbar.php'; ?>
    <div id="content">
        <?php handle_messages_html(); ?>
        <!-- Begin login Main Body -->
        <h1>Open New Repository</h1>
        <?php
            if (isset($_SESSION["login"]))
            {
                include 'panels/new_repo_panel.php';
            }
            else
            {
                echo "<p>You must be logged in to view this page.</p>";
            }
        ?>
        <br>
        <!-- End login Main Body -->
    </div>
    <?php include 'common/footer.php'; ?>
</body>
</html>
