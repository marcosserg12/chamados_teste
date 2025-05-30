<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/../vendor/autoload.php';


$ponto = new Ponto();
$usuario = new Usuario();
$geral = new Geral();
$id_usuario_info = $_REQUEST['id_usuario'];
$dados_usuario = $usuario->buscaUsuario($id_usuario_info);

?>
<div class="card-header pt-7">
    <h4>Editar Informações Usuário</h4>
</div>
<div class="card-body">
    <form class="kt-form" name="form_alterar_usuario">
        <div class="kt-portlet__body">
            <div class="form-group row">
                <div class="col-md-6">
                    <label>Nome Usuário</label>
                    <input type="text" id="ds_nome" name="ds_nome" class="form-control" value="<?php echo $dados_usuario['ds_nome'] ?>">
                </div>
                <div class="col-md-4">
                    <label>CPF</label>
                    <input type="text" id="nu_cpf" name="nu_cpf" class="form-control" value="<?php echo $dados_usuario['nu_cpf'] ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label>Login</label>
                    <input type="text" id="ds_usuario" name="ds_usuario" class="form-control" value="<?php echo $dados_usuario['ds_usuario'] ?>">
                </div>
                <div class="col-md-2">
                    <label>Telefone</label>
                    <input type="text" id="nu_telefone" name="nu_telefone" class="form-control" value="<?php echo $dados_usuario['nu_telefone'] ?>">
                </div>
                <div class="col-md-2">
                    <label>Data do atestado ASO </label>
                    <div class="position-relative d-flex align-items-center">
                        <input class="form-control " name="dt_atestado" value="<?php echo $geral->formataData($dados_usuario['dt_atestado']) ?>"/>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label>E-mail</label>
                    <input type="text" id="ds_email" name="ds_email" class="form-control" value="<?php echo $dados_usuario['ds_email'] ?>">
                </div>
                <div class="col-md-4">
                    <label for="id_perfil">Perfil</label>
                    <select class="form-control" id="id_perfil" name="id_perfil">
                        <option value=""></option>
                        <option <?php if ($dados_usuario['id_perfil'] == 1) echo "selected" ?> value="1">Admin</option>
                        <option <?php if ($dados_usuario['id_perfil'] == 2) echo "selected" ?> value="2">Funcionário</option>
                    </select>
                </div>

            </div>
            <div class="form-group row">
                <div class="col-md-4">
                    <label>CEP</label>
                    <input type="text" id="nu_cep" name="nu_cep" class="form-control" onblur="buscarEndereco()" value=" <?php echo $dados_usuario['nu_cep'] ?>">
                </div>
                <div class="col-md-6">
                    <label>Endereço</label>
                    <textarea type="text" id="ds_endereco" name="ds_endereco" class="form-control"><?php echo $dados_usuario['ds_endereco'] ?></textarea>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-8">
                    <label>Ativo</label>
                    <div class="kt-radio-inline">
                        <label class="kt-radio">
                            <input type="radio" id="st_ativo" name="st_ativo" value="A" <?php if ($dados_usuario['st_ativo'] == 'A') echo "checked" ?>> Sim
                            <span></span>
                        </label>
                        <label class="kt-radio">
                            <input type="radio" name="st_ativo" value="I" <?php if ($dados_usuario['st_ativo'] == 'I') echo "checked" ?>> Não
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
            <input type="hidden" name="id_usuario" id="id_usuario" value="<?php echo $id_usuario_info ?>">
        </div>
        <br>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <button type="submit" class="btn btn-success">Alterar</button>
                <button type="button" id="resetar_senha" class="btn btn-info">Resetar Senha</button>
                <button type="button" class="btn btn-danger" onclick="voltar()">Cancelar</button>
            </div>
        </div>
    </form>
</div>


<script>
    function voltar() {
        $.ajax({
            url: 'tabela_usuario.php',
            type: 'post',
            beforeSend: function() {
                swal.fire({
                    position: 'center',
                    icon: 'info',
                    text: 'Carregando...',
                    timer: '800',
                    showConfirmButton: false
                });
                $("#editar_usuario").html("<p></p>");
            },
            success: function(response) {
                // swal.fire({
                //     position: 'center',
                //     text: 'Carregado',
                //     timer: '700',
                //     showConfirmButton: false
                // });
                $("#editar_usuario").html(response);
            },
            error: function(data) {
                swal.fire("Erro", data.responseText, "error");
            }
        });
    }

    function buscarEndereco() {
        // Captura o valor do campo de CEP
        let cep = document.getElementById('nu_cep').value;

        // Remove qualquer caractere que não seja número
        cep = cep.replace(/\D/g, '');

        // Verifica se o CEP tem exatamente 8 dígitos
        if (cep.length === 8) {
            // Monta a URL para a consulta do CEP na API ViaCEP
            let url = `https://viacep.com.br/ws/${cep}/json/`;

            // Faz a requisição usando fetch
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (!data.erro) {
                        // Preenche o campo de endereço com os dados retornados
                        document.getElementById('ds_endereco').value = `${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`;
                    } else {
                        alert('CEP não encontrado.');
                    }
                })
                .catch(error => {
                    alert('Erro ao buscar o CEP. Verifique sua conexão.');
                });
        } else {
            alert('CEP inválido. O CEP deve ter 8 dígitos.');
        }
    }

    function validar(form) {
        var errors = [];
        Validator.clearFormErrors(form);

        if ($(form).find('[name="ds_nome"]').val().length === 0) {
            errors.push({
                input: $(form).find('[name="ds_nome"]'),
                message: ['Campo de preenchimento obrigatório.']
            });
        }

        if ($(form).find('[name="ds_usuario"]').val().length === 0) {
            errors.push({
                input: $(form).find('[name="ds_usuario"]'),
                message: ['Campo de preenchimento obrigatório.']
            });
        }

        if ($(form).find('[name="nu_cpf"]').val().length === 0) {
            errors.push({
                input: $(form).find('[name="nu_cpf"]'),
                message: ['Campo de preenchimento obrigatório.']
            });
        }

        if ($(form).find('[name="nu_telefone"]').val().length === 0) {
            errors.push({
                input: $(form).find('[name="nu_telefone"]'),
                message: ['Campo de preenchimento obrigatório.']
            });
        }

        if ($(form).find('[name="ds_email"]').val().length === 0) {
            errors.push({
                input: $(form).find('[name="ds_email"]'),
                message: ['Campo de preenchimento obrigatório.']
            });
        }

        if ($(form).find('[name="id_perfil"]').val().length === 0) {
            errors.push({
                input: $(form).find('[name="id_perfil"]'),
                message: ['Campo de preenchimento obrigatório.']
            });
        }

        if ($(form).find('[name="nu_cep"]').val().length === 0) {
            errors.push({
                input: $(form).find('[name="nu_cep"]'),
                message: ['Campo de preenchimento obrigatório.']
            });
        }

        if ($(form).find('[name="ds_endereco"]').val().length === 0) {
            errors.push({
                input: $(form).find('[name="ds_endereco"]'),
                message: ['Campo de preenchimento obrigatório.']
            });
        }

        if ($(form).find('[name="st_ativo"]').filter(':checked').length === 0) {
            errors.push({
                input: $(form).find('[name="st_ativo"]').first().parent(),
                message: ['Campo de preenchimento obrigatório.']
            });
        }

        if (errors.length > 0) {
            console.log(errors);
            Validator.setFormError(errors);
            return false;
        }

        return true;
    }

    $(document).ready(function() {
        $("#nu_cpf").inputmask({
            "mask": "999.999.999-99",
            autoUnmask: true,
        });
        $("#nu_telefone").inputmask({
            "mask": "(99) 99999-9999",
            autoUnmask: true,
        });
        $("#nu_cep").inputmask({
            "mask": "99999-999",
            autoUnmask: true,
        });
        $('input[name="dt_atestado"]').flatpickr({
            dateFormat: "d/m/Y"
        });

        $('form[name="form_alterar_usuario"]').on("submit", function(e) {
            e.preventDefault();

            if (!validar($(this))) {
                return false;
            }

            $.ajax({
                url: 'appUsuario/gravar_alterar_usuario.php',
                data: $(this).serialize(),
                type: 'post',
                success: function(response) {
                    swal.fire({
                        position: 'top-right',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: true
                    }).then((result) => {
                        location.reload();

                    });
                },
                error: function(data) {
                    if (data.responseJSON) {
                        return swal.fire("Erro", data.responseJSON.message, "error");
                    }

                    swal.fire("Erro", data.responseText, "error");
                }
            });
        });
        $("#resetar_senha").on("click", function() {
            Swal.fire({
                title: "Tem certeza?",
                text: "Tem certeza que deseja resetar a senha deste usuário?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Resetar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    var id_usuario = document.getElementById('id_usuario').value;
                    $.ajax({
                        url: 'appUsuario/resetar_resenha.php',
                        type: 'post',
                        data: {
                            id_usuario: id_usuario
                        },
                        success: function(data) {
                            swal.fire({
                                position: 'top-right',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: true
                            }).then((result) => {
                                location.reload();

                            });
                        },
                        error: function(data) {
                            swal.fire('Erro!', data.responseJSON.message, 'error');
                        }
                    });
                }
            });
        });
    });
</script>