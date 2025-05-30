<?php
require_once("Conecta.php");
class Geral
{
	function formata_cpf_cnpj($cpf_cnpj)
	{
		/*
		        Pega qualquer CPF e CNPJ e formata

		        CPF: 000.000.000-00
		        CNPJ: 00.000.000/0000-00
		    */

		## Retirando tudo que não for número.
		$cpf_cnpj = preg_replace("/[^0-9]/", "", $cpf_cnpj);
		$tipo_dado = NULL;
		if (strlen($cpf_cnpj) == 11) {
			$tipo_dado = "cpf";
		}
		if (strlen($cpf_cnpj) == 14) {
			$tipo_dado = "cnpj";
		}
		switch ($tipo_dado) {
			default:
				$cpf_cnpj_formatado = "Não foi possível definir tipo de dado";
				break;

			case "cpf":
				$bloco_1 = substr($cpf_cnpj, 0, 3);
				$bloco_2 = substr($cpf_cnpj, 3, 3);
				$bloco_3 = substr($cpf_cnpj, 6, 3);
				$dig_verificador = substr($cpf_cnpj, -2);
				$cpf_cnpj_formatado = $bloco_1 . "." . $bloco_2 . "." . $bloco_3 . "-" . $dig_verificador;
				break;

			case "cnpj":
				$bloco_1 = substr($cpf_cnpj, 0, 2);
				$bloco_2 = substr($cpf_cnpj, 2, 3);
				$bloco_3 = substr($cpf_cnpj, 5, 3);
				$bloco_4 = substr($cpf_cnpj, 8, 4);
				$digito_verificador = substr($cpf_cnpj, -2);
				$cpf_cnpj_formatado = $bloco_1 . "." . $bloco_2 . "." . $bloco_3 . "/" . $bloco_4 . "-" . $digito_verificador;
				break;
		}
		return $cpf_cnpj_formatado;
	}

	public function mandaEmail($email, $mensagem)
	{
		$quebra_linha = "\n";

		$emailsender = 'suporte@ahoraeagora.org';

		$headers = "MIME-Version: 1.1" . $quebra_linha;
		$headers .= "Content-type: text/html; charset=utf-8" . $quebra_linha;
		// Perceba que a linha acima contém "text/html", sem essa linha, a mensagem não chegará formatada.
		$headers .= "From: " . $emailsender . $quebra_linha;
		$headers .= "Return-Path: " . $emailsender . $quebra_linha;

		$headers .= "Reply-To: " . $emailsender . $quebra_linha;
		// Note que o e-mail do remetente será usado no campo Reply-To (Responder Para)

		/* Enviando a mensagem */
		$emaildestinatario = $email;
		$assunto = "Cadastro Ensaio Clínico";
		$mensagemHTML = $mensagem;
		mail($emaildestinatario, $assunto, $mensagemHTML, $headers, "-r" . $emailsender);
	}

	public function formataData($data)
	{
		if ($data == "") {
			return "";
		}
		return date('d/m/Y', strtotime($data));
	}
	public function formataHora($data)
	{
		if ($data == "") {
			return "";
		}
		return date('H:i', strtotime($data));
	}

	public function formataDataHora($data)
	{
		return date('d/m/Y H:i:s', strtotime($data));
	}

	public function formataDataSQL($data)
	{
		return date('Y-m-d', strtotime(str_replace('/', '-', $data)));
	}
	public function formataDataHoraSQL($dataHora)
	{
		return date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $dataHora)));
	}


	public function listarOptionsPais($cd_pais = "")
	{
		try {

			$con = Conecta::getConexao();
			$select = "SELECT cd_pais, nm_pais
							FROM tb_pais";
			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "";

			while ($dados = $stmt->fetch()) {
				if ($dados['cd_pais'] == $cd_pais) {
					$options .= "<option value='" . $dados['cd_pais'] . "' selected>" . $dados['nm_pais'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['cd_pais'] . "'>" . $dados['nm_pais'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsNacionalidade($co_nacionalidade = "")
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT co_nacionalidade_sus, nacio_pais
							FROM tb_pais
							WHERE nacio_pais <> '-'
							ORDER BY nacio_pais";
			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "";

			while ($dados = $stmt->fetch()) {
				if ($dados['co_nacionalidade_sus'] == $co_nacionalidade) {
					$options .= "<option value='" . $dados['co_nacionalidade_sus'] . "' selected>" . $dados['nacio_pais'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['co_nacionalidade_sus'] . "'>" . $dados['nacio_pais'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}


	public function listarOptionsUF($cd_uf = "")
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT cd_uf, nm_uf
							FROM tb_uf";
			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "";

			while ($dados = $stmt->fetch()) {
				if ($dados['cd_uf'] == $cd_uf) {
					$options .= "<option value='" . $dados['cd_uf'] . "' selected>" . $dados['nm_uf'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['cd_uf'] . "'>" . $dados['nm_uf'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsCidade($cd_uf, $cd_cid = "")
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT cd_cid, nm_cid
							FROM tb_cidade
							WHERE cd_uf = :cd_uf";
			$stmt = $con->prepare($select);
			$params = array(':cd_uf' => $cd_uf);
			$stmt->execute($params);

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['cd_cid'] == $cd_cid) {
					$options .= "<option value='" . $dados['cd_cid'] . "' selected>" . $dados['nm_cid'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['cd_cid'] . "'>" . $dados['nm_cid'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsPerfil($id_perfil = null)
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT id_perfil, ds_perfil
							FROM tb_perfil";
			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_perfil'] == $id_perfil) {
					$options .= "<option value='" . $dados['id_perfil'] . "' selected>" . $dados['ds_perfil'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_perfil'] . "'>" . $dados['ds_perfil'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsGenero($co_genero = "")
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT co_genero, ds_genero
							FROM tb_genero";
			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "";

			while ($dados = $stmt->fetch()) {
				if ($dados['co_genero'] == $co_genero) {
					$options .= "<option value='" . $dados['co_genero'] . "' selected>" . $dados['ds_genero'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['co_genero'] . "'>" . $dados['ds_genero'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsOrientacao($co_orientacao_sexual = "")
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT co_orientacao_sexual, ds_orientacao_sexual
							FROM tb_orientacao_sexual";
			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "";

			while ($dados = $stmt->fetch()) {
				if ($dados['co_orientacao_sexual'] == $co_orientacao_sexual) {
					$options .= "<option value='" . $dados['co_orientacao_sexual'] . "' selected>" . $dados['ds_orientacao_sexual'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['co_orientacao_sexual'] . "'>" . $dados['ds_orientacao_sexual'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsRaca($raca = "")
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT raca, ds_raca
							FROM tb_raca";
			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "";

			while ($dados = $stmt->fetch()) {
				if ($dados['raca'] == $raca) {
					$options .= "<option value='" . $dados['raca'] . "' selected>" . $dados['ds_raca'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['raca'] . "'>" . $dados['ds_raca'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function buscarUF($cd_cid)
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT cd_uf
							FROM tb_cidade
							WHERE cd_cid = :cd_cid";
			$stmt = $con->prepare($select);
			$params = array(':cd_cid' => $cd_cid);

			$stmt->execute($params);
			$dados = $stmt->fetch();
			return $dados['cd_uf'];
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarCheckProblema($co_seq, $desc)
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT
								tp.co_tipo_problema,
								tp.ds_tipo_problema,
							    case when rr.co_seq_resposta_questionario_recrutado is not null then 1 else 0 end as resposta
							FROM
								tb_tipo_problema tp
							LEFT JOIN rl_resposta_recrutado_problema rr ON tp.co_tipo_problema=rr.co_tipo_problema
							AND rr.co_seq_resposta_questionario_recrutado = :co_seq
							ORDER BY co_tipo_problema";

			$stmt = $con->prepare($select);
			$params = array(':co_seq' => $co_seq);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {
				$ds_tipo_problema = str_replace("[Testes/Vouchers]", $desc, $dados['ds_tipo_problema']);

				if ($dados['resposta'] == 1) {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='problema[]' id='problema_" . $dados['co_tipo_problema'] . "' value='" . $dados['co_tipo_problema'] . "' checked> " . $ds_tipo_problema . ".
										<span></span>
									</label>";
				} else {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='problema[]' id='problema_" . $dados['co_tipo_problema'] . "' value='" . $dados['co_tipo_problema'] . "'> " . $ds_tipo_problema . ".
										<span></span>
									</label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarCheckExposicao($co_seq)
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT
								tp.co_tipo_exposicao_risco,
								tp.ds_tipo_exposicao_risco,
							    case when rr.co_seq_resposta_questionario_recrutado is not null then 1 else 0 end as resposta
							FROM
								tb_tipo_exposicao_risco tp
							LEFT JOIN rl_resposta_recrutado_exposicao_risco rr ON tp.co_tipo_exposicao_risco=rr.co_tipo_exposicao_risco
							AND rr.co_seq_resposta_questionario_recrutado = :co_seq
							ORDER BY co_tipo_exposicao_risco";

			$stmt = $con->prepare($select);
			$params = array(':co_seq' => $co_seq);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {

				if ($dados['resposta'] == 1) {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='exposicao_risco[]' id='exposicao_risco_" . $dados['co_tipo_exposicao_risco'] . "' value='" . $dados['co_tipo_exposicao_risco'] . "' checked> " . $dados['ds_tipo_exposicao_risco'] . ".
										<span></span>
									</label>";
				} else {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='exposicao_risco[]' id='exposicao_risco_" . $dados['co_tipo_exposicao_risco'] . "' value='" . $dados['co_tipo_exposicao_risco'] . "'> " . $dados['ds_tipo_exposicao_risco'] . ".
										<span></span>
									</label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarCheckExposicaoAtendimento($co_seq)
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT
								tp.co_tipo_exposicao_risco,
								tp.ds_tipo_exposicao_risco,
							    case when rr.co_seq_atendimento is not null then 1 else 0 end as resposta
							FROM
								tb_tipo_exposicao_risco tp
							LEFT JOIN rl_primeiro_atendimento_exposicao_risco rr ON tp.co_tipo_exposicao_risco=rr.co_tipo_exposicao_risco
							AND rr.co_seq_atendimento = :co_seq
							ORDER BY co_tipo_exposicao_risco";

			$stmt = $con->prepare($select);
			$params = array(':co_seq' => $co_seq);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {

				if ($dados['resposta'] == 1) {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='exposicao_risco[]' id='exposicao_risco_" . $dados['co_tipo_exposicao_risco'] . "' value='" . $dados['co_tipo_exposicao_risco'] . "' checked> " . $dados['ds_tipo_exposicao_risco'] . ".
										<span></span>
									</label>";
				} else {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='exposicao_risco[]' id='exposicao_risco_" . $dados['co_tipo_exposicao_risco'] . "' value='" . $dados['co_tipo_exposicao_risco'] . "'> " . $dados['ds_tipo_exposicao_risco'] . ".
										<span></span>
									</label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarCheckRelacao($co_seq)
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT
								tp.co_tipo_relacao_sexual,
								tp.ds_tipo_relacao_sexual,
							    case when rr.co_seq_resposta_questionario_recrutado is not null then 1 else 0 end as resposta
							FROM
								tb_tipo_relacao_sexual tp
							LEFT JOIN rl_resposta_recrutado_relacao_sexual rr ON tp.co_tipo_relacao_sexual=rr.co_tipo_relacao_sexual
							AND rr.co_seq_resposta_questionario_recrutado = :co_seq
							ORDER BY co_tipo_relacao_sexual";

			$stmt = $con->prepare($select);
			$params = array(':co_seq' => $co_seq);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {

				if ($dados['resposta'] == 1) {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='relacao_sexual[]' id='relacao_sexual_" . $dados['co_tipo_relacao_sexual'] . "' value='" . $dados['co_tipo_relacao_sexual'] . "' checked> " . $dados['ds_tipo_relacao_sexual'] . ".
										<span></span>
									</label>";
				} else {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='relacao_sexual[]' id='relacao_sexual_" . $dados['co_tipo_relacao_sexual'] . "' value='" . $dados['co_tipo_relacao_sexual'] . "'> " . $dados['ds_tipo_relacao_sexual'] . ".
										<span></span>
									</label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarCheckRelacaoAtendimento($co_seq)
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT
								tp.co_tipo_relacao_sexual,
								tp.ds_tipo_relacao_sexual,
							    case when rr.co_seq_atendimento is not null then 1 else 0 end as resposta
							FROM
								tb_tipo_relacao_sexual tp
							LEFT JOIN rl_primeiro_atendimento_relacao_sexual rr ON tp.co_tipo_relacao_sexual=rr.co_tipo_relacao_sexual
							AND rr.co_seq_atendimento = :co_seq
							ORDER BY co_tipo_relacao_sexual";

			$stmt = $con->prepare($select);
			$params = array(':co_seq' => $co_seq);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {

				if ($dados['resposta'] == 1) {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='relacao_sexual[]' id='relacao_sexual_" . $dados['co_tipo_relacao_sexual'] . "' value='" . $dados['co_tipo_relacao_sexual'] . "' checked> " . $dados['ds_tipo_relacao_sexual'] . ".
										<span></span>
									</label>";
				} else {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='relacao_sexual[]' id='relacao_sexual_" . $dados['co_tipo_relacao_sexual'] . "' value='" . $dados['co_tipo_relacao_sexual'] . "'> " . $dados['ds_tipo_relacao_sexual'] . ".
										<span></span>
									</label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}


	public function listarCheckIST($co_seq)
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT
								tp.co_ist,
								tp.ds_ist,
							    case when rr.co_seq_resposta_questionario_recrutado is not null then 1 else 0 end as resposta
							FROM
								tb_ist tp
							LEFT JOIN rl_resposta_recrutado_ist rr ON tp.co_ist=rr.co_ist
							AND rr.co_seq_resposta_questionario_recrutado = :co_seq
							ORDER BY co_ist";

			$stmt = $con->prepare($select);
			$params = array(':co_seq' => $co_seq);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {

				if ($dados['resposta'] == 1) {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='ist[]' id='ist_" . $dados['co_ist'] . "' value='" . $dados['co_ist'] . "' checked> " . $dados['ds_ist'] . ".
										<span></span>
									</label>";
				} else {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='ist[]' id='ist_" . $dados['co_ist'] . "' value='" . $dados['co_ist'] . "'> " . $dados['ds_ist'] . ".
										<span></span>
									</label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarCheckISTAtendimento($co_seq)
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT
								tp.co_ist,
								tp.ds_ist,
							    case when rr.co_seq_atendimento is not null then 1 else 0 end as resposta
							FROM
								tb_ist tp
							LEFT JOIN rl_primeiro_atendimento_ist rr ON tp.co_ist=rr.co_ist
							AND rr.co_seq_atendimento = :co_seq
							ORDER BY co_ist";

			$stmt = $con->prepare($select);
			$params = array(':co_seq' => $co_seq);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {

				if ($dados['resposta'] == 1) {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='ist[]' id='ist_" . $dados['co_ist'] . "' value='" . $dados['co_ist'] . "' checked> " . $dados['ds_ist'] . ".
										<span></span>
									</label>";
				} else {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='ist[]' id='ist_" . $dados['co_ist'] . "' value='" . $dados['co_ist'] . "'> " . $dados['ds_ist'] . ".
										<span></span>
									</label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarCheckSubstancia($co_seq)
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT
								tp.co_tipo_substancia,
								tp.ds_tipo_substancia,
							    case when rr.co_seq_resposta_questionario_recrutado is not null then 1 else 0 end as resposta
							FROM
								tb_tipo_substancia tp
							LEFT JOIN rl_resposta_recrutado_substancia rr ON tp.co_tipo_substancia=rr.co_tipo_substancia
							AND rr.co_seq_resposta_questionario_recrutado = :co_seq
							ORDER BY co_tipo_substancia";

			$stmt = $con->prepare($select);
			$params = array(':co_seq' => $co_seq);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {

				if ($dados['resposta'] == 1) {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='substancia[]' id='substancia_" . $dados['co_tipo_substancia'] . "' value='" . $dados['co_tipo_substancia'] . "' checked> " . $dados['ds_tipo_substancia'] . ".
										<span></span>
									</label>";
				} else {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='substancia[]' id='substancia_" . $dados['co_tipo_substancia'] . "' value='" . $dados['co_tipo_substancia'] . "'> " . $dados['ds_tipo_substancia'] . ".
										<span></span>
									</label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarCheckSubstanciaAtendimento($co_seq)
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT
								tp.co_tipo_substancia,
								tp.ds_tipo_substancia,
							    case when rr.co_seq_atendimento is not null then 1 else 0 end as resposta
							FROM
								tb_tipo_substancia tp
							LEFT JOIN rl_primeiro_atendimento_substancia rr ON tp.co_tipo_substancia=rr.co_tipo_substancia
							AND rr.co_seq_atendimento = :co_seq
							ORDER BY co_tipo_substancia";

			$stmt = $con->prepare($select);
			$params = array(':co_seq' => $co_seq);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {

				if ($dados['resposta'] == 1) {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='substancia[]' id='substancia_" . $dados['co_tipo_substancia'] . "' value='" . $dados['co_tipo_substancia'] . "' checked> " . $dados['ds_tipo_substancia'] . ".
										<span></span>
									</label>";
				} else {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='substancia[]' id='substancia_" . $dados['co_tipo_substancia'] . "' value='" . $dados['co_tipo_substancia'] . "'> " . $dados['ds_tipo_substancia'] . ".
										<span></span>
									</label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}


	public function listarCheckProblemaDist($co_seq)
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT
								tp.co_tipo_problema_dist,
								tp.ds_tipo_problema_dist,
							    case when rr.co_seq_resposta_questionario_participante is not null then 1 else 0 end as resposta
							FROM
								tb_tipo_problema_dist tp
							LEFT JOIN rl_resposta_participante_problema rr ON tp.co_tipo_problema_dist=rr.co_tipo_problema_dist
							AND rr.co_seq_resposta_questionario_participante = :co_seq
							ORDER BY co_tipo_problema_dist";

			$stmt = $con->prepare($select);
			$params = array(':co_seq' => $co_seq);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {

				if ($dados['resposta'] == 1) {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='problema[]' id='problema_" . $dados['co_tipo_problema_dist'] . "' value='" . $dados['co_tipo_problema_dist'] . "' checked> " . $dados['ds_tipo_problema_dist'] . ".
										<span></span>
									</label>";
				} else {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='problema[]' id='problema_" . $dados['co_tipo_problema_dist'] . "' value='" . $dados['co_tipo_problema_dist'] . "'> " . $dados['ds_tipo_problema_dist'] . ".
										<span></span>
									</label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}


	public function listarCheckMotivo($co_seq)
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT
								tp.co_tipo_motivo,
								tp.ds_tipo_motivo,
							    case when rr.co_seq_resposta_questionario_participante is not null then 1 else 0 end as resposta
							FROM
								tb_tipo_motivo tp
							LEFT JOIN rl_resposta_participante_motivo rr ON tp.co_tipo_motivo=rr.co_tipo_motivo
							AND rr.co_seq_resposta_questionario_participante = :co_seq
							ORDER BY co_tipo_motivo";

			$stmt = $con->prepare($select);
			$params = array(':co_seq' => $co_seq);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {

				if ($dados['resposta'] == 1) {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='motivo[]' id='motivo_" . $dados['co_tipo_motivo'] . "' value='" . $dados['co_tipo_motivo'] . "' checked> " . $dados['ds_tipo_motivo'] . ".
										<span></span>
									</label>";
				} else {
					$check .= "<label class='m-checkbox'>
										<input type='checkbox' name='motivo[]' id='motivo_" . $dados['co_tipo_motivo'] . "' value='" . $dados['co_tipo_motivo'] . "'> " . $dados['ds_tipo_motivo'] . ".
										<span></span>
									</label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function substituirString($ds_grupo, $string)
	{
		if ($ds_grupo == "Vouchers") {
			$desc 	= "Cupom";
			$retorno = str_replace("[Tratamento/Controle]", $desc, $string);
		} else if ($ds_grupo == "Testes") {
			$desc 	= "Autotestes de HIV";
			$retorno = str_replace("[Tratamento/Controle]", $desc, $string);
		}

		return $retorno;
	}



	public function listarOptionsTurno($id_turno = null)
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT id_turno, ds_turno
							FROM tb_turno";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_turno'] == $id_turno) {
					$options .= "<option value='" . $dados['id_turno'] . "' selected>" . $dados['ds_turno'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_turno'] . "'>" . $dados['ds_turno'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsCodigoMotor($id_codigo_motor = null)
	{
		try {
			$con = Conecta::getConexao();


			$select = "SELECT id_codigo_motor, ds_codigo_motor
							FROM tb_codigo_motor";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_codigo_motor'] == $id_codigo_motor) {
					$options .= "<option value='" . $dados['id_codigo_motor'] . "' selected>" . $dados['ds_codigo_motor'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_codigo_motor'] . "'>" . $dados['ds_codigo_motor'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsCodigoRespiratorio($id_codigo_respiratorio = null)
	{
		try {
			$con = Conecta::getConexao();


			$select = "SELECT id_codigo_respiratorio, ds_codigo_respiratorio
							FROM tb_codigo_respiratorio";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_codigo_respiratorio'] == $id_codigo_respiratorio) {
					$options .= "<option value='" . $dados['id_codigo_respiratorio'] . "' selected>" . $dados['ds_codigo_respiratorio'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_codigo_respiratorio'] . "'>" . $dados['ds_codigo_respiratorio'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsMobilidade($id_mobilidade = null)
	{
		try {
			$con = Conecta::getConexao();


			$select = "SELECT id_mobilidade, ds_mobilidade
							FROM tb_mobilidade";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_mobilidade'] == $id_mobilidade) {
					$options .= "<option value='" . $dados['id_mobilidade'] . "' selected>" . $dados['ds_mobilidade'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_mobilidade'] . "'>" . $dados['ds_mobilidade'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsFuncionalidade($id_funcionalidade = null)
	{
		try {
			$con = Conecta::getConexao();


			$select = "SELECT id_funcionalidade, ds_funcionalidade
							FROM tb_funcionalidade";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_funcionalidade'] == $id_funcionalidade) {
					$options .= "<option value='" . $dados['id_funcionalidade'] . "' selected>" . $dados['ds_funcionalidade'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_funcionalidade'] . "'>" . $dados['ds_funcionalidade'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsTonus($id_tonus = null)
	{
		try {
			$con = Conecta::getConexao();


			$select = "SELECT id_tonus, ds_tonus
							FROM tb_tonus";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_tonus'] == $id_tonus) {
					$options .= "<option value='" . $dados['id_tonus'] . "' selected>" . $dados['ds_tonus'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_tonus'] . "'>" . $dados['ds_tonus'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsTrofismo($id_trofismo = null)
	{
		try {
			$con = Conecta::getConexao();


			$select = "SELECT id_trofismo, ds_trofismo
							FROM tb_trofismo";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_trofismo'] == $id_trofismo) {
					$options .= "<option value='" . $dados['id_trofismo'] . "' selected>" . $dados['ds_trofismo'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_trofismo'] . "'>" . $dados['ds_trofismo'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsEdemas($id_edema = null)
	{
		try {
			$con = Conecta::getConexao();


			$select = "SELECT id_edema, ds_edema
							FROM tb_edema";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_edema'] == $id_edema) {
					$options .= "<option value='" . $dados['id_edema'] . "' selected>" . $dados['ds_edema'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_edema'] . "'>" . $dados['ds_edema'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarCheckDeformidades($id_avaliacao_funcional = null)
	{
		try {
			$con = Conecta::getConexao();


			$select = "SELECT de.id_deformidade, de.ds_deformidade,
							CASE WHEN rl.id_avaliacao_funcional is not null then 1 else 0 end as resposta
							FROM tb_deformidades de
							LEFT JOIN rl_avaliacao_funcional_deformidades rl ON de.id_deformidade = rl.id_deformidade
							AND rl.id_avaliacao_funcional = :id_avaliacao_funcional
							ORDER BY id_deformidade";

			$stmt = $con->prepare($select);
			$params = array(':id_avaliacao_funcional' => $id_avaliacao_funcional);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {
				if ($dados['resposta'] == 1) {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_deformidade[]' id='id_deformidade_" . $dados['id_deformidade'] . "' value='" . $dados['id_deformidade'] . "' checked> " . $dados['ds_deformidade'] . "
                                    <span></span>
                                </label>";
				} else {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_deformidade[]' id='id_deformidade_" . $dados['id_deformidade'] . "' value='" . $dados['id_deformidade'] . "'> " . $dados['ds_deformidade'] . "
                                    <span></span>
                                </label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarCheckNivelConsciencia($id_avaliacao_funcional = null)
	{
		try {
			$con = Conecta::getConexao();


			$select = "SELECT nc.id_nivel_consciencia, nc.ds_nivel_consciencia,
							CASE WHEN rl.id_avaliacao_funcional is not null then 1 else 0 end as resposta
							FROM tb_nivel_consciencia nc
							LEFT JOIN rl_avaliacao_funcional_nivel_consciencia rl ON nc.id_nivel_consciencia = rl.id_nivel_consciencia
							AND rl.id_avaliacao_funcional = :id_avaliacao_funcional
							ORDER BY id_nivel_consciencia";

			$stmt = $con->prepare($select);
			$params = array(':id_avaliacao_funcional' => $id_avaliacao_funcional);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {
				if ($dados['resposta'] == 1) {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_nivel_consciencia[]' id='id_nivel_consciencia_" . $dados['id_nivel_consciencia'] . "' value='" . $dados['id_nivel_consciencia'] . "' checked> " . $dados['ds_nivel_consciencia'] . "
                                    <span></span>
                                </label>";
				} else {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_nivel_consciencia[]' id='id_nivel_consciencia_" . $dados['id_nivel_consciencia'] . "' value='" . $dados['id_nivel_consciencia'] . "'> " . $dados['ds_nivel_consciencia'] . "
                                    <span></span>
                                </label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarUnidade($id_hospital)
	{
		$con = Conecta::getConexao();

		$select = "SELECT u.id_unidade, u.ds_unidade
                            FROM tb_unidade u
                            INNER JOIN tb_hospital h on u.id_hospital=h.id_hospital and u.id_hospital = :id_hospital
                            WHERE st_ativo = 'A'";

		$stmt = $con->prepare($select);
		$params = array(':id_hospital' => $id_hospital);
		$stmt->execute($params);
		$dados = $stmt->fetchAll();
		return $dados;
	}



	public function listarOptionsUnidade($id_hospital, $id_unidade = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT u.id_unidade, u.ds_unidade
                            FROM tb_unidade u
                            INNER JOIN tb_hospital h on u.id_hospital=h.id_hospital and u.id_hospital = :id_hospital
                            WHERE st_ativo = 'A'";

			$stmt = $con->prepare($select);
			$params = array(':id_hospital' => $id_hospital);
			$stmt->execute($params);

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_unidade'] == $id_unidade) {
					$options .= "<option value='" . $dados['id_unidade'] . "' selected>" . $dados['ds_unidade'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_unidade'] . "'>" . $dados['ds_unidade'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsUnidadeLegadoC($id_hospital, $id_unidade = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT u.id_unidade_legado, u.ds_unidade
                            FROM tb_unidade u
                            INNER JOIN tb_hospital h on u.id_hospital=h.id_hospital and u.id_hospital = :id_hospital
                            WHERE st_ativo = 'A'";

			$stmt = $con->prepare($select);
			$params = array(':id_hospital' => $id_hospital);
			$stmt->execute($params);

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_unidade_legado'] == $id_unidade) {
					$options .= "<option value='" . $dados['id_unidade_legado'] . "' selected>" . $dados['ds_unidade'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_unidade_legado'] . "'>" . $dados['ds_unidade'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsUnidadeLegado($id_hospital, $id_setor = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT s.id_setor, s.setor
                            FROM tb_setores_estado_nutricional s
                            INNER JOIN tb_hospital h on s.hospitais_id=h.hospitais_id and h.id_hospital = :id_hospital
                            WHERE status = 'A'";

			$stmt = $con->prepare($select);
			$params = array(':id_hospital' => $id_hospital);
			$stmt->execute($params);

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_setor'] == $id_setor) {
					$options .= "<option value='" . $dados['id_setor'] . "' selected>" . $dados['setor'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_setor'] . "'>" . $dados['setor'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}


	public function listarOptionsNivelAtividade($id_nivel_atividade = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT id_nivel_atividade, ds_nivel_atividade
							FROM tb_nivel_atividade ";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_nivel_atividade'] == $id_nivel_atividade) {
					$options .= "<option value='" . $dados['id_nivel_atividade'] . "' selected>" . $dados['ds_nivel_atividade'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_nivel_atividade'] . "'>" . $dados['ds_nivel_atividade'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsMurmurioVesicular($id_murmurio_vesicular = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT id_murmurio_vesicular, ds_murmurio_vesicular
							FROM tb_murmurio_vesicular ";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_murmurio_vesicular'] == $id_murmurio_vesicular) {
					$options .= "<option value='" . $dados['id_murmurio_vesicular'] . "' selected>" . $dados['ds_murmurio_vesicular'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_murmurio_vesicular'] . "'>" . $dados['ds_murmurio_vesicular'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsRuidoAdventicio($id_ruido_adventicio = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT id_ruido_adventicio, ds_ruido_adventicio
							FROM tb_ruido_adventicio ";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_ruido_adventicio'] == $id_ruido_adventicio) {
					$options .= "<option value='" . $dados['id_ruido_adventicio'] . "' selected>" . $dados['ds_ruido_adventicio'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_ruido_adventicio'] . "'>" . $dados['ds_ruido_adventicio'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsRitmoVentilatorio($id_ritmo_ventilatorio = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT id_ritmo_ventilatorio, ds_ritmo_ventilatorio
							FROM tb_ritmo_ventilatorio ";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_ritmo_ventilatorio'] == $id_ritmo_ventilatorio) {
					$options .= "<option value='" . $dados['id_ritmo_ventilatorio'] . "' selected>" . $dados['ds_ritmo_ventilatorio'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_ritmo_ventilatorio'] . "'>" . $dados['ds_ritmo_ventilatorio'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsDescVentilatorio($id_desc_ventilatorio = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT id_desc_ventilatorio, ds_desc_ventilatorio
							FROM tb_desc_ventilatorio ";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_desc_ventilatorio'] == $id_desc_ventilatorio) {
					$options .= "<option value='" . $dados['id_desc_ventilatorio'] . "' selected>" . $dados['ds_desc_ventilatorio'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_desc_ventilatorio'] . "'>" . $dados['ds_desc_ventilatorio'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}


	public function listarOptionsDispneia($id_dispneia = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT id_dispneia, ds_dispneia
							FROM tb_dispneia ";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_dispneia'] == $id_dispneia) {
					$options .= "<option value='" . $dados['id_dispneia'] . "' selected>" . $dados['ds_dispneia'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_dispneia'] . "'>" . $dados['ds_dispneia'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}


	public function listarOptionsTosse($id_tosse = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT id_tosse, ds_tosse
							FROM tb_tosse ";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_tosse'] == $id_tosse) {
					$options .= "<option value='" . $dados['id_tosse'] . "' selected>" . $dados['ds_tosse'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_tosse'] . "'>" . $dados['ds_tosse'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsTipoDreno($id_tipo_dreno = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT id_tipo_dreno, ds_tipo_dreno
							FROM tb_tipo_dreno ";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_tipo_dreno'] == $id_tipo_dreno) {
					$options .= "<option value='" . $dados['id_tipo_dreno'] . "' selected>" . $dados['ds_tipo_dreno'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_tipo_dreno'] . "'>" . $dados['ds_tipo_dreno'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsVNI($id_tipo_vni = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT id_tipo_vni, ds_tipo_vni
							FROM tb_tipo_vni ";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_tipo_vni'] == $id_tipo_vni) {
					$options .= "<option value='" . $dados['id_tipo_vni'] . "' selected>" . $dados['ds_tipo_vni'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_tipo_vni'] . "'>" . $dados['ds_tipo_vni'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsTipoAcomodacao($id_tipo_acomodacao = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT id_tipo_acomodacao, ds_tipo_acomodacao
							FROM tb_tipo_acomodacao ";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_tipo_acomodacao'] == $id_tipo_acomodacao) {
					$options .= "<option value='" . $dados['id_tipo_acomodacao'] . "' selected>" . $dados['ds_tipo_acomodacao'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_tipo_acomodacao'] . "'>" . $dados['ds_tipo_acomodacao'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}




	public function listarCheckPadraoVentilatorio($id_avaliacao_respiratoria = null)
	{
		try {
			$con = Conecta::getConexao();


			$select = "SELECT pv.id_padrao_ventilatorio, pv.ds_padrao_ventilatorio,
							CASE WHEN rl.id_avaliacao_respiratoria is not null then 1 else 0 end as resposta
							FROM tb_padrao_ventilatorio pv
							LEFT JOIN rl_avaliacao_respiratoria_padrao_ventilatorio rl ON pv.id_padrao_ventilatorio = rl.id_padrao_ventilatorio
							AND rl.id_avaliacao_respiratoria = :id_avaliacao_respiratoria
							ORDER BY id_padrao_ventilatorio";

			$stmt = $con->prepare($select);
			$params = array(':id_avaliacao_respiratoria' => $id_avaliacao_respiratoria);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {
				if ($dados['resposta'] == 1) {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_padrao_ventilatorio[]' id='id_padrao_ventilatorio_" . $dados['id_padrao_ventilatorio'] . "' value='" . $dados['id_padrao_ventilatorio'] . "' checked> " . $dados['ds_padrao_ventilatorio'] . "
                                    <span></span>
                                </label>";
				} else {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_padrao_ventilatorio[]' id='id_padrao_ventilatorio_" . $dados['id_padrao_ventilatorio'] . "' value='" . $dados['id_padrao_ventilatorio'] . "'> " . $dados['ds_padrao_ventilatorio'] . "
                                    <span></span>
                                </label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}


	public function listarCheckTipoVentilacao($id_avaliacao_respiratoria = null)
	{
		try {
			$con = Conecta::getConexao();


			$select = "SELECT pv.id_tipo_ventilacao, pv.ds_tipo_ventilacao,
							CASE WHEN rl.id_avaliacao_respiratoria is not null then 1 else 0 end as resposta
							FROM tb_tipo_ventilacao pv
							LEFT JOIN rl_avaliacao_respiratoria_tipo_ventilacao rl ON pv.id_tipo_ventilacao = rl.id_tipo_ventilacao
							AND rl.id_avaliacao_respiratoria = :id_avaliacao_respiratoria
							ORDER BY id_tipo_ventilacao";

			$stmt = $con->prepare($select);
			$params = array(':id_avaliacao_respiratoria' => $id_avaliacao_respiratoria);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {
				if ($dados['resposta'] == 1) {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_tipo_ventilacao[]' id='id_tipo_ventilacao_" . $dados['id_tipo_ventilacao'] . "' value='" . $dados['id_tipo_ventilacao'] . "' checked> " . $dados['ds_tipo_ventilacao'] . "
                                    <span></span>
                                </label>";
				} else {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_tipo_ventilacao[]' id='id_tipo_ventilacao_" . $dados['id_tipo_ventilacao'] . "' value='" . $dados['id_tipo_ventilacao'] . "'> " . $dados['ds_tipo_ventilacao'] . "
                                    <span></span>
                                </label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}


	public function listarCheckParametroVentilatorio($id_avaliacao_respiratoria = null)
	{
		try {
			$con = Conecta::getConexao();


			$select = "SELECT pv.id_parametro_ventilatorio, pv.ds_parametro_ventilatorio,
							CASE WHEN rl.id_avaliacao_respiratoria is not null then 1 else 0 end as resposta
							FROM tb_parametro_ventilatorio pv
							LEFT JOIN rl_avaliacao_respiratoria_parametro_ventilatorio rl ON pv.id_parametro_ventilatorio = rl.id_parametro_ventilatorio
							AND rl.id_avaliacao_respiratoria = :id_avaliacao_respiratoria
							ORDER BY id_parametro_ventilatorio";

			$stmt = $con->prepare($select);
			$params = array(':id_avaliacao_respiratoria' => $id_avaliacao_respiratoria);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {
				if ($dados['resposta'] == 1) {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_parametro_ventilatorio[]' id='id_parametro_ventilatorio_" . $dados['id_parametro_ventilatorio'] . "' value='" . $dados['id_parametro_ventilatorio'] . "' checked> " . $dados['ds_parametro_ventilatorio'] . "
                                    <span></span>
                                </label>";
				} else {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_parametro_ventilatorio[]' id='id_parametro_ventilatorio_" . $dados['id_parametro_ventilatorio'] . "' value='" . $dados['id_parametro_ventilatorio'] . "'> " . $dados['ds_parametro_ventilatorio'] . "
                                    <span></span>
                                </label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}


	public function listarCheckExpansibilidade($id_avaliacao_respiratoria = null)
	{
		try {
			$con = Conecta::getConexao();


			$select = "SELECT pv.id_expansibilidade_toracica, pv.ds_expansibilidade_toracica,
							CASE WHEN rl.id_avaliacao_respiratoria is not null then 1 else 0 end as resposta
							FROM tb_expansibilidade_toracica pv
							LEFT JOIN rl_avaliacao_respiratoria_expansibilidade_toracica rl ON pv.id_expansibilidade_toracica = rl.id_expansibilidade_toracica
							AND rl.id_avaliacao_respiratoria = :id_avaliacao_respiratoria
							ORDER BY id_expansibilidade_toracica";

			$stmt = $con->prepare($select);
			$params = array(':id_avaliacao_respiratoria' => $id_avaliacao_respiratoria);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {
				if ($dados['resposta'] == 1) {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_expansibilidade_toracica[]' id='id_expansibilidade_toracica_" . $dados['id_expansibilidade_toracica'] . "' value='" . $dados['id_expansibilidade_toracica'] . "' checked> " . $dados['ds_expansibilidade_toracica'] . "
                                    <span></span>
                                </label>";
				} else {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_expansibilidade_toracica[]' id='id_expansibilidade_toracica_" . $dados['id_expansibilidade_toracica'] . "' value='" . $dados['id_expansibilidade_toracica'] . "'> " . $dados['ds_expansibilidade_toracica'] . "
                                    <span></span>
                                </label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}


	public function consultaTipoUnidade($id_unidade = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT st_tipo
							FROM tb_unidade
							WHERE st_ativo = 'A' and id_unidade = :id_unidade";

			$stmt = $con->prepare($select);
			$params = array(':id_unidade' => $id_unidade);
			$stmt->execute($params);

			$retorno = $stmt->fetch();
			return $retorno['st_tipo'];
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}


	public function listarOptionsItensGastos($id_item_gasto = null)
	{
		try {
			$con = Conecta::getConexao();
			$select = "SELECT id_item_gasto, ds_item_gasto
							FROM tb_item_gasto";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_item_gasto'] == $id_item_gasto) {
					$options .= "<option value='" . $dados['id_item_gasto'] . "' selected>" . $dados['ds_item_gasto'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_item_gasto'] . "'>" . $dados['ds_item_gasto'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}


	public function listarCheckFisioterapiaRespiratoria($id_conduta_fisioterapeutica = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT fr.id_tipo_fisioterapia_respiratoria, fr.ds_tipo_fisioterapia_respiratoria,
							CASE WHEN rl.id_tipo_fisioterapia_respiratoria is not null then 1 else 0 end as resposta
							FROM tb_tipo_fisioterapia_respiratoria fr
							LEFT JOIN rl_conduta_fisioterapeutica_respiratoria rl ON fr.id_tipo_fisioterapia_respiratoria = rl.id_tipo_fisioterapia_respiratoria
							AND rl.id_conduta_fisioterapeutica = :id_conduta_fisioterapeutica
							ORDER BY id_tipo_fisioterapia_respiratoria";

			$stmt = $con->prepare($select);
			$params = array(':id_conduta_fisioterapeutica' => $id_conduta_fisioterapeutica);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {
				if ($dados['resposta'] == 1) {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_tipo_fisioterapia_respiratoria[]' id='id_tipo_fisioterapia_respiratoria_" . $dados['id_tipo_fisioterapia_respiratoria'] . "' value='" . $dados['id_tipo_fisioterapia_respiratoria'] . "' checked> " . $dados['ds_tipo_fisioterapia_respiratoria'] . "
                                    <span></span>
                                </label>";
				} else {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_tipo_fisioterapia_respiratoria[]' id='id_tipo_fisioterapia_respiratoria_" . $dados['id_tipo_fisioterapia_respiratoria'] . "' value='" . $dados['id_tipo_fisioterapia_respiratoria'] . "'> " . $dados['ds_tipo_fisioterapia_respiratoria'] . "
                                    <span></span>
                                </label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}


	public function listarCheckCinesioterapia($id_conduta_fisioterapeutica = null)
	{
		try {
			$con = Conecta::getConexao();


			$select = "SELECT ci.id_tipo_cinesioterapia, ci.ds_tipo_cinesioterapia,
							CASE WHEN rl.id_tipo_cinesioterapia is not null then 1 else 0 end as resposta
							FROM tb_tipo_cinesioterapia ci
							LEFT JOIN rl_conduta_fisioterapeutica_cinesioterapia rl ON ci.id_tipo_cinesioterapia = rl.id_tipo_cinesioterapia
							AND rl.id_conduta_fisioterapeutica = :id_conduta_fisioterapeutica
							ORDER BY id_tipo_cinesioterapia";

			$stmt = $con->prepare($select);
			$params = array(':id_conduta_fisioterapeutica' => $id_conduta_fisioterapeutica);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {
				if ($dados['resposta'] == 1) {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_tipo_cinesioterapia[]' id='id_tipo_cinesioterapia_" . $dados['id_tipo_cinesioterapia'] . "' value='" . $dados['id_tipo_cinesioterapia'] . "' checked> " . $dados['ds_tipo_cinesioterapia'] . "
                                    <span></span>
                                </label>";
				} else {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_tipo_cinesioterapia[]' id='id_tipo_cinesioterapia_" . $dados['id_tipo_cinesioterapia'] . "' value='" . $dados['id_tipo_cinesioterapia'] . "'> " . $dados['ds_tipo_cinesioterapia'] . "
                                    <span></span>
                                </label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarCheckFisioterapiaMotora($id_conduta_fisioterapeutica = null)
	{
		try {
			$con = Conecta::getConexao();


			$select = "SELECT fm.id_tipo_fisioterapia_motora, fm.ds_tipo_fisioterapia_motora,
							CASE WHEN rl.id_tipo_fisioterapia_motora is not null then 1 else 0 end as resposta
							FROM tb_tipo_fisioterapia_motora fm
							LEFT JOIN rl_conduta_fisioterapeutica_motora rl ON fm.id_tipo_fisioterapia_motora = rl.id_tipo_fisioterapia_motora
							AND rl.id_conduta_fisioterapeutica = :id_conduta_fisioterapeutica
							ORDER BY id_tipo_fisioterapia_motora";

			$stmt = $con->prepare($select);
			$params = array(':id_conduta_fisioterapeutica' => $id_conduta_fisioterapeutica);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {
				if ($dados['resposta'] == 1) {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_tipo_fisioterapia_motora[]' id='id_tipo_fisioterapia_motora_" . $dados['id_tipo_fisioterapia_motora'] . "' value='" . $dados['id_tipo_fisioterapia_motora'] . "' checked> " . $dados['ds_tipo_fisioterapia_motora'] . "
                                    <span></span>
                                </label>";
				} else {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='id_tipo_fisioterapia_motora[]' id='id_tipo_fisioterapia_motora_" . $dados['id_tipo_fisioterapia_motora'] . "' value='" . $dados['id_tipo_fisioterapia_motora'] . "'> " . $dados['ds_tipo_fisioterapia_motora'] . "
                                    <span></span>
                                </label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}


	public function listarCausasRetorno($id_causa = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT id_causa, ds_causa
							FROM tb_causa_retorno ";

			$stmt = $con->prepare($select);
			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_causa'] == $id_causa) {
					$options .= "<option value='" . $dados['id_causa'] . "' selected>" . $dados['ds_causa'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_causa'] . "'>" . $dados['ds_causa'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsMotivoAlta($id_motivo_alta = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT id_motivo_alta, ds_motivo_alta
							FROM tb_motivo_alta";

			$stmt = $con->prepare($select);

			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_motivo_alta'] == $id_motivo_alta) {
					$options .= "<option value='" . $dados['id_motivo_alta'] . "' selected>" . $dados['ds_motivo_alta'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_motivo_alta'] . "'>" . $dados['ds_motivo_alta'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarCheckSituacaoTEV($id_tev = null)
	{
		try {
			$con = Conecta::getConexao();


			$select = "SELECT st.co_situacao_tev, st.ds_situacao_tev,
							CASE WHEN rl.co_situacao_tev is not null then 1 else 0 end as resposta
							FROM tb_situacao_tev st
							LEFT JOIN rl_tev_situacao rl ON st.co_situacao_tev = rl.co_situacao_tev
							AND rl.id_tev = :id_tev
							ORDER BY st.co_situacao_tev";

			$stmt = $con->prepare($select);
			$params = array(':id_tev' => $id_tev);
			$stmt->execute($params);

			$check = "";

			while ($dados = $stmt->fetch()) {
				if ($dados['resposta'] == 1) {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='co_situacao_tev[]' id='co_situacao_tev_" . $dados['co_situacao_tev'] . "' value='" . $dados['co_situacao_tev'] . "' checked> " . $dados['ds_situacao_tev'] . "
                                    <span></span>
                                </label>";
				} else {
					$check .= "<label class='kt-checkbox'>
                                    <input type='checkbox' name='co_situacao_tev[]' id='co_situacao_tev_" . $dados['co_situacao_tev'] . "' value='" . $dados['co_situacao_tev'] . "'> " . $dados['ds_situacao_tev'] . "
                                    <span></span>
                                </label>";
				}
			}
			return $check;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function listarOptionsDiagnostico($id_diagnostico = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT id_diagnostico, ds_diagnostico
							FROM tb_diagnostico";

			$stmt = $con->prepare($select);

			$stmt->execute();

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_diagnostico'] == $id_diagnostico) {
					$options .= "<option value='" . $dados['id_diagnostico'] . "' selected>" . $dados['ds_diagnostico'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_diagnostico'] . "'>" . $dados['ds_diagnostico'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}

	public function buscarDadosUnidade($id_unidade)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT id_unidade, ds_unidade
							FROM tb_unidade
							WHERE id_unidade = :id_unidade";

			$stmt = $con->prepare($select);
			$params = array(':id_unidade' => $id_unidade);

			$stmt->execute($params);
			$dados = $stmt->fetch();
			return $dados['ds_unidade'];
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print "ERRO:" . $e->getMessage();
		}
	}

	public function buscarDadosNivelAtividade($id_nivel_atividade)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT id_nivel_atividade, ds_nivel_atividade
							FROM tb_nivel_atividade
							WHERE id_nivel_atividade = :id_nivel_atividade";

			$stmt = $con->prepare($select);
			$params = array(':id_nivel_atividade' => $id_nivel_atividade);

			$stmt->execute($params);
			$dados = $stmt->fetch();
			return $dados['ds_nivel_atividade'];
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print "ERRO:" . $e->getMessage();
		}
	}

	public function buscarDadosNivelAtividadeResp($id_nivel_atividade_resp)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT id_nivel_atividade_resp, ds_nivel_atividade_resp
							FROM tb_nivel_atividade_resp
							WHERE id_nivel_atividade_resp = :id_nivel_atividade_resp";

			$stmt = $con->prepare($select);
			$params = array(':id_nivel_atividade_resp' => $id_nivel_atividade_resp);

			$stmt->execute($params);
			$dados = $stmt->fetch();
			return $dados['ds_nivel_atividade_resp'];
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print "ERRO:" . $e->getMessage();
		}
	}

	// public function buscarDadosGastos($id_paciente, $dt_plantao)
	// {
	// 	try{
	// 		$con = Conecta::getConexao();

	// 		$select = "SELECT id_nivel_atividade_resp, ds_nivel_atividade_resp
	// 					FROM tb_nivel_atividade_resp
	// 					WHERE id_nivel_atividade_resp = :id_nivel_atividade_resp";

	// 		$stmt = $con->prepare($select);
	// 		$params = array(':id_nivel_atividade_resp' => $id_nivel_atividade_resp);

	// 		$stmt->execute($params);
	// 		$dados = $stmt->fetch();
	// 		return $dados['ds_nivel_atividade_resp'];


	// 	}
	// 	catch(exception $e)
	// 	{
	// 		header('HTTP/1.1 500 Internal Server Error');
	// 		print "ERRO:".$e->getMessage();
	// 	}
	// }


	//Listar convenios

	public function listarOptionsConvenio($id_hospital, $id_convenio = null)
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT c.id_convenio, ds_convenio
			FROM ibranutro.tb_convenio c
			INNER JOIN rl_convenio_hospital rl on c.id_convenio = rl.id_convenio
			WHERE rl.id_hospital = :id_hospital
			ORDER BY ds_convenio;";

			$stmt = $con->prepare($select);
			$params = array(':id_hospital' => $id_hospital);
			$stmt->execute($params);

			$options = "<option value=''>Selecione..</option>";

			while ($dados = $stmt->fetch()) {
				if ($dados['id_convenio'] == $id_convenio) {
					$options .= "<option value='" . $dados['id_convenio'] . "' selected>" . $dados['ds_convenio'] . "</option>";
				} else {
					$options .= "<option value='" . $dados['id_convenio'] . "'>" . $dados['ds_convenio'] . "</option>";
				}
			}
			return $options;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print $e->getMessage();
		}
	}
	public function BuscarnotificacaoAlteracaoTN()
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT
			a.nu_atendimento,
			a.id_admissao,
    		p.ds_nome,
    		h.ds_hospital,
    		ac.ds_identificacao,
    		c.ds_convenio,
			dt_terapia_nutricional,
			FLOOR(TIMESTAMPDIFF(MINUTE, dt_update, NOW()) / 60) AS horas_decorridas
			FROM
			(SELECT id_admissao,dt_terapia_nutricional,dt_update
			FROM tb_terapia_nutricional
			WHERE DATE(dt_update) = CURDATE()) as tn
			INNER JOIN tb_admissao a ON tn.id_admissao = a.id_admissao
			INNER JOIN tb_hospital h ON h.id_hospital = a.id_hospital
			INNER JOIN tb_acomodacao ac ON ac.id_acomodacao = a.id_acomodacao
			INNER JOIN tb_paciente p ON p.id_paciente = a.id_paciente
			INNER JOIN tb_convenio c ON c.id_convenio = a.id_convenio;
			";

			$stmt = $con->prepare($select);
			$stmt->execute();
			$dados = $stmt->fetchAll();
			return $dados;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print "ERRO:" . $e->getMessage();
		}
	}
	public function BuscarnotificacaoNovoConvenio()
	{
		try {
			$con = Conecta::getConexao();

			$select = "SELECT *,
			FLOOR(TIMESTAMPDIFF(MINUTE, dt_created, NOW()) / 60) AS horas_decorridas
			FROM tb_convenio
			WHERE DATE(dt_created) = CURDATE()
			";

			$stmt = $con->prepare($select);
			$stmt->execute();
			$dados = $stmt->fetchAll();
			return $dados;
		} catch (exception $e) {
			header('HTTP/1.1 500 Internal Server Error');
			print "ERRO:" . $e->getMessage();
		}
	}
}
