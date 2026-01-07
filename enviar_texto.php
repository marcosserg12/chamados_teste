<?php
// Define o cabeçalho para retornar JSON
header('Content-Type: application/json');
ini_set('display_errors', 0); // Oculta erros para o cliente API
error_reporting(E_ALL);

// Configurações da Evolution API
$baseUrl = "http://145.223.26.225:8081/";
$instance = "Fiocruz";
// ⚠️ ATENÇÃO: É crucial proteger este token em um ambiente de produção!
// Considere usar variáveis de ambiente ou arquivos de configuração seguros.
$token = "D8C74399F179-4B14-932F-8634E1A81160";

// --- Lógica para obter os dados da requisição POST ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Método não permitido. Use POST."]);
    exit;
}

// Obtém o corpo da requisição POST
$input_data = json_decode(file_get_contents('php://input'), true);

// 1. VERIFICAÇÃO DO NÚMERO (OBRIGATÓRIO)
if (!isset($input_data['numero']) || empty($input_data['numero'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "O campo 'numero' é obrigatório."]);
    exit;
}
$numero = $input_data['numero'];

// 2. OBTÉM OS CAMPOS DE MENSAGEM (COM VALORES PADRÃO)
$titulo = $input_data['title'] ?? "CLINICA XYZ - Confirmação";
$descricao_principal = $input_data['description'] ?? "Olá, tudo bem? Precisamos confirmar algumas informações.";
$texto_botao = $input_data['buttonText'] ?? "TOQUE AQUI";
$texto_rodape = $input_data['footerText'] ?? "Informações adicionais:\nhttps://seudominio.com.br";
$titulo_secao = $input_data['sectionTitle'] ?? "VOCÊ CONFIRMA O AGENDAMENTO?";

// 3. OBTÉM AS DESCRIÇÕES DAS LINHAS (NOVO)
$descricao_sim = $input_data['descricao_sim'] ?? "Confirmo meu agendamento.";
$descricao_nao = $input_data['descricao_nao'] ?? "Não posso comparecer.";

// Endpoint
$url = $baseUrl . "message/sendList/" . $instance;

// Corpo da requisição para a Evolution API
$data = [
    "number" => $numero,
    "title" => $titulo,
    "description" => $descricao_principal,
    "buttonText" => $texto_botao,
    "footerText" => $texto_rodape,
    "sections" => [
        [
            "title" => $titulo_secao,
            "rows" => [
                [
                    "title" => "✅ SIM",
                    // Descrição DINÂMICA
                    "description" => $descricao_sim,
                    "rowId" => "resposta_sim"
                ],
                [
                    "title" => "❌ NÃO",
                    // Descrição DINÂMICA
                    "description" => $descricao_nao,
                    "rowId" => "resposta_nao"
                ]
            ]
        ]
    ]
];

// Cabeçalhos para a Evolution API
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
    // Erro de cURL (ex: problema de conexão, timeout)
    http_response_code(500); // Erro interno do servidor
    echo json_encode([
        "status" => "error",
        "message" => "Erro de cURL ao enviar mensagem.",
        "curl_error" => curl_error($ch)
    ]);
} else {
    // Resposta da Evolution API
    $api_response = json_decode($response);
    // Retorna o corpo da resposta para o cliente.
    echo $response;
}

curl_close($ch);
