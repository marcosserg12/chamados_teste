<?php

require __DIR__ . '/../vendor/autoload.php';

try{
    $usuario = new Usuario();
    $id_usuario = $_POST["id_usuario"];
    $id_usuario = $usuario->excluirUsuario($id_usuario);

}catch (Exception $exception){
    echo json_response([
        'message' => 'ERRO: ' . $exception->getMessage(), 
        'data' => []
    ], 500);
}