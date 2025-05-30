<?php
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);

require __DIR__ . '/../vendor/autoload.php';

try{
    $usuario = new Usuario();

    $usuario->recuperarSenha($_POST);  

    echo json_response([
        'message' => 'Foi enviado para seu e-mail um link para redefinir a senha.', 
        'data' => [

        ]
    ]);

}catch (Exception $exception){
    echo json_response([
        'message' => 'ERRO: ' . $exception->getMessage(),
        'data' => []
    ], 500);
}