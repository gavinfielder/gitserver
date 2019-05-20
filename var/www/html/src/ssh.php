<?php

function validate_ssh_public_key($ssh_key)
{
    if (gettype($ssh_key) != "string")
        return false;
    $ssh_key = trim($ssh_key);
    if (strlen($ssh_key) < 254)
        return false;
    $pos = strpos($ssh_key, "ssh-rsa");
    if ($pos === false || $pos != 0)
        return false;
    if (strpos($ssh_key, "PRIVATE"))
        return false;
    return true;
}

function ssh_key_already_authorized($ssh_key)
{
    if (!validate_ssh_public_key($ssh_key))
        return -1;
    $keys = file_get_contents("/home/git/.ssh/authorized_keys");
    if (!$keys)
        return -1;
    if ($ssh_key[strlen($ssh_key) - 1] != "\n")
        $ssh_key .= "\n";
    if (strpos($keys, $ssh_key) === false)
        return false;
    return true;
}

function add_ssh_key($ssh_key)
{
    if (!validate_ssh_public_key($ssh_key))
        return false;
    if (ssh_key_already_authorized($ssh_key) === true)
        return true;
    $fout = fopen("/home/git/.ssh/authorized_keys", "a");
    if ($fout === false)
        return false;
    $ssh_key = preg_replace("/\r/", "", $ssh_key);
    if ($ssh_key[strlen($ssh_key) - 1] != "\n")
        $ssh_key .= "\n";
    fwrite($fout, $ssh_key);
    fclose($fout);
    return true;
}

?>
