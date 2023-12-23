<?php
include "../../global/conexion.php";
//Varibales
$Fecha_Actual = date("Y-m-d H:i:s");
$datos = array();
switch ($_GET['op']) {
    case 'Mostrar_Lista_OT':
        $query = ejecutarConsulta("SELECT O.Id,concat_ws(' ',C.Nombre,C.Apellido_P ,C.Apellido_M) AS Cliente,Ob.Nombre_Obra AS Obra,O.Proyecto,concat_ws(' ',CC.Nombre,CC.Apellido_P,CC.Apellido_M) AS Contacto, O.Prioridad,O.Fecha_Inicio,O.Fecha_Final,O.Observaciones,O.Status FROM Ordenes_Trabajo O
        LEFT JOIN Clientes C on(O.Id_Cliente=C.Id)
        LEFT JOIN Obras Ob ON (O.Id_Obra=Ob.Id)
        LEFT JOIN Contactos_Clientes CC ON(O.Id_Contacto=CC.Id);");
        while ($fila = mysqli_fetch_object($query)) {
            $status = "";
            $Prioridad = "";

            if ($fila->Status == 'A') {
                $status = '<div class="badge text-white bg-primary">Activo</div>';
            } else if ($fila->Status == 'U') {
                $status = '<div class="badge text-white bg-success">Ejecución</div>';
            } else if ($fila->Status == 'C') {
                $status = '<div class="badge text-white bg-secondary">Concluido</div>';
            } else if ($fila->Status == 'B') {
                $status = '<div class="badge text-white bg-danger">Cancelado</div>';
            }

            if ($fila->Prioridad == "Alto") {
                $Prioridad = '<div class="badge text-white bg-danger">Alto</div>';
            } else if ($fila->Prioridad == "Mediano") {
                $Prioridad = '<div class="badge text-white bg-warning">Mediano</div>';
            } else if ($fila->Prioridad == "Bajo") {
                $Prioridad = '<div class="badge text-white bg-success">Bajo</div>';
            }

            $Fechas = '<div class="alert alert-success" role="alert">
                <b>Fecha de inicio: </b> ' . $fila->Fecha_Inicio . ' <br>
                <b>Fecha final: </b> ' . $fila->Fecha_Final . '
            </div>';

            $datos[] = array(
                "0" => "<div class='text-center'>$fila->Id</div>",
                "1" => "<div class='text-left'>$fila->Cliente</div>",
                "2" => "<div class='text-left'>$fila->Obra</div>",
                "3" => "<div class='text-left'>$fila->Proyecto</div>",
                "4" => "<div class='text-left'>$fila->Contacto</div>",
                "5" => $Prioridad,
                "6" => "<div class='text-left'>$Fechas</div>",
                "7" => "<div class='text-left'>" . nl2br($fila->Observaciones) . "</div>",
                "8" => "<div class='text-center'>$status</div>",
            );
        }
        $results = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($datos), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($datos), //enviamos el total registros a visualizar
            "aaData" => $datos
        );
        //Enviamos los datos de la tabla 
        echo json_encode($results);
        break;
}
