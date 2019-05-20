<?php

session_start();
setlocale(LC_ALL, "us");

function message_to_user($msg)
{
    $_SESSION['message'] = $msg;
}

function handle_messages()
{
    if (isset($_SESSION['message']))
    {
        echo $_SESSION['message'] . "\n";
        unset($_SESSION['message']);
    }
}

function handle_messages_html()
{
    if (isset($_SESSION['message']))
    {
        echo "<div class=\"user-message\">" . $_SESSION['message'] . "</div>\n";
        unset($_SESSION['message']);
    }
}

function redirect($page)
{
    ob_start();
    header('Location: ' . $page);
    ob_end_flush();
    exit (0);
}

function gitserver_password_hash($raw)
{
    $salt_begin = "j;HG8!d@87";
    $salt_end = "8h#Nd.";
    $str = $salt_begin.$raw.$salt_end;
    $hash = hash('whirlpool', $str);
    return ($hash);
}

?>
