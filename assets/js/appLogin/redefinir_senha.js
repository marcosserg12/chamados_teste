$(document).ready(function() {
    $('form[name="form_redefinir_senha"]').on("submit", function(e){
        e.preventDefault();

        if( ! validar($(this)))
        {
            return false;
        }

        $.ajax({
            url: '../appUsuario/gravar_redefinir_senha.php',
            data: $(this).serialize(),
            type: 'post',
            success: function(response) {
                swal.fire({
                    position: 'top-right',
                    type: 'success', 
                    title: response.message,
                    showConfirmButton: true
                });

                window.location.href = "./index.php";
            },
            error: function (data) {
                if(data.responseJSON){
                    return swal.fire("Erro", data.responseJSON.message, "error");
                }

                swal.fire("Erro", data.responseText, "error");
            }
        });
    });
});

function validar(form)
{
    var errors = [];
    Validator.clearFormErrors(form);

    var $novaSenha = $(form).find('[name="ds_nova_senha"]'),
        $confirmarNovaSenha = $(form).find('[name="ds_confirmar_nova_senha"]');

    if($novaSenha.val().length === 0)
    {
        errors.push({input: $novaSenha, message: ['Campo de preenchimento obrigatório.']});
    }

    if($confirmarNovaSenha.val().length === 0)
    {
        errors.push({input: $confirmarNovaSenha, message: ['Campo de preenchimento obrigatório.']});
    }

    if($novaSenha.val().length !== 0 && $confirmarNovaSenha.val().length !== 0 && $novaSenha.val() !== $confirmarNovaSenha.val())
    {
        errors.push({input: $confirmarNovaSenha, message: ['As senhas não conferem.']});
    }

    if(errors.length > 0){
        console.log(errors);
        Validator.setFormError(errors);
        return false;
    }

    return true;
}