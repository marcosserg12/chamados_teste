<?php
include '../scripts.php';
require '../vendor/autoload.php';

$id_arquivo = $_POST['id_arquivo'] ?? null;
if (!$id_arquivo) {
    http_response_code(400);
    exit('ID não informado.');
}

$chamados = new Chamados();
$arquivo = $chamados->buscarArquivo($id_arquivo);
if ($arquivo && file_exists('../uploads/' . $arquivo['ds_caminho_arquivo'])) {
    unlink('../uploads/' . $arquivo['ds_caminho_arquivo']);
}
$chamados->excluirArquivo($id_arquivo);
echo 'ok';
