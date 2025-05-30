<?php

require_once("Conecta.php");

class Usuario
{

    function validaLogin($ds_usuario)
    {
        $con = Conecta::getConexao();

        $select = "SELECT
							u.id_usuario,
							-- rl.id_hospital,
							ds_usuario,
							ds_nome,
							ds_senha,
                            ds_email,
							u.id_perfil,
                            p.ds_perfil,
                            st_reset_senha
						FROM
						    tb_usuario u
						-- INNER join rl_usuario_hospital rl on u.id_usuario=rl.id_usuario
                        INNER JOIN tb_perfil p on u.id_perfil = p.id_perfil
						WHERE
						-- rl.id_hospital = :id_hospital
                         ds_usuario = :ds_usuario
                        and u.st_ativo = 'A'";

        $stmt = $con->prepare($select);
        $params = array(
            // ':id_hospital' => $id_hospital,
            ':ds_usuario' => $ds_usuario
        );

        $stmt->execute($params);
        $dados = $stmt->fetchAll();

        if (!$dados) {
            return [];
        }

        $dados = array_reduce($dados, function ($carry, $item) {

            $carry['id_usuario'] = $item['id_usuario'];
            $carry['ds_usuario'] = $item['ds_usuario'];
            $carry['ds_nome'] = $item['ds_nome'];
            $carry['ds_email'] = $item['ds_email'];
            $carry['ds_senha'] = $item['ds_senha'];
            $carry['id_perfil'] = $item['id_perfil'];
            $carry['ds_perfil'] = $item['ds_perfil'];
            $carry['st_reset_senha'] = $item['st_reset_senha'];
            // $carry['id_hospital'] = $item['id_hospital'];

            /*$carry['permissoes'][] = [
                'id_permissao,' => $item['id_permissao'],
                'ds_permissao,' => $item['ds_permissao'],
                'ds_role' => $item['ds_role'],
            ];*/

            return $carry;
        });

        return $dados;
    }

    public function buscaUsuario($id_usuario)
    {
        $con = Conecta::getConexao();

        $select = "SELECT
                            *
                        FROM
                            tb_usuario
						WHERE
						id_usuario = :id_usuario
                ";

        $stmt = $con->prepare($select);
        $params = array(':id_usuario' => $id_usuario);
        $stmt->execute($params);
        return  $stmt->fetch();
    }

    function buscaPorUsuarioEmail(string $usuario, string $email)
    {
        $con = Conecta::getConexao();

        $select = "
                SELECT
                         id_usuario,
                        ds_usuario,
                        ds_nome,
                        ds_email,
                        st_ativo,
                        id_perfil
                 FROM
                        tb_usuario
                    WHERE
                        ds_usuario = :ds_usuario
                        and ds_email = :ds_email
                        and st_ativo = 'A'
                ";

        $stmt = $con->prepare($select);
        $params = array(
            ':ds_usuario' => $usuario,
            ':ds_email' => $email
        );
        $stmt->execute($params);
        return  $stmt->fetch();
    }

    public function listarUsuario_adm($id_usuario = null)
    {
        $con = Conecta::getConexao();

        if ($id_usuario === null) {
            $select = "SELECT * FROM tb_usuario where id_perfil = 1";
            $stmt = $con->prepare($select);
            $stmt->execute();
        } else {
            $select = "SELECT * FROM tb_usuario WHERE id_usuario != :id_usuario and id_perfil = 1";
            $stmt = $con->prepare($select);
            $stmt->execute([':id_usuario' => $id_usuario]);
        }

        return $stmt->fetchAll();
    }

    public function listarUsuario($id_usuario = null)
    {
        $con = Conecta::getConexao();

        if ($id_usuario === null) {
            $select = "SELECT * FROM tb_usuario";
            $stmt = $con->prepare($select);
            $stmt->execute();
        } else {
            $select = "SELECT * FROM tb_usuario WHERE id_usuario != :id_usuario";
            $stmt = $con->prepare($select);
            $stmt->execute([':id_usuario' => $id_usuario]);
        }

        return $stmt->fetchAll();
    }



    public function gravarUsuario(array $dados)
    {
        $ds_nome        = $dados['ds_nome'];
        $ds_usuario     = $dados['ds_usuario'];
        $ds_senha       = $dados['ds_senha'];
        $id_perfil      = $dados['id_perfil'];
        $ds_email       = $dados['ds_email'];
        $st_ativo      = 'A';
        $nu_telefone = preg_replace('/\D/', '', $dados['nu_telefone']);
        $nu_cep = preg_replace('/\D/', '', $dados['nu_cep']);
        $ds_endereco = $dados['ds_endereco'];

        $con = Conecta::getConexao();
        $insert = "INSERT into tb_usuario (
                            ds_nome,
                            ds_usuario,
                            id_perfil,
                            ds_email,
                            st_ativo,
                            nu_telefone,
                            nu_cep,
                            ds_endereco,
                            st_reset_senha,
                            dt_insert,
                            ds_senha
                            )
							VALUES (
							:ds_nome,
							:ds_usuario,
							:id_perfil,
							:ds_email,
							:st_ativo,
                            :nu_telefone,
                            :nu_cep,
                            :ds_endereco,
                            1,
                            NOW(),
                            :ds_senha
                            )";

        $stmt = $con->prepare($insert);

        $params = array(
            ':ds_nome' => $ds_nome,
            ':ds_usuario' => $ds_usuario,
            ':id_perfil' => $id_perfil,
            ':ds_email' => $ds_email,
            ':st_ativo' => $st_ativo,
            ':nu_telefone' => $nu_telefone,
            ':nu_cep' => $nu_cep,
            ':ds_endereco' => $ds_endereco,
            ':ds_senha' =>  hash("SHA512", $ds_senha)

        );

        $stmt->execute($params);
    }


    public function alterar(array $dados)
    {
        $id_usuario     = intval($dados['edit_id_usuario']);
        $ds_nome        = $dados['edit_ds_nome'];
        $ds_usuario     = $dados['edit_ds_usuario'];
        $ds_email       = $dados['edit_ds_email'];
        $id_perfil      = $dados['edit_id_perfil'];
        $nu_telefone    = preg_replace('/\D/', '', $dados['edit_nu_telefone']);
        $nu_cep         = preg_replace('/\D/', '', $dados['edit_nu_cep']);
        $ds_endereco    = $dados['edit_ds_endereco'];
        $ds_senha       = $dados['edit_ds_senha'];

        $con = Conecta::getConexao();

        $update = "UPDATE tb_usuario SET
                    ds_nome = :ds_nome,
                    ds_usuario = :ds_usuario,
                    ds_email = :ds_email,
                    id_perfil = :id_perfil,
                    nu_telefone = :nu_telefone,
                    nu_cep = :nu_cep,
                    ds_endereco = :ds_endereco,
                    st_reset_senha = 1,
                    ds_senha = :ds_senha,
                    dt_update = NOW()
                WHERE id_usuario = :id_usuario";

        $stmt = $con->prepare($update);

        $params = [
            ':ds_nome' => $ds_nome,
            ':ds_usuario' => $ds_usuario,
            ':ds_email' => $ds_email,
            ':id_perfil' => $id_perfil,
            ':nu_telefone' => $nu_telefone,
            ':nu_cep' => $nu_cep,
            ':ds_endereco' => $ds_endereco,
            ':ds_senha' => hash('SHA512', $ds_senha),
            ':id_usuario' => $id_usuario
        ];

        $stmt->execute($params);
    }

    public function alterarDadosUsuario($id_usuario, array $dados)
    {
        $con = Conecta::getConexao();
        $update = "UPDATE
                          tb_usuario
                        SET ds_nome = :ds_nome,
                            ds_usuario = :ds_usuario,
                            ds_email = :ds_email
						WHERE
						    id_usuario = :id_usuario";

        $stmt = $con->prepare($update);

        $params = array(
            ':ds_nome' => $dados['ds_nome'],
            ':ds_usuario' => $dados['ds_usuario'],
            ':ds_email' => $dados['ds_email'],
            ':id_usuario' => $id_usuario
        );
        $stmt->execute($params);

        $dadosUsuario = Security::getUser();
        $dadosUsuario['ds_nome'] = $dados['ds_nome'];

        Security::refreshUser($dadosUsuario);
    }

    public function alterarSenhaUsuario($id_usuario, array $dados)
    {
        $con = Conecta::getConexao();
        $update = "UPDATE
                          tb_usuario
                        SET ds_senha = :ds_senha
						WHERE
						    id_usuario = :id_usuario";

        $stmt = $con->prepare($update);

        $params = array(
            ':ds_senha' => hash("SHA512", $dados['ds_nova_senha']),
            ':id_usuario' => $id_usuario
        );
        $stmt->execute($params);
    }

    public function verificarSenhaAtual(int $id_usuario, string $senha)
    {
        $usuario = $this->buscaUsuario($id_usuario);
        return $usuario['ds_senha'] === hash("SHA512", $senha);
    }

    public function recuperarSenha(array $data)
    {
        $usuario = $this->buscaPorUsuarioEmail($data['ds_usuario'], $data['ds_email']);

        if (!$usuario) {
            throw new Exception('Os dados informados estão incorretos.');
        }

        $sql = "
                INSERT INTO tb_redefinir_senha
                (
                    id_usuario,
                    ds_token,
                    dt_solicitacao
                ) VALUES (
                    :id_usuario,
                    :ds_token,
                    :dt_solicitacao
                );
            ";

        $token = generate_token();

        $connection = Conecta::getConexao();
        $stmt = $connection->prepare($sql);
        $stmt->execute([
            ':id_usuario' => $usuario['id_usuario'],
            ':ds_token' => $token,
            ':dt_solicitacao' => date('Y-m-d H:i:s'),
        ]);

        // UsuarioMailer::sendRecuperarSenha([
        //     'ds_nome' => $usuario['ds_nome'],
        //     'ds_usuario' => $usuario['ds_usuario'],
        //     'ds_email' => $usuario['ds_email'],
        //     'token' => $token
        // ]);
    }

    public function verificarToken(string $token)
    {
        $con = Conecta::getConexao();

        $select = "
                SELECT
                    *
                FROM
                    tb_redefinir_senha
                WHERE
                    ds_token = :ds_token
            ";

        $stmt = $con->prepare($select);
        $params = array(
            ':ds_token' => $token
        );
        $stmt->execute($params);

        $redefinirSenha = $stmt->fetch();

        if (!$redefinirSenha) {
            throw new Exception('Atenção! Código Inválido. Solicite um novo código de redefinição de senha!');
        }

        $dtSolicitacao = new DateTime($redefinirSenha['dt_solicitacao']);
        $dtAlteracao = empty($redefinirSenha['dt_alteracao']) ? null : new DateTime($redefinirSenha['dt_alteracao']);

        if ($dtSolicitacao->diff(new \DateTime())->format('%a') > 1 && !$redefinirSenha['dt_alteracao']) {
            throw new Exception('Atenção! Código Expirou. Solicite um novo código de redefinição de senha!');
        }

        if ($dtAlteracao instanceof \DateTime) {
            $diffTime = convert_diff_to_time($dtAlteracao->diff(new \DateTime()));
            throw new Exception(sprintf('Sua senha foi alterada há %s atrás! Caso não tenha sido você, por favor solicite uma nova redefinição de senha!', $diffTime));
        }

        return $redefinirSenha;
    }

    public function redefinirSenha(array $dados)
    {

        $redefinirSenha = $this->verificarToken($dados['token']);

        $this->alterarSenhaUsuario($redefinirSenha['id_usuario'], $dados);

        $con = Conecta::getConexao();

        $update = "
            UPDATE tb_redefinir_senha
            SET dt_alteracao = :dt_alteracao
            WHERE
                ds_token = :ds_token
        ";

        $stmt = $con->prepare($update);
        $params = array(
            ':ds_token' => $redefinirSenha['ds_token'],
            ':dt_alteracao' => date('Y-m-d H:i:s'),
        );
        $stmt->execute($params);
    }

    public function excluirUsuario($id_usuario)
    {
        $con = Conecta::getConexao();

        $deleteUsuarioHospital = "
            DELETE
            FROM
                rl_usuario_hospital
            WHERE
                id_usuario = :id_usuario
        ";

        $stmtUsuarioHospital = $con->prepare($deleteUsuarioHospital);
        $stmtUsuarioHospital->execute([':id_usuario' => $id_usuario]);


        $delete = "
            DELETE
            FROM
                tb_usuario
            WHERE
                id_usuario = :id_usuario
        ";

        $stmt = $con->prepare($delete);
        $stmt->execute([':id_usuario' => $id_usuario]);
    }

    public function liberarUsuario($id_usuario)
    {
        $con = Conecta::getConexao();
        $update = "UPDATE
                          tb_usuario
                        SET dt_liberado = :dt_liberado
						WHERE
						    id_usuario = :id_usuario";

        $stmt = $con->prepare($update);

        $params = array(
            ':dt_liberado' => date('Y-m-d'),
            ':id_usuario' => $id_usuario
        );
        $stmt->execute($params);
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

    public function resetarSenha(int $id_usuario): void
    {
        $connection = Conecta::getConexao();

        $query = "
            UPDATE tb_usuario
            SET ds_senha = :ds_senha,
            st_reset_senha = 1,
            dt_update = NOW()
            WHERE id_usuario = :id_usuario
        ";

        $stmt = $connection->prepare($query);
        $stmt->execute([':id_usuario' => $id_usuario, ':ds_senha' =>  hash("SHA512", 'elite123')]);
    }
    public function alterarSenha($id_usuario, $ds_senha): void
    {
        $connection = Conecta::getConexao();

        $query = "
            UPDATE tb_usuario
            SET ds_senha = :ds_senha,
            st_reset_senha = 0,
            dt_update = NOW()
            WHERE id_usuario = :id_usuario
        ";

        $stmt = $connection->prepare($query);
        $stmt->execute([':id_usuario' => $id_usuario, ':ds_senha' =>  hash("SHA512", $ds_senha)]);
    }
    function lista()
    {
        $con = Conecta::getConexao();

        $select = "SELECT id_usuario,ds_usuario,ds_nome,st_ativo,ds_perfil
                    from tb_usuario u
                    left join tb_perfil p on p.id_perfil = u.id_perfil";

        $stmt = $con->prepare($select);


        $stmt->execute();
        $dados = $stmt->fetchAll();

        return $dados;
    }
}
