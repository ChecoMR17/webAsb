<?php
session_start();
include "../../global/conexion.php";
$Fecha_Actual = date("Y-m-d H:i:s");
$Num_ot = isset($_POST["Num_ot"]) ? $_POST["Num_ot"] : "";
$Id_Material = isset($_POST["Id_Material"]) ? $_POST["Id_Material"] : "";
$Precio = isset($_POST["Precio"]) ? $_POST["Precio"] : "";
$Cantidad = isset($_POST["Cantidad"]) ? $_POST["Cantidad"] : "";
$Fec_Ent = isset($_POST["Fec_Ent"]) ? $_POST["Fec_Ent"] : "";
$Usuario = $_SESSION['Id_Empleado'];

$Id_MS = isset($_POST["Id_MS"]) ? $_POST["Id_MS"] : "";
$Id_MaterialS = isset($_POST["Id_MaterialS"]) ? $_POST["Id_MaterialS"] : "";
$Cantidad_MS = isset($_POST["Cantidad_MS"]) ? $_POST["Cantidad_MS"] : "";

$salida = "";
$datos = array();
switch ($_GET['op']) {
    case 'Guardar_Inventario':
        // Obtenemos la constante por ordenes de trabajo
        $Constante = ejecutarConsultaSimpleFila("SELECT Cons FROM Inventarios WHERE Num_OT='$Num_ot' ORDER BY Cons DESC LIMIT 1;")[0];
        $Constante = $Constante == "" ? 1 : $Constante + 1;
        $query = ejecutarConsulta("INSERT INTO Inventarios(Num_OT, Cons, Id_Mat, Cant_Req, IU_Req, Fec_Ent, Fec_Req,Pre_Com,Pre_Prov, Status) 
        VALUES ('$Num_ot','$Constante', '$Id_Material', '$Cantidad', '$Usuario','$Fec_Ent','$Fecha_Actual','$Precio','0','A')");
        echo $query ? 200 : 201;
        break;
    case 'Guardar_IS':
        if ($Id_MS == "") { // Insert
            // Validamos que aun no exista el material 
            $Count = ejecutarConsultaSimpleFila("SELECT count(*) FROM Inventario_S WHERE Id_Material='$Id_MaterialS';")[0];
            if ($Count == 0) {
                $query = ejecutarConsulta("INSERT INTO Inventario_S(Id_Material,Cantidad) VALUE('$Id_MaterialS','$Cantidad_MS');");
                echo $query ? 200 : 201;
            } else {
                echo 202;
            }
        } else {
            $query = ejecutarConsulta("UPDATE Inventario_S SET Id_Material='$Id_MaterialS',Cantidad='$Cantidad_MS' WHERE Id='$Id_MS'");
            echo $query ? 200 : 201;
        }
        break;
    case 'Mostrar_Tabla_Inventario':
        $query = ejecutarConsulta("SELECT I.Id_Inv,I.Cons,M.Cve_Mat,M.Desc_Mat,I.Cant_Req,U.Desc_UM,I.Cant_exis,I.Status FROM Inventarios I
        LEFT JOIN Cat_Materiales M ON(I.Id_Mat=M.Id_Mat)
        LEFT JOIN Cat_Unidad_Medida U on(M.Id_UM2=U.Id_UM)
        WHERE I.Num_OT='$Num_ot';");
        while ($fila = mysqli_fetch_object($query)) {
            $Botones = '';
            $status = "";
            if ($fila->Status == 'A') {
                $status = '<div class="col badge text-white bg-primary">Activo</div>';
                $Botones = '
                <button type="button" class="btn btn-success btn-sm mr-2" title="Autorizar" onclick="Autorizar_Material(' . $fila->Id_Inv . ')"><i class="fa-solid fa-list-check fa-beat"></i></button>
                <button type="button" class="btn btn-danger btn-sm mr-2" title="Cancelar" onclick="Cancelar_Material(' . $fila->Id_Inv . ')"><i class="fa-solid fa-xmark fa-beat"></i></button>
                ';
            } else if ($fila->Status == 'U') {
                $status = '<div class="col badge text-white bg-success">Autorizado</div>';
                $Botones = '';
            } else if ($fila->Status == 'B') {
                $status = '<div class="col badge text-white bg-danger">Cancelado</div>';
                $Botones = '';
            }
            $Count = 0;
            $Count_M = ejecutarConsulta("SELECT P.Cant AS cantidad FROM OT_OC_Partidas P 
            LEFT JOIN OT_OC OC ON(P.Cons_OC=OC.Cons_OC)
            WHERE P.Id_Inv='$fila->Id_Inv' AND P.Num_OT='$Num_ot' AND OC.Status='A';");
            while ($fila_M = mysqli_fetch_object($Count_M)) {
                $Count += $fila_M->cantidad != "" ? $fila_M->cantidad : 0;
            }

            $Count_C = 0;
            $Count_Com = ejecutarConsulta("SELECT P.Cant AS cantidad FROM OT_OC_Partidas P 
            LEFT JOIN OT_OC OC ON(P.Cons_OC=OC.Cons_OC)
            WHERE P.Id_Inv='$fila->Id_Inv' AND P.Num_OT='$Num_ot' AND OC.Status='U';");
            while ($fila_C = mysqli_fetch_object($Count_Com)) {
                $Count_C += $fila_C->cantidad != "" ? $fila_C->cantidad : 0;
            }
            $fila->Cant_exis = $fila->Cant_exis == "" ? 0 : $fila->Cant_exis;
            $Por_comprar = ($fila->Cant_Req - $fila->Cant_exis) - $Count_C;
            //  $Por_comprar = $Por_comprar < 0 ? 0 : $Por_comprar;
            $Existencias = $fila->Cant_exis + $Count_C;


            $Cantidades = "
            <div class='alert alert-success' role='alert'>
                <b>Requerido: </b><span>$fila->Cant_Req $fila->Desc_UM</span> <br>
                <b>Por comprar: </b><span>$Por_comprar $fila->Desc_UM</span> <br>
                <b>Existencias: </b><span>$Existencias $fila->Desc_UM</span>
            </div>
            ";

            $datos[] = array(
                "0" => "<div class='text-center'>$fila->Cons</div>",
                "1" => "<div class='text-left'>$fila->Cve_Mat</div>",
                "2" => "<div class='text-left'>$fila->Desc_Mat</div>",
                "3" => "<div class='text-left'>$Cantidades</div>",
                "4" => $status,
                "5" => "<div class='d-flex justify-content-left'>$Botones</div>",
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
    case 'Mostrar_Tabla_Inventario_S':
        $query = ejecutarConsulta("SELECT I.Id,M.Cve_Mat,M.Desc_Mat,I.Cantidad FROM Inventario_S I
        LEFT JOIN Cat_Materiales M ON(I.Id_Material=M.Id_Mat);");
        while ($fila = mysqli_fetch_object($query)) {
            $Botones = '
                <button type="button" class="btn btn-info btn-sm mr-2" title="Autorizar" onclick="Datos_IS(' . $fila->Id . ')"><i class="fa-solid fa-pen-to-square fa-beat"></i></button>
                ';
            $datos[] = array(
                "0" => "<div class='text-center'>$fila->Id</div>",
                "1" => "<div class='text-left'>$fila->Cve_Mat</div>",
                "2" => "<div class='text-left'>$fila->Desc_Mat</div>",
                "3" => "<div class='text-left'>$fila->Cantidad</div>",
                "4" => "<div class='d-flex justify-content-center'>$Botones</div>",
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
    case 'Datos_IS':
        $query = ejecutarConsultaSimpleFila("SELECT * FROM Inventario_S WHERE Id='$Id_MS';");
        echo json_encode($query);
        break;
    case 'Autorizar_Material':
        $Inventario_S = ejecutarConsultaSimpleFila("SELECT Id_Mat,Cant_Req FROM Inventarios WHERE Id_Inv='$Id_Material'");
        $Datos_S = ejecutarConsultaSimpleFila("SELECT*FROM Inventario_S where Id_Material='" . $Inventario_S[0] . "'");
        $Datos_S["Cantidad"] = $Datos_S["Cantidad"] == "" ? 0 : $Datos_S["Cantidad"];
        $Resta = $Datos_S["Cantidad"] - $Inventario_S[1];
        $Resta = $Resta < 0 ? 0 : $Resta;
        $Datos_S["Cantidad"] = $Datos_S["Cantidad"] > $Inventario_S[1] ? $Inventario_S[1] : $$Datos_S["Cantidad"];
        $query = ejecutarConsulta("UPDATE Inventarios SET Status='U',Fec_Aut='$Fecha_Actual',IU_Aut='$Usuario',Cant_exis='" . $Datos_S["Cantidad"] . "' WHERE Id_Inv='$Id_Material'");
        ejecutarConsulta("UPDATE Inventario_S SET Cantidad='$Resta' WHERE Id_Material='$Inventario_S[0]';");
        echo $query ? 200 : 201;
        break;
    case 'Cancelar_Material':
        $query = ejecutarConsulta("UPDATE Inventarios SET Status='B' WHERE Id_Inv='$Id_Material'");
        echo $query ? 200 : 201;
        break;
    case 'Mostrar_OT':
        $query = ejecutarConsulta("SELECT O.Id,concat_ws(' ',C.Nombre,C.Apellido_P ,C.Apellido_M) AS Cliente,OB.Nombre_Obra AS Obra,O.Proyecto, O.Status FROM Ordenes_Trabajo O 
        LEFT JOIN Clientes C ON(O.Id_CLiente=C.Id)
        LEFT JOIN Obras OB ON(O.Id_Obra=OB.Id)");
        while ($fila = mysqli_fetch_object($query)) {
            $salida .= "<option class='text-dark' value='$fila->Id' data-subtext='OT:$fila->Id, Cliente: $fila->Cliente, Obra: $fila->Obra'>$fila->Proyecto</option>";
        }
        echo $salida;
        break;
    case 'Mostrar_Materiales':
        $query = ejecutarConsulta("SELECT M.Id_Mat,M.Cve_Mat,M.Desc_Mat,UM.Abrev
        FROM Cat_Materiales M 
        LEFT JOIN Cat_Unidad_Medida UM on(M.Id_UM2=UM.Id_UM)
        WHERE M.Status='A';");
        while ($fila = mysqli_fetch_object($query)) {
            $salida .= "<option class='text-dark' value='$fila->Id_Mat' data-subtext='Clave: $fila->Cve_Mat'>$fila->Desc_Mat, UM:$fila->Abrev</option>";
        }
        echo $salida;
        break;
}
