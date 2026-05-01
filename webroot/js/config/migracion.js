function hard_reset() {
    if( confirm("Advertencia: Estas apunto de eliminar todos los valores de la Base de Datos. ¿Quieres continuar?") ) {
        $("#spinnerModal").modal("show");
        $.post(site_url+"migracion/hard_reset", {}, function(json) {
            if( typeof(json.error) !== 'undefined' ) {
                toastmsg(json.error, "danger");
            } else {   
                let html = '';
                html += `<div><br/>TABLAS TOTALES: ${json.n_tablas}</div>`;
                html += `<div><br/>TABLAS ELIMINADAS: ${json.n_del}</div>`;
                html += tableHtml(json.tablas_del, 7);
                html += `<div><br/>RESULTADO: ${json.n_resultado}</div>`;
                html += tableHtml(json.resultado, 6);
                html += `<div><br/>${json.fk}</div>`;
                $("#resultado").html(html);
            }
            $("#spinnerModal").modal("hide");
        },"json")
        .fail(function(data) {
            console.log(data);
            $("#spinnerModal").modal("hide");
            if (typeof data.responseJSON.error !== "undefined") {
                toastmsg(data.responseJSON.error, "danger");
            } else {
                toastmsg("Error de red, verifica tu conexión a internet.", "danger");
            }
        });
    }
}

function tableHtml(tabla, colums_rows) {
    let html = '';
    let j =  0;
    tabla.forEach(function(row) {
        j++;
        if( j == 1 ) { //Abrimos el renglon
            html += '<div class="table-row">';
        } 
        html += `<div class="table-cell">${row}</div>`;
        if( j == colums_rows ) {//Cerramos renglon
            html += '</div>';
            j = 0;
        }    
    });
    if( j != colums_rows && j != 0 ) 
        html += '</div>';

    return html;
}