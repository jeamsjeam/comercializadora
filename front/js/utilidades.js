document.addEventListener("DOMContentLoaded", async function () {

    // Se valida si la ruta no es index, registro o recuperacion
    if (window.location.href.indexOf('login.html') === -1 && 
        window.location.href.indexOf('registro.html') === -1 &&
        window.location.href.indexOf('recuperacion.html') === -1) {

        // Se carga el navbar y se crea el html de los modales
        CargarNavbar(window.location.href);
        CrearModales();

        // Se verifica si existe usuario en el sessionStorage
        //En caso de no existir se redigire al login 
        let datosUsuario = sessionStorage.getItem('usuario');
        if (typeof datosUsuario === 'undefined' || datosUsuario === null) {
            window.location.href = "login.html";
        }

        //Se verifica si no ex el index
        if (window.location.href.indexOf('index.html') !== -1) {

            // Se verifica si existe usarioLogeado, para poder mostrar una notificacion en verde
            let usuarioLogeado = JSON.parse(localStorage.getItem('usuarioLogeado'));
            if (typeof usuarioLogeado !== 'undefined' &&  usuarioLogeado !== null && typeof usuarioLogeado.usuario !== 'undefined' && usuarioLogeado.usuario !== null) {
                mostrarNotificacion("Usuario " + usuarioLogeado.usuario + " logeado!","linear-gradient(to right, #00b09b, #96c93d)"); 
            }

            // Se verifica si existe tasaRegistrada, para poder mostrar una notificacion en verde
            /*let tasaRegistrada = JSON.parse(localStorage.getItem('tasa'));
            if (typeof tasaRegistrada !== 'undefined' &&  tasaRegistrada !== null && typeof tasaRegistrada.tasa !== 'undefined' && tasaRegistrada.tasa !== null) {
                mostrarNotificacion("Tasas Registradas", "linear-gradient(to right, #00b09b, #96c93d)"); 
            }*/
        }
        
        // Se eliminan los siguientes objetos del localStorage
        localStorage.removeItem('usuarioLogeado');
        //localStorage.removeItem('tasa');

        // Se llama a la funcion que verifica si existe tasa del dia actual
        await Loading(true)
        /*await VerificarTasa();
        // Despachar un evento personalizado indicando que tasas está llena
        const event = new CustomEvent('tasasListas', { detail: tasas });
        document.dispatchEvent(event);*/
        await Loading(false)

        const eventLoading = new CustomEvent('eventLoading', { detail: modalLoading });
        document.dispatchEvent(eventLoading);

    } else {
        // Se elimina el objeto usuario en caso de estar en el login
        sessionStorage.removeItem('usuario');
    }
});

// Variables globales
var monedas = null;
var tasas = [];
var modalTasas= null;
var modalLoading = null;

// Funcion que inserta el html de los modales en el body y activa el modal del loading
function CrearModales() {
    const modalDivTasas = document.createElement('div');
    modalDivTasas.innerHTML = `
        <div class="modal fade" id="modalTasaVerificacion" tabindex="-1" aria-labelledby="modalTasaVerificacionLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-ls">
                <div id="modalTasaVerificacionDiv" class="modal-content">
                </div>
            </div>
        </div>`;
    document.body.appendChild(modalDivTasas);

    const modalDiv = document.createElement('div');
    modalDiv.innerHTML = `
        <div class="modal fade" id="modalLoading" tabindex="-1" aria-labelledby="modalLoading" aria-hidden="true" data-bs-backdrop="static">
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
    modalLoading = new bootstrap.Modal(document.getElementById('modalLoading'));
}

// Funcion que abre o cierra el modal del loading
function Loading(bandera){
    return new Promise((resolve) => {
        const elementoModal = document.getElementById('modalLoading');
        elementoModal.addEventListener(bandera ? 'shown.bs.modal' : 'hidden.bs.modal', () => {
            resolve();
        }, { once: true });
        if(bandera)
            modalLoading.show();
        else
            modalLoading.hide();
    });
}

// Funcion para consultar a los archivos php
// tabla: el nombre de la tabla, que es el mismo que el archivo php respectivo
// datos: son los datos que se van a enviar a la consulta
async function consultar(tabla,datos) {
    try {    

        // Se convierte los datos de entrada en un string y se asigna a la propiedad datos de otro objeto
        let envioDatos = {
			datos: JSON.stringify(datos)
		};

        // Se crea la url y se consulta con fetch
        let url = 'http://localhost/comercializadora/backend/' + tabla + '.php'
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

// Muestra notificaciones
//texto: es el texto que se vera en la notificacion
//color: es el color que tendra la notificacion
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

// Funcion que da un formato de DD-MM-YYYY a las fechas
//fechaString: es el string que contiene la fecha
function formatoFechaString(fechaString) {
    // Crea un objeto Date a partir de la cadena de fecha
    const date = new Date(fechaString);
    
    if (isNaN(date.getTime())) {
        return "Fecha no válida"; // Maneja casos en los que la cadena de fecha no es válida
    }
    
    const day = String(date.getDate()).padStart(2, '0'); // Obtener el día y agregar ceros a la izquierda si es necesario
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Obtener el mes (los meses comienzan desde 0) y agregar ceros a la izquierda si es necesario
    const year = date.getFullYear(); // Obtener el año

    return `${day}-${month}-${year}`;
}

// Funcion que da un formato de dos decimales a los numeros
//fechaString: es el string que contiene la fecha
function formatoDecimalString(valor) {

    let decimal = 0;

    // Se verifica si no es NaN, si es string o si es un numero para su respectiva accion
    if(isNaN(valor)){
        decimal = 0
    }else if(typeof valor === 'string'){
        if(valor === ''){
            decimal = 0
        }else{
            decimal = parseFloat(valor.replace(',', '.'));
        }
    }else{
        decimal = valor
    }

    if (decimal % 1 !== 0) {
        // Si tiene decimales, mostrar dos decimales
        return decimal.toFixed(2);
    } else {
        // Si no tiene decimales, mostrar sin decimales
        return decimal.toString();
    }
}

// Funcion que sirve para cargar los select
// tabla: el nombre de la tabla, que es el mismo que el archivo php respectivo
// idSelect: el id del select que se quiere llenar
// error: mensaje personalizado en caso de que ocurra un error
// datos: datos para llenar el select, son opcionales si se envian no se consulta la base de datos
async function ObtenerSelect(tabla, idSelect, error, datos) {
	try{

        // Se obtiene el select y se limpia
        let select = document.getElementById(idSelect);
        select.innerHTML = ""

        // Se verifica si se enviaron los datos, en caso de que no se consulta la base de datos
        if(typeof datos !== 'undefined' && datos !== null){
            datos.forEach(s => {
                // Creamos una opción para cada select
                let option = document.createElement("option");
                option.value = s.id;
                option.textContent = s.nombre;
                select.appendChild(option);
            });
        }else{
            let datos = {
                accion: "obtenerTodos"
            };

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
        }
	}catch(e){
		mostrarNotificacion("Error: " + e,"#FF0000")  
		console.error('Error:', e);
	}
}

// Registra tasa del dia de hoy
async function RegistrarVerificarTasa(){

    let usuario = JSON.parse(sessionStorage.getItem('usuario'))
    let exitoVerificarTasas = false;
    modalTasas.hide()
    try{
        await Loading(true)

        // Se recorren las monedas y se consulta la casa para cada moneda que no sea principal
        // Y se registra la tasa
        for(let i = 0; i < monedas.length; i++){
            if(monedas[i].principal !== "1" && document.getElementById("modalTasaVerificacionInput" + monedas[i].nombre.replace(' ', '')) != null){
                let datos = {
                    accion: "insertar",
                    datos: {
                        tasa: parseFloat(document.getElementById("modalTasaVerificacionInput" + monedas[i].nombre.replace(' ', '')).value.replace(',', '.')),
                        usuario_id: usuario.id,
                        moneda_id: monedas[i].id
                    }
                };
       
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
        mostrarNotificacion("Error: " + e,"#FF0000")  
        console.error('Error:', e);
    }
}

// Funcion que carga el navbar
// pagina: es la pagina actual, sirve para colocar como activo en el navbar
function CargarNavbar(pagina){
    document.getElementById("navbar").innerHTML = `
                    <div  class="container-fluid">
                        <div class="navbar-brand">CLUB DEPORTIVO</div>
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon"></span>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarNav">
                                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                                    <li class="nav-item">
                                        <a class="nav-link ${pagina.indexOf('index.html') !== -1 ? 'active' : ''}" href="index.html">Inicio</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link ${pagina.indexOf('personas.html') !== -1 ? 'active' : ''}" href="personas.html">Registrar Jugador</a>
                                    </li>
                                    <!-- <li class="nav-item">
                                        <a class="nav-link ${pagina.indexOf('productos.html') !== -1 ? 'active' : ''}" href="productos.html">Registrar Productos</a>
                                    </li> -->
                                </ul>
                                <a type="button" class="btn btn-dark" href="login.html">Cerrar Sesion</a>
                            </div>
                    </div>`;
}

// Funcion que verifica si existe tasa para el dia actual
async function VerificarTasa(){
    try {

        // Se consultan las monedas
        monedas = await consultar('monedas', { accion: "obtenerTodos", datos: {}});
        if(typeof monedas === 'undefined' || monedas === null){
            throw("Error al consultar la base de datos")
        }
        let contenido = ``;

        // Se recorren las monedas y se consulta la tasa siempre que no sea la moneda principal
        for(let i = 0; i < monedas.length; i++){
            if(monedas[i].principal !== '1'){
                let tasa = await consultar('tasas_cambio', { accion: "obtenerPorUltimaFecha", datos: {moneda_id: monedas[i].id}});
                if(typeof tasa !== 'undefined' && tasa !== null){
                    tasas.push(tasa)
                }else{
                    let nombre = "modalTasaVerificacionInput" + monedas[i].nombre.replace(' ', '');
                    let etiquera = "Tasa para " + monedas[i].nombre;
                    contenido += `
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label class="mb-2 text-muted" for="${nombre}">${etiquera}</label>
                            <input id="${nombre}" type="text" class="form-control" name="${nombre}" value="" pattern="^[0-9]+(\.[0-9]+)?$" required autofocus>
                        </div>  
                    </div> `;
                }
            }
        }

        // Si no existe se abre un modal para registrar las tasas
        if(contenido !== ''){
            document.getElementById('modalTasaVerificacionDiv').innerHTML = `
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
                            </div>`;
            modalTasas = new bootstrap.Modal(document.getElementById('modalTasaVerificacion'));
            modalTasas.show();
        }
    } catch (error) {
        document.getElementById('modalTasaVerificacion').innerHTML = `
                    <div class="modal-body text-center">
                        ${error}
                    </div>`;
        modalTasas = new bootstrap.Modal(document.getElementById('modalTasaVerificacion'));
        modalTasas.show();
        console.error('Error:', error);
    }
}
