<?php

    include 'utilidades/utilidades.php';

    function ObtenerPorId($datos,$tabla) {

        try{
            // Consulta a la base de datos
            $sql = "SELECT p.*, c.nombre as tipofactura, u.usuario, m.nombre as moneda FROM ".$tabla." p";
            $sql .= " JOIN tipo_factura c on c.id = p.tipo_factura_id ";
            $sql .= " JOIN usuarios u on u.id = p.usuario_id ";
            $sql .= " JOIN monedas m on m.id = p.moneda_id ";
            $sql .= " WHERE p.id = ".$datos['id'];

            $factura = obtenerUno($sql);

            // Consulta a la base de datos
            $sql = "SELECT p.*, c.nombre as producto FROM detalles_factura p";
            $sql .= " JOIN productos c on c.id = p.producto_id ";
            $sql .= " WHERE p.factura_id = ".$datos['id'];

            $factura["detallefactura"] = obtenerVarios($sql);
            return $factura;

        }catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    function ObtenerPorTipo($datos,$tabla) {

        try{
            // Consulta a la base de datos
            $sql = "SELECT p.*, c.nombre as tipofactura, u.usuario, m.nombre as moneda FROM ".$tabla." p";
            $sql .= " JOIN tipo_factura c on c.id = p.tipo_factura_id ";
            $sql .= " JOIN usuarios u on u.id = p.usuario_id ";
            $sql .= " JOIN monedas m on m.id = p.moneda_id ";
            $sql .= " WHERE p.tipo_factura = ".$datos['tipo_factura'];

            if($datos["fecha_inicio"] !== null && $datos["fecha_fin"] !==  null){
                $sql .= " AND p.fecha_creacion >= '".$datos["fecha_inicio"]." 00:00:00'";
                $sql .= " AND p.fecha_creacion <= '".$datos["fecha_fin"]." 23:59:59'";
            }

            $consulta = obtenerVarios($sql);

            $facturas = null;

            foreach ($consulta as $factura) {
                // Consulta a la base de datos
                $sql = "SELECT p.*, c.nombre as producto FROM detalles_factura p";
                $sql .= " JOIN productos c on c.id = p.producto_id ";
                $sql .= " WHERE p.factura_id = ".$factura['id'];

                $factura["detallefactura"] = obtenerVarios($sql);
                $facturas[] = $factura;
            }
            
            return $facturas;

        }catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    function ObtenerPorPersona($datos,$tabla) {

        try{
            // Consulta a la base de datos
            $sql = "SELECT p.*, c.nombre as tipofactura, u.usuario, m.nombre as moneda FROM ".$tabla." p";
            $sql .= " JOIN tipo_factura c on c.id = p.tipo_factura_id ";
            $sql .= " JOIN usuarios u on u.id = p.usuario_id ";
            $sql .= " JOIN monedas m on m.id = p.moneda_id ";
            $sql .= " WHERE p.persona_id = ".$datos['persona_id'];

            if($datos["fecha_inicio"] !== null && $datos["fecha_fin"] !==  null){
                $sql .= " AND p.fecha_creacion >= '".$datos["fecha_inicio"]." 00:00:00'";
                $sql .= " AND p.fecha_creacion <= '".$datos["fecha_fin"]." 23:59:59'";
            }

            $consulta = obtenerVarios($sql);

            $facturas = null;

            foreach ($consulta as $factura) {
                // Consulta a la base de datos
                $sql = "SELECT p.*, c.nombre as producto FROM detalles_factura p";
                $sql .= " JOIN productos c on c.id = p.producto_id ";
                $sql .= " WHERE p.factura_id = ".$factura['id'];

                $factura["detallefactura"] = obtenerVarios($sql);
                $facturas[] = $factura;
            }
            
            return $facturas;

        }catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    function ObtenerPorMoneda($datos,$tabla) {

        try{
            // Consulta a la base de datos
            $sql = "SELECT p.*, c.nombre as tipofactura, u.usuario, m.nombre as moneda FROM ".$tabla." p";
            $sql .= " JOIN tipo_factura c on c.id = p.tipo_factura_id ";
            $sql .= " JOIN usuarios u on u.id = p.usuario_id ";
            $sql .= " JOIN monedas m on m.id = p.moneda_id ";
            $sql .= " WHERE p.moneda_id = ".$datos['moneda_id'];

            if($datos["fecha_inicio"] !== null && $datos["fecha_fin"] !==  null){
                $sql .= " AND p.fecha_creacion >= '".$datos["fecha_inicio"]." 00:00:00'";
                $sql .= " AND p.fecha_creacion <= '".$datos["fecha_fin"]." 23:59:59'";
            }

            $consulta = obtenerVarios($sql);

            $facturas = null;

            foreach ($consulta as $factura) {
                // Consulta a la base de datos
                $sql = "SELECT p.*, c.nombre as producto FROM detalles_factura p";
                $sql .= " JOIN productos c on c.id = p.producto_id ";
                $sql .= " WHERE p.factura_id = ".$factura['id'];

                $factura["detallefactura"] = obtenerVarios($sql);
                $facturas[] = $factura;
            }
            
            return $facturas;

        }catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    function ObtenerCantidadVentas($datos,$tabla) {

        try{
            // Consulta a la base de datos
            
            $sql = "SELECT p.tipo_factura_id AS factura, COUNT(p.tipo_factura_id) AS cantidad FROM ".$tabla." p";
            $sql .= " WHERE p.tipo_factura_id = 1";
            if($datos["fecha_inicio"] !== null && $datos["fecha_fin"] !==  null){
                $sql .= " AND p.fecha_creacion >= '".$datos["fecha_inicio"]." 00:00:00'";
                $sql .= " AND p.fecha_creacion <= '".$datos["fecha_fin"]." 23:59:59'";
            }
            $sql .= " UNION ";
            $sql .= "SELECT p.tipo_factura_id AS factura, COUNT(p.tipo_factura_id) AS cantidad FROM ".$tabla." p";
            $sql .= " WHERE p.tipo_factura_id = 2";
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

    function ObtenerTodosPorFecha($datos,$tabla) {

        try{         
            // Consulta a la base de datos
            $sql = "SELECT p.*, c.nombre as tipofactura, u.usuario, m.nombre as moneda FROM ".$tabla." p";
            $sql .= " JOIN tipo_factura c on c.id = p.tipo_factura_id ";
            $sql .= " JOIN usuarios u on u.id = p.usuario_id ";
            $sql .= " JOIN monedas m on m.id = p.moneda_id ";

            if($datos["fecha_inicio"] !== null && $datos["fecha_fin"] !==  null){
                $sql .= " WHERE p.fecha_creacion >= '".$datos["fecha_inicio"]." 00:00:00'";
                $sql .= " AND p.fecha_creacion <= '".$datos["fecha_fin"]." 23:59:59'";
            }
       
            $consulta = obtenerVarios($sql);

            $facturas = null;

            foreach ($consulta as $factura) {
                // Consulta a la base de datos
                $sql = "SELECT p.*, c.nombre as producto FROM detalles_factura p";
                $sql .= " JOIN productos c on c.id = p.producto_id ";
                $sql .= " WHERE p.factura_id = ".$factura['id'];

                $factura["detallefactura"] = obtenerVarios($sql);
                $facturas[] = $factura;
            }
            
            return $facturas;

        }catch (Exception $e) {

            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    function ObtenerTodos($tabla) {

        try{         
            // Consulta a la base de datos
            $sql = "SELECT p.*, c.nombre as tipofactura, u.usuario, m.nombre as moneda FROM ".$tabla." p";
            $sql .= " JOIN tipo_factura c on c.id = p.tipo_factura_id ";
            $sql .= " JOIN usuarios u on u.id = p.usuario_id ";
            $sql .= " JOIN monedas m on m.id = p.moneda_id ";

            $consulta = obtenerVarios($sql);

            $facturas = null;

            foreach ($consulta as $factura) {
                // Consulta a la base de datos
                $sql = "SELECT p.*, c.nombre as producto FROM detalles_factura p";
                $sql .= " JOIN productos c on c.id = p.producto_id ";
                $sql .= " WHERE p.factura_id = ".$factura['id'];

                $factura["detallefactura"] = obtenerVarios($sql);
                $facturas[] = $factura;
            }
            
            return $facturas;

        }catch (Exception $e) {

            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    function ObtenerPorListaId($datos,$tabla) {
        
        try{
            // Consulta a la base de datos
            $sql = "SELECT p.*, c.nombre as tipofactura, u.usuario, m.nombre as moneda FROM ".$tabla." p";
            $sql .= " JOIN tipo_factura c on c.id = p.tipo_factura_id ";
            $sql .= " JOIN usuarios u on u.id = p.usuario_id ";
            $sql .= " JOIN monedas m on m.id = p.moneda_id WHERE p.id in (";
            foreach ($datos as $id) {
                $sql .= $id.",";
            }
            
            return ObtenerVarios(rtrim($sql, ',').")");

        }catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }  
    }

    function insertar($datos,$tabla) {
        
        try {
            // Consulta a la base de datos
            // Consulta a la base de datos
            $sql = "INSERT INTO ".$tabla." (persona_id, estado, total, moneda_id, tasa_cambio, usuario_id, fecha_creacion, tipo_factura_id) VALUES ";
            $sql .= "(".$datos['persona_id'].", ";
            $sql .= "'".$datos['estado']."', ";
            $sql .= $datos['total'].", ";
            $sql .= $datos['moneda_id'].", ";
            $sql .= $datos['tasa_cambio'].", ";
            $sql .= $datos['usuario_id'].", ";
            $sql .= "NOW(), ";
            $sql .= $datos['tipo_factura_id'].") ";
    
            return insertarUno($sql,$tabla);

        } catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }
    }

    function insertarLista($datos,$tabla) {
       
        try {
            // Consulta a la base de datos
            $sql = "INSERT INTO ".$tabla." (persona_id, estado, total, moneda_id, tasa_cambio, usuario_id, fecha_creacion, tipo_factura_id) VALUES ";

            // Se recorre el objeto procesado y se construye la query
            for ($i = 0; $i < count($datos); $i++){

                $sql .= "(".$datos['persona_id'].", ";
                $sql .= "'".$datos['estado']."', ";
                $sql .= $datos['total'].", ";
                $sql .= $datos['moneda_id'].", ";
                $sql .= $datos['tasa_cambio'].", ";
                $sql .= $datos['usuario_id'].", ";
                $sql .= "NOW(), ";
                $sql .= $datos['tipo_factura_id']."), ";
            }

            return insertarVarios(rtrim($sql, ','),$tabla);

        } catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }
    }

    function actualizar($datos,$tabla) {
        
        try {
            // Consulta a la base de datos
            $sql = "UPDATE ".$tabla." SET ";
            $sql .= "persona_id = ".$datos['persona_id'].", ";
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
    
    function actualizarLista($datos,$tabla) {
        
        try {
            $listaSQL = null;
    
            // Se recorre el objeto procesado y se construye la query
            foreach ($datos as $dato) {

                // Consulta a la base de datos para actualizar el registro
                $sql = "UPDATE ".$tabla." SET ";
                $sql .= "persona_id = ".$dato['persona_id'].", ";
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

        $tabla = "facturas";
        
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
            case "obtenerPorTipo":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(ObtenerPorTipo($datos,$tabla));

                break;
            case "obtenerPorPersona":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(ObtenerPorPersona($datos,$tabla));

                break;
            case "obtenerPorMoneda":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(ObtenerPorMoneda($datos,$tabla));

                break;
            case "obtenerCantidadVentas":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(ObtenerCantidadVentas($datos,$tabla));

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