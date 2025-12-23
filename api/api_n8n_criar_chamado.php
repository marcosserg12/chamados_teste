<?php
// api_n8n_criar_chamado.php
header('Content-Type: application/json');

// Inclua sua conexão e classes aqui
require_once 'conexao.php'; // Ajuste o caminho
// require_once 'classes/Chamado.php';

try {
    // 1. Recebe os dados crus do n8n
    $input = file_get_contents('php://input');
    $dados = json_decode($input, true);

    if (!$dados) {
        throw new Exception("Nenhum dado recebido");
    }

    $connection = Conecta::getConexao();
    $connection->beginTransaction();

    // 2. Insere o Chamado (Sua lógica original)
    $sql = "INSERT INTO tb_chamados (id_usuario, ds_titulo, ds_descricao, dt_data_chamado, id_empresa, id_localizacao, id_tipo_chamado, id_motivo_principal, id_motivo_associado, st_grau, st_status)
            VALUES (:id_usuario, :ds_titulo, :ds_descricao, NOW(), :id_empresa, :id_localizacao, :id_tipo_chamado, :id_motivo_principal, :id_motivo_associado, :st_grau, 0)";

    $stmt = $connection->prepare($sql);
    $stmt->execute([
        ':id_usuario' => $dados['id_usuario'],
        ':ds_titulo'  => $dados['ds_titulo'],
        ':ds_descricao' => $dados['ds_descricao'],
        ':id_empresa' => $dados['id_empresa'],
        ':id_localizacao' => $dados['id_localizacao'],
        ':id_tipo_chamado' => $dados['id_tipo_chamado'],
        ':id_motivo_principal' => $dados['id_motivo_principal'],
        ':id_motivo_associado' => $dados['id_motivo_associado'] ?? null,
        ':st_grau' => $dados['st_grau'] ?? null
    ]);

    $id_chamado = $connection->lastInsertId();

    // 3. Processa o Anexo (Se houver URL vinda do WhatsApp)
    if (!empty($dados['url_anexo']) && filter_var($dados['url_anexo'], FILTER_VALIDATE_URL)) {

        $dataHoje = date('Y-m-d');
        // Caminho físico no servidor (Ajuste o __DIR__ conforme sua estrutura)
        $pastaRelativa = "uploads/{$dataHoje}/{$id_chamado}/";
        $pastaAbsoluta = __DIR__ . "/../" . $pastaRelativa;

        // Cria a pasta
        if (!is_dir($pastaAbsoluta)) {
            mkdir($pastaAbsoluta, 0755, true);
        }

        // Gera nome único
        $extensao = pathinfo(parse_url($dados['url_anexo'], PHP_URL_PATH), PATHINFO_EXTENSION);
        if(!$extensao) $extensao = 'jpg'; // Fallback

        $nomeArquivo = uniqid("anexo_{$id_chamado}_") . "." . $extensao;
        $caminhoFinal = $pastaAbsoluta . $nomeArquivo;

        // BAIXA O ARQUIVO DO WHATSAPP E SALVA NO DISCO
        $conteudoArquivo = file_get_contents($dados['url_anexo']);
        if ($conteudoArquivo !== false) {
            file_put_contents($caminhoFinal, $conteudoArquivo);

            // Salva o caminho no banco rl_arquivo_chamado
            $caminhoBanco = $pastaRelativa . $nomeArquivo;
            $stmtArq = $connection->prepare("INSERT INTO rl_arquivo_chamado (id_chamado, ds_caminho_arquivo) VALUES (?, ?)");
            $stmtArq->execute([$id_chamado, $caminhoBanco]);
        }
    }

    $connection->commit();
    echo json_encode(['status' => 'sucesso', 'id_chamado' => $id_chamado]);

} catch (Exception $e) {
    if (isset($connection)) $connection->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
}
?>