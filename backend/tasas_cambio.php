<?php

    // Se importa el archivo utilidades.php
    include 'utilidades/utilidades.php';

    // Funcion para obtener un registro por id
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function ObtenerPorId($datos,$tabla) {

        try{
            // Consulta a la base de datos
            $sql = "SELECT t.*, us.usuario,mo.nombre as moneda,mo.simbolo FROM ".$tabla." t";
            $sql .= " JOIN usuarios us ON us.id = t.usuario_id";
            $sql .= " JOIN monedas mo ON mo.id = t.moneda_id";
            $sql .= " WHERE t.id = ".$datos['id'];

            return obtenerUno($sql);

        }catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    // Funcion para obtener un registro por fecha mas actual
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function ObtenerPorUltimaFecha($datos,$tabla) {
        try{         
            // Consulta a la base de datos
            $sql = "SELECT t.*, us.usuario,mo.nombre as moneda,mo.simbolo FROM ".$tabla." t";
            $sql .= " JOIN usuarios us ON us.id = t.usuario_id";
            $sql .= " JOIN monedas mo ON mo.id = t.moneda_id";
            $sql .= " WHERE fecha = CURDATE() AND t.moneda_id = ".$datos['moneda_id'];
            $sql .= " ORDER BY t.fecha_creacion DESC LIMIT 1";
       
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
            $sql = "SELECT t.*, us.usuario,mo.nombre as moneda,mo.simbolo FROM ".$tabla." t";
            $sql .= " JOIN usuarios us ON us.id = t.usuario_id";
            $sql .= " JOIN monedas mo ON mo.id = t.moneda_id";
       
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
            $sql = "SELECT t.*, us.usuario,mo.nombre as moneda,mo.simbolo FROM ".$tabla." t";
            $sql .= " JOIN usuarios us ON us.id = t.usuario_id WHERE t.id in (";
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
            // Consulta a la base de datos
            $sql = "INSERT INTO ".$tabla." (moneda_id, tasa, fecha, usuario_id, fecha_creacion) VALUES ";
            $sql .= "(".$datos['moneda_id'].",".$datos['tasa'].", CURDATE(),".$datos['usuario_id'].", NOW())";

            return insertarUno($sql,$tabla);

        } catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $sql, "\n"];
        }
    }

    // Funcion para insertar varios registros
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function insertarLista($datos,$tabla) {
        
        try {
            // Consulta a la base de datos
            $sql = "INSERT INTO ".$tabla." (moneda_id, tasa, fecha, usuario_id, fecha_creacion) VALUES ";

            // Se recorre el objeto procesado y se construye la query
            for ($i = 0; $i < count($datos); $i++){

                $sql .= "(".$datos['moneda_id'].",".$datos['tasa'].", CURDATE(),".$datos['usuario_id'].", NOW()),";
            }

            return insertaxrVarios($sql,$datos,$tabla);

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
            $sql .= "moneda_id = '".$datos['moneda_id']."', ";
            $sql .= "tasa = '".$datos['tasa']."', ";
            $sql .= "usuario_id = '".$datos['usuario_id']."' ";
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
                $sql .= "moneda_id = '".$dato['moneda_id']."', ";
                $sql .= "tasa = '".$dato['tasa']."', ";
                $sql .= "usuario_id = '".$dato['usuario_id']."' ";
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
           
            return eliminarLista($listaIds,$tabla,rtrim($sql, ',').")");

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
        $tabla = "tasas_cambio";
        
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
            case "obtenerPorUltimaFecha":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(ObtenerPorUltimaFecha($datos,$tabla));

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
