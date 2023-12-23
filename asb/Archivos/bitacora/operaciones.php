<?php
session_start();
include "../../global/conexion.php";
$id = isset($_POST["id"]) ? $_POST["id"] : "";
$idProyecto = isset($_POST["idProyecto"]) ? $_POST["idProyecto"] : "";
$idActividad = isset($_POST["idActividad"]) ? $_POST["idActividad"] : "";
$fechaInicial = isset($_POST["fechaInicial"]) ? $_POST["fechaInicial"] : "";
$fechaFinal = isset($_POST["fechaFinal"]) ? $_POST["fechaFinal"] : "";
$dBitacora = isset($_POST["dBitacora"]) ? $_POST["dBitacora"] : "";
$idEvidencia = isset($_POST["idEvidencia"]) ? $_POST["idEvidencia"] : "";
$nombreImg = isset($_POST["nombreImg"]) ? $_POST["nombreImg"] : "";
$orden = isset($_POST["orden"]) ? $_POST["orden"] : "";

$Id_Usuario = $_SESSION['Id_Usuario'];
$query = "";
$salida = "";
$Ejecutar = "";
$New_Alto = 800;
$New_Ancho = 600;
$datos = array();
switch ($_GET['op']) {
    case 'guardarBitacora':
        if ($id == "") {
            $query = ejecutarConsulta("INSERT INTO reportesObra(idProyecto,idResponsable,fechaInicio,FechaFinal, Descripcion) VALUES ('$idProyecto','$Id_Usuario','$fechaInicial','$fechaFinal','$dBitacora')");
        } else {
            $query = ejecutarConsulta("UPDATE reportesObra SET fechaInicio='$fechaInicial',FechaFinal='$fechaFinal',Descripcion='$dBitacora' WHERE id='$id'");
        }
        echo $query ? 200 : 201;
        break;
    case 'obtenerRegistrosB':
        $query = ejecutarConsulta("SELECT R.id,CONCAT(E.Nombre,' ',E.Apellido_P,' ',E.Apellido_M) AS responsable,R.fechaInicio,R.FechaFinal,R.Descripcion,R.status,R.idProyecto FROM reportesObra R LEFT JOIN User U ON(R.idResponsable=U.Id) LEFT JOIN Empleados E ON(U.Id_Empleado=E.Id_Empleado) WHERE R.idProyecto='$id';");
        while ($fila = mysqli_fetch_object($query)) {
            $desOriginal = $fila->Descripcion;
            $textoRecortado = substr($desOriginal, 0, 200);
            if (strlen($textoRecortado) < strlen($desOriginal)) {
                $textoRecortado .= '..<i title="El texto completo de vera en la bitácora o en el reporte de obra" class="fa-solid fa-caret-right"></i>';
            }
            $botones = '
                <button type="button" class="btn btn-outline-info btn-sm mr-2" title="Modificar" onclick="obtenerDatos(' . $fila->id . ')"><i class="fa-solid fa-pen-to-square"></i></button>
                <button type="button" class="btn btn-success btn-sm mr-2" title="Agregar evidencia" onclick="datosEvidencias(' . $fila->id . ')" data-toggle="modal" data-target="#modalEvidencias"><i class="fa-solid fa-cloud-arrow-up"></i></button>
                <a type="button" href="../Archivos/bitacora/reporteObra.php?idReporte=' . base64_encode($fila->id) . '&&idProyecto=' . base64_encode($fila->idProyecto) . '" target="_blank" rel="noopener noreferrer" class="btn btn-danger btn-sm mr-2" title="Imprimir reporte"><i class="fa-solid fa-file-pdf"></i></a>
                ';
            $fechas = "
            <div class=''>
            <b>Inicial:</b> <br>$fila->fechaInicio
            <br>
            <b>Final:</b> <br>$fila->FechaFinal
            </div>
            ";
            $datos[] = array(
                "0" => "<div class='text-left'>$fila->responsable</div>",
                "1" => "<div class='text-left'>$fechas</div>",
                "2" => "<div class='text-left'>" . nl2br($textoRecortado) . "</div>",
                "3" => "<div class='d-flex justify-content-center'>$botones</div>"
            );
        }
        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($datos),
            "iTotalDisplayRecords" => count($datos),
            "aaData" => $datos
        );
        echo json_encode($results);

        break;
    case 'obtenerDatos':
        $query = ejecutarConsultaSimpleFila("SELECT fechaInicio,FechaFinal,Descripcion FROM reportesObra WHERE id='$id'");
        echo json_encode($query);
        break;
    case 'obtenerProyectos':
        $query = ejecutarConsulta("SELECT id,Proyecto FROM Ordenes_Trabajo");
        while ($fila = mysqli_fetch_object($query)) {
            $salida .= "<option value='$fila->id' class='text-dark'>$fila->id .-$fila->Proyecto</option>";
        }
        echo $salida;
        break;
    case 'guardarEvidencia':
        $Ruta_Carpeta = "../../Documentos/bitacora/proyecto$idProyecto/evidencia" . $idActividad;
        $cantidadArchivos = count($_FILES['evidencias']['tmp_name']);
        $count = 0;
        if (crearDirectorio($Ruta_Carpeta)) {
            if (is_array($_FILES['evidencias']['tmp_name']) || is_object($_FILES['evidencias']['tmp_name'])) {
                foreach ($_FILES["evidencias"]['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES["evidencias"]["name"][$key]) {
                        $filename = $_FILES["evidencias"]["name"][$key];
                        $fileTmpPath = $_FILES["evidencias"]["tmp_name"][$key];
                        $Extension = pathinfo($filename, PATHINFO_EXTENSION);
                        $New_Name = str_replace(" ", "_", $filename);
                        $Ruta_Destino = $Ruta_Carpeta . "/" . $New_Name;
                        $Ruta_Guardar = "../Documentos/bitacora/proyecto$idProyecto/evidencia$idActividad" . "/" . $New_Name;

                        if ($Extension == "png" || $Extension == "jpg") {
                            /* Obtener el ancho y el alto de la imagen. */
                            list($Ancho_Real, $Alto_Real) = getimagesize($fileTmpPath);
                            /* Dividiendo el ancho máximo por el ancho de la imagen. */
                            $x_ratio = $New_Ancho / $Ancho_Real;
                            $y_ratio = $New_Alto / $Alto_Real;
                            if (($Ancho_Real <= $New_Ancho) && ($Alto_Real <= $New_Alto)) {
                                $Ancho_Final = $Ancho_Real;
                                $Alto_Final = $Alto_Real;
                                $Grados_R = 0;
                            } else if (($x_ratio * $Alto_Real) < $New_Alto) {
                                $Alto_Final = ceil($x_ratio * $Alto_Real);
                                $Ancho_Final = $New_Ancho;
                                //$Grados_R = 0;
                            } else {
                                $Ancho_Final = ceil($y_ratio * $Ancho_Real);
                                $Alto_Final = $New_Alto;
                            }
                            $lienzo = imagecreatetruecolor($Ancho_Final, $Alto_Final);
                            if ($Extension == "png") {
                                $original = imagecreatefrompng($fileTmpPath);
                                imagecopyresampled($lienzo, $original, 0, 0, 0, 0, $Ancho_Final, $Alto_Final, $Ancho_Real, $Alto_Real);
                                $rotate = imagerotate($lienzo, $Grados_R, 0);
                                $Ejecutar = imagepng($rotate, $Ruta_Destino);
                            } else if ($Extension == "jpg") {
                                $original = imagecreatefromjpeg($fileTmpPath);
                                imagecopyresampled($lienzo, $original, 0, 0, 0, 0, $Ancho_Final, $Alto_Final, $Ancho_Real, $Alto_Real);
                                $rotate = imagerotate($lienzo, $Grados_R, 0);
                                $Ejecutar = imagejpeg($rotate, $Ruta_Destino);
                            }

                            if ($Ejecutar) {
                                $query = ejecutarConsulta("INSERT INTO archivosReporte(idReporte,rutaArchivo, Descripcion) VALUES ('$idActividad','$Ruta_Guardar','$nombreImg')");
                                if ($query) {
                                    $count++;
                                }
                            }
                        } else {
                            if (move_uploaded_file($fileTmpPath, $Ruta_Destino)) {
                                $query = ejecutarConsulta("INSERT INTO archivosReporte(idReporte,rutaArchivo, Descripcion) VALUES ('$idActividad','$Ruta_Guardar','$nombreImg')");
                                if ($query) {
                                    $count++;
                                }
                            }
                        }
                    }
                }
            }
        } else {
            echo 201;
        }
        echo $count == $cantidadArchivos ? 200 : 201;
        break;
    case 'mostrarEvidencias':
        $query = ejecutarConsulta("SELECT * FROM `archivosReporte` WHERE idReporte='$id' ORDER BY orden ASC;");
        while ($fila = mysqli_fetch_object($query)) {
            $Extension = pathinfo($fila->rutaArchivo, PATHINFO_EXTENSION);
            $rutaAr = ($Extension == 'pdf') ? "../img/pdf.jpg" : $fila->rutaArchivo;

            $botones = ($Extension == 'pdf') ? '
            <button type="button" class="btn btn-danger btn-sm mr-2" title="Eliminar" onclick="eliminarEvidencia(' . $fila->id . ')"><i class="fa-solid fa-trash-can"></i></button>
            <a href="' . $fila->rutaArchivo . '" target="_blank" rel="noopener noreferrer" type="button" class="btn btn-info btn-sm mr-2" title="Expandir"><i class="fa-solid fa-maximize"></i></a>
            ' : '
            <button type="button" class="btn btn-danger btn-sm mr-2" title="Eliminar" onclick="eliminarEvidencia(' . $fila->id . ')"><i class="fa-solid fa-trash-can"></i></button>
            <a href="' . $fila->rutaArchivo . '" target="_blank" rel="noopener noreferrer" type="button" class="btn btn-info btn-sm mr-2" title="Expandir"><i class="fa-solid fa-maximize"></i></a>
            <button type="button" class="btn btn-secondary btn-sm" title="Rotar" onclick="rotarImg(' . $fila->id . ')"><i class="fa-solid fa-arrows-rotate"></i></button>
            ';
            $salida .= '
                    <div class="card border-primary mr-2 mt-2" style="width: 20em;">
                        <div class="card-header">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-left d-flex row">
                                <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                    <input type="text" class="form-control form-control-sm" onkeyup="actualizarOrden(' . $fila->id . ',' . " $('#ordenEvidencia$fila->id').val()" . ')" id="ordenEvidencia' . $fila->id . '" value="' . $fila->orden . '">
                                </div>
                                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 mt-2">
                                    <input type="text" class="form-control form-control-sm" onkeyup="actualizarNombre(' . $fila->id . ',' . " $('#nameEvidencia$fila->id').val()" . ')" id="nameEvidencia' . $fila->id . '" value="' . $fila->Descripcion . '">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-center">
                                <img src="' . $rutaAr . '" class="card-img-top" alt="IMG">
                            </div>
                        </div>
                        <div class="card-footer">
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 d-flex justify-content-center">
                                </div>
                                <div class="d-flex justify-content-end">
                                ' . $botones . '
                                </div>
                        </div>
                    </div>';
        }

        echo $salida;
        break;
    case 'rotarImg':
        $query = ejecutarConsultaSimpleFila("SELECT rutaArchivo FROM archivosReporte WHERE id='$id';")[0];
        $image = "../" . $query;
        $Name = basename($query);
        $Extension = pathinfo($query, PATHINFO_EXTENSION);
        //Creamos una nueva imagen a partir del fichero inicial
        $source = ($Extension == "png") ? imagecreatefrompng($image) : imagecreatefromjpeg($image);

        //Rotamos la imagen 270 grados
        $rotate = imagerotate($source, 270, 0);
        if ($Extension == "png") {
            echo (imagepng($rotate, $image)) ? 200 : 201;
        } else {
            echo (imagejpeg($rotate, $image)) ? 200 : 201;
        }

        break;
    case 'actualizarNombre':
        $query = ejecutarConsulta("UPDATE archivosReporte SET Descripcion='$nombreImg' WHERE id='$id'");
        echo $query ? 200 : 201;
        break;
    case 'actualizarOrden':
        $query = ejecutarConsulta("UPDATE archivosReporte SET orden='$orden' WHERE id='$id'");
        echo $query ? 200 : 201;
        break;
    case 'eliminarEvidencia':
        $query = ejecutarConsultaSimpleFila("SELECT rutaArchivo FROM archivosReporte WHERE id='$id';")[0];
        $archivo = "../" . $query;
        if (file_exists($archivo)) {
            if (unlink($archivo)) {
                $query = ejecutarConsulta("DELETE FROM archivosReporte WHERE id='$id'");
                echo $query ? 200 : 201;
            } else {
                echo 201;
            }
        } else {
            echo 200;
        }
        break;
}

function crearDirectorio($Ruta_Carpeta)
{
    if (!is_dir($Ruta_Carpeta)) {
        if (mkdir($Ruta_Carpeta, 0775, true)) {
            chmod($Ruta_Carpeta, 0775);
            return true;
        } else {
            return false;
        }
    } else {
        return true;
    }
};
