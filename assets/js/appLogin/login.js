$(document).ready(function() {
	$("#entrar").on("click", function(e){
		e.preventDefault();
		if (validar())
		{
			var ds_usuario 	= $('#ds_usuario').val();
			var ds_senha    = $('#ds_senha').val();

			$.ajax({
				url: 'appUsuario/login.php',
				type:'post',
				data: {
					// id_hospital: id_hospital,
					ds_usuario: ds_usuario,
					ds_senha: ds_senha
				},
				success: function(response) {
					console.log(response);
					$(location).attr('href', 'principal.php?firstAccess');
				},
				error: function(data){
                    if(data.responseJSON){
                        return swal.fire("Erro", data.responseJSON.message, "error");
                    }

                    swal.fire("Erro", data.responseText, "error");
				}
			});
		}
	});
});


function validar()
{

	if ($("#ds_usuario").val() == "")
	{
		$("#ds_usuario").focus();
		swal.fire("Erro", "Preencha o Usuário", "error");
		return false;
	}

	if ($("#ds_senha").val() == "")
	{
		$("#ds_senha").focus();
		swal.fire("Erro", "Preencha a senha", "error");
		return false;
	}

	return true;

}