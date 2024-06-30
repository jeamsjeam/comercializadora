<?php

    // Importar archivos php
    include 'Conexion.php';

    // headers que evitan el error de cors para tener acceso desde cualquier sitio
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

    // Funcion para consultar un unico registro
    // Solo revise un string que es la consulta sql
    function obtenerUno($sql) {

        // Crear instancia de la clase Conexion
        $db = new Conexion();
        
        try{
            // Consulta a la base de datos
            $consulta = $db->consulta($sql);
            
            // Variable resultado
            $resultado = null;
        
            // Se pregunt si la consulta no es null y tiene registros
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

    // Funcion para consultar varios registros
    // $sql: String que es la consulta sql
    function ObtenerVarios($sql) {

        // Crear instancia de la clase Conexion
        $db = new Conexion();

        try{         
            // Consulta a la base de datos
            $consulta = $db->consulta($sql);

            // Variable resultado
            $resultado = null;

            // Se pregunt si la consulta no es null y tiene registros
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

    // Funcion para insertar un unico registro
    // $sql: String que es la consulta sql
    // $tabla: String que es el nombre de la tabla
    function insertarUno($sql,$tabla) {

        // Crear instancia de la clase Conexion
        $db = new Conexion();
        try {
            // Consulta a la base de datos
            $resultado = $db->consulta($sql);

            // Verificar si la consulta se ejecutó correctamente
            if ($resultado === true) {
                // Obtener el ID del nuevo registro insertado
                $id_insertado['id'] = $db->getConexion()->insert_id;
                $db->cerrar();
                
                // Consultar y devolver el registro insertado
                return ObtenerPorId($id_insertado,$tabla);
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

    // Funcion para insertar varios registro
    // $sql: String que es la consulta sql
    // $datos: datos que se enviaron para ser insertados, se utilizan para saber la cantidad
    // $tabla: String que es el nombre de la tabla
    function insertarVarios($sql,$datos,$tabla) {

        // Crear instancia de la clase Conexion
        $db = new Conexion();

        try {
            // Consulta a la base de datos
            $resultado = $db->consulta($sql);
    
            /// Verificar si la consulta se ejecutó correctamente
            if ($resultado === true) {
                // Obtener el rango de IDs asignados a los registros insertados
                $primer_id = $db->getConexion()->insert_id;
                $db->cerrar();
                $ultimo_id = $primer_id + count($datos) - 1;

                // Consultar y devolver los registros insertados
                $ids_insertados = range($primer_id, $ultimo_id);

                // Consultar y devolver los registros insertados
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

    // Funcion para insertar varios registros
    // $sql: String que es la consulta sql
    // $datos: datos que se enviaron para ser insertados, se utilizan para saber la cantidad
    // $tabla: String que es el nombre de la tabla
    function insertarVariosExtra($sql,$datos,$tabla) {

        // Crear instancia de la clase Conexion
        $db = new Conexion();

        try {
            // Consulta a la base de datos
            $resultado = $db->consulta($sql);
    
            /// Verificar si la consulta se ejecutó correctamente
            if ($resultado === true) {
                // Obtener el rango de IDs asignados a los registros insertados
                $primer_id = $db->getConexion()->insert_id;
                $db->cerrar();
                $ultimo_id = $primer_id + count($datos) - 1;

                // Consultar y devolver los registros insertados
                $ids_insertados = range($primer_id, $ultimo_id);

                // Consultar y devolver los registros insertados
                return ObtenerPorListaIdExtra($ids_insertados,$tabla);
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

    // Funcion para actualizar un unico registro
    // $datos: datos que se enviaron para ser insertados, se utilizan para ser enviados a la funcion que consulta por id
    // $tabla: String que es el nombre de la tabla
    // $sql: String que es la consulta sql
    function actualizarUno($datos,$tabla,$sql) {

        // Crear instancia de la clase Conexion
        $db = new Conexion();
    
        try {
            // Consulta a la base de datos
            $resultado = $db->consulta($sql);
    
            // Verificar si la consulta se ejecutó correctamente
            if ($resultado === true) {
                $db->cerrar();
                
                // Consultar y devolver el registro actualizado
                return ObtenerPorId($datos,$tabla);
            } else {
                $db->cerrar();

                // Si la consulta falla, devolver un mensaje de error
                return ['error' => 'Error al actualizar el registro con ID '.$datos['id']];
            }
        } catch (Exception $e) {
            // Cerrar la conexión manualmente
            $db->cerrar();
    
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }
    }
    
    // Funcion para actualizar varios registros
    // $datos: datos que se enviaron para ser insertados, se utilizan para ser enviados a la funcion que consulta por id
    // $tabla: String que es el nombre de la tabla
    function actualizarVarios($datos,$tabla) {
        
        // Crear instancia de la clase Conexion
        $db = new Conexion();        
    
        try {
            // Iniciar transacción
            $db->consulta("START TRANSACTION");
            
            // Lista para almacenar los IDs actualizados
            $listaIds = [];
    
            // Se recorre el objeto procesado
            foreach ($datos as $dato) {
                // Consulta a la base de datos para verificar si el ID existe
                $resultadoExiste = ObtenerPorId($dato,$tabla);

                // Verificar si el ID existe en la base de datos
                if ($resultadoExiste === null || $resultadoExiste["id"] === null) {
                    // Si el ID no existe, hacer rollback y devolver un mensaje de error
                    $db->consulta("ROLLBACK");
                    $db->cerrar();
                    return ['error' => 'El ID '.$dato['id'].' no existe en la base de datos'];
                }
 
                // Consulta a la base de datos
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

    // Funcion para eliminar un unico registro
    // $datos: datos que se enviaron para ser insertados, se utilizan para ser enviados a la funcion que consulta por id
    // $tabla: String que es el nombre de la tabla
    // $sql: String que es la consulta sql
    function eliminarUno($datos,$tabla,$sql) {

        // Crear instancia de la clase Conexion
        $db = new Conexion();
    
        try {
            // Se busca el registro que se va a eliminar para poder retornarlo
            $registro = ObtenerPorId($datos,$tabla);

            // Se verifica si existen los registros que seran eliminados
            if($registro === null){
                return ['error' => 'Al eliminar el registro, no se encontro'];
            }

            // Consulta a la base de datos
            $resultado = $db->consulta($sql);
    
            // Verificar si la consulta se ejecutó correctamente
            if ($resultado === true) {
                $db->cerrar();

                // Devolver el registro actualizado
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
    
    // Funcion para eliminar varios registros
    // $datos: datos que se enviaron para ser insertados, se utilizan para ser enviados a la funcion que consulta por id
    // $tabla: String que es el nombre de la tabla
    // $sql: String que es la consulta sql
    function eliminarVarios($datos,$tabla,$sql) {

        // Crear instancia de la clase Conexion
        $db = new Conexion();

        try {
            // Se buscan los registros que se van a eliminar para poder retornarlos
            $listaIds = [];
            $registros = obtenerPorListaId($listaIds,$tabla);

            // Se verifica si existen los registros que seran eliminados
            if($registros === null || $registros[0] === null || $registros[0]['id']=== null){
                return ['error' => 'Al eliminar los registros, no se encontraron'];
            }

            // Consulta a la base de datos
            $resultado = $db->consulta($sql);
    
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

    // Funcion para consultar una query de cualquier tipo
    // $sql: String que es la consulta sql
    function ConsultaSQL($sql){

         // Crear instancia de la clase Conexion
         $db = new Conexion();

         try {

            // Consulta a la base de datos
             $resultado = $db->consulta($sql);
     
             /// Verificar si la consulta se ejecutó correctamente
             if ($resultado === true) {
                 $db->cerrar();
 
                 // Si la consulta fue exitosa, se retorna true
                 return true;
             } else {
                 $db->cerrar();
 
                 // Si la consulta falla, se retorna false
                 return false;
             }
         } catch (Exception $e) {
              // Cerrar la conexión manualmente
              $db->cerrar();
 
             // Si la consulta falla, se retorna false
             return false;
         }
    }

?>