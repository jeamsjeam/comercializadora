<?php

    include 'utilidades/utilidades.php';
    include 'utilidades/Conexion.php';

    function obtenerPorId($datos) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();
        
        try{
            // Consulta a la base de datos
            $sql = "SELECT us.*, ro.nombre as 'rolnombre' FROM usuarios us ";
            $sql .= " JOIN roles ro ON ro.id = us.rol_id ";
            $sql .= " WHERE us.id = ".$datos['id'];
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

    function obtenerPorUsuario($datos) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();
        
        try{
            // Consulta a la base de datos
            $sql = "SELECT us.*, ro.nombre as 'rolnombre' FROM usuarios us ";
            $sql .= " JOIN roles ro ON ro.id = us.rol_id ";
            $sql .= " WHERE usuario = '".$datos['usuario']."'";
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

    function obtenerPorUsuarioClave($datos) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();
        
        try{
            // Consulta a la base de datos
            $sql = "SELECT us.*, ro.nombre as 'rolnombre' FROM usuarios us ";
            $sql .= " JOIN roles ro ON ro.id = us.rol_id ";
            $sql .= " WHERE usuario = '".$datos['usuario']."' AND clave = '".$datos['clave']."'";
            $consulta = $db->consulta($sql);
        
            $resultado = null;
        
            if ($consulta !== null && $consulta->num_rows > 0) {
                // Obtener el primer resultado
                $fila = $consulta->fetch_assoc();
                
                // Almacenar la consulta en un diccionario
                $resultado[] = $fila;
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

    function ObtenerTodos() {
        // Crear instancia de la clase Conexion
        $db = new Conexion();

        try{         
            // Consulta a la base de datos
            $sql = "SELECT us.*, ro.nombre as 'rolnombre' FROM usuarios us ";
            $sql .= " JOIN roles ro ON ro.id = us.rol_id ";
            $consulta = $db->consulta($sql);

            $resultado = null;

            if ($consulta !== null && $consulta->num_rows > 0) {
                // Almacenar la consulta en un diccionario
                while($fila = $consulta->fetch_assoc()) {
                    // Almacenar la consulta en un diccionario
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

    function ObtenerPorListaId($datos) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();

        try{
            // Consulta a la base de datos
            $sql = "SELECT * FROM usuarios WHERE id in (";
            foreach ($datos as $id) {
                $sql .= $id.",";
            }
            $sql = rtrim($sql, ',').")";

            $consulta = $db->consulta($sql);

            $resultado = null;

            if ($consulta !== null && $consulta->num_rows > 0) {
                // Almacenar la consulta en un diccionario
                while($fila = $consulta->fetch_assoc()) {
                    $resultado = $fila;
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

    function insertar($datos) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();
        try {
            $datosAdministrador = [
                'usuario' => $datos["usuarioAdministrador"],
                'clave' => $datos["claveAdministrador"],
            ];

            $usuarioAdministrador = obtenerPorUsuarioClave($datosAdministrador);

            if($usuarioAdministrador == null || $usuarioAdministrador["rolnombre"] != 'Administrador'){
                return ['error' => 'No se encontro usuario administrador'];
            }

            // Consulta a la base de datos
            $sql = "INSERT INTO usuarios (usuario, correo, clave, rol_id, fecha_creacion, estado) VALUES ";
            $sql .= "('".$datos['usuario']."', '".$datos['correo']."','".$datos['clave']."', ".$datos['rol_id'].", NOW(), '".$datos['estado']."')";
    
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

    function insertarLista($datos) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();

        try {
            $datosAdministrador = [
                'usuario' => $datos[0]["usuarioAdministrador"],
                'clave' => $datos[0]["claveAdministrador"],
            ];

            $usuarioAdministrador = obtenerPorUsuarioClave($datos);

            if($usuarioAdministrador == null){
                return ['error' => 'No se encontro usuario administrador'];
            }

            // Consulta a la base de datos
            $sql = "INSERT INTO usuarios (usuario, correo, clave, rol_id, fecha_creacion, estado) VALUES ";

            // Se recorre el objeto procesado y se construye la query
            for ($i = 0; $i < count($datos); $i++){

                $sql .= "('".$datos[$i]['usuario']."', '".$datos[$i]['correo']."','".$datos[$i]['clave']."', ".$datos[$i]['rol_id'].", NOW(), '".$datos[$i]['estado']."')";
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
                return ObtenerPorListaId($ids_insertados);
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

    function actualizar($datos) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();
    
        try {
            // Consulta a la base de datos
            $sql = "UPDATE usuarios SET ";
            $sql .= "usuario = '".$datos['usuario']."', ";
            $sql .= "correo = '".$datos['correo']."', ";
            $sql .= "clave = '".$datos['clave']."', ";
            $sql .= "rol_id = '".$datos['rol_id']."', ";
            $sql .= "estado = '".$datos['estado']."' ";
            $sql .= "WHERE id = ".$datos['id'];
            
            $resultado = $db->consulta($sql);
    
            // Verificar si la consulta se ejecutó correctamente
            if ($resultado === true) {
                $db->cerrar();
                
                // Consultar y devolver el registro actualizado
                return obtenerPorId($datos);
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
    
    function actualizarLista($datos) {
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
       
                $sql = "UPDATE usuarios SET ";
                $sql .= "usuario = '".$dato['usuario']."', ";
                $sql .= "correo = '".$dato['correo']."', ";
                $sql .= "clave = '".$dato['clave']."', ";
                $sql .= "rol_id = '".$dato['rol_id']."', ";
                $sql .= "estado = '".$dato['estado']."' ";
                $sql .= "WHERE id = ".$dato['id'];
                $resultado_update = $db->consulta($sql);
    
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
            return obtenerPorListaId($listaIds);
    
        } catch (Exception $e) {
            // Cerrar la conexión manualmente
            $db->cerrar();
    
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ' . $e->getMessage()];
        }
    }    
    
    function eliminar($datos) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();
    
        try {
            $registro = obtenerPorId($datos);

            // Consulta a la base de datos
            $sql = "DELETE FROM usuarios WHERE id = ".$datos['id'];
            
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
    
    function eliminarLista($datos) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();

        try {
            // Consulta a la base de datos
            $sql = "";
            $listaIds = [];
            // Se recorre el objeto procesado y se construye la query

            $sql = "DELETE FROM usuarios WHERE id IN (";

            for ($i = 0; $i < count($datos); $i++){
                // Consulta a la base de datos
                $sql .= $datos[$i]['id'].",";
                $listaIds[$i] = $datos[$i]['id'];
            }
            $sql = rtrim($sql, ',').")";

            $registros = ObtenerPorListaId($listaIds);

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

        $data = json_decode($datosRecibidos, true);

        $accion = $data['accion'] ?? null;

        switch ($accion){
            case "obtenerPorId":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(ObtenerPorId($datos));

                break;
            case "obtenerPorUsuario":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(obtenerPorUsuario($datos));

                break;
            case "obtenerPorUsuarioClave":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(obtenerPorUsuarioClave($datos));

                break;
            case "obtenerTodos":

                echo json_encode(ObtenerTodos());

                break;
            case "insertar":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(insertar($datos));

                break;
            case "insertarLista":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(insertarLista($datos));

                break;
            case "actualizar":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(actualizar($datos));

                break;
            case "actualizarLista":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(actualizarLista($datos));

                break;
            case "eliminar":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(eliminar($datos));

                break;
            case "eliminarLista":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(eliminarLista($datos));

                break;
            default:
                echo json_encode(['error' => 'Valor de accion no válido: ' . $accion]);
                break;
        }
    } else {
        echo json_encode(['error' => 'No se ha recibido la solicitud POST']);
    }

?>
