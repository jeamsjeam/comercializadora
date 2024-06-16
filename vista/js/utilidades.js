document.addEventListener("DOMContentLoaded", async function () {
    if (window.location.href.indexOf('login.html') === -1 && 
        window.location.href.indexOf('registro.html') === -1 &&
        window.location.href.indexOf('recuperacion.html') === -1) {

        CargarNavbar(window.location.href);
        createLoadingModal();

        let datosUsuario = sessionStorage.getItem('usuario');
        if (typeof datosUsuario === 'undefined' || datosUsuario === null) {
            window.location.href = "login.html";
        }

        if (window.location.href.indexOf('index.html') !== -1) {
            let usuarioLogeado = JSON.parse(localStorage.getItem('usuarioLogeado'));
            if (typeof usuarioLogeado !== 'undefined' &&  usuarioLogeado !== null && typeof usuarioLogeado.usuario !== 'undefined' && usuarioLogeado.usuario !== null) {
                mostrarNotificacion("Usuario " + usuarioLogeado.usuario + " logeado!","linear-gradient(to right, #00b09b, #96c93d)"); 
            }

            let tasaRegistrada = JSON.parse(localStorage.getItem('tasa'));
            if (typeof tasaRegistrada !== 'undefined' &&  tasaRegistrada !== null && typeof tasaRegistrada.tasa !== 'undefined' && tasaRegistrada.tasa !== null) {
                mostrarNotificacion("Tasas Registradas", "linear-gradient(to right, #00b09b, #96c93d)"); 
            }
        }
        
        localStorage.removeItem('usuarioLogeado');
        localStorage.removeItem('tasa');

        await Loading(true)
        await VerificarTasa();
        await Loading(false)

    } else {
        sessionStorage.removeItem('usuario');
    }
});

var monedas = null;
var tasas = [];
var modalLoading = null;

function createLoadingModal() {
    const modalDiv = document.createElement('div');
    modalDiv.innerHTML = `
        <div class="modal fade" id="ModalLoading" tabindex="-1" aria-labelledby="ModalLoading" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-ls">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <div class="row">
                            <div class="col-12">
                                <div class="spinner-border m-5" style="width: 3rem; height: 3rem;" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label>Cargando...</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    document.body.appendChild(modalDiv);
    modalLoading = new bootstrap.Modal(document.getElementById('ModalLoading'));
}

function Loading(bandera){
    return new Promise((resolve) => {
        const elementoModal = document.getElementById('ModalLoading');
        elementoModal.addEventListener(bandera ? 'shown.bs.modal' : 'hidden.bs.modal', () => {
            resolve();
        }, { once: true });
        if(bandera)
            modalLoading.show();
        else
            modalLoading.hide();
    });
}

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
        Loading(true)
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
        Loading(false)
	}catch(e){
        Loading(false)
		mostrarNotificacion("Error:", e,"#FF0000") 
		console.error('Error:', e);
	}
}

async function RegistrarVerificarTasa(){
    let usuario = JSON.parse(sessionStorage.getItem('usuario'))
    let exitoVerificarTasas = false;
    
    try{
        await Loading(true)
        for(let i = 0; i < this.monedas.length; i++){
            if(monedas[i].nombre !== 'Dólar'){
                let datos = {
                    accion: "insertar",
                    datos: {
                        tasa: parseFloat(document.getElementById("ModalTasaVerificacionInput" + monedas[i].nombre).value),
                        usuario_id: usuario.usuarioId,
                        moneda_id: monedas[i].id
                    }
                };
    
                document.getElementById("ModalTasaVerificacionInput" + monedas[i].nombre).value = '';
        
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

        await Loading(false)

        if(exitoVerificarTasas){
            window.location.href = "index.html";
        }else{
            mostrarNotificacion("No se pudo registrar","#FF0000") 
        }
    }catch(e){
        Loading(false)
        mostrarNotificacion("Error:", e,"#FF0000") 
        console.error('Error:', e);
    }
    
}

function CargarNavbar(pagina){
    document.getElementById("navbar").innerHTML = `
                    <div  class="container-fluid">
                        <div class="navbar-brand">Comercializadora</div>
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarNav">
                                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                <li class="nav-item">
                                    <a class="nav-link ${pagina.indexOf('index.html') !== -1 ? 'active' : ''}" href="index.html">Inicio</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ${pagina.indexOf('productos.html') !== -1 ? 'active' : ''}" href="productos.html">Registrar Productos</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ${pagina.indexOf('personas.html') !== -1 ? 'active' : ''}" href="personas.html">Registrar Personas</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link ${pagina.indexOf('factura.html') !== -1 ? 'active' : ''}" href="factura.html">Registrar Factura</a>
                                </li>
                                </ul>
                                <a type="button" class="btn btn-dark" href="login.html">Cerrar Sesion</a>
                            </div>
                    </div>`;
}

async function VerificarTasa(){
    try {
        monedas = await consultar('monedas', { accion: "obtenerTodos", datos: {}});
        if(typeof this.monedas === 'undefined' || this.monedas === null){
            throw("Error al consultar la base de datos")
        }
        let contenido = ``;

        for(let i = 0; i < this.monedas.length; i++){
            if(monedas[i].nombre !== 'Dólar'){
                let tasa = await consultar('tasas_cambio', { accion: "obtenerPorUltimaFecha", datos: {moneda_id: monedas[i].id}});
                if(typeof tasa !== 'undefined' && tasa !== null){
                    tasas.push(tasa)
                }else{
                    let nombre = "ModalTasaVerificacionInput" + monedas[i].nombre;
                    let etiquera = "Tasa para " + monedas[i].nombre;
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
            const modalDiv = document.createElement('div');
            modalDiv.innerHTML = `
                        <div class="modal fade" id="ModalTasaVerificacion" tabindex="-1" aria-labelledby="ModalTasaVerificacionLabel" aria-hidden="true" data-bs-backdrop="static">
                            <div class="modal-dialog modal-ls">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <form name="formularioComprobarTasas" id="formularioComprobarTasas" method="post" class="m-3" action="#" onsubmit="event.preventDefault(); RegistrarVerificarTasa()">
                                            ${contenido}
                                            <div class="row mt-4 mb-3">
                                                <!-- Boton Registrar -->
                                                <div class="col-6">
                                                    <button type="submit" name="botonRegistrarModalTasaVerificacion" value="1" class="btn btn-primary">Registrar</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>`;
            document.body.appendChild(modalDiv);
            var myModal = new bootstrap.Modal(document.getElementById('ModalTasaVerificacion'));
            myModal.show();
        }
    } catch (error) {
        const modalDiv = document.createElement('div');
        modalDiv.innerHTML = `
                        <div class="modal fade" id="ModalTasaVerificacion" tabindex="-1" aria-labelledby="ModalTasaVerificacionLabel" aria-hidden="true" data-bs-backdrop="static">
                            <div class="modal-dialog modal-ls">
                                <div class="modal-content">
                                    <div class="modal-body text-center">
                                        ${error}
                                    </div>
                                </div>
                            </div>
                        </div>`;
        document.body.appendChild(modalDiv);
        var myModal = new bootstrap.Modal(document.getElementById('ModalTasaVerificacion'));
        myModal.show();
        console.error('Error:', error);
    }
}
