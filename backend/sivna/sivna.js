var fs = require('fs');
var express = require('express');
var app = express();
var https = require('https');
var options = {
    key: fs.readFileSync('/etc/letsencrypt/live/ccphonex.assertivebusiness.com.mx/privkey.pem'),
    cert: fs.readFileSync('/etc/letsencrypt/live/ccphonex.assertivebusiness.com.mx/cert.pem')
}
var server = https.createServer(options, app);
var io = require("socket.io").listen(server);
var mysql = require('mysql');
var port = process.env.PORT || 3000;


server.listen(port, function () {
    console.log('Servidor escuchando por %d', port);
});

var con = mysql.createConnection({
    host: "localhost",
    user: "aldo",
    password: "4ss3rt1v3",
    database: "assertive"
});

con.connect(function(err) {
    if (err) throw err;
    console.log("Connected to DB!");
});

app.get('/', function(req, res) {
    // collect your data and then send it as a response
    res.json(data);
});

require('./chati.js')(io, con);
require('./helpdesk.js')(io, con);
