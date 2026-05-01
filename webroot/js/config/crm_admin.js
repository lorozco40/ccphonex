$(document).ready(function () {

    $(document).on("click", ".abrir-email-modal", function(){
        var id_form          = $(this).data("id");
        var id_campaign      = $(this).data("idcampaign");
        var name             = $(this).data("name");
        var id_email_account = $(this).data("idemailaccount");

        $(".abrir-email-modal").removeClass("marcar-btn");
        $(this).addClass("marcar-btn");


        $("#id_email_account2 option").each(function(){
           if( $(this).data('idcampaign') != id_campaign && $(this).val() != "" ) $(this).hide()
           else $(this).show()
        });

        if( id_email_account > 0 ) $("#id_email_account2").val(id_email_account)
        else $("#id_email_account2").val("")

        $("#nombre-modal-crm").text(name);
        $("#id_form_hidden").val(id_form);

        $("#SeleccionarEmailModal").modal("show");
    });

    $(document).on("click", "#BtnActualizarCuentaEmail", function(){
        savecta();
    });
});

function savecta(data) {
    $("#spinnerModal").modal("show");
    $.ajax({
        url: site_url+'crm/actualizarCuentaEmail',
        type: 'POST',
        data: $("#formCrmModal").serialize(),
        dataType: 'json',
    })
    .done(function(data){
        $("#spinnerModal").modal("hide");
        if ( !data.success ) {
            toastmsg(data.mensajes, "danger");
        } else {
            let id = $("#id_email_account2").val();
            $(".marcar-btn").data("idemailaccount", id);
            $("#SeleccionarEmailModal").modal("hide");
            toastmsg(data.mensajes, "success");
        }
    })
    .fail(function(data) {
        $("#spinnerModal").modal("hide");
        toastmsg("Error de red, verifica tu conexión a internet.", "danger");
    });
}
