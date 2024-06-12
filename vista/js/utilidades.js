document.addEventListener("DOMContentLoaded", function () {
    if (window.location.href.indexOf('login.html') === -1) {
        let datosUsuario = sessionStorage.getItem('usuario')
        if(typeof datosUsuario === 'undefined' || datosUsuario === null){
            window.location.href = "login.html";
        }
    }
});

async function consultar(tabla,datos) {
    try {

        let envioDatos = {
			datos: JSON.stringify(datos)
		};

        let url = 'http://localhost/comercializadora/controlador/' + tabla + '.php'
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams(envioDatos)
        });
        const data = await response.json();
		console.log(data)
		return data;
    } catch (error) {
        console.error('Error:', error);
    }
}

function mostrarNotificacion(texto,color) {
    var notificacion = Toastify({
      text: texto,
      duration: 3000,
      gravity: "top-right",
      close: true,
      backgroundColor: color
    });
  
    notificacion.showToast();
}

function formatDateString(dateString) {
    // Crea un objeto Date a partir de la cadena de fecha
    const date = new Date(dateString);
    
    if (isNaN(date.getTime())) {
        return "Fecha no válida"; // Maneja casos en los que la cadena de fecha no es válida
    }
    
    const day = String(date.getDate()).padStart(2, '0'); // Obtener el día y agregar ceros a la izquierda si es necesario
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Obtener el mes (los meses comienzan desde 0) y agregar ceros a la izquierda si es necesario
    const year = date.getFullYear(); // Obtener el año

    return `${day}-${month}-${year}`;
}