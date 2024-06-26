document.addEventListener("DOMContentLoaded", async function () {

    // Se revisa si las tasas están listas para mostrarlas en el HTML
    document.addEventListener('tasasListas', async function(event) {
        // Aquí puedes continuar con la lógica que depende de la variable tasas
        if (typeof event.detail !== 'undefined' && event.detail !== null && event.detail.length > 0) {
            await ObtenerSelect("monedas", "monedas-select", "Moneda",monedas);
            await ObtenerSelect("tipo_factura", "tipofacturas-select", "Tipo Factura");
        } 
    });

    productos = await consultar("productos", {accion: "obtenerTodos"})
    productos.unshift({
        categoria: "",
        categoria_id:"0",
        descripcion: "",
        estado: "Activo",
        fecha_creacion: "",
        id: "0",
        nombre: "Seleccionar",
        precio: "0",
        stock: "0",
    }); 

    await ObtenerSelect("productos", "productos-select-0", "Producto",productos);

    $("#productos-select-0").select2();

});

$("[id^='productos-select-']").on('change', function() {
    if(typeof $(this).val() === 'undefined' || $(this).val() === null || $(this).val() == '0'){
        return
    }
    LlenarInputProductos($(this).attr('id').split('-')[2], $(this).val());

});

var modalPersonas = null
var persona = null
var productos = []

async function BuscarPersona(cedula, bandera){
    try{
        let seccionFactura = document.getElementById("seccionFactura")
        seccionFactura.className = "d-none"
        persona = null

        let datos = {
            accion: 'obtenerPorCedula',
            datos: {
                cedula: (typeof cedula !== 'undefined' && cedula !== null ? cedula : document.getElementById("buscarCedula").value)
            }
        };

        let data = await consultar("personas",datos);

        if(data !== null && typeof data !== 'undefined'){
            if (data.message) {
                mostrarNotificacion(data.message,"#FF0000") 
            } else if (data.error) {
                mostrarNotificacion(data.error,"#FF0000") 
            } else {
                if(bandera)
                    mostrarNotificacion("Persona Encontrada", "linear-gradient(to right, #00b09b, #96c93d)"); 
                InfoBusquedaPersona(data)
                persona = data
                seccionFactura.className = ""
            }
        }else{
            await ModalPersonas(null,true,'insertar')
        }
	}catch(e){
		mostrarNotificacion("Error:", e,"#FF0000") 
		console.error('Error:', e);
	}
}

async function ModalPersonas(datos,bandera,tipo){
    if(typeof modalPersonas === 'undefined' || modalPersonas === null){
        modalPersonas = new bootstrap.Modal(document.getElementById('modalPersonas'));
    }

    if(bandera){
        if(tipo === 'insertar' || tipo === 'actualizar'){
            ContenidoPersona(tipo === 'insertar' ? null : personas.find((x) => parseInt(x.id) === datos))
            await ObtenerSelect("tipo_persona", "tipopersonas-select", "Tipo Persona");
        }else if(tipo === 'eliminar'){
            ContenidoConfirmacionEliminar(datos)
        }
    }else{
        if(tipo === 'insertar' || tipo === 'actualizar'){
            await AccionPersona(tipo)
        }else if(tipo === 'eliminar'){
            await EliminarPersona(datos)
        }
    }

    return new Promise((resolve) => {
        const elementoModal = document.getElementById('modalPersonas');
        elementoModal.addEventListener(bandera ? 'shown.bs.modal' : 'hidden.bs.modal', () => {
            resolve();
        }, { once: true });
        if(bandera)
            modalPersonas.show();
        else
        modalPersonas.hide();
    });
}

function ContenidoPersona(datos){
    let bandera = (typeof datos === 'undefined' || datos === null)
    let contenido = `<h1 class="fs-4 card-title fw-bold mb-4">${bandera ? 'Registrar' : 'Modificar'}</h1>
                        <form action="#" method="POST" class="needs-validation" novalidate="" autocomplete="off" onsubmit="event.preventDefault(); ModalPersonas(0,false,'${bandera ? 'insertar' : 'actualizar'}')">

                            <div class="mb-3">
                                <label class="mb-2 text-muted" for="tipopersona">Tipo Persona</label>
                                <select name="tipopersona" class="form-select" aria-label="Default select example" id="tipopersonas-select">
                                    <!-- Agrega opciones del select si es necesario -->
                                </select>
                            </div>

                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-3">
                                        <label class="mb-2 text-muted" for="extrangero">-</label>
                                        <select name="extrangero" class="form-select" aria-label="Default select example" id="extrangero-select">
                                            <option value="0" selected>V</option>
                                            <option value="1">E</option>
                                        </select>
                                    </div>
                                    <div class="col-9">
                                        <label class="mb-2 text-muted" for="cedulaPersona">Cedula</label>
                                        <input id="cedulaPersona" type="text" class="form-control" name="cedulaPersona" value="${bandera ? document.getElementById("buscarCedula").value : datos.cedula}" ${bandera ? '' : 'disabled'} required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="mb-2 text-muted" for="nombrePersona">Nombre</label>
                                <input id="nombrePersona" type="text" class="form-control" name="nombrePersona" value="${bandera ? '' : datos.nombre}" required >
                                <input id="idPersona" type="text" class="form-control" name="idPersona" value="${bandera ? '0' : datos.id}" hidden>
                            </div>

                            <div class="mb-3">
                                <label class="mb-2 text-muted" for="telefonoPersona">Telefono</label>
                                <input id="telefonoPersona" type="text" class="form-control" name="telefonoPersona" value="${bandera ? '' : datos.telefono}" required>
                            </div>

                            <div class="mb-3">
                                <label class="mb-2 text-muted" for="direccionPersona">Direccion</label>
                                <input id="direccionPersona" type="text" class="form-control" name="direccionPersona" value="${bandera ? '' : datos.direccion}" required>
                            </div>

                            <div class="row mt-3">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary ms-auto">${bandera ? 'Registrar' : 'Modificar'}</button>
                                </div>
                                <div class="col-6">
                                    <div name="" value="2" class="btn btn-secondary" onclick="ModalPersonas(0,false,'cancelar')">Cancelar</div>
                                </div>
                            </div>
                        </form>`
    document.getElementById("contenidoPersonas").innerHTML = contenido
}

async function AccionPersona(accion){
    try{
        let nombre = document.getElementById("nombrePersona")
        let id = document.getElementById("idPersona")
        let cedula = document.getElementById("cedulaPersona")
        let direccion = document.getElementById("direccionPersona")
        let telefono = document.getElementById("telefonoPersona")
        let extrangero = document.querySelector('select[name="extrangero"]').selectedOptions[0]
        let tipo_persona_id = document.querySelector('select[name="tipopersona"]').selectedOptions[0]

        if(nombre.value === '' ||
            cedula.value === '' ||
            direccion.value === '' ||
            telefono.value === ''){
                mostrarNotificacion("Todos los campos son requeridos","#FF0000") 
                return;
        }
        let datos = {
            accion: accion,
            datos: { 
                id: parseInt(id.value),
                nombre: nombre.value,
                cedula: cedula.value,
                direccion: direccion.value,
                telefono: telefono.value,
                extrangero: parseInt(extrangero.value),
                tipo_persona_id: parseInt(tipo_persona_id.value),
                estado: 'Activo'
            }
        };

        let data = await consultar("personas",datos);
        if(data !== null && typeof data !== 'undefined'){
            if (data.message) {
                mostrarNotificacion(data.message,"#FF0000") 
            } else if (data.error) {
                mostrarNotificacion(data.error,"#FF0000") 
            } else {
                await BuscarPersona(cedula.value, false);
                id.value = ''
                nombre.value = ''
                cedula.value = ''
                direccion.value = ''
                telefono.value = ''
                extrangero.value = 0
                tipo_persona_id.value = 1
                mostrarNotificacion("Persona " + (accion === 'insertar' ? 'Registrado' : 'Modificado'), "linear-gradient(to right, #00b09b, #96c93d)"); 
            }
        }else{
            mostrarNotificacion("No se encontro ningun " + error,"#FF0000") 
        }
        
	}catch(e){
		mostrarNotificacion("Error:", e,"#FF0000") 
		console.error('Error:', e);
	}
}

function InfoBusquedaPersona(datos){
    let contenido =`<div class="col-12 d-flex flex-row bd-highlight mb-3">
                        <div class="p-2 bd-highlight"> 
                            <label class="text-muted" for="nombre">Cedula</label>
                            <input type="text" class="form-control" name="nombre" value="${datos.extrangero + '-' + datos.cedula}" disabled>
                        </div>
                        <div class="p-2 bd-highlight"> 
                            <label class="text-muted" for="nombre">Nombre</label>
                            <input type="text" class="form-control" name="nombre" value="${datos.nombre}" disabled>
                        </div>
                        <div class="p-2 bd-highlight"> 
                            <label class="text-muted" for="nombre">Telefono</label>
                            <input type="text" class="form-control" name="nombre" value="${datos.telefono}" disabled>
                        </div>
                        <div class="p-2 bd-highlight"> 
                            <label class="text-muted" for="direccion">Direccion</label>
                            <input type="text" class="form-control" name="direccion" value="${datos.direccion}" disabled>
                        </div>
                        <div class="p-2 bd-highlight"> 
                            <label class="text-muted" for="tipo">Tipo</label>
                            <input type="text" class="form-control" name="tipo" value="${datos.tipopersona}" disabled>
                        </div>
                    </div>`
    document.getElementById("infoBusquedaPersona").innerHTML = contenido
}

function LlenarInputProductos(numero, valor){
    console.log(numero + ': ' + valor);
    let producto = productos.find(x => x.id === valor)

    document.getElementById("producto-"+numero).value = producto.nombre
    document.getElementById("productoid-"+numero).value = producto.id
    document.getElementById("stock-"+numero).value = producto.stock
    document.getElementById("precio-"+numero).value = producto.precio

}

// Llama a la función iniciarImpresionAutomatica() cada 2 segundos
setInterval(function() {
    iniciarImpresionAutomatica();
}, 5000);

// Función para iniciar la impresión automática
function iniciarImpresionAutomatica() {
    // Selector para obtener todos los inputs cuyo id comience con 'productoid-'
    var inputs = document.querySelectorAll('input[id^="productoid-"]');
    
    if(inputs.length === 0){
        console.log("No se encontraron inputs con id que comience con 'productoid-'.");
        return;
    }

    inputs.forEach(function(input, index) {
        var valor = input.value;
        console.log("Valor del input " + index + ": " + valor);
    });
}