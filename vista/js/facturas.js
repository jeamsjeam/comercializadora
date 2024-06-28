document.addEventListener("DOMContentLoaded", async function () {

    // Se revisa si las tasas están listas para mostrarlas en el HTML
    document.addEventListener('tasasListas', async function(event) {
        // Aquí puedes continuar con la lógica que depende de la variable tasas
        if (typeof event.detail !== 'undefined' && event.detail !== null && event.detail.length > 0) {
            await ObtenerSelect("monedas", "monedas-select", "Moneda",monedas);
            await ObtenerSelect("tipo_factura", "tipofacturas-select", "Tipo Factura");

            document.getElementById("tasaActual").value = tasaSeleccionada

            $("[id='monedas-select']").on('change', function() {
                BuscarTasaSeleccionada($(this).val())
            });
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

    usuario = JSON.parse(sessionStorage.getItem('usuario'))

});

var modalPersonas = null
var persona = null
var productos = []
var contadorSeccionFactura = 0
var banderaIntervalo = true
var banderaExisteStock = true
var factura = null
var detalleFactura = []
var tasaSeleccionada = 1
var monedaSelecionada = 1
var usuario = null;

async function LimpiarVariables(busqueda){
    factura = null
    banderaExisteStock = true
    banderaIntervalo = true
    detalleFactura = []
    persona = null
    document.getElementById("contenidoFactura").innerHTML = ''
    document.getElementById("contenidoPersonas").innerHTML = ''
    contadorSeccionFactura = 0
    document.getElementById("seccionFactura").className = "d-none"
    await SeccionesFacturaProducto(contadorSeccionFactura)
    if(busqueda)
        document.getElementById("buscarCedula").value = ''
}

async function BuscarPersona(cedula, bandera){
    try{
        await Loading(true)
        await LimpiarVariables(false)

        let seccionFactura = document.getElementById("seccionFactura")

        let datos = {
            accion: 'obtenerPorCedula',
            datos: {
                cedula: (typeof cedula !== 'undefined' && cedula !== null ? cedula : document.getElementById("buscarCedula").value)
            }
        };

        let data = await consultar("personas",datos);
        await Loading(false)
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
                seccionFactura.className = "mb-5"
            }
        }else{
            await ModalPersonas(null,true,'insertar')
        }
        
	}catch(e){
        await Loading(false)
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
                            <input type="text" class="form-control" name="nombre" value="${(datos.extrangero === 0 ? 'E' : 'V') + '-' + datos.cedula}" disabled>
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

async function SeccionesFacturaProducto(indice){

    let contenido =`<div id="seccion-${indice}" class="row p-2 borde-abajo">
                        <div class="col-sm-12 col-md-12 col-lg-3 col-lx-3 col-xxl-3 mb-5 mb-lg-0">
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-12">
                                        <label class="mb-2 text-muted" for="productos-select-${indice}">Productos</label>
                                    </div>
                                    <div id="select2-${indice}" class="col-12">
                                        <select name="productos-select-${indice}" class="form-select" aria-label="Default select example"
                                            id="productos-select-${indice}" style="width: 100%;">
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-9 col-lx-9 col-xxl-9 mb-5 mb-lg-0">
                            <div class="row">
                                <div class="col-3">
                                    <div class="mb-3">
                                        <label class="mb-2 text-muted" for="producto-${indice}">Producto</label>
                                        <input id="producto-${indice}" type="text" class="form-control" name="producto-${indice}" value=""
                                            disabled>
                                        <input id="productoid-${indice}" type="text" class="form-control" name="productoid-${indice}"
                                            value="" hidden>
                                        <input id="estado-${indice}" type="text" class="form-control" name="estado-${indice}" value="Activo"
                                            hidden>
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="mb-3">
                                        <label class="mb-2 text-muted" for="cantidad-${indice}">Cantidad</label>
                                        <input id="cantidad-${indice}" type="number" class="form-control" name="cantidad-${indice}" value=""
                                            required style="max-width: 150px;">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="mb-3">
                                        <label class="mb-2 text-muted" for="monto-${indice}">Monto</label>
                                        <input id="monto-${indice}" type="text" class="form-control" name="monto-${indice}" value=""
                                            disabled style="max-width: 150px;">
                                        <input id="precio-principal-${indice}" type="text" class="form-control" name="precio-principal-${indice}" value=""
                                            hidden >
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="mb-3">
                                        <label class="mb-2 text-muted" for="precio-unitario-${indice}">Precio Unitario</label>
                                        <input id="precio-unitario-${indice}" type="text" class="form-control" name="precio-unitario-${indice}" value=""
                                            disabled style="max-width: 150px;">
                                    </div>
                                </div>
                                <div class="col-2">
                                    <div class="mb-3" >
                                        <label class="mb-2 text-muted" for="stock-${indice}">Stock</label>
                                        <input id="stock-${indice}" type="text" class="form-control" name="stock-${indice}" value=""
                                            disabled style="max-width: 150px;">
                                    </div>
                                </div>
                                <div class="col-1">
                                    <div class="mb-3" style="padding: 34px;">
                                        <button class="btn btn-sm btn-danger" onclick="BorrarSeccionFactura(${indice})"><i
                                                class="bi bi-trash3"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`

    let documento = document.getElementById("contenidoFactura")
    documento.insertAdjacentHTML('beforeend', contenido);

    await ObtenerSelect("productos", "productos-select-" + indice, "Producto",productos);
    $("#productos-select-" + indice).select2();

    $("[id^='productos-select-']").on('change', function() {
        LlenarInputProductos($(this).attr('id').split('-')[2], $(this).val());
    });

    $("[id^='cantidad-']").on('input', function() {
        ValorCantidadProducto($(this).val(),$(this).attr('id').split('-')[1] )
    });

    contadorSeccionFactura++;
}

function ValorCantidadProducto(valor, numero){
    let precioPrincipal = document.getElementById("precio-principal-" + numero).value
    if(typeof precioPrincipal === 'undefined' || precioPrincipal === null || precioPrincipal === ''){
        return
    }

    document.getElementById("monto-" + numero).value = formatoDecimalString(parseFloat(precioPrincipal.replace(',','.')) * tasaSeleccionada * parseInt(valor))

    SumarTotal()

}

function SumarTotal(){
    // Selector para obtener todos los inputs cuyo id comience con 'productoid-'
    let inputs = document.querySelectorAll('input[id^="monto-"]');
    
    if(typeof inputs === 'undefined' || inputs === null || inputs.length === 0){
        return;
    }

    let total = 0

    inputs.forEach(function(input) {
        let valorPrecio = input.value;
        let numeroPrecio = input.id.split('-')[1]

        let estado = document.getElementById("estado-"+numeroPrecio).value

        if(estado.value !== 'Inactivo' && valorPrecio !== '' && valorPrecio !== '0'){
            total += parseFloat(valorPrecio)
        }
    });

    if(total >= 0)
        document.getElementById("montoTotal").value = total
}

async function AgregarSeccionFactura(){
    await SeccionesFacturaProducto(contadorSeccionFactura)
}

function BorrarSeccionFactura(valor){
    document.getElementById("estado-"+valor).value = "Inactivo"
    document.getElementById("seccion-"+valor).className += " d-none"
    document.getElementById("cantidad-"+valor).removeAttribute("required");
}

function LlenarInputProductos(numero, valor){

    if(typeof valor === 'undefined' || valor === null || valor === '0'){
        document.getElementById("producto-"+numero).value = ''
        document.getElementById("productoid-"+numero).value = ''
        document.getElementById("stock-"+numero).value = ''
        document.getElementById("monto-"+numero).value = ''
        document.getElementById("precio-principal-"+numero).value = ''
        document.getElementById("precio-unitario-"+numero).value = ''
        return
    }

    let bandera = false;

    var inputs = document.querySelectorAll('input[id^="productoid-"]');
    inputs.forEach(function(input) {
        if(input.value === valor && numero !== input.id.split('-')[1]){
            mostrarNotificacion("Producto ya seleccionado","#FF0000") 
            bandera = true
            return;
        }
    });

    if(bandera){
        document.getElementById("producto-"+numero).value = ''
        document.getElementById("productoid-"+numero).value = ''
        document.getElementById("stock-"+numero).value = ''
        document.getElementById("monto-"+numero).value = ''
        document.getElementById("precio-principal-"+numero).value = ''
        document.getElementById("precio-unitario-"+numero).value = ''
        return;
    }

    let producto = productos.find(x => x.id === valor)

    document.getElementById("producto-"+numero).value = producto.nombre
    document.getElementById("productoid-"+numero).value = producto.id
    document.getElementById("stock-"+numero).value = producto.stock
    let cantidad = document.getElementById("cantidad-"+numero).value
    if(typeof cantidad === 'undefined' || cantidad === null || cantidad === ''){
        document.getElementById("monto-"+numero).value = 0
    }else{
        document.getElementById("monto-"+numero).value = formatoDecimalString(parseFloat(producto.precio) * tasaSeleccionada * parseInt(cantidad))
    }
    document.getElementById("precio-principal-"+numero).value = producto.precio
    document.getElementById("precio-unitario-"+numero).value = formatoDecimalString(parseFloat(producto.precio) * tasaSeleccionada)

    SumarTotal()
}

// Llama a la función iniciarImpresionAutomatica() cada 2 segundos
setInterval(async function() {
    if(!banderaIntervalo){
        return
    }
    await VerificarStockProductos();
}, 4000);

// Función para iniciar la impresión automática
async function VerificarStockProductos() {
    
    banderaExisteStock = false

    // Selector para obtener todos los inputs cuyo id comience con 'productoid-'
    let inputs = document.querySelectorAll('input[id^="productoid-"]');
    
    if(typeof inputs === 'undefined' || inputs === null || inputs.length === 0){
        return;
    }

    let listaInfoProductos = []

    inputs.forEach(function(input) {
        let valor = input.value;
        let numero = input.id.split('-')[1]
 
        if(document.getElementById("estado-"+numero).value !== 'Inactivo' && valor !== '' && valor !== '0'){
            listaInfoProductos.push({
                index: parseInt(numero),
                id: parseInt(valor)
            })
        }
    });

    if(typeof listaInfoProductos !== 'undefined' && listaInfoProductos !== null && listaInfoProductos.length > 0){

        let montoTotal = 0

        let data = await consultar("productos",{"accion":"obtenerPorListaId","datos":listaInfoProductos.map(x => x.id)})

        if(typeof data !== 'undefined' && data !== null && data.length > 0){

            banderaExisteStock = true
            detalleFactura = []

            for(let i = 0; i < data.length; i++){
                let posicion = parseInt(listaInfoProductos.find(x => x.id === parseInt(data[i].id)).index)
                let valorCantidad = document.getElementById("cantidad-"+posicion).value

                let totalStock = parseInt(data[i].stock) - parseInt(valorCantidad !== '' ? parseInt(valorCantidad) : 0)
                let stockProducto = document.getElementById("stock-" + posicion)
                if(totalStock < 0 || valorCantidad === ''){
                    if(totalStock < 0){
                        totalStock = 0
                        stockProducto.className = "form-control fondo-rojo"
                    }
                    banderaExisteStock = false
                }else{
                    stockProducto.className = "form-control"
                }
                stockProducto.value = totalStock

                let precioProducto = document.getElementById("monto-"+posicion).value

                montoTotal += typeof precioProducto !== 'undefined' && precioProducto !== null && precioProducto !== '' ? parseFloat(precioProducto.replace(',','.')) : 0

                if(valorCantidad !== ''){
                    detalleFactura.push({
                        factura_id: 0,
                        producto_id: parseInt(data[i].id),
                        cantidad: parseInt(valorCantidad),
                        precio_unitario: parseFloat(data[i].precio) * tasaSeleccionada
                    })
                }
            }
        }

        factura = {
            persona_id: parseInt(persona.id),
            estado: 'Pagada',
            total: montoTotal,
            moneda_id: monedaSelecionada,
            tasa_cambio: tasaSeleccionada,
            usuario_id : parseInt(usuario.id),
            tipo_factura_id: parseInt(document.querySelector('select[name="tipofactura"]').selectedOptions[0].value)
        }

        document.getElementById("montoTotal").value = montoTotal
    }
}

function BuscarTasaSeleccionada(monedaid){
    if(monedas.find(x => x.id === monedaid).principal === '1'){
        tasaSeleccionada = 1
    }
    else{
        tasaSeleccionada = parseFloat(tasas.find(x => x.moneda_id === monedaid).tasa)
    }

    monedaSelecionada = parseInt(monedaid)

    document.getElementById("tasaActual").value = tasaSeleccionada

    // Selector para obtener todos los inputs cuyo id comience con 'productoid-'
    let inputs = document.querySelectorAll('input[id^="precio-principal-"]');
    
    if(typeof inputs === 'undefined' || inputs === null || inputs.length === 0){
        return;
    }

    inputs.forEach(function(input) {
        let valor = input.value;
        let numero = input.id.split('-')[2]

        if(document.getElementById("estado-"+numero).value !== 'Inactivo' && valor !== '' && valor !== '0'){
            let precioActual = document.getElementById("precio-unitario-"+numero)
            precioActual.value = formatoDecimalString(parseFloat(valor.replace(',','.')) * tasaSeleccionada)

            let MontoActual = document.getElementById("monto-"+numero)
            let cantidadActual = document.getElementById("cantidad-"+numero).value

            MontoActual.value =  formatoDecimalString(parseFloat(valor.replace(',','.')) * tasaSeleccionada * parseInt(typeof cantidadActual === 'undefined' || cantidadActual === null || cantidadActual === '' ? 1 : cantidadActual))
        }
    });

    SumarTotal()
}

async function RegistrarFactura(){
    banderaIntervalo = false
    await VerificarStockProductos()
    if(!banderaExisteStock){
        mostrarNotificacion("No existe stock o no se agregaron todas las cantidades de productos", "#FF0000") 
        banderaIntervalo = true
    }else{
        try{
            console.log(factura)
            let datos = {
                accion: 'insertarConDetalles',
                datos: { 
                    factura: factura,
                    detalle_factura: detalleFactura
                }
            };
    
            let data = await consultar("facturas",datos);
            if(data !== null && typeof data !== 'undefined'){
                if (data.message) {
                    mostrarNotificacion(data.message,"#FF0000") 
                } else if (data.error) {
                    mostrarNotificacion(data.error,"#FF0000") 
                } else {
                    await LimpiarVariables(true)
                    mostrarNotificacion("Factura Registrada", "linear-gradient(to right, #00b09b, #96c93d)"); 
                }
            }else{
                mostrarNotificacion("No se registro" + error,"#FF0000") 
            }
            
        }catch(e){
            mostrarNotificacion("Error:", e,"#FF0000") 
            console.error('Error:', e);
        }
    }
}