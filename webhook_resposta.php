<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexão com banco
$pdo = new PDO("mysql:host=145.223.26.225;port=3306;dbname=chamado;charset=utf8", "marcos", "M@rcos648209");

// Recebe o JSON enviado pela Evolution
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Verifica se é uma resposta de lista
if(isset($data['type']) && $data['type'] === 'list_response') {
    $numero = $data['number'];
    $resposta = $data['rowId'];
    $mensagem = $data['title'] ?? '';
    $hora = date('Y-m-d H:i:s');

    // Salvar no banco
    $stmt = $pdo->prepare("INSERT INTO respostas_whatsapp (numero, resposta, mensagem, data_hora) VALUES (:numero, :resposta, :mensagem, :hora)");
    $stmt->execute([
        ':numero' => $numero,
        ':resposta' => $resposta,
        ':mensagem' => $mensagem,
        ':hora' => $hora
    ]);

    echo json_encode(["status" => "ok"]);
} else {
    echo json_encode(["status" => "tipo inválido"]);
}
