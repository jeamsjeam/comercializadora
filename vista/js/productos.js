document.addEventListener("DOMContentLoaded", async function () {
    await DatosTabla()
});

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

var dataTable;
var dataTableIsInitialized = false;
var numeroPorPagona = 10;

const dataTableOptions = {
    //scrollX: "2000px",
    //lengthMenu: [5, 10, 15, 20, 100, 200, 500],
    columnDefs: [
        { className: "centered", targets: [0, 1, 2, 3, 4, 5, 6] },
        //{ orderable: false, targets: [1, 2] },
        //{ searchable: false, targets: [1] }
        //{ width: "50%", targets: [0] }
    ],
    pageLength: numeroPorPagona,
    destroy: true,
    language: {
        lengthMenu: "Mostrar _MENU_ registros por página",
        zeroRecords: "Ningún reposo encontrado",
        info: "Mostrando de _START_ a _END_ de un total de _TOTAL_ registros",
        infoEmpty: "Ningún reposo encontrado",
        infoFiltered: "(filtrados desde _MAX_ registros totales)",
        search: "Buscar:",
        loadingRecords: "Cargando...",
        paginate: {
            first: "Primero",
            last: "Último",
            next: "Siguiente",
            previous: "Anterior"
        }
    },
    sScrollY: (250),
};

function initDataTable(datos) { 
    if (dataTableIsInitialized) {
        dataTable.destroy();
    }

    listUsers(datos);

    dataTable = $("#datatable_productos").DataTable(dataTableOptions);

    dataTableIsInitialized = true;
}

function listUsers(datos) {
    try {
        let content = ``;
        datos.forEach((dato, index) => {
            content += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${dato.nombre != null && typeof dato.nombre !== 'undefined' ? dato.nombre : ''}</td>
                    <td>${dato.descripcion != null && typeof dato.descripcion !== 'undefined' ? dato.descripcion : ''}</td>
                    <td>${dato.precio != null && typeof dato.precio !== 'undefined' ? dato.precio : ''}</td>
                    <td>${dato.stock != null && typeof dato.stock !== 'undefined' ? dato.stock : ''}</td>
                    <td>${dato.categoria != null && typeof dato.categoria !== 'undefined' ? dato.categoria : ''}</td>
                    <!-- <td><i class="fa-solid fa-check" style="color: green;"></i></td> -->
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="AbrirModalModificardato(${dato.id})"
                        ${dato.nombre != null && typeof dato.nombre !== 'undefined' ? '' : 'disabled'}
                        ><i class="bi bi-pen"></i></button>
                        <button class="btn btn-sm btn-danger" onclick="AbrirModalEliminardato(${dato.id})"
                        ${dato.nombre != null && typeof dato.nombre !== 'undefined' ? '' : 'disabled'}
                        ><i class="bi bi-trash3"></i></button>
                    </td>
                </tr>`;
        });
        tableBody_productos.innerHTML = content;
    } catch (ex) {
        alert(ex);
    }
}