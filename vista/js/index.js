document.addEventListener("DOMContentLoaded", async function () {

    // Se revisa si las tasas están listas para mostrarlas en el HTML
    document.addEventListener('tasasListas', function(event) {
        // Aquí puedes continuar con la lógica que depende de la variable tasas
        if (typeof event.detail !== 'undefined' && event.detail !== null && event.detail.length > 0) {
            let contenido = ``;
            for (let i = 0; i < tasas.length; i++) {

                contenido += `
                    <li class="list-group-item d-flex justify-content-between align-items-center px-5">
                      ${tasas[i].moneda}
                      <span class="badge bg-success rounded-pill" style="font-size: 1.2rem;">${formatoDecimalString(tasas[i].tasa)}</span>
                    </li>`;
            }
            document.getElementById('informacionTasas').innerHTML = contenido;

            contenido = ``;
            for (let i = 0; i < monedas.length; i++) {
                contenido += `
                    <li class="list-group-item d-flex justify-content-between align-items-center px-5">
                      ${monedas[i].nombre} <div>`
                if(monedas[i].principal === '1'){
                    contenido += `<span class="badge bg-success rounded-pill mx-3" style="font-size: 0.8rem;">Principal</span>`
                }
                contenido += `<button class="btn btn-sm btn-primary" onclick="ModificarMoneda(${monedas[i].id})"><i class="bi bi-check2-circle"></i></button>
                        </div>
                    </li>`;
            }
            document.getElementById('informacionMonedas').innerHTML = contenido;
        } 

        let tasaRegistrada = JSON.parse(localStorage.getItem('tasa'));
            if (typeof tasaRegistrada !== 'undefined' &&  tasaRegistrada !== null && typeof tasaRegistrada.tasa !== 'undefined' && tasaRegistrada.tasa !== null) {
                mostrarNotificacion("Tasas Registradas", "linear-gradient(to right, #00b09b, #96c93d)"); 
            }

    });

    let monedaModificada = JSON.parse(localStorage.getItem('monedaModificada'));
    if (typeof monedaModificada !== 'undefined' &&  monedaModificada !== null && typeof monedaModificada.nombre !== 'undefined' && monedaModificada.nombre !== null) {
        mostrarNotificacion("Moneda " + monedaModificada.nombre + " modificada como principal", "linear-gradient(to right, #00b09b, #96c93d)"); 
    }
    localStorage.removeItem('monedaModificada');

    InicializarFechasFacturas()
    
    await consultarFacturas()
});

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

async function consultarFacturas() {
	try{    
        await Loading(true)

        var spanVentas = document.getElementById("spanVentas")
        var spanCompras = document.getElementById("spanCompras")

        spanVentas.innerHTML = 0
        spanCompras.innerHTML = 0

        spanVentas.className = "badge bg-danger rounded-pill"
        spanCompras.className = "badge bg-danger rounded-pill"

        let fechaInicio = document.getElementById("fechaInicio").value
        let fechaFin = document.getElementById("fechaFin").value

        let datos = {
            accion: "obtenerCantidadVentas",
            datos: {
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin
            }
        };

		let data = await consultar("facturas",datos);

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
                if(typeof data[0].factura !== 'undefined' && data[0].factura !== null){
                    for(let i = 0; i < data.length; i++){
                        if(data[i].factura === "1"){
                            spanVentas.innerHTML = data[i].cantidad
                            if(data[i].cantidad !== '0'){
                                spanVentas.className = "badge bg-primary rounded-pill"
                            }else{
                                spanVentas.className = "badge bg-danger rounded-pill"
                            }
                            
                        }else{ 
                            spanCompras.innerHTML = data[i].cantidad
                            if(data[i].cantidad !== '0'){
                                spanCompras.className = "badge bg-primary rounded-pill"
                            }else{
                                spanCompras.className = "badge bg-danger rounded-pill"
                            }
                        }
                    }
                } 
			}
		}else{
			mostrarNotificacion("No se encontro datos","#FF0000") 
		}
        await DatosGrafica(fechaInicio, fechaFin);
        await Loading(false)
	}catch(e){
        await Loading(false)
		mostrarNotificacion("Error:", e,"#FF0000") 
		console.error('Error:', e);
	}
}

async function DatosGrafica(fechaInicio, fechaFin){

    try {

        let dataTipo1 = []
        let dataTipo2 = []
        let etiquetas = []

        for(let i = 0; i < 2; i++){

            let datos = {
                accion: "obtenerCantidadPorTipo",
                datos: {
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin,
                    tipo: (i+1),
                    estado: "Pagada"
                }
            };
            let data = await consultar("facturas",datos);
        
            let inicio = new Date(fechaInicio)
            let fin = new Date(fechaFin)

            while(fin.getTime() >= inicio.getTime()){
                inicio.setDate(inicio.getDate() + 1);
                let dia = inicio.getDate()
                let mes = (inicio.getMonth() + 1)
                let anio = inicio.getFullYear()

                if(dia<10)
                    dia='0'+dia;
                if(mes<10)
                    mes='0'+mes
                
                let iteracionActual = dia + '-' + mes + '-' + anio

                if(i === 0){
                    etiquetas.push(iteracionActual)
                }
                
                let datoExiste = null

                if(data !== null && typeof data !== 'undefined' && data.length > 0 && typeof data[0].fecha !== 'undefined'  && data[0].fecha !== null){
                    datoExiste = data.find(x => x.fecha === iteracionActual)
                }

                if(typeof datoExiste !== 'undefined' && datoExiste !== null){
                    if(parseInt(datoExiste.tipo) == 1){
                        dataTipo1.push(parseInt(datoExiste.cantidad))
                    }else{
                        dataTipo2.push(parseInt(datoExiste.cantidad))
                    }
                    
                }else{
                    if((i+1) == 1){
                        dataTipo1.push(0)
                    }else{
                        dataTipo2.push(0)
                    }
                }
            }
        }

        Grafica(etiquetas, dataTipo1,dataTipo2)
    } catch (e) {
        mostrarNotificacion("Error:", e,"#FF0000") 
		console.error('Error:', e);
    }

    
}

async function ModificarMoneda(id){

    try{

        moneda = monedas.find((element) => element.id > id);

        let datos = {
            accion: "actualizar",
            datos: {
                id: id,
                principal: 1
            }
        };

        let data = await consultar("monedas",datos);
        
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
                mostrarNotificacion("Moneda: " + data.usuario,"linear-gradient(to right, #00b09b, #96c93d)") 
                let eliminado = {
                    moneda: data.nombre
                }

                localStorage.setItem('modificarMoneda', JSON.stringify(eliminado))
                window.location.href = "index.html";
            }
        }else{
            mostrarNotificacion("No se pudo eliminar","#FF0000") 
        }
    }catch(e){
        mostrarNotificacion("Error:", e,"#FF0000") 
        console.error('Error:', e);
    }
    
}

async function AbrirModalCrearTasa(){
    await ObtenerSelect("monedas", "monedas-select", "moneda", monedas);
    let modalTasas = new bootstrap.Modal(document.getElementById('modalCrearTasa'));
    modalTasas.show();
}

async function CrearTasa(){
    let moneda = document.querySelector('select[name="rol"]').selectedOptions[0].value
    let tasa = document.getElementById("tasaNueva").value
}

var myChart = null

function Grafica(etiquetas, datosTipo1, datosTipo2){
    let ctx = document.getElementById('myChart');

    let compras = {
                    label: 'Ventas',
                    data: datosTipo1,
                    backgroundColor: 'rgba(0,255,0)',
                    borderWidth: 1
                };
    let ventas = {
                    label: 'Compras',
                    data: datosTipo2,
                    backgroundColor: 'rgba(0,0,255)',
                    borderWidth: 1
                };

    if (myChart) {
        myChart.destroy();
    }

    myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: etiquetas,
            datasets: [
                compras, 
                ventas
            ]
        },
        options: {
            scales: {
              y: {
                ticks: {
                  stepSize: 1
                },
              }
            }
          }
      });
}