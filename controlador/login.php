<?php 
include("funciones/abrir_conexion.php");
include("funciones/cerrar_conexion.php");
session_start();
?>

<?php 
	include("funciones/funcionesParaConsultas.php");
	if(isset($_GET['sesion'])){
		$sesion=$_GET['sesion'];
		if($sesion==0){
			session_destroy();
			header('location:login.php');
		}
	}
	if(isset($_POST['botonLogin'])){
		$user = $_POST['usuario'];
		$pass = $_POST['clave'];
		$tabla = "usuario";
		$colum = "clave";
		include("funciones/abrir_conexion.php");
		$validarInsert = mysqli_query($conexion,consultaColumnasLogin($tabla,$tabla,$colum,$user,$pass));
		include("funciones/cerrar_conexion.php");	
		if(mysqli_num_rows($validarInsert)>0){
			$_SESSION["login"]=$usuario;  
			header('location:index.php');
		}else{
			header('location:login.php');
			
		}
	}
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="bootstrap-5/css/bootstrap.min.css">
	<title>Reposos</title>
	<link rel="stylesheet" href="css/estilo.css">
	<link rel="stylesheet" href="css/estiloLogin.css">
</head>
<body class="loginBody">

	<nav class="navbar navbar-dark color">
	  <div class="container-fluid justify-content-center">
	  	<a href="login.php">
	    	<span class="navbar-brand mb-0 h1" >Reposos</span>
	    </a>
	  </div>
	</nav>

	<div class="wrapper fadeInDown">
	  <div id="formContent">

	    <form name="formLogin" method="post" action="login.php">
	      <input type="text" id="usuario" class="fadeIn second" name="usuario" placeholder="Usuario" required>
	      <input type="password" id="clave" class="pass fadeIn third" name="clave" placeholder="Contraseña" required>
	      <button type="submit" name="botonLogin" class="btn btn-secondary mt-3 mb-3">Ingresar</button>
	    </form>

	  </div>
	</div>

	<!--<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-eMNCOe7tC1doHpGoWe/6oMVemdAVTMs2xqW4mwXrXsW0L84Iytr2wi5v2QjrP/xp" crossorigin="anonymous"></script>-->
    <script src="bootstrap-5/js/bootstrap.min.js"></script>
	<!--  <script src="js/validar.js"></script> -->

</body>
</html>
