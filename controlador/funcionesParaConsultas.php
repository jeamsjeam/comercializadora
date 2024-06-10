<?php

	//Consultar todos los registros de la base de datos
	function consultaCompleta($tabladb){
		return "SELECT * FROM $tabladb";
	}

	function InsertarPaciente($cedula,$nombres,$apellidos,$institucion,$fecha_nacimiento,$telefono,$direccion,$cargo,$dependencia,$municipio){
		return "INSERT INTO paciente(cedula, nombre, apellido, institucion_laboral, fecha_nacimiento, direccion, telefono, car_cod, dep_cod, mun_cod) VALUES ('$cedula','$nombres','$apellidos','$institucion','$fecha_nacimiento','$direccion','$telefono','$cargo','$dependencia','$municipio');";
	}

	function InsertarReposo($codasistencial,$codregistro,$reposocuido,$fechadesde,$fechahasta,$quienvalida,$especialdiadvalida,$cedula){		
		return "INSERT INTO reposo(codigo_asistencial, codigo_registro, reposo_cuido, fecha_desde, fecha_hasta, quien_valida, especialidad_valida, pac_cedula) VALUES ('$codasistencial','$codregistro','$reposocuido','$fechadesde','$fechahasta','$quienvalida','$especialdiadvalida','$cedula');";
	}

	//Consultar por columna y valor en la base de datos
	function consultaColumna($tabladb,$columna,$valor){
		return "SELECT * FROM $tabladb where $columna='$valor'";
	}

	function consultaColumnasLogin($tabladb,$columna,$columna2,$valor,$valor2){
		return "SELECT * FROM $tabladb where $columna='$valor' AND $columna2='$valor2'";
	}

	//Consultar todos los registros de la base de datos
	function consultaCompleta2(){
		return "SELECT DISTINCT pa.cedula, pa.nombre, pa.apellido,pa.institucion_laboral, pa.fecha_nacimiento, pa.direccion, pa.telefono, (SELECT descripcion from cargo where cod = pa.car_cod) cargo, (SELECT descripcion from dependencia where cod = pa.dep_cod) dependencia, (SELECT descripcion from municipio where cod = pa.mun_cod) municipio, (select sum(TIMESTAMPDIFF(DAY,fecha_desde,fecha_hasta)) Dias from reposo where pac_cedula = pa.cedula) dias FROM paciente pa join reposo re on pa.cedula=re.pac_cedula;";
	}

	function consultaCompletaColumna($valor){
		return "SELECT DISTINCT pa.cedula, pa.nombre, pa.apellido,pa.institucion_laboral, pa.fecha_nacimiento, pa.direccion, pa.telefono, (SELECT descripcion from cargo where cod = pa.car_cod) cargo, (SELECT descripcion from dependencia where cod = pa.dep_cod) dependencia, (SELECT descripcion from municipio where cod = pa.mun_cod) municipio, (select sum(TIMESTAMPDIFF(DAY,fecha_desde,fecha_hasta)) Dias from reposo where pac_cedula = pa.cedula) dias FROM paciente pa join reposo re on pa.cedula=re.pac_cedula WHERE pa.cedula='$valor';";
	}

	function consultaCompleta2limite($limitinicio,$max_registros){
		return "SELECT DISTINCT pa.cedula, pa.nombre, pa.apellido,pa.institucion_laboral, pa.fecha_nacimiento, pa.direccion, pa.telefono, (SELECT descripcion from cargo where cod = pa.car_cod) cargo, (SELECT descripcion from dependencia where cod = pa.dep_cod) dependencia, (SELECT descripcion from municipio where cod = pa.mun_cod) municipio, (select sum(TIMESTAMPDIFF(DAY,fecha_desde,fecha_hasta)) Dias from reposo where pac_cedula = pa.cedula) dias FROM paciente pa join reposo re on pa.cedula=re.pac_cedula Limit $limitinicio, $max_registros;";
	}

	function consultaReposo($valor){
		return "SELECT codigo_asistencial, codigo_registro, reposo_cuido, fecha_desde, fecha_hasta, quien_valida, especialidad_valida FROM reposo WHERE pac_cedula='$valor' order by fecha_hasta;";
	}

	function consultaReposoSumaFechas($valor){
		return "SELECT *, (select sum(TIMESTAMPDIFF(DAY,fecha_desde,fecha_hasta)) Dias from reposo rep where rep.cod = re.cod) dias from reposo re where re.pac_cedula='$valor' order by fecha_hasta;";
	}

	function consultaReposoRangoFechas($valor,$fechadesde,$fechahasta){
		return "SELECT * FROM reposo WHERE pac_cedula='$valor' AND ((fecha_desde<'$fechadesde' AND fecha_hasta>'$fechadesde') OR (fecha_desde<'$fechahasta' AND fecha_hasta>'$fechahasta'));";
	}



?>