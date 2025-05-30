<?php

require_once("Conecta.php");
date_default_timezone_set('America/Sao_Paulo');

class Chamados
{

    function listaChamados()
    {
        $con = Conecta::getConexao();

        $select = "SELECT c.*,u1.ds_nome,u2.ds_nome,rl.dt_aceito FROM tb_chamados c
        left join tb_usuario u1 on u1.id_usuario = c.id_usuario
        left join rl_chamado_usuario rl on rl.id_chamado = c.id_chamado
        left join tb_usuario u2 on u2.id_usuario = rl.id_usuario";

        $stmt = $con->prepare($select);


        $stmt->execute();
        $dados = $stmt->fetchAll();

        return $dados;
    }

    function listaChamadosRecentes($id_perfil, $id_usuario)
    {
        $con = Conecta::getConexao();
        if ($id_perfil == 1) {
            $where = "rl.id_usuario = " . $id_usuario . " or c.st_status = 0";
        } else {
            $where = "c.id_usuario = " . $id_usuario;
        }

        $select = "SELECT c.*,u1.ds_nome,u2.ds_nome,rl.dt_aceito FROM tb_chamados c
        left join tb_usuario u1 on u1.id_usuario = c.id_usuario
        left join rl_chamado_usuario rl on rl.id_chamado = c.id_chamado
        left join tb_usuario u2 on u2.id_usuario = rl.id_usuario
        where $where
        order by c.id_chamado desc limit 10";

        $stmt = $con->prepare($select);


        $stmt->execute();
        $dados = $stmt->fetchAll();

        return $dados;
    }

    function totalChamados($id_perfil, $id_usuario)
    {
        $con = Conecta::getConexao();
        if ($id_perfil == 1) {
            $where = "rl.id_usuario = " . $id_usuario . " or c.st_status = 0";
        } else {
            $where = "c.id_usuario = " . $id_usuario;
        }

        $select = "SELECT  COUNT(CASE WHEN c.st_status = 0 THEN 1 END) AS aberto,
            COUNT(CASE WHEN c.st_status = 1 THEN 1 END) AS andamento,
            COUNT(CASE WHEN c.st_status = 9 THEN 1 END) AS resolvidos
        FROM tb_chamados c
        left join tb_usuario u1 on u1.id_usuario = c.id_usuario
        left join rl_chamado_usuario rl on rl.id_chamado = c.id_chamado
        left join tb_usuario u2 on u2.id_usuario = rl.id_usuario
        where $where
        order by c.id_chamado desc ";

        $stmt = $con->prepare($select);


        $stmt->execute();
        $dados = $stmt->fetch();

        return $dados;
    }


    public function gravar(array $dados)
    {
        $connection = Conecta::getConexao();

        $sql = "
                INSERT INTO tb_chamados
                (
                    id_usuario,
                    ds_titulo,
                    ds_descricao,
                    dt_data_chamado
                ) VALUES (
                    :id_usuario,
                    :ds_titulo,
                    :ds_descricao,
                    :dt_data_chamado
                );
            ";


        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $dados['id_usuario'],
            ':ds_titulo' => $dados['ds_titulo'],
            ':ds_descricao' => $dados['ds_descricao'],
            ':dt_data_chamado' => date('Y-m-d H:i:s'),
        ]);
        $id_chamado = $connection->lastInsertId();
        $this->salvarArquivosChamado($id_chamado);

        return $id_chamado;
    }
    public function salvarArquivosChamado($id_chamado)
    {
        if (!isset($_FILES['arquivo']) || !is_array($_FILES['arquivo']['name'])) {
            // Nenhum arquivo enviado ou formato errado - apenas continua
            return;
        }

        $arquivos = $_FILES['arquivo'];
        $totalArquivos = count($arquivos['name']);

        if ($totalArquivos == 0) {
            // Nenhum arquivo para processar - apenas continua
            return;
        }

        $dataHoje = date('Y-m-d');
        $pastaDestino = __DIR__ . "/../uploads/{$dataHoje}/{$id_chamado}/";

        // Cria a pasta, se necessário
        if (!is_dir($pastaDestino)) {
            mkdir($pastaDestino, 0755, true);
        }

        $connection = Conecta::getConexao();
        if (!$connection) {
            // Falha na conexão - apenas retorna
            return;
        }

        for ($i = 0; $i < $totalArquivos; $i++) {
            $nomeOriginal = $arquivos['name'][$i];
            $nomeTemp = $arquivos['tmp_name'][$i];
            $erro = $arquivos['error'][$i];

            if ($erro === UPLOAD_ERR_NO_FILE) {
                // Nenhum arquivo enviado para este índice - apenas continua
                continue;
            }

            if ($erro === UPLOAD_ERR_OK) {
                $extensao = pathinfo($nomeOriginal, PATHINFO_EXTENSION);
                $nomeUnico = uniqid('chamado_' . $id_chamado . '_', true) . '.' . $extensao;
                $caminhoFinal = $pastaDestino . $nomeUnico;

                if (move_uploaded_file($nomeTemp, $caminhoFinal)) {
                    $stmt = $connection->prepare("INSERT INTO rl_arquivo_chamado (id_chamado, ds_caminho_arquivo) VALUES (?, ?)");
                    $stmt->execute([$id_chamado, "{$dataHoje}/{$id_chamado}/{$nomeUnico}"]);
                }
                // Se move_uploaded_file falhar, apenas ignora este arquivo
            }
            // Se erro diferente de OK, apenas ignora este arquivo
        }
    }





    public function listarOptionsMedicos($id_usuario = null)
    {
        try {
            $con = Conecta::getConexao();

            $select = "SELECT DISTINCT u.id_usuario, ds_nome
                        FROM tb_usuario u INNER JOIN rl_usuario_hospital rl on u.id_usuario=rl.id_usuario-- and id_hospital = :id_hospital
                        WHERE st_ativo = 'A'
                        and id_perfil = 4";

            $stmt = $con->prepare($select);
            // $params = array(':id_hospital' => $id_hospital);
            // $stmt->execute($params);
            $stmt->execute();

            $options = "";

            while ($dados = $stmt->fetch()) {
                if ($dados['id_usuario'] == $id_usuario) {
                    $options .= "<option value='" . $dados['id_usuario'] . "' selected>" . $dados['ds_nome'] . "</option>";
                } else {
                    $options .= "<option value='" . $dados['id_usuario'] . "'>" . $dados['ds_nome'] . "</option>";
                }
            }
            return $options;
        } catch (exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            print $e->getMessage();
        }
    }

    public function alterarStatus(int $id_usuario, $status): void
    {
        $connection = Conecta::getConexao();

        $query = "
            UPDATE tb_usuario
            SET st_ativo = :status
            WHERE id_usuario = :id_usuario
        ";

        $stmt = $connection->prepare($query);
        $stmt->execute([':id_usuario' => $id_usuario, ':status' => $status]);
    }

    function mostrarChamado($id_chamado)
    {
        $con = Conecta::getConexao();

        $select = "SELECT c.*,u1.ds_nome as criador,u2.ds_nome as designado,rl.dt_aceito,rl.id_usuario as id_usuario_designado FROM tb_chamados c
        left join tb_usuario u1 on u1.id_usuario = c.id_usuario
        left join rl_chamado_usuario rl on rl.id_chamado = c.id_chamado
        left join tb_usuario u2 on u2.id_usuario = rl.id_usuario
        where c.id_chamado = :id_chamado";

        $stmt = $con->prepare($select);


        $stmt->execute([':id_chamado' => $id_chamado]);
        $dados = $stmt->fetch();

        return $dados;
    }
    function mostrararquivosChamado($id_chamado)
    {
        $con = Conecta::getConexao();

        $select = "SELECT rla.* FROM tb_chamados c
        left join rl_arquivo_chamado rla on rla.id_chamado = c.id_chamado
        where c.id_chamado = :id_chamado";

        $stmt = $con->prepare($select);


        $stmt->execute([':id_chamado' => $id_chamado]);
        $dados = $stmt->fetchAll();

        return $dados;
    }
    function meusChamados($id_perfil, $id_usuario)
    {
        $con = Conecta::getConexao();
        if ($id_perfil == 1) {
            $where = "rl.id_usuario = " . $id_usuario . " ";
        } else {
            $where = "c.id_usuario = " . $id_usuario;
        }

        $select = "SELECT c.*,u1.ds_nome,u2.ds_nome,rl.dt_aceito FROM tb_chamados c
        left join tb_usuario u1 on u1.id_usuario = c.id_usuario
        left join rl_chamado_usuario rl on rl.id_chamado = c.id_chamado
        left join tb_usuario u2 on u2.id_usuario = rl.id_usuario
        where $where
        order by c.id_chamado desc";

        $stmt = $con->prepare($select);


        $stmt->execute();
        $dados = $stmt->fetchAll();

        return $dados;
    }
    function todosChamados()
    {
        $con = Conecta::getConexao();

        $select = "SELECT c.*,u1.ds_nome as criado,u2.ds_nome as atribuido,rl.dt_aceito FROM tb_chamados c
        left join tb_usuario u1 on u1.id_usuario = c.id_usuario
        LEFT JOIN rl_chamado_usuario rl ON rl.id_chamado = c.id_chamado
        left join tb_usuario u2 on u2.id_usuario = rl.id_usuario
        order by c.dt_data_chamado desc";

        $stmt = $con->prepare($select);
        $stmt->execute();
        $dados = $stmt->fetchAll();

        return $dados;
    }
    function lista_historico($id_chamado)
    {
        $con = Conecta::getConexao();

        $select = "SELECT
                hu.id_historico AS id,
                hu.id_chamado,
                hu.id_usuario_adm AS id_usuario,
                hu.id_usuario_desginado,
                NULL AS ds_comentario,
                NULL AS st_status,
                CAST(hu.dt_update AS DATETIME) AS dt_evento,
                'usuario_chamado' AS origem,
                u_adm.ds_nome AS ds_nome_usuario,
                u_desig.ds_nome AS ds_nome_usuario_designado
            FROM tb_historico_usuario_chamado hu
            LEFT JOIN tb_usuario u_adm ON hu.id_usuario_adm = u_adm.id_usuario
            LEFT JOIN tb_usuario u_desig ON hu.id_usuario_desginado = u_desig.id_usuario
            WHERE hu.id_chamado = :id_chamado

            UNION ALL

            SELECT
                hs.id_historico AS id,
                hs.id_chamado,
                hs.id_usuario,
                NULL AS id_usuario_desginado,
                NULL AS ds_comentario,
                hs.st_status,
                CAST(hs.dt_update AS DATETIME) AS dt_evento,
                'status_chamado' AS origem,
                u.ds_nome AS ds_nome_usuario,
                NULL AS ds_nome_usuario_designado
            FROM tb_historico_status_chamado hs
            LEFT JOIN tb_usuario u ON hs.id_usuario = u.id_usuario
            WHERE hs.id_chamado = :id_chamado

            UNION ALL

            SELECT
                cc.id_comentario_chamado AS id,
                cc.id_chamado,
                cc.id_usuario,
                NULL AS id_usuario_desginado,
                cc.ds_comentario,
                NULL AS st_status,
                CAST(cc.dt_comentario AS DATETIME) AS dt_evento,
                'comentario_chamado' AS origem,
                u.ds_nome AS ds_nome_usuario,
                NULL AS ds_nome_usuario_designado
            FROM tb_comentario_chamado cc
            LEFT JOIN tb_usuario u ON cc.id_usuario = u.id_usuario
            WHERE cc.id_chamado = :id_chamado

            ORDER BY dt_evento;
            ";

        $stmt = $con->prepare($select);


        $stmt->execute([':id_chamado' => $id_chamado]);
        $dados = $stmt->fetchAll();

        return $dados;
    }

    function atribuirFuncoesUsuario(array $dados)
    {
        $this->cadastrarHistoricoUsuario($dados);
        $this->atribuirChamado($dados['id_chamado'], $dados['id_usuario_desginado']);
        $this->mudarStatusChamado(1, $dados['id_chamado'], $dados['id_usuario_adm']);
    }
    function cadastrarHistoricoUsuario(array $dados): void
    {
        $connection = Conecta::getConexao();

        $sql = "
                INSERT INTO tb_historico_usuario_chamado
                (
                    id_chamado,
                    id_usuario_adm,
                    id_usuario_desginado,
                    dt_update
                ) VALUES (
                    :id_chamado,
                    :id_usuario_adm,
                    :id_usuario_desginado,
                    :dt_update
                );
            ";


        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':id_chamado' => $dados['id_chamado'],
            ':id_usuario_adm' => $dados['id_usuario_adm'],
            ':id_usuario_desginado' => $dados['id_usuario_desginado'],
            ':dt_update' => date('Y-m-d H:i:s'),
        ]);
    }
    function atribuirChamado($id_chamado, $id_usuario): void
    {

        $sql = 'DELETE FROM rl_chamado_usuario WHERE id_chamado = :id_chamado;';
        $connection = Conecta::getConexao();

        $stmt = $connection->prepare($sql);
        $stmt->execute([':id_chamado' => $id_chamado]);
        if ($id_usuario != null) {

            $sql = "
                INSERT INTO rl_chamado_usuario
                (
                    id_chamado,
                    id_usuario,
                    dt_aceito
                ) VALUES (
                    :id_chamado,
                    :id_usuario,
                    :dt_aceito
                );
            ";

            $stmt = $connection->prepare($sql);
            $stmt->execute([
                ':id_chamado' => $id_chamado,
                ':id_usuario' => $id_usuario,
                ':dt_aceito' => date('Y-m-d H:i:s'),
            ]);
        }
    }
    function mudarStatusChamado($status, $id_chamado, $id_usuario): void
    {
        $connection = Conecta::getConexao();

        if ($status == 0) {
            $this->atribuirChamado($id_chamado, null);
        }

        $sql = "
                UPDATE tb_chamados SET st_status = :status WHERE (id_chamado = :id_chamado);

            ";

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':id_chamado' => $id_chamado,
            ':status' => $status
        ]);
        $this->cadastrarHistoricoStatus($status, $id_chamado, $id_usuario);
    }
    function verificarUltimoStatus($id_chamado): ?string
    {
        $connection = Conecta::getConexao();

        $sql = "
        SELECT st_status
        FROM tb_historico_status_chamado
        WHERE id_chamado = :id_chamado
        ORDER BY dt_update DESC
        LIMIT 1
    ";

        $stmt = $connection->prepare($sql);
        $stmt->execute([':id_chamado' => $id_chamado]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ? $resultado['st_status'] : null;
    }

    function cadastrarHistoricoStatus($status, $id_chamado, $id_usuario): void
    {
        $ultimoStatus = $this->verificarUltimoStatus($id_chamado);

        // Se o último status for igual ao novo, não cadastra
        if ($ultimoStatus == $status) {
            return;
        }

        $connection = Conecta::getConexao();

        $sql = "
        INSERT INTO tb_historico_status_chamado
        (
            id_chamado,
            st_status,
            id_usuario,
            dt_update
        ) VALUES (
            :id_chamado,
            :st_status,
            :id_usuario,
            :dt_update
        );
    ";

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':id_chamado' => $id_chamado,
            ':st_status' => $status,
            ':id_usuario' => $id_usuario,
            ':dt_update' => date('Y-m-d H:i:s')
        ]);
    }
    function gravarComentario($id_chamado, $ds_comentario, $id_usuario): void
    {
        $connection = Conecta::getConexao();

        $sql = "
                INSERT INTO tb_comentario_chamado
                (
                    id_chamado,
                    ds_comentario,
                    id_usuario,
                    dt_comentario
                ) VALUES (
                    :id_chamado,
                    :ds_comentario,
                    :id_usuario,
                    :dt_comentario
                );
            ";


        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':id_chamado' => $id_chamado,
            ':ds_comentario' => $ds_comentario,
            ':id_usuario' => $id_usuario,
            ':dt_comentario' => date('Y-m-d H:i:s')
        ]);
    }
    function verificarNumeroTelChamado($id_chamado): ?string
    {
        $connection = Conecta::getConexao();

        $sql = "
        SELECT u.nu_telefone
        FROM tb_usuario u
        inner join rl_chamado_usuario rl on rl.id_usuario = u.id_usuario
        WHERE rl.id_chamado = :id_chamado
        limit 1";

        $stmt = $connection->prepare($sql);
        $stmt->execute([':id_chamado' => $id_chamado]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ? $resultado['nu_telefone'] : null;
    }
}
