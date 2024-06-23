<?php

    include 'utilidades/utilidades.php';

    function ObtenerPorId($datos,$tabla) {

        try{
            // Consulta a la base de datos
            $sql = "SELECT p.*, tp.nombre as tipopersona FROM ".$tabla." p";
            $sql .= " JOIN tipo_persona tp on tp.id = p.tipo_persona_id ";
            $sql .= " WHERE p.id = ".$datos['id'];

            return obtenerUno($sql);

        }catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    function ObtenerPorCedula($datos,$tabla) {

        try{
            // Consulta a la base de datos
            $sql = "SELECT p.*, tp.nombre as tipopersona FROM ".$tabla." p";
            $sql .= " JOIN tipo_persona tp on tp.id = p.tipo_persona_id ";
            $sql .= " WHERE p.estado = 'Activo' AND p.cedula = '".$datos['cedula']."'";

            return obtenerUno($sql);

        }catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    function ObtenerTodos($tabla) {

        try{         
            // Consulta a la base de datos
            $sql = "SELECT p.*, tp.nombre as tipopersona FROM ".$tabla." p";
            $sql .= " JOIN tipo_persona tp on tp.id = p.tipo_persona_id ";
            $sql .= " WHERE p.estado = 'Activo'";
       
            return ObtenerVarios($sql);

        }catch (Exception $e) {

            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    function ObtenerPorListaId($datos,$tabla) {
        
        try{
            // Consulta a la base de datos
            $sql = "SELECT p.*, tp.nombre as tipopersona FROM ".$tabla." p";
            $sql .=" JOIN tipo_persona tp on tp.id = p.tipo_persona_id WHERE p.id in (";
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

            $existeCedula = ObtenerPorCedula($datos,$tabla);

            if($existeCedula !== null){
                return ['error' => 'La cedula ya esta registrada'];
            }

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
    
            return insertarUno($sql,$tabla);

        } catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }
    }

    function insertarLista($datos,$tabla) {
       
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

                $existeCedula = ObtenerPorCedula($datos[$i],$tabla);

                if($existeCedula !== null){
                    return ['error' => 'La cedula ya esta registrada'];
                }
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
            $sql .= "nombre = '".$datos['nombre']."', ";
            $sql .= "extrangero = ".$datos['extrangero'].", ";
            $sql .= "telefono = '".$datos['telefono']."', ";
            $sql .= "direccion = '".$datos['direccion']."', ";
            $sql .= "estado = '".$datos['estado']."', ";
            $sql .= "tipo_persona_id = ".$datos['tipo_persona_id']." ";
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
                $sql .= "nombre = '".$dato['nombre']."', ";
                $sql .= "extrangero = ".$dato['extrangero'].", ";
                $sql .= "telefono = '".$dato['telefono']."', ";
                $sql .= "direccion = '".$dato['direccion']."', ";
                $sql .= "estado = '".$dato['estado']."', ";
                $sql .= "tipo_persona_id = ".$dato['tipo_persona_id']." ";
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
            case "obtenerPorCedula":
                $datos = $data['datos'] ?? null;
                if($datos === null){
                    echo json_encode(['error' => 'No se envio datos']);
                    break;
                }
                echo json_encode(ObtenerPorCedula($datos,$tabla));

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
