<?php

    include 'utilidades/Conexion.php';
    include 'utilidades/utilidades.php';

    function obtenerPorId($datos,$tabla) {

        try{
            // Consulta a la base de datos
            $sql = "SELECT * FROM ".$tabla." WHERE id = ".$datos['id'];

            return obtenerUno($sql);

        }catch (Exception $e) {
             // Cerrar la conexión manualmente
             $db->cerrar();

            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    function ObtenerTodos($tabla) {

        try{         
            // Consulta a la base de datos
            $sql = "SELECT * FROM ".$tabla;
       
            return ObtenerVarios($sql);

        }catch (Exception $e) {

            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    function ObtenerPorListaId($datos,$tabla) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();

        try{
            // Consulta a la base de datos
            $sql = "SELECT * FROM ".$tabla." WHERE id in (";
            foreach ($datos as $id) {
                $sql .= $id.",";
            }
            $sql = rtrim($sql, ',').")";

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

    function insertar($datos,$tabla) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();
        try {
            // Consulta a la base de datos
            // Consulta a la base de datos
            $sql = "INSERT INTO ".$tabla." (nombre, cedula, extrangero, telefono, direccion, fecha_creacion, estado, tipo_persona_id) VALUES ";
            $sql .= "('".$datos['nombre']."', ";
            $sql .= "'".$datos['cedula']."', ";
            $sql .= $datos['extrangero'].", ";
            $sql .= "'".$datos['telefono']."', ";
            $sql .= "'".$datos['direccion']."', ";
            $sql .= "NOW(), ";
            $sql .= "'".$datos['estado']."', ";
            $sql .= $datos['tipo_persona_id'].") ";
    
            $resultado = $db->consulta($sql);
    
            // Verificar si la consulta se ejecutó correctamente
            if ($resultado === true) {
                // Obtener el ID del nuevo registro insertado
                $id_insertado['id'] = $db->getConexion()->insert_id;
                $db->cerrar();
                // Consultar y devolver el registro insertado
                return obtenerPorId($id_insertado);
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

    function insertarLista($datos,$tabla) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();

        try {
            // Consulta a la base de datos
            $sql = "INSERT INTO ".$tabla." (nombre, cedula, extrangero, telefono, direccion, fecha_creacion, estado, tipo_persona_id) VALUES ";

            // Se recorre el objeto procesado y se construye la query
            for ($i = 0; $i < count($datos); $i++){

                $sql .= "'".$datos[$i]['nombre']."', ";
                $sql .= "'".$datos[$i]['cedula']."', ";
                $sql .= $datos[$i]['extrangero'].", ";
                $sql .= "'".$datos[$i]['telefono']."', ";
                $sql .= "'".$datos[$i]['direccion']."', ";
                $sql .= "NOW(), ";
                $sql .= "'".$datos[$i]['estado']."', ";
                $sql .= $datos[$i]['tipo_persona_id']."), ";
            }

            $resultado = $db->consulta(rtrim($sql, ','));
    
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

    function actualizar($datos,$tabla) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();
    
        try {
            // Consulta a la base de datos
            $sql = "UPDATE ".$tabla." SET ";
            $sql .= "nombre = '".$datos['nombre']."', ";
            $sql .= "cedula = '".$datos['cedula']."', ";
            $sql .= "extrangero = ".$datos['extrangero'].", ";
            $sql .= "telefono = '".$datos['telefono']."', ";
            $sql .= "direccion = '".$datos['direccion']."', ";
            $sql .= "estado = '".$datos['estado']."', ";
            $sql .= "tipo_persona_id = ".$datos['tipo_persona_id']." ";
            $sql .= "WHERE id = ".$datos['id'];
            
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
    
    function actualizarLista($datos,$tabla) {
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
                $resultadoExiste = obtenerPorId($dato);

                // Verificar si el ID existe en la base de datos
                if ($resultadoExiste === null || $resultadoExiste["id"] === null) {
                    // Si el ID no existe, hacer rollback y devolver un mensaje de error
                    $db->consulta("ROLLBACK");
                    $db->cerrar();
                    return ['error' => 'El ID '.$dato['id'].' no existe en la base de datos'];
                }
    
                // Consulta a la base de datos para actualizar el registro
                $sql_update = "UPDATE ".$tabla." SET "
                $sql_update .= "nombre = '".$dato['nombre']."', ";
                $sql_update .= "cedula = '".$dato['cedula']."', ";
                $sql_update .= "extrangero = ".$dato['extrangero'].", ";
                $sql_update .= "telefono = '".$dato['telefono']."', ";
                $sql_update .= "direccion = '".$dato['direccion']."', ";
                $sql_update .= "estado = '".$dato['estado']."', ";
                $sql_update .= "tipo_persona_id = ".$dato['tipo_persona_id']." ";
                $sql_update .= " WHERE id = ".$dato['id'];
                $resultado_update = $db->consulta($sql_update);
    
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
    
    function eliminar($datos,$tabla) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();
    
        try {
            $registro = obtenerPorId($datos,$tabla);

            // Consulta a la base de datos
            $sql = "DELETE FROM ".$tabla." WHERE id = ".$datos['id'];
            
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
    
    function eliminarLista($datos,$tabla) {
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

    // Verificar si se ha enviado el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $datosRecibidos = $_POST['datos'] ?? null;
        if($datosRecibidos === null){
            echo json_encode(['error' => 'No se envio datos']);
            return;
        }

        $tabla = "personas";
        
        $data = json_decode($datosRecibidos, true);

        $accion = $data['accion'] ?? null;

        switch ($accion){
            case "obtenerPorId":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(ObtenerPorId($datos,$tabla));

                break;
            case "obtenerTodos":

                echo json_encode(ObtenerTodos($tabla));

                break;
            case "insertar":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(insertar($datos,$tabla));

                break;
            case "insertarLista":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(insertarLista($datos,$tabla));

                break;
            case "actualizar":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(actualizar($datos,$tabla));

                break;
            case "actualizarLista":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(actualizarLista($datos,$tabla));

                break;
            case "eliminar":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(eliminar($datos,$tabla));

                break;
            case "eliminarLista":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(eliminarLista($datos,$tabla));

                break;
            default:
                echo json_encode(['error' => 'Valor de accion no válido: ' . $accion]);
                break;
        }
    } else {
        echo json_encode(['error' => 'No se ha recibido la solicitud POST']);
    }

?>
