<?php

require __DIR__ . '/../vendor/autoload.php';

try{
    $chamados = new Chamados();

    $id_chamado = $chamados->atribuirFuncoesUsuario($_POST);

    echo json_response([
        'message' => 'Chamado atribuído com sucesso!',
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