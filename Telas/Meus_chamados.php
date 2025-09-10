<?php
include   '../scripts.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../vendor/autoload.php';

if (!Security::isAuthenticated()) {
    redirect('../index.php');
}

$chamados = new Chamados();
$geral    = new Geral();
$dados    = $chamados->meusChamados(Security::getUser()['id_perfil'], Security::getUser()['id_usuario']);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        /* Título/Motivo no MOBILE: sem word-break; título com ellipsis por padrão */
        #tabelaChamados th:nth-child(2),
        #tabelaChamados td:nth-child(2) {
            width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        #tabelaChamados th:nth-child(3),
        #tabelaChamados td:nth-child(3) {
            width: 15%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ID e Ações nunca truncam quando visíveis (desktop) */
        #tabelaChamados th:nth-child(1),
        #tabelaChamados td:nth-child(1),
        #tabelaChamados th:nth-child(8),
        #tabelaChamados td:nth-child(8) {
            white-space: nowrap !important;
            overflow: visible !important;
            text-overflow: clip !important;
        }

        #tabelaChamados td:nth-child(1) {
            min-width: 64px;
        }

        #tabelaChamados td:nth-child(8) {
            min-width: 48px;
        }

        /* Clamp (sem word-break) */
        .title-clamp-2 {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .clamp-2 {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ===== MOBILE ===== */
        @media (max-width: 767px) {

            /* esconder cabeçalho e coluna ID no mobile */
            #tabelaChamados thead {
                display: none;
            }

            #tabelaChamados td:nth-child(1) {
                display: none;
            }

            /* <- some a coluna ID */

            /* linhas mais “card” */
            #tabelaChamados tr {
                border-bottom: 1px solid #eee;
            }

            #tabelaChamados td {
                padding: .75rem 1rem !important;
            }

            /* título pode quebrar naturalmente (2 linhas via clamp) */
            #tabelaChamados td:nth-child(2) {
                white-space: normal;
                overflow: visible;
                text-overflow: clip;
            }

            #tabelaChamados th:nth-child(8),
            #tabelaChamados td:nth-child(8) {
                display: none !important;
            }
        }

        /* ===== DESKTOP ===== */
        @media (min-width: 768px) {
            #tabelaChamados {
                width: 100%;
            }

            /* larguras desktop */
            #tabelaChamados th:nth-child(1),
            #tabelaChamados td:nth-child(1) {
                width: 6%;
            }

            /* ID   */
            #tabelaChamados th:nth-child(4),
            #tabelaChamados td:nth-child(4) {
                width: 10%;
            }

            /* Status */
            #tabelaChamados th:nth-child(5),
            #tabelaChamados td:nth-child(5) {
                width: 10%;
            }

            /* Data  */
            #tabelaChamados th:nth-child(6),
            #tabelaChamados td:nth-child(6) {
                width: 10%;
            }

            /* Hora  */
            #tabelaChamados th:nth-child(7),
            #tabelaChamados td:nth-child(7) {
                width: 14%;
            }

            /* Local */
            #tabelaChamados th:nth-child(8),
            #tabelaChamados td:nth-child(8) {
                width: 6%;
            }

            /* Ações */

            /* no desktop, título/motivo podem quebrar */
            #tabelaChamados th:nth-child(2),
            #tabelaChamados td:nth-child(2),
            #tabelaChamados th:nth-child(3),
            #tabelaChamados td:nth-child(3) {
                white-space: normal !important;
                overflow: visible;
                text-overflow: clip;
            }

            #tabelaChamados th:nth-child(2),
            #tabelaChamados td:nth-child(2) {
                width: 32%;
            }

            #tabelaChamados th:nth-child(3),
            #tabelaChamados td:nth-child(3) {
                width: 12%;
            }
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <?php include '../menu_lateral.php'; ?>
    <div class="md:ml-64 min-h-screen">
        <?php include '../header.php'; ?>
        <main class="max-w-8xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div id="myTicketsView">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Meus Chamados</h2>
                    <?php if (Security::getUser()['id_perfil'] == 2): ?>
                        <a href="../Telas/Novo_chamado.php" id="createNewTicketBtn" class="gradient-bg text-white px-4 py-2 rounded-md flex items-center">
                            <i class="fas fa-plus mr-2"></i> Novo Chamado
                        </a>
                    <?php endif ?>
                </div>

                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    <select id="ticketFilter" class="block appearance-none bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                        <option value="">Todos</option>
                                        <option value="1">Em Andamento</option>
                                        <option value="9">Resolvidos</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="relative w-full md:w-auto">
                                <input type="text" id="ticketSearch" placeholder="Buscar chamados..." class="bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded w-full focus:outline-none focus:bg-white focus:border-gray-500">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-200" id="myTicketsList">
                        <div id="noDataMessage" class="p-8 text-center text-gray-500 hidden">
                            <i class="fas fa-ticket-alt text-4xl mb-4 text-gray-300"></i>
                            <p>Nenhum chamado encontrado</p>
                        </div>

                        <div class="overflow-x-auto" style="padding: 10px;">
                            <table id="tabelaChamados" class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                        <th class="px-6 py-3 text-left text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Motivo</th>
                                        <th class="px-6 py-3 text-left text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Status</th>
                                        <th class="px-6 py-3 text-left text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Data</th>
                                        <th class="px-6 py-3 text-left text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Hora</th>
                                        <th class="px-6 py-3 text-left text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Local</th>
                                        <th class="px-6 py-3 text-left text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="recentTicketsTable" class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($dados as $dado): ?>
                                        <?php
                                        $status = $dado['st_status'];
                                        if ($status == 0) {
                                            $st_status = '<span class="px-2 py-1 text-[10px] sm:text-xs rounded-full bg-blue-100 text-blue-800">Aberto</span>';
                                            $st_txt    = 'Aberto';
                                        } elseif ($status == 1) {
                                            $st_status = '<span class="px-2 py-1 text-[10px] sm:text-xs rounded-full bg-yellow-100 text-yellow-800">Em Andamento</span>';
                                            $st_txt    = 'Em Andamento';
                                        } else {
                                            $st_status = '<span class="px-2 py-1 text-[10px] sm:text-xs rounded-full bg-green-100 text-green-800">Resolvido</span>';
                                            $st_txt    = 'Resolvido';
                                            $status    = 9;
                                        }
                                        $dataFmt = $geral->formataData($dado['dt_data_chamado']);
                                        $horaFmt = $geral->formataHora($dado['dt_data_chamado']);
                                        $tooltip = "Motivo: {$dado['ds_descricao_motivo']} | Status: {$st_txt} | Data: {$dataFmt} {$horaFmt} | Local: {$dado['ds_localizacao']}";
                                        ?>
                                        <tr data-status="<?= $status ?>">
                                            <!-- ID (desktop) -->
                                            <td class="px-6 py-4 text-xs md:text-sm text-gray-500 whitespace-nowrap">#<?= $dado['id_chamado'] ?></td>

                                            <!-- Título + ID (badge no mobile) + resumo mobile -->
                                            <td class="px-6 py-4 text-sm md:text-base font-medium text-gray-900"
                                                title="<?= htmlspecialchars($tooltip, ENT_QUOTES, 'UTF-8'); ?>">

                                                <!-- Linha com ID (somente mobile) + Título -->
                                                <div class="flex items-start gap-2">
                                                    <span class="md:hidden px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 text-[11px]">#<?= $dado['id_chamado'] ?></span>
                                                    <div class="title-clamp-2 md:whitespace-normal md:overflow-visible md:text-clip">
                                                        <?= $dado['ds_titulo'] ?>
                                                    </div>
                                                </div>

                                                <!-- Resumo só no mobile -->
                                                <div class="md:hidden mt-2 space-y-2">
                                                    <div class="text-xs text-gray-600 clamp-2">
                                                        <span class="font-semibold">Motivo:</span>
                                                        <?= $dado['ds_descricao_motivo'] ?>
                                                    </div>
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <?= $st_status ?>
                                                        <span class="text-[11px] text-gray-500"><?= $dataFmt ?> <?= $horaFmt ?></span>
                                                        <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-800 text-[10px]">
                                                            <?= $dado['ds_localizacao'] ?>
                                                        </span>
                                                        <a href="../Telas/Detalhe_chamado.php?id_chamado=<?= $dado['id_chamado'] ?>"
                                                            class="text-[11px] text-blue-600">Ver</a>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Colunas desktop -->
                                            <td class="px-6 py-4 text-sm font-medium text-gray-600 whitespace-normal break-words hidden md:table-cell">
                                                <?= $dado['ds_descricao_motivo'] ?>
                                            </td>
                                            <td class="px-6 py-4 hidden md:table-cell"><?= $st_status ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap hidden md:table-cell">
                                                <?= $dataFmt ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap hidden md:table-cell">
                                                <?= $horaFmt ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                                <span class="px-2 py-1 rounded-full bg-red-100 text-red-800 text-[10px] md:text-xs">
                                                    <?= $dado['ds_localizacao'] ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-xs md:text-sm font-medium whitespace-nowrap">
                                                <a href="../Telas/Detalhe_chamado.php?id_chamado=<?= $dado['id_chamado'] ?>"
                                                    class="text-blue-600 hover:text-blue-900 inline-block">Ver</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        $(document).ready(function() {
            const table = $('#tabelaChamados').DataTable({
                ordering: false,
                responsive: true,
                autoWidth: false,
                dom: `
      <"w-full"t>
      <"flex flex-col md:flex-row md:justify-between md:items-center mt-4"
        <"text-sm text-gray-600"i>
        <"mt-2 md:mt-0"p>
      >
    `,
                lengthMenu: [10, 15, 20, 30, 50, 80, 100],
                pageLength: 10,
                language: {
                    lengthMenu: '',
                    search: '',
                    info: 'Mostrando _START_ até _END_ de _TOTAL_',
                    zeroRecords: '',
                    emptyTable: '',
                    infoEmpty: 'Mostrando 0 até 0 de 0',
                    paginate: {
                        first: 'Primeira',
                        last: 'Última',
                        next: '>',
                        previous: '<'
                    }
                }
            });

            function toggleNoDataMessage() {
                const hasData = table.rows({
                    filter: 'applied'
                }).data().length > 0;
                $('#noDataMessage').toggleClass('hidden', hasData);
                $('#tabelaChamados').toggleClass('hidden', !hasData);
            }
            toggleNoDataMessage();
            table.on('draw', toggleNoDataMessage);

            // Filtro de status (coluna 3 = "Status" em 0-based)
            $('#ticketFilter').on('change', function() {
                const map = {
                    '0': 'Aberto',
                    '1': 'Em Andamento',
                    '9': 'Resolvido'
                };
                const texto = map[$(this).val()] || '';
                table.column(3).search(texto, true, false).draw();
            });

            $('#ticketSearch').on('keyup', function() {
                table.search(this.value).draw();
            });
        });
    </script>
</body>

</html>