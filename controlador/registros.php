<?php 
include("funciones/abrir_conexion.php");
include("funciones/cerrar_conexion.php");
session_start();
if(!isset($_SESSION["login"])){
	header('location:login.php');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="bootstrap-5/css/bootstrap.min.css">
	<title>Reposo</title>
	<link rel="stylesheet" href="css/estilo.css">
</head>
<body>
	<!-- PHP include -->
	<?php 
		include("funciones/funcionesParaPHP.php");
		include("funciones/funcionesParaHTML.php");
		include("funciones/funcionesParaConsultas.php");
	?>
	<!-- Fin PHP include -->
	<?php
		if(!$_GET){
			header('location:registros.php?pagina=1');
		}
		if(isset($_POST['botonConsultar']) || (isset($_GET['botonConsultar']))){
			if(isset($_POST['botonConsultar'])){
				$botonConsultar=$_POST['botonConsultar'];
			}else if(isset($_GET['botonConsultar'])){
				$botonConsultar=$_GET['botonConsultar'];
			}else{
				$botonConsultar=0;
			}
			include("funciones/abrir_conexion.php");	
						//If - Consulta toda la base de datos para saber cuantos registros tiene
						if($botonConsultar==1){
							$resultado= mysqli_query($conexion,consultaCompleta2());
						}
						
						$total_registros = mysqli_num_rows($resultado);
						$max_registros_estatico=50;
						$cambioASelect=7;

						if(isset($_POST['cantidadConsulta'])){
							$cantidadPOST=$_POST['cantidadConsulta'];
							if($cantidadPOST==0){
								$max_registros=$max_registros_estatico;
							}else if($cantidadPOST<0){
								$max_registros=$cantidadPOST*-1;
							}else if($cantidadPOST>500){
								$max_registros=500; 
							}else{
								$max_registros=$_POST['cantidadConsulta'];
							}
						}else if(!isset($_GET['cantidadConsulta'])){
							$max_registros=$max_registros_estatico;

						}else{
							$max_registros=$_GET['cantidadConsulta'];
						}

						$paginas = ceil($total_registros/$max_registros);
						$limitinicio=($_GET['pagina']-1)*$max_registros;
						$paginaGET = $_GET['pagina'];
						$limitfinal=$limitinicio+$max_registros;		
			include("funciones/cerrar_conexion.php");
		}
	?>

	<!-- Navbar -->
	<nav class="navbar navbar-dark color">
	  <div class="container-fluid d-flex justify-content-evenly">
	  	<a href="index.php">
	  		<button type="submit" name="" class="btn bordeBoton"><span class="navbar-brand mb-0 h1" >Inicio</span></button></a>
	  	<div>
	    	<span class="navbar-brand mb-0 h1" >Reposos</span>
	    </div class="">
	    <a href="login.php?sesion=0" class="">
	    	<button type="submit" name="" class="btn bordeBoton"><span class="navbar-brand mb-0 h1" >Cerrar Sesion</span></button></a>
	  </div>
	</nav>
	<!-- Contenedor Paginacion Arriba -->
	<?php liSiguienteAnterior($paginaGET,$paginas,$cambioASelect,$botonConsultar,$total_registros,$max_registros) ?>
	<!-- Contenedor Paginacion Arriba -->

	<!-- Contenedor Principal -->
	<div class="container-fluit">
		<div class="row mt-4">
			<div class="col-12">
				<?php

					if(isset($_POST['botonConsultar']) || (isset($_GET['botonConsultar']))){

						include("funciones/abrir_conexion.php");	

						//If - consulta de toda la base de datos con limites por pagina
						if($botonConsultar==1){
							$resultado2 = mysqli_query($conexion,consultaCompleta2limite($limitinicio,$max_registros));
						}
						
						$cont=$limitinicio;
						
						?>
						<div class="table-responsive">
							<table class="table table-sm table-hover table-bordered">
								<tr class="table-active">
									<th>#</th>
									<th>Cedula</th>
									<th>Nombres</th>
									<th>Apellidos</th>
									<th>Institucion Laboral</th>
									<th>Fecha de Nacimiento</th>
									<th style="width:400px">Direccion</th>
									<th>Telefono</th>
									<th>Cargo</th>
									<th>Dependencia</th>
									<th>Municipio</th>	
									<th>Dias De reposo</th>	
								</tr>
						<?php
						while($consulta = mysqli_fetch_array($resultado2)){
							
							$cont++;
							?>
										<tr >
											<td><?php echo $cont; ?></td>
											<td><?php echo $consulta['cedula']; ?></td>
											<td><?php echo $consulta['nombre']; ?></td>
											<td><?php echo $consulta['apellido']; ?></td>
											<td><?php echo $consulta['institucion_laboral']; ?></td>
											<td><?php echo ordenarFecha($consulta['fecha_nacimiento']); ?></td>
											<td style="width:400px"><?php echo $consulta['direccion']; ?></td>
											<?php if($consulta['telefono']!=0){ ?>
												<td><?php echo $consulta['telefono']; ?></td>	
											<?php }else{ ?>
												<td class=""></td>	
											<?php } ?>
											<td><?php echo $consulta['cargo']; ?></td>		
											<td><?php echo $consulta['dependencia']; ?></td>
											<td><?php echo $consulta['municipio']; ?></td>
											<td><?php echo $consulta['dias']; ?></td>
										</tr>
										<?php										
							}
							?>
									</table>
								</div>
							<?php
					}
					include("funciones/cerrar_conexion.php");
				?>
			</div>
		</div>
	</div>
	<!-- Fin Contenedor Principal -->
	
	<!-- Contenedor Paginacion Abajo -->
	<?php liSiguienteAnterior($paginaGET,$paginas,$cambioASelect,$botonConsultar,$total_registros,$max_registros) ?>
	<!-- Contenedor Paginacion Abajo -->

	<!--<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-eMNCOe7tC1doHpGoWe/6oMVemdAVTMs2xqW4mwXrXsW0L84Iytr2wi5v2QjrP/xp" crossorigin="anonymous"></script>-->
    <script src="bootstrap-5/js/bootstrap.min.js"></script>
	<!--  <script src="js/validar.js"></script> -->

</body>
</html>
