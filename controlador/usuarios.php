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

    include 'utilidades.php';
    include 'Conexion.php';

    function obtenerPorId($datos) {
        // Crear instancia de la clase Conexion
        $db = new Conexion();
        
        try{

            $datosProcesados = procesarDatos($datos);

            // Consulta a la base de datos
            $sql = "SELECT * FROM usuarios WHERE id = ".$datosProcesados['id'];
            $consulta = $db->consulta($sql);
        
            $resultado = null;
        
            if ($consulta !== null && $consulta->num_rows > 0) {
                // Obtener el primer resultado
                $fila = $consulta->fetch_assoc();
                
                // Almacenar la consulta en un diccionario
                $resultado = [
                    'id' => $fila["id"],
                    'usuario' => $fila["usuario"],
                    'correo' => $fila["correo"]
                    'clave' => $fila["clave"],
                    'rol_id' => $fila["rol_id"],
                    'fecha_creacion' => $fila["fecha_creacion"]
                    'estado' => $fila["estado"]
                ];
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

            $datosProcesados = procesarDatos($datos);

            // Consulta a la base de datos
            $sql = "SELECT * FROM usuarios WHERE usuario = ".$datosProcesados['usuario'];
            $consulta = $db->consulta($sql);
        
            $resultado = null;
        
            if ($consulta !== null && $consulta->num_rows > 0) {
                // Obtener el primer resultado
                $fila = $consulta->fetch_assoc();
                
                // Almacenar la consulta en un diccionario
                $resultado = [
                    'id' => $fila["id"],
                    'usuario' => $fila["usuario"],
                    'correo' => $fila["correo"]
                    'clave' => $fila["clave"],
                    'rol_id' => $fila["rol_id"],
                    'fecha_creacion' => $fila["fecha_creacion"]
                    'estado' => $fila["estado"]
                ];
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

            $datosProcesados = procesarDatos($datos);

            // Consulta a la base de datos
            $sql = "SELECT * FROM usuarios WHERE usuario = ".$datosProcesados['usuario']." AND clave = ".$datosProcesados['usuario'];
            $consulta = $db->consulta($sql);
        
            $resultado = null;
        
            if ($consulta !== null && $consulta->num_rows > 0) {
                // Obtener el primer resultado
                $fila = $consulta->fetch_assoc();
                
                // Almacenar la consulta en un diccionario
                $resultado = [
                    'id' => $fila["id"],
                    'usuario' => $fila["usuario"],
                    'correo' => $fila["correo"]
                    'clave' => $fila["clave"],
                    'rol_id' => $fila["rol_id"],
                    'fecha_creacion' => $fila["fecha_creacion"]
                    'estado' => $fila["estado"]
                ];
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
            $sql = "SELECT * FROM usuarios";
            $consulta = $db->consulta($sql);

            $resultado = null;

            if ($consulta !== null && $consulta->num_rows > 0) {
                // Almacenar la consulta en un diccionario
                while($fila = $consulta->fetch_assoc()) {
                    // Almacenar la consulta en un diccionario
                    $resultado = [
                        'id' => $fila["id"],
                        'usuario' => $fila["usuario"],
                        'correo' => $fila["correo"]
                        'clave' => $fila["clave"],
                        'rol_id' => $fila["rol_id"],
                        'fecha_creacion' => $fila["fecha_creacion"]
                        'estado' => $fila["estado"]
                    ];
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
                    $resultado = [
                        'id' => $fila["id"],
                        'usuario' => $fila["usuario"],
                        'correo' => $fila["correo"]
                        'clave' => $fila["clave"],
                        'rol_id' => $fila["rol_id"],
                        'fecha_creacion' => $fila["fecha_creacion"]
                        'estado' => $fila["estado"]
                    ];
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
            $datosProcesados = procesarDatos($datos);

            // Consulta a la base de datos
            $sql = "INSERT INTO usuarios (usuario, correo, clave, rol_id, fecha_creacion, estado) VALUES ";
            $sql .= "('".$datosProcesados['nombre']."', '".$datosProcesados['correo']."','".$datosProcesados['clave']."', ".$datosProcesados['rol_id'].", NOW(), '".$datosProcesados['estado']."')";
    
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
            // Se procesan los datos
            $datosProcesados = procesarDatos($datos);

            // Consulta a la base de datos
            $sql = "INSERT INTO usuarios (usuario, correo, clave, rol_id, fecha_creacion, estado) VALUES ";

            // Se recorre el objeto procesado y se construye la query
            for ($i = 0; $i < count($datosProcesados); $i++){

                $sql .= "('".$datosProcesados[$i]['nombre']."', '".$datosProcesados[$i]['correo']."','".$datosProcesados[$i]['clave']."', ".$datosProcesados[$i]['rol_id'].", NOW(), '".$datosProcesados[$i]['estado']."')";
            }

            $resultado = $db->consulta(rtrim($sql, ','));
    
            /// Verificar si la consulta se ejecutó correctamente
            if ($resultado === true) {
                // Obtener el rango de IDs asignados a los registros insertados
                $primer_id = $db->getConexion()->insert_id;
                $db->cerrar();
                $ultimo_id = $primer_id + count($datosProcesados) - 1;

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
            $datosProcesados = procesarDatos($datos);
    
            // Consulta a la base de datos
            $sql = "UPDATE usuarios SET ";
            $sql .= "usuario = '".$datosProcesados['usuario']."', ";
            $sql .= "correo = '".$datosProcesados['correo']."', ";
            $sql .= "clave = '".$datosProcesados['clave']."', ";
            $sql .= "rol_id = '".$datosProcesados['rol_id']."', ";
            $sql .= "estado = '".$datosProcesados['estado']."' ";
            $sql .= "WHERE id = ".$datosProcesados['id'];
            
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
    
            // Se procesan los datos
            $datosProcesados = procesarDatos($datos);
    
            // Lista para almacenar los IDs actualizados
            $listaIds = [];
    
            // Se recorre el objeto procesado y se construye la query
            foreach ($datosProcesados as $dato) {
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
            $datosProcesados = procesarDatos($datos);
            
            $registro = obtenerPorId($datos);

            // Consulta a la base de datos
            $sql = "DELETE FROM usuarios WHERE id = ".$datosProcesados['id'];
            
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
            // Se procesan los datos
            $datosProcesados = procesarDatos($datos);

            // Consulta a la base de datos
            $sql = "";
            $listaIds = [];
            // Se recorre el objeto procesado y se construye la query

            $sql = "DELETE FROM usuarios WHERE id IN (";

            for ($i = 0; $i < count($datosProcesados); $i++){
                // Consulta a la base de datos
                $sql .= $datosProcesados[$i]['id'].",";
                $listaIds[$i] = $datosProcesados[$i]['id'];
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
        $accion= $_POST['accion'] ?? null;

        switch ($accion){
            case "obtenerPorId":
                $datos = $_POST['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }

                echo json_encode(ObtenerPorId($datos));

                break;
            case "obtenerPorUsuario":
                $datos = $_POST['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }

                echo json_encode(obtenerPorUsuario($datos));

                break;
            case "obtenerPorUsuarioClave":
                $datos = $_POST['datos'] ?? null;
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
                $datos = $_POST['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(insertar($datos));

                break;
            case "insertarLista":
                $datos = $_POST['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(insertarLista($datos));

                break;
            case "actualizar":
                $datos = $_POST['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(actualizar($datos));

                break;
            case "actualizarLista":
                $datos = $_POST['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(actualizarLista($datos));

                break;
            case "eliminar":
                $datos = $_POST['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(eliminar($datos));

                break;
            case "eliminarLista":
                $datos = $_POST['datos'] ?? null;
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
