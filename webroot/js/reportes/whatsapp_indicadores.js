$(document).ready(function(){
    actualiza_select_encuesta();
    $('.datepicker').datepicker({
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setpiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'],
        dateFormat: agente.FormatoFechaJs,
        autoHide: true,
        changeYear: true,
        changeMonth: true,
        onSelect: function(){
            cargar();
        }
    });
    $(document).on("change", "#cuenta", function(){
        $("#repo").html('');
        actualiza_select_encuesta();
    });
    $(".buscador")
    var images = [];
})

function actualiza_select_encuesta() {
    $("#spinnerModal").modal("show");
    id_cuenta = $("#cuenta").val();
    $("#encuesta").html('');
    $("#encuesta").val('');
    let options = "options += `<option value='0'>-Seleccione una encuesta-</option>`;";
    $.post(site_url+"whatsapp/get_rate", {id_cuenta: id_cuenta}, function(data){
        $("#spinnerModal").modal("hide");
        if (data == false) {
            options += `<option value="0" disabled>No hay encuestas</option>`;
        } else {
            data.forEach(function(row){
                options += `<option value="${row.id}">${row.name}</option>`;
            });
        }
        $("#encuesta").html(options);
    },"json");
}

function cargar() {
    $("#spinnerModal").modal("show");
    let data = {
        id_cuenta: $("#cuenta").val(),
        id_encuesta: $("#encuesta").val(),
        max: $("#max").val(),
        min: $("#min").val()
    };
    images = [];
    $.post(site_url+"whatsapp/encuesta_indicador_data", data, function(resp){
        $("#spinnerModal").modal("hide");
        let html = '';
        if( resp.n == 0 ) {
            html = `<div>${resp.msg}</div>`;
            $("#reporte_ind").html(html);
        } else {
            html = `<h5>Encuestas contestadas: <strong>${resp.n}</strong></h5><hr/><br/>`;
            resp.q.map( (row) =>{
                html += `
                    <div class="row my-5">
                        <div class="col-12 col-lg-7 col-xl-8 text-center text-lg-left order-1 order-lg-2">
                            <div class="wa-ind-card-pie d-inline-block" id="graphic_${row.id}">Grafica</div>
                        </div>
                        <div class="col-12 col-lg-5 col-xl-4 order-2 order-lg-1">
                            <div class="table table-striped">
                                ${row.pregunta}`;
                                row.ans.map( (item, index) => {
                                    let perc_text = (index == 0) ? '%' : parseFloat((item[1] / row.ans_tot * 100).toFixed(1));
                                    html += `
                                    <div class="table-row">
                                        <div class="table-cell">${item[0]}</div>
                                        <div class="table-cell">${item[1]}</div>
                                        <div class="table-cell">${perc_text}</div>
                                    </div>`;
                                });
                                html += `
                            </div>
                        </div>
                    </div>
                    <hr/>`;
            });
            $("#reporte_ind").html(html);
            resp.q.map(
                (row) => {
                    let id =  'graphic_'+row.id;
                    google.charts.setOnLoadCallback(
                        function() {drawChartPie(id, row.ans, row.pregunta) }
                    );
                }
            );
        }
    },"json");
}

$(document).ready(function() {
    google.charts.load('current', {'packages':['corechart']});
});

function drawChartPie(id, preData, title='') {
    let piechart = document.getElementById(id);
    let data = google.visualization.arrayToDataTable(preData);
    const options = {
        height: 350,
        title: title
    };
    chartpie = new google.visualization.PieChart(piechart);
    images.push(chartpie);
    google.visualization.events.addListener(chartpie, 'ready', function() {
        piechart.innerHTML = '<img id="chartpie" src="'+chartpie.getImageURI()+'">';
    });
    chartpie.draw(data, options);
}

$(document).on("click", "#pdfbtn", function(e){
    e.preventDefault();
    let number_img = 0;
    var pdf = new jsPDF({
        orientation: 'portrait',
        format:      'letter'
    })
    pdf.setLineWidth(1.0);
    pdf.line(15, 28, 200, 28);
    pdf.setFontSize(30);
    pdf.text(35, 23, 'Encuesta whatsapp indicadores');
    images.map(
        (item) => {
            number_img++;
            if( number_img == 5 ) {
                number_img = 1;
                pdf.addPage('letter', 'portrait')
            }
            switch( number_img ) {
                case 1: pdf.addImage(item.getImageURI(), 'PNG', 0,   30,  0, 85); break;
                case 2: pdf.addImage(item.getImageURI(), 'PNG', 100, 30,  0, 85); break;
                case 3: pdf.addImage(item.getImageURI(), 'PNG', 0,   150, 0, 85); break;
                case 4: pdf.addImage(item.getImageURI(), 'PNG', 100, 150, 0, 85); break;
            }
        }
    );
    pdf.save('whatsapp-indicadores.pdf');
});