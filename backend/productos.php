<?php

    // Se importa el archivo utilidades.php
    include 'utilidades/utilidades.php';

    // Funcion para obtener un registro por id
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function ObtenerPorId($datos,$tabla) {

        try{
            // Consulta a la base de datos
            $sql = "SELECT p.*, c.nombre as categoria FROM ".$tabla." p";
            $sql .= " JOIN categorias c on c.id = p.categoria_id ";
            $sql .= " WHERE p.id = ".$datos['id'];

            return obtenerUno($sql);

        }catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    // Funcion para obtener registro por nombre
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function ObtenerPorNombre($datos,$tabla) {

        try{
            // Consulta a la base de datos
            $sql = "SELECT p.*, c.nombre as categoria FROM ".$tabla." p";
            $sql .= " JOIN categorias c on c.id = p.categoria_id ";
            $sql .= " WHERE p.estado = 'Activo' AND p.nombre = ".$datos['nombre'];

            return obtenerUno($sql);

        }catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    // Funcion para obtener registros por categoria
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function ObtenerPorCategoria($datos,$tabla) {

        try{
            // Consulta a la base de datos
            $sql = "SELECT p.*, c.nombre as categoria FROM ".$tabla." p";
            $sql .= " JOIN categorias c on c.id = p.categoria_id ";
            $sql .= " WHERE p.estado = 'Activo' AND p.categoria_id = ".$datos['categoria_id'];

            return obtenerVarios($sql);

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
            $sql = "SELECT p.*, c.nombre as categoria FROM ".$tabla." p";
            $sql .= " JOIN categorias c on c.id = p.categoria_id ";
            $sql .= " WHERE p.estado = 'Activo'";
       
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
            $sql = "SELECT p.*, c.nombre as categoria FROM ".$tabla." p";
            $sql .=" JOIN categorias c on c.id = p.categoria_id WHERE p.id in (";
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
            $sql = "INSERT INTO ".$tabla." (nombre, descripcion, precio, descuento, cantidad_descuento, stock, categoria_id, fecha_creacion, estado) VALUES ";
            $sql .= "('".$datos['nombre']."', ";
            $sql .= "'".$datos['descripcion']."', ";
            $sql .= $datos['precio'].", ";
            $sql .= $datos['descuento'].", ";
            $sql .= $datos['cantidad_descuento'].", ";
            $sql .= $datos['stock'].", ";
            $sql .= $datos['categoria_id'].", ";
            $sql .= "NOW(), ";
            $sql .= "'".$datos['estado']."') ";
    
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
            $sql = "INSERT INTO ".$tabla." (nombre, descripcion, precio, descuento, cantidad_descuento, stock, categoria_id, fecha_creacion, estado) VALUES ";

            // Se recorre el objeto procesado y se construye la query
            for ($i = 0; $i < count($datos); $i++){

                $sql .= "('".$datos[$i]['nombre']."', ";
                $sql .= "'".$datos[$i]['descripcion']."', ";
                $sql .= $datos[$i]['precio'].", ";
                $sql .= $datos[$i]['descuento'].", ";
                $sql .= $datos[$i]['cantidad_descuento'].", ";
                $sql .= $datos[$i]['stock'].", ";
                $sql .= $datos[$i]['categoria_id'].", ";
                $sql .= "NOW(), ";
                $sql .= "'".$datos[$i]['estado']."'), ";
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
            $sql .= "nombre = '".$datos['nombre']."', ";
            $sql .= "descripcion = '".$datos['descripcion']."', ";
            $sql .= "precio = ".$datos['precio'].", ";
            $sql .= "descuento = ".$datos['descuento'].", ";
            $sql .= "cantidad_descuento = ".$datos['cantidad_descuento'].", ";
            $sql .= "stock = ".$datos['stock'].", ";
            $sql .= "categoria_id = ".$datos['categoria_id'].", ";
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
                $sql .= "nombre = '".$dato['nombre']."', ";
                $sql .= "descripcion = '".$dato['descripcion']."', ";
                $sql .= "precio = ".$dato['precio'].", ";
                $sql .= "descuento = ".$dato['descuento'].", ";
                $sql .= "cantidad_descuento = ".$dato['cantidad_descuento'].", ";
                $sql .= "stock = ".$dato['stock'].", ";
                $sql .= "categoria_id = ".$dato['categoria_id'].", ";
                $sql .= "estado = '".$dato['estado']."' ";
                $sql .= " WHERE id = ".$dato['id'];
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
            $sql = "UPDATE ".$tabla." SET estado = 'Inactivo' WHERE id = ".$datos['id'];
            
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

            $sql = "UPDATE ".$tabla." SET estado = 'Inactivo' WHERE id IN (";

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
        $tabla = "productos";
        
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
            case "obtenerPorNombre":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(ObtenerPorNombre($datos,$tabla));

                break;
            case "obtenerPorCategoria":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(ObtenerPorCategoria($datos,$tabla));

                break;
            case "obtenerPorListaId":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(obtenerPorListaId($datos,$tabla));

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
