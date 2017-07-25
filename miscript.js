$(function(){
 $("#obtenervalores").click(function(){
    $("#response-container").html("<p>Buscando...</p>");
 var url = "precios.php"; // El script a dónde se realizará la petición.

    $.ajax({
           type: "POST",
           url: url,
           data: $("#datos").serialize(), // Adjuntar los campos del formulario enviado.
           success: function(data)
           {
               $("#response-container").html(data); // Mostrar la respuestas del script PHP.
           }
         });

    return false; // Evitar ejecutar el submit del formulario.
 });
});