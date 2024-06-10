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
    await mostrarResultado();
});

async function mostrarResultado() {
	try{
		const resultadoDiv = document.getElementById('resultado');

		let datos = {
			accion: 'eliminarLista',
			datos2: {
				id: 26,
				nombre: 'Prueba26'
			},
			datos: [
				{id: 22,nombre: 'Prueba2'},
				{id: 25,nombre: 'Prueba2'},
				{id: 27,nombre: 'Prueba2'}
			]
		};

		console.log(datos)
		let data = await consultar('categorias',datos);
		resultadoDiv.innerHTML = '';

		if (data.message) {
			resultadoDiv.innerHTML = `<p>${data.message}</p>`;
		} else if (data.error) {
			resultadoDiv.innerHTML = `<p>Error: ${data.error}</p>`;
		} else {
			const lista = document.createElement('ul');
			data.forEach(categoria => {
				const item = document.createElement('li');
				item.textContent = `ID: ${categoria.id} - Nombre: ${categoria.nombre} - Fecha de Creación: ${categoria.fecha_creacion}`;
				lista.appendChild(item);
			});
			resultadoDiv.appendChild(lista);
		}
	}catch(e){
		console.error('Error:', e);
	}
	
}