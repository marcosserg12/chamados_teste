<!-- Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md mx-auto">
                <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Adicionar Usuário</h2>
                <form id="form_editar_usuario">
                    <input type="hidden" name="edit_id_usuario" id="edit_id_usuario">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="userName">Nome</label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="edit_userName" name="edit_ds_nome" type="text" placeholder="Nome completo" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="userLogin">Login</label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="edit_userLogin" name="edit_ds_usuario" type="text" placeholder="Login" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="userEmail">Email</label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="edit_userEmail" name="edit_ds_email" type="email" placeholder="Email" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="userPhone">Telefone</label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="edit_userPhone" name="edit_nu_telefone" type="text" placeholder="(00) 00000-0000" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="userCep">CEP</label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="edit_userCep" name="edit_nu_cep" type="text" placeholder="00000-000" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="userAddress">Endereço</label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="edit_userAddress" name="edit_ds_endereco" type="text" placeholder="Endereço" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="userType">Tipo de Usuário</label>
                        <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="edit_userType" name="edit_id_perfil" required>
                            <option value="1">Usuário Normal</option>
                            <option value="2">Administrador</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="userPassword">Senha Temporária</label>
                        <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="edit_userPassword" name="edit_ds_senha" type="password" placeholder="******************" required>
                        <p class="text-xs text-gray-600">O usuário deverá alterar a senha no primeiro login.</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <button type="button" class="bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="gradient-bg text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Editar Usuário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Máscara telefone
    document.addEventListener("DOMContentLoaded", function() {
        IMask(document.getElementById('edit_userPhone'), {
            mask: '(00) 00000-0000'
        });

        IMask(document.getElementById('edit_userCep'), {
            mask: '00000-000'
        });
    });

    // Auto preenchimento do endereço via CEP
    document.getElementById("edit_userCep").addEventListener("blur", async function() {
        const cep = this.value.replace(/\D/g, "");
        if (cep.length === 8) {
            try {
                const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await response.json();
                if (!data.erro) {
                    document.getElementById("userAddress").value = `${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`;
                } else {
                    alert("CEP não encontrado.");
                }
            } catch (error) {
                alert("Erro ao buscar o CEP.");
            }
        }
    });

    $('#form_editar_usuario').on("submit", function(e) {
        e.preventDefault();


        $.ajax({
            url: '../appUsuario/gravar_alterar_usuario.php',
            data: $(this).serialize(),
            type: 'post',
            success: function(response) {
                swal.fire({
                    position: 'top-center',
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
</script>