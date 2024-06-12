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

document.addEventListener('DOMContentLoaded', async function() {

});

async function verificarUsuario() {
	try{
		const resultadoDiv = document.getElementById('resultado');

		let datos = {
			accion: 'obtenerPorUsuarioClave',
			datos: {
				usuario: document.getElementById("usuario").value,
				clave: document.getElementById("clave").value
			}
		};

		let data = await consultar('usuarios',datos);
		if(data !== null && typeof data !== 'undefined'){
			if (data.message) {
				resultadoDiv.innerHTML = `<p>${data.message}</p>`;
			} else if (data.error) {
				resultadoDiv.innerHTML = `<p>Error: ${data.error}</p>`;
			} else {
				resultadoDiv.innerHTML = 'Usuario: ' + data.usuario;
			}
		}else{
			resultadoDiv.innerHTML = 'No se encontro ningun usuario';
		}
	}catch(e){
		console.error('Error:', e);
	}
	
}