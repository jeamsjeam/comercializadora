<?php

    // Se importa el archivo utilidades.php
    include 'utilidades/utilidades.php';

    // Funcion para obtener un registro por id
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function ObtenerPorId($datos,$tabla) {

        try{
            // Consulta a la base de datos
            $sql = "SELECT p.*, c.nombre as producto FROM ".$tabla." p";
            $sql .= " JOIN productos c on c.id = p.producto_id ";
            $sql .= " WHERE p.id = ".$datos['id'];

            return obtenerUno($sql);

        }catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    // Funcion para obtener un registro por producto
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function ObtenerPorProducto($datos,$tabla) {

        try{
            $sql = "SELECT p.*, c.nombre as producto FROM ".$tabla." p";
            $sql .= " JOIN productos c on c.id = p.producto_id ";
            $sql .= " WHERE p.producto_id = ".$datos['producto_id'];

            if($datos["fecha_inicio"] !== null && $datos["fecha_fin"] !==  null){
                $sql .= " AND p.fecha_creacion >= '".$datos["fecha_inicio"]." 00:00:00'";
                $sql .= " AND p.fecha_creacion <= '".$datos["fecha_fin"]." 23:59:59'";
            }

            return obtenerVarios($sql);

        }catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    // Funcion para obtener registros por fecha
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function ObtenerTodosPorFecha($datos,$tabla) {

        try{         
            // Consulta a la base de datos
            $sql = "SELECT p.*, c.nombre as producto FROM ".$tabla." p";
            $sql .= " JOIN productos c on c.id = p.producto_id ";

            if($datos["fecha_inicio"] !== null && $datos["fecha_fin"] !==  null){
                $sql .= " AND p.fecha_creacion >= '".$datos["fecha_inicio"]." 00:00:00'";
                $sql .= " AND p.fecha_creacion <= '".$datos["fecha_fin"]." 23:59:59'";
            }
       
            return ObtenerVarios($sql);

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
            $sql = "SELECT p.*, c.nombre as producto FROM ".$tabla." p";
            $sql .= " JOIN productos c on c.id = p.producto_id ";
       
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
            $sql = "SELECT p.*, c.nombre as producto FROM ".$tabla." p";
            $sql .= " JOIN productos c on c.id = p.producto_id WHERE p.id in (";
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
            // Consulta a la base de datos
            $sql = "INSERT INTO ".$tabla." (factura_id, producto_id, cantidad, precio_unitario, fecha_creacion) VALUES ";
            $sql .= "(".$datos['factura_id'].", ";
            $sql .= "".$datos['producto_id'].", ";
            $sql .= "".$datos['cantidad'].", ";
            $sql .= "".$datos['precio_unitario'].", ";
            $sql .= "NOW() ) ";
    
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
            // Consulta a la base de datos
            $$sql = "INSERT INTO ".$tabla." (factura_id, producto_id, cantidad, precio_unitario, fecha_creacion) VALUES ";

            // Se recorre el objeto procesado y se construye la query
            foreach ($datos as $dato) {

                // Consulta a la base de datos para actualizar el registro
                $sql .= "(".$dato['factura_id'].", ";
                $sql .= "".$dato['producto_id'].", ";
                $sql .= "".$dato['cantidad'].", ";
                $sql .= "".$dato['precio_unitario'].", ";
                $sql .= "NOW() ), ";
            }

            return insertarVarios(rtrim($sql, ','),$datos,$tabla);

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
            $sql .= "persona_id = ".$datos['persona_id'].", ";
            $sql .= "fecha_factura = '".$datos['fecha_factura']."', ";
            $sql .= "estado = '".$datos['estado']."', ";
            $sql .= "total = ".$datos['total'].", ";
            $sql .= "moneda_id = ".$datos['moneda_id'].", ";
            $sql .= "tasa_cambio = ".$datos['tasa_cambio'].", ";
            $sql .= "usuario_id = ".$datos['usuario_id'].", ";
            $sql .= "tipo_factura_id = ".$datos['tipo_factura_id']." ";
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
                $sql .= "persona_id = ".$dato['persona_id'].", ";
                $sql .= "fecha_factura = '".$dato['fecha_factura']."', ";
                $sql .= "estado = '".$dato['estado']."', ";
                $sql .= "total = ".$dato['total'].", ";
                $sql .= "moneda_id = ".$dato['moneda_id'].", ";
                $sql .= "tasa_cambio = ".$dato['tasa_cambio'].", ";
                $sql .= "usuario_id = ".$dato['usuario_id'].", ";
                $sql .= "tipo_factura_id = ".$dato['tipo_factura_id']." ";
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
        $tabla = "detalles_factura";
        
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
            case "obtenerPorProducto":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(ObtenerPorCategoria($datos,$tabla));

                break;
            case "obtenerTodosPorFecha":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(ObtenerTodosPorFecha($datos,$tabla));

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
