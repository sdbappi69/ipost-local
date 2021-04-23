<?php

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://tm-ipost.publicdemo.xyz/generate",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"sub_order_id\"\r\n\r\n45\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"unique_suborder_id\"\r\n\r\nDCLUFY4701\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"task_type_id\"\r\n\r\n1\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"picking_title\"\r\n\r\nTest Product 1\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"picking_address\"\r\n\r\nH/O: XX, Road: YY\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"picking_latitude\"\r\n\r\n36.077637\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"picking_longitude\"\r\n\r\n43.655946\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"delivery_title\"\r\n\r\nTest Miah\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"delivery_address\"\r\n\r\nWest Azerbaijan Province, 26, Iran\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"delivery_latitude\"\r\n\r\n36.014084\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"delivery_longitude\"\r\n\r\n43.700395\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"quantity\"\r\n\r\n1\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"amount\"\r\n\r\n15000\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"hub_id\"\r\n\r\n6\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"hub_title\"\r\n\r\nDemo Kurd Hub\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--",
  CURLOPT_HTTPHEADER => array(
    "Accept: */*",
    "Accept-Encoding: gzip, deflate",
    "Cache-Control: no-cache",
    "Connection: keep-alive",
    "Content-Length: 1881",
    "Content-Type: multipart/form-data; boundary=--------------------------383261996796761110029267",
    "Host: tm-ipost.publicdemo.xyz",
    "Postman-Token: e43be613-2ec7-4f36-be3f-2fa982d158ea,1bb08023-0f84-40e2-ac0c-2a2284491529",
    "User-Agent: PostmanRuntime/7.20.1",
    "cache-control: no-cache",
    "content-type: multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}