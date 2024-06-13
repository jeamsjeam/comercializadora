document.addEventListener("DOMContentLoaded", function () {
    if (window.location.href.indexOf('login.html') === -1 && 
        window.location.href.indexOf('registro.html') === -1 &&
        window.location.href.indexOf('recuperacion.html') === -1) {
        let datosUsuario = sessionStorage.getItem('usuario')
        if(typeof datosUsuario === 'undefined' || datosUsuario === null){
            window.location.href = "login.html";
        }
        if(window.location.href.indexOf('index.html') !== -1){
            let usuarioLogeado = JSON.parse(localStorage.getItem('usuarioLogeado'))
            if(typeof usuarioLogeado !== 'undefined' &&  usuarioLogeado !== null && typeof usuarioLogeado.usuario !== 'undefined' && usuarioLogeado.usuario !== null){
                mostrarNotificacion("Usuario " + usuarioLogeado.usuario + " logeado!","linear-gradient(to right, #00b09b, #96c93d)") 
            }
        }
        localStorage.removeItem('usuarioLogeado');
    }else{
        sessionStorage.removeItem('usuario');
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

async function ObtenerRoles(tabla, idSelect, error) {
	try{
		let datos = {
			accion: "obtenerTodos"
		};

		let rolesSelect = document.getElementById(idSelect);

		let data = await consultar(tabla,datos);
		if(data !== null && typeof data !== 'undefined'){
			if (data.message) {
				mostrarNotificacion(data.message,"#FF0000") 
			} else if (data.error) {
				mostrarNotificacion(data.error,"#FF0000") 
			} else {
				data.forEach(role => {
					// Creamos una opción para cada rol
					let option = document.createElement("option");
					option.value = role.id;
					option.textContent = role.nombre;
					rolesSelect.appendChild(option);
				});
			}
		}else{
			mostrarNotificacion("No se encontro ningun " + error,"#FF0000") 
		}
	}catch(e){
		mostrarNotificacion("Error:", e,"#FF0000") 
		console.error('Error:', e);
	}
}