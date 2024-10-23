<?php

    // Se importa el archivo utilidades.php
    include 'utilidades/utilidades.php';

    // Funcion para obtener un registro por id
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function ObtenerPorId($datos,$tabla) {

        try{
            // Consulta a la base de datos
            $sql = "SELECT p.* FROM ".$tabla." p";
            $sql .= " WHERE p.id = ".$datos['id'];
           
            return obtenerUno($sql);

        }catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    // Funcion para obtener un registro por cedula
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function ObtenerPorCedula($datos,$tabla) {

        try{
            // Consulta a la base de datos
            $sql = "SELECT p.* FROM ".$tabla." p";
            $sql .= " WHERE p.numero_cedula = '".$datos['cedula']."'";

            return obtenerUno($sql);

        }catch (Exception $e) {
            
            // Código que se ejecuta si se lanza una excepción
            return ['error' => 'Excepción capturada: ',  $e->getMessage(), "\n"];
        }   
    }

    require_once 'vendor/autoload.php';

    use PhpOffice\PhpWord\TemplateProcessor;

    function CrearConstancia($datos,$tabla){
        try{
            $datosConsulta = ObtenerPorId($datos, $tabla);
        $rutaTemplate = __DIR__ . '/CONSTANCIA.docx';  // Ruta de la plantilla

        $templateWord = new TemplateProcessor($rutaTemplate);
        $nombre = $datosConsulta["nombres_apellidos"];
        $cedula = $datosConsulta["numero_cedula"];

        $templateWord->setValue('nombre', $nombre);
        $templateWord->setValue('cedula', $cedula);

        // Ruta donde se guardará el archivo generado (en el sistema de archivos)
        $rutaGuardada = __DIR__ . '/PRUEBAS.docx';
        $templateWord->saveAs($rutaGuardada);

        // Convertir la ruta completa en una ruta accesible por el navegador
        $rutaWeb = str_replace($_SERVER['DOCUMENT_ROOT'], '', $rutaGuardada);
        $rutaWeb = str_replace('\\', '/', $rutaWeb);  // Cambiar \ por / en caso de Windows

        return $rutaWeb;  // Retornar la ruta en formato web (relativa)

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
            $sql = "SELECT p.* FROM ".$tabla." p where p.estado = 1";
       
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

    // Funcion para insertar un registro
    // $datos: datos para ser insertados
    // $tabla: String que es el nombre de la tabla
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

    // Función para insertar un jugador y su representante
    function insertarJugador($datos) {

        // Validar que los datos del jugador estén presentes
        if (!isset($datos['nombres_apellidos']) || !isset($datos['numero_cedula']) || 
            !isset($datos['celular']) || !isset($datos['direccion_exacta'])) {
            return ['error' => 'Faltan datos del jugador.'];
        }

        // Extraer los datos del jugador
        $numero_cedula = $datos['numero_cedula'];
        $nombres_apellidos = $datos['nombres_apellidos'];
        $fecha_nacimiento = $datos['fecha_nacimiento'];
        $celular = $datos['celular'];
        $direccion_exacta = $datos['direccion_exacta'];
        $lugar_estudio = $datos['lugar_estudio'] ?? null;
        $grado = $datos['grado'] ?? null;
        $seccion = $datos['seccion'] ?? null;
        $enfermedades_alergias = $datos['enfermedades_alergias'] ?? null;
        $tipo_sangre = $datos['tipo_sangre'] ?? null;
        $historial_deportivo = $datos['historial_deportivo'] ?? null;
        $perfil = $datos['perfil'] ?? null;
        $posicion = $datos['posicion'] ?? null;
        $otras_actividades = $datos['otras_actividades'] ?? null;
        $lugar_actividades = $datos['lugar_actividades'] ?? null;
        $personas_vive = $datos['personas_vive'] ?? null;
        $facebook_jugador = $datos['facebook_jugador'] ?? null;
        $instagram_jugador = $datos['instagram_jugador'] ?? null;

        // Consulta SQL para insertar el jugador
        $sqlJugador = "INSERT INTO jugador (estado, numero_cedula, nombres_apellidos, fecha_nacimiento, celular, direccion_exacta, lugar_estudio, grado, seccion, enfermedades_alergias, tipo_sangre, historial_deportivo, perfil, posicion, otras_actividades, lugar_actividades, personas_vive, facebook_jugador, instagram_jugador) 
                    VALUES (1, '$numero_cedula', '$nombres_apellidos', '$fecha_nacimiento', '$celular', '$direccion_exacta', '$lugar_estudio', '$grado', '$seccion', '$enfermedades_alergias', '$tipo_sangre', '$historial_deportivo', '$perfil', '$posicion', '$otras_actividades', '$lugar_actividades', '$personas_vive', '$facebook_jugador', '$instagram_jugador')";

        // Ejecutar la consulta para insertar el jugador
        $resultadoJugador = insertarUno($sqlJugador, 'jugador');

        // Verificar si la inserción del jugador fue exitosa
        if (isset($resultadoJugador['error'])) {
            return $resultadoJugador; // Devolver el error en caso de fallo
        }

        // Obtener el ID del jugador insertado
        $id_jugador = $resultadoJugador['id']; 

        // Validar que los datos del representante estén presentes
        if (!isset($datos['representante'])) {
            return ['error' => 'Faltan datos del representante.'];
        }

        $representante = $datos['representante'];

        // Extraer los datos del representante
        $nombre_apellidos_pariente = $representante['nombre_apellidos'];
        $fecha_nacimiento_pariente = $datos['fecha_nacimiento'] ?? null;
        $numero_cedula_pariente = $representante['numero_cedula'] ?? null;
        $celular_pariente = $representante['telefono'];
        $parentesco = $representante['parentesco'];
        $trabajo_pariente = $representante['trabajo'] ?? null;
        $facebook_pariente = $representante['facebook'] ?? null;
        $instagram_pariente = $representante['instagram'] ?? null;

        // Consulta SQL para insertar el pariente
        $sqlPariente = "INSERT INTO parientes (jugadorId, nombre_apellidos, fecha_nacimiento, numero_cedula, celular, parentesco, trabajo, facebook, instagram) 
                        VALUES ('$id_jugador', '$nombre_apellidos_pariente', '$fecha_nacimiento_pariente', '$numero_cedula_pariente', '$celular_pariente', '$parentesco', '$trabajo_pariente', '$facebook_pariente', '$instagram_pariente')";

        // Ejecutar la consulta para insertar el pariente
        $resultadoPariente = insertarUno($sqlPariente, 'parientes');

        // Verificar si la inserción del pariente fue exitosa
        if (isset($resultadoPariente['error'])) {
            return $resultadoPariente; // Devolver el error en caso de fallo
        }

        // Si todo es exitoso, retornar el resultado
        return [
            'mensaje' => 'Jugador y representante insertados exitosamente.',
            'jugador' => $resultadoJugador
        ];
    }

/*
    function insertarJugador($datos, $tabla) {
        try {
            // Validar si ya existe un jugador con la misma cédula
            $existeCedula = ObtenerPorCedula($datos, $tabla);
            if ($existeCedula !== null) {
                return ['error' => 'La cédula ya está registrada'];
            }

            // Consulta para insertar los datos del jugador
            $sqlJugador = "INSERT INTO " . $tabla . " (nombre, cedula, telefono, direccion, fecha_creacion, estado) VALUES ";
            $sqlJugador .= "('" . $datos['nombre'] . "', ";
            $sqlJugador .= "'" . $datos['cedula'] . "', ";
            $sqlJugador .= "'" . $datos['telefono'] . "', ";
            $sqlJugador .= "'" . $datos['direccion'] . "', ";
            $sqlJugador .= "NOW(), ";
            $sqlJugador .= "'Activo')";

            // Insertar el jugador
            $resultadoJugador = insertarUno($sqlJugador, $tabla);

            if (isset($datos['mama'])) {
                // Consulta para insertar los datos de la madre
                $sqlMama = "INSERT INTO " . $tabla . "_familia (jugador_id, tipo_familiar, nombre, telefono) VALUES ";
                $sqlMama .= "(" . $resultadoJugador['id'] . ", 'madre', '" . $datos['mama']['nombre'] . "', '" . $datos['mama']['telefono'] . "')";
                insertarUno($sqlMama, $tabla . "_familia");
            }

            if (isset($datos['pana'])) {
                // Consulta para insertar los datos del pana
                $sqlPana = "INSERT INTO " . $tabla . "_familia (jugador_id, tipo_familiar, nombre, telefono) VALUES ";
                $sqlPana .= "(" . $resultadoJugador['id'] . ", 'pana', '" . $datos['pana']['nombre'] . "', '" . $datos['pana']['telefono'] . "')";
                insertarUno($sqlPana, $tabla . "_familia");
            }

            return ['success' => 'Jugador y familia registrados correctamente'];

        } catch (Exception $e) {
            return ['error' => 'Excepción capturada: ' . $e->getMessage()];
        }
    }*/

    // Funcion para insertar varios registros
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
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
            $sql .= "numero_cedula = '".$datos['numero_cedula']."', ";
            $sql .= "nombres_apellidos = '".$datos['nombres_apellidos']."', ";
            $sql .= "fecha_nacimiento = '".$datos['fecha_nacimiento']."', ";
            $sql .= "celular = '".$datos['celular']."', ";
            $sql .= "direccion_exacta = '".$datos['direccion_exacta']."', ";
            $sql .= "lugar_estudio = '".($datos['lugar_estudio'] ?? null)."', ";
            $sql .= "grado = '".($datos['grado'] ?? null)."', ";
            $sql .= "seccion = '".($datos['seccion'] ?? null)."', ";
            $sql .= "enfermedades_alergias = '".($datos['enfermedades_alergias'] ?? null)."', ";
            $sql .= "tipo_sangre = '".($datos['tipo_sangre'] ?? null)."', ";
            $sql .= "historial_deportivo = '".($datos['historial_deportivo'] ?? null)."', ";
            $sql .= "perfil = '".($datos['perfil'] ?? null)."', ";
            $sql .= "posicion = '".($datos['posicion'] ?? null)."', ";
            $sql .= "otras_actividades = '".($datos['otras_actividades'] ?? null)."', ";
            $sql .= "lugar_actividades = '".($datos['lugar_actividades'] ?? null)."', ";
            $sql .= "personas_vive = '".($datos['personas_vive'] ?? null)."', ";
            $sql .= "facebook_jugador = '".($datos['facebook_jugador'] ?? null)."', ";
            $sql .= "instagram_jugador = '".($datos['instagram_jugador'] ?? null)."' ";
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
    
    // Funcion para eliminar un registro
    // $datos: datos para ser consultados
    // $tabla: String que es el nombre de la tabla
    function eliminar($datos,$tabla) {

        try {
            
            // Consulta a la base de datos
            $sql = "UPDATE ".$tabla." SET estado = 0 WHERE id = ".$datos['id'];
            
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
        $tabla = "jugador";
        
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
            case "crearConstancia":
                    $datos = $data['datos'] ?? null;
                    if($datos === null){
                        echo json_encode(['error' => 'No se envio datos']);
                        break;
                    }
                    echo json_encode(CrearConstancia($datos,$tabla));
    
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
                echo json_encode(insertarJugador($datos));

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
