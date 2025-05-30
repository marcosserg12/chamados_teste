$(document).ready(function() {
    $('form[name="form_recuperar_senha"]').on("submit", function(e){
        e.preventDefault();

        if( ! validar($(this)))
        {
            return false;
        }

        $.ajax({
            url: '../appUsuario/gravar_recuperar_senha.php',
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

    if($(form).find('[name="ds_usuario"]').val().length === 0)
    {
        errors.push({input: $(form).find('[name="ds_usuario"]'), message: ['Campo de preenchimento obrigatório.']});
    }

    if($(form).find('[name="ds_email"]').val().length === 0)
    {
        errors.push({input: $(form).find('[name="ds_email"]'), message: ['Campo de preenchimento obrigatório.']});
    }

    if(errors.length > 0){
        console.log(errors);
        Validator.setFormError(errors);
        return false;
    }

    return true;
}