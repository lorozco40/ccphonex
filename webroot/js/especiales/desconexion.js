var pag = $("#pag").val();

$(document).ready(function() {
    pag = 0;
    traerdatos();
    $('.datepicker').datepicker({
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setpiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        dateFormat: agente.FormatoFechaJs,
        autoHide: true,
        changeYear: true,
        changeMonth: true,
    });
});

$(document).on("change", "#min, #max, #campana", function() {
    pag = 0;
    traerdatos();
});

$(document).on("click", ".page-link", function(e) {
    e.preventDefault();
    pag =($(this).data("ci-pagination-page")*REGS_POR_PAG)-REGS_POR_PAG;
    traerdatos();
});

function traerdatos() {
    $("#desconec").html("<tr><td colspan='13' class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    $(".parad-none").addClass("d-none").removeClass('parad-none');
    $.post(site_url+"desconexion/data", {min: $("#min").val(), max: $("#max").val(), campana: $("#campana").val(), page: pag}, function(data) {
        var html = "<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>";
        if (data.data.length == '' || data.data.length == null) {
            $("#desconec").html(html);
        } else {
            html = "<div class='table table-striped'><div class='table-header-group'>"+
            "<div class='table-cell'>Call id</div><div class='table-cell'>Call date</div><div class='table-cell'>Call time</div>"+
            "<div class='table-cell'>Call waiting time</div><div class='table-cell'>Call duration</div><div class='table-cell'>Phone</div>"+
            "<div class='table-cell'>Agent email</div><div class='table-cell'>Agent name</div><div class='table-cell'>Agent working total hours</div>"+
            "<div class='table-cell'>Call direction</div><div class='table-cell'>Disconnected by</div><div class='table-cell'>Call type</div>"+
            "<div class='table-cell'>County</div></div>";

            data.data.forEach(function(row){
                html += "<div class='table-row'>"+
                "<div class='table-cell'>"+row.call_id+"</div>"+
                "<div class='table-cell'>"+row.call_date+"</div>"+
                "<div class='table-cell'>"+row.call_time+"</div>"+
                "<div class='table-cell'>"+row.call_waiting_time+"</div>"+
                "<div class='table-cell'>"+row.call_duration+"</div>"+
                "<div class='table-cell'>"+row.phone+"</div>"+
                "<div class='table-cell'>"+row.agent_email+"</div>"+
                "<div class='table-cell'>"+row.agent_name+"</div>"+
                "<div class='table-cell'>"+row.agent_working_total_hours+"</div>"+
                "<div class='table-cell'>"+row.call_direction+"</div>"+
                "<div class='table-cell'>"+row.disconnected_by+"</div>"+
                "<div class='table-cell'>"+row.call_type+"</div>"+
                "<div class='table-cell'>"+row.country+"</div></div>\n";
            });

            mos = ((pag+REGS_POR_PAG)>data.cuenta) ? data.cuenta : pag+REGS_POR_PAG;
            $("#inireg").text(pag+1);
            $("#finreg").text(mos);
            $("#totreg").text(data.cuenta);
            $("#pagination").html(data.pagination);
            $(".d-none").addClass("parad-none").removeClass('d-none');
        }
        $("#desconec").html(html);
    },"json");
};
