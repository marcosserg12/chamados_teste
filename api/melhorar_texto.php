<?php
// Arquivo: api/melhorar_texto.php
header("Content-Type: application/json");
$envPath = dirname(__DIR__) . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        putenv(trim($line));
    }
}

// Pegue o conteúdo da requisição
$data = json_decode(file_get_contents("php://input"), true);
$texto = $data['texto'] ?? '';

if (!$texto) {
    echo json_encode(["erro" => "Texto vazio."]);
    exit;
}

// Sua chave secreta da OpenAI
$apiKey = getenv('OPENAI_API_KEY');

// Parâmetros da chamada à OpenAI
$payload = [
    "model" => "gpt-3.5-turbo",
    "messages" => [
        ["role" => "system", "content" => "Você é um assistente que reescreve textos de forma mais clara e profissional."],
        ["role" => "user", "content" => "Melhore este texto: " . $texto]
    ],
    "temperature" => 0.7
];

// Faz a requisição cURL
$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $apiKey"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
$resposta = curl_exec($ch);
curl_close($ch);

$respostaJson = json_decode($resposta, true);
$melhorado = $respostaJson['choices'][0]['message']['content'] ?? null;

echo json_encode(["melhorado" => trim($melhorado)]);
