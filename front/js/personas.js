document.addEventListener("DOMContentLoaded", async function () {
    document.addEventListener('eventLoading', async function(event) {
        if (typeof event.detail !== 'undefined' && event.detail !== null) {
            await Loading(true)
            await DatosTabla()
            await Loading(false)

            //InicializarFechasFacturas()
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

        let data = await consultar("jugadores",datos);
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
		mostrarNotificacion("Error: " + e,"#FF0000")  
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
            //await ObtenerSelect("tipo_persona", "tipopersonas-select", "Tipo Persona");
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

function ContenidoPersona(datos) {
    let bandera = (typeof datos === 'undefined' || datos === null);
    let contenido = `<h1 class="fs-4 card-title fw-bold mb-4">${bandera ? 'Registrar' : 'Modificar'}</h1>
                        <form action="#" method="POST" class="needs-validation" novalidate="" autocomplete="off" onsubmit="event.preventDefault(); ModalPersonas(0,false,'${bandera ? 'insertar' : 'actualizar'}')">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="cedulaPersona">Cédula</label>
                                    <input id="cedulaPersona" type="text" class="form-control" name="cedulaPersona" value="${bandera ? '' : datos.numero_cedula}" ${bandera ? '' : 'disabled'} required>
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="nombrePersona">Nombre</label>
                                    <input id="nombrePersona" type="text" class="form-control" name="nombrePersona" value="${bandera ? '' : datos.nombres_apellidos}" required>
                                    <input id="idPersona" type="text" class="form-control" name="idPersona" value="${bandera ? '0' : datos.id}" hidden>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="telefonoPersona">Teléfono</label>
                                    <input id="telefonoPersona" type="text" class="form-control" name="telefonoPersona" value="${bandera ? '' : datos.celular}">
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="direccionPersona">Dirección</label>
                                    <input id="direccionPersona" type="text" class="form-control" name="direccionPersona" value="${bandera ? '' : datos.direccion_exacta}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="fechaNacimiento">Fecha de Nacimiento</label>
                                    <input id="fechaNacimiento" type="date" class="form-control" name="fechaNacimiento" value="${bandera ? '' : datos.fecha_nacimiento}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="celular">Celular</label>
                                    <input id="celular" type="text" class="form-control" name="celular" value="${bandera ? '' : datos.celular}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="lugarEstudio">Lugar de Estudio</label>
                                    <input id="lugarEstudio" type="text" class="form-control" name="lugarEstudio" value="${bandera ? '' : datos.lugar_estudio}">
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="grado">Grado</label>
                                    <input id="grado" type="text" class="form-control" name="grado" value="${bandera ? '' : datos.grado}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="seccion">Sección</label>
                                    <input id="seccion" type="text" class="form-control" name="seccion" value="${bandera ? '' : datos.seccion}">
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="enfermedadesAlergias">Enfermedades/Alergias</label>
                                    <textarea id="enfermedadesAlergias" class="form-control" name="enfermedadesAlergias">${bandera ? '' : datos.enfermedades_alergias}</textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="tipoSangre">Tipo de Sangre</label>
                                    <input id="tipoSangre" type="text" class="form-control" name="tipoSangre" value="${bandera ? '' : datos.tipo_sangre}">
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="historialDeportivo">Historial Deportivo</label>
                                    <textarea id="historialDeportivo" class="form-control" name="historialDeportivo">${bandera ? '' : datos.historial_deportivo}</textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="perfil">Perfil</label>
                                    <input id="perfil" type="text" class="form-control" name="perfil" value="${bandera ? '' : datos.perfil}">
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="posicion">Posición</label>
                                    <input id="posicion" type="text" class="form-control" name="posicion" value="${bandera ? '' : datos.posicion}">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="otrasActividades">Otras Actividades</label>
                                    <textarea id="otrasActividades" class="form-control" name="otrasActividades">${bandera ? '' : datos.otras_actividades}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="lugarActividades">Lugar de Actividades</label>
                                    <textarea id="lugarActividades" class="form-control" name="lugarActividades">${bandera ? '' : datos.lugar_actividades}</textarea>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="mb-2 text-muted" for="personasVive">Personas con las que vive</label>
                                    <textarea id="personasVive" class="form-control" name="personasVive">${bandera ? '' : datos.personas_vive}</textarea>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <h4 class="mt-4">Redes Sociales del Jugador</h4>
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="facebookJugador">Facebook</label>
                                    <input id="facebookJugador" type="text" class="form-control" name="facebookJugador" value="${bandera ? '' : datos.facebook_jugador}">
                                </div>
                                <div class="col-md-6">
                                    <label class="mb-2 text-muted" for="instagramJugador">Instagram</label>
                                    <input id="instagramJugador" type="text" class="form-control" name="instagramJugador" value="${bandera ? '' : datos.instagram_jugador}">
                                </div>
                            </div>`
                            console.log(bandera)
        if(bandera == true){
            console.log(bandera)

            contenido += `<div class="row mb-4">
                                    <h4 class="mt-4">Datos del Representante</h4>
                                    <div class="col-md-6">
                                        <label class="mb-2 text-muted" for="nombreRepresentante">Nombre del Representante</label>
                                        <input id="nombreRepresentante" type="text" class="form-control" name="nombreRepresentante" value="${bandera ? '' : datos.representante?.nombre_apellidos}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="mb-2 text-muted" for="tipoRepresentante">Tipo de Representante</label>
                                        <select id="tipoRepresentante" class="form-select" name="tipoRepresentante" required>
                                            <option value="padre" ${bandera ? '' : (datos.representante?.parentesco === 'padre' ? 'selected' : '')}>Padre</option>
                                            <option value="madre" ${bandera ? '' : (datos.representante?.parentesco === 'madre' ? 'selected' : '')}>Madre</option>
                                        </select>
                                    </div>
                                </div>
    
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="mb-2 text-muted" for="telefonoRepresentante">Teléfono del Representante</label>
                                        <input id="telefonoRepresentante" type="text" class="form-control" name="telefonoRepresentante" value="${bandera ? '' : datos.representante?.telefono}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="mb-2 text-muted" for="direccionRepresentante">Dirección del Representante</label>
                                        <input id="direccionRepresentante" type="text" class="form-control" name="direccionRepresentante" value="${bandera ? '' : datos.representante?.direccion}">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="mb-2 text-muted" for="cedulaRepresentante">Cédula del Representante</label>
                                        <input id="cedulaRepresentante" type="text" class="form-control" name="cedulaRepresentante" value="${bandera ? '' : datos.representante?.cedula}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="mb-2 text-muted" for="correoRepresentante">Correo del Representante</label>
                                        <input id="correoRepresentante" type="email" class="form-control" name="correoRepresentante" value="${bandera ? '' : datos.representante?.correo}">
                                    </div>
                                </div>`
        }

    contenido += `<div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">${bandera ? 'Registrar' : 'Modificar'}</button>
                            </div>
                        </form>`;
    document.getElementById("contenidoPersonas").innerHTML = contenido;
}


async function EliminarPersona(id){
    try{
        let datos = {
            accion: "eliminar",
            datos: { id: id }
        };

        let data = await consultar("jugadores",datos);
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
		mostrarNotificacion("Error: " + e,"#FF0000")  
		console.error('Error:', e);
	}
}

async function AccionPersona(accion) {
    try {
        // Datos del jugador
        let id = document.getElementById("idPersona");
        let cedula = document.getElementById("cedulaPersona");
        let nombre = document.getElementById("nombrePersona");
        let fechaNacimiento = document.getElementById("fechaNacimiento");
        let celular = document.getElementById("celular");
        let direccionExacta = document.getElementById("direccionPersona"); // Cambiado de direccionExacta a direccionPersona
        let lugarEstudio = document.getElementById("lugarEstudio");
        let grado = document.getElementById("grado");
        let seccion = document.getElementById("seccion");
        let enfermedadesAlergias = document.getElementById("enfermedadesAlergias");
        let tipoSangre = document.getElementById("tipoSangre");
        let historialDeportivo = document.getElementById("historialDeportivo");
        let perfil = document.getElementById("perfil");
        let posicion = document.getElementById("posicion");
        let otrasActividades = document.getElementById("otrasActividades");
        let lugarActividades = document.getElementById("lugarActividades");
        let personasVive = document.getElementById("personasVive");

        let representante = {}
        if(accion !== 'actualizar'){

            // Datos del representante
            let nombreRepresentante = document.getElementById("nombreRepresentante");
            let tipoRepresentante = document.getElementById("tipoRepresentante").value;
            let telefonoRepresentante = document.getElementById("telefonoRepresentante");
            let direccionRepresentante = document.getElementById("direccionRepresentante");
            let numeroCedulaRepresentante = document.getElementById("cedulaRepresentante"); // Cambiado de numeroCedulaRepresentante a cedulaRepresentante
            let parentesco = document.getElementById("tipoRepresentante").value; // Suponiendo que el parentesco es el tipo de representante
            let trabajoRepresentante = document.getElementById("trabajoRepresentante");
            let facebookRepresentante = document.getElementById("facebookJugador"); // Cambiado de facebookRepresentante a facebookJugador
            let instagramRepresentante = document.getElementById("instagramJugador"); // Cambiado de instagramRepresentante a instagramJugador
            representante = {
                nombre_apellidos: nombreRepresentante.value,
                tipo_representante: tipoRepresentante,
                telefono: telefonoRepresentante.value,
                direccion: direccionRepresentante.value,
                numero_cedula: numeroCedulaRepresentante.value, // Asegúrate de que este ID sea correcto
                parentesco: parentesco, // Se asume que este campo es el mismo tipo de representante
                //trabajo: trabajoRepresentante.value,
                trabajo: "",
                facebook: facebookRepresentante.value,
                instagram: instagramRepresentante.value
            }
    
            // Validación de campos obligatorios
            if (nombre.value === '' || cedula.value === '' || nombreRepresentante.value === '') {
                mostrarNotificacion("Todos los campos obligatorios deben ser llenados", "#FF0000");
                return;
            }
        }

        // Recopilación de datos
        let datos = {
            accion: accion,
            datos: {
                id: parseInt(id.value),
                numero_cedula: cedula.value,
                nombres_apellidos: nombre.value,
                fecha_nacimiento: fechaNacimiento.value,
                celular: celular.value,
                direccion_exacta: direccionExacta.value, // Asegúrate de que este ID sea correcto
                lugar_estudio: lugarEstudio.value,
                grado: grado.value,
                seccion: seccion.value,
                enfermedades_alergias: enfermedadesAlergias.value,
                tipo_sangre: tipoSangre.value,
                historial_deportivo: historialDeportivo.value,
                perfil: perfil.value,
                posicion: posicion.value,
                otras_actividades: otrasActividades.value,
                lugar_actividades: lugarActividades.value,
                personas_vive: personasVive.value,
                representante: representante
            }
        };
        console.log(datos)
        // Llamada a la función para enviar los datos
        let data = await consultar("jugadores", datos);
        if (data !== null && typeof data !== 'undefined') {
            if (data.message) {
                mostrarNotificacion(data.message, "#FF0000");
            } else if (data.error) {
                mostrarNotificacion(data.error, "#FF0000");
            } else {
                // Limpieza de campos tras éxito
                limpiarCampos();
                mostrarNotificacion("Persona " + (accion === 'insertar' ? 'Registrada' : 'Modificada'), "linear-gradient(to right, #00b09b, #96c93d)");
            }
        } else {
            mostrarNotificacion("No se encontró ningún " + error, "#FF0000");
        }

    } catch (e) {
        mostrarNotificacion("Error: " + e, "#FF0000");
        console.error('Error:', e);
    }
}

function limpiarCampos() {
    let campos = ["idPersona", "cedulaPersona", "nombrePersona", "fechaNacimiento", "celular", "direccionExacta", "lugarEstudio", "grado", "seccion", 
        "enfermedadesAlergias", "tipoSangre", "historialDeportivo", "perfil", "posicion", "otrasActividades", "lugarActividades", "personasVive", 
        "nombreRepresentante", "telefonoRepresentante", "celularRepresentante", "direccionRepresentante", "numeroCedulaRepresentante", 
        "parentesco", "trabajoRepresentante", "facebookRepresentante", "instagramRepresentante","direccionPersona",
        "tipoRepresentante","facebookJugador","instagramJugador"];

    for(const campo of campos){
        try{
            document.getElementById(campo).value = '';
        }catch{
            continue;
        }
    }
}



var dataTable;
var dataTableIsInitialized = false;
var numeroPorPagona = 10;

const dataTableOptions = {
    scrollY: 'auto',  // Ajusta la altura automáticamente
    scrollCollapse: true,  // Permite colapsar la tabla si hay menos registros
    columnDefs: [
        { className: "centered", targets: [0, 1, 2, 3, 4, 5] }
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
        console.log(datos)
        let content = ``;
        datos.forEach((dato, index) => {
            content += `
                 <tr>
                    <td>${index + 1}</td>
                    <td>${dato.numero_cedula != null && typeof dato.numero_cedula !== 'undefined' ? dato.numero_cedula : ''}</td>
                    <td>${dato.nombres_apellidos != null && typeof dato.nombres_apellidos !== 'undefined' ? dato.nombres_apellidos : ''}</td>
                    <td>${dato.celular != null && typeof dato.celular !== 'undefined' ? dato.celular : ''}</td>
                    <td>${dato.direccion_exacta != null && typeof dato.direccion_exacta !== 'undefined' ? dato.direccion_exacta : ''}</td>
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