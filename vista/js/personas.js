document.addEventListener("DOMContentLoaded", async function () {
    document.addEventListener('eventLoading', async function(event) {
        if (typeof event.detail !== 'undefined' && event.detail !== null) {
            await Loading(true)
            await DatosTabla()
            await Loading(false)
        }
    });
});

var personas = []
var modalPersonas = null

async function DatosTabla(){
    try{

        let datos = {
            accion: "obtenerTodos"
        };

        let data = await consultar("personas",datos);
        if(data !== null && typeof data !== 'undefined'){
            if (data.message) {
                mostrarNotificacion(data.message,"#FF0000") 
            } else if (data.error) {
                mostrarNotificacion(data.error,"#FF0000") 
            } else {
                personas = data
                initDataTable(data)
            }
        }else{
            mostrarNotificacion("No se encontro ningun " + error,"#FF0000") 
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
            await DatosTabla()
        }else if(tipo === 'eliminar'){
            await EliminarPersona(datos)
            await DatosTabla()
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

function ContenidoConfirmacionEliminar(datos){
    let contenido = `<div class="row mt-4 mb-3">
                        <h4>¿Esta seguro que desea eliminar este elemento?</h4>
                        <div class="col-6">
                             <button name="eliminarPersona" value="1" class="btn btn-danger" onclick="ModalPersonas(${datos},false,'eliminar')">Eliminar</button>
                        </div>
                        <div class="col-6">
                            <button name="cancelarEliminarPersona" value="2" class="btn btn-secondary" onclick="ModalPersonas(0,false,'cancelar')">Cancelar</button>
                        </div>
                    </div>`
    document.getElementById("contenidoPersonas").innerHTML = contenido
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
                                            <option value="0" ${bandera ? 'selected' : (datos.extrangero === '0' ? 'selected' : '')} >V</option>
                                            <option value="1" ${bandera ? '' : (datos.extrangero === '1' ? 'selected' : '')}>E</option>
                                        </select>
                                    </div>
                                    <div class="col-9">
                                        <label class="mb-2 text-muted" for="cedulaPersona">Cedula</label>
                                        <input id="cedulaPersona" type="text" class="form-control" name="cedulaPersona" value="${bandera ? '' : datos.cedula}" ${bandera ? '' : 'disabled'} required>
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

async function EliminarPersona(id){
    try{
        let datos = {
            accion: "eliminar",
            datos: { id: id }
        };

        let data = await consultar("personas",datos);
        if(data !== null && typeof data !== 'undefined'){
            if (data.message) {
                mostrarNotificacion(data.message,"#FF0000") 
            } else if (data.error) {
                mostrarNotificacion(data.error,"#FF0000") 
            } else {
                mostrarNotificacion("Persona Eliminado", "linear-gradient(to right, #00b09b, #96c93d)"); 
            }
        }else{
            mostrarNotificacion("No se encontro ningun " + error,"#FF0000") 
        }
        
	}catch(e){
		mostrarNotificacion("Error:", e,"#FF0000") 
		console.error('Error:', e);
	}
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

var dataTable;
var dataTableIsInitialized = false;
var numeroPorPagona = 10;

const dataTableOptions = {
    scrollY: 'auto',  // Ajusta la altura automáticamente
    scrollCollapse: true,  // Permite colapsar la tabla si hay menos registros
    columnDefs: [
        { className: "centered", targets: [0, 1, 2, 3, 4, 5, 6] }
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

    dataTable = $("#datatable_personas").DataTable(dataTableOptions);

    dataTableIsInitialized = true;
}

function listaDatos(datos) {
    try {
        let content = ``;
        datos.forEach((dato, index) => {
            content += `
                 <tr>
                    <td>${index + 1}</td>
                    <td>${dato.extrangero != null && typeof dato.extrangero !== 'undefined' ? (dato.extrangero === '0' ? 'V' : 'E') : ''}</td>
                    <td>${dato.cedula != null && typeof dato.cedula !== 'undefined' ? dato.cedula : ''}</td>
                    <td>${dato.nombre != null && typeof dato.nombre !== 'undefined' ? dato.nombre : ''}</td>
                    <td>${dato.telefono != null && typeof dato.telefono !== 'undefined' ? dato.telefono : ''}</td>
                    <td>${dato.direccion != null && typeof dato.direccion !== 'undefined' ? dato.direccion : ''}</td>
                    <td>${dato.tipopersona != null && typeof dato.tipopersona !== 'undefined' ? dato.tipopersona : ''}</td>
                    <!-- <td><i class="fa-solid fa-check" style="color: green;"></i></td> -->
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="ModalPersonas(${dato.id},true,'actualizar')"
                        ><i class="bi bi-pen"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="ModalPersonas(${dato.id},true,'eliminar')"
                        ><i class="bi bi-trash3"></i></button>
                    </td>
                </tr>`;
        });
        tableBody_personas.innerHTML = content;
    } catch (ex) {
        alert(ex);
    }
}