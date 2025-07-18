<?php
require __DIR__ . '/../vendor/autoload.php';

try {
    $chamados = new Chamados();
    $id_empresa = $_REQUEST['id_empresa'] ?? null;
    $id_usuario = $_REQUEST['id_usuario'] ?? null;

    $resultado = $chamados->lista_localizacao_usuario($id_empresa,$id_usuario);

    echo json_encode($resultado);
} catch (Exception $e) {
    echo json_encode([
        'message' => 'ERRO: ' . $e->getMessage(),
        'data' => []
    ], JSON_UNESCAPED_UNICODE);
}
