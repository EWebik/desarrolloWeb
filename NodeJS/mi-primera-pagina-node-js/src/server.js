var express = require('express');
var webpack = require('webpack');
var webpackDevMiddleware = require('webpack-dev-middleware');
var webpackConfig = require('../webpack.config');
var badyParser = require("body-parser");

const email = require("./servidor/email");
const contacto = require("./servidor/contacto");

var app = express();
app.set('port', (process.env.PORT || 3000));

app.use('/static', express.static('dist'));
app.use(badyParser.json());
app.use(badyParser.urlencoded({ extended: true }));
app.use(webpackDevMiddleware(webpack(webpackConfig)));

const oEmail = new email({
    "host":"tu-host",
    "port":"el-puerto",
    "secure":false,
    "auth":{
        "type":"login",
        "user":"tu-correo@ewebik.com.mx",
        "pass":"tu-password"
    }
});

const oContacto =  new contacto({
    host:"localhost",
    user:"user",
    password:"password",
    database:"database"
});

app.get('/', function (req, res, next) {
    res.send('EWebik');
});

app.post('/api/contacto', function (req, res, next) {
    let email ={
        from:"ewebik@ewebik.com.mx",
        to:"contacto@ewebik.com.mx",
        subject:"Nuevo mensaje de usuario",
        html:`
            <div>
            <p>Correo: ${req.body.c}</p>
            <p>Nombre: ${req.body.n}</p>
            <p>Mensaje: ${req.body.m}</p>
            </div>
        `
    };

    oContacto.agregarUsuario(req.body.n,req.body.c);

    oEmail.enviarCorreo(email);
    res.send("ok");
});

app.listen(app.get('port'), () => {
    console.log('Servidor activo 1');
})