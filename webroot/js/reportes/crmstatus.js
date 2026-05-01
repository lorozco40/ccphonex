var chartpie1, chartpie2, canvas1, canvas2;
$(document).ready(function() {
    google.charts.load('current', {'packages':['corechart']});
    traerdatos();
});
$(document).on("change", "select", function() {
    traerdatos();
});
$(document).on("click", "#pdfbtn", function(e){
    e.preventDefault();
    var pdf = new jsPDF();
    pdf.setLineWidth(1.0);
    pdf.line(15, 25, 195, 25);
    pdf.line(15, 270, 195, 270);
    pdf.setFontSize(25);
    pdf.text(20, 20, 'Help desk estatus');
    pdf.setFontSize(10);
    pdf.text(170, 20, moment().format('DD/MM/YYYY'));
    if (chartpie1 && chartpie2) {
        pdf.addImage(document.getElementById("chartpie1"), 0, 26, 150, 0);
        pdf.addImage(canvas1, 'JPEG', 140, 35, 60, 0);
    } else if (chartpie1) {
        pdf.addImage(document.getElementById("chartpie1"), 25, 26, 200, 0);
        pdf.addImage(canvas1, 'JPEG', 15, 110, 80, 0);
    }
    if (chartpie2) {
        pdf.addImage(document.getElementById("chartpie2"), 0, 135, 150, 0);
        pdf.addImage(canvas2, 'JPEG', 140, 145, 60, 0);
    }
    pdf.save('TicketStatus_'+Date.now()+'.pdf');
    // window.open(pdf.output('bloburl'));
});


function traerdatos() {
    chartpie1 = chartpie2 = null;
    $("#spinnerModal").modal("show");
    var html = '<div class="table table-striped"><div class="table-header-group">' +
        '<div class="table-cell">Status</div><div class="table-cell">#</div></div>';
    var total = 0;
    var grafdata = [['Rubro', 'Porcentaje']];

    var html2 = '<div class="table table-striped"><div class="table-header-group">' +
    '<div class="table-cell">Semáforo</div><div class="table-cell">#</div></div>';
    var total2 = 0;
    var grafdata2 = [['Rubro', 'Porcentaje']];

    $.post(site_url+"crm/crmstatus_data", {crm:$("select[name=crm]").val()}, function(data) {
        if (data.estatus.length == '' || data.estatus.length == null) {
            $('#repo').html("");
            $('#graf').html("<h4>Sin datos para mostrar</h4>");
        } else {
            data.estatus.forEach((row, key) => {
                total += parseInt(row.cuantos);
                html += '<div class="table-row"><div class="table-cell">' + row.estatus +
                '</div><div class="table-cell">' + row.cuantos + '</div></div>';
                grafdata.push([row.estatus, parseInt(row.cuantos)]);
            });
            html += '<hr><div class="table-header-group">' +
            '<div class="table-cell">Total</div><div class="table-cell">'+ total + '</div></div></div>';
            $("#repo").html(html);
            google.charts.setOnLoadCallback(function() {drawChartPie(grafdata) });
        }

        if (data.semaforo.length == '' || data.semaforo.length == null) {
            $('#repo2').html("");
            $('#graf2').html("");
        } else {
            data.semaforo.forEach((row, key) => {
                total2 += parseInt(row.cuantos);
                html2 += '<div class="table-row"><div class="table-cell">' + row.semaforo +
                '</div><div class="table-cell">' + row.cuantos + '</div></div>';
                grafdata2.push([row.semaforo, parseInt(row.cuantos)]);
            });
            html2 += '<hr><div class="table-header-group">' +
            '<div class="table-cell">Total</div><div class="table-cell">'+ total2 + '</div></div></div>';
            $("#repo2").html(html2);
            google.charts.setOnLoadCallback(function() {drawChartPie2(grafdata2) });
        }
        $("#spinnerModal").modal("hide");
    })
    .fail(function(data) {
        $("#spinnerModal").modal("hide");
        if (typeof data.responseJSON.error !== "undefined") {
            toastmsg(data.responseJSON.error, "danger");
        } else {
            toastmsg("Error de red, verifica tu conexión a internet.", "danger");
        }
    });
}

function drawChartPie(infor) {
    var piedata = google.visualization.arrayToDataTable(infor);

    var pieoptions = {
        height: 300,
        title: 'Totales por estatus'
    };

    var piechart = document.getElementById('graf');
    chartpie1 = new google.visualization.PieChart(piechart);

    google.visualization.events.addListener(chartpie1, 'ready', function() {
        piechart.innerHTML = '<img id="chartpie1" src="'+chartpie1.getImageURI()+'">';
    });

    chartpie1.draw(piedata, pieoptions);
    html2canvas(document.getElementById('repo')).then(function(canvas) {
        canvas1 = canvas;
    });
}

function drawChartPie2(infor) {
    let piedata = google.visualization.arrayToDataTable(infor);

    let colors = [];
    //Obtenemos el array de colores
    infor.forEach(row => {
        let semaforo_srt = row[0].toUpperCase();
        if( semaforo_srt == 'VERDE' )           { colors.push('#388E3C'); }
        else if( semaforo_srt == 'AMARILLO' )   { colors.push('#FBC02D'); }
        else if( semaforo_srt == 'ROJO' )       { colors.push('#D32F2F'); }
        else if( semaforo_srt == 'RUBRO' )      { }
        else                                    { colors.push('#455A64') };
    });

    let pieoptions = {
        height: 300,
        title: 'Totales por Semáforo', 
        colors: colors//['#FF0000', '#00FF00', '#0000FF', '#FFFF00']
    };

    let piechart = document.getElementById('graf2');
    chartpie2 = new google.visualization.PieChart(piechart);

    google.visualization.events.addListener(chartpie2, 'ready', function() {
        piechart.innerHTML = '<img id="chartpie2" src="'+chartpie2.getImageURI()+'">';
    });

    chartpie2.draw(piedata, pieoptions);
    html2canvas(document.getElementById('repo2')).then(function(canvas) {
        canvas2 = canvas;
    });
}
