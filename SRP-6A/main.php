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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        .footer_bottom {
            position: fixed;
            width: 100%;
            left: 0;
            bottom: 0;
            flex: 0;
        }
    </style>
    <link href="./css/signin.css" rel="stylesheet">

</head>

<body class="text-center">
    <h1 "text-center">You have sucessfully logged in.</h1>
    <footer class="my-3 footer_bottom">
        <p class="text-center text-muted">&copy;idimus <?php echo date("Y"); ?></p>
    </footer>
</body>

</html>