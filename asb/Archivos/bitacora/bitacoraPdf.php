<?php
session_start();
if (!isset($_SESSION['Id_Empleado'])) {
    header("location: ../../index.php");
}

$idProyecto = base64_decode($_GET['id']);
include "../../global/conexion.php";
$datosGenerales = ejecutarConsultaSimpleFila("SELECT CONCAT(C.Nombre,' ', C.Apellido_P,' ', C.Apellido_M) AS cliente, OB.Nombre_Obra,O.Proyecto,O.Status FROM Ordenes_Trabajo O LEFT JOIN Clientes C ON(O.Id_Cliente=C.Id) LEFT JOIN Obras OB ON(O.Id_Obra=OB.Id) WHERE O.Id='$idProyecto'; ");
$fechaInicial = ejecutarConsultaSimpleFila("SELECT fechaInicio FROM reportesObra WHERE idProyecto='$idProyecto' ORDER BY fechaInicio ASC LIMIT 1")[0];
$fechaFinal = ejecutarConsultaSimpleFila("SELECT FechaFinal FROM reportesObra WHERE idProyecto='$idProyecto' ORDER BY FechaFinal DESC LIMIT 1")[0];
$query = ejecutarConsulta("SELECT CONCAT(E.Nombre,' ', E.Apellido_P,' ',E.Apellido_M) AS responsable, R.fechaInicio,R.FechaFinal,R.Descripcion,R.status FROM reportesObra R LEFT JOIN User U ON(R.idResponsable=U.Id) LEFT JOIN Empleados E ON(U.Id_Empleado=E.Id_Empleado) WHERE R.idProyecto='$idProyecto' ORDER BY fechaInicio ASC;");
$salida = "";
while ($fila = mysqli_fetch_object($query)) {
    $salida .= "
     <tr>
        <td class='W200 textCenter alineado'>$fila->responsable</td>
        <td class='W120 alineado'>
        <b>Fecha inicial:</b>
        $fila->fechaInicio
        <br>
        <b>Fecha final:</b>
        $fila->FechaFinal
        </td>
        <td class='W418'>" . nl2br($fila->Descripcion) . "</td>
    </tr>
    ";
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
                <h2>BITÁCORA DE TRABAJO DE CAMPO</h2>
            </th>
        </tr>
        <tr>
            <th colspan="3" class="textDerecha"><?php echo "N° Proyecto: " . $idProyecto; ?></th>
        </tr>
        <tr>
            <th colspan="3" class="textDerecha"><?php echo fechaActual(); ?></th>
        </tr>
    </table>

    <table border="0" align="left" cellspacing=0 cellpadding=0 class="mb15">
        <tr>
            <td class="W560"><b>Cliente:</b><?php echo $datosGenerales['cliente'] ?></td>
            <td class="W186"><b>Fecha inicial:</b><?php echo $fechaInicial; ?></td>
        </tr>
        <tr>
            <td class="W560"><b>Obra:</b> <?php echo $datosGenerales['Nombre_Obra'] ?></td>
            <td class="W186"><b>Fecha final:</b> <?php echo $fechaFinal; ?></td>
        </tr>
        <tr>
            <td class="W746" colspan="2"><b>Proyecto:</b> <?php echo $datosGenerales['Proyecto'] ?></td>
        </tr>
    </table>

    <table border="0" align="left" cellspacing=0 cellpadding=0 class="mb15">
        <tr>
            <th class="textCenter W200 fondoA">Responsable</th>
            <th class="textCenter W120 fondoA">Periodo</th>
            <th class="textCenter W418 fondoA">Descripción de la actividad</th>
        </tr>
        <?php echo $salida; ?>
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
<?php
$content = ob_get_clean();
require '../../../Library/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf('P', 'LETTER', 'es', 'true', 'UTF-8');
$html2pdf->writeHTML($content);
$html2pdf->output('BITACORA_PROYECTO_' . $id . '.pdf');
?>