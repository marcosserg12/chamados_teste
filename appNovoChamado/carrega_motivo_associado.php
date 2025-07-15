<?php

require __DIR__ . '/../vendor/autoload.php';

try {
    $chamados = new Chamados();
    $id_motivo_principal = $_REQUEST['id_motivo_principal'] ?? null;

    $resultado = $chamados->lista_motivo_associado($id_motivo_principal);

    echo json_encode($resultado);
} catch (Exception $e) {
    echo json_encode([
        'message' => 'ERRO: ' . $e->getMessage(),
        'data' => []
    ], JSON_UNESCAPED_UNICODE);
}
