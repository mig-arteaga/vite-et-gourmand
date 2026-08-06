<?php

header('Content-Type: application/json');

try {
    $apiKey = "AIzaSyBZoRUyakwYeNp2phgZBUNDEvnWlO2Vv1c";

    $address = $_POST['address'] ?? '';
    $city = $_POST['city'] ?? '';
    $zipcode = $_POST['zipcode'] ?? '';

    $destination = "$address, $zipcode $city";

    $data = [
        "origin" => [
            "address" => "3 Place Notre Dame, 38000 Grenoble"
        ],
        "destination" => [
            "address" => $destination
        ],
        "travelMode" => "DRIVE"
    ];

    $headers = [
        "Content-Type: application/json",
        "X-Goog-Api-Key: $apiKey",
        "X-Goog-FieldMask: routes.distanceMeters,routes.duration"
    ];

    $ch = curl_init("https://routes.googleapis.com/directions/v2:computeRoutes");

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    $response = curl_exec($ch);

    if ($response === false) {
        throw new Exception(curl_error($ch));
    }

    $result = json_decode($response, true);

    if (empty($result)) {
        throw new Exception("Empty response from Google API");
    }

    echo json_encode($result);

} catch (Exception $e) {
    echo json_encode([
        "error" => $e->getMessage()
    ]);
}

?>