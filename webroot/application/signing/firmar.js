const argv = require('minimist')(process.argv.slice(1));
const rprv = argv.rprv;
const req  = argv.req;
const rs   = require('jsrsasign');
const fs   = require('fs');
let priv   = fs.readFileSync(rprv + 'cc_key.pem','utf8');
// let pub    = fs.readFileSync(rprv + 'cc_crt.pem','utf8');

/*
Generar el las llaves publicas y privadas con openssl:
openssl ecparam -genkey -name secp384r1 -out private_kinon.key
openssl ec -in private_kinon.key -pubout -out public_kinon.key
Con ésta misma librería:
let ecKeypair = rs.KEYUTIL.generateKeypair("EC", "secp384r1");
*/

/* Si fuera con llaves reales en secp384r1
let pub  = fs.readFileSync('public_kinon.key','utf8');
let priv = fs.readFileSync('private_kinon.key','utf8');
pub  = rs.KEYUTIL.getKey(pub);
priv = rs.KEYUTIL.getKey(priv);
sig.init(pub) // para firmar
sig.init(priv) // para verificar firma */

// Firmar
let sig = new rs.KJUR.crypto.Signature({"alg": "SHA256withECDSA"});
sig.init({d: priv.trim().replace(/[\x00-\x1F\x7F-\x9F]/g, ""), curve: "secp384r1"});
sig.updateString(req);
console.log(sig.sign());

// Verficar
// var sigv = new rs.KJUR.crypto.Signature({"alg": "SHA256withECDSA"});
// sigv.init({d: pub.trim().replace(/[\x00-\x1F\x7F-\x9F]/g, ""), curve: "secp384r1"});
// sigv.updateString(req);
// console.log(sig.sign());
