<?php

require __DIR__ . '/../vendor/autoload.php';

try{
    $chamados = new Chamados();

    $id_chamado = $chamados->mudarStatusChamado($_POST['st_status'],$_POST['id_chamado'],$_POST['id_usuario']);

    echo json_response([
        'message' => 'Chamado alterado com sucesso!',
        'data' => [
            'id_chamado' => $id_chamado
        ]
    ]);

}catch (Exception $exception){
    echo json_response([
        'message' => 'ERRO: ' . $exception->getMessage(),
        'data' => []
    ], 500);
}