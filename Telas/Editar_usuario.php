<?php
include '../scripts.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../vendor/autoload.php';

if (!Security::isAuthenticated()) {
    redirect('../index.php');
}

$usuario = new Usuario();
$chamado = new Chamados();

$id_usuario = $_GET['id_usuario'] ?? null;
$dados = $usuario->buscaUsuario($id_usuario); // retorna nome, login, etc.
$vinculos = $usuario->buscarEmpresasELocalizacoesVinculadas($id_usuario); // id_empresa, id_localizacao

$lista_empresas = $chamado->lista_empresas();
$lista_perfil = $usuario->listaPerfil();
?>

<body class="bg-gray-100 font-sans">
    <?php include '../menu_lateral.php'; ?>
    <div class="md:ml-64 min-h-screen">
        <?php include '../header.php'; ?>

        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800">Editar Usuário</h2>
                <a href="Gerenciar_usuario.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Voltar
                </a>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <form id="form_usuario" novalidate>
                    <input type="hidden" name="id_usuario" value="<?= $dados['id_usuario'] ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                            <input name="ds_nome" type="text" required value="<?= $dados['ds_nome'] ?>"
                                class="shadow-sm block w-full border border-gray-300 rounded-md p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Login</label>
                            <input name="ds_usuario" type="text" required value="<?= $dados['ds_usuario'] ?>"
                                class="shadow-sm block w-full border border-gray-300 rounded-md p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input name="ds_email" type="email" required value="<?= $dados['ds_email'] ?>"
                                class="shadow-sm block w-full border border-gray-300 rounded-md p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                            <input name="nu_telefone" id="userPhone" type="text" required value="<?= $dados['nu_telefone'] ?>"
                                class="shadow-sm block w-full border border-gray-300 rounded-md p-2">
                        </div>
                        <!-- <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">CEP</label>
                            <input name="nu_cep" id="userCep" type="text" required value="<?= $dados['nu_cep'] ?>"
                                class="shadow-sm block w-full border border-gray-300 rounded-md p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Endereço</label>
                            <input name="ds_endereco" id="userAddress" type="text" required value="<?= $dados['ds_endereco'] ?>"
                                class="shadow-sm block w-full border border-gray-300 rounded-md p-2">
                        </div> -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Perfil</label>
                            <select name="id_perfil" class="select2 w-full border border-gray-300 rounded-md p-2" required>
                                <option value="">Selecione</option>
                                <?php foreach ($lista_perfil as $perfil) : ?>
                                    <option value="<?= $perfil['id_perfil'] ?>" <?= $dados['id_perfil'] == $perfil['id_perfil'] ? 'selected' : '' ?>>
                                        <?= $perfil['ds_perfil'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nova senha</label>
                            <input name="ds_senha" type="password" placeholder="********"
                                class="shadow-sm block w-full border border-gray-300 rounded-md p-2">
                            <p class="text-xs text-gray-500">Deixe em branco para manter a senha atual.</p>
                        </div>
                    </div>

                    <hr class="my-6">

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Empresas vinculadas</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <?php
                            $empresasMarcadas = array_unique(array_column($vinculos, 'id_empresa'));
                            foreach ($lista_empresas as $empresa) : ?>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="form-checkbox text-blue-600 id_empresa_checkbox"
                                        name="id_empresas[]" value="<?= $empresa['id_empresa'] ?>"
                                        <?= in_array($empresa['id_empresa'], $empresasMarcadas) ? 'checked' : '' ?>>
                                    <span class="ml-2"><?= $empresa['ds_empresa'] ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Localizações vinculadas</label>
                        <div id="localizacoesCheckbox" class="flex flex-col gap-4">
                            <!-- Localizações serão preenchidas via JS -->
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="gradient-bg text-white px-6 py-2 rounded-md flex items-center">
                            <i class="fas fa-save mr-2"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        const vinculosUsuario = <?= json_encode($vinculos) ?>;

        document.addEventListener("DOMContentLoaded", function() {
            IMask(document.getElementById('userPhone'), {
                mask: '(00) 00000-0000'
            });
            // IMask(document.getElementById('userCep'), { mask: '00000-000' });

            // $('#userCep').on('blur', async function () {
            //     const cep = this.value.replace(/\D/g, "");
            //     if (cep.length === 8) {
            //         try {
            //             const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
            //             const data = await response.json();
            //             if (!data.erro) {
            //                 $('#userAddress').val(`${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`);
            //             }
            //         } catch (error) {
            //             alert("Erro ao buscar o CEP.");
            //         }
            //     }
            // });

            // Inicializa localizações com empresas já marcadas
            const marcadas = $('.id_empresa_checkbox:checked').map(function() {
                return $(this).val();
            }).get();
            carregarLocalizacoes(marcadas, vinculosUsuario);

            $('.id_empresa_checkbox').on('change', function() {
                const selecionadas = $('.id_empresa_checkbox:checked').map(function() {
                    return this.value;
                }).get();
                carregarLocalizacoes(selecionadas, vinculosUsuario);
            });

            $(document).on('change', '.checkbox-todos', function() {
                const grupo = $(this).data('grupo');
                const checked = $(this).is(':checked');
                $(`.checkbox-localizacao[data-grupo="${grupo}"]`).prop('checked', checked);
            });

            $('#form_usuario').on('submit', function(e) {
                e.preventDefault();
                if (!validarFormulario(this)) return;
                if (!validarEmpresasELocalizacoes()) return;

                $.post('../appUsuario/gravar_alterar_usuario.php', $(this).serialize(), function(response) {
                    swal.fire({
                        icon: 'success',
                        title: response.message
                    }).then(() => {
                        window.location.href = 'Gerenciar_usuario.php';
                    });
                }).fail(function(data) {
                    swal.fire("Erro", data.responseJSON?.message || data.responseText, "error");
                });
            });
        });

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

        function carregarLocalizacoes(empresasSelecionadas, vinculos = []) {
            $('#localizacoesCheckbox').html('');
            empresasSelecionadas.forEach(id_empresa => {
                const nomeEmpresa = $(`.id_empresa_checkbox[value="${id_empresa}"]`).closest('label').text().trim();

                $.getJSON('../appNovoChamado/carrega_localizacao.php', {
                    id_empresa: id_empresa
                }, function(localizacoes) {
                    if (!Array.isArray(localizacoes)) return;

                    let grupoId = `grupo-${id_empresa}`;
                    let html = `<div><div class="font-bold text-gray-800 mb-2">${nomeEmpresa}</div><div class="flex flex-col gap-2">`;

                    localizacoes.forEach(loc => {
                        const valor = `${id_empresa}-${loc.id_localizacao}`;
                        const checked = vinculos.some(v => v.id_empresa == id_empresa && v.id_localizacao == loc.id_localizacao) ? 'checked' : '';
                        html += `
                            <label class="inline-flex items-center">
                                <input type="checkbox" class="form-checkbox text-blue-600 checkbox-localizacao"
                                    data-grupo="${grupoId}" name="vinculos[]" value="${valor}" ${checked}>
                                <span class="ml-2">${loc.ds_localizacao}</span>
                            </label>`;
                    });

                    html += `
                        <label class="inline-flex items-center mt-2">
                            <input type="checkbox" class="form-checkbox text-green-600 checkbox-todos" data-grupo="${grupoId}">
                            <span class="ml-2 text-green-700 font-medium text-sm">Marcar todos</span>
                        </label>
                    </div></div><hr class="my-4">`;

                    $('#localizacoesCheckbox').append(html);
                });
            });
        }
    </script>
</body>