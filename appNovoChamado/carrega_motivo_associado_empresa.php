<?php

require __DIR__ . '/../vendor/autoload.php';

try {
    $chamados = new Chamados();
    $id_motivo_principal = $_REQUEST['id_motivo_principal'] ?? null;
    $id_empresa = $_REQUEST['id_empresa'] ?? null;

    $resultado = $chamados->lista_motivo_associado_empresa($id_motivo_principal,$id_empresa);

    echo json_encode($resultado);
} catch (Exception $e) {
    echo json_encode([
        'message' => 'ERRO: ' . $e->getMessage(),
        'data' => []
    ], JSON_UNESCAPED_UNICODE);
}
