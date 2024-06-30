document.addEventListener("DOMContentLoaded", async function () {
    document.addEventListener('eventLoading', async function(event) {
        if (typeof event.detail !== 'undefined' && event.detail !== null) {
            await Loading(true)
            await DatosTabla()
            await Loading(false)
        }
    });
});

var productos = []
var modalProductos = null

async function DatosTabla(){
    try{

        let datos = {
            accion: "obtenerTodos"
        };

        let data = await consultar("productos",datos);
        if(data !== null && typeof data !== 'undefined'){
            if (data.message) {
                mostrarNotificacion(data.message,"#FF0000") 
            } else if (data.error) {
                mostrarNotificacion(data.error,"#FF0000") 
            } else {
                productos = data
                initDataTable(data)
            }
        }else{
            mostrarNotificacion("No se encontro ningun " + error,"#FF0000") 
        }
        
	}catch(e){
		mostrarNotificacion("Error: " + e,"#FF0000")  
		console.error('Error:', e);
	}
}

async function ModalProductos(datos,bandera,tipo){
    if(bandera){
        
    }
    if(typeof modalProductos === 'undefined' || modalProductos === null){
        modalProductos = new bootstrap.Modal(document.getElementById('modalProductos'));
    }

    if(bandera){
        if(tipo === 'insertar' || tipo === 'actualizar'){
            ContenidoProducto(tipo === 'insertar' ? null : productos.find((x) => parseInt(x.id) === datos))
            await ObtenerSelect("categorias", "categorias-select", "categoria");
        }else if(tipo === 'eliminar'){
            ContenidoConfirmacionEliminar(datos)
        }
    }else{
        if(tipo === 'insertar' || tipo === 'actualizar'){
            await AccionProducto(tipo)
            await DatosTabla()
        }else if(tipo === 'eliminar'){
            await EliminarProducto(datos)
            await DatosTabla()
        }
    }

    return new Promise((resolve) => {
        const elementoModal = document.getElementById('modalProductos');
        elementoModal.addEventListener(bandera ? 'shown.bs.modal' : 'hidden.bs.modal', () => {
            resolve();
        }, { once: true });
        if(bandera)
            modalProductos.show();
        else
        modalProductos.hide();
    });
}

function ContenidoConfirmacionEliminar(datos){
    let contenido = `<div class="row mt-4 mb-3">
                        <h4>¿Esta seguro que desea eliminar este elemento?</h4>
                        <div class="col-6">
                             <button name="eliminarProducto" value="1" class="btn btn-danger" onclick="ModalProductos(${datos},false,'eliminar')">Eliminar</button>
                        </div>
                        <div class="col-6">
                            <button name="cancelarEliminarProducto" value="2" class="btn btn-secondary" onclick="ModalProductos(0,false,'cancelar')">Cancelar</button>
                        </div>
                    </div>`
    document.getElementById("contenidoProductos").innerHTML = contenido
}

function ContenidoProducto(datos){
    let bandera = (typeof datos === 'undefined' || datos === null)
    let contenido = `<h1 class="fs-4 card-title fw-bold mb-4">${bandera ? 'Registrar' : 'Modificar'}</h1>
							<form action="#" method="POST" class="needs-validation" novalidate="" autocomplete="off" onsubmit="event.preventDefault(); ModalProductos(0,false,'${bandera ? 'insertar' : 'actualizar'}')">

                                <div class="mb-3">
									<label class="mb-2 text-muted" for="categoria">Categoria</label>
									<select name="categoria" class="form-select" aria-label="Default select example" id="categorias-select">
										<!-- Agrega opciones del select si es necesario -->
									</select>
								</div>

								<div class="mb-3">
									<label class="mb-2 text-muted" for="nombreProducto">Nombre</label>
									<input id="nombreProducto" type="text" class="form-control" name="nombreProducto" value="${bandera ? '' : datos.nombre}" required >
                                    <input id="idProducto" type="text" class="form-control" name="idProducto" value="${bandera ? '0' : datos.id}" hidden>
								</div>

								<div class="mb-3">
									<label class="mb-2 text-muted" for="descripcionProducto">Descripcion</label>
									<input id="descripcionProducto" type="text" class="form-control" name="descripcionProducto" value="${bandera ? '' : datos.descripcion}" required>
								</div>

								<div class="mb-3">
									<label class="mb-2 text-muted" for="precioProducto">Precio</label>
									<input id="precioProducto" type="text" class="form-control" name="precioProducto" value="${bandera ? '' : datos.precio}" required>
								</div>

                                <div class="row"> 
                                    <div class="col-6"> 
                                        <div class="mb-3">
                                            <label class="mb-2 text-muted" for="descuentoProducto">Descuento</label>
                                            <input id="descuentoProducto" type="text" class="form-control" name="descuentoProducto" value="${bandera ? '' : datos.descuento}" required>
                                        </div>
                                    </div>
                                    <div class="col-6"> 
                                        <div class="mb-3">
                                            <label class="mb-2 text-muted" for="cantidadDescuentoProducto">Cantidad descuento</label>
                                            <input id="cantidadDescuentoProducto" type="text" class="form-control" name="cantidadDescuentoProducto" value="${bandera ? '' : datos.cantidad_descuento}" required>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="mb-3">
									<label class="mb-2 text-muted" for="stockProducto">Stock</label>
									<input id="stockProducto" type="text" class="form-control" name="stockProducto" value="${bandera ? '' : datos.stock}" required>
								</div>

								<div class="row mt-3">
									<div class="col-6">
                                        <button type="submit" class="btn btn-primary ms-auto">${bandera ? 'Registrar' : 'Modificar'}</button>
                                    </div>
                                    <div class="col-6">
                                        <div name="" value="2" class="btn btn-secondary" onclick="ModalProductos(0,false,'cancelar')">Cancelar</div>
                                    </div>
								</div>
							</form>`
    document.getElementById("contenidoProductos").innerHTML = contenido
}

async function EliminarProducto(id){
    try{
        let datos = {
            accion: "eliminar",
            datos: { id: id }
        };

        let data = await consultar("productos",datos);
        if(data !== null && typeof data !== 'undefined'){
            if (data.message) {
                mostrarNotificacion(data.message,"#FF0000") 
            } else if (data.error) {
                mostrarNotificacion(data.error,"#FF0000") 
            } else {
                mostrarNotificacion("Producto Eliminado", "linear-gradient(to right, #00b09b, #96c93d)"); 
            }
        }else{
            mostrarNotificacion("No se encontro ningun " + error,"#FF0000") 
        }
        
	}catch(e){
		mostrarNotificacion("Error: " + e,"#FF0000")  
		console.error('Error:', e);
	}
}

async function AccionProducto(accion){
    try{
        let nombre = document.getElementById("nombreProducto")
        let id = document.getElementById("idProducto")
        let descripcion = document.getElementById("descripcionProducto")
        let precio = document.getElementById("precioProducto")
        let descuento = document.getElementById("descuentoProducto")
        let cantidadDescuento = document.getElementById("cantidadDescuentoProducto")
        let stock = document.getElementById("stockProducto")
        let categoria_id = document.querySelector('select[name="categoria"]').selectedOptions[0]

        if(nombre.value === '' ||
            descripcion.value ==='' ||
            precio.value === '' ||
            stock.value === ''){
                mostrarNotificacion("Todos los campos son requeridos","#FF0000") 
                return;
        }

        let datos = {
            accion: accion,
            datos: { 
                id: parseInt(id.value),
                nombre: nombre.value,
                descripcion: descripcion.value,
                precio: parseFloat(precio.value.replace(',','.')),
                descuento: parseFloat(descuento.value.replace(',','.')),
                cantidad_descuento: parseInt(cantidadDescuento.value),
                stock: parseInt(stock.value),
                categoria_id: categoria_id.value,
                estado: 'Activo'
            }
        };

        let data = await consultar("productos",datos);
        if(data !== null && typeof data !== 'undefined'){
            if (data.message) {
                mostrarNotificacion(data.message,"#FF0000") 
            } else if (data.error) {
                mostrarNotificacion(data.error,"#FF0000") 
            } else {
                id.value = ''
                nombre.value = ''
                descripcion.value = ''
                precio.value = ''
                descuento.value = ''
                cantidadDescuento.value = ''
                stock.value = ''
                categoria_id.value = 1
                mostrarNotificacion("Producto " + (accion === 'insertar' ? 'Registrado' : 'Modificado'), "linear-gradient(to right, #00b09b, #96c93d)"); 
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
        { className: "centered", targets: [0, 1, 2, 3, 4, 5, 6, 7, 8] }
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

    dataTable = $("#datatable_productos").DataTable(dataTableOptions);

    dataTableIsInitialized = true;
}

function listaDatos(datos) {
    try {
        let content = ``;
        datos.forEach((dato, index) => {
            content += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${dato.nombre != null && typeof dato.nombre !== 'undefined' ? dato.nombre : ''}</td>
                    <td>${dato.descripcion != null && typeof dato.descripcion !== 'undefined' ? dato.descripcion : ''}</td>
                    <td>${dato.precio != null && typeof dato.precio !== 'undefined' ? dato.precio : ''}</td>
                    <td>${dato.descuento != null && typeof dato.descuento !== 'undefined' ? dato.descuento + ' %' : ''}</td>
                    <td>${dato.cantidad_descuento != null && typeof dato.cantidad_descuento !== 'undefined' ? dato.cantidad_descuento : ''}</td>
                    <td>${dato.stock != null && typeof dato.stock !== 'undefined' ? dato.stock : ''}</td>
                    <td>${dato.categoria != null && typeof dato.categoria !== 'undefined' ? dato.categoria : ''}</td>
                    <!-- <td><i class="fa-solid fa-check" style="color: green;"></i></td> -->
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="ModalProductos(${dato.id},true,'actualizar')"
                        ><i class="bi bi-pen"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="ModalProductos(${dato.id},true,'eliminar')"
                        ><i class="bi bi-trash3"></i></button>
                    </td>
                </tr>`;
        });
        tableBody_productos.innerHTML = content;
    } catch (ex) {
        alert(ex);
    }
}