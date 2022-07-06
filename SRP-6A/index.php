<?php

include "utils.php";

if (!isLoggedIn())
{
    header("Location: ./login.php");
    die();
}

header("Location: ./main.php"); 
die();

?>