<?php
    // Permitir solicitudes desde cualquier origen
    header("Access-Control-Allow-Origin: *");
    // Permitir los métodos de solicitud especificados
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
    // Permitir los encabezados especificados
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    // Permitir que las cookies se incluyan en las solicitudes (si es necesario)
    header("Access-Control-Allow-Credentials: true");
    // Establecer el tipo de contenido de la respuesta como JSON
    header("Content-Type: application/json");

    function obtenerUno($sql) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();
        
        try{
            // Consulta a la base de datos
            $consulta = $db->consulta($sql);
        
            $resultado = null;
        
            if ($consulta !== null && $consulta->num_rows > 0) {
                // Obtener el primer resultado
                $fila = $consulta->fetch_assoc();
                
                // Almacenar la consulta en un diccionario
                $resultado = $fila;
            }
        
            // Cerrar la conexión manualmente
            $db->cerrar();
        
            return $resultado;
        }catch (Exception $e) {
             // Cerrar la conexión manualmente
             $db->cerrar();

            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    function ObtenerVarios($sql) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();

        try{         
            // Consulta a la base de datos
            $consulta = $db->consulta($sql);

            $resultado = null;

            if ($consulta !== null && $consulta->num_rows > 0) {
                // Almacenar la consulta en un diccionario
                while($fila = $consulta->fetch_assoc()) {
                    $resultado[] = $fila;
                }
            }

            // Cerrar la conexión manualmente
            $db->cerrar();
            return $resultado;
        }catch (Exception $e) {
             // Cerrar la conexión manualmente
             $db->cerrar();

            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    function insertarUno($sql,$tabla) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();
        try {

            $resultado = $db->consulta($sql);
    
            // Verificar si la consulta se ejecutó correctamente
            if ($resultado === true) {
                // Obtener el ID del nuevo registro insertado
                $id_insertado['id'] = $db->getConexion()->insert_id;
                $db->cerrar();

                // Consultar y devolver el registro insertado
                return obtenerPorId($id_insertado,$tabla);
            } else {
                $db->cerrar();
                // Si la consulta falla, devolver un mensaje de error
                return ['error' => 'Error al insertar el registro'];
            }
        } catch (Exception $e) {
             // Cerrar la conexión manualmente
             $db->cerrar();

            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }
    }

    function insertarVarios($datos,$tabla) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();

        try {

            $resultado = $db->consulta($sql);
    
            /// Verificar si la consulta se ejecutó correctamente
            if ($resultado === true) {
                // Obtener el rango de IDs asignados a los registros insertados
                $primer_id = $db->getConexion()->insert_id;
                $db->cerrar();
                $ultimo_id = $primer_id + count($datos) - 1;

                // Consultar y devolver los registros insertados
                $ids_insertados = range($primer_id, $ultimo_id);
                return ObtenerPorListaId($ids_insertados,$tabla);
            } else {
                $db->cerrar();
                // Si la consulta falla, devolver un mensaje de error
                return ['error' => 'Error al insertar los registros'];
            }
        } catch (Exception $e) {
             // Cerrar la conexión manualmente
             $db->cerrar();

            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }
    }

    function actualizarUno($sql,$tabla) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();
    
        try {

            $resultado = $db->consulta($sql);
    
            // Verificar si la consulta se ejecutó correctamente
            if ($resultado === true) {
                $db->cerrar();
                
                // Consultar y devolver el registro actualizado
                return obtenerPorId($datos,$tabla);
            } else {
                $db->cerrar();

                // Si la consulta falla, devolver un mensaje de error
                return ['error' => 'Error al actualizar el registro'];
            }
        } catch (Exception $e) {
            // Cerrar la conexión manualmente
            $db->cerrar();
    
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }
    }
    
    function actualizarVarios($datos,$tabla) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();        
    
        try {
            // Iniciar transacción
            $db->consulta("START TRANSACTION");
            
            // Lista para almacenar los IDs actualizados
            $listaIds = [];
    
            // Se recorre el objeto procesado y se construye la query
            foreach ($datos as $dato) {
                // Consulta a la base de datos para verificar si el ID existe
                $resultadoExiste = obtenerPorId($dato,$tabla);

                // Verificar si el ID existe en la base de datos
                if ($resultadoExiste === null || $resultadoExiste["id"] === null) {
                    // Si el ID no existe, hacer rollback y devolver un mensaje de error
                    $db->consulta("ROLLBACK");
                    $db->cerrar();
                    return ['error' => 'El ID '.$dato['id'].' no existe en la base de datos'];
                }
 
                $resultado_update = $db->consulta($dato["sql"]);
    
                // Verificar si la consulta de actualización se ejecutó correctamente
                if ($resultado_update !== true) {
                    // Si la consulta falla, hacer rollback y devolver un mensaje de error
                    $db->consulta("ROLLBACK");
                    $db->cerrar();
                    return ['error' => 'Error al actualizar el registro con ID '.$dato['id']];
                }
                
                // Agregar el ID a la lista de IDs actualizados
                $listaIds[] = $dato['id'];
            }
    
            // Si todas las actualizaciones fueron exitosas, realizar el commit
            $db->consulta("COMMIT");
            $db->cerrar();
    
            // Consultar y devolver los registros actualizados
            return obtenerPorListaId($listaIds,$tabla);
    
        } catch (Exception $e) {
            // Cerrar la conexión manualmente
            $db->cerrar();
    
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ' . $e->getMessage()];
        }
    }   

    function eliminarUno($sql,$tabla) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();
    
        try {
            $registro = obtenerPorId($datos,$tabla);

            $resultado = $db->consulta($sql);
    
            // Verificar si la consulta se ejecutó correctamente
            if ($resultado === true) {
                $db->cerrar();

                // Consultar y devolver el registro actualizado
                return $registro;
            } else {
                $db->cerrar();

                // Si la consulta falla, devolver un mensaje de error
                return ['error' => 'Error al eliminar el registro'];
            }
        } catch (Exception $e) {
            // Cerrar la conexión manualmente
            $db->cerrar();
    
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }
    }
    
    function eliminarVarios($datos,$tabla) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();

        try {
            // Consulta a la base de datos
            $sql = "";
            $listaIds = [];
            // Se recorre el objeto procesado y se construye la query

            $sql = "DELETE FROM ".$tabla." WHERE id IN (";

            for ($i = 0; $i < count($datos); $i++){
                // Consulta a la base de datos
                $sql .= $datos[$i]['id'].",";
                $listaIds[$i] = $datos[$i]['id'];
            }
            $sql = rtrim($sql, ',').")";

            $registros = obtenerPorListaId($listaIds,$tabla);

            if($registros === null || $registros[0] === null || $registros[0]['id']=== null){
                return ['error' => 'Al eliminar los registros, no se encontraron registros'];
            }

            $resultado = $db->consulta(rtrim($sql, ','));
    
            /// Verificar si la consulta se ejecutó correctamente
            if ($resultado === true) {
                $db->cerrar();

                // Consultar y devolver los registros insertados
                return $registros;
            } else {
                $db->cerrar();

                // Si la consulta falla, devolver un mensaje de error
                return ['error' => 'Al eliminar los registros'];
            }
        } catch (Exception $e) {
             // Cerrar la conexión manualmente
             $db->cerrar();

            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }
    }

?>