document.addEventListener("DOMContentLoaded", async function () {
    InicializarFechasFacturas()
    document.addEventListener('eventLoading', async function(event) {
        if (typeof event.detail !== 'undefined' && event.detail !== null) {
            await DatosTabla()
        }
    });
});

var facturas = []
var modalfacturas = null
var modalFacturaRegistrada = null

// Funcion que agrega fecha inicio y fecha fin de un rango de hace 7 dias hasta hoy
function InicializarFechasFacturas(){
    let fecha = new Date(); //Fecha actual
    let mes = fecha.getMonth()+1; //obteniendo mes
    let dia = fecha.getDate(); //obteniendo dia
    let ano = fecha.getFullYear(); //obteniendo año
    if(dia<10)
        dia='0'+dia; //agrega cero si el menor de 10
    if(mes<10)
        mes='0'+mes //agrega cero si el menor de 10
    document.getElementById('fechaFin').value=ano+"-"+mes+"-"+dia;

    fecha.setDate(fecha.getDate()-7);
    mes = fecha.getMonth()+1; //obteniendo mes
    dia = fecha.getDate(); //obteniendo dia
    ano = fecha.getFullYear(); //obteniendo año
    if(dia<10)
        dia='0'+dia; //agrega cero si el menor de 10
    if(mes<10)
        mes='0'+mes //agrega cero si el menor de 10
    document.getElementById('fechaInicio').value=ano+"-"+mes+"-"+dia;
}

async function DatosTabla(){
    try{
        await Loading(true)
        let datos = {
            accion: "obtenerTodosPorFecha",
            datos:{
                fecha_inicio: document.getElementById('fechaInicio').value,
                fecha_fin: document.getElementById('fechaFin').value,
            }
        };

        let data = await consultar("facturas",datos);
        if(data !== null && typeof data !== 'undefined'){
            if (data.message) {
                await Loading(false)
                mostrarNotificacion(data.message,"#FF0000") 
            } else if (data.error) {
                await Loading(false)
                mostrarNotificacion(data.error,"#FF0000") 
            } else {
                facturas = data
                initDataTable(data)
                await Loading(false)
            }
        }else{
            await Loading(false)
            mostrarNotificacion("No se encontro ningun " + error,"#FF0000") 
        }
        
	}catch(e){
        await Loading(false)
		mostrarNotificacion("Error: " + e,"#FF0000")  
		console.error('Error:', e);
	}
}

async function Modalfacturas(datos,bandera,tipo){
    if(typeof modalfacturas === 'undefined' || modalfacturas === null){
        modalfacturas = new bootstrap.Modal(document.getElementById('modalfacturas'));
    }
   
    if(bandera){
        if(tipo === 'verDetalle'){
            ContenidoFactura(tipo === 'insertar' ? null : facturas.find((x) => parseInt(x.id) === datos))
            await ObtenerSelect("tipo_Factura", "tipofacturas-select", "Tipo Factura");
        }else if(tipo === 'eliminar'){
            ContenidoConfirmacionEliminar(datos)
        }
    }else{
        if(tipo === 'verDetalle'){
            await AccionFactura(tipo)
            await DatosTabla()
        }else if(tipo === 'eliminar'){
            await EliminarFactura(datos)
            await DatosTabla()
        }
    }

    return new Promise((resolve) => {
        const elementoModal = document.getElementById('modalfacturas');
        elementoModal.addEventListener(bandera ? 'shown.bs.modal' : 'hidden.bs.modal', () => {
            resolve();
        }, { once: true });
        if(bandera)
            modalfacturas.show();
        else
        modalfacturas.hide();
    });
}

function ContenidoConfirmacionEliminar(datos){
    let contenido = `<div class="row mt-4 mb-3">
                        <h4>¿Esta seguro que desea cambiar el estado a cancelado de esta factura?</h4>
                        <div class="col-6">
                             <button name="eliminarFactura" value="1" class="btn btn-danger" onclick="Modalfacturas(${datos},false,'eliminar')">Cambiar estado</button>
                        </div>
                        <div class="col-6">
                            <button name="cancelarEliminarFactura" value="2" class="btn btn-secondary" onclick="Modalfacturas(0,false,'cancelar')">Cancelar</button>
                        </div>
                    </div>`
    document.getElementById("contenidofacturas").innerHTML = contenido
}

function ContenidoFactura(datos){
    let bandera = (typeof datos === 'undefined' || datos === null)
    let contenido = `<h1 class="fs-4 card-title fw-bold mb-4">${bandera ? 'Registrar' : 'Modificar'}</h1>
                        <form action="#" method="POST" class="needs-validation" novalidate="" autocomplete="off" onsubmit="event.preventDefault(); Modalfacturas(0,false,'${bandera ? 'insertar' : 'actualizar'}')">

                            <div class="mb-3">
                                <label class="mb-2 text-muted" for="tipoFactura">Tipo Factura</label>
                                <select name="tipoFactura" class="form-select" aria-label="Default select example" id="tipofacturas-select">
                                    <!-- Agrega opciones del select si es necesario -->
                                </select>
                            </div>

                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-3">
                                        <label class="mb-2 text-muted" for="extrangero">-</label>
                                        <select name="extrangero" class="form-select" aria-label="Default select example" id="extrangero-select">
                                            <option value="0" ${bandera ? 'selected' : (datos.extrangero === '0' ? 'selected' : '')} >V</option>
                                            <option value="1" ${bandera ? '' : (datos.extrangero === '1' ? 'selected' : '')}>E</option>
                                        </select>
                                    </div>
                                    <div class="col-9">
                                        <label class="mb-2 text-muted" for="cedulaFactura">Cedula</label>
                                        <input id="cedulaFactura" type="text" class="form-control" name="cedulaFactura" value="${bandera ? '' : datos.cedula}" ${bandera ? '' : 'disabled'} required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="mb-2 text-muted" for="nombreFactura">Nombre</label>
                                <input id="nombreFactura" type="text" class="form-control" name="nombreFactura" value="${bandera ? '' : datos.nombre}" required >
                                <input id="idFactura" type="text" class="form-control" name="idFactura" value="${bandera ? '0' : datos.id}" hidden>
                            </div>

                            <div class="mb-3">
                                <label class="mb-2 text-muted" for="telefonoFactura">Telefono</label>
                                <input id="telefonoFactura" type="text" class="form-control" name="telefonoFactura" value="${bandera ? '' : datos.telefono}" required>
                            </div>

                            <div class="mb-3">
                                <label class="mb-2 text-muted" for="direccionFactura">Direccion</label>
                                <input id="direccionFactura" type="text" class="form-control" name="direccionFactura" value="${bandera ? '' : datos.direccion}" required>
                            </div>

                            <div class="row mt-3">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-primary ms-auto">${bandera ? 'Registrar' : 'Modificar'}</button>
                                </div>
                                <div class="col-6">
                                    <div name="" value="2" class="btn btn-secondary" onclick="Modalfacturas(0,false,'cancelar')">Cancelar</div>
                                </div>
                            </div>
                        </form>`
    document.getElementById("contenidofacturas").innerHTML = contenido
}

async function EliminarFactura(id){
    try{
        let datos = {
            accion: "eliminar",
            datos: { id: id }
        };

        let data = await consultar("facturas",datos);
        if(data !== null && typeof data !== 'undefined'){
            if (data.message) {
                mostrarNotificacion(data.message,"#FF0000") 
            } else if (data.error) {
                mostrarNotificacion(data.error,"#FF0000") 
            } else {
                mostrarNotificacion("Factura cancelada", "linear-gradient(to right, #00b09b, #96c93d)"); 
            }
        }else{
            mostrarNotificacion("No se encontro ningun registro","#FF0000") 
        }
        
	}catch(e){
		mostrarNotificacion("Error: " + e,"#FF0000")  
		console.error('Error:', e);
	}
}

async function AccionFactura(accion){
    try{
        let nombre = document.getElementById("nombreFactura")
        let id = document.getElementById("idFactura")
        let cedula = document.getElementById("cedulaFactura")
        let direccion = document.getElementById("direccionFactura")
        let telefono = document.getElementById("telefonoFactura")
        let extrangero = document.querySelector('select[name="extrangero"]').selectedOptions[0]
        let tipo_Factura_id = document.querySelector('select[name="tipoFactura"]').selectedOptions[0]

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
                tipo_Factura_id: parseInt(tipo_Factura_id.value),
                estado: 'Activo'
            }
        };

        let data = await consultar("facturas",datos);
        if(data !== null && typeof data !== 'undefined'){
            if (data.message) {
                mostrarNotificacion(data.message,"#FF0000") 
            } else if (data.error) {
                mostrarNotificacion(data.error,"#FF0000") 
            } else {
                id.value = ''
                nombre.value = ''
                cedula.value = ''
                direccion.value = ''
                telefono.value = ''
                extrangero.value = 0
                tipo_Factura_id.value = 1
                mostrarNotificacion("Factura " + (accion === 'insertar' ? 'Registrado' : 'Modificado'), "linear-gradient(to right, #00b09b, #96c93d)"); 
            }
        }else{
            mostrarNotificacion("No se encontro ningun " + error,"#FF0000") 
        }
        
	}catch(e){
		mostrarNotificacion("Error: " + e,"#FF0000")  
		console.error('Error:', e);
	}
}

var dataTable;
var dataTableIsInitialized = false;
var numeroPorPagona = 10;

const dataTableOptions = {
    scrollY: 'auto',  // Ajusta la altura automáticamente
    scrollCollapse: true,  // Permite colapsar la tabla si hay menos registros
    columnDefs: [
        { className: "centered", targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] }
    ],
    pageLength: numeroPorPagona,
    destroy: true,
    language: {
        lengthMenu: "Mostrar _MENU_ registros por página",
        zeroRecords: "Ningún registro encontrado",
        info: "Mostrando de _START_ a _END_ de un total de _TOTAL_ registros",
        infoEmpty: "Ningún registro encontrado",
        infoFiltered: "(filtrados desde _MAX_ registros totales)",
        search: "Buscar:",
        loadingRecords: "Cargando...",
        paginate: {
            first: "Primero",
            last: "Último",
            next: "Siguiente",
            previous: "Anterior"
        }
    }
};

function initDataTable(datos) { 
    if (dataTableIsInitialized) {
        dataTable.destroy();
    }

    listaDatos(datos);

    dataTable = $("#datatable_facturas").DataTable(dataTableOptions);

    dataTableIsInitialized = true;
}

function listaDatos(datos) {
    try {
        let content = ``;
        datos.forEach((dato, index) => {
            content += `
                 <tr>
                    <td>${index + 1}</td>
                    <td>${dato.tipofactura != null && typeof dato.tipofactura !== 'undefined' ?  dato.tipofactura : ''}</td>
                    <td>${dato.estado != null && typeof dato.estado !== 'undefined' ? dato.estado : ''}</td>
                    <td>${dato.cedula != null && typeof dato.cedula !== 'undefined' ? (dato.extrangero === '0' ? 'V-' : 'E-') + dato.cedula : ''}</td>
                    <td>${dato.persona != null && typeof dato.persona !== 'undefined' ? dato.persona : ''}</td>
                    <td>${dato.total != null && typeof dato.total !== 'undefined' ? formatoDecimalString(dato.total) : ''}</td>
                    <td>${dato.moneda != null && typeof dato.moneda !== 'undefined' ? dato.moneda : ''}</td>
                    <td>${dato.tasa_cambio != null && typeof dato.tasa_cambio !== 'undefined' ? formatoDecimalString(dato.tasa_cambio) : ''}</td>
                    <td>${dato.usuario != null && typeof dato.usuario !== 'undefined' ? dato.usuario : ''}</td>
                    <td>${dato.fecha_creacion != null && typeof dato.fecha_creacion !== 'undefined' ? dato.fecha_creacion : ''}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="ModalFacturaRegistrada(${dato.id},true,'verDetalle')"
                        ><i class="bi bi-eye"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="Modalfacturas(${dato.id},true,'eliminar')" ${dato.estado !== 'Pagada' ? 'disabled' : ''}
                        ><i class="bi bi-x-octagon"></i></button>
                    </td>
                </tr>`;
        });
        tableBody_facturas.innerHTML = content;
    } catch (ex) {
        alert(ex);
    }
}

async function ModalFacturaRegistrada(facturaId,bandera){

    let datos = []

    if(bandera){
        try{
            datos = await consultar("detalles_factura",{accion: "obtenerPorFactura", datos:{ factura_id: parseInt(facturaId)}})
            if(datos !== null && typeof datos !== 'undefined'){
                if (datos.message) {
                    mostrarNotificacion(datos.message,"#FF0000") 
                    return
                } else if (datos.error) {
                    mostrarNotificacion(datos.error,"#FF0000") 
                } 
            }else{
                mostrarNotificacion("No se encontro ningun registro","#FF0000") 
                return
            }
            
        }catch(e){
            mostrarNotificacion("Error: " + e,"#FF0000")  
            console.error('Error:', e);
            return
        }
    }


    if(typeof modalFacturaRegistrada === 'undefined' || modalFacturaRegistrada === null){
        modalFacturaRegistrada = new bootstrap.Modal(document.getElementById('modalFacturaRegistrada'));
    }

    if(bandera){
        ContenidoFacturaRegistrada(datos)
    }

    return new Promise((resolve) => {
        const elementoModal = document.getElementById('modalFacturaRegistrada');
        elementoModal.addEventListener(bandera ? 'shown.bs.modal' : 'hidden.bs.modal', () => {
            resolve();
        }, { once: true });
        if(bandera)
            modalFacturaRegistrada.show();
        else
        modalFacturaRegistrada.hide();
    });
}

function ContenidoFacturaRegistrada(datos){
    let contenido = `<div class="row mt-3">
                        <div class="col-12 ">
                            <h3 class="fs-4 card-title fw-bold mb-4">Detalles</h3>
                            <div id="datosDetalleFactura">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5 text-center">
                        <div class="col-12">
                            <button class="btn btn-primary ms-auto" onclick="ModalFacturaRegistrada(null,false)">Cerrar</button>
                        </div>
                    </div>`
    document.getElementById("contenidoFacturaRegistrada").innerHTML = contenido
    ContenidoDetalleProducto(datos)
}

function ContenidoDetalleProducto(datos){
    let contenido = ``
    if(datos.length > 4){
        document.getElementById("datosDetalleFactura").className = "scrollable-div"
    }
    for(let i = 0; i < datos.length; i++){
        contenido += `<div class="row p-3">
                            <div class="col-1">
                                <label class="mb-2 text-muted" for="detalleIndice"></label>
                                <label class="form-control" for="detalleIndice" style="border: none !important;"># ${i+1}</label>
                            </div>
                            <div class="col-4">
                                <label class="mb-2 text-muted" for="detalleProducto">Producto</label>
                                <input id="detalleProducto" type="text" class="form-control" name="detalleProducto" value="${datos[i].producto}" disabled>
                            </div>
                            <div class="col-4">
                                <label class="mb-2 text-muted" for="detalleCantidad">Cantidad</label>
                                <input id="detalleCantidad" type="text" class="form-control" name="detalleCantidad" value="${formatoDecimalString(datos[i].cantidad)}" disabled>
                            </div>
                            <div class="col-3">
                                <label class="mb-2 text-muted" for="detallePrecio">Precio</label>
                                <input id="detallePrecio" type="text" class="form-control" name="detallePrecio" value="${formatoDecimalString(datos[i].precio_unitario)}" disabled>
                            </div>
                        </div>`
    }
    document.getElementById("datosDetalleFactura").innerHTML = contenido
}