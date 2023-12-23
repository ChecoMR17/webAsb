<?php
session_start();
include "../../global/conexion.php";
$Fecha_Actual = date("Y-m-d H:i:s");
$Fecha = date("Y-m-d");
$Usuario = $_SESSION['Id_Empleado'];
$Id = isset($_POST["Id"]) ? $_POST["Id"] : "";

$Num_ot = isset($_POST["Num_ot"]) ? $_POST["Num_ot"] : "";
$Proveedor = isset($_POST["Proveedor"]) ? $_POST["Proveedor"] : "";
$Sucursal = isset($_POST["Sucursal"]) ? $_POST["Sucursal"] : "";
$Cuenta = isset($_POST["Cuenta"]) ? $_POST["Cuenta"] : "";
$Forma_Pago = isset($_POST["F_Pago"]) ? $_POST["F_Pago"] : "";
$Fecha_Ent = isset($_POST["Fec_Ent"]) ? $_POST["Fec_Ent"] : "";
$P_Descuento = !empty($_POST["P_Descuento"]) ? $_POST["P_Descuento"] : 0;
$Observaciones = isset($_POST["Observaciones"]) ? $_POST["Observaciones"] : "";
$Save_Data = isset($_POST["Save_Data"]) ? $_POST["Save_Data"] : "";
$Cantidad = isset($_POST["Cantidad"]) ? $_POST["Cantidad"] : "";
$Precio = isset($_POST["Precio"]) ? $_POST["Precio"] : "";

$salida = "";
$datos = array();
switch ($_GET['op']) {
    case 'Guardar_OC':
        if ($Id == "") { //Insert
            $query = ejecutarConsulta("INSERT INTO OT_OC(Num_OT, Id_Prov, Cons_Suc, Cons_Cta, Form_Pago, Fec_Ent, Fec_Sol, IU_Sol, Descuento, Obs, Status) 
            VALUES ('$Num_ot', '$Proveedor', '$Sucursal', '$Cuenta', '$Forma_Pago', '$Fecha_Ent', '$Fecha_Actual', '$Usuario', '$P_Descuento', '$Observaciones', 'A');");
        } else {
            $query = ejecutarConsulta("UPDATE OT_OC SET Num_OT='$Num_ot', Id_Prov='$Proveedor', Cons_Suc='$Sucursal', Cons_Cta='$Cuenta', Form_Pago='$Forma_Pago', Fec_Ent='$Fecha_Ent', Fec_Sol='$Fecha_Actual', IU_Sol='$Usuario', Descuento='$P_Descuento', Obs='$Observaciones' WHERE Cons_OC='$Id'");
        }
        echo $query ? 200 : 201;
        break;
    case 'Guardar_MP':
        $success = 0;
        foreach ($Save_Data as $Id_Material) {
            $Datos_E = ejecutarConsultaSimpleFila("SELECT I.Cant_Req,I.Pre_Prov,M.Costo,I.Cant_exis FROM Inventarios I
            LEFT JOIN Cat_Materiales M ON(I.Id_Mat=M.Id_Mat)
            WHERE I.Id_Inv='$Id_Material';");
            //Calidamo que la misma constante del material no exista en partidas
            $Count = ejecutarConsultaSimpleFila("SELECT count(*) FROM OT_OC_Partidas WHERE Id_Inv='$Id_Material' AND Num_OT='$Num_ot' AND Cons_OC='$Id'")[0];
            if ($Count == 0) {
                $Count_C = 0;
                $Count_Com = ejecutarConsulta("SELECT P.Cant AS cantidad FROM OT_OC_Partidas P 
                LEFT JOIN OT_OC OC ON(P.Cons_OC=OC.Cons_OC)
                WHERE P.Id_Inv='$Id_Material' AND P.Num_OT='$Num_ot' AND OC.Status='U';");

                while ($fila_C = mysqli_fetch_object($Count_Com)) {
                    $Count_C += $fila_C->cantidad != "" ? $fila_C->cantidad : 0;
                }

                $Resta = $Datos_E[0] - $Datos_E[3] - $Count_C;
                $query = ejecutarConsulta("INSERT INTO OT_OC_Partidas(Id_Inv, Num_OT, Cons_OC, Cant, Pre_Comp, Pre_Prov,Obs) 
                VALUES ('$Id_Material', '$Num_ot', '$Id', '$Resta', '" . $Datos_E[1] . "', '" . $Datos_E[2] . "','N')");
                $query ? $success++ : "";
            }
        }
        echo count($Save_Data) == $success ? 200 : 201;
        break;
    case 'Mostrar_Tabla_OC':
        $query = ejecutarConsulta("SELECT OC.Cons_OC,OC.Num_OT,concat_ws(' ',P.Nombre,P.Apellido_P,P.Apellido_M) AS Proveedor,S.Nombre AS Sucursal,OC.Form_Pago,OC.Fec_Ent,OC.Descuento,OC.Status FROM OT_OC OC 
        LEFT JOIN Proveedores P ON(OC.Id_Prov=P.Id) 
        LEFT JOIN Sucursales_Proveedores S ON(OC.Cons_Suc=S.Id)
        WHERE OC.Num_OT='$Num_ot';");
        while ($fila = mysqli_fetch_object($query)) {
            $Botones = '';
            $status = "";
            $Count = ejecutarConsultaSimpleFila("SELECT count(*) FROM OT_OC_Partidas WHERE Cons_OC='$fila->Cons_OC'")[0];
            $Btn_A = $Count > 0 ? '<button type="button" class="btn btn-outline-success btn-sm mr-2" title="Autorizar" onclick="Autorizar_OC(' . $fila->Cons_OC . ')"><i class="fa-solid fa-check-double fa-beat"></i></button>' : '';
            $Btn_PDF = $Count > 0 ? '<a type="button" class="btn btn-danger btn-sm mr-2"  href="../Archivos/Ordenes/Formatos/ordenes_compra.php?OC=' . base64_encode($fila->Cons_OC) . '" target="_blank" rel="noopener noreferrer" title="PDF"><i class="fa-solid fa-file-pdf fa-beat"></i></a>' : '';
            if ($fila->Status == 'A') {
                $status = '<div class="col badge text-white bg-primary">Activo</div>';
                $Botones = '
                <button type="button" class="btn btn-outline-info btn-sm mr-2" title="Editar" onclick="Datos_Modificacion(' . $fila->Cons_OC . ')"><i class="fa-solid fa-pen-to-square fa-beat"></i></button>
                ' . $Btn_A . '
                <button type="button" class="btn btn-secondary btn-sm mr-2" title="Agregar materiales" onclick="Mostar_Datos(' . $fila->Cons_OC . ')" data-toggle="modal" data-target="#Modal_Materiales"><i class="fa-regular fa-file-lines fa-beat"></i></button>
                ' . $Btn_PDF . '
                <button type="button" class="btn btn-outline-danger btn-sm mr-2" title="Cancelar" onclick="Cancelar_OC(' . $fila->Cons_OC . ')"><i class="fa-solid fa-xmark fa-beat"></i></button>
                ';
            } else if ($fila->Status == 'U') {
                $status = '<div class="col badge text-white bg-success">Autorizado</div>';
                $Botones = '
                <button type="button" class="btn btn-secondary btn-sm mr-2" title="Agregar materiales" onclick="Mostar_Datos(' . $fila->Cons_OC . ')" data-toggle="modal" data-target="#Modal_Materiales"><i class="fa-regular fa-file-lines fa-beat"></i></button>
                <a type="button" class="btn btn-danger btn-sm mr-2"  href="../Archivos/Ordenes/Formatos/ordenes_compra.php?OC=' . base64_encode($fila->Cons_OC) . '" target="_blank" rel="noopener noreferrer" title="PDF"><i class="fa-solid fa-file-pdf fa-beat"></i></a>
                ';
            } else if ($fila->Status == 'B') {
                $status = '<div class="col badge text-white bg-danger">Cancelado</div>';
                $Botones = '';
            }


            $datos[] = array(
                "0" => "<div class='text-center'>$fila->Cons_OC</div>",
                "1" => "<div class='text-left'>$fila->Proveedor</div>",
                "2" => "<div class='text-left'>$fila->Sucursal</div>",
                "3" => "<div class='text-left'>$fila->Form_Pago</div>",
                "4" => "<div class='text-center'>$fila->Fec_Ent</div>",
                "5" => "<div class='text-center'>$" . number_format($fila->Descuento, 2) . "</div>",
                "6" => $status,
                "7" => "<div class='d-flex justify-content-left'>$Botones</div>",
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
    case 'Mostrar_Tabla_MPendientes':
        $query = ejecutarConsulta("SELECT I.Id_Inv,I.Cons,M.Cve_Mat,M.Desc_Mat,I.Cant_Req,I.Pre_Com,Desc_Fam,I.Pre_Prov,I.Cant_exis FROM Inventarios I
        LEFT JOIN Cat_Materiales M ON(I.Id_Mat=M.Id_Mat)
        LEFT JOIN Cat_Familias F ON(M.Id_Fam=F.Id_Fam)
        WHERE Num_OT='$Num_ot' AND I.Status='U';");
        $Count_U = ejecutarConsultaSimpleFila("SELECT count(*) FROM OT_OC WHERE Cons_OC='$Id' AND Status='U';")[0];
        while ($fila = mysqli_fetch_object($query)) {
            $Count = 0;
            $Count_M = ejecutarConsulta("SELECT Cant AS cantidad FROM OT_OC_Partidas WHERE Id_Inv='$fila->Id_Inv' AND Num_OT='$Num_ot';");
            while ($fila_M = mysqli_fetch_object($Count_M)) {
                $Count += $fila_M->cantidad != "" ? $fila_M->cantidad : 0;
            }
            $Por_comprar = ($fila->Cant_Req - $fila->Cant_exis) - $Count;

            //$fila->Cant_Req -= $Count;

            $Botones = $Count_U == 0 ? '<input type="checkbox" class="check MP" name="" id="" value="' . $fila->Id_Inv . '">' : "";
            if ($Por_comprar > 0) {
                $datos[] = array(
                    "0" => "<div class='text-center'>$fila->Id_Inv</div>",
                    "1" => "<div class='text-left'>$fila->Cve_Mat</div>",
                    "2" => "<div class='text-left'>$fila->Desc_Mat</div>",
                    "3" => "<div class='text-left'>$Por_comprar</div>",
                    "4" => "<div class='text-center'>$" . number_format($fila->Pre_Prov, 2) . "</div>",
                    "5" => "<div class='text-left'>$fila->Desc_Fam</div>",
                    "6" => "<div class='d-flex justify-content-left'>$Botones</div>",
                );
            }
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
    case 'Mostrar_Tabla_Parciales':
        $query = ejecutarConsulta("SELECT P.Id,M.Cve_Mat,M.Desc_Mat,P.Cant,P.Pre_Prov,F.Desc_Fam FROM OT_OC_Partidas P
        LEFT JOIN Inventarios I ON (P.Id_Inv=I.Id_Inv)
        LEFT JOIN Cat_Materiales M ON(I.Id_Mat=M.Id_Mat)
        LEFT JOIN Cat_Familias F ON(M.Id_Fam=F.Id_Fam)
        WHERE P.Num_OT='$Num_ot' AND P.Cons_OC='$Id'");
        $Count = ejecutarConsultaSimpleFila("SELECT count(*) FROM OT_OC WHERE Cons_OC='$Id' AND Status='U';")[0];
        while ($fila = mysqli_fetch_object($query)) {
            $Botones = $Count == 0 ? '<button type="button" class="btn btn-outline-danger btn-sm mr-2" title="Cancelar" onclick="Eliminar_MP(' . $fila->Id . ')"><i class="fa-solid fa-xmark fa-beat"></i></button>' : "";
            $Input_Can = $Count == 0 ? "<input type='text' class='numero cantidad_material' id='Cantidad_M' onkeypress='return filterFloat(event,this)' value='$fila->Cant'>" : "<div class='text-center'> $fila->Cant</div>";
            $Input_Pre = $Count == 0 ? "<input type='text' class='numero cantidad_material' id='Precio_M' onkeypress='return filterFloat(event,this)' value='" . number_format($fila->Pre_Prov, 2) . "'>" : "<div class='text-center'>" . number_format($fila->Pre_Prov, 2) . "</div>";
            $datos[] = array(
                "0" => "<div class='text-center' id='Id_mat_p'>$fila->Id</div>",
                "1" => "<div class='text-left'>$fila->Cve_Mat</div>",
                "2" => "<div class='text-left'>$fila->Desc_Mat</div>",
                "3" => $Input_Can,
                "4" => $Input_Pre,
                "5" => "<div class='text-left'>$fila->Desc_Fam</div'",
                "6" => "<div class='d-flex justify-content-left'>$Botones</div>",
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
    case 'Mostrar_Proveedores':
        $query = ejecutarConsulta("SELECT Id,concat_ws(' ',Nombre,Apellido_P,Apellido_M) AS Nombre FROM Proveedores WHERE Status='A';");
        while ($fila = mysqli_fetch_object($query)) {
            $salida .= "<option class='text-dark' value='$fila->Id'>$fila->Nombre</option>";
        }
        echo $salida;
        break;
    case 'Buscar_Sucursales':
        $query = ejecutarConsulta("SELECT Id,Nombre FROM Sucursales_Proveedores WHERE Id_Proveedor='$Id' AND Status='A';");
        while ($fila = mysqli_fetch_object($query)) {
            $salida .= "<option class='text-dark' value='$fila->Id'>$fila->Nombre</option>";
        }
        echo $salida;
        break;
    case 'Buscar_Cuentas':
        $query = ejecutarConsulta("SELECT BP.Id,B.Nombre FROM Bancos_Proveedores BP 
        LEFT JOIN Bancos B ON(BP.Id_Banco=B.Id)
        WHERE BP.Id_Proveedor='$Id' AND BP.Status='A'");
        while ($fila = mysqli_fetch_object($query)) {
            $salida .= "<option class='text-dark' value='$fila->Id'>$fila->Nombre</option>";
        }
        echo $salida;
        break;
    case 'Validar_Cant':
        $query = ejecutarConsultaSimpleFila("SELECT I.Cant_Req FROM OT_OC_Partidas P
        LEFT JOIN Inventarios I ON(P.Id_Inv=I.Id_Inv)
        WHERE P.Id='$Id'")[0];
        echo $query;
        break;
    case 'Actualizar_Cant':
        $query = ejecutarConsulta("UPDATE OT_OC_Partidas SET Cant='$Cantidad', Pre_Prov='$Precio' WHERE Id='$Id '");
        echo $query ? 200 : 201;
        break;
    case 'Eliminar_MP':
        $query = ejecutarConsulta("DELETE FROM OT_OC_Partidas WHERE Id='$Id'");
        echo $query ? 200 : 201;
        break;
    case 'Cancelar_OC':
        $query = ejecutarConsulta("UPDATE OT_OC SET Status='B' WHERE Cons_OC='$Id';");
        echo $query ? 200 : 201;
        break;
    case 'Autorizar_OC':
        $query = ejecutarConsulta("UPDATE OT_OC SET Status='U',Fec_Aut='$Fecha',IU_AUT='$Usuario' WHERE Cons_OC='$Id';");
        echo $query ? 200 : 201;
        break;
    case 'Datos_Modificacion':
        $query = ejecutarConsultaSimpleFila("SELECT*FROM OT_OC WHERE Cons_OC='$Id';");
        echo json_encode($query);
        break;
}
