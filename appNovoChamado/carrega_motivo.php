<?php

require __DIR__ . '/../vendor/autoload.php';

try {
    $chamados = new Chamados();
    $id_tipo_chamado = $_REQUEST['id_tipo_chamado'] ?? null;

    $resultado = $chamados->lista_motivo($id_tipo_chamado);

    echo json_encode($resultado);
} catch (Exception $e) {
    echo json_encode([
        'message' => 'ERRO: ' . $e->getMessage(),
        'data' => []
    ], JSON_UNESCAPED_UNICODE);
}
