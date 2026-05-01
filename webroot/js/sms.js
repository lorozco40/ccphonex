$(document).on("keyup", "#saludo, #msg, #cierre", function(){
        $("#smspreview").text($("#saludo").val()+' @nombre '+$("#msg").val()+' @dato '+$("#cierre").val());
});
$(document).on("submit", "#addform", function(e) {
    e.preventDefault();
    var data = $(this).serializefiles();
    $.ajax({
        type: 'POST',
        method: 'POST',
        cache: false,
        contentType: false,
        processData: false,
        url: site_url+'sms/subir_csv',
        data: data,
        dataType: 'json'
    })
    .done(function(data){
        if (data.status == 'error') {
        	toastmsg(data.msg, "danger");
        } else {
            location.reload();
        }
    })
    .fail(function(data) {
        toastmsg("El servidor no ha contestado, pide asistencia técnica.", "danger");
    });
});
$(document).on("submit", ".startform", function(ev) {
    ev.preventDefault();
    var form = $(this);
    form.find("button").replaceWith('<button disabled>En proceso ...</button>');
    toastmsg("Espera por favor, no recargues la página");
    $.ajax({
        type: 'POST',
        method: 'POST',
        cache: false,
        contentType: 'application/x-www-form-urlencoded',
        processData: false,
        url: form.attr('action'),
        data: form.serialize(),
        dataType: 'json'
    })
    .done(function(data){
        if (data.status == 'error') {
        	toastmsg(data.msg, "danger");
            form.find("button").replaceWith('<button class="btn btn-secondary" type="submit">Enviar</button>');
        } else {
            location.reload();
        }
    })
    .fail(function(data) {
        toastmsg("El servidor no ha contestado, pide asistencia técnica.", "danger");
        form.find("button").replaceWith('<button class="btn btn-secondary" type="submit">Enviar</button>');
    });
});

(function($) {
$.fn.serializefiles = function() {
    var obj = $(this);
    /* ADD FILE TO PARAM AJAX */
    var formData = new FormData();
    $.each($(obj).find("input[type='file']"), function(i, tag) {
        $.each($(tag)[0].files, function(i, file) {
            formData.append(tag.name, file);
        });
    });
    var params = $(obj).serializeArray();
    $.each(params, function (i, val) {
        formData.append(val.name, val.value);
    });
    return formData;
};
})(jQuery);
