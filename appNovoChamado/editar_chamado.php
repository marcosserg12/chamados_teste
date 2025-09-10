<?php

require __DIR__ . '/../vendor/autoload.php';

try {
    $chamados = new Chamados();

    $id_chamado = $chamados->editarChamado($_POST);
    $nu_telefone_dono = $chamados->telefoneDono($_POST['id_chamado']);
    $nu_telefone_responsavel = $chamados->verificarNumeroTelChamado($_POST['id_chamado']);

    $evolutionSender = new EvolutionWhatsAppSender(
        'http://145.223.26.225:8081/',
        'B825E8E34BE9-4486-AD4E-C4B8ECA49257',
        'teste'
    );



    // Envia mensagem para o responsável e dono do chamado
    $mensagem = "Chamado *#{$_POST['id_chamado']}* Alterado: \n" .
        "Titulo: *{$_POST['ds_titulo']}* \n" .
        "*Confira em:* \n" .
        "https://chamados.sisibranutro.com.br/Telas/Detalhe_chamado.php?id_chamado={$_POST['id_chamado']}";


    $responsavel = '55' . $nu_telefone_responsavel;
    $dono = '55' . $nu_telefone_dono;
    $evolutionSender->sendMessage($responsavel, $mensagem);
    $evolutionSender->sendMessage($dono, $mensagem);


    echo json_response([
        'message' => 'Chamado editado com sucesso!',
        'data' => [
            'id_chamado' => $id_chamado
        ]
    ]);
} catch (Exception $exception) {
    echo json_response([
        'message' => 'ERRO: ' . $exception->getMessage(),
        'data' => []
    ], 500);
}
