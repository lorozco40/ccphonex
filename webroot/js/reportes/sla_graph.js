var chartcombo, chart2, chartbar;
$(document).ready(function() {
    google.charts.load('current', {'packages':['corechart']});
    google.charts.load('current', {'packages':['bar']});
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

$(document).on("change", "#min, #max, #tipo, #campana", function() {
    traerdatos();
});


function traerdatos() {
    $("#combochart").html("<tr><td class='text-center'><i class='fas fa-spinner fa-2x fa-spin'></i><span class='sr-only'>Cargando ...</span></td></tr>");
    $("#piechart, #barchart").html("");
    $.post(site_url+"sl/slingrafdata", {min: $("#min").val(), max: $("#max").val(), tipo: $("#tipo option:selected").val(),
        campana: $("#campana option:selected").val()}, function(data) {
        if (data.data.length == '' || data.data.length == null) {
            $('#combochart').html("<tr><td style='color: #9a8a14;'>Sin datos con esos parámetros.</td></tr>");
        } else {
            google.charts.setOnLoadCallback(function() {drawChartsCombo(data) });
            google.charts.setOnLoadCallback(function() {drawChart2(data) });
            google.charts.setOnLoadCallback(function() {drawChartBar(data) });
        }
    },"json");
};

function drawChartsCombo(infor) {
    datos = [['Hora', 'Recibidas', 'Atendidas', 'Abandonadas']];
    infor.data.forEach(function(row){
        datos.push([row.hora, parseInt(row.llamadas), parseInt(row.atendidas), parseInt(row.abandonadas)]);
    });

    var combodata = google.visualization.arrayToDataTable(datos);

    var view = new google.visualization.DataView (combodata);
    view.setColumns([0,
        1,{calc: "stringify", sourceColumn: 1, type: "string", role: "annotation" },
        2,{calc: "stringify", sourceColumn: 2, type: "string", role: "annotation" },
        3,{calc: "stringify", sourceColumn: 3, type: "string", role: "annotation" },
    ]);

    var combooptions = {
        height: 450,
        chartArea: {width: '80%'},
        legend:{position: 'bottom'},
        seriesType: 'bars',

        series: {0: {color: '#15a8c0'}, 1: {color: '#9451ec'}, 2: {color: '#aece35'}},
        hAxis: {title: 'Cada 30 minutos', minValue: 0, titleTextStyle:{bold:true}},
        vAxis: {title: 'Llamadas', titleTextStyle:{bold:true}}
    };

    var combochart = document.getElementById('combochart');
    chartcombo = new google.visualization.ComboChart(combochart);

    google.visualization.events.addListener(chartcombo, 'ready', function() {
        combochart.innerHTML = '<img id="chartcombo" src="'+chartcombo.getImageURI()+'">';
    });

    chartcombo.draw(view, combooptions);
}

function drawChart2(infor) {
    var bardata = google.visualization.arrayToDataTable([
        ["", "Segundos", { role: "style" } ],
        ['SL('+infor.tot[0].seg+'seg)', parseFloat(infor.tot[0].tsf), "#3366cc"],
        ['ABA', parseFloat(infor.tot[0].aba), "#dc3912"]
    ]);

    var barview = new google.visualization.DataView(bardata);

    barview.setColumns([0, 1,
        { calc: "stringify",
            sourceColumn: 1,
            type: "string",
            role: "annotation"
        },
        2]
    );

    var baroptions = {
        height: 250,
        title: 'Total en porcentaje',
        bars: 'horizontal',
        legend: { position: "none" },
    };

    var barchart = document.getElementById('piechart');
    chart2 = new google.visualization.BarChart(barchart);

    google.visualization.events.addListener(chart2, 'ready', function() {
        barchart.innerHTML = '<img id="chartbar" src="'+chart2.getImageURI()+'">';
    });

    chart2.draw(barview, baroptions);
}

function drawChartBar(infor) {
    var bardata = google.visualization.arrayToDataTable([
        ["", "Segundos", { role: "style" } ],
        ['ASA', timeStringToNumber(infor.tot[0].asa), "#f9780a"],
        ['AHT', timeStringToNumber(infor.tot[0].tat), "#856404"]
    ]);

    var barview = new google.visualization.DataView(bardata);

    barview.setColumns([0, 1,
        { calc: "stringify",
            sourceColumn: 1,
            type: "string",
            role: "annotation"
        },
        2]
    );

    var baroptions = {
        height: 250,
        title: 'Tiempo total en segundos',
        bars: 'horizontal',
        legend: { position: "none" },
    };

    var barchart = document.getElementById('barchart');
    chartbar = new google.visualization.BarChart(barchart);

    google.visualization.events.addListener(chartbar, 'ready', function() {
        barchart.innerHTML = '<img id="chartbar" src="'+chartbar.getImageURI()+'">';
    });

    chartbar.draw(barview, baroptions);
}

function timeStringToNumber(time) {
    var hoursMinutes = time.split(/[.:]/);
    return (parseInt(hoursMinutes[0])*60*60)+(parseInt(hoursMinutes[1])*60)+parseInt(hoursMinutes[2]);
}

$(document).on("click", "#pdfbtn", function(e){
    e.preventDefault();
    var pdf = new jsPDF({
        orientation: 'portrait'
    })

    pdf.setLineWidth(1.0);
    pdf.line(20, 28, 189, 28);
    pdf.line(20, 268, 189, 268);

    pdf.setFontSize(30);
    pdf.text(45, 23, 'Nivel de servicio Inbound');

    pdf.addImage(chartcombo.getImageURI(), 0, 30, 230, 0);
    pdf.addImage(chart2.getImageURI(), 48, 135);
    pdf.addImage(chartbar.getImageURI(), 47, 195);
    pdf.save('sl_'+Date.now()+'.pdf');
});
