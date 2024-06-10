<?php

    function procesarDatos($datos) {
        $resultado = [];

        // Verificar si los datos no están vacíos y son un array
        if (!empty($datos) && is_array($datos)) {
            foreach ($datos as $clave => $valor) {
                // Verificar si $valor es un array o un objeto
                if (is_array($valor) || is_object($valor)) {
                    // Convertir el valor en un array asociativo
                    $valorArray = (array)$valor;
                    // Recorrer el array y agregar cada elemento al resultado
                    foreach ($valorArray as $subclave => $subvalor) {
                        $resultado[$clave][$subclave] = $subvalor;
                    }
                } else {
                    // Si no es un array o un objeto, agregar el valor directamente al resultado
                    $resultado[$clave] = $valor;
                }
            }
        } else {
            // Si los datos no son válidos, devolver un mensaje
            $resultado['mensaje'] = "No se recibieron datos válidos";
        }

        return $resultado;
    }

?>