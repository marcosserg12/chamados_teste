<?php

require __DIR__ . '/../vendor/autoload.php';

try{
    $chamados = new Chamados();

    $id_chamado = $chamados->atribuirFuncoesUsuario($_POST);
    $dados = $chamados->mostrarChamado($_POST['id_chamado']);
    $nu_telefone_dono = $chamados->telefoneDono($_POST['id_chamado']);
    $nu_telefone_responsavel = $chamados->verificarNumeroTelChamado($_POST['id_chamado']);

    $evolutionSender = new EvolutionWhatsAppSender(
        'http://145.223.26.225:8081/',
        'B825E8E34BE9-4486-AD4E-C4B8ECA49257',
        'teste'
    );



     // Envia mensagem para o responsável pelo chamado
    $mensagemResponsavel = "Chamado  foi atribuído para você : \n" .
                    "Titulo: {$dados['ds_titulo']}\n" .
                    "Localização: {$dados['ds_localizacao']}\n" .
                    "Empresa: {$dados['ds_empresa']}\n" .
                    "*Confira em:* \n" .
                    "https://chamados.sisibranutro.com.br/Telas/Detalhe_chamado.php?id_chamado={$_POST['id_chamado']}";


    $responsavel = '55' . $nu_telefone_responsavel;
    $evolutionSender->sendMessage($responsavel, $mensagemResponsavel);

     // Envia mensagem para o dono do chamado
    $mensagemDono = "Chamado #{$_POST['id_chamado']} foi atribuído para: * {$dados['designado']} *: \n" .
                    "Titulo: {$dados['ds_titulo']}\n" .
                    "Localização: {$dados['ds_localizacao']}\n" .
                    "Empresa: {$dados['ds_empresa']}\n" .
                    "*Confira em:* \n" .
                    "https://chamados.sisibranutro.com.br/Telas/Detalhe_chamado.php?id_chamado={$_POST['id_chamado']}";


    $dono = '55' . $nu_telefone_dono;
    $evolutionSender->sendMessage($dono, $mensagemDono);

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