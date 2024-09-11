<?php
include 'connect-db.php';

$title = $_POST['title'] ?: '';
$message = $_POST['message'];
$launch_url = $_POST['launch_url'] ?: '';

sendNotification($title, $message, $launch_url);

function sendNotification($title, $message, $launch_url)
{
    $app_id = "";
    $rest_api_key = "";
    $image_path = '';
    if ($_FILES) {
        $uploads_dir = $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']) . '/images';
        @$tmp_name = $_FILES['img_path']["tmp_name"];
        @$name = $_FILES['img_path']["name"];
        $uniquenumber1 = rand(20000, 32000);
        $uniquenumber2 = rand(20000, 32000);
        $uniquenumber3 = rand(20000, 32000);
        $uniquenumber4 = rand(20000, 32000);
        $uniquename = $uniquenumber1 . $uniquenumber2 . $uniquenumber3 . $uniquenumber4;
        $image_path = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://" . $uploads_dir . "/" . $uniquename . $name;
        @move_uploaded_file($tmp_name, "$uploads_dir/$uniquename$name");
    }

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://onesignal.com/api/v1/notifications',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
  "app_id": ' . $app_id . ',
  "included_segments": ["Total Subscriptions"],
  "headings": {"en": "' . $title . '"},
  "data": {"foo": "bar"},
  "contents": {"en": "' . $message . '"},
  "web_url" : "' . 'https://' . $launch_url . '",
  "chrome_web_image": "' . $image_path . '"
}',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic ' . $rest_api_key,
            'Content-Type: application/json',
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    $responsearr = json_decode($response, true);
    if ($responsearr['id']) {
        header("Location:index.html?status=success");
    } else {
        header("Location:index.html?status=error");
    }
}