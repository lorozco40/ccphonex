let powsockfun;
let wsUsers = {};
const powsockmanagemsg = msg => {
    switch (msg.a) {
        case 'achat': // Assertive Chat flow
            achat.procesamsg(msg);
            break;
        case 'bas':
            agente.perci = msg.perm;
            wsUsers = msg.rel;
            chatiman.update_users();
            break;
        case 'chat':
            chatiman.getChat(msg);
            break;
        case 'updusr':
            wsUsers[msg.id] = msg.user;
            chatiman.update_users();
            break;
        case 'ticket':
            formsection.setTickets(msg.ents);
            break;
        case 'noti':
            toastmsg(msg.msg, msg.tipo);
            break;
        default:
            console.log("Mensaje de tipo desconocido recibido");
    }
}
const powsockcon = () => {
    powsock = new WebSocket("wss://" + bago_url + "/ws");
    powsockfun = {
        registro: () => {
            powsockfun.send({a: 'reg', id: agente.id})
        },
        send: msg => {
            msg = JSON.stringify(msg);
            // console.log("Sale mensaje ws:"); // Debug
            // console.log(msg); // Debug
            powsock.send(msg);
        },
    }

    powsock.onopen = () => {
        // console.log("Powsock conectado"); // Debug
        if (typeof agente.id != 'undefined') powsockfun.registro();
    };

    powsock.onmessage = e => {
        // console.log("Mensaje recibido:"); // Debug
        // console.log(e.data); // Debug
        data = JSON.parse(e.data);
        if (data.a == 'achat') {
            achat.procesamsg(data);
        } else {
            powsockmanagemsg(data);
        }
    };

    powsock.onclose = () => {
        console.log("Powsock cerrado, Reconectando en 5 segundos ...");
        setTimeout(() => {
            powsockcon();
        }, 5000);
    };

    powsock.onerror = error => {
        console.error(error);
    };
}

powsockcon();
