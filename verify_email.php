<?php
$curl = curl_init();

$data = [
    'email' =>$_POST['email'],
];

$post_data = http_build_query($data);

curl_setopt_array($curl, array(
    CURLOPT_URL => "https://disify.com/api/email",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => $post_data,
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "CURL Error #:" . $err;
} else {
    echo $response;
}