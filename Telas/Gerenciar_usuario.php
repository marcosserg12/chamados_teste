<?php
include   '../scripts.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require  '../vendor/autoload.php';
session_start();

if (!Security::isAuthenticated()) {
    redirect('../index.php');
}
if (Security::getUser()['id_perfil'] != 1) {
    redirect('../index.php');
}

$usuario = new Usuario();
$geral = new Geral();
$dados = $usuario->lista();

?>


<body class="bg-gray-100 font-sans">

    <?php include   '../menu_lateral.php'; ?>
    <div class="md:ml-64 min-h-screen">
        <?php include   '../header.php'; ?>
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div id="adminUsersView">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Gerenciar Usuários</h2>
                    <button id="addNewUserBtn" type="button" class="gradient-bg text-white px-4 py-2 rounded-md flex items-center" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-user-plus mr-2"></i> Novo Usuário
                    </button>
                </div>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <div class="relative w-full md:w-64">
                            <input type="text" id="userSearch" placeholder="Buscar usuários..." class="bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded w-full focus:outline-none focus:bg-white focus:border-gray-500">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto" style="padding: 10px;">
                        <table id="tabelaUsuarios" class="min-w-[700px]">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="adminUsersTable">

                                <!-- Users will be loaded here -->
                                <?php foreach ($dados as $dado) {
                                    if ($dado['st_ativo'] == 'A') {
                                        $st_status = '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                            Ativo
                        </span>';
                                    } else {
                                        $st_status = '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                            Desativado
                        </span>';
                                    }
                                ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $dado['id_usuario'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $dado['ds_nome'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $dado['ds_usuario'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $dado['ds_perfil'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap"><?= $dado['st_ativo'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include  '../Telas/_modal_adicionar_usuario.php'; ?>
    <?php include  '../Telas/_modal_editar_usuario.php'; ?>

    <script>
        const table = $('#tabelaUsuarios').DataTable({
            ordering: true,
            responsive: true, // permite adaptação automática
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
            },

            columnDefs: [{
                    targets: -1,
                    title: 'Ações',
                    orderable: false,
                    render: function(data, type, full, meta) {
                        if (full[4] == 'A') {
                            return `
        <a href="#" class="btn btn-sm btn-outline-primary me-1 btn-editar2" title="Alterar Cadastro" data-usuario="${full[0]}" >
            <i class="bi bi-pencil-square"></i>
        </a>
        <a href="#" class="btn btn-sm btn-outline-danger btn-excluir" title="Inativar" data-usuario="${full[0]}" data-status="I">
            <i class="bi bi-slash-circle"></i>
        </a>`;
                        } else if (full[4] == 'I') {
                            return `
        <a href="#" class="btn btn-sm btn-outline-primary me-1 btn-editar2" title="Alterar Cadastro" data-usuario="${full[0]}">
            <i class="bi bi-pencil-square"></i>
        </a>
        <a href="#" class="btn btn-sm btn-outline-success btn-excluir" title="Ativar" data-usuario="${full[0]}" data-status="A">
            <i class="bi bi-check-circle"></i>
        </a>`;
                        } else {
                            return `
        <a href="#" class="btn btn-sm btn-outline-primary me-1 btn-editar2" title="Alterar Cadastro" data-usuario="${full[0]}" >
            <i class="bi bi-pencil-square"></i>
        </a>
        <a href="#" class="btn btn-sm btn-outline-danger btn-excluir" title="Inativar" data-usuario="${full[0]}" data-status="I">
            <i class="bi bi-slash-circle"></i>
        </a>`;
                        }
                    }

                },
                {
                    targets: 0,
                    visible: false
                },
                {
                    targets: [4],
                    render: function(data, type, full, meta) {
                        var status = {
                            'A': {
                                'title': 'Ativo',
                                'class': 'px-2 py-1 text-xs rounded-full bg-green-100 text-green-800'
                            },
                            'I': {
                                'title': 'Inativo',
                                'class': 'px-2 py-1 text-xs rounded-full bg-red-100 text-red-800'
                            },
                        };
                        if (typeof status[data] === 'undefined') {
                            return data;
                        }
                        return '<span class="kt-badge ' + status[data].class + ' kt-badge--inline kt-badge--pill">' + status[data].title + '</span>';
                    }
                }
            ]
        });
        // Busca personalizada (campo fora do DataTables)
        $('#userSearch').on('keyup', function() {
            table.search(this.value).draw();
        });

        $(document).on("click", ".btn-excluir", function() {
            var id_usuario = $(this).data("usuario");
            var st_ativo = $(this).data("status");

            $.ajax({
                url: '../appUsuario/alterar_status.php',
                type: 'post',
                data: {
                    id_usuario: id_usuario,
                    st_ativo: st_ativo
                },
                success: function(data) {
                    swal.fire({
                        position: 'top-center',
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
        });

        $(document).on("click", ".btn-editar2", function() {
            // e.preventDefault();

            const userId = $(this).data('usuario');

            $.ajax({
                url: '../appUsuario/buscar_usuario.php',
                type: 'GET',
                data: {
                    id: userId
                },
                dataType: 'json',
                success: function(response) {
                    const data = response.data;

                    $('#edit_userName').val(data.ds_nome);
                    $('#edit_userLogin').val(data.ds_usuario);
                    $('#edit_userEmail').val(data.ds_email);
                    $('#edit_userPhone').val(data.nu_telefone);
                    $('#edit_userCep').val(data.nu_cep);
                    $('#edit_userAddress').val(data.ds_endereco);
                    $('#edit_userType').val(data.id_perfil);
                    $('#edit_userPassword').val('');
                    $('#edit_id_usuario').val(data.id_usuario);

                    $('#editUserModal').modal('show');
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || 'Erro ao buscar dados do usuário.';
                    swal.fire("Erro", msg, "error");
                }
            });
        });
    </script>