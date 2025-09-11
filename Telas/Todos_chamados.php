<?php
include   '../scripts.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../vendor/autoload.php';

if (!Security::isAuthenticated()) {
    redirect('../index.php');
}
if (!in_array(Security::getUser()['id_perfil'], [1, 3, 4])) {
    redirect('../index.php');
}

$chamados = new Chamados();
$geral    = new Geral();
$dados    = $chamados->todosChamados(Security::getUser()['id_perfil'], Security::getUser()['id_usuario']);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        /* ===================== DESKTOP (>=768px) ===================== */
        @media (min-width:768px) {
            #tabelaChamados {
                width: 100%;
                table-layout: auto;
            }

            /* distribuição parecida com a outra tela */
            #tabelaChamados th:nth-child(1),
            #tabelaChamados td:nth-child(1) {
                width: 6%;
                white-space: nowrap;
            }

            #tabelaChamados th:nth-child(2),
            #tabelaChamados td:nth-child(2) {
                width: 32%;
                white-space: normal;
                word-break: break-word;
            }

            #tabelaChamados th:nth-child(3),
            #tabelaChamados td:nth-child(3) {
                width: 18%;
            }

            #tabelaChamados th:nth-child(4),
            #tabelaChamados td:nth-child(4) {
                width: 18%;
            }

            #tabelaChamados th:nth-child(5),
            #tabelaChamados td:nth-child(5) {
                width: 12%;
            }

            #tabelaChamados th:nth-child(6),
            #tabelaChamados td:nth-child(6) {
                width: 10%;
                white-space: nowrap;
            }

            #tabelaChamados th:nth-child(7),
            #tabelaChamados td:nth-child(7) {
                width: 4%;
                white-space: nowrap;
            }

            /* evita truncar ID/Ações no desktop */
            #tabelaChamados td:nth-child(1),
            #tabelaChamados td:nth-child(7) {
                overflow: visible !important;
                text-overflow: clip !important;
            }
        }

        /* ===================== MOBILE (<=767px) ===================== */
        @media (max-width: 767px) {
            .pagination {
                justify-content: center !important;
            }

            /* esconde cabeçalho */
            #tabelaChamados thead {
                display: none;
            }

            /* mantém cálculo correto de altura/colunas */
            #tabelaChamados {
                width: 100% !important;
                table-layout: fixed;
            }

            /* mantém linhas como table-row (NÃO usar block) */
            #tabelaChamados tbody tr {
                display: table-row;
                border-bottom: 1px solid #eee;
            }

            /* esconde todas as células por padrão… */
            #tabelaChamados tbody td {
                display: none;
                padding: .9rem 1rem !important;
                vertical-align: top;
            }

            /* …e mostra APENAS a 2ª (Título) como table-cell ocupando 100% */
            #tabelaChamados tbody td:nth-child(2) {
                display: table-cell !important;
                width: 100% !important;
                white-space: normal;
                overflow: visible;
                text-overflow: clip;
            }

            /* tipografia/utilitários do “card” mobile */
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

        /* clamps (sem word-break agressivo) */
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
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <?php include  '../menu_lateral.php'; ?>
    <div class="md:ml-64 min-h-screen">
        <?php include  '../header.php'; ?>
        <main class="max-w-8xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <input hidden name="nome_usuario" id="nome_usuario" value="<?= Security::getUser()['ds_nome'] ?>">

            <div id="adminTicketsView">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Todos os Chamados</h2>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <select id="adminTicketFilter" class="block appearance-none bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                <option value="">Todos</option>
                                <option value="0">Abertos</option>
                                <option value="1">Em Andamento</option>
                                <option value="9">Resolvidos</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                        <div class="relative">
                            <select id="adminAssignedFilter" class="block appearance-none bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                <option value="">Todos</option>
                                <option value="eu">Atribuídos a mim</option>
                                <option value="outros">Atribuídos a outros</option>
                                <option value="nao_atribuido">Não atribuídos</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <div class="relative w-full md:w-64">
                            <input type="text" id="adminTicketSearch" placeholder="Buscar chamados..." class="bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded w-full focus:outline-none focus:bg-white focus:border-gray-500">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <div style="padding:20px;">
                        <table id="tabelaChamados" class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criador</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Atribuído</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="recentTicketsTable" class="bg-white divide-y divide-gray-200">
                                <?php foreach ($dados as $dado): ?>
                                    <?php
                                    $status = $dado['st_status'];
                                    if ($status == 0) {
                                        $st_status = '<span class="px-2 py-1 m-badge rounded-full bg-blue-100 text-blue-800">Aberto</span>';
                                        $st_txt    = 'Aberto';
                                    } elseif ($status == 1) {
                                        $st_status = '<span class="px-2 py-1 m-badge rounded-full bg-yellow-100 text-yellow-800">Em Andamento</span>';
                                        $st_txt    = 'Em Andamento';
                                    } else {
                                        $st_status = '<span class="px-2 py-1 m-badge rounded-full bg-green-100 text-green-800">Resolvido</span>';
                                        $st_txt    = 'Resolvido';
                                        $status    = 9; // normaliza para o filtro
                                    }
                                    $dataFmt = $geral->formataData($dado['dt_data_chamado']);
                                    $tooltip = "Criador: {$dado['criado']} | Atribuído: {$dado['atribuido']} | Status: {$st_txt} | Data: {$dataFmt}";
                                    ?>
                                    <tr data-status="<?= $status ?>">
                                        <!-- ID (desktop) -->
                                        <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">#<?= $dado['id_chamado'] ?></td>

                                        <!-- TÍTULO + RESUMO MOBILE -->
                                        <td class="px-6 py-4 text-sm md:text-base font-medium text-gray-900"
                                            title="<?= htmlspecialchars($tooltip, ENT_QUOTES, 'UTF-8'); ?>">

                                            <!-- topo do “card” no mobile -->
                                            <div class="flex items-start gap-2 md:hidden mb-1">
                                                <span class="px-2 py-0.5 rounded-full bg-gray-100 text-gray-700 text-[11px]">#<?= $dado['id_chamado'] ?></span>
                                                <div class="m-title title-clamp-2"><?= $dado['ds_titulo'] ?></div>
                                            </div>

                                            <!-- título desktop -->
                                            <div class="hidden md:block"><?= $dado['ds_titulo'] ?></div>

                                            <!-- resumo mobile -->
                                            <div class="md:hidden mt-1 space-y-1">
                                                <div class="m-line clamp-2"><span class="font-semibold">Criador:</span> <?= $dado['criado'] ?></div>
                                                <div class="m-line clamp-2"><span class="font-semibold">Atribuído:</span> <?= $dado['atribuido'] ?: '—' ?></div>
                                                <div class="flex items-center gap-2 flex-wrap mt-1">
                                                    <?= $st_status ?>
                                                    <span class="text-[11px] text-gray-500"><?= $dataFmt ?></span>
                                                    <a href="../Telas/Detalhe_chamado.php?id_chamado=<?= $dado['id_chamado'] ?>" class="text-[11px] text-blue-600">Ver</a>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- colunas só no desktop -->
                                        <td class="px-6 py-4 text-sm text-gray-900 hidden md:table-cell"><?= $dado['criado'] ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-900 hidden md:table-cell"><?= $dado['atribuido'] ?></td>
                                        <td class="px-6 py-4 hidden md:table-cell"><?= $st_status ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-500 hidden md:table-cell"><?= $dataFmt ?></td>
                                        <td class="px-6 py-4 text-sm font-medium hidden md:table-cell">
                                            <a href="../Telas/Detalhe_chamado.php?id_chamado=<?= $dado['id_chamado'] ?>" class="text-blue-600 hover:text-blue-900">Ver</a>
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

    <script>
        $(document).ready(function() {
            let filtroAtribuicao = '';
            const meuNome = $('#nome_usuario').val();

            const table = $('#tabelaChamados').DataTable({
                ordering: false,
                responsive: true, // manter controle manual do layout
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                autoWidth: false, // evita inline widths que atrapalham no mobile
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
                    zeroRecords: 'Não existem dados cadastrados',
                    infoEmpty: 'Mostrando 0 até 0 de 0',
                    paginate: {
                        first: 'Primeira',
                        last: 'Última',
                        next: '>',
                        previous: '<'
                    }
                }
            });

            // Filtro de Status (coluna 4 em 0-based)
            $('#adminTicketFilter').on('change', function() {
                const map = {
                    '0': 'Aberto',
                    '1': 'Em Andamento',
                    '9': 'Resolvido'
                };
                const texto = map[$(this).val()] || '';
                table.column(4).search(texto, true, false).draw();
            });

            // Busca global
            $('#adminTicketSearch').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Filtro de Atribuição
            $.fn.dataTable.ext.search.push(function(settings, data) {
                const nomeAtribuido = data[3] || '';
                if (filtroAtribuicao === 'eu') return nomeAtribuido === meuNome;
                if (filtroAtribuicao === 'outros') return nomeAtribuido && nomeAtribuido !== meuNome;
                if (filtroAtribuicao === 'nao_atribuido') return nomeAtribuido === '';
                return true;
            });
            $('#adminAssignedFilter').on('change', function() {
                filtroAtribuicao = $(this).val();
                table.draw();
            });
        });
    </script>
</body>

</html>