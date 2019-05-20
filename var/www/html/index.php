<!DOCTYPE html>
<?php require_once 'src/master.php'; ?>
<?php require_once 'src/git.php'; ?>
<html>
<head>
    <title>Repositories</title>
    <link rel="stylesheet" href="src/style.css">
</head>
<body>
    <?php include 'common/header.php'; ?>
    <?php include 'common/navbar.php'; ?>
    <div id="content">
        <?php handle_messages_html(); ?>
        <!-- Begin login Main Body -->
        <h1>Server Repositories</h1>
        <?php echo generate_repo_table(); ?>
        <!-- End login Main Body -->
    </div>
    <?php include 'common/footer.php'; ?>
</body>
</html>
