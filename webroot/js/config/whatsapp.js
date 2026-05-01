$(document).on("click", "#btn_wac_nueva", function(){
    $("#wac_nueva").toggle();
});

$(document).on("click", "#btn_wac_cancel", function(){
    limpiarcampos();
});

$(document).on("click", ".btn_wac_guarda", function(){
    id  = $(this).data("id");
    nom = $("#wac_nombre_"+id).val();
    num = $("#wac_numero_"+id).val().replace(/\D/g,'');
    cha = $("#wac_idchatapi_"+id).val();
    tok = $("#wac_token_"+id).val();
    cam = $("#wac_campana_"+id).val();
    act = ($("#wac_active_"+id).is(':checked')) ? '1' : '0';
    alm = $("#wac_almacen_"+id).val();
    val = validaCampos(nom, num, cam);
    if(!val.salida) {
        toastmsg(val.msg, "danger");
        return false;
    } else {
        $("#spinnerModal").modal("show");
        $.post(site_url+"whatsapp/guardarcta",
            {id:id,nom:nom,num:num,cha:cha,cam:cam,tok:tok,act:act,alm:alm}, function(data){
            if(data=="error") {
                $("#spinnerModal").modal("hide");
                toastmsg("Datos erróneos o duplicados, favor de verificar.", "danger");
                return false;
            } else {
                location.reload();
            }
        },"json");
    }
});

function validaCampos(nom, num, cam){
    ret = {salida:false,msg:"Debes asignar un nombre a la cuenta (min 3 car)."};
    if(nom.length <= 2) {
        return ret;
    }
    if(num.length <= 10) {
        ret.msg = "El campo 'Número' debe ser de 11 caracteres o más y tener solo números.";
        return ret;
    }
    if(cam == 0) {
        ret.msg = "Por favor elige una campaña.";
        return ret;
    }
    ret = {salida:true,msg:"Guardado exitosamente."};
    return ret;
}

function limpiarcampos() {
    $("#wac_nueva input").val("");
    $("#wac_nueva select").val("0");
    $("#wac_nueva").toggle();
}
