#!/usr/bin/php
<?php

require_once '/usr/gitserver_access_iptables.php';
require_once '/usr/gitserver_access_sql.php';
require_once '/etc/gitserver_access.conf';

$dport = 57348;
$maintenance_keys_file = "/home/maint/.ssh/authorized_keys";
$log_file = "/var/log/gitserver_access.log";

function write_to_log($msg)
{
	global $log_file;
	$fout = fopen($log_file, "a");
	if ($fout)
	{
		fwrite($fout, "" . date("D M j G:i:s T Y") . ": $msg\n");
		fclose($fout);
	}
}

$sock = socket_create_listen($dport);
socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>5, "usec"=>0)); 
if ($sock != false)
{
	$received = "";
	$ret = false;
	while (true)
	{
        //echo "Awaiting connections...\n";
		$connection = @socket_accept($sock);
        //echo "Connection? ";

		if ($connection)
		{
            //echo "Got connection.\n";
			socket_getpeername($connection, $saddress, $sport);
			$bytes = 0;
			while ($ret = socket_read($connection, 500, PHP_BINARY_READ))
			{
				$received .= $ret;
				$bytes += strlen($ret);
			}
			$received = trim($received);
			$keyparts = explode(" ", $received);
            //echo "Received $bytes bytes.\n";
			if ($bytes > 254 && isset($keyparts[1]))
			{
				$keys = file_get_contents($maintenance_keys_file);
				if ($keys === false)
					write_to_log("Could not access $maintenance_keys_file");
				else if (strpos($received, $keyparts[1]) === false)
				{
					write_to_log("Invalid access request from $saddress ($sport)");
				}
				else
				{
					//Connected user is authorized to connect this endpoint
					if (add_authorized_address($saddress))
					{
						$addresses = get_authorized_addresses();
						update_firewall($addresses);
						write_to_log("Successful connection of $saddress");
					}
					else
						write_to_log("SQL failure while connecting $saddress");
				}
			}
			socket_close($connection);
		}
	}
}

?>
