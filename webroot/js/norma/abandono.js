var min = $("#min").val();
var max = $("#max").val();
var pag = $("#pag").val();
var fijo = $("#campanas option:selected").val();
var llamadas =$("#llamadas option:selected").val();

$(document).ready(function() {
    pag = 0;
    traerdatosinb();
});

$(document).on("change", "#min, #max, #campanas, #llamadas", function() {
    pag = 0;
    min = $("#min").val();
    max = $("#max").val();
    fijo = $("#campanas option:selected").val();
    llamadas =$("#llamadas option:selected").val();
    traerdatosinb();
});

$(document).on("click", ".page-link", function(e) {
    e.preventDefault();
    pag = ($(this).data("ci-pagination-page")*REGS_POR_PAG)-REGS_POR_PAG;
    traerdatosinb();
});

function traerdatosinb() {
    $("#abandono").html("<tr><td colspan='13' class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    $(".parad-none").addClass("d-none").removeClass('parad-none');
    $.post(site_url+"norma035/lista_abandono", {min: min, max: max, page: pag, campana: fijo, llamadas: llamadas}, function(data) {
        var html = "<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>";
        if (data.data.length == '' || data.data.length == null) {
            $("#abandono").html(html);
        } else {
            html = "<div class='table table-striped'><div class='table-header-group'>"+
                "<div class='table-cell'>Fecha</div><div class='table-cell'>Número</div><div class='table-cell'>Campaña</div>"+
                "<div class='table-cell'>DID</div><div class='table-cell'>CallerId</div><div class='table-cell'>Espera en cola</div>"+
                "<div class='table-cell'>Espera total</div><div class='table-cell'>Duración</div><div class='table-cell'>Estatus</div>"+
                "<div class='table-cell'>Grabación</div></div>";

                data.data.forEach(function(row){
                    html += "<div class='table-row'><div class='table-cell' id='fecha"+row.id+"'>"+row.fecha+"</div>"+
                    "<div class='table-cell' id='numero"+row.id+"'>"+row.numero+"</div>"+
                    "<div class='table-cell'>"+row.campana+"</div>"+
                    "<div class='table-cell'>"+row.did+"</div>"+
                    "<div class='table-cell'>"+row.CallerId+"</div>"+
                    "<div class='table-cell'>"+row.duration_wait+"</div>"+
                    "<div class='table-cell'>"+row.duration_tot+"</div>"+
                    "<div class='table-cell'>"+row.duracion+"</div>"+
                    "<div class='table-cell'>"+row.estatus+"</div>"+
                    "<div class='table-cell'>"+row.grabacion+"</div></div>\n";
                })
                mos = ((pag+REGS_POR_PAG)>data.cuenta) ? data.cuenta : pag+REGS_POR_PAG;
                $("#inireg").text(pag+1);
                $("#finreg").text(mos);
                $("#totreg").text(data.cuenta);
                $("#pagination").html(data.pagination);
                $(".d-none").addClass("parad-none").removeClass('d-none');
            }
        $("#abandono").html(html);
    },"json");
};

$(document).on("click", ".dinau", function(){
    var id  = $(this).data("id");
    var src = $(this).data("src");
    $("#escuchaudioAudio").html("");
    $("#audfecha").text("No audio");
    $("#audnumero, #audagente").text("");
    $.post(site_url+"ajax/tmpaudio", {src: $(this).data("src")}, function(data) {
        if (data == 'OK') {
            var sound      = document.createElement('audio');
            sound.id       = id;
            sound.controls = 'controls';
            sound.preload  = true;
            sound.autoplay = true;
            sound.src      = site_url+'files/'+src;
            $("#escuchaudioAudio").html(sound);
            $("#audfecha").text($("#fecha"+id).text());
            $("#audnumero").text($("#numero"+id).text());
            $("#audagente").text($("#agente"+id).text());
        } else {
            $("#escuchaudioAudio").html(data);
        }
    },"json");
});
