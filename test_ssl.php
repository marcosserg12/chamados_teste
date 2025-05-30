<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.twilio.com");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Erro CURL: ' . curl_error($ch);
} else {
    echo "Conexão SSL funcionando!";
}

curl_close($ch);