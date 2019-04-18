const mysql = require("mysql");
class Contacto{
    constructor(oConfig){
        this.oConfig = oConfig;
    }

    /***
     * Recibe un correo y nombre y si no existe en la base de datos lo inserta
     */
    agregarUsuario(nombre, correo){
        var con = mysql.createConnection(this.oConfig);
        con.connect(function(error){
            try{
                if(error){
                    console.log("Error al establecer la conexión a la BD -- " + error);
                }else{
                    console.log("Conexión exitosa");
                    con.query(`SELECT COUNT(*) AS usuario FROM USUARIOS WHERE correo = '${correo}'`,function(error, res, campo){
                        if(error){
                            console.log("Error en select BD -- " + console.error());
                        }else{
                            console.log("Usuarios encontrados: " + res[0].usuario);
                            if(parseInt(res[0].usuario) == 0){
                                con.query(`INSERT INTO usuarios (nombre, correo) values('${nombre}','${correo}')`
                                ,function(error, res, campo){
                                    if(error){
                                        console.log("Error al insertar nuevo usuario en BD -- " + console.error());
                                    }else{
                                        console.log("Nuevo usuario registrado");
                                    }
                                });
                            }
                        }
                    });
                }
            }catch(x){
                console.log("Contacto.agregarUsuario.connect --Error-- " + x);
            }
        });
    }
}
module.exports = Contacto;