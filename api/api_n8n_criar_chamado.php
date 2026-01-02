<?php
// api_n8n_criar_chamado.php
header('Content-Type: application/json');
date_default_timezone_set('America/Sao_Paulo');

require_once '../Classes/Conecta.php';

try {
    // 1. Recebe e valida dados
    $input = file_get_contents('php://input');
    $dados = json_decode($input, true);

    if (!$dados) throw new Exception("Nenhum dado JSON recebido");

    $connection = Conecta::getConexao();
    $connection->beginTransaction();

    $dataAtual = date('Y-m-d H:i:s');

    // Lógica st_anexo
    $temAnexo = !empty($dados['url_anexo']) && $dados['url_anexo'] !== "";
    $st_anexo = $temAnexo ? 'S' : 'N';

    // 2. Insert Principal
    $sql = "INSERT INTO tb_chamados (
                id_usuario, ds_titulo, ds_descricao, dt_data_chamado,
                id_empresa, id_localizacao, id_tipo_chamado,
                id_motivo_principal, id_motivo_associado, st_grau, st_status, st_anexo
            ) VALUES (
                :id_usuario, :ds_titulo, :ds_descricao, :dt_data_chamado,
                :id_empresa, :id_localizacao, :id_tipo_chamado,
                :id_motivo_principal, :id_motivo_associado, :st_grau, 0, :st_anexo
            )";

    $stmt = $connection->prepare($sql);
    $stmt->execute([
        ':id_usuario' => $dados['id_usuario'],
        ':ds_titulo'  => $dados['ds_titulo'],
        ':ds_descricao' => $dados['ds_descricao'],
        ':dt_data_chamado' => $dataAtual,
        ':id_empresa' => $dados['id_empresa'],
        ':id_localizacao' => $dados['id_localizacao'],
        ':id_tipo_chamado' => $dados['id_tipo_chamado'],
        ':id_motivo_principal' => $dados['id_motivo_principal'],
        ':id_motivo_associado' => !empty($dados['id_motivo_associado']) ? $dados['id_motivo_associado'] : null,
        ':st_grau' => $dados['st_grau'] ?? null,
        ':st_anexo' => $st_anexo
    ]);

    $id_chamado = $connection->lastInsertId();

    // 3. Processamento do Anexo (Híbrido: URL ou Base64)
    if ($temAnexo) {

        // Prepara pastas
        $dataPasta = date('Y-m-d');
        $pastaRelativa = "uploads/{$dataPasta}/{$id_chamado}/";
        $pastaAbsoluta = __DIR__ . "/../" . $pastaRelativa;

        if (!is_dir($pastaAbsoluta)) {
            mkdir($pastaAbsoluta, 0755, true);
        }

        $dadoAnexo = $dados['url_anexo'];
        $conteudoArquivo = false;
        $extensao = 'jpg'; // Padrão

        // A. Verifica se é URL (Começa com http)
        if (filter_var($dadoAnexo, FILTER_VALIDATE_URL)) {
            $conteudoArquivo = file_get_contents($dadoAnexo);

            // Tenta pegar extensão da URL
            $pathInfo = pathinfo(parse_url($dadoAnexo, PHP_URL_PATH));
            if (isset($pathInfo['extension'])) {
                $extensao = $pathInfo['extension'];
            }
        }
        // B. Se não é URL, assume que é Base64
        else {
            // Limpa cabeçalho se houver (ex: data:image/png;base64,...)
            if (preg_match('/^data:image\/(\w+);base64,/', $dadoAnexo, $type)) {
                $dadoAnexo = substr($dadoAnexo, strpos($dadoAnexo, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif
                $extensao = $type;
            } else {
                // Tenta definir extensão pelo tipo informado pelo n8n
                if (isset($dados['tipo']) && $dados['tipo'] == 'documento') {
                    $extensao = 'pdf';
                }
            }
            $dadoAnexo = str_replace(' ', '+', $dadoAnexo);
            $conteudoArquivo = base64_decode($dadoAnexo);
        }

        // C. Salva o Arquivo Fisicamente
        if ($conteudoArquivo !== false) {
            $nomeArquivo = uniqid("anexo_{$id_chamado}_") . "." . $extensao;
            $caminhoFinal = $pastaAbsoluta . $nomeArquivo;

            file_put_contents($caminhoFinal, $conteudoArquivo);

            // D. Salva referência no Banco
            $caminhoParaBanco = $dataPasta . "/" . $id_chamado . "/" . $nomeArquivo;
            $stmtArq = $connection->prepare("INSERT INTO rl_arquivo_chamado (id_chamado, ds_caminho_arquivo) VALUES (?, ?)");
            $stmtArq->execute([$id_chamado, $caminhoParaBanco]);
        }
    }

    $connection->commit();
    echo json_encode([
        'status' => 'sucesso',
        'id_chamado' => $id_chamado,
        'tem_anexo' => $st_anexo,
        'mensagem' => 'Chamado criado.'
    ]);

} catch (Exception $e) {
    if (isset($connection)) $connection->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
}
?>