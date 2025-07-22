<?php include '../scripts.php'; ?>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../vendor/autoload.php';

$chamado = new Chamados();
$usuario = new Usuario();
$lista_empresas = $chamado->lista_empresas();
$lista_perfil = $usuario->listaPerfil();

if (!Security::isAuthenticated()) {
    redirect('../index.php');
}
?>

<body class="bg-gray-100 font-sans">
    <?php include '../menu_lateral.php'; ?>
    <div class="md:ml-64 min-h-screen">
        <?php include '../header.php'; ?>

        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Adicionar Usuário</h2>
                <a href="Gerenciar_usuario.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Voltar
                </a>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <form id="form_usuario" novalidate>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="userName">Nome</label>
                            <input id="userName" name="ds_nome" type="text" placeholder="Nome completo" required
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="userLogin">Login</label>
                            <input id="userLogin" name="ds_usuario" type="text" placeholder="Login" required
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="userEmail">Email</label>
                            <input id="userEmail" name="ds_email" type="email" placeholder="Email" required
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="userPhone">Telefone</label>
                            <input id="userPhone" name="nu_telefone" type="text" placeholder="(00) 00000-0000" required
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                        </div>
                        <!-- <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="userCep">CEP</label>
                            <input id="userCep" name="nu_cep" type="text" placeholder="00000-000" required
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="userAddress">Endereço</label>
                            <input id="userAddress" name="ds_endereco" type="text" placeholder="Endereço" required
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                        </div> -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="userType">Tipo de Usuário</label>
                            <select id="userType" name="id_perfil" class="select2 w-full border border-gray-300 rounded-md p-2" required>
                                <option value="">Selecione o Perfil</option>
                                <?php foreach ($lista_perfil as $perfil) : ?>
                                    <option value="<?= $perfil['id_perfil'] ?>"><?= $perfil['ds_perfil'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="userPassword">Senha Temporária</label>
                            <input id="userPassword" name="ds_senha" type="password" placeholder="******************" required
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border">
                            <p class="text-xs text-gray-500 mt-1">O usuário deverá alterar a senha no primeiro login.</p>
                        </div>

                    </div>
                    <hr class="my-6">
                    <!-- EMPRESAS -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Empresas vinculadas</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <?php foreach ($lista_empresas as $empresa) : ?>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="form-checkbox text-blue-600 id_empresa_checkbox required"
                                        name="id_empresas[]" value="<?= $empresa['id_empresa'] ?>">
                                    <span class="ml-2"><?= $empresa['ds_empresa'] ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- LOCALIZAÇÕES DINÂMICAS -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Localizações vinculadas</label>
                        <div id="localizacoesCheckbox" class="flex flex-col gap-4">
                            <!-- Localizações serão preenchidas via JS -->
                        </div>

                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="gradient-bg text-white px-6 py-2 rounded-md flex items-center">
                            <i class="fas fa-user-plus mr-2"></i> Criar Usuário
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Máscaras
        document.addEventListener("DOMContentLoaded", function() {
            IMask(document.getElementById('userPhone'), {
                mask: '(00) 00000-0000'
            });

            // IMask(document.getElementById('userCep'), {
            //     mask: '00000-000'
            // });
        });

        // Auto preenchimento via CEP
        // document.getElementById("userCep").addEventListener("blur", async function() {
        //     const cep = this.value.replace(/\D/g, "");
        //     if (cep.length === 8) {
        //         try {
        //             const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        //             const data = await response.json();
        //             if (!data.erro) {
        //                 document.getElementById("userAddress").value = `${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`;
        //             } else {
        //                 alert("CEP não encontrado.");
        //             }
        //         } catch (error) {
        //             alert("Erro ao buscar o CEP.");
        //         }
        //     }
        // });

        function validarEmpresasELocalizacoes() {
            let isValid = true;

            $('.error-message').remove();

            const empresasMarcadas = $('.id_empresa_checkbox:checked');
            if (empresasMarcadas.length === 0) {
                isValid = false;
                $('.id_empresa_checkbox').last().parent().parent()
                    .after('<p class="text-red-500 text-sm mt-1 error-message">Selecione pelo menos uma empresa.</p>');
            }

            const localizacoesMarcadas = $('.checkbox-localizacao:checked');
            let localizacaoValida = false;

            localizacoesMarcadas.each(function() {
                const valor = $(this).val(); // ex: "2-15"
                const id_empresa = valor.split('-')[0];
                if (empresasMarcadas.filter(`[value="${id_empresa}"]`).length > 0) {
                    localizacaoValida = true;
                    return false; // já encontrou, sai do loop
                }
            });

            if (!localizacaoValida) {
                isValid = false;
                $('#localizacoesCheckbox').after('<p class="text-red-500 text-sm mt-1 error-message">Selecione pelo menos uma localização da(s) empresa(s) escolhida(s).</p>');
            }

            return isValid;
        }


        $('#form_usuario').on("submit", function(e) {
            e.preventDefault();
            if (!validarFormulario(this)) return;

            if (!validarEmpresasELocalizacoes()) return;

            $.ajax({
                url: '../appUsuario/gravar_usuario.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    swal.fire({
                        position: 'top-right',
                        icon: 'success',
                        title: response.message,
                        showConfirmButton: true
                    }).then(() => {
                        window.location.href = 'Gerenciar_usuario.php';
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

        function carregarLocalizacoes(empresasSelecionadas) {
            $('#localizacoesCheckbox').html('');

            empresasSelecionadas.forEach(function(id_empresa) {
                const label = $(`.id_empresa_checkbox[value="${id_empresa}"]`).closest('label').text().trim();

                $.ajax({
                    url: '../appNovoChamado/carrega_localizacao.php',
                    method: 'GET',
                    data: {
                        id_empresa: id_empresa
                    },
                    success: function(data) {
                        try {
                            const localizacoes = JSON.parse(data);

                            if (Array.isArray(localizacoes) && localizacoes.length > 0) {
                                let grupoId = `grupo-empresa-${id_empresa}`;
                                let htmlGrupo = `
                            <div class="mb-6 border-b pb-4">
                                <div class="font-semibold text-gray-800 mb-2 text-lg">${label}</div>
                                <div class="flex flex-col gap-2">`;

                                localizacoes.forEach(loc => {
                                    htmlGrupo += `
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" class="form-checkbox text-blue-600 checkbox-localizacao"
                                                data-grupo="${grupoId}" name="vinculos[]" value="${id_empresa}-${loc.id_localizacao}">
                                            <span class="ml-2">${loc.ds_localizacao}</span>
                                        </label>`;

                                });

                                // Checkbox "Todos"
                                htmlGrupo += `
                                <label class="inline-flex items-center mt-2">
                                    <input type="checkbox" class="form-checkbox text-green-600 checkbox-todos" data-grupo="${grupoId}">
                                    <span class="ml-2 font-medium text-sm text-green-700">Marcar todos</span>
                                </label>`;

                                htmlGrupo += `
                                </div>
                            </div>`;

                                $('#localizacoesCheckbox').append(htmlGrupo);
                            }
                        } catch (e) {
                            console.error("Erro ao processar localizações:", e);
                        }
                    },
                    error: function() {
                        console.error("Erro ao buscar localizações da empresa", id_empresa);
                    }
                });
            });
        }

        // Delegação de evento após renderização dinâmica
        $(document).on('change', '.checkbox-todos', function() {
            const grupo = $(this).data('grupo');
            const isChecked = $(this).is(':checked');
            $(`.checkbox-localizacao[data-grupo="${grupo}"]`).prop('checked', isChecked);
        });



        $('.id_empresa_checkbox').on('change', function() {
            const empresasSelecionadas = $('.id_empresa_checkbox:checked').map(function() {
                return this.value;
            }).get();

            if (empresasSelecionadas.length > 0) {
                carregarLocalizacoes(empresasSelecionadas);
            } else {
                $('#localizacoesCheckbox').html('');
            }
        });
    </script>
</body>