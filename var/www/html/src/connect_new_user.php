<?php

require_once 'src/master.php';
require_once 'src/ssh.php';

function connect_new_user_error($msg, $sql_connection = NULL)
{
    message_to_user($msg);
    unset($_POST);
    if ($sql_connection != NULL)
        $sql_connection->close();
    redirect('connect_user.php');
    exit (0);
}

// Adds a new git user. Called by connect_user.php POST.
// Precondition:
//   $_POST['username']
//   $_POST['email']
//   $_POST['passwd']           (plain text)
//   $_POST['passwd-confirm']
//   $_POST['ssh-key']
function connect_new_user()
{
    //Validate data
    if ($_POST['passwd'] != $_POST['passwd-confirm'])
        connect_new_user_error("Passwords do not match.");
    $username = trim($_POST['username']);
    if (strlen($username) < 4 || strlen($username) > 32)
        connect_new_user_error("Username must be between 4 and 32 characters.");
    if (preg_match('/[^A-Za-z0-9_.@]/', $username))
        connect_new_user_error("Usernames may have only a-z, A-Z, 0-9, and the characters _ . @");
    if (strlen($_POST['passwd']) < 3)
        connect_new_user_error("Password must be at least 3 characters.");
    $password = gitserver_password_hash(trim($_POST['passwd']));
    $email = trim($_POST['email']);
    if ($email == "")
        $email == NULL;
    $ssh_key = $_POST['ssh-key'];
    if ($ssh_key == "")
        $ssh_key = NULL;
    else if (!validate_ssh_public_key($ssh_key))
        connect_new_user_error("SSH public key given does not appear to be valid. Enter a valid SSH public key or leave blank.");
    unset($_POST);

    //Connect new user
	$db_servername = "127.0.0.1";
	$db_username = "git";
	$db_password = getenv('GITSERVER_SQL_GIT_PASSWD');
	$db_dbname = "gitserver";
	$conn = new mysqli($db_servername, $db_username, $db_password, $db_dbname);
	if ($conn->connect_error)
		connect_new_user_error("SQL Connection Error: ".$conn->connect_error);
    //First check if the user exists
	$sql = "SELECT * FROM user WHERE username = '$username'";
	$result = $conn->query($sql);
    if ($result === false)
        connect_new_user_error("SQL Error while checking user duplicate: ".$conn->error, $conn);
    if ($result->num_rows > 0)
        connect_new_user_error("Username '$username' is taken.", $conn);
    //Insert the user
    $sql = "INSERT INTO user (`username`, `password`, `email`) VALUES ";
    $sql .= "('$username', '$password', ";
    $sql .= ($email == NULL ? "NULL" : "'$email'");
    $sql .= ")";
    $result = $conn->query($sql);
    if ($result == false)
    {
        connect_new_user_error("SQL Error encountered: ".$conn->error, $conn);
    }
	$conn->close();

    //Add the SSH key to the git authorized_keys file
    if ($ssh_key != NULL)
    {
        if (add_ssh_key($ssh_key))
            $ssh_result = "The requested SSH key was authorized.";
        else
            connect_new_user_error("User was successfully created in database, but could not authorize the given SSH key.");
    }
    else
        $ssh_result = "No SSH key authorized. SSH keys are required to access gitserver using git. Add an SSH key through 'My Account'.";

    //Done
    message_to_user("Created new user: '$username'. " . $ssh_result);
    redirect("login.php");
}












?>
