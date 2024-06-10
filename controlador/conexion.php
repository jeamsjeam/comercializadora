<?php
class Conexion {
    private $servidor = "localhost";
    private $usuario = "root";
    private $contrasena = "";
    private $baseDatos = "comercializadora";
    private $conexion;

    public function __construct() {
        $this->conectar();
    }

    private function conectar() {
        // Crear la conexión
        $this->conexion = new mysqli($this->servidor, $this->usuario, $this->contrasena, $this->baseDatos);
        
        // Verificar la conexión
        if ($this->conexion->connect_error) {
            die("Fallo de conexión: " . $this->conexion->connect_error);
        }
    }

    public function consulta($sql) {
        $resultado = $this->conexion->query($sql);
        
        if ($resultado === false) {
            return null;
        } else {
            return $resultado;
        }
    }

    public function cerrar() {
        if ($this->conexion && !$this->conexion->connect_errno) {
            $this->conexion->close();
            $this->conexion = null;
        }
    }

    public function getConexion(){
        return $this->conexion;
    }

    public function __destruct() {
        $this->cerrar();
    }
}
?>
