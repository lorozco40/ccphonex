$(document).on("click", "#btn_emergencia", function(){
    $("#respuesta").html("<i class='fas fa-spinner fa-2x fa-pulse'></i><span class='sr-only'>Cargando ...</span>").css("display", "block");
    $("#btn_emergencia").css("display", "none");
    $.ajax({
        url: site_url+"generales/emergencia_ejecutar",
        type: 'POST',
        // async: false,
        cache: false,
        timeout: 30000,
        dataType: "json",
        error: function(){
            $("#respuesta").html("Error de comunicación");
            $("#btn_emergencia").css("display", "block");
        },
        success: function(msg){
            $("#respuesta").html(msg);
            $("#btn_emergencia").css("display", "block");
        }
    });
});
