<?php include __DIR__ . '/scripts.php'; ?>
<!-- 0 - Chamado em Aberto -->
<!-- 1 - Chamado em Andamento -->
<!-- 9 - Chamado Finalizado -->
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/vendor/autoload.php';

if (!Security::isAuthenticated()) {
    redirect('../index.php');
}

$chamados = new Chamados();
$geral = new Geral();
$dados = $chamados->listaChamadosRecentes(Security::getUser()['id_perfil'], Security::getUser()['id_usuario']);
$totalchamados = $chamados->totalChamadosPorUsuario(Security::getUser()['id_perfil'], Security::getUser()['id_usuario']);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <style>
        /* ===== Utilitários para clamps (sem word-break agressivo) ===== */
        .title-clamp-2 {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ===== Desktop ===== */
        @media (min-width:768px) {
            #tabelaSimples {
                width: 100%;
                table-layout: auto;
            }

            /* distribuições confortáveis */
            #tabelaSimples th:nth-child(1),
            #tabelaSimples td:nth-child(1) {
                width: 8%;
                white-space: nowrap;
            }

            #tabelaSimples th:nth-child(2),
            #tabelaSimples td:nth-child(2) {
                width: 42%;
                white-space: normal;
                word-break: break-word;
            }

            #tabelaSimples th:nth-child(3),
            #tabelaSimples td:nth-child(3) {
                width: 14%;
            }

            #tabelaSimples th:nth-child(4),
            #tabelaSimples td:nth-child(4) {
                width: 14%;
                white-space: nowrap;
            }

            #tabelaSimples th:nth-child(5),
            #tabelaSimples td:nth-child(5) {
                width: 10%;
                white-space: nowrap;
            }

            #tabelaSimples th:nth-child(6),
            #tabelaSimples td:nth-child(6) {
                width: 12%;
                white-space: nowrap;
            }
        }

        /* ===== Mobile (<=767px) — card dentro da célula Título ===== */
        @media (max-width:767px) {

            /* esconder o cabeçalho */
            #tabelaSimples thead {
                display: none;
            }

            /* manter comportamento de tabela e cálculos corretos */
            #tabelaSimples {
                width: 100% !important;
                table-layout: fixed;
            }

            #tabelaSimples tbody tr {
                display: table-row;
                border-bottom: 1px solid #eee;
            }

            /* esconder TODAS as células por padrão… */
            #tabelaSimples tbody td {
                display: none;
                padding: .9rem 1rem !important;
                vertical-align: top;
            }

            /* …e mostrar APENAS a coluna 2 (Título) como table-cell ocupando 100% */
            #tabelaSimples tbody td:nth-child(2) {
                display: table-cell !important;
                width: 100% !important;
                white-space: normal;
                overflow: visible;
                text-overflow: clip;
            }

            /* tipografia do “card” mobile dentro da célula Título */
            .m-title {
                font-size: 1rem;
                line-height: 1.25rem;
                font-weight: 600;
                color: #111827;
            }

            .m-line {
                font-size: .8rem;
                color: #4B5563;
            }

            .m-badge {
                font-size: .68rem;
            }
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">

    <?php include __DIR__ . '/menu_lateral.php'; ?>
    <div class="md:ml-64 min-h-screen">
        <?php include __DIR__ . '/header.php'; ?>

        <main class="max-w-8xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div id="dashboardView">

                <!-- Cards de contagem -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Chamados Abertos</p>
                                <p class="text-2xl font-semibold text-gray-800" id="openTicketsCount"><?= $totalchamados['aberto']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Em Andamento</p>
                                <p class="text-2xl font-semibold text-gray-800" id="inProgressTicketsCount"><?= $totalchamados['andamento']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Resolvidos</p>
                                <p class="text-2xl font-semibold text-gray-800" id="resolvedTicketsCount"><?= $totalchamados['resolvidos']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Últimos Chamados -->
                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Últimos Chamados</h2>
                        <?php if (in_array(Security::getUser()['id_perfil'], [1, 3, 4])) : ?>
                            <a href="../Telas/Todos_chamados.php" class="text-sm text-blue-600 hover:underline" id="viewAllTickets">Ver todos</a>
                        <?php endif ?>
                    </div>

                    <div id="noDataMessage" class="p-8 text-center text-gray-500 hidden">
                        <i class="fas fa-ticket-alt text-4xl mb-4 text-gray-300"></i>
                        <p>Nenhum chamado encontrado</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="tabelaSimples" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>

                            <tbody class="bg-white divide-y divide-gray-200" id="recentTicketsTable">
                                <?php foreach ($dados as $dado):
                                    // badge de status + texto
                                    if ($dado['st_status'] == 0) {
                                        $st_badge = '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Aberto</span>';
                                        $st_txt   = 'Aberto';
                                    } elseif ($dado['st_status'] == 1) {
                                        $st_badge = '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Em Andamento</span>';
                                        $st_txt   = 'Em Andamento';
                                    } else {
                                        $st_badge = '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Resolvido</span>';
                                        $st_txt   = 'Resolvido';
                                    }

                                    $dataFmt = $geral->formataData($dado['dt_data_chamado']);
                                    $horaFmt = $geral->formataHora($dado['dt_data_chamado']);
                                    $tooltip = "Status: {$st_txt} | Data: {$dataFmt} {$horaFmt}";
                                ?>
                                    <tr>
                                        <!-- ID (desktop) -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#<?= $dado['id_chamado'] ?></td>

                                        <!-- TÍTULO + RESUMO MOBILE -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" title="<?= htmlspecialchars($tooltip, ENT_QUOTES, 'UTF-8'); ?>">
                                            <!-- Topo do card no mobile -->
                                            <div class="flex items-start gap-2 md:hidden mb-1">
                                                <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 text-[11px]">#<?= $dado['id_chamado'] ?></span>
                                                <div class="m-title title-clamp-2"><?= $dado['ds_titulo'] ?></div>
                                            </div>

                                            <!-- Título no desktop -->
                                            <div class="hidden md:block"><?= $dado['ds_titulo'] ?></div>

                                            <!-- Resumo no mobile -->
                                            <div class="md:hidden mt-1 space-y-1">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <?= $st_badge ?>
                                                    <span class="text-[11px] text-gray-500"><?= $dataFmt ?> <?= $horaFmt ?></span>
                                                    <a href="../Telas/Detalhe_chamado.php?id_chamado=<?= $dado['id_chamado'] ?>" class="text-[11px] text-blue-600">Ver</a>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Status (desktop) -->
                                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell"><?= $st_badge ?></td>
                                        <!-- Data (desktop) -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell"><?= $dataFmt ?></td>
                                        <!-- Hora (desktop) -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell"><?= $horaFmt ?></td>
                                        <!-- Ações (desktop) -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium hidden md:table-cell">
                                            <a href="../Telas/Detalhe_chamado.php?id_chamado=<?= $dado['id_chamado'] ?>" class="text-blue-600 hover:text-blue-900 view-ticket">Ver</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Modal de Redefinição de Senha -->
    <div x-data="{ open: <?= Security::getUser()['st_reset_senha'] == 1 ? 'true' : 'false' ?> }"
        x-show="open"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
        x-transition>
        <div class="bg-white p-6 rounded-lg shadow max-w-md w-full">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Redefinir Senha</h2>
            <form id="formResetSenha">
                <input type="hidden" name="id_usuario" value="<?= Security::getUser()['id_usuario'] ?>">
                <div class="mb-4">
                    <label for="novaSenha" class="block text-sm font-medium text-gray-700">Nova Senha</label>
                    <input type="password" id="novaSenha" name="novaSenha" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
                </div>
                <div class="mb-4">
                    <label for="confirmarSenha" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                    <input type="password" id="confirmarSenha" name="confirmarSenha" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-3 py-2">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tabela = document.getElementById('tabelaSimples');
            const tbody = document.getElementById('recentTicketsTable');
            const noDataMessage = document.getElementById('noDataMessage');

            const temChamados = tbody.querySelectorAll('tr').length > 0;

            if (temChamados) {
                tabela.classList.remove('hidden');
                noDataMessage.classList.add('hidden');
            } else {
                tabela.classList.add('hidden');
                noDataMessage.classList.remove('hidden');
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        $('#formResetSenha').on('submit', function(e) {
            e.preventDefault();

            const senha = $('#novaSenha').val();
            const confirmar = $('#confirmarSenha').val();

            if (senha !== confirmar) {
                Swal.fire({
                    title: 'Erro!',
                    text: 'As senhas não coincidem.',
                    icon: 'error'
                });
                return;
            }
            $('#loader').show();

            $.ajax({
                url: 'appUsuario/alterar_senha.php',
                method: 'POST',
                data: {
                    id_usuario: $('input[name="id_usuario"]').val(),
                    senha
                },
                dataType: 'json',
                success: function(response) {
                    $('#loader').hide();
                    Swal.fire({
                            title: response.message || 'Senha alterada com sucesso!',
                            icon: 'success'
                        })
                        .then(() => location.reload());
                },
                error: function() {
                    Swal.fire({
                        title: 'Erro!',
                        text: 'Erro ao tentar alterar a senha.',
                        icon: 'error'
                    });
                }
            });
        });
    </script>
</body>

</html>