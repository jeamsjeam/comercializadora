async function consultar(tabla,datos) {
    try {
        const cuerpoDatos = new URLSearchParams();
        for (const clave in datos) {
            if (typeof datos[clave] === 'object') {
                if (Array.isArray(datos[clave])) {
                    datos[clave].forEach((obj, indice) => {
                        for (const subclave in obj) {
                            cuerpoDatos.append(`${clave}[${indice}][${subclave}]`, obj[subclave]);
                        }
                    });
                } else {
                    for (const subclave in datos[clave]) {
                        cuerpoDatos.append(`${clave}[${subclave}]`, datos[clave][subclave]);
                    }
                }
            } else {
                cuerpoDatos.append(clave, datos[clave]);
            }
        }
        let url = 'http://localhost/comercializadora/controlador/' + tabla + '.php'
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: cuerpoDatos
        });
        const data = await response.json();
		console.log(data)
		return data;
    } catch (error) {
        console.error('Error:', error);
    }
}