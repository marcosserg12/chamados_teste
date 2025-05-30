$(document).ready(function () {
    InitializeWidgets();
});

var InitializeWidgets = function () {
    // $('.horario').mask('99:99');

    // $('.datepicker').datepicker({
    //     language: "pt-BR",
    //     todayHighlight: true,
    //     orientation: "bottom left"
    // });

    // $('.datepicker').mask('00/00/0000', { placeholder: '00/00/0000' });

    //formata CPF
    $("#nu_cpf").inputmask({
        "mask": "999.999.999-99",
        autoUnmask: true,
    });

    $(".mask-cnpj").inputmask({
        "mask": "99.999.999/9999-99",
        autoUnmask: true,
    });

    $(".mask-number").mask('0#');
    $('.mask-money').mask("#.##0,00", { reverse: true });

    $(".mask-altura").mask('000');
    $('.mask-peso, .mask-litros').mask("#0", { reverse: true });

    $(".mask-moeda").mask('#.##0,00', { reverse: true });

    $(".mask-telefone").mask('(##)#####-####');
};


var calculateAge = function (birthday, dateForCalculate = moment()) {
    var birthdayMoment = moment(birthday, 'DD/MM/YYYY');
    if (!birthdayMoment.isValid()) {
        return false;
    }

    return dateForCalculate.diff(birthdayMoment, 'years');
};


function redirectTo(url, successCallback) {
    $.ajax({
        url: url,
        type: 'GET',
        beforeSend: function () {
            $(".spinner-container").removeClass("kt-hidden");
        }
    })
        .done(function (data) {
            $(".spinner-container").addClass("kt-hidden");
            $('#conteudo').html(data);
            if (!$("#kt_subheader").length) {
                $('#conteudo').css("margin-top", "-55px");
            }
            /*$('#conteudo').css("margin-top", "-55px");*/
            if (typeof (successCallback) === "function") {
                successCallback();
            }
        })
        .fail(function (data) {
            if (data.status == 401) {
                window.location.href = "/index.php";
            }

            if (data.responseJSON) {
                return swal.fire("Erro", data.responseJSON.message, "error");
            }

            swal.fire("Erro", data.responseText, "error");
        })
        .always(function () {
            InitializeWidgets();
        });
}

var Validator = {
    setError: function (element, message) {
        var $divInput = $(element).parent();
        element.addClass('is-invalid');

        $divInput.addClass('validated');
        $divInput.find('.invalid-feedback').remove();

        $divInput.append('<span class="invalid-feedback">' + message + '<br></span>');
    },
    clearFormErrors: function (form) {
        $(form)
            .find('.is-invalid')
            .removeClass('is-invalid');

        $(form)
            .find('.invalid-feedback')
            .remove()
            ;
    },
    setFormError: function (errors) {
        errors.forEach(function (element) {
            Validator.setError(element.input, element.message);
        });
    }
};