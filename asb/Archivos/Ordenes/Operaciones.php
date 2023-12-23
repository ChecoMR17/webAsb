<?php
include "../../global/conexion.php";
//Varibales
$Fecha_Actual = date("Y-m-d H:i:s");
$Id = isset($_POST['Id']) ? $_POST['Id'] : "";
$panelControl = isset($_POST['panelControl']) ? $_POST['panelControl'] : "";
$clave = isset($_POST['clave']) ? $_POST['clave'] : "";
$Cliente = isset($_POST['Cliente']) ? $_POST['Cliente'] : "";
$Obras = isset($_POST['Obras']) ? $_POST['Obras'] : "";
$Contactos = isset($_POST['Contactos']) ? $_POST['Contactos'] : "";
$Prioridad = isset($_POST['Prioridad']) ? $_POST['Prioridad'] : "";
$Proyecto = isset($_POST['Proyecto']) ? $_POST['Proyecto'] : "";
$Fecha_Inicio = isset($_POST['Fecha_Inicio']) ? $_POST['Fecha_Inicio'] : "";
$Fecha_Final = isset($_POST['Fecha_Final']) ? $_POST['Fecha_Final'] : "";
$Observaciones = isset($_POST['Observaciones']) ? $_POST['Observaciones'] : "";
$Opciones_Status = !empty($_POST['Opciones_Status']) ? $_POST['Opciones_Status'] : "A";
$Clasificacion = isset($_POST["Clasificacion"]) ? $_POST["Clasificacion"] : "";
$Id_Clasificacion = isset($_POST["Id_Clasificacion"]) ? $_POST["Id_Clasificacion"] : "";
$Nombre_Clasificacion = isset($_POST["Nombre_Clasificacion"]) ? $_POST["Nombre_Clasificacion"] : "";
$Id_Actividad = isset($_POST["Id_Actividad"]) ? $_POST["Id_Actividad"] : "";
$Fecha_Actividad = isset($_POST["Fecha_Actividad"]) ? $_POST["Fecha_Actividad"] : "";
$Nombre_Actividad = isset($_POST["Nombre_Actividad"]) ? $_POST["Nombre_Actividad"] : "";
$Descripcion_Actividad = isset($_POST["Descripcion_Actividad"]) ? $_POST["Descripcion_Actividad"] : "";

$Nombre_documento = isset($_POST["Nombre_documento"]) ? $_POST["Nombre_documento"] : "";
$Descripcion_Documento = isset($_POST["Descripcion_Documento"]) ? $_POST["Descripcion_Documento"] : "";
$N_Cotizacion = isset($_POST["N_Cotizacion"]) ? $_POST["N_Cotizacion"] : "";

$hostMqtt = isset($_POST['hostMqtt']) ? $_POST['hostMqtt'] : "";
$puertoMqtt = isset($_POST['puertoMqtt']) ? $_POST['puertoMqtt'] : "";
$timeMqtt = isset($_POST['timeMqtt']) ? $_POST['timeMqtt'] : "";
$hostPlc = isset($_POST['hostPlc']) ? $_POST['hostPlc'] : "";
$puertoPlc = isset($_POST['puertoPlc']) ? $_POST['puertoPlc'] : "";
$longitud = isset($_POST['longitud']) ? $_POST['longitud'] : "";
$latitud = isset($_POST['latitud']) ? $_POST['latitud'] : "";
$guardadoLocal = isset($_POST['guardadoLocal']) ? $_POST['guardadoLocal'] : "";
$licencia = isset($_POST['licencia']) ? $_POST['licencia'] : "";
$idDispositivo = isset($_POST['idDispositivo']) ? $_POST['idDispositivo'] : "";

$ruta = isset($_POST['ruta']) ? $_POST['ruta'] : "";

$IdParametro = isset($_POST['IdParametro']) ? $_POST['IdParametro'] : "";
$direccionesP = isset($_POST['direccionesP']) ? $_POST['direccionesP'] : "";
$tipoParametro = isset($_POST['tipoParametro']) ? $_POST['tipoParametro'] : "";
$clasificacionParametro = isset($_POST['clasificacionParametro']) ? $_POST['clasificacionParametro'] : "";
$nombreParametro = isset($_POST['nombreParametro']) ? $_POST['nombreParametro'] : "";
$unidadM = isset($_POST['unidadM']) ? $_POST['unidadM'] : "";
$permiso = isset($_POST['permiso']) ? $_POST['permiso'] : "";
$descripcionP = isset($_POST['descripcionP']) ? $_POST['descripcionP'] : "";
$indicador = isset($_POST['indicador']) ? $_POST['indicador'] : "";




$salida = "";
$datos = array();
switch ($_GET['op']) {
    case 'Guardar_Ordenes_Trabajo':
        if ($Id == "") { //Insert
            $query = ejecutarConsulta("INSERT INTO Ordenes_Trabajo(Id_Cliente,Id_Obra,Id_Contacto,Id_Clasificacion,Prioridad,Proyecto,Fecha_Inicio,Fecha_Final,Observaciones,Fecha_Alta,Status) 
            VALUES('$Cliente','$Obras','$Contactos','$Clasificacion','$Prioridad','$Proyecto','$Fecha_Inicio','$Fecha_Final','$Observaciones','$Fecha_Actual','$Opciones_Status')");
        } else {
            // Validamos si si ya se había ingresado el status U
            $Validar_Status = ejecutarConsultaSimpleFila("SELECT Status,Fecha_Ejecucion,Fecha_Concluido,Fecha_Cancelacion FROM Ordenes_Trabajo WHERE Id='$Id';");
            $Fecha_Ejecucion = "";
            $Fecha_Concluido = "";
            $Fecha_Cancelacion = "";
            if ($Validar_Status['Status'] != $Opciones_Status) {
                if ($Opciones_Status == "U") {
                    $Fecha_Ejecucion = ($Validar_Status['Fecha_Ejecucion'] == "") ? ",Fecha_Ejecucion='$Fecha_Actual'" : "";
                }
                if ($Opciones_Status == "U") {
                    $Fecha_Concluido = ($Validar_Status['Fecha_Concluido'] == "") ? ",Fecha_Concluido='$Fecha_Actual'" : "";
                }
                if ($Opciones_Status == "B") {
                    $Fecha_Cancelacion = ($Validar_Status['Fecha_Cancelacion'] == "") ? ",Fecha_Cancelacion='$Fecha_Actual'" : "";
                }
            }
            $query = ejecutarConsulta("UPDATE Ordenes_Trabajo SET Id_Cliente='$Cliente',Id_Obra='$Obras',Id_Contacto='$Contactos',Id_Clasificacion='$Clasificacion',Prioridad='$Prioridad',Proyecto='$Proyecto',Fecha_Inicio='$Fecha_Inicio',Fecha_Final='$Fecha_Final',Observaciones='$Observaciones',N_Cotizacion='$N_Cotizacion',Fecha_Modificacion='$Fecha_Actual',Status='$Opciones_Status' $Fecha_Ejecucion  $Fecha_Concluido $Fecha_Cancelacion WHERE Id='$Id'");
        }
        echo $query ? 200 : 201;
        break;
    case 'Mostrar_Clientes':
        $query = ejecutarConsulta("SELECT*FROM Clientes WHERE Status='A';");
        while ($fila = mysqli_fetch_object($query)) {
            $salida .= "<option class='text-dark' value='$fila->Id'>$fila->Nombre $fila->Apellido_P $fila->Apellido_M</option>";
        }
        echo $salida;
        break;
    case 'Buscar_Obras':
        $query = ejecutarConsulta("SELECT*FROM Obras WHERE Id_Cliente='$Cliente' AND Status='A';");
        while ($fila = mysqli_fetch_object($query)) {
            $salida .= "<option class='text-dark' value='$fila->Id'>$fila->Nombre_Obra</option>";
        }
        echo $salida;
        break;
    case 'Buscar_Contactos':
        $query = ejecutarConsulta("SELECT*FROM Contactos_Clientes WHERE Id_Cliente='$Cliente' AND Status='A';");
        while ($fila = mysqli_fetch_object($query)) {
            $salida .= "<option class='text-dark' value='$fila->Id'>$fila->Nombre $fila->Apellido_P $fila->Apellido_M</option>";
        }
        echo $salida;
        break;
    case 'Mostrar_Lista_OT':
        $query = ejecutarConsulta("SELECT O.Id,concat_ws(' ',C.Nombre,C.Apellido_P ,C.Apellido_M) AS Cliente,Ob.Nombre_Obra AS Obra,O.Proyecto,concat_ws(' ',CC.Nombre,CC.Apellido_P,CC.Apellido_M) AS Contacto, O.Prioridad,O.Fecha_Inicio,O.Fecha_Final,O.Observaciones,O.Status,O.N_Cotizacion FROM Ordenes_Trabajo O
        LEFT JOIN Clientes C on(O.Id_Cliente=C.Id)
        LEFT JOIN Obras Ob ON (O.Id_Obra=Ob.Id)
        LEFT JOIN Contactos_Clientes CC ON(O.Id_Contacto=CC.Id);");
        while ($fila = mysqli_fetch_object($query)) {
            $Botones = '';
            $status = "";
            $Prioridad = "";
            $Btn_Cotizacion = $fila->N_Cotizacion != "" ? '<a type="button" class="btn btn-secondary btn-sm mr-2" href="../Archivos/Presupuestos/Pres_Obra.php?Num_OT=' . base64_encode($fila->Id) . '&&Num_Cot=' . base64_encode($fila->N_Cotizacion) . '" target="_blank" title="Imprimir cotización pdf">' . $fila->N_Cotizacion . '</a>' : '';
            if ($fila->Status == 'A') {
                $status = '<div class="badge text-white bg-primary">Activo</div>';
                $Botones = '
                <button type="button" class="btn btn-outline-info btn-sm mr-2" title="Modificar" onclick="Datos_Modificar(' . $fila->Id . ')"><i class="fa-solid fa-user-pen fa-beat"></i></button>
                <button type="button" class="btn btn-outline-success btn-sm mr-2" title="Agregar actividades" onclick="Mostrar_Id(' . $fila->Id . ',1)" data-toggle="modal" data-target="#Guardar_Actividades"><i class="fa-solid fa-list-check fa-beat"></i></button>
                <button type="button" class="btn btn-primary btn-sm mr-2" title="Agregar documento" onclick="Mostrar_D_D(' . $fila->Id . ',1)" data-toggle="modal" data-target="#Guardar_Documentos"><i class="fa-solid fa-cloud-arrow-up fa-beat"></i></i></button>
                <a type="button" class="btn btn-danger btn-sm mr-2" href="../Archivos/Ordenes/Formatos/Ordenes.php?Num_OT=' . base64_encode($fila->Id) . '" target="_blank" title="Imprimir pdf"><i class="fa-regular fa-file-pdf fa-beat"></i></a>
                ' . $Btn_Cotizacion . '
                <button type="button" class="btn btn-secondary btn-sm mr-2" title="Telemetria" data-toggle="modal" data-target="#modalTelemetria" onclick="valoresTelemetria(' . $fila->Id . ')"><i class="fa-solid fa-network-wired"></i></button>
                ';
            } else if ($fila->Status == 'U') {
                $status = '<div class="badge text-white bg-success">Ejecución</div>';
                $Botones = '
                <button type="button" class="btn btn-outline-info btn-sm mr-2" title="Modificar" onclick="Datos_Modificar(' . $fila->Id . ')"><i class="fa-solid fa-user-pen fa-beat"></i></button>
                <button type="button" class="btn btn-outline-success btn-sm mr-2" title="Agregar actividades" onclick="Mostrar_Id(' . $fila->Id . ',1)" data-toggle="modal" data-target="#Guardar_Actividades"><i class="fa-solid fa-list-check fa-beat"></i></button>
                <button type="button" class="btn btn-primary btn-sm mr-2" title="Agregar documento" onclick="Mostrar_D_D(' . $fila->Id . ',1)" data-toggle="modal" data-target="#Guardar_Documentos"><i class="fa-solid fa-cloud-arrow-up fa-beat"></i></i></button>
                <a type="button" class="btn btn-danger btn-sm mr-2" href="../Archivos/Ordenes/Formatos/Ordenes.php?Num_OT=' . base64_encode($fila->Id) . '" target="_blank" title="Imprimir pdf"><i class="fa-regular fa-file-pdf fa-beat"></i></a>
                ' . $Btn_Cotizacion . '
                <button type="button" class="btn btn-secondary btn-sm mr-2" title="Telemetria" data-toggle="modal" data-target="#modalTelemetria" onclick="valoresTelemetria(' . $fila->Id . ')"><i class="fa-solid fa-network-wired"></i></button>
                ';
            } else if ($fila->Status == 'C') {
                $status = '<div class="badge text-white bg-secondary">Concluido</div>';
                $Botones = '
                <button type="button" class="btn btn-outline-info btn-sm mr-2" title="Modificar" onclick="Datos_Modificar(' . $fila->Id . ')"><i class="fa-solid fa-user-pen fa-beat"></i></button>
                <button type="button" class="btn btn-outline-success btn-sm mr-2" title="Agregar actividades" onclick="Mostrar_Id(' . $fila->Id . ',1)" data-toggle="modal" data-target="#Guardar_Actividades"><i class="fa-solid fa-list-check fa-beat"></i></button>
                <button type="button" class="btn btn-primary btn-sm mr-2" title="Agregar documento" onclick="Mostrar_D_D(' . $fila->Id . ',1)" data-toggle="modal" data-target="#Guardar_Documentos"><i class="fa-solid fa-cloud-arrow-up fa-beat"></i></i></button>
                <a type="button" class="btn btn-danger btn-sm mr-2" href="../Archivos/Ordenes/Formatos/Ordenes.php?Num_OT=' . base64_encode($fila->Id) . '" target="_blank" title="Imprimir pdf"><i class="fa-regular fa-file-pdf fa-beat"></i></a>
                ' . $Btn_Cotizacion . '
                <button type="button" class="btn btn-secondary btn-sm mr-2" title="Telemetria" data-toggle="modal" data-target="#modalTelemetria" onclick="valoresTelemetria(' . $fila->Id . ')"><i class="fa-solid fa-network-wired"></i></button>
                ';
            } else if ($fila->Status == 'B') {
                $status = '<div class="badge text-white bg-danger">Cancelado</div>';
                $Botones = '
                <a type="button" class="btn btn-danger btn-sm mr-2" href="../Archivos/Ordenes/Formatos/Ordenes.php?Num_OT=   " target="_blank" title="Imprimir pdf"><i class="fa-regular fa-file-pdf fa-beat"></i></a>
                ';
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
                "9" => "<div class='d-flex justify-content-center'>$Botones</div>",
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
    case 'Datos_Modificar':
        $query = ejecutarConsultaSimpleFila("SELECT*FROM Ordenes_Trabajo WHERE Id='$Id'");
        echo json_encode($query);
        break;
    case 'Ejecucion_Ot':
        $query = ejecutarConsulta("UPDATE Ordenes_Trabajo SET Status='U', Fecha_Ejecucion='$Fecha_Actual',Fecha_Modificacion='$Fecha_Actual' WHERE Id='$Id'");
        echo $query ? 200 : 201;
        break;
    case 'Guardar_Clasificacion':
        if ($Id_Clasificacion == "") { // Insert
            // Validar existencia
            $COUNT = ejecutarConsultaSimpleFila("SELECT count(*) FROM Clasificaciones WHERE Nombre='$Nombre_Clasificacion' AND Status='A'")[0];
            if ($COUNT == 0) {
                $query = ejecutarConsulta("INSERT INTO Clasificaciones(Nombre,Status) VALUES('$Nombre_Clasificacion','A')");
                echo $query ? 200 : 201;
            } else {
                echo 202;
            }
        } else { // update
            $query = ejecutarConsulta("UPDATE Clasificaciones SET Nombre='$Nombre_Clasificacion' WHERE Id='$Id_Clasificacion'");
            echo $query ? 200 : 201;
        }
        break;
    case 'Mostrar_Tbl_Clasificaciones':
        $query = ejecutarConsulta("SELECT*FROM Clasificaciones WHERE Status='A';");
        while ($fila = mysqli_fetch_object($query)) {
            $Botones = '
                <button type="button" class="btn btn-outline-info btn-sm mr-2" title="Modificar" onclick="Datos_Clasificacion(' . $fila->Id . ')"><i class="fa-solid fa-user-pen fa-beat"></i></button>
                <button type="button" class="btn btn-outline-danger btn-sm" title="Eliminar" onclick="Eliminar_Clasificacion(' . $fila->Id . ')"><i class="fa-solid fa-xmark fa-beat"></i></button>
                ';
            $datos[] = array(
                "0" => "<div class='text-left'>$fila->Id</div>",
                "1" => "<div class='text-left'>$fila->Nombre</div>",
                "2" => "<div class='d-flex justify-content-center'>$Botones</div>"
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
    case 'Datos_Clasificacion':
        $query = ejecutarConsultaSimpleFila("SELECT*FROM Clasificaciones WHERE Id='$Id_Clasificacion'");
        echo json_encode($query);
        break;
    case 'Eliminar_Clasificacion':
        $query = ejecutarConsulta("UPDATE Clasificaciones SET Status='E' WHERE Id='$Id_Clasificacion'");
        echo $query ? 200 : 201;
        break;
    case 'Buscar_Clasificacion':
        $query = ejecutarConsulta("SELECT*FROM Clasificaciones WHERE Status='A';");
        while ($fila = mysqli_fetch_object($query)) {
            $Salida .= "<option class='text-dark' value='$fila->Id'>$fila->Nombre</option>";
        }
        echo $Salida;
        break;
    case 'Guardar_Actividad':
        if ($Id_Actividad == "") { // Insert
            $query = ejecutarConsulta("INSERT INTO Actividades_OT (Id_OT,Actividad,Descripcion,Fecha_Actividad,Fecha_Alta,Status) VALUES('$Id','$Nombre_Actividad','$Descripcion_Actividad','$Fecha_Actividad','$Fecha_Actual','A');");
        } else { //Update
            $query = ejecutarConsulta("UPDATE Actividades_OT SET Actividad='$Nombre_Actividad',Descripcion='$Descripcion_Actividad',Fecha_Actividad='$Fecha_Actividad',Fecha_Modificacion='$Fecha_Actual' WHERE Id='$Id_Actividad'");
        }
        echo $query ? 200 : 201;
        break;
    case 'Mostrar_Tbl_Actividades':
        $query = ejecutarConsulta("SELECT*FROM Actividades_OT WHERE Id_OT='$Id' AND Status='A';");
        while ($fila = mysqli_fetch_object($query)) {
            $Botones = '
                <button type="button" class="btn btn-outline-info btn-sm mr-2" title="Modificar" onclick="Datos_Modificar_A(' . $fila->Id . ')"><i class="fa-solid fa-user-pen fa-beat"></i></button>
                <button type="button" class="btn btn-outline-danger btn-sm" title="Eliminar" onclick="Elimiar_Actividad(' . $fila->Id . ')"><i class="fa-solid fa-xmark fa-beat"></i></button>
                ';
            $datos[] = array(
                "0" => "<div class='text-left'>$fila->Fecha_Actividad</div>",
                "1" => "<div class='text-left'>$fila->Actividad</div>",
                "2" => "<div class='text-left'>$fila->Descripcion</div>",
                "3" => "<div class='d-flex justify-content-center'>$Botones</div>"
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
    case 'Datos_Modificar_A':
        $query = ejecutarConsultaSimpleFila("SELECT*FROM Actividades_OT WHERE Id='$Id';");
        echo json_encode($query);
        break;
    case 'Elimiar_Actividad':
        $query = ejecutarConsulta("UPDATE Actividades_OT SET Status='E',Fecha_Eliminar='$Fecha_Actual' WHERE Id='$Id_Actividad'");
        echo $query ? 200 : 201;
        break;
    case 'Guardar_Documentos';
        $fileTmpPath = $_FILES['Documento']['tmp_name'];
        $Documento = $_FILES['Documento']['name'];
        //Validamos que que exista la carpeta de ordenes
        $Ruta_Principal = "../../Documentos/ordenes";
        $Ruta_Ordenes = "../../Documentos/ordenes/OT_" . $Id;
        //Cambiamos en nombre
        $Nombre_Documento = str_replace(" ", "_", $Documento);
        // Ultimas rutas
        $Path_Destino = "$Ruta_Ordenes/$Nombre_Documento";
        $Path_BD = "../Documentos/ordenes/OT_$Id/$Nombre_Documento";
        if (!is_dir($Ruta_Principal)) {
            if (mkdir($Ruta_Principal, 0777, true)) {
                // Establece los permisos en la carpeta
                chmod($Ruta_Principal, 0777);
            }
        }
        // Validamos que la existencia de una carpeta para la orden de trabajo
        if (!is_dir($Ruta_Ordenes)) {
            if (mkdir($Ruta_Ordenes, 0777, true)) {
                // Establece los permisos en la carpeta
                chmod($Ruta_Ordenes, 0777);
            }
        }
        if (move_uploaded_file($fileTmpPath, $Path_Destino)) {
            $query = ejecutarConsulta("INSERT INTO Documentos_OT(Id_OT,Nombre,Ruta,Observaciones,Fecha_alta) VALUES('$Id','$Nombre_documento','$Path_BD','$Descripcion_Documento','$Fecha_Actual');");
            echo $query ? 200 : 201;
        } else {
            echo 202;
        }
        break;
    case 'guardarPanel':
        $IdPanel = $_POST['IdPanel'];
        $namePanel = $_POST['namePanel'];
        $fileTmpPath = $_FILES['imagenPanel']['tmp_name'];
        $Documento = $_FILES['imagenPanel']['name'];

        //Cambiamos en nombre
        $Nombre_Documento = str_replace(" ", "_", $Documento);

        //Validamos que que exista la carpeta de ordenes
        $Ruta_Principal = "../../Documentos/imgPanel";
        if (!is_dir($Ruta_Principal)) {
            if (mkdir($Ruta_Principal, 0777, true)) {
                chmod($Ruta_Principal, 0777);
            } else {
                $error = error_get_last();
                echo "Carpeta no creada. ¡Error!: " . $error['message'];
            }
        }

        // Ultimas rutas
        $Path_Destino = "$Ruta_Principal/$Nombre_Documento";
        $Ruta_PrincipalG = "../Documentos/imgPanel/$Nombre_Documento";
        if ($IdPanel == "") {
            // Validar la existencia del nombre y del archivo
            $count = ejecutarConsultaSimpleFila("SELECT COUNT(*) FROM panelControl WHERE nombre='$namePanel' AND img='$Ruta_PrincipalG';")[0];
            if ($count == 0) {
                if (move_uploaded_file($fileTmpPath, $Path_Destino)) {
                    $query = ejecutarConsulta("INSERT INTO panelControl(nombre,img) VALUES ('$namePanel','$Ruta_PrincipalG')");
                    echo $query ? 200 : 201;
                } else {
                    $error = error_get_last();
                    echo "Carpeta no creada. ¡Error!: " . $error['message'] . 202;
                }
            } else {
                echo 203;
            }
        } else {
            // obtener datos
            $query = ejecutarConsultaSimpleFila("SELECT * FROM panelControl WHERE id='$IdPanel'");
            if (unlink("../" . $query[2])) {
                if (move_uploaded_file($fileTmpPath, $Path_Destino)) {
                    $query = ejecutarConsulta("UPDATE panelControl SET nombre='$namePanel',img='$Ruta_PrincipalG' WHERE id='$IdPanel'");
                    echo $query ? 200 : 201;
                } else {
                    echo 202;
                }
            } else {
                echo 201;
            }
        }


        break;
    case 'Buscar_Archivos':
        $query = ejecutarConsulta("SELECT*FROM Documentos_OT WHERE Id_OT='$Id'");
        while ($fila = mysqli_fetch_object($query)) {
            $boton = '
            <button type="button" class="btn btn-outline-success btn-sm mr-2" title="Descargar" onclick="Descargar_Archivo(' . "'$fila->Ruta'" . ',' . "'$fila->Nombre'" . ')"><i class="fa-solid fa-cloud-arrow-down fa-bounce"></i></button>
            <button type="button" class="btn btn-outline-info btn-sm mr-2" title="Ampliar" onclick="Ampliar_Archivo(' . "'$fila->Ruta'" . ')" ><i class="fa-solid fa-up-right-and-down-left-from-center fa-bounce"></i></button>
            <button type="button" class="btn btn-outline-secondary btn-sm" title="Eliminar" onclick="Eliminar_Archivo(' . $fila->Id . ')"><i class="fa-solid fa-trash-can fa-bounce"></i></button>
            ';
            $Extencion = pathinfo($fila->Ruta, PATHINFO_EXTENSION);
            $View = ($Extencion == "png" || $Extencion == "jpg" || $Extencion == "jpeg") ? '<img src="' . $fila->Ruta . '"  class="card-img-top" alt="' . $fila->Tipo_Documento . '" height="200px">' : '<iframe src="' . $fila->Ruta . '" frameborder="0" style="width: 700px; height: 200px;"></iframe>';
            $salida .= '
                    <div class="card border-primary mr-2 mt-2" style="width: 30rem;">
                        <div class="card-header">
                            <div class="d-flex justify-content-center text-success">' . $fila->Nombre . '</div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-center">
                            ' . $View . '
                            </div>
                        </div>
                        <div class="card-footer">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 d-flex justify-content-center">
                                   ' . $fila->Tipo_Documento . '
                                </div>
                                <div class="d-flex justify-content-end">
                                    ' . $boton . '
                                </div>
                        </div>
                    </div>';
        }
        echo $salida;
        break;
    case 'Eliminar_Archivo':
        //Comprobamos si existe el archivo/*
        $query = ejecutarConsultaSimpleFila("SELECT*FROM Documentos_OT WHERE Id='$Id';")[3];
        $Ruta = "../" . $query;
        if (file_exists($Ruta)) {
            unlink($Ruta);
        }
        //Eliminamos el registro en la BD
        $sql = ejecutarConsulta("DELETE FROM Documentos_OT WHERE Id='$Id';");
        echo $sql ? 200 : 201;
        break;
    case 'Buscar_cotizaciones':
        $query = ejecutarConsulta("SELECT Num_Cot FROM Presupuesto WHERE Num_OT='$Id' AND Status='U';");
        while ($fila = mysqli_fetch_object($query)) {
            $Salida .= "<option class='text-dark' value='$fila->Num_Cot'>$fila->Num_Cot</option>";
        }
        echo "<option class='text-dark' value=''>N/A</option>" . $Salida;
        break;
    case 'guardarDispositivo':
        if ($idDispositivo == "") { // Insert
            // Validar clave
            $count = ejecutarConsultaSimpleFila("SELECT COUNT(*) FROM `dispositivos` WHERE clave='$clave'")[0];
            if ($count) {
                echo 201;
            } else {
                $query = ejecutarConsulta("INSERT INTO dispositivos( idProyecto, clave, mqttHost, mqttPort, mqttTime, plcHost, plcPort, longitud, latitud, licencia, mysqlTime) VALUES 
            ('$Id','$clave','$hostMqtt','$puertoMqtt','$timeMqtt','$hostPlc','$puertoPlc','$longitud','$latitud','$licencia','$guardadoLocal')");
                echo $query ? 200 : 201;
            }
        } else { // Update
            $query = ejecutarConsulta("UPDATE dispositivos SET clave='$clave',
        mqttHost='$hostMqtt',mqttPort='$puertoMqtt',mqttTime='$timeMqtt',plcHost='$hostPlc',plcPort='$puertoPlc',longitud='$longitud',latitud='$latitud',licencia='$licencia',mysqlTime='$guardadoLocal' WHERE id='$idDispositivo'");
            echo $query ? 200 : 201;
        }

        break;
    case 'GuardarParametros':
        if ($IdParametro == "") {
            // validamos que el mismo regitro no este en la misma lista de parametros del dispositivo
            $count = ejecutarConsultaSimpleFila("SELECT COUNT(*) FROM parametros WHERE dispositivo='$idDispositivo' AND addr='$direccionesP'")[0];
            if ($count == 0) {
                $query = ejecutarConsulta("INSERT INTO parametros(dispositivo,idPanel, tipo, addr, nombre, descripcion, permiso, um,clasificacion) 
                VALUES ('$idDispositivo','$panelControl','$tipoParametro','$direccionesP','$nombreParametro','$descripcionP','$permiso','$unidadM','$clasificacionParametro')");
                echo $query ? 200 : 201;
            } else {
                echo 203;
            }
        } else {
            // Validamos si el parametro cuenta con subparametros
            $count = ejecutarConsultaSimpleFila("SELECT COUNT(*) FROM subparametros WHERE idParametro='$IdParametro'")[0];
            if ($count > 0) {
                $type = ejecutarConsultaSimpleFila("SELECT clasificacion FROM parametros WHERE id='$IdParametro'")[0];
                if ($type != $clasificacionParametro) {
                    ejecutarConsulta("DELETE FROM subparametros WHERE idParametro='$IdParametro'");
                }
            }

            $query = ejecutarConsulta("UPDATE parametros SET tipo='$tipoParametro',idPanel='$panelControl',addr='$direccionesP',nombre='$nombreParametro',descripcion='$descripcionP',permiso='$permiso',um='$unidadM',clasificacion='$clasificacionParametro' WHERE id='$IdParametro'");
            echo $query ? 200 : 201;
        }

        break;
    case 'GuardarSubParametros':
        $IdParametroS = isset($_POST['IdParametroS']) ? $_POST['IdParametroS'] : "";
        $IdSubParametro = isset($_POST['IdSubParametro']) ? $_POST['IdSubParametro'] : "";
        $direccionesPS = isset($_POST['direccionesPS']) ? $_POST['direccionesPS'] : "";
        $nombreParametroS = isset($_POST['nombreParametroS']) ? $_POST['nombreParametroS'] : "";
        $count = ejecutarConsultaSimpleFila("SELECT COUNT(*) FROM parametros WHERE dispositivo='$idDispositivo' AND addr='$direccionesPS'")[0];
        $count2 = ejecutarConsultaSimpleFila("SELECT COUNT(*) FROM subparametros sp 
        left join parametros p on(sp.idParametro=p.id) WHERE p.dispositivo='$idDispositivo' AND sp.addr='$direccionesPS'")[0];
        $count3 = $count + $count2;
        if ($IdSubParametro == "") {
            // validamos que el mismo regitro no este en la misma lista de parametros del dispositivo
            $count = ejecutarConsultaSimpleFila("SELECT COUNT(*) FROM parametros WHERE dispositivo='$idDispositivo' AND addr='$direccionesPS'")[0];
            if ($count3 == 0) {
                $query = ejecutarConsulta("INSERT INTO subparametros(idParametro, tipo, addr, nombre, clasificacion) VALUES ('$IdParametroS','BIT','$direccionesPS','$nombreParametroS','BOTON')");
                echo $query ? 200 : 201;
            } else {
                echo 203;
            }
        } else {
            $query = ejecutarConsulta("UPDATE subparametros SET addr='$direccionesPS',nombre='$nombreParametroS' WHERE id='$IdSubParametro'");
            echo $query ? 200 : 201;
        }
        break;
    case 'listaDispositivos':
        $query = ejecutarConsulta("SELECT * FROM dispositivos WHERE idProyecto='$Id'");
        while ($fila = mysqli_fetch_object($query)) {
            $Salida .= "<option class='text-dark' value='$fila->id'>$fila->clave</option>";
        }
        echo "<option class='text-dark' value='' selected>Nuevo</option>" . $Salida;
        break;
    case 'consultarDispositivo':
        $query = ejecutarConsultaSimpleFila("SELECT * FROM dispositivos WHERE id='$Id'");
        echo json_encode($query);
        break;
    case 'consultarParametro':
        $query = ejecutarConsultaSimpleFila("SELECT * FROM parametros WHERE id='$Id'");
        header('Content-Type: application/json');
        echo json_encode($query);
        break;
    case 'consultarParametros':
        $query = ejecutarConsulta("SELECT * FROM parametros WHERE dispositivo='$Id' AND idPanel='$panelControl'");
        while ($fila = mysqli_fetch_object($query)) {
            $fila->permiso = ($fila->permiso == "E") ? "Escritura" : (($fila->permiso == "L") ? "Lectura" : "Ninguno");


            $Botones = $fila->clasificacion == "BOTON" ? '
            <button type="button" class="btn btn-outline-info btn-sm mr-2" title="Modificar" onclick="consultarParametro(' . $fila->id . ')"><i class="fa-solid fa-pen-to-square"></i></button>
            <button type="button" class="btn btn-success btn-sm mr-2" title="Actualizar registro" onclick="actualizarPMqtt(' . $fila->id . ')"><i class="fa-solid fa-file-import"></i></button>
            <button type="button" class="btn btn-secondary btn-sm" title="Agregar SubRegistro" data-toggle="modal" data-target="#modalSubRegistros" onclick="pasarIdParametro(' . $fila->id . ')"><i class="fa-solid fa-plus"></i></button>
            ' : '
            <button type="button" class="btn btn-outline-info btn-sm mr-2" title="Modificar" onclick="consultarParametro(' . $fila->id . ')"><i class="fa-solid fa-pen-to-square"></i></button>
            <button type="button" class="btn btn-success btn-sm mr-2" title="Actualizar registro" onclick="actualizarPMqtt(' . $fila->id . ')"><i class="fa-solid fa-file-import"></i></button>
            ';


            $datos[] = array(
                "0" => "<div class='text-center'>$fila->addr</div>",
                "1" => "<div class='text-left'>$fila->clasificacion</div>",
                "2" => "<div class='text-left'>$fila->tipo</div>",
                "3" => "<div class='text-left'>$fila->nombre</div>",
                "4" => "<div class='text-center'>$fila->um</div>",
                "5" => "<div class='text-center'>$fila->permiso</div>",
                "6" => "<div>$fila->descripcion</div>",
                "7" => "<div class='justify-content-left d-flex'>$Botones</div>"
            );
        }
        $results = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($datos), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($datos), //enviamos el total registros a visualizar
            "aaData" => $datos
        );
        echo json_encode($results);
        break;
    case 'consultarSubParametros':
        $query = ejecutarConsulta("SELECT * FROM subparametros WHERE idParametro='$Id'");
        while ($fila = mysqli_fetch_object($query)) {

            $Botones = '
            <button type="button" class="btn btn-outline-info btn-sm mr-2" title="Modificar" onclick="ModificarSubParametro(' . $fila->id . ',' . $fila->addr . ',' . "'" . $fila->nombre . "'" . ');"><i class="fa-solid fa-pen-to-square"></i></button>
            <button type="button" class="btn btn-danger btn-sm mr-2" title="Eliminar registro" onclick="eliminarSubParametro(' . $fila->id . ')"><i class="fa-solid fa-xmark"></i></button>
            ';
            $datos[] = array(
                "0" => "<div class='text-center'>$fila->addr</div>",
                "1" => "<div class='text-left'>$fila->nombre</div>",
                "2" => "<div class='justify-content-left d-flex'>$Botones</div>",
            );
        }
        $results = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($datos), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($datos), //enviamos el total registros a visualizar
            "aaData" => $datos
        );
        echo json_encode($results);
        break;
    case 'eliminarSubParametro':
        $query = ejecutarConsulta("DELETE FROM `subparametros` WHERE id='$Id'");
        echo $query ? 200 : 201;
        break;
    case 'descargarInstalacion':
        $apiBase = "http://www.sistema-asbombeo.com:3000/data/v1/telemetria";
        $dbUser = "root";
        $dbPasswors = "asb";
        $dbPort = "3306";

        $carpeta = '../../telemetria/dispositivo';
        $carpetaTemporal = "../../telemetria/temporal";
        $contenido = "dbHostName=127.0.0.1\ndbUser=$dbUser\ndbPassword=$dbPasswors\ndbPort=$dbPort\nurlApi=$apiBase\nnumProyecto=$Id\nclave=$clave\ndbcheckDatabaseQuery=SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME\ntRegistros=CREATE TABLE IF NOT EXISTS registros (id bigint AUTO_INCREMENT NOT NULL PRIMARY KEY, descripcion varchar(500)  NOT NULL ,fecha datetime  NOT NULL DEFAULT current_timestamp, tipo_registro varchar(50));\ntDispositivos=CREATE TABLE IF NOT EXISTS dispositivos (Id bigint AUTO_INCREMENT PRIMARY KEY,idDB bigint NOT NULL,folio bigint  NOT NULL,clave varchar(100) NOT NULL ,mqttTime float  NOT NULL ,mqttHost varchar(100)  NOT NULL ,mqttPort int  NOT NULL ,plcHost varchar(50)  NOT NULL , plcPort int  NOT NULL ,mysqlTime float  NOT NULL ,longitud varchar(100)  NOT NULL ,latitud varchar(100)  NOT NULL ,licencia date);\ntParametros=CREATE TABLE IF NOT EXISTS parametros (id bigint AUTO_INCREMENT PRIMARY KEY,idDB bigint NOT NULL,dispositivo bigint NOT NULL,tipo varchar(50) NOT NULL,addr int NOT NULL,nombre varchar(50) NOT NULL,descripcion varchar(150) NOT NULL,permiso varchar(1)  NOT NULL,um varchar(20) NOT NULL,clasificacion varchar(50) NOT NULL);\ntHistorial=CREATE TABLE IF NOT EXISTS historial (id bigint AUTO_INCREMENT PRIMARY KEY,parametro bigint  NOT NULL ,valor varchar(100)  NOT NULL ,fecha datetime NOT NULL DEFAULT current_timestamp,tipo varchar(50)  NOT NULL);\nfkeyP=ALTER TABLE parametros ADD CONSTRAINT fk_parametros_dispositivo FOREIGN KEY(dispositivo) REFERENCES dispositivos (Id);\nfkeyH=ALTER TABLE historial ADD CONSTRAINT fk_historial_parametro FOREIGN KEY(parametro) REFERENCES parametros (id);\ntriggerId=CREATE TRIGGER tr_dispositivos_insert AFTER INSERT ON dispositivos FOR EACH ROW BEGIN INSERT INTO registros (descripcion, fecha, tipo_registro) VALUES ('Se inserto un registro en dispositivos', NOW(), 'Inserción'); END;\ntriggerAd=CREATE TRIGGER tr_dispositivos_update AFTER UPDATE ON dispositivos FOR EACH ROW BEGIN INSERT INTO registros (descripcion, fecha, tipo_registro) VALUES ('Se actualizo un registro en dispositivos', NOW(), 'Actualización'); END;\ntriggerIp=CREATE TRIGGER tr_parametros_insert AFTER INSERT ON parametros FOR EACH ROW BEGIN INSERT INTO registros (descripcion, fecha, tipo_registro) VALUES ('Se inserto un registro en parametros', NOW(), 'Inserción'); END;\ntriggerAr=CREATE TRIGGER tr_parametros_update AFTER UPDATE ON parametros FOR EACH ROW BEGIN INSERT INTO registros (descripcion, fecha, tipo_registro) VALUES ('Se actualizo un registro parametros', NOW(), 'Actualización'); END;\ntriggerIh=CREATE TRIGGER tr_historial_insert AFTER INSERT ON historial FOR EACH ROW BEGIN INSERT INTO registros (descripcion, fecha, tipo_registro) VALUES ('Se inserto un registro en el historial', NOW(), 'Inserción'); END;\ntriggerAh=CREATE TRIGGER tr_historial_update AFTER UPDATE ON historial FOR EACH ROW BEGIN INSERT INTO registros (descripcion, fecha, tipo_registro) VALUES ('Se actualizo un registro en el historial', NOW(), 'Actualización'); END;\nqueryParametros=SELECT * FROM `parametros` WHERE permiso<>'N' ORDER BY tipo ASC;\nqueryUpdateD=UPDATE dispositivos SET clave=?,mqttTime=?,mqttHost=?,mqttPort=?,plcHost=?,plcPort=?,mysqlTime=?,longitud=?,latitud=?,licencia=? WHERE idDB=?\nqueryParametrosU=UPDATE parametros SET tipo=?,addr=?,nombre=?,descripcion=?,permiso=?,um=? WHERE idDB=?";
        $nombreArchivoZip = '../../telemetria/temporal/' . $clave . '.zip';
        $zip = new ZipArchive();

        try {
            if (file_put_contents($carpeta . '/.env', $contenido)) {
                // Intentar abrir el archivo zip
                if ($zip->open($nombreArchivoZip, ZipArchive::CREATE) === TRUE) {
                    $archivos = new FilesystemIterator($carpeta);
                    foreach ($archivos as $archivo) {
                        $nombreArchivo = $archivo->getFilename();
                        $zip->addFile($archivo->getPathname(), $nombreArchivo);
                    }

                    if ($zip->close()) {
                        chmod($nombreArchivoZip, 0644);
                        $response = array("status" => "success", "message" => "Archivo ZIP creado correctamente", "ruta" => "../telemetria/temporal/" . $clave . ".zip");
                    } else {
                        throw new Exception("Error al cerrar el archivo ZIP");
                    }
                } else {
                    throw new Exception("Error al abrir el archivo ZIP");
                }
            } else {
                throw new Exception("Error al escribir en el archivo .env");
            }
        } catch (Exception $e) {
            $response = array("status" => "error", "message" => $e->getMessage());
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        break;
    case 'eliminarArchivo':

        if (unlink("../" . $ruta)) {
            $response = array("status" => "success", "message" => "Archivo eliminado correctamente");
        } else {
            $response = array("status" => "error", "message" => "Error al eliminar el archivo");
        }
        header('Content-Type: application/json');
        echo json_encode($response);
        break;
    case 'mostrarPanel':
        $query = ejecutarConsulta("SELECT * FROM panelControl");
        while ($fila = mysqli_fetch_object($query)) {

            $count = ejecutarConsultaSimpleFila("SELECT COUNT(*) FROM parametros WHERE idPanel='$fila->id' AND dispositivo='$Id'")[0];

            $boton = $indicador == "N" ? '
            <button type="button" class="btn btn-info btn-sm" title="Editar" onclick="editarImg(' . $fila->id . ',' . "'" . $fila->nombre . "'" . ')"><i class="fa-solid fa-pen-to-square"></i></button>
            ' : '
            <button type="button" class="btn btn-info btn-sm" title="Editar" data-toggle="modal" data-target="#modalPanelesControl" onclick="mostrarModalPD(' . $fila->id . ')"><span class="badge badge-light">' . $count . '</span> <i class="fa-solid fa-list-ul"></i></button>
            ';
            $salida .= '
                    <div class="card border-primary mr-2 mt-2" style="width: 20em;">
                        <div class="card-header">
                            <div class="d-flex justify-content-center text-success">' . $fila->nombre . '</div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-center">
                                <img src="' . $fila->img . '" class="card-img-top" alt="' . $fila->nombre . '" height="200px">
                            </div>
                        </div>
                        <div class="card-footer">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 d-flex justify-content-center">
                                </div>
                                <div class="d-flex justify-content-end">
                                ' . $boton . '
                                </div>
                        </div>
                    </div>';
        }
        echo $salida;
        break;
    case "listarPaneles":
        $query = ejecutarConsulta("SELECT * FROM panelControl");
        while ($fila = mysqli_fetch_object($query)) {
            $salida .= "<option value='$fila->id'>$fila->nombre</option>";
        }
        echo "<option value=''>-----------</option>" . $salida;
        break;
        /*
    case 'buscarImg':
        $query = ejecutarConsultaSimpleFila("SELECT img FROM panelControl where id='$Id'")[0];
        echo $query != "" ? "<picture><img src='$query' alt='img' width='100' height='100'></picture>" : "";
        break;*/
}
