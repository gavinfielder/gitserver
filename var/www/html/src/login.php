<?php

require_once 'src/master.php';

function login_error($msg, $sql_connection = NULL)
{
    message_to_user($msg);
    unset($_POST);
    if ($sql_connection != NULL)
        $sql_connection->close();
    redirect('login.php');
    exit (0);
}

function logout()
{
    unset($_SESSION["login"]);
}

// Logs in a user. Called by WEB_ROOT/login.php
// Precondition:
//   $_POST['username']
//   $_POST['passwd']           (plain text)
function login()
{
    //Validate data
    $username = $_POST['username'];
    if (strlen($username) < 4 || strlen($username) > 32)
        login_error("Username must be between 4 and 32 characters.");
    if (strlen($_POST['passwd']) < 3)
        login_error("Password must be at least 3 characters.");
    $password = gitserver_password_hash(trim($_POST['passwd']));
    unset($_POST);

    if (($ret = authenticate_username_password
                ($username, $password, true)) === true)
    {
        $_SESSION["login"] = $username;
        message_to_user("Logged in as $username");
        redirect("index.php");
    }
    else
    {
        login_error($ret);
    }
}

function authenticate_username_password($username, $password,
                                        $already_hashed = false)
{
    if (!($already_hashed))
        $password = gitserver_password_hash($password);
    //Find user
	$db_servername = "127.0.0.1";
	$db_username = "git";
	$db_password = getenv('GITSERVER_SQL_GIT_PASSWD');
	$db_dbname = "gitserver";
	$conn = new mysqli($db_servername, $db_username, $db_password, $db_dbname);
	if ($conn->connect_error)
        return ("SQL Connection Error: ".$conn->connect_error);
	else //connected
	{
        //First check if the user exists
		$sql = "SELECT password FROM user WHERE username = '$username'";
		$result = $conn->query($sql);
        if ($result === false)
        {
    		$conn->close();
            return ("SQL Error while checking user existence: ".$conn->error);
        }
		if ($result->num_rows == 0)
        {
    		$conn->close();
            return ("User not found: '$username'");
        }
        //Check the password
        $row = $result->fetch_assoc();
        if ($row["password"] != $password)
        {
    		$conn->close();
            return ("Invalid password");
        }
        //Successful authentication
		$conn->close();
        return (true);
    }
}

function change_password_error($msg, $sql_connection = NULL)
{
    message_to_user($msg);
    unset($_POST);
    if ($sql_connection != NULL)
        $sql_connection->close();
    redirect('account.php');
    exit (0);
}

// Changes the current user's password Called by WEB_ROOT/account.php
// Precondition:
//   $_POST['old-passwd']           (plain text)
//   $_POST['new-passwd']           (plain text)
//   $_POST['new-passwd-confirm']   (plain text)
function change_password()
{
    //Validate data
    if (!isset($_SESSION["login"]))
        change_password_error("Not logged in.");
    $username = $_SESSION["login"];
    if (!(($ret = authenticate_username_password($username, $_POST['old-passwd'])) === true))
        change_password_error($ret);
    if (strlen($_POST['new-passwd']) < 3)
        change_password_error("Password must be at least 3 characters.");
    if ($_POST['new-passwd'] != $_POST['new-passwd-confirm'])
        change_password_error("Passwords do not match.");
    $password = gitserver_password_hash(trim($_POST['new-passwd']));
    unset($_POST);

    if (($ret = update_password($username, $password)) === false)
        change_password_error($ret);
    message_to_user("Password updated.");
    redirect("account.php");
}


function update_password($username, $password_hash)
{
    //Find user
	$db_servername = "127.0.0.1";
	$db_username = "git";
	$db_password = getenv('GITSERVER_SQL_GIT_PASSWD');
	$db_dbname = "gitserver";
	$conn = new mysqli($db_servername, $db_username, $db_password, $db_dbname);
	if ($conn->connect_error)
        return ("SQL Connection Error: ".$conn->connect_error);
	else //connected
	{
		$sql = "UPDATE user SET password = '$password_hash' WHERE username = '$username'";
		$result = $conn->query($sql);
        if ($result === false)
        {
    		$conn->close();
            return ("SQL Error while updating password".$conn->error);
        }
  		$conn->close();
        return (true);
    }
}

?>
