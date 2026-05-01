var chiusers = {};

module.exports = function(io, con) {
    io.on('connection', function (socket) {
        actualiza_usuarios();
        console.log(getFecha()+' usuario conectado en '+socket.id);

        socket.on('subscribe', function(data) {
            actualiza_usuarios();
            if(chiusers[data.uid]) {
                chiusers[data.uid].sid = socket.id;
                io.emit('users_list', chiusers);
                console.log('usuario suscrito ' + JSON.stringify(chiusers[data.uid]));
                sql = "SELECT * from chatinterno_entry where id_usuario_recibe='" +
                data.uid + "' AND estatus = 'Enviado'";
                con.query(sql, function (err, result) {
                    if (err) throw err;
                    if (result.length > 0 ) {
                        socket.emit('new_msg', result);
                    }
                });
            } else {
                chiusers[data.uid] = {uid:data.uid, sid:'',nombre:'',perfil:'agente',permisos:'0,0,0,0,0',activo:'1'};
            }
        });

        socket.on('user_select', function(data) {
            sql = "UPDATE chatinterno_entry SET fecha_lectura=NOW(), estatus='Leido' WHERE estatus='Enviado' AND id_usuario_emite='"+
                data.to+"' AND id_usuario_recibe='"+data.from+"'";
            con.query(sql, function (err, result) {
                if (err) throw err;
                console.log("Mensajes actualizados " + JSON.stringify(result));
            });
            if( data.to == '0' ) {
                sql = "SELECT * from (SELECT id, id_usuario_emite, id_usuario_recibe, " +
                    "mensaje, cast(fecha_envio as char) as fecha_envio " +
                    "from chatinterno_entry where id_usuario_recibe='0' AND id > '" +
                    data.mid+"' order by id desc limit 20) t order by id";
            } else {
                sql = "SELECT * from (SELECT id, id_usuario_emite, id_usuario_recibe, mensaje, cast(fecha_envio as char) as fecha_envio from chatinterno_entry where ((id_usuario_emite = '" +
                    data.to + "' AND id_usuario_recibe='"+data.from+"') or (id_usuario_recibe = '" +
                    data.to + "' AND id_usuario_emite='"+data.from+"') ) AND id > '" +
                    data.mid+"' order by id desc limit 20) t order by id";
            }
            con.query(sql, function (err, result) {
                if (err) throw err;
                socket.emit('new_msg', result);
            });
        });

        socket.on('last_messages', function(data) {
            if( data.to == '0' ) {
                sql = "SELECT id, id_usuario_emite, id_usuario_recibe, mensaje, cast(fecha_envio as char) as fecha_envio from chatinterno_entry where id_usuario_recibe='0' AND id < '" +
                    data.mid+"' order by id desc limit 20 ";
            } else {
                sql = "SELECT id, id_usuario_emite, id_usuario_recibe, mensaje, cast(fecha_envio as char) as fecha_envio from chatinterno_entry where ((id_usuario_emite = '" +
                    data.to + "' AND id_usuario_recibe='"+data.from+"') or (id_usuario_recibe = '" +
                    data.to + "' AND id_usuario_emite='"+data.from+"') ) AND id < '" +
                    data.mid+"' order by id desc limit 20";
            }
            con.query(sql, function (err, result) {
                if (err) throw err;
                socket.emit('get_last_messages', result);
            });
        });
        socket.on('new_msg', function(data){
            to_permisos = (data.to == 0) ? '1,1,1,1,1' : chiusers[data.to].permisos;
            permisos_to = traduce_permisos(to_permisos);
            permisos_from = traduce_permisos(chiusers[data.from].permisos);
            var toemit = [{id: '0', mensaje: data.msg, fecha_envio: getFecha(), id_usuario_emite: data.from, id_usuario_recibe: data.to}];
            fail = false;
            var tipocast = 'to';
            if(data.to == '0') {
                tipocast = 'bc';
                if(permisos_from.emd == '0') { fail = true; }
            } else {
                if(permisos_to.rmu == '0') { fail = true; }
                if(chiusers[data.to].perfil == 'agente' && permisos_from.emu == '0') { fail = true; }
                if(chiusers[data.to].perfil != 'agente' && permisos_from.ems == '0') { fail = true; }
            }
            if(fail) {
                socket.emit('failure_msg', toemit);
                return false;
            }
            var sql="INSERT INTO chatinterno_entry (id, id_usuario_emite, id_usuario_recibe, " +
                "mensaje, fecha_envio, estatus) VALUES (0, " +
                data.from + ", " + data.to + ", '" +
                data.msg + "', '" + toemit[0].fecha_envio + "', 'Enviado')";
            con.query(sql, function (err, result) {
                if (err) throw err;
                toemit[0].id = result.insertId;
                if (tipocast == 'bc') {
                    socket.broadcast.emit('new_msg', toemit);
                } else {
                    io.sockets.to(chiusers[data.to].sid).emit('new_msg', toemit);
                }
            });
        });

        socket.on('read_msgs', function(data){
            var sql = "UPDATE chatinterno_entry SET fecha_lectura = NOW(), estatus = 'Leido' " +
                "WHERE estatus = 'Enviado' AND id_usuario_emite = '" + data.to +
                "' AND id_usuario_recibe = '" + data.from + "'";
            con.query(sql, function (err, result) {
                if (err) throw err;
            });
        });

        socket.on('new_user', function(data){
            var c1;
            var sqlc1 = "SELECT COUNT(*) AS c FROM user_data WHERE id_user = '"+data.id+
            "' AND id_catalog = (SELECT id FROM catalogs WHERE cat='userData' AND val='chatinterno')";
            con.query(sqlc1, function(err, result, fields){
                if(err) throw err;
                c1 = parseInt(result[0]["c"]);
                if( c1 > 0 ) {
                    sqlu = "UPDATE user_data SET val = '"+data.data+"' WHERE id_user = '"+data.id+
                    "' AND id_catalog = (SELECT id FROM catalogs WHERE cat='userData' AND val='chatinterno')";
                    con.query(sqlu, function(err, result, fields){
                        if(err) throw err;
                    });
                } else {
                    sqli = "INSERT INTO user_data (id_user, id_catalog, val) VALUES ('"+data.id+
                    "',(SELECT id FROM catalogs WHERE cat='userData' AND val='chatinterno') , '"+data.data+"'); ";
                    con.query(sqli, function(err, result, fields){
                        if(err) throw err;
                    });
                }
                actualiza_usuarios();
                io.emit('users_list', chiusers);
            });
        });

        socket.on('disconnect', function(){
            actualiza_usuarios();
            io.emit('users_list', chiusers);
        });
    });

    function getFecha() {
        var d = new Date();
        fecha = d.getFullYear()+"-"+('0'+(d.getMonth() + 1)).slice(-2)+"-"+('0'+d.getDate()).slice(-2);
        hora = ('0'+d.getHours()).slice(-2)+":"+('0'+d.getMinutes()).slice(-2)+":"+('0'+d.getSeconds()).slice(-2);
        return fecha+" "+hora;
    }

    function traduce_permisos(permisos = '0,0,0,0,0') {
        permisos = ('undefined' === typeof permisos || permisos.length < 9) ? '0,0,0,0,0' : permisos;
        permisos = permisos.split(",");
        output = new Array();
        indices = new Array("pc","emd","ems","emu","rmu");
        for(h=0; h<permisos.length; h++){
            output[indices[h]]=permisos[h];
        };
        return output;
    }

    function actualiza_usuarios() {
	query = "SELECT u.id AS uid, '' AS sid, concat(u.name, ' ', u.last) AS nombre, " +
            "COALESCE(IF(ud.val='','agente',ud.val),'agente') AS perfil, " +
            "COALESCE(ud2.val,'0,0,0,0,0') AS permisos, IF(COALESCE(s.uid, '0')<>'0', '1', '0') AS activo, " +
            "ud3.val AS campanas " +
            "FROM user u " +
            "LEFT JOIN user_data ud on ud.id_user = u.id " +
            "LEFT JOIN catalogs c on c.id = ud.id_catalog " +
            "LEFT JOIN user_data ud2 on ud2.id_user = u.id " +
            "LEFT JOIN catalogs c2 on c2.id = ud2.id_catalog " +
            "LEFT JOIN ses_ab s on s.uid = u.id " +
            "LEFT JOIN user_data ud3 on ud3.id_user = u.id " +
            "LEFT JOIN catalogs c3 on c3.id = ud3.id_catalog " +
            "WHERE u.id>1 AND u.active=1 AND c.cat = 'userData' AND c.val = 'perfil' " +
            "AND c2.cat = 'userData' AND c2.val = 'chatinterno' " +
            "AND c3.cat = 'userData' AND c3.val = 'campanas' " +
            "ORDER by activo DESC, perfil, u.name, u.last";
        con.query(query, function(err, result){
            if(err) throw err;
            newUsers = {};
            result.forEach((item,i)=>{
                newUsers[item.uid] = item;
                newUsers[item.uid].sid = (undefined !== chiusers[item.uid]) ? chiusers[item.uid].sid : '';

            });
            chiusers = newUsers;
        });
    }

}
