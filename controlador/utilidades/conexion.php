<?php

class Conexion {

    // Datos de conexion con la base de datos
    private $servidor = "localhost";
    private $usuario = "root";
    private $contrasena = "";
    private $baseDatos = "comercializadora";
    private $conexion;

    //Constructor de la clase
    public function __construct() {
        $this->conectar();
    }

    // funcion para iniciar la conexion con la base de datos
    private function conectar() {
        // Crear la conexión
        $this->conexion = new mysqli($this->servidor, $this->usuario, $this->contrasena, $this->baseDatos);
        
        // Verificar la conexión
        if ($this->conexion->connect_error) {
            die("Fallo de conexión: " . $this->conexion->connect_error);
        }
    }

    // funcion para ejecurtar la consulta a la base de datos
    public function consulta($sql) {
        $resultado = $this->conexion->query($sql);
        
        if ($resultado === false) {
            return null;
        } else {
            return $resultado;
        }
    }

    // funcion para cerrar la conexion con la base de datos
    public function cerrar() {
        if ($this->conexion && !$this->conexion->connect_errno) {
            $this->conexion->close();
            $this->conexion = null;
        }
    }

    // Funcion para poder acceder al atributo conexion de la clase
    public function getConexion(){
        return $this->conexion;
    }

    // Destructor de la clase
    public function __destruct() {
        $this->cerrar();
    }
}
?>
