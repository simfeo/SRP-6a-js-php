<?php
include "../utils.php";
include "../config.php";

//startSession();

if (!isLoggedIn()) {
    die();
}

$requestBody = file_get_contents('php://input');

$deserialized = json_decode($requestBody);

$url = SIGNAL_API_URL."/v2/send";

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = array(
   "Accept: application/json",
   "Content-Type: application/json",
);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$data = (object) [
    'message' => $deserialized->state ,
    'number' => SENDER_PHONE_NUMBER,
    'recipients' => [
        //fill from db
        '+380667239220'
    ]
];

curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

$resp = curl_exec($curl);
curl_close($curl);

echo '{"status": "success"}';
?>