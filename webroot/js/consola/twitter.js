var preguntartwitter = null;
var newtweet = null;

$(document).ready(function(){
    if (agente.permisoSec.includes('twitter')) {
        if (!preguntartwitter) {
            preguntartwitter = setInterval(function(){ traer_cola_tweets() }, 10000);
        }
    }
});

$(document).on("submit", "#replytwform", function(e){
    e.preventDefault();
    if ($("#twstatusid").val()!="" && $("#twusername").val()!="" && $("#twreply").val()!="" && $("#twid").val()!='') {
        $.post(site_url+'twitter/twitear', $(this).serialize(), function(res) {
            if (res.status == "1") {
                if (!preguntartwitter) {
                    preguntartwitter = setInterval(function(){ traer_cola_tweets() }, 10000);
                }
                toastmsg("Twitter enviado. "+res.msg, "success");
                $("#twitter .d-block").removeClass("d-block").addClass("d-none");
                $(".twbadge").remove();
            } else if (res.status == "error") {
                toastmsg(res.msg, "danger");
            }
        }, 'json')
        .fail(function() {
            toastmsg("Error de red, verifica tu conexión a internet.", "danger");
        });
    } else {
        toastmsg("Debes añadir contenido.", "danger");
    }
})

function poner_tweet_enhtml() {
    embedea(newtweet.empresa, newtweet.user_screen_name, newtweet.id_str);
    $("#twitter .d-none").removeClass("d-none").addClass("d-block");
    $("#twuser").html(newtweet.user_screen_name);
    $("#twid").val(newtweet.id);
    $("#twstatusid").val(newtweet.id_str);
    $("#twusername").val(newtweet.user_screen_name);
}

function traer_cola_tweets() {
    $.post(site_url+'twitter/traer_tweet', function(res) {
        if (res.status == "1") {
            clearInterval(preguntartwitter);
            preguntartwitter = null;
            toastmsg("Tienes un Tweet.", "success");
            newtweet = res.data;
            $("[href='#twitter']").append('<span class="badge twbadge">1</span>');
            poner_tweet_enhtml();
        } else if (res.status == "error") {
            toastmsg(res.msg, "danger");
        }
    }, 'json')
    .fail(function() {
        toastmsg("No se ha podido conectar con el servidor.", "danger");
    });
}

function embedea(empresa, usuario, id) {
    $("#twfrom").html("El usuario <strong class='tword'>@"+usuario+"</strong> a posteado:");
    // Refrescar tweets de la empresa
    twttr.widgets.createTimeline({
        sourceType: "profile",
        screenName: empresa
    }, document.getElementById("twtimeline"), {
        width: null,
        height: 450,
        chrome: "nofooter", // noscrollbar
        linkColor: "#1ca1f2"
    });
    // Mostrar timeline del usuario que envió el tweet
    twttr.widgets.createTimeline({
        sourceType: "profile",
        screenName: usuario
    }, document.getElementById("twusertimeline"), {
        width: null,
        height: 450,
        chrome: "nofooter",
        linkColor: "#1ca1f2"
    });
    // Tweet recibido
    twttr.widgets.createTweet(
        id,
        document.getElementById('twrecibido'), {
            theme: 'dark',
            height: null,
            width: 335,
            chrome: "nofooter",
            linkColor: "#1ca1f2"
        });
}
