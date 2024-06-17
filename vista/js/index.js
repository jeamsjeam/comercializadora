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
        } 
    });

    let fecha = new Date(); //Fecha actual
    let mes = fecha.getMonth()+1; //obteniendo mes
    let dia = fecha.getDate(); //obteniendo dia
    let ano = fecha.getFullYear(); //obteniendo año
    if(dia<10)
        dia='0'+dia; //agrega cero si el menor de 10
    if(mes<10)
        mes='0'+mes //agrega cero si el menor de 10
    document.getElementById('fechaInicio').value=ano+"-"+mes+"-"+dia;
    document.getElementById('fechaFin').value=ano+"-"+mes+"-"+dia;
    
    await consultarFacturas()
});

async function consultarFacturas() {
	try{    
        await Loading(true)

        var spanVentas = document.getElementById("spanVentas")
        var spanCompras = document.getElementById("spanCompras")

        spanVentas.innerHTML = 0
        spanCompras.innerHTML = 0

        spanVentas.className = "badge bg-danger rounded-pill"
        spanCompras.className = "badge bg-danger rounded-pill"

        let datos = {
            accion: "obtenerCantidadVentas",
            datos: {
                fecha_inicio: document.getElementById("fechaInicio").value,
                fecha_fin: document.getElementById("fechaFin").value
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
                            spanVentas.className = "badge bg-primary rounded-pill"
                        }else{ 
                            spanCompras.innerHTML = data[i].cantidad
                            spanCompras.className = "badge bg-primary rounded-pill"
                        }
                    }
                } 
			}
		}else{
			mostrarNotificacion("No se encontro datos","#FF0000") 
		}
        await Loading(false)
	}catch(e){
        await Loading(false)
		mostrarNotificacion("Error:", e,"#FF0000") 
		console.error('Error:', e);
	}
}