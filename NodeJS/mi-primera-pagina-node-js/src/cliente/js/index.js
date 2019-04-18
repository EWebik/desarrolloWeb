const axios = require("axios");
require('../css/keyframes.css');
require('../css/banner.css');
require('../css/style.css');

AOS.init();

document.getElementById("idBtnEnviar").addEventListener("click",function(){
    let strCorreo = document.getElementById("idInCE").value;
    let strNombre = document.getElementById("idInNom").value;
    let strMensaje = document.getElementById("idInMen").value;

    if(strCorreo != "" && strNombre != "" && strMensaje != ""){
        let datos ={
            c: strCorreo,
            n: strNombre,
            m:strMensaje
        };
        axios.post('/api/contacto',datos)
        .then(function(response){
            document.getElementById("idInCE").value="";
            document.getElementById("idInNom").value="";
            document.getElementById("idInMen").value="";
            alert("Gracias por escribirnos, en breve te contactaremos");
        }).catch(function(error){
            console.log(error);
        });
    }else{
        alert("Por fovor rellenar todos los campos");
    }

});