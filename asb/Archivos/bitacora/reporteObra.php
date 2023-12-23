<?php
session_start();
if (!isset($_SESSION['Id_Empleado'])) {
    header("location: ../../index.php");
}
include "../../global/conexion.php";
$idProyecto = base64_decode($_GET['idProyecto']);
$idReporte = base64_decode($_GET['idReporte']);
$datosGenerales = ejecutarConsultaSimpleFila("SELECT CONCAT(C.Nombre,' ', C.Apellido_P,' ', C.Apellido_M) AS cliente, OB.Nombre_Obra,O.Proyecto,O.Status FROM Ordenes_Trabajo O LEFT JOIN Clientes C ON(O.Id_Cliente=C.Id) LEFT JOIN Obras OB ON(O.Id_Obra=OB.Id) WHERE O.Id='$idProyecto'; ");
$query = ejecutarConsultaSimpleFila("SELECT CONCAT(E.Nombre,' ',E.Apellido_P,' ',E.Apellido_M) AS responsable,E.Correo,R.fechaInicio,R.FechaFinal,R.Descripcion FROM reportesObra R LEFT JOIN User U ON(R.idResponsable=U.Id) LEFT JOIN Empleados E ON(U.Id_Empleado=E.Id_Empleado) WHERE R.id='$idReporte';");
$archivos = ejecutarConsulta("SELECT * FROM archivosReporte WHERE idReporte='$idReporte'");
$salida = "";
$rutas = array();
$descripciones = array();
while ($fila = mysqli_fetch_object($archivos)) {
    $rutaDB = "../" . $fila->rutaArchivo;
    if (is_file($rutaDB)) {
        $extension = pathinfo($fila->rutaArchivo, PATHINFO_EXTENSION);
        $evidenciaA = ($extension == 'pdf') ? "../../img/pdf.jpg" : $rutaDB;
        array_push($rutas, $rutaDB);
        array_push($descripciones, $fila->Descripcion);
    }
}

$i1 = 0;
$i2 = 1;
$i3 = 2;
$cantidad = count($rutas);
$Can_Real = (count($rutas) / 2);

for ($i = 0; $i < $Can_Real; $i++) {
    $img1 = isset($rutas[$i1]) ? (pathinfo($rutas[$i1], PATHINFO_EXTENSION)) == "pdf" ? '<a href="' . $rutas[$i1] . '" target="_blank" rel="noopener noreferrer"><img src="../../img/pdf.jpg" alt="' . basename($rutas[$i1]) . '" srcset=""></a>' : '<a href="' . $rutas[$i1] . '" target="_blank" rel="noopener noreferrer"><img src="' . $rutas[$i1] . '" alt="' . basename($rutas[$i1]) . '" srcset=""></a>' : "";
    $img2 = isset($rutas[$i2]) ? (pathinfo($rutas[$i2], PATHINFO_EXTENSION)) == "pdf" ? '<a href="' . $rutas[$i2] . '" target="_blank" rel="noopener noreferrer"><img src="../../img/pdf.jpg" alt="' . basename($rutas[$i2]) . '" srcset=""></a>' : '<a href="' . $rutas[$i2] . '" target="_blank" rel="noopener noreferrer"><img src="' . $rutas[$i2] . '" alt="' . basename($rutas[$i2]) . '" srcset=""></a>' : "";
    $img3 = isset($rutas[$i3]) ? (pathinfo($rutas[$i3], PATHINFO_EXTENSION)) == "pdf" ? '<a href="' . $rutas[$i3] . '" target="_blank" rel="noopener noreferrer"><img src="../../img/pdf.jpg" alt="' . basename($rutas[$i3]) . '" srcset=""></a>' : '<a href="' . $rutas[$i3] . '" target="_blank" rel="noopener noreferrer"><img src="' . $rutas[$i3] . '" alt="' . basename($rutas[$i3]) . '" srcset=""></a>' : "";
    $descripciones[($i1)] = isset($descripciones[$i1]) ? $descripciones[$i1] : "";
    $descripciones[($i2)] = isset($descripciones[$i2]) ? $descripciones[$i2] : "";
    $descripciones[($i3)] = isset($descripciones[$i3]) ? $descripciones[$i3] : "";

    $salida .= '
     <table border="0" align="left" cellspacing=0 cellpadding=0 class="mb15">
        <tr>
            <th class="contenedor">
                ' . $img1 . '
            </th>
            <th class="W10"></th>
            <th class="contenedor">
                ' . $img2 . '
            </th>
            <th class="W10"></th>
            <th class="contenedor">
                ' . $img3 . '
            </th>
        </tr>
        <tr>
            <th class="W240">' . $descripciones[($i1)] . '</th>
            <th class="W10"></th>
            <th class="W240">' . $descripciones[($i2)] . '</th>
            <th class="W10"></th>
            <th class="W240">' . $descripciones[($i3)] . '</th>
        </tr>
    </table>';
    $i1 += 3;
    $i2 += 3;
    $i3 += 3;
}
ob_start();
?>
<style>
    .alineado {
        vertical-align: middle;
    }

    .mb15 {
        margin-bottom: 15px;
    }

    .W10 {
        width: 10px;
    }

    .W120 {
        width: 120px;
    }

    .W150 {
        width: 150px;
    }

    .W186 {
        width: 186px;
    }

    .W200 {
        width: 200px;
    }

    .W240 {
        width: 240px;
    }

    .W400 {
        width: 400px;
    }

    .W418 {
        width: 418px;
    }

    .W560 {
        width: 560px;
    }

    .W746 {
        width: 746px;
    }

    .textCenter {
        text-align: center;
    }

    .textDerecha {
        text-align: right;
    }

    .fondoA {
        background-color: #213846;
        color: white;
    }

    .lineaDebajo {
        border-bottom: 2px solid #f7ca46;
        padding-bottom: 5px;
        display: inline-block;
    }

    .lineaArriba {
        border-top: 2px solid #f7ca46;
        padding-top: 5px;
        display: inline-block;
    }

    .contenedor {
        width: 243px;
        height: 243px;
        overflow: hidden;
    }

    .contenedor img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
</style>
<page backtop="43mm" backtborder-top="10mm" backleft="0mm" backright="1mm">
    <page_header>
        <table border="0" align="left" cellspacing=0 cellpadding=0 class="mb15">
            <tr>
                <th class="W150 lineaDebajo">
                    <img src="../../../img/logoA.png" alt="Logo" width="150">

                </th>
                <th class="W400 lineaDebajo">
                    <h4>Automatización y Sistemas de Bombeo</h4>
                    Ingeniería aplicada a sistemas de bombeo y Tratamiento de aguas residuales
                </th>
                <th class="W200 lineaDebajo textDerecha">
                    Página [[page_cu]] / [[page_nb]]
                </th>
            </tr>
        </table>
    </page_header>

    <table border="0" align="left" cellspacing=0 cellpadding=0 class="mb15">
        <tr>
            <th colspan="3" class="textCenter W746">
                <h2>REPORTE DE OBRA</h2>
            </th>
        </tr>
        <tr>
            <th colspan="3" class="textDerecha"><?php echo "N° Proyecto: " . $idProyecto ?></th>
        </tr>
        <tr>
            <th colspan="3" class="textDerecha"><?php echo fechaActual(); ?></th>
        </tr>
    </table>

    <table border="0" align="left" cellspacing=0 cellpadding=0 class="mb15">
        <tr>
            <td class="W560"><b>Cliente:</b><?php echo $datosGenerales['cliente'] ?></td>
            <td class="W186"><b>Fecha inicial:</b><?php echo $query['fechaInicio']; ?></td>
        </tr>
        <tr>
            <td class="W560"><b>Obra:</b> <?php echo $datosGenerales['Nombre_Obra'] ?></td>
            <td class="W186"><b>Fecha final:</b> <?php echo $query['FechaFinal']; ?></td>
        </tr>
        <tr>
            <td class="W746" colspan="2"><b>Proyecto:</b> <?php echo $datosGenerales['Proyecto'] ?></td>
        </tr>
    </table>
    <p><b>A QUIEN CORRESPONDA:</b></p>
    <p><b>P R E S E N T E:</b></p>
    <p>POR MEDIO DEL PRESENTE ME PERMITO INFORMAR AL RESPECTO DEL SERVICIO DE <b><?php echo $datosGenerales['Proyecto'] ?></b> DE LA OBRA <b><?php echo $datosGenerales['Nombre_Obra'] ?>.</b></p>
    <p><b>HALLAZGOS:</b></p>
    <p>
        <?php echo nl2br($query['Descripcion']); ?>
    </p>
    <p>
        SIN MÁS QUE AGREGAR AL PRESENTE REPORTE, QUEDO PENDIENTE Y ENVIÓ UN CORDIAL SALUDO
    </p>
    <table border="0" align="left" cellspacing=0 cellpadding=0 class="mb15">
        <tr>
            <th class="W746 textCenter"><?php echo $query['responsable']; ?></th>
        </tr>
        <tr>
            <th class="W746 textCenter"><a href="mailto:<?php echo $query['Correo']; ?>"><?php echo $query['Correo']; ?></a></th>
        </tr>
    </table>
    <page_footer>
        <table align="left" border="0" cellspacing=0 cellpadding=0>
            <tr>
                <th class="lineaArriba" style="width: 420px; text-aling:left;">Privada Leona Vicario 10, Santa Rosa 30 centro, C.P.: 62772,<br> Tlaltizapán Morelos.</th>
                <th class="textDerecha lineaArriba" style="width: 318px;">
                    <a href="www.asbombeo.com" target="_blank" rel="noopener noreferrer">www.asbombeo.com</a> <br>
                    <a href="ventas@asbombeo.com" target="_blank" rel="noopener noreferrer">ventas@asbombeo.com</a> <br>
                    <a href="#" target="_blank" rel="noopener noreferrer">Teléfono: 734 108 96 80</a>
                </th>
            </tr>
        </table>
    </page_footer>

</page>
<?php if ($salida != "") { ?>
    <page backtop="43mm" backtborder-top="10mm" backleft="0mm" backright="1mm">
        <page_header>
            <table border="0" align="left" cellspacing=0 cellpadding=0 class="mb15">
                <tr>
                    <th class="W150 lineaDebajo">
                        <img src="../../img/logoA.png" alt="Logo" width="150">
                    </th>
                    <th class="W400 lineaDebajo">
                        <h4>Automatización y Sistemas de Bombeo</h4>
                        Ingeniería aplicada a sistemas de bombeo y Tratamiento de aguas residuales
                    </th>
                    <th class="W200 lineaDebajo textDerecha">
                        Página [[page_cu]] / [[page_nb]]
                    </th>
                </tr>
            </table>
        </page_header>

        <table border="0" align="left" cellspacing=0 cellpadding=0 class="mb15">
            <tr>
                <th colspan="3" class="textCenter W746">
                    <h2>EVIDENCIAS DE OBRA</h2>
                </th>
            </tr>
            <tr>
                <th colspan="3" class="textDerecha"><?php echo "N° Proyecto: " . $idProyecto ?></th>
            </tr>
            <tr>
                <th colspan="3" class="textDerecha"><?php echo fechaActual(); ?></th>
            </tr>
        </table>
        <?php echo $salida; ?>

        <page_footer>
            <table align="left" border="0" cellspacing=0 cellpadding=0>
                <tr>
                    <th class="lineaArriba" style="width: 420px; text-aling:left;">Privada Leona Vicario 10, Santa Rosa 30 centro, C.P.: 62772,<br> Tlaltizapán Morelos.</th>
                    <th class="textDerecha lineaArriba" style="width: 318px;">
                        <a href="www.asbombeo.com" target="_blank" rel="noopener noreferrer">www.asbombeo.com</a> <br>
                        <a href="ventas@asbombeo.com" target="_blank" rel="noopener noreferrer">ventas@asbombeo.com</a> <br>
                        <a href="#" target="_blank" rel="noopener noreferrer">Teléfono: 734 108 96 80</a>
                    </th>
                </tr>
            </table>
        </page_footer>

    </page>
<?php } ?>
<?php

$content = ob_get_clean();
require '../../../Library/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf('P', 'LETTER', 'es', 'true', 'UTF-8');
$html2pdf->writeHTML($content);
$html2pdf->output('Reporte de obra.pdf');
?>