<?php

require_once 'src/master.php';

function create_git_repo($name, $creator, $description)
{
    $name = trim($name);
    $description = trim($description);
    $creator = trim($creator);
    if (preg_match('/[^a-zA-Z0-9_]/', $name))
        return ("Repo names must be only alphanumeric and underscore.");
    if (strlen($name) < 3 || strlen($name) > 100)
        return ("Repo names must be between 3 and 100 characters.");
    if (strlen($description) < 1)
        return ("Description cannot be empty.");
    if (file_exists("/home/git/$name"))
        return ("A repo of that name already exists on the server.");
    if (preg_match('/[<>]/', $description))
        return ("Descriptions cannot contain the characters <, >, or ' (apostrophe).");

    //make the repo
    $result = shell_exec("mkdir /home/git/$name");
    if (!file_exists("/home/git/$name"))
        return ("Error: Could not create the server directory.");
    $result = shell_exec("git -C /home/git/$name init --bare");
    if (strpos($result, "Initialized empty Git repository in /home/git/") === false)
    {
        shell_exec("rm -rf /home/git/$name");
        return ("Error: git could not create the repo.");
    }

    //Add the repo to the database
	$db_servername = "127.0.0.1";
	$db_username = "git";
	$db_password = getenv('GITSERVER_SQL_GIT_PASSWD');
	$db_dbname = "gitserver";
	$conn = new mysqli($db_servername, $db_username, $db_password, $db_dbname);
	if ($conn->connect_error)
    {
        shell_exec("rm -rf /home/git/$name");
        return ("SQL Connection Error: ".$conn->connect_error);
    }
    //Insert the repo record
    //$created_time = date("Y-m-d H:i:s");
	$sql = "INSERT IGNORE INTO repos";
    $sql .= " (`name`, `creator`, `created_time`, `description`) VALUES\n";
    $sql .= "('$name', '$creator', NOW(), '$description');";
	$result = $conn->query($sql);
    if ($result === false)
    {
        shell_exec("rm -rf /home/git/$name");
        $ret = "SQL Error while creating repo record: ".$conn->error;
        $conn->close();
        return $ret;
    }
    $conn->close();
    return (true);
}

function get_git_info(&$info)
{
    $info = array();
	$db_servername = "127.0.0.1";
	$db_username = "git";
	$db_password = getenv('GITSERVER_SQL_GIT_PASSWD');
	$db_dbname = "gitserver";
	$conn = new mysqli($db_servername, $db_username, $db_password, $db_dbname);
	if ($conn->connect_error)
        return ("SQL Connection Error: ".$conn->connect_error);
    //Insert the repo record
	$sql = "SELECT * FROM repos;";
	$result = $conn->query($sql);
    if ($result === false)
    {
        $ret = "SQL Error while getting repository information: ".$conn->error;
        $conn->close();
        return $ret;
    }
    $conn->close();
    for ($i = 0; $i < $result->num_rows; $i++)
    {
        $row = $result->fetch_assoc();
        $ret = get_additional_repo_info($row);
        if ($ret === true)
            $info[] = $row;
    }
    return (true);
}

function get_additional_repo_info(&$row)
{
    if (!isset($row["name"]))
        return (false);
    $name = $row["name"];
    $additional = shell_exec("git -C /home/git/$name log -n 1 --format=\"%cn[FND]%ct[FND]%s\" 2>&1");
    if (strpos($additional, "[FND]") > 0)
    {
        $additional = explode("[FND]", $additional);
        $row["last_commit_by"] = $additional[0];
        $row["last_commit_time"] = date("F j Y g:i a", intval($additional[1]));
        $row["last_commit_message"] = trim($additional[2]);
        $row["url"] = "gitserver:/repos/$name";
        return (true);
    }
    else if (strpos($additional, "does not have any commits yet") > 0)
    {
        $row["last_commit_by"] = "N/A";
        $row["last_commit_time"] = "No commits yet";
        $row["last_commit_message"] = "N/A";
        $row["url"] = "gitserver:/repos/$name";
        return (true);
    }
    return (false);
}

function generate_repo_table()
{
    $html = "<table class=\"repo-table\">";
    $html .= "<colgroup>";
    $html .= "<col class=\"col1\" />";
    $html .= "<col class=\"col2\" />";
    $html .= "<col class=\"col3\" />";
    $html .= "<col class=\"col4\" />";
    $html .= "<col class=\"col5\" />";
    $html .= "<col class=\"col6\" />";
    $html .= "<col class=\"col7\" />";
    $html .= "<col class=\"col8\" />";
    $html .= "</colgroup>";
    $html .= "<tr class=\"repo-table-header\">";
    $html .= "<td>Repo Name</td>";
    $html .= "<td>Description</td>";
    $html .= "<td>Created</td>";
    $html .= "<td>Creator</td>";
    $html .= "<td>Last Commit</td>";
    $html .= "<td>By</td>";
    $html .= "<td>Message</td>";
    $html .= "<td>URL</td>";
    $html .= "</tr>";

    $status = get_git_info($info);
    if ($status === true)
    {
        foreach ($info as $key => $repo)
        {
            $html .= generate_repo_row($repo);
        }
        $html .= "</table>";
        return ($html);
    }
    return $status;
}

function generate_repo_row($repo_info)
{
    $html = "<td>";
    $html .= $repo_info["name"];
    $html .= "</td><td>";
    $html .= $repo_info["description"];
    $html .= "</td><td>";
    $html .= reformat_created_time($repo_info["created_time"]);
    $html .= "</td><td>";
    $html .= $repo_info["creator"];
    $html .= "</td><td>";
    $html .= $repo_info["last_commit_time"];
    $html .= "</td><td>";
    $html .= $repo_info["last_commit_by"];
    $html .= "</td><td>";
    $html .= $repo_info["last_commit_message"];
    $html .= "</td><td>";
    $html .= $repo_info["url"];
    $html .= "</td></tr>";
    return ($html);
}

function reformat_created_time($created_time)
{
    $format = 'Y-m-d H:i:s';
    $date = DateTime::createFromFormat($format, $created_time);
    $new = $date->format("F jS Y");
    return $new;
}

?>
