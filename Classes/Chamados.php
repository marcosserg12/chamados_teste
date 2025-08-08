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
        if ($id_perfil == 1 || $id_perfil == 4) {
            $where = "rl.id_usuario = " . $id_usuario . " or c.st_status = 0";
        } else if ($id_perfil == 3) {
            $where = "r2.id_usuario = " . $id_usuario . " ";
        } else {
            $where = "c.id_usuario = " . $id_usuario;
        }

        $select = "SELECT DISTINCT c.*,u1.ds_nome,u2.ds_nome,rl.dt_aceito FROM tb_chamados c
        left join tb_usuario u1 on u1.id_usuario = c.id_usuario
        left join rl_chamado_usuario rl on rl.id_chamado = c.id_chamado
        left join tb_usuario u2 on u2.id_usuario = rl.id_usuario
        INNER JOIN rl_usuario_empresa_localizacao r2 ON r2.id_localizacao = c.id_localizacao
        where $where
        order by st_status = 1 desc, st_status = 0 desc,c.id_chamado desc limit 10";

        $stmt = $con->prepare($select);


        $stmt->execute();
        $dados = $stmt->fetchAll();

        return $dados;
    }

    function totalChamadosPorUsuario($id_perfil, $id_usuario)
    {
        $con = Conecta::getConexao();
        if ($id_perfil == 1|| $id_perfil == 4) {
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
                    dt_data_chamado,
                    id_empresa,
                    id_localizacao,
                    id_tipo_chamado,
                    id_motivo_principal,
                    id_motivo_associado,
                    ds_patrimonio,
                    st_grau
                ) VALUES (
                    :id_usuario,
                    :ds_titulo,
                    :ds_descricao,
                    :dt_data_chamado,
                    :id_empresa,
                    :id_localizacao,
                    :id_tipo_chamado,
                    :id_motivo_principal,
                    :id_motivo_associado,
                    :ds_patrimonio,
                    :st_grau
                );
            ";


        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $dados['id_usuario'],
            ':ds_titulo' => $dados['ds_titulo'],
            ':ds_descricao' => $dados['ds_descricao'],
            ':dt_data_chamado' => date('Y-m-d H:i:s'),
            ':id_empresa' => $dados['id_empresa'],
            ':id_localizacao' => $dados['id_localizacao'],
            ':id_tipo_chamado' => $dados['id_tipo_chamado'],
            ':id_motivo_principal' => $dados['id_motivo_principal'],
            ':id_motivo_associado' => $dados['id_motivo_associado'],
            ':ds_patrimonio' => $dados['ds_patrimonio'],
            ':st_grau' => $dados['st_grau']
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

        $select = "SELECT c.*,u1.ds_nome as criador,u2.ds_nome as designado,rl.dt_aceito,rl.id_usuario as id_usuario_designado,hc.dt_update,e.ds_empresa,l.ds_localizacao FROM tb_chamados c
        left join tb_usuario u1 on u1.id_usuario = c.id_usuario
        left join rl_chamado_usuario rl on rl.id_chamado = c.id_chamado
        left join tb_usuario u2 on u2.id_usuario = rl.id_usuario
        left join tb_localizacao l on l.id_localizacao = c.id_localizacao
        left join tb_empresa e on e.id_empresa = c.id_empresa
        LEFT JOIN (
        SELECT id_chamado, MAX(dt_update) as dt_update
            FROM tb_historico_status_chamado
            GROUP BY id_chamado
        ) hc ON c.id_chamado = hc.id_chamado

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
        if ($id_perfil == 1 || $id_perfil == 4) {
            $where = "rl.id_usuario = " . $id_usuario . " or c.id_usuario =" . $id_usuario;
        } else {
            $where = "c.id_usuario = " . $id_usuario;
        }

        $select = "SELECT DISTINCT c.*,u1.ds_nome,u2.ds_nome,rl.dt_aceito FROM tb_chamados c
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
    function todosChamados($id_perfil, $id_usuario)
    {
        $con = Conecta::getConexao();

        if ($id_perfil == 3) {
            $where = "Where r2.id_usuario = " . $id_usuario . " ";
        } else {
            $where = "";
        }

        $select = "SELECT DISTINCT c.*,u1.ds_nome as criado,u2.ds_nome as atribuido,rl.dt_aceito FROM tb_chamados c
        left join tb_usuario u1 on u1.id_usuario = c.id_usuario
        LEFT JOIN rl_chamado_usuario rl ON rl.id_chamado = c.id_chamado
        left join tb_usuario u2 on u2.id_usuario = rl.id_usuario
        INNER JOIN rl_usuario_empresa_localizacao r2 ON r2.id_localizacao = c.id_localizacao
        $where
        order by c.id_chamado desc,c.dt_data_chamado desc";

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

        UNION ALL

        SELECT
            he.id_historico AS id,
            he.id_chamado,
            he.id_usuario,
            NULL AS id_usuario_desginado,
            NULL AS ds_comentario,
            NULL AS st_status,
            CAST(he.dt_update AS DATETIME) AS dt_evento,
            'edicao_chamado' AS origem,
            u.ds_nome AS ds_nome_usuario,
            NULL AS ds_nome_usuario_designado
        FROM tb_historico_edicao he
        LEFT JOIN tb_usuario u ON he.id_usuario = u.id_usuario
        WHERE he.id_chamado = :id_chamado

        ORDER BY dt_evento desc;
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
    function lista_tipos_chamados()
    {
        $con = Conecta::getConexao();

        $select = "SELECT * FROM tb_tipo_chamado;";

        $stmt = $con->prepare($select);


        $stmt->execute();
        $dados = $stmt->fetchAll();

        return $dados;
    }
    function lista_motivo($id_tipo_chamado)
    {
        $con = Conecta::getConexao();

        $select = "SELECT * FROM tb_motivo_principal where id_tipo_chamado = :id_tipo_chamado
            ";

        $stmt = $con->prepare($select);


        $stmt->execute([':id_tipo_chamado' => $id_tipo_chamado]);
        $dados = $stmt->fetchAll();

        return $dados;
    }
    function lista_motivo_associado($id_motivo_principal)
    {
        $con = Conecta::getConexao();

        $select = "SELECT * FROM tb_motivo_associado where id_motivo_principal = :id_motivo_principal
            ";

        $stmt = $con->prepare($select);


        $stmt->execute([':id_motivo_principal' => $id_motivo_principal]);
        $dados = $stmt->fetchAll();

        return $dados;
    }
    function lista_motivo_associado_empresa($id_motivo_principal, $id_empresa)
    {
        $con = Conecta::getConexao();

        $select = "SELECT * FROM tb_motivo_associado where id_motivo_principal = :id_motivo_principal and id_empresa = :id_empresa
            ";

        $stmt = $con->prepare($select);


        $stmt->execute([':id_motivo_principal' => $id_motivo_principal, ':id_empresa' => $id_empresa]);
        $dados = $stmt->fetchAll();

        return $dados;
    }

    function lista_empresas()
    {
        $con = Conecta::getConexao();

        $select = "SELECT * FROM tb_empresa;";

        $stmt = $con->prepare($select);


        $stmt->execute();
        $dados = $stmt->fetchAll();

        return $dados;
    }
    function lista_empresas_usuario($id_usuario)
    {
        $con = Conecta::getConexao();

        $select = "SELECT distinct e.id_empresa,e.ds_empresa FROM tb_empresa e
        inner join rl_usuario_empresa_localizacao rl on rl.id_empresa = e.id_empresa
        where rl.id_usuario = :id_usuario";

        $stmt = $con->prepare($select);


        $stmt->execute([':id_usuario' => $id_usuario]);
        $dados = $stmt->fetchAll();

        return $dados;
    }
    function lista_localizacao($id_empresa)
    {
        $con = Conecta::getConexao();

        $select = "SELECT * FROM tb_localizacao l
        inner join rl_empresa_localizacao rl on l.id_localizacao = rl.id_localizacao
        where rl.id_empresa = :id_empresa
            ";

        $stmt = $con->prepare($select);


        $stmt->execute([':id_empresa' => $id_empresa]);
        $dados = $stmt->fetchAll();

        return $dados;
    }
    function lista_localizacao_usuario($id_empresa, $id_usuario)
    {
        $con = Conecta::getConexao();

        $select = "SELECT l.*,rl.id_empresa FROM tb_localizacao l
        inner join rl_usuario_empresa_localizacao rl on rl.id_localizacao = l.id_localizacao
        where rl.id_empresa = :id_empresa and id_usuario = :id_usuario
            ";

        $stmt = $con->prepare($select);


        $stmt->execute([':id_empresa' => $id_empresa, ':id_usuario' => $id_usuario]);
        $dados = $stmt->fetchAll();

        return $dados;
    }
    public function buscarArquivo($id_arquivo)
    {
        $con = Conecta::getConexao();
        $stmt = $con->prepare("SELECT * FROM rl_arquivo_chamado WHERE id_arquivo = :id");
        $stmt->execute([':id' => $id_arquivo]);
        return $stmt->fetch();
    }
    public function excluirArquivo($id_arquivo)
    {
        $con = Conecta::getConexao();
        $stmt = $con->prepare("DELETE FROM rl_arquivo_chamado WHERE id_arquivo = :id");
        return $stmt->execute([':id' => $id_arquivo]);
    }
    public function editarChamado(array $dados)
    {
        $connection = Conecta::getConexao();

        $sql = "
        UPDATE tb_chamados
        SET
            ds_titulo = :ds_titulo,
            ds_descricao = :ds_descricao,
            id_empresa = :id_empresa,
            id_localizacao = :id_localizacao,
            id_tipo_chamado = :id_tipo_chamado,
            id_motivo_principal = :id_motivo_principal,
            id_motivo_associado = :id_motivo_associado,
            ds_patrimonio = :ds_patrimonio
        WHERE id_chamado = :id_chamado
    ";

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':id_chamado' => $dados['id_chamado'],
            ':ds_titulo' => $dados['ds_titulo'],
            ':ds_descricao' => $dados['ds_descricao'],
            ':id_empresa' => $dados['id_empresa'],
            ':id_localizacao' => $dados['id_localizacao'],
            ':id_tipo_chamado' => $dados['id_tipo_chamado'],
            ':id_motivo_principal' => $dados['id_motivo_principal'],
            ':id_motivo_associado' => $dados['id_motivo_associado'],
            ':ds_patrimonio' => $dados['ds_patrimonio']
        ]);

        $this->salvarArquivosChamado($dados['id_chamado']);
        $this->cadastrarHistoricoEdicao($dados);

        return true;
    }
    function cadastrarHistoricoEdicao(array $dados): void
    {
        $connection = Conecta::getConexao();

        $sql = "
                INSERT INTO tb_historico_edicao(
                    id_chamado,
                    id_usuario,
                    dt_update
                ) VALUES (
                    :id_chamado,
                    :id_usuario,
                    :dt_update
                );
            ";


        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':id_chamado' => $dados['id_chamado'],
            ':id_usuario' => $dados['id_usuario'],
            ':dt_update' => date('Y-m-d H:i:s'),
        ]);
    }

    function numerosTecnicos()
    {
        $connection = Conecta::getConexao();

        $sql = "
        SELECT u.id_usuario,u.nu_telefone
        FROM tb_usuario u
        WHERE id_perfil = 4 or id_usuario = 23";

        $stmt = $connection->prepare($sql);
        $stmt->execute();

        $resultado = $stmt->fetchAll();

        return $resultado;
    }
    function buscarNumeroPeloUsr($id_usuario): ?string
    {
        $connection = Conecta::getConexao();

        $sql = "
        SELECT u.id_usuario,u.nu_telefone
        FROM tb_usuario u
        WHERE u.id_usuario = :id_usuario
        limit 1";

        $stmt = $connection->prepare($sql);
        $stmt->execute([':id_usuario' => $id_usuario]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ? $resultado['nu_telefone'] : null;
    }


    function numerosGestores($id_empresa, $id_localizacao)
    {
        $connection = Conecta::getConexao();

        $sql = "SELECT u.* from tb_usuario u
        left join rl_usuario_empresa_localizacao rl on rl.id_usuario = u.id_usuario
        where id_perfil = 3 and rl.id_localizacao = :id_localizacao and rl.id_empresa = :id_empresa";

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':id_localizacao' => $id_localizacao,
            ':id_empresa' => $id_empresa
        ]);

        $resultado = $stmt->fetchAll();

        return $resultado;
    }

    function infoLocEmpresa($id_empresa, $id_localizacao)
    {
        $connection = Conecta::getConexao();

        $sql = "SELECT ds_localizacao,ds_empresa from tb_localizacao l
        inner join rl_empresa_localizacao rl on rl.id_localizacao = l.id_localizacao
        inner join tb_empresa e on e.id_empresa = rl.id_empresa
        where rl.id_empresa = :id_empresa and rl.id_localizacao = :id_localizacao;";

        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':id_localizacao' => $id_localizacao,
            ':id_empresa' => $id_empresa
        ]);

        $resultado = $stmt->fetch();

        return $resultado;
    }
    function telefoneDono($id_chamado): ?string
    {
        $connection = Conecta::getConexao();

        $sql = "
        SELECT u.nu_telefone
        FROM tb_usuario u
        inner join tb_chamados c on c.id_usuario = u.id_usuario
        WHERE c.id_chamado = :id_chamado
        limit 1";

        $stmt = $connection->prepare($sql);
        $stmt->execute([':id_chamado' => $id_chamado]);

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado ? $resultado['nu_telefone'] : null;
    }
    function totalChamados()
    {
        $con = Conecta::getConexao();


        $select = "SELECT  COUNT(CASE WHEN c.st_status = 0 THEN 1 END) AS aberto,
            COUNT(CASE WHEN c.st_status = 1 THEN 1 END) AS andamento,
            COUNT(CASE WHEN c.st_status = 9 THEN 1 END) AS resolvidos
        FROM tb_chamados c
        left join tb_usuario u1 on u1.id_usuario = c.id_usuario
        left join rl_chamado_usuario rl on rl.id_chamado = c.id_chamado
        left join tb_usuario u2 on u2.id_usuario = rl.id_usuario
        order by c.id_chamado desc ";

        $stmt = $con->prepare($select);


        $stmt->execute();
        $dados = $stmt->fetch();

        return $dados;
    }
    function totalChamadosSemanas()
    {
        $con = Conecta::getConexao();

        $sql = "SELECT
            -- Semana Atual
            SUM(CASE WHEN WEEK(c.dt_data_chamado, 1) = WEEK(CURDATE(), 1) AND YEAR(c.dt_data_chamado) = YEAR(CURDATE()) AND c.st_status = 0 THEN 1 ELSE 0 END) AS aberto_atual,
            SUM(CASE WHEN WEEK(c.dt_data_chamado, 1) = WEEK(CURDATE(), 1) AND YEAR(c.dt_data_chamado) = YEAR(CURDATE()) AND c.st_status = 1 THEN 1 ELSE 0 END) AS andamento_atual,
            SUM(CASE WHEN WEEK(c.dt_data_chamado, 1) = WEEK(CURDATE(), 1) AND YEAR(c.dt_data_chamado) = YEAR(CURDATE()) AND c.st_status = 9 THEN 1 ELSE 0 END) AS resolvidos_atual,

            -- Semana Anterior
            SUM(CASE WHEN WEEK(c.dt_data_chamado, 1) = WEEK(CURDATE(), 1) - 1 AND YEAR(c.dt_data_chamado) = YEAR(CURDATE()) AND c.st_status = 0 THEN 1 ELSE 0 END) AS aberto_anterior,
            SUM(CASE WHEN WEEK(c.dt_data_chamado, 1) = WEEK(CURDATE(), 1) - 1 AND YEAR(c.dt_data_chamado) = YEAR(CURDATE()) AND c.st_status = 1 THEN 1 ELSE 0 END) AS andamento_anterior,
            SUM(CASE WHEN WEEK(c.dt_data_chamado, 1) = WEEK(CURDATE(), 1) - 1 AND YEAR(c.dt_data_chamado) = YEAR(CURDATE()) AND c.st_status = 9 THEN 1 ELSE 0 END) AS resolvidos_anterior
        FROM tb_chamados c
    ";

        $stmt = $con->prepare($sql);
        $stmt->execute();
        $dados = $stmt->fetch(PDO::FETCH_ASSOC);

        return $dados;
    }
    function chamadosTecnico()
    {
        $con = Conecta::getConexao();


        $select = "SELECT
                    u2.id_usuario,
                    u2.ds_nome,
                    u2.ds_email,
                    COUNT(CASE WHEN c.st_status = 1 THEN 1 END) AS andamento,
                    COUNT(CASE WHEN c.st_status = 9 THEN 1 END) AS resolvidos
                FROM tb_usuario u2
                LEFT JOIN rl_chamado_usuario rl ON u2.id_usuario = rl.id_usuario
                LEFT JOIN tb_chamados c ON c.id_chamado = rl.id_chamado
                WHERE u2.id_perfil = 4
                GROUP BY u2.ds_nome, u2.ds_email, u2.id_usuario
                ORDER BY u2.ds_nome ASC;";

        $stmt = $con->prepare($select);


        $stmt->execute();
        $dados = $stmt->fetchAll();

        return $dados;
    }
    function ultimosChamadosTecnico($id_usuario)
    {
        $con = Conecta::getConexao();


        $select = "SELECT
            c.id_chamado,
            c.ds_titulo,
            c.ds_descricao,
            c.st_status,
            rl.dt_aceito,
            (
                SELECT h.dt_update
                FROM tb_historico_status_chamado h
                WHERE h.id_chamado = c.id_chamado
                ORDER BY h.dt_update DESC
                LIMIT 1
            ) AS ultima_atualizacao
        FROM rl_chamado_usuario rl
        INNER JOIN tb_chamados c ON c.id_chamado = rl.id_chamado
        WHERE rl.id_usuario = ?
        ORDER BY rl.dt_aceito DESC
        LIMIT 5";

        $stmt = $con->prepare($select);


        $stmt->execute([$id_usuario]);
        $dados = $stmt->fetchAll();

        return $dados;
    }
}
