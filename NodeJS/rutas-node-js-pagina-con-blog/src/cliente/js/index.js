require("../css/style.css");
const axios = require("axios");

//Inicializa carousel
$(".carousel").carousel({
  interval: 2000
});

/**Control scroll */
$(window).scroll(function() {
  var scroll = $(window).scrollTop();
  try {
    if (scroll > 400 && scroll > $("#id-menus").offset().top - 200) {
      document
        .getElementById("id-menus")
        .classList.add("nav-scroll-contenido", "animated", "bounceInDown");
    } else {
      document
        .getElementById("id-menus")
        .classList.remove("nav-scroll-contenido", "animated", "bounceInDown");
    }
  } catch (x) {}
});

//Contacto
document
  .getElementById("idEnviarMensaje")
  .addEventListener("click", function() {
    let strNombre = document.getElementById("nombre").value;
    let strCorreo = document.getElementById("Correo").value;
    let strMensaje = document.getElementById("mensaje").value;
    if (strCorreo != "" && strNombre != "" && strMensaje != "") {
      let datos = {
        c: strCorreo,
        n: strNombre,
        m: strMensaje
      };
      axios
        .post("/api/contacto", datos)
        .then(function(response) {
          document.getElementById("nombre").value = "";
          document.getElementById("Correo").value = "";
          document.getElementById("mensaje").value = "";
          alert("Gracias por escribirnos, en breve te contactaremos");
        })
        .catch(function(error) {
          console.log(error);
        });
    } else {
      alert("Por fovor rellenar todos los campos");
    }
  });
