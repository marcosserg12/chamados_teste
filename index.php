<!DOCTYPE html>
<?php
// require_once __DIR__ . '/core.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/vendor/autoload.php';
$chamados = new Chamados();
$lista_empresas = $chamados->lista_empresas();
$uri = $_SERVER['HTTP_HOST'];
?>
<html lang="pt-BR">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Chamados</title>
    <script src="../assets/js/custom/scripts/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }

        .ticket-card {
            transition: all 0.3s ease;
        }

        .ticket-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .file-preview {
            max-width: 100px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .sidebar {
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: absolute;
                z-index: 50;
                height: 100vh;
            }

            .sidebar.open {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body class="bg-gray-100 font-sans">
    <!-- Login Modal -->
    <div id="loginModal" class="fixed inset-0 flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 z-50">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-md">
            <div class="gradient-bg p-6 text-center">
                <!-- Logo Space -->
                <div class="flex justify-center mb-3">
                    <!-- <div class="bg-white/20 backdrop-blur-sm rounded-full p-4 w-24 h-24 flex items-center justify-center">
                        <i class="fas fa-headset text-white text-4xl"></i>
                    </div> -->
                    <div class="bg-white rounded-full p-2 shadow-md inline-flex items-center justify-center mb-1">
                        <img src="../assets/media/grupo_ibra.png" alt="Logo" class=" object-contain">
                    </div>

                </div>
                <h1 class="text-2xl font-bold text-white">Sistema de Chamados</h1>
                <p class="text-white/80 mt-1">Acesse sua conta</p>
            </div>

            <div class="p-8">
                <form id="loginForm" class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700" for="ds_usuario">
                            Usuário
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input class="pl-10 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md py-3 px-4 border"
                                id="ds_usuario" name="ds_usuario" type="text" placeholder="Digite seu usuário" required>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700" for="ds_senha">
                            Senha
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input class="pl-10 shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md py-3 px-4 border"
                                id="ds_senha" name="ds_senha" type="password" placeholder="Digite sua senha" required>
                        </div>
                    </div>
                    <!-- <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700" for="empresa">
                            Empresa
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <select id="empresa" name="id_empresa" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <?php foreach ($lista_empresas as $empresa) : ?>
                                    <option value="<?= $empresa['id_empresa'] ?>"><?= $empresa['ds_empresa'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div> -->

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                                Lembrar-me
                            </label>
                        </div>

                    </div>

                    <div>
                        <button id="entrar" type="submit" class="gradient-bg w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            Entrar
                        </button>
                    </div>
                </form>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">
                                suporte@ibranutro.com.br
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>


<script src="../assets/js/custom/scripts/sweetalert2.js"></script>

<script src="./assets/js/appLogin/login.js" type="text/javascript"></script>

<?php if (isset($_GET['session']) && $_GET['session'] === 'expired'): ?>
    <script>
        Swal.fire({
            icon: 'info',
            title: 'Sessão expirada',
            text: 'Sua sessão foi finalizada por inatividade. Por favor, faça login novamente.',
            confirmButtonText: 'Ok'
        });
    </script>
<?php endif; ?>