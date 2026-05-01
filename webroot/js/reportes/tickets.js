var qfields = {
    'Ticket': 't.id',
    'Persona que reporta': 'c.name',
    'Area de la persona que reporta': 't.area_que_reporta',
    'Ingeniero responsable': 't.ing_resp',
    'Serie': 't.no_serie',
    'Marca': 't.marca',
    'Modelo': 't.modelo',
    'Ubicación': 't.ubicacion',
    'Descripción': 't.detalle',
    'Fecha apertura': 't.creacion',
    'Hora apertura': 'no',
    'Fecha llegada': 't.llegada',
    'Hora llegada': 'no',
    'Tiempo de arribo': 'no',
    'Atención en sitio': 't.atte_sitio',
    'Acciones realizadas': 't.acciones',
    'Fecha cierre': 't.termino',
    'Hora cierre': 'no',
    'Estatus': 't.estatus'
}
var orden = 't.id';
var ordendir = ' ASC';

$(document).ready(function() {
    $('.datepicker').datepicker({
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setpiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        dateFormat: agente.FormatoFechaJs,
        autoHide: true,
        changeYear: true,
        changeMonth: true,
    });
    $(document).on("change", "#min, #max, .main select", function() {
        $("#pag").val('0');
        traerdatos();
    });
    $(document).on("click", ".page-link", function(e) {
        e.preventDefault();
        $("#pag").val(($(this).data("ci-pagination-page")*REGS_POR_PAG)-REGS_POR_PAG);
        traerdatos();
    });
    $(document).on("click", ".ordenador", function(){
        $("#pag").val('0');
        estacol = $(this).data('col');
        ordendir = (estacol != orden || ordendir == ' DESC') ? ' ASC' : ' DESC';
        orden = estacol;
        $("#orden").val(estacol + ordendir);
        traerdatos();
    });
    $(document).on("click", "#btnbus", function(e){
        e.preventDefault();
        $("#pag").val('0');
        traerdatos();
    });
    $(document).on("keypress", "#tid", function(e) {
        var keycode = (e.keyCode ? e.keyCode : (e.which ? e.which : e.key));
        if(keycode == 13) {
            e.preventDefault();
            $("#pag").val('0');
            traerdatos();
        }
    });
});

function traerdatos() {
    $("#repo").html("<tr><td class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    $(".parad-none").addClass("d-none").removeClass('parad-none');
    ordenpedir = orden + ordendir;
    formdata = (ordenpedir == 't.id ASC') ? $("#repoform").serialize() : $("#repoform").serialize() + '&orden=' + ordenpedir;
    $.post(site_url+"reportes/data_aicm", formdata, function(data) {
        var html = "<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>";
        if (data.error) {
            toastmsg(data.error, "danger");
        } else if (!data.data || data.data.length == '' || data.data.length == null) {
        } else {
            var html = "<div class='table table-striped'><div class='table-header-group'>";
            data.campos.forEach(function(row){
                clase = ('undefined' !== typeof qfields[row] && qfields[row] != 'no') ? ' ordenador' : '';
                datacol = (clase != '') ? " data-col='"+qfields[row]+"'" : "";
                html += "<div class='table-cell"+clase+"'"+datacol+">"+row+"</div>\n";
            });
            html += "</div>";
            data.data.forEach(function(row) {
                html += "<div class='table-row'>\n";
                data.campos.forEach(function(row2){
                    html += "<div class='table-cell'>"+row[row2]+"</div>\n";
                })
                html += "</div>\n";
            });
            if(data.tot) {
                html += "<div class='table-row total'>\n";
                data.campos.forEach(function(row3){
                    html += "<div class='table-cell'>"+data.tot[0][row3]+"</div>\n";
                })
                html += "</div>\n";
            }

            mos = ((parseInt(data.pag)+REGS_POR_PAG)>data.cuenta) ? data.cuenta : parseInt(data.pag)+REGS_POR_PAG;
            $("#inireg").text(parseInt(data.pag)+1);
            $("#finreg").text(mos);
            $("#totreg").text(data.cuenta);
            $("#pagination").html(data.pagination);
            $(".d-none").addClass("parad-none").removeClass('d-none');
            $("#tid").val();
        }
        $("#repo").html(html);
        nuclas = (ordendir == ' ASC') ? 'ordasc' : 'orddesc';
        $(".ordenador[data-col='"+orden+"']").addClass(nuclas);
    }, "json");
}
