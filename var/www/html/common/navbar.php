<!DOCTYPE html>
<html>
<body>
    <div id="navbar">
        <div class="navbar-links-container">
            <button class="navbar-button" onclick="window.location.href='index.php';">Repositories</button>
            <button class="navbar-button" onclick="window.location.href='new_repo.php';">New Repo</button>
            <button class="navbar-button" onclick="window.location.href='account.php';">My Account</button>
        </div>
        <div align="right" class="login-info">
            <?php
                if (isset($_SESSION["login"]))
                {
                    echo "Logged in as ".$_SESSION["login"] . ".  ";
                    echo "<a href=\"logout.php\">Logout</a>";
                }
                else
                {
                    echo "Not logged in.  ";
                    echo "<a href=\"login.php\">Login</a> ";
                    echo "or <a href=\"connect_user.php\">New User</a>";
                }
            ?>
        </div>
    </div>
</body>
</html>
