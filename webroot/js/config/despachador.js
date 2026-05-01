$(document).on("change", ".options", function(){
    var cual = "c"+$(this).data("camp")+"values"+$(this).data("id");
    if ($(this).val()=="dropdown" || $(this).val()=="radio") {
        $("input[name='"+cual+"']").attr("readonly", false).val("");
    } else {
        $("input[name='"+cual+"']").attr("readonly", true).val("");
    }
});

$(document).on("submit", "#adddesp", function(){
    desp_name = $("#despname").val();
    desp_camp = $("#campana").val();
    if( desp_name.length == 0 || desp_camp == '0' ) {
        toastmsg("Por favor ingresa todos los datos.", "danger");
        return false;
    }
    return true;
});

$(document).on("click", ".updField", function(){
    _obj = $(this).attr('class');
    obj = parseInt(_obj.substring( _obj.indexOf("obj") , _obj.indexOf("tab") ).replace("obj", ""));
    $("#fieldForm"+obj).trigger("reset");
    $("#fieldForm"+obj+" input[type=checkbox]").prop('checked', false);
    $("#fieldForm"+obj).attr("action", site_url+"despachador/upd_field");
    $.post(site_url+"despachador/get_field", {id: $(this).data("id")}, function(data) {
        $("#fieldForm"+obj+" #id"+obj).val(data.id);
        $("#fieldForm"+obj+" #name"+obj).val(data.name);
        $("#fieldForm"+obj+" #oldname"+obj).val(data.slug);
        $("#fieldForm"+obj+" #typedb"+obj).val(data.typedb);
        $("#fieldForm"+obj+" #type"+obj).val(data.type);
        $("#fieldForm"+obj+" #opciones"+obj).val(data.options);
        $("#fieldForm"+obj+" #order"+obj).val(data.order);
        $("#fieldForm"+obj+" input[name=depend][value=" + data.depend + "]").prop('checked', true);
        if(data.readonly == 1) $("#fieldForm"+obj+" #readonly"+obj).prop('checked', true);
        if(data.required == 1) $("#fieldForm"+obj+" #required"+obj).prop('checked', true);
        $("#fieldModal"+obj).modal('show');
    },"json");
});

$(document).on("click", ".addField", function(){
    id = ($(this).attr("id")).replace("addField", "");
    $("#typedb"+id).val("0");
    $("#fieldForm"+id).trigger("reset");
    $("#fieldForm"+id).attr("action", site_url+"despachador/add_field");
    $("#fieldModal"+id).modal('show');
});

$(document).on("click", ".addQualif", function(){
    id = ($(this).attr("id")).replace("addQualif", "");
    $("#typedb"+id).val("1");
    $("#fieldForm"+id).trigger("reset");
    $("#fieldForm"+id).attr("action", site_url+"despachador/add_field");
    $("#fieldModal"+id).modal('show');
});

$(document).on("click", ".tipodesp", function(){
    id = $(this).data("id_desp");
    tipo = $(this).val();
    $.post(site_url+'despachador/updtipo',$(this).closest('form').serialize());
    $("#despauto"+id).val(tipo);
    if (tipo == "predictivo" || tipo == "predictivoamd") {
        $("#desp_colas" + id + ", #preddata" + id + ", #toques" + id).show();
        $("#desp_agentes" + id).hide();
    } else {
        $("#desp_colas" + id + ", #preddata" + id + ", #toques" + id).hide();
        $("#desp_agentes"+id).show();
    }
});

$(document).on("change", ".vueltas", function(){
    id = $(this).data("id_desp");
    $.post(site_url+'despachador/updvueltas',{
        id_desp: id,
        vueltas: $(this).val()
    });
});

$(document).on("submit", ".formpreddata", function(){
    $("#spinnerModal").modal("show");
});

$(document).on("change", ".queuedes", function (){
    span = $(this).parent().next();
    queue = $(this).val();
    camp = $(this).data("campid");
    members = '';
    if (queue != '') {
        if( typeof colas[queue] === 'undefined' ) { return false; }
        for (var member in colas[queue].members) {
            members += "<li class='list-group-item'>"+member+"</li>";
        }
        $("#colaval" + camp).val(queue);
    }
    span.html(members);
});

$(document).on("click", ".despaddregs", function(e){
    e.preventDefault();
    id = $(this).data("id");
    $("#ModalAddRegs input[name=id_desp]").val(id);
    $("#ModalAddRegs").modal("show");
});

$(document).on("click", ".toglecats", function() {
    var did = $(this).data("did");
    $("#catalogos" + did).toggleClass("d-none");
});

$(document).on("click", ".togleditcat", function() {
    var did = $(this).data("cid");
    if ($(this).html() == "Cancelar") {
        vermisec("#tablacats");
    } else {
        vermisec("#catformedit"+did);
    }
});

$(document).on("submit", ".form-ajax", function(e) {
    e.preventDefault();
    var href = $(this).attr("action");
    var did = $(this).children("input[name='did']").val();
    $.post(href, $(this).serialize(), function(data) {
        $("#catvals" + did).html(data);
        if (href.indexOf("ver_cats") > 0 || href.indexOf("edit_cats") > 0) {
            vermisec("#tablacats");
        } else {
            vermisec("#catformedit"+did);
        }
    });
});

$(document).on("click", ".link-ajax", function(e) {
    e.preventDefault();
    var href = $(this).attr("href");
    var did = $(this).data("did");
    $.get(href, function(data) {
        $("#catvals" + did).html(data);
    });
});
$(document).on("click", ".vermisec", function() {
    vermisec($(this).data("sec"));
});

function vermisec(toshowsec) {
    $(".onlyone").addClass("d-none");
    $(toshowsec).removeClass("d-none");
}