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
    
});
