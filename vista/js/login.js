document.addEventListener('DOMContentLoaded', async function() {
	sessionStorage.removeItem('usuario');
});

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
				mostrarNotificacion(data.erro,"#FF0000") 
			} else {
				mostrarNotificacion("Usuario: " + data.usuario,"linear-gradient(to right, #00b09b, #96c93d)") 
				let usuario = {
					usuario: data.usuario,
					rol: data.rolnombre,
					rolId: data.rol_id
				}
				sessionStorage.setItem('usuario', JSON.stringify(usuario))
				window.location.href = "index.html";
			}
		}else{
			mostrarNotificacion("No se encontro ningun usuario","#FF0000") 
		}
	}catch(e){
		mostrarNotificacion("Error:", e,"#FF0000") 
		console.error('Error:', e);
	}
	
}