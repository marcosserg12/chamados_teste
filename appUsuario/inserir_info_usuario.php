<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/../vendor/autoload.php';


$ponto = new Ponto();
$usuario = new Usuario();
$geral = new Geral();

?>
<div class="card-header pt-7">
    <h4>Adicionar Usuário</h4>
</div>
<div class="card-body">
    <form class="kt-form" name="form_usuario">
        <div class="kt-portlet__body">
            <div class="form-group row">
                <div class="col-md-6">
                    <label>Nome Usuário</label>
                    <input type="text" id="ds_nome" name="ds_nome" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>CPF</label>
                    <input type="text" id="nu_cpf" name="nu_cpf" class="form-control">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label>Login</label>
                    <input type="text" id="ds_usuario" name="ds_usuario" class="form-control">
                </div>
                <div class="col-md-4">
                    <label>Telefone</label>
                    <input type="text" id="nu_telefone" name="nu_telefone" class="form-control">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label>E-mail</label>
                    <input type="text" id="ds_email" name="ds_email" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="id_perfil">Perfil</label>
                    <select class="form-control" id="id_perfil" name="id_perfil">
                        <option value=""></option>
                        <option value="1">Admin</option>
                        <option value="2">Funcionário</option>
                    </select>
                </div>

            </div>
            <div class="form-group row">
                <div class="col-md-4">
                    <label>CEP</label>
                    <input type="text" id="nu_cep" name="nu_cep" class="form-control" onblur="buscarEndereco()">
                </div>
                <div class="col-md-6">
                    <label>Endereço</label>
                    <textarea type="text" id="ds_endereco" name="ds_endereco" class="form-control"></textarea>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-md-8">
                    <label>Ativo</label>
                    <div class="kt-radio-inline">
                        <label class="kt-radio">
                            <input type="radio" id="st_ativo" name="st_ativo" value="A" checked> Sim
                            <span></span>
                        </label>
                        <label class="kt-radio">
                            <input type="radio" name="st_ativo" value="I"> Não
                            <span></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="kt-portlet__foot">
            <div class="kt-form__actions">
                <button type="submit" class="btn btn-success">Salvar</button>
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
                    text: 'Carregando...',
                    icon: 'info',
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
                //     icon: 'success',
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
            swal.fire("Erro", 'CEP inválido. O CEP deve ter 8 dígitos', "error");
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
        $('form[name="form_usuario"]').on("submit", function(e) {
            e.preventDefault();

            if (!validar($(this))) {
                return false;
            }

            $.ajax({
                url: 'appUsuario/gravar_usuario.php',
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
    });
</script>