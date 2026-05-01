$(document).ready(function() {
    datosdespachadorindi();
});

$(document).on("change", "#id_desp", function() {
    datosdespachadorindi();
});

function datosdespachadorindi() {
    $("#totales").html("<tr><td class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    $("#tipi").html("");
    $("#estatus-agendado-totales").html("");
    if ($("#id_desp").val() == '' || $("#id_desp").val() == null) {
        $("#totales").html("<tr><td>Sin datos con esos parámetros.</td></tr>");
    } else {
        $.post(site_url+"despachador/reporte_indicador_data", {id_desp: $("#id_desp").val()}, function(data) {
            let indicadores = data.indicador
            let estatus_agendado_totales = data.estatus_agendado_totales
            if( indicadores != false ){
                var filas = "<div class='table table-striped'><div class=table-header-group>"+
                "<div class='table-cell'>No</div><div class='table-cell'>Descripción</div><div class='table-cell'>Totales</div>"+
                "<div class='table-row'><div class='table-cell'>1</div><div class='table-cell'>Total de registros</div><div class='table-cell'>"+indicadores.data.totreg+"</div></div>"+
                "<div class='table-row'><div class='table-cell'>2</div><div class='table-cell'>Cerrados (finalizados)</div><div class='table-cell'>"+indicadores.data.cerradas+"</div></div>"+
                "<div class='table-row'><div class='table-cell'>3</div><div class='table-cell'>Nuevos (sin tocar)</div><div class='table-cell'>"+indicadores.data.sintocar+"</div></div>"+
                "<div class='table-row'><div class='table-cell'>4</div><div class='table-cell'>Registros desplegados</div><div class='table-cell'>"+indicadores.data.despliegues+"</div></div>"+
                "<div class='table-row'><div class='table-cell'>5</div><div class='table-cell'>Parcialmente actualizados</div><div class='table-cell'>"+indicadores.data.parcial+"</div></div>"+
                "<div class='table-row'><div class='table-cell'>6</div><div class='table-cell'>Abiertos</div><div class='table-cell'>"+indicadores.data.abiertas+"</div></div>\n";

                $("#totales").html(filas);

                if (indicadores.tipi.length == '' || indicadores.tipi.length == null) {
                    $("#tipi").html(html2);
                } else {
                    html2 = "<div class='table table-striped'><div class='table-header-group'>"+
                        "<div class='table-cell'>Tipificación</div><div class='table-cell'>Abierto</div>"+
                        "<div class='table-cell'>Cerrados</div><div class='table-cell'>Total</div></div>";

                        indicadores.tipi.forEach(function(row) {
                            qualif = row.qualif;
                            if (row.qualif == '') qualif = 'Sin calificar';
                            html2 += "<div class='table-row'><div class='table-cell'>"+qualif+"</div>"+
                                "<div class='table-cell'>"+row.abierto+"</div>"+
                                "<div class='table-cell'>"+row.cerrado+"</div>"+
                                "<div class='table-cell'>"+row.total+"</div></div>\n";
                            });

                            html2 += "<div class='table-row' style='background-color: #2b2b2b; color: #ffffff; font-weight: bold;'>"+
                            "<div class='table-cell'>Total</div>"+
                            "<div class='table-cell'>"+indicadores.data.abiertas+"</div>"+
                            "<div class='table-cell'>"+indicadores.data.cerradas+"</div>"+
                            "<div class='table-cell'>"+indicadores.data.totreg+"</div></div>";

                        $("#tipi").html(html2);
                }
            }
            let html = "";
            if( estatus_agendado_totales != false ){
                let registros = estatus_agendado_totales.cuenta
                html = `
                    <div class='table table-striped'>
                        <div class=table-header-group>
                            <div class='table-cell'>Estatus</div>
                            <div class='table-cell'>Agendado</div>
                            <div class='table-cell'>No Agendado</div>
                            <div class='table-cell'>Totales</div>
                        </div>`;
                        let total_agendado = total_no_agendado = total = 0;
                        if( registros > 0 ){
                            let data = estatus_agendado_totales.data;
                            data.forEach(function(row) {
                                let total_fila = parseInt(row.agendada) + parseInt(row.no_agendada);
                                total_agendado = parseInt(row.agendada) + total_agendado;
                                total_no_agendado = parseInt(row.no_agendada) + total_no_agendado;
                                total = total + total_fila;
                                html += `
                                <div class='table-row'>
                                    <div class='table-cell'>`+row.estatus+`</div>
                                    <div class='table-cell'>`+row.agendada+`</div>
                                    <div class='table-cell'>`+row.no_agendada+`</div>
                                    <div class='table-cell'>`+total_fila+`</div>
                                </div>
                                `;
                            })
                        }else{
                            html += `
                                <div class='table-row'>
                                    <div class='table-cell'>Abiertas</div>
                                    <div class='table-cell'>0</div>
                                    <div class='table-cell'>0</div>
                                    <div class='table-cell'>0</div>
                                </div>
                                <div class='table-row'>
                                    <div class='table-cell'>Cerradas</div>
                                    <div class='table-cell'>0</div>
                                    <div class='table-cell'>0</div>
                                    <div class='table-cell'>0</div>
                                </div>
                            `;
                        }
                        html += `<div class='table-row' style='background-color: #2b2b2b; color: #ffffff; font-weight: bold;'>
                                    <div class='table-cell'>TOTAL</div>
                                    <div class='table-cell'>`+total_agendado+`</div>
                                    <div class='table-cell'>`+total_no_agendado+`</div>
                                    <div class='table-cell'>`+total+`</div>
                                </div>`;
                html += `</div>`;
            }
            $("#estatus-agendado-totales").html(html);
        }, "json");
    }
};
