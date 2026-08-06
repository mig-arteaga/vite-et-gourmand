<?php

$ch = curl_init("https://routes.googleapis.com");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$response = curl_exec($ch);

if ($response === false) {
    echo curl_error($ch);
} else {
    echo "SSL OK";
}