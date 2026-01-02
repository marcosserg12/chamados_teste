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

    if($dados['st_grau'] == 777) {
        $dados['st_grau'] = null;
    }

    // 2. Insert Principal
    $sql = "INSERT INTO tb_chamados (
                id_usuario, ds_titulo, ds_descricao, dt_data_chamado,
                id_empresa, id_localizacao, id_tipo_chamado,
                id_motivo_principal, id_motivo_associado, st_grau
            ) VALUES (
                :id_usuario, :ds_titulo, :ds_descricao, :dt_data_chamado,
                :id_empresa, :id_localizacao, :id_tipo_chamado,
                :id_motivo_principal, :id_motivo_associado, :st_grau
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
        ':st_grau' => $dados['st_grau'] ?? null
    ]);

    $id_chamado = $connection->lastInsertId();

    // 3. Processamento do Anexo (Versão Inteligente para Documentos e Imagens)
    if ($temAnexo) {

        $dataPasta = date('Y-m-d');
        $pastaRelativa = "uploads/{$dataPasta}/{$id_chamado}/";
        $pastaAbsoluta = __DIR__ . "/../" . $pastaRelativa;

        if (!is_dir($pastaAbsoluta)) {
            mkdir($pastaAbsoluta, 0755, true);
        }

        $dadoAnexo = $dados['url_anexo'];
        $conteudoArquivo = false;
        $extensao = 'jpg'; // Fallback padrão

        // A. Verifica se é URL (Link http...)
        if (filter_var($dadoAnexo, FILTER_VALIDATE_URL)) {
            $conteudoArquivo = file_get_contents($dadoAnexo);
            $pathInfo = pathinfo(parse_url($dadoAnexo, PHP_URL_PATH));
            if (isset($pathInfo['extension'])) {
                $extensao = $pathInfo['extension'];
            }
        }
        // B. Se é BASE64 (Texto gigante)
        else {
            // Detecta o Mime-Type no cabeçalho (ex: data:application/pdf;base64,...)
            if (preg_match('/^data:([\w\/.-]+);base64,/', $dadoAnexo, $matches)) {
                $mimeType = $matches[1];

                // Mapa de Extensões comuns
                $mapaExtensoes = [
                    'application/pdf' => 'pdf',
                    'application/msword' => 'doc',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
                    'application/vnd.ms-excel' => 'xls',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/webp' => 'webp'
                ];

                if (isset($mapaExtensoes[$mimeType])) {
                    $extensao = $mapaExtensoes[$mimeType];
                }

                // Remove o cabeçalho para decodificar apenas o arquivo
                $dadoAnexo = substr($dadoAnexo, strpos($dadoAnexo, ',') + 1);
            }
            // Se não tiver cabeçalho, confia no tipo enviado pelo n8n
            else if (isset($dados['tipo']) && $dados['tipo'] == 'documento') {
                $extensao = 'pdf'; // Chute seguro para documentos sem header
            }

            $dadoAnexo = str_replace(' ', '+', $dadoAnexo); // Corrige espaços
            $conteudoArquivo = base64_decode($dadoAnexo);
        }

        // C. Salva
        if ($conteudoArquivo !== false) {
            $nomeArquivo = uniqid("anexo_{$id_chamado}_") . "." . $extensao;
            $caminhoFinal = $pastaAbsoluta . $nomeArquivo;

            file_put_contents($caminhoFinal, $conteudoArquivo);

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
