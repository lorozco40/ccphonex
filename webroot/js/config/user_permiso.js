$("#sel-permisoRepo").click(function(event) {
    if(this.checked) {
        $(":checkbox[class='permisoRepo']").each(function() { this.checked = true; });
    } else {
        $(":checkbox[class='permisoRepo']").each(function() { this.checked = false; });
    }
});

$("#sel-permiso").click(function(event) {
    if(this.checked) {
        $(":checkbox[class='permiso']").each(function() { this.checked = true; });
    } else {
        $(":checkbox[class='permiso']").each(function() { this.checked = false; });
    }
});

$("#sel-permisoSec").click(function(event) {
    if(this.checked) {
        $(":checkbox[class='permisoSec']").each(function() { this.checked = true; });
    } else {
        $(":checkbox[class='permisoSec']").each(function() { this.checked = false; });
    }
});

$("#sel-permisoEsp").click(function(event) {
    if(this.checked) {
        $(":checkbox[class='permisoEsp']").each(function() { this.checked = true; });
    } else {
        $(":checkbox[class='permisoEsp']").each(function() { this.checked = false; });
    }
});

$(document).on("click", "#selcams option", function(){
    let selCamsTarget = $("#selcams").data("target");
    let current = $("#selcams").val();
    let old = $("input[name="+selCamsTarget+"]").val();
    old = old.split(',');
    //se reseteara siempre que hay una deseleccion
    old.forEach(function(a) {
        if(current == null || current.includes(a) === false) {
            $("#selwhats").val("");
            $("#selemail").val("");
        }
    }) 
    apliudupd();
});

$(document).on("click", "#selwhats option", function(){
    //Se agrega esta condicion para validar si la opcion esta seleccionada, en caso de que sea una des-seleccion no se hara nada.
    //Este cambio tambien se agrego en el main.js ya que de manera global se autoseleccionaba cada vez que se seleccionaba una opcion. 
    if( $(this).prop("selected") ){
        if ($(this).prop("selected",true)) {
            let cam = $(this).data("idcampaign");
            if ('undefined' !== typeof cam) {
                $("#selcams option[value='']").prop("selected", false);
                $("#selcams option[value="+cam+"]").prop("selected", true);
            }
        }
    }
    apliudupd();
});

$(document).on("change", "#selemail", function(){
    let cam = $("#selemail option:selected").data("idcampaign");
    if ('undefined' !== typeof cam) {
        $("#selcams option[value='']").prop("selected", false);
        $("#selcams option[value="+cam+"]").prop("selected", true);
    }
    apliudupd();
});

function apliudupd() {
    let selCamsTarget = $("#selcams").data("target");
    let selWhatsTarget = $("#selwhats").data("target");
    let selCamsValue   = $("#selcams").val() == null ? [''] : $("#selcams").val();
    let selWhatsValue  = $("#selwhats").val() == null ? [''] : $("#selwhats").val();
    $("input[name="+selCamsTarget+"]").val(selCamsValue.join(','));
    $("input[name="+selWhatsTarget+"]").val(selWhatsValue.join(','));
}
