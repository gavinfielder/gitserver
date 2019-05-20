<?php

//Gets the list of authorized IP addresses
function get_authorized_addresses()
{
	$servername = "127.0.0.1";
	$username = "gitserver_access";
    $password = GITSERVER_ACCESS_PASSWD;
	$dbname = "gitserver_access";

	//Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);

	//check connection
	if ($conn->connect_error)
	{
		return (false);
	}
	else
	{
		$sql = "SELECT * FROM connected_addresses";
		$result = $conn->query($sql);
		$ret = array();
		$i = 0;
		if ($result->num_rows > 0)
		{
			while ($row = $result->fetch_assoc())
				$ret[$i++] = $row["address"];
		}
		$conn->close();
		return ($ret);
	}
}

//Gets the list of authorized IP addresses
function add_authorized_address($address)
{
	$servername = "127.0.0.1";
	$username = "gitserver_access";
    $password = GITSERVER_ACCESS_PASSWD;
	$dbname = "gitserver_access";

	//Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);

	//check connection
	if ($conn->connect_error)
	{
		return (false);
	}
	else
	{
		$sql = "INSERT IGNORE INTO connected_addresses VALUES ('$address')";
		$result = $conn->query($sql);
		if ($result === true)
		{
			//echo "address registered: $address\n";
		}
		else
		{
			$result = false;
			//echo "error: " . $conn->error . "\n";
		}
		$conn->close();
		return ($result);
	}
}

?>
