

$(document).ready(function(){

  $('#user-submit').click(function() {
    var usuarioNombre = $('#name').val();
    var email = $('#mail').val();
    var contrasena = $('#password').val();
    var token = localStorage.getItem("Authorization"); 
    console.log(token);

    $.ajax({
     type: "POST",
     url: "http://localhost:8888/SitiosVisitados/public/index.php/api/crearuser",
     headers: {
      Authorization: token
    },

    data: {
     usuarioNombre: usuarioNombre,
     email: email,
     contrasena: contrasena
   },

   success: function(data, text, done){
    console.log(data);
            },

            error: function(data, text, done){
              console.log(data.responseText);
              console.log("Hola");
            }

          });
  });

  return false;
});
