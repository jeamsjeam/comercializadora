document.addEventListener('DOMContentLoaded', async function() {

	// Se comprueba en que pantalla esta, para realizar diferentes acciones
	// Como cargar el select de roles o 
	// Verificar si existe usuarioRegistrado para mostrar una notificacion en verde
	if (window.location.href.indexOf('registro.html') !== -1) {
        await ObtenerSelect("roles", "roles-select", "rol");
    }else if (window.location.href.indexOf('login.html') !== -1){
		let usuarioRegistrado = JSON.parse(localStorage.getItem('usuarioRegistrado'))
		if(typeof usuarioRegistrado !== 'undefined' &&  usuarioRegistrado !== null && typeof usuarioRegistrado.usuario !== 'undefined' && usuarioRegistrado.usuario !== null){
			mostrarNotificacion("Usuario " + usuarioRegistrado.usuario + " registrado!","linear-gradient(to right, #00b09b, #96c93d)") 
		}
	}
	localStorage.removeItem('usuarioRegistrado');
});

// Funcion que valida los campos del formulario del login y registro
(function () {
	'use strict'

	// Fetch all the forms we want to apply custom Bootstrap validation styles to
	var forms = document.querySelectorAll('.needs-validation')

	// Loop over them and prevent submission
	Array.prototype.slice.call(forms)
		.forEach(function (form) {
			form.addEventListener('submit', function (event) {
				if (!form.checkValidity()) {
					event.preventDefault()
					event.stopPropagation()
				}

				form.classList.add('was-validated')
			}, false)
		})
})()

// Funcion que verifica si existe el usuario
async function verificarUsuario() {
	try{
		let datos = {
			accion: "obtenerPorUsuarioClave",
			datos: {
				usuario: document.getElementById("usuario").value,
				clave: document.getElementById("clave").value
			}
		};

		document.getElementById("usuario").value = ''
		document.getElementById("clave").value = ''

		let data = await consultar("usuarios",datos);
		if(data !== null && typeof data !== 'undefined'){
			if (data.message) {
				mostrarNotificacion(data.message,"#FF0000") 
			} else if (data.error) {
				if(typeof data[0] !== 'undefined' && data[0] !== null){
					mostrarNotificacion(data.error + " " + data[0] ,"#FF0000") 
				}else{
					mostrarNotificacion(data.error,"#FF0000") 
				}
			} else {
				mostrarNotificacion("Usuario: " + data.usuario,"linear-gradient(to right, #00b09b, #96c93d)") 
				
				// Se crea en el sessionStorage el usuario y se redirige al index
				let usuario = {
					usuario: data.usuario,
					rol: data.rolnombre,
					rolId: data.rol_id,
					id: data.id
				}
				sessionStorage.setItem('usuario', JSON.stringify(usuario))
				localStorage.setItem('usuarioLogeado', JSON.stringify(usuario))
				window.location.href = "index.html";
			}
		}else{
			mostrarNotificacion("No se encontro ningun usuario","#FF0000") 
		}
	}catch(e){
		mostrarNotificacion("Error: " + e,"#FF0000")  
		console.error('Error:', e);
	}
}

// Funcion para crear usuario
async function crearUsuario() {
	try{
		let datos = {
			accion: "insertar",
			datos: {
				usuario: document.getElementById("usuario").value,
				clave: document.getElementById("clave").value,
				correo: document.getElementById("correo").value,
				usuarioAdministrador: document.getElementById("usuarioAdministrador").value,
				claveAdministrador: document.getElementById("claveAdministrador").value,
				rol_id: document.querySelector('select[name="rol"]').selectedOptions[0].value,
				estado: 'Activo'
			}
		};

		document.getElementById("usuario").value = '',
		document.getElementById("clave").value = '',
		document.getElementById("correo").value = '',
		document.getElementById("usuarioAdministrador").value = '',
		document.getElementById("claveAdministrador").value = '',
		document.querySelector('select[name="rol"]').value = 1;

		let data = await consultar("usuarios",datos);

		if(data !== null && typeof data !== 'undefined'){
			if (data.message) {
				mostrarNotificacion(data.message,"#FF0000") 
			} else if (data.error) {
				if(typeof data[0] !== 'undefined' && data[0] !== null){
					mostrarNotificacion(data.error + " " + data[0] ,"#FF0000") 
				}else{
					mostrarNotificacion(data.error,"#FF0000") 
				}
			} else {

				// Se guarda en el localStorage el objeto usuarioRegistrado y se redirige al login
				let usuarioRegistrado = {
					usuario: data.usuario
				}
				localStorage.setItem('usuarioRegistrado', JSON.stringify(usuarioRegistrado))
				//mostrarNotificacion("Usuario: " + data.usuario,"linear-gradient(to right, #00b09b, #96c93d)") 
				window.location.href = "login.html";
			}
		}else{
			mostrarNotificacion("No se encontro ningun usuario","#FF0000") 
		}
	}catch(e){
		mostrarNotificacion("Error: " + e,"#FF0000")  
		console.error('Error:', e);
	}
}

