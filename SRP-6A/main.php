<?php
include "utils.php";

startSession();

if (!isLoggedIn())
{
    header("Location: ./index.php");
    die();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="idimus">
    <title>Login success</title>

    <link rel="canonical" href="https://idimus.xyz/work/">

    <link href="./css/bootstrap.min.css" rel="stylesheet" integrity="sha384-0evHe/X+R7YkIZDRvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxaWutUpFmBp4vmVor" crossorigin="anonymous">
    <meta name="theme-color" content="#712cf9">
</head>

<body class="text-center">
    <p>This is an example of a simple HTML page with one paragraph.</p>
</body>

</html>