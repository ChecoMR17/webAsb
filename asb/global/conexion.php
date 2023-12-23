<?php
$host = "localhost";
$user = "root";
$password = "smr26";
$BD = "asb";
$port = "3306";
$conexion = mysqli_connect($host, $user, $password, $BD, $port);
date_default_timezone_set('America/Mexico_City');
if ($conexion) {
    if (!function_exists('ejecutarConsulta')) {
        function ejecutarConsulta($sql)
        {
            global $conexion;
            $query = mysqli_query($conexion, $sql);
            return $query;
        }

        function ejecutarConsultaSimpleFila($sql)
        {
            global $conexion;
            $query = mysqli_query($conexion, $sql);
            $row = mysqli_fetch_array($query);
            return $row;
        }
    }
    if (!function_exists('fechaActual')) {
        function fechaActual()
        {
            date_default_timezone_set('America/Mexico_City');
            $week_days = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado");
            $months = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
            $year_now = date("Y");
            $month_now = date("n");
            $day_now = date("j");
            $week_day_now = date("w");
            $date = $week_days[$week_day_now] . ", " . $day_now . " de " . $months[$month_now] . " de " . $year_now;
            return $date;
        }
    }
} else {
    return "Error al conexion";
}
