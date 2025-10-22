<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- ARQUIVOS DE LOG ---
// Crie estes arquivos no mesmo diretório do script e dê permissão de escrita (ex: chmod 777)
$log_file = 'webhook_log.txt'; // Para registrar o JSON recebido
$error_file = 'webhook_erros.txt'; // Para registrar erros de banco

// Função para logar erros
function log_error($message)
{
    global $error_file;
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n\n", 3, $error_file);
}

// --- BANCO DE DADOS ---
$dsn = "mysql:host=145.223.26.225;port=3306;dbname=chamado;charset=utf8";
$username = "marcos";
$password = "M@rcos648209";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (\PDOException $e) {
    log_error("Erro de Conexão PDO: " . $e->getMessage());
    http_response_code(500); // Erro interno do servidor
    echo json_encode(["status" => "erro", "message" => "Erro de conexão DB."]);
    exit; // Para a execução
}

// --- PROCESSAMENTO DO WEBHOOK ---

// 1. Recebe e LOGA o JSON cru
$input = file_get_contents('php://input');
file_put_contents($log_file, date('[Y-m-d H:i:s] ') . $input . "\n\n", FILE_APPEND);

// 2. Decodifica o JSON
$data = json_decode($input, true);

// 3. Verifica se o JSON é válido
if (json_last_error() !== JSON_ERROR_NONE) {
    log_error("Erro ao decodificar JSON. Recebido: " . $input);
    echo json_encode(["status" => "erro", "message" => "JSON inválido."]);
    exit;
}

// 4. VERIFICA A ESTRUTURA CORRETA (ESTA É A PARTE MAIS IMPORTANTE)
// A Evolution API geralmente aninha a resposta de lista_
$resposta = null;
$mensagem_titulo = null;
$numero = null;

// Verifica se é um evento de mensagem e se contém uma 'listResponseMessage'
if (isset($data['data']['message']['listResponseMessage'])) {

    // Pega o ID da linha selecionada
    $resposta = $data['data']['message']['listResponseMessage']['singleSelectReply']['selectedRowId'] ?? null;

    // Pega o título da opção que o usuário clicou
    $mensagem_titulo = $data['data']['message']['listResponseMessage']['title'] ?? '';

    // Pega o número de quem enviou (geralmente vem no campo 'sender' ou 'key.remoteJid')
    $numero = $data['sender'] ?? $data['data']['key']['remoteJid'] ?? null;

    // Limpa o número para salvar no banco (remove o @s.whatsapp.net)
    if ($numero) {
        $numero = preg_replace('/@s\.whatsapp\.net$/', '', $numero);
    }
}

// 5. Se encontramos uma resposta de lista, salva no banco
if ($resposta !== null && $numero !== null) {

    $hora = date('Y-m-d H:i:s');

    try {
        // Salvar no banco
        $stmt = $pdo->prepare("INSERT INTO respostas_whatsapp (numero, resposta, mensagem, data_hora) VALUES (:numero, :resposta, :mensagem, :hora)");
        $stmt->execute([
            ':numero' => $numero,
            ':resposta' => $resposta,      // Ex: "resposta_sim" ou "resposta_nao"
            ':mensagem' => $mensagem_titulo, // Ex: "✅ SIM"
            ':hora' => $hora
        ]);

        echo json_encode(["status" => "ok", "message" => "Resposta registrada."]);
    } catch (\PDOException $e) {
        // Loga o erro específico do INSERT
        log_error("Erro no INSERT: " . $e->getMessage() . " | Dados: numero=$numero, resposta=$resposta, mensagem=$mensagem_titulo");
        echo json_encode(["status" => "erro", "message" => "Erro ao salvar no DB."]);
    }
} else {
    // Se não for uma resposta de lista, apenas informa (pode ser uma msg de status, etc)
    echo json_encode(["status" => "ignorado", "message" => "Evento não é uma resposta de lista."]);
}
