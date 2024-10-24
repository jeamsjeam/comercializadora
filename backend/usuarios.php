<?php

    // Se importa el archivo utilidades.php
    include 'utilidades/utilidades.php';

    // Funcion para obtener un registro por id
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function ObtenerPorId($datos,$tabla) {

        try{
            // Consulta a la base de datos
            $sql = "SELECT us.*, ro.nombre as 'rolnombre' FROM ".$tabla." us ";
            $sql .= " JOIN roles ro ON ro.id = us.rol_id ";
            $sql .= " WHERE us.id = ".$datos['id'];

            return obtenerUno($sql);

        }catch (Exception $e) {
             // Cerrar la conexión manualmente
             $db->cerrar();

            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    // Funcion para obtener un registro por usuario
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function obtenerPorUsuario($datos,$tabla) {

        try{         
            // Consulta a la base de datos
            $sql = "SELECT us.*, ro.nombre as 'rolnombre' FROM ".$tabla." us ";
            $sql .= " JOIN roles ro ON ro.id = us.rol_id ";
            $sql .= " WHERE us.usuario = '".$datos['usuario']."'";
       
            return ObtenerUno($sql);

        }catch (Exception $e) {

            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    // Funcion para obtener un registro por usuario y clave
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function obtenerPorUsuarioClave($datos,$tabla) {

        try{         
            // Consulta a la base de datos
            $sql = "SELECT us.*, ro.nombre as 'rolnombre' FROM ".$tabla." us ";
            $sql .= " JOIN roles ro ON ro.id = us.rol_id ";
            $sql .= " WHERE us.usuario = '".$datos['usuario']."' AND us.clave = '".$datos['clave']."'";
       
            return ObtenerUno($sql);

        }catch (Exception $e) {

            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    // Funcion para obtener todos los registros
    // $tabla: String que es el nombre de la tabla
    function ObtenerTodos($tabla) {

        try{         
            // Consulta a la base de datos
            $sql = "SELECT us.*, ro.nombre as 'rolnombre' FROM ".$tabla." us ";
            $sql .= " JOIN roles ro ON ro.id = us.rol_id ";
       
            return ObtenerVarios($sql);

        }catch (Exception $e) {

            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    // Funcion para obtener registos por una lista de id
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function ObtenerPorListaId($datos,$tabla) {
        
        try{
            // Consulta a la base de datos
            $sql = "SELECT us.*, ro.nombre as 'rolnombre' FROM ".$tabla." us ";
            $sql .= " JOIN roles ro ON ro.id = us.rol_id WHERE us.id in (";

            foreach ($datos as $id) {
                $sql .= $id.",";
            }
            
            return ObtenerVarios(rtrim($sql, ',').")");

        }catch (Exception $e) {

            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }  
    }

    // Funcion para insertar un registro
    // $datos: datos para ser insertados
    // $tabla: String que es el nombre de la tabla
    function insertar($datos,$tabla) {
        
        try {
            $datosAdministrador = [
                'usuario' => $datos["usuarioAdministrador"],
                'clave' => $datos["claveAdministrador"],
            ];

            $usuarioAdministrador = obtenerPorUsuarioClave($datosAdministrador,$tabla);

            if($usuarioAdministrador == null || $usuarioAdministrador["rolnombre"] == null || $usuarioAdministrador["rolnombre"] != "Administrador"){
                return ['error' => 'No se encontro usuario administrador'];
            }

            // Consulta a la base de datos
            $sql = "INSERT INTO ".$tabla." (usuario, correo, clave, rol_id, fecha_creacion, estado) VALUES ";
            $sql .= "('".$datos['usuario']."', '".$datos['correo']."','".$datos['clave']."', ".$datos['rol_id'].", NOW(), '".$datos['estado']."')";
    
            return insertarUno($sql,$tabla);

        } catch (Exception $e) {

            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }
    }

    // Funcion para insertar varios registros
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function insertarLista($datos,$tabla) {
        
        try {
            $datosAdministrador = [
                'usuario' => $datos[0]["usuarioAdministrador"],
                'clave' => $datos[0]["claveAdministrador"],
            ];

            $usuarioAdministrador = obtenerPorUsuarioClave($datos,$tabla);

            if($usuarioAdministrador == null || $usuarioAdministrador["rolnombre"] == null || $usuarioAdministrador["rolnombre"] != "Administrador"){
                return ['error' => 'No se encontro usuario administrador'];
            }

            // Consulta a la base de datos
            $sql = "INSERT INTO ".$tabla." (usuario, correo, clave, rol_id, fecha_creacion, estado) VALUES ";

            // Se recorre el objeto procesado y se construye la query
            for ($i = 0; $i < count($datos); $i++){

                $sql .= "('".$datos[$i]['usuario']."', '".$datos[$i]['correo']."','".$datos[$i]['clave']."', ".$datos[$i]['rol_id'].", NOW(), '".$datos[$i]['estado']."')";
            }

            return insertarVarios(rtrim($sql, ','),$datos, $tabla);
    
        } catch (Exception $e) {

            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }
    }

    // Funcion para actualizar un registro
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function actualizar($datos,$tabla) {
        
        try {
            // Consulta a la base de datos
            $sql = "UPDATE ".$tabla." SET ";
            $sql .= "usuario = '".$datos['usuario']."', ";
            $sql .= "correo = '".$datos['correo']."', ";
            $sql .= "clave = '".$datos['clave']."', ";
            $sql .= "rol_id = '".$datos['rol_id']."', ";
            $sql .= "estado = '".$datos['estado']."' ";
            $sql .= "WHERE id = ".$datos['id'];
            
            return actualizarUno($datos,$tabla,$sql);

        } catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }
    }
    
    // Funcion para actualizar varios registros
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function actualizarLista($datos,$tabla) {
        
        try {
            $listaSQL = null;
    
            // Se recorre el objeto procesado y se construye la query
            foreach ($datos as $dato) {

                // Consulta a la base de datos para actualizar el registro
                $sql = "UPDATE ".$tabla." SET ";
                $sql .= "usuario = '".$dato['usuario']."', ";
                $sql .= "correo = '".$dato['correo']."', ";
                $sql .= "clave = '".$dato['clave']."', ";
                $sql .= "rol_id = '".$dato['rol_id']."', ";
                $sql .= "estado = '".$dato['estado']."' ";
                $sql .= "WHERE id = ".$dato['id'];
                $listaSQL[] = [
                    'id' => $dato['id'],
                    'sql' => $sql
                ];
            }
    
            // Consultar y devolver los registros actualizados
            return actualizarVarios($listaSQL,$tabla);
    
        } catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ' . $e->getMessage()];
        }
    }    
    
    // Funcion para eliminar un registro
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function eliminar($datos,$tabla) {
        
        try {
            // Consulta a la base de datos
            $sql = "DELETE FROM ".$tabla." WHERE id = ".$datos['id'];
            
            return eliminarUno($datos,$tabla,$sql);

        } catch (Exception $e) {
           
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }
    }
    
    // Funcion para eliminar varios registros
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function eliminarLista($datos,$tabla) {
        
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

            return eliminarLista($listaIds,$tabla,$sql);

        } catch (Exception $e) {
             
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

        // Se asigna el nombr de una tabla
        $tabla = "usuarios";
        
        // Se guarda los datos enviados en una variable
        $data = json_decode($datosRecibidos, true);

        // Se consulta la accion a tomar
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
            case "obtenerPorUsuario":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(ObtenerPorUsuario($datos,$tabla));

                break;
            case "obtenerPorUsuarioClave":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(ObtenerPorUsuarioClave($datos,$tabla));

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
