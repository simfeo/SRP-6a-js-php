<?php

include "utils.php";

if ( !isset($_SESSION['lastactivity']) )
{
    header("Location: login.php");
    die();
}
else if (!startSession(true, "mine"))
{
    header("Location: login.php");
    die();
}

header("Location: main.php"); 
die();

?>