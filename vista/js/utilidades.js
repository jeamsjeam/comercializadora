document.addEventListener("DOMContentLoaded", async function () {

    // Se verifica que no sea ni la pantalla de index, registro o recuperacion
    if (window.location.href.indexOf('login.html') === -1 && 
        window.location.href.indexOf('registro.html') === -1 &&
        window.location.href.indexOf('recuperacion.html') === -1) {

        // Se extrae el objeto usuario del sessionStorage y se comprueba si existe, en caso de no existir se retorna al login
        let datosUsuario = sessionStorage.getItem('usuario')
        if(typeof datosUsuario === 'undefined' || datosUsuario === null){
            window.location.href = "login.html";
        }

        // Se verifica que sa el index, y se pregunta si en el localStorage se encuentra un objeto logeado o tasa para pintar una notificaion despues se borran los objetos
        if(window.location.href.indexOf('index.html') !== -1){
            let usuarioLogeado = JSON.parse(localStorage.getItem('usuarioLogeado'))
            if(typeof usuarioLogeado !== 'undefined' &&  usuarioLogeado !== null && typeof usuarioLogeado.usuario !== 'undefined' && usuarioLogeado.usuario !== null){
                mostrarNotificacion("Usuario " + usuarioLogeado.usuario + " logeado!","linear-gradient(to right, #00b09b, #96c93d)") 
            }
            let tasaRegistrada = JSON.parse(localStorage.getItem('tasa'))
            if(typeof tasaRegistrada !== 'undefined' &&  tasaRegistrada !== null && typeof tasaRegistrada.tasa !== 'undefined' && tasaRegistrada.tasa !== null){
                mostrarNotificacion("Tasas Registradas" ,"linear-gradient(to right, #00b09b, #96c93d)") 
            }
        }
        localStorage.removeItem('usuarioLogeado');
        localStorage.removeItem('tasa');

        // Se consulta si existe una tasa actual, si no se abre un modal para registrarla
        await VerificarTasa();
        
        /*let dataUsuario = JSON.parse(sessionStorage.getItem('usuario'))
            if(dataUsuario.rol === 'Asistencial'){
                window.location.href = "asistencial.html";
            }else if(dataUsuario.rol === 'Recursos Humanos'){
                window.location.href = "recursoshumanos.html";
            }else if(dataUsuario.rol === 'Central de Citas'){
                window.location.href = "citas.html";
            }else if(dataUsuario.rol === 'Paciente'){
                window.location.href = "menuPaciente.html";
            }*/
    }else{
        sessionStorage.removeItem('usuario');
    }
});

var monedas = null;
var tasas = [];

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

async function ObtenerSelect(tabla, idSelect, error) {
	try{
		let datos = {
			accion: "obtenerTodos"
		};

		let select = document.getElementById(idSelect);

		let data = await consultar(tabla,datos);
		if(data !== null && typeof data !== 'undefined'){
			if (data.message) {
				mostrarNotificacion(data.message,"#FF0000") 
			} else if (data.error) {
				mostrarNotificacion(data.error,"#FF0000") 
			} else {
				data.forEach(s => {
					// Creamos una opción para cada select
					let option = document.createElement("option");
					option.value = s.id;
					option.textContent = s.nombre;
					select.appendChild(option);
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

async function registrarVerificarTasa(){
    let usuario = JSON.parse(sessionStorage.getItem('usuario'))
    let exitoVerificarTasas = false;
    
    try{
        for(let i = 0; i < this.monedas.length; i++){
            if(this.monedas[i].nombre !== 'Dólar'){
                let datos = {
                    accion: "insertar",
                    datos: {
                        tasa: parseFloat(document.getElementById("ModalTasaInput" + this.monedas[i].nombre).value),
                        usuario_id: usuario.usuarioId,
                        moneda_id: this.monedas[i].id
                    }
                };
    
                document.getElementById("ModalTasaInput" + this.monedas[i].nombre).value = '';
        
                let data = await consultar("tasas_cambio",datos);
        
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
                        let tasa = {
                            tasa: data.tasa
                        }
                        localStorage.setItem('tasa', JSON.stringify(tasa))
                        exitoVerificarTasas = true;
                    }
                }else{
                    mostrarNotificacion("No se pudo registrar","#FF0000") 
                }
            }
        }

        if(exitoVerificarTasas){
            window.location.href = "index.html";
        }else{
            mostrarNotificacion("No se pudo registrar","#FF0000") 
        }
    }catch(e){
        mostrarNotificacion("Error:", e,"#FF0000") 
        console.error('Error:', e);
    }
    
}

async function VerificarTasa(){

    this.monedas = await consultar('monedas', { accion: "obtenerTodos", datos: {}});
    let contenido = ``;

    for(let i = 0; i < this.monedas.length; i++){
        if(this.monedas[i].nombre !== 'Dólar'){
            let tasa = await consultar('tasas_cambio', { accion: "obtenerPorUltimaFecha", datos: {moneda_id: this.monedas[i].id}});
            if(typeof tasa !== 'undefined' && tasa !== null){
                this.tasas.push(tasa)
            }else{
                let nombre = "ModalTasaInput" + this.monedas[i].nombre;
                let etiquera = "Tasa para " + this.monedas[i].nombre;
                contenido += `
                <div class="row">
                    <div class="col-12 mb-2">
                        <label class="mb-2 text-muted" for="${nombre}">${etiquera}</label>
                        <input id="${nombre}" type="text" class="form-control" name="${nombre}" value="" pattern ="^[0-9]+(,[0-9]+)?$" required autofocus>
                    </div>  
                </div> `;
            }
        }
    }

    if(contenido !== ''){
        document.getElementById("modalComprobarTasas").innerHTML = `
                    <div class="modal fade" id="ModalTasa" tabindex="-1" aria-labelledby="ModalTasaLabel" aria-hidden="true" data-bs-backdrop="static">
                        <div class="modal-dialog modal-ls">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <form name="formularioComprobarTasas" id="formularioComprobarTasas" method="post" class="m-3" action="#" onsubmit="event.preventDefault(); registrarVerificarTasa()">
                                        ${contenido}
                                        <div class="row mt-4 mb-3">
                                            <!-- Boton Registrar -->
                                            <div class="col-6">
                                                <button type="submit" name="botonRegistrarModalTasa" value="1" class="btn btn-primary">Registrar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>`;
        var myModal = new bootstrap.Modal(document.getElementById('ModalTasa'));
        myModal.show();
    }
}


