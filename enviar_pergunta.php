<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configurações da Evolution API
$baseUrl = "http://145.223.26.225:8081/";
$instance = "MeuNumero";
$token = "F32F86C62D6B-4092-940F-2965C534E61B";

// Endpoint
$url = $baseUrl . "message/sendList/" . $instance;

// Número do destinatário (com domínio @s.whatsapp.net)
$numero = "5561982616352"; // Exemplo

// Corpo da requisição
$data = [
    "number" => $numero,
    "title" => "CLINICA XYZ - Confirmação",
    "description" => "Olá, tudo bem? Precisamos confirmar algumas informações.",
    "buttonText" => "TOQUE AQUI",
    "footerText" => "Informações adicionais:\nhttps://seudominio.com.br",
    "sections" => [
        [
            "title" => "VOCÊ CONFIRMA O AGENDAMENTO?",
            "rows" => [
                [
                    "title" => "✅ SIM",
                    "description" => "Confirmo meu agendamento.",
                    "rowId" => "resposta_sim"
                ],
                [
                    "title" => "❌ NÃO",
                    "description" => "Não posso comparecer.",
                    "rowId" => "resposta_nao"
                ]
            ]
        ]
    ]
];

// Cabeçalhos
$headers = [
    "Content-Type: application/json",
    "apikey: $token"
];

// Envio via cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if ($response === false) {
    echo "Erro ao enviar: " . curl_error($ch);
} else {
    echo "Resposta Evolution API:\n" . $response;
}

curl_close($ch);
