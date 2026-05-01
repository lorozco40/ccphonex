$(document).ready(function(){
    actualiza_select_encuesta();
    $(document).on("change", "#cuenta", function(){
        actualiza_select_encuesta();
    });
})

function actualiza_select_encuesta() {
    id_cuenta = $("#cuenta").val();
    $("#encuesta").html('');
    $("#encuesta").val('');
    let options = "options += `<option value='0'>-Seleccione una encuesta-</option>`;";
    $.post(site_url+"whatsapp/get_rate", {id_cuenta: id_cuenta}, function(data){
        if (data == false) {
            options += `<option value="0" disabled>No hay encuestas</option>`;
        } else {
            data.forEach(function(row){
                options += `<option value="${row.id}">${row.name}</option>`;
            });
        }
        $("#encuesta").html(options);
    },"json");
}