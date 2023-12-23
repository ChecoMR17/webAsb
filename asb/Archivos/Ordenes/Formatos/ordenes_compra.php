<?php session_start();
if (!isset($_SESSION['Id_Empleado'])) {
    header("location:../../../../index.php");
}
include "../../../global/conexion.php";
include "../../../../global/numaLetras.php";
$Fecha_Actual = date("Y-m-d");
// Establecer la configuración regional en español
setlocale(LC_TIME, 'es_ES.utf8');

// Obtener la fecha actual en letra y en español
$fecha = strftime("%A %d de %B del %Y");
$OC = base64_decode($_GET['OC']);
$Salida = "";
$subtotal = 0;
$Datos_Principales = ejecutarConsultaSimpleFila("SELECT OC.Num_OT,O.Proyecto,concat_ws(' ',P.Nombre,P.Apellido_P,P.Apellido_M) AS Proveedor,
SP.Nombre AS Sucursal,SP.Calle,SP.N_Exterior,SP.N_Interior,SP.Colonia,SP.CP,M.Nombre AS Municipio,E.Nombre AS Estado,SP.Celular,SP.Telefono,
SP.Correo_C,SP.Correo_P,P.RFC,B.Nombre AS Banco,BP.Sucursal AS S_Banco,BP.Cuenta,BP.Referencia,BP.Clave,OC.Form_Pago,OC.Fec_ent,OC.Descuento,OC.Obs FROM OT_OC OC 
LEFT JOIN Ordenes_Trabajo O ON(OC.Num_OT=O.Id)
LEFT JOIN Proveedores P ON(OC.Id_Prov=P.Id)
LEFT JOIN Sucursales_Proveedores SP ON(OC.Cons_Suc=SP.Id)
LEFT JOIN Estados E ON(SP.Id_Estado=E.Id_Estado)
LEFT JOIN Municipios M ON(SP.Id_Municipios=M.Id_Municipios)
LEFT JOIN Bancos_Proveedores BP ON(OC.Cons_Cta=BP.Id)
LEFT JOIN Bancos B ON(BP.Id_Banco=B.Id)
WHERE OC.Cons_OC='$OC';");

$Datos_Principales["Correo_P"] = $Datos_Principales["Correo_P"] != "" ? ", " . "<a href='mailto:" . $Datos_Principales["Correo_P"] . "' target='_blank' rel='noopener noreferrer'>" . $Datos_Principales["Correo_P"] . "</a>" : "";
$Correos = "<a href='mailto:" . $Datos_Principales["Correo_C"] . "' target='_blank' rel='noopener noreferrer'>" . $Datos_Principales["Correo_C"] . "</a>" . $Datos_Principales["Correo_P"];

$teledono = $Datos_Principales["Telefono"] != "" ? "(" . substr($Datos_Principales["Telefono"], 0, 3) . ") " . substr($Datos_Principales["Telefono"], 3, 3) . "-" . substr($Datos_Principales["Telefono"], 6) : "";


$Datos_Principales["Telefono"] = $Datos_Principales["Telefono"] != "" ? ", " . "<a href='tel:" . $Datos_Principales["Telefono"] . "' target='_blank' rel='noopener noreferrer'>" . $teledono . "</a>" : "";

$Celular = "(" . substr($Datos_Principales["Celular"], 0, 3) . ") " . substr($Datos_Principales["Celular"], 3, 3) . "-" . substr($Datos_Principales["Celular"], 6);
$Telefonos = "<a href='tel:" . $Datos_Principales["Celular"] . "' target='_blank' rel='noopener noreferrer'>" . $Celular . "</a>" . $Datos_Principales["Telefono"];

$Datos_Principales["N_Exterior"] = ($Datos_Principales["N_Exterior"] == "0") ? "S/N" : $Datos_Principales["N_Exterior"];
$Datos_Principales["N_Interior"] = ($Datos_Principales["N_Interior"] != "0") ? ", # " . $Datos_Principales["N_Interior"] . " " : "";
$Direccion = "C. " . $Datos_Principales["Calle"] . ", # " . $Datos_Principales["N_Exterior"] . $Datos_Principales["N_Interior"] . ", Loc. " . $Datos_Principales["Colonia"] . ", CP. " . $Datos_Principales["CP"] . ", " . $Datos_Principales["Municipio"] . ", " . $Datos_Principales["Estado"];

$Direccion_G = $Datos_Principales["Calle"] . " " . $Datos_Principales["N_Exterior"] . " " . $Datos_Principales["Municipio"] . " " . $Datos_Principales["Estado"];

$Direccion = "<a href='www.google.com/maps/search/?api=1&query=" . $Direccion_G . "' target='_blank' rel='noopener noreferrer'>" . strtoupper($Direccion) . "</a>";


$Materiales = ejecutarConsulta("SELECT P.Id_Inv,M.Desc_Mat,Abrev,P.Cant,P.Pre_Prov,P.Cant*P.Pre_Prov AS Total FROM OT_OC_Partidas P 
LEFT JOIN Inventarios I ON (P.Id_Inv=I.Id_Inv)
LEFT JOIN Cat_Materiales M ON(I.Id_Mat=M.Id_Mat)
LEFT JOIN Cat_Unidad_Medida U ON(M.Id_UM2=U.Id_UM)
WHERE P.Cons_OC='$OC';");
while ($fila = mysqli_fetch_object($Materiales)) {
    $Salida .= "
            <tr>
                <td class='text_left /* `W322` is a CSS class that sets the width of an HTML element to
                322 pixels. It is used in the code to define the width of a
                table cell. */
                W322'>$fila->Desc_Mat</td>
                <td class='text_center W100'>$fila->Cant</td>
                <td class='text_center W100'>$fila->Abrev</td>
                <td class='text_center W100'>$" . number_format($fila->Pre_Prov, 2) . "</td>
                <td class='text_center W100'>$" . number_format($fila->Total, 2) . "</td>
            </tr>";
    $subtotal += $fila->Total;
}
$Total_OC = number_format((($subtotal - $Datos_Principales["Descuento"]) + ($subtotal * 0.16)), 2);;
ob_start();
?>
<style>
    .W40 {
        width: 40px;
    }

    .W100 {
        width: 100px;
    }

    .W110 {
        width: 110px;
    }

    .W150 {
        width: 150px;
    }

    .W200 {
        width: 200px;
    }

    .W322 {
        width: 322px;
    }

    .W400 {
        width: 400px;
    }

    .W546 {
        width: 546px;
    }

    .W555 {
        width: 555px;
    }

    .W754 {
        width: 754px;
    }

    .mb_15 {
        margin-bottom: 15px;
    }

    .text_right {
        text-align: right;
    }

    .text_center {
        text-align: center;
    }

    .text_left {
        text-align: left;
    }

    .Color_Fondo {
        background-color: #b1c797;
    }

    .Color_Fondo_y {
        background-color: #ffff99;
    }

    @media all {
        .page-break {
            display: none;
            page-break-after: avoid
        }
    }

    @media print {
        .page-break {
            display: block;
            page-break-before: always;
        }
    }
</style>
<page backtop="5mm" backbottom="10mm" backleft="0mm" backright="1mm">
    <div class="page-break" style="position: absolute;">

        <table border="0" align="left" cellspacing=0 cellpadding=0 class="mb_15">
            <tr>
                <th class="W150"><img src="../../../../img/Logo.png" alt="Logo" width="150"></th>
                <th class="W400">
                    <h4>Automatización y Sistemas de Bombeo</h4>
                    Ingeniería aplicada a sistemas de bombeo y Tratamiento de aguas residuales
                </th>
                <th class="W200"></th>
            </tr>
            <tr>
                <th colspan="3" class="text_center Color_Fondo">ORDEN DE COMPRA</th>
            </tr>
        </table>

        <table border="0" align="left" cellspacing=0 cellpadding=0 class="mb_15">
            <tr>
                <th colspan="2" class="text_center Color_Fondo_y">DATOS GENERALES</th>
            </tr>
            <tr>
                <td class="W546"><b>Numero de orden de trabajo: </b> <?php echo $Datos_Principales["Num_OT"]; ?></td>
                <td class="text_right W200"><?php echo $fecha; ?></td>
            </tr>
            <tr>
                <td class="W546"><b>Proyecto: </b><?php echo $Datos_Principales["Proyecto"]; ?></td>
                <td class="W200"><b>Numero de OC: </b> <?php echo $OC; ?></td>
            </tr>
            <tr>
                <td class="W546"><b>Proveedor: </b> <?php echo $Datos_Principales["Proveedor"]; ?></td>
                <td class="W200"><b>Datos bancarios: </b></td>
            </tr>
            <tr>
                <td class="W546"><b>Sucursal: </b> <?php echo $Datos_Principales["Sucursal"]; ?></td>
                <td class="W200"><b>Banco: </b> <?php echo $Datos_Principales["Banco"]; ?></td>
            </tr>
            <tr>
                <td class="W546"><b>Domicilio: </b> <?php echo $Direccion; ?></td>
                <td class="W200"><b>Sucursal: </b> <?php echo $Datos_Principales["S_Banco"]; ?></td>
            </tr>
            <tr>
                <td class="W546"><b>N° telefónicos: </b> <?php echo $Telefonos; ?></td>
                <td class="W200"><b>Cuenta: </b> <?php echo $Datos_Principales["Cuenta"]; ?></td>
            </tr>
            <tr>
                <td class="W546"><b>RFC: </b> <?php echo $Datos_Principales["RFC"]; ?></td>
                <td class="W200"><b>Referencia: </b> <?php echo $Datos_Principales["Referencia"]; ?></td>
            </tr>
            <tr>
                <td class="W546"><b>Correo: </b> <?php echo $Correos; ?></td>
                <td class="W200"><b>Clabe: </b> <?php echo $Datos_Principales["Clave"]; ?></td>
            </tr>
            <tr>
                <td class="W546"><b>Fecha de entrega: </b> <?php echo $Datos_Principales["Fec_ent"]; ?></td>
                <td class="W200"><b>Forma de pago: </b> <?php echo $Datos_Principales["Form_Pago"]; ?></td>
            </tr>
        </table>

        <table border="1" align="left" cellspacing=0 cellpadding=0 class="mb_15">
            <tr>
                <th colspan="5" class="text_center Color_Fondo_y">LISTA DE MATERIALES</th>
            </tr>
            <tr>
                <th class="text_center">Material</th>
                <th class="text_center">Cantidad</th>
                <th class="text_center">Unidad</th>
                <th class="text_center">Precio</th>
                <th class="text_center">Total</th>
            </tr>
            <?php echo $Salida; ?>

        </table>
        <table border="0" align="left" cellspacing=0 cellpadding=0 class="mb_15">
            <tr>
                <th class="W555"></th>
                <th class="text_left W100 Color_Fondo">Subtotal</th>
                <th class="text_right Color_Fondo_y W110"><?php echo "$" . number_format($subtotal, 2); ?></th>
            </tr>
            <tr>
                <th class="W555"></th>
                <th class="text_left W100 Color_Fondo">Descuento</th>
                <th class="text_right Color_Fondo_y W110"><?php echo "$" . number_format($Datos_Principales["Descuento"], 2); ?></th>
            </tr>
            <tr>
                <th class="W555"></th>
                <th class="text_left W100 Color_Fondo">I.V.A (16%)</th>
                <th class="text_right Color_Fondo_y W110"><?php echo "$" . number_format(($subtotal * 0.16), 2); ?></th>
            </tr>
            <tr>
                <th class="W555"></th>
                <th class="text_left W100 Color_Fondo">Total</th>
                <th class="text_right Color_Fondo_y W110"><?php echo "$" . $Total_OC; ?></th>
            </tr>
            <tr>
                <th colspan="3" class="text_left"><?php echo convertir($Total_OC, "MXN", ""); ?></th>
            </tr>
        </table>
        <table border="0" align="left" cellspacing=0 cellpadding=0 class="">
            <tr>
                <th class="" style="width: 760px;">Observaciones: </th>
            </tr>
            <tr>
                <td class="" style="width: 760px;"><?php echo nl2br($Datos_Principales["Obs"]); ?></td>
            </tr>
        </table>

    </div>
    <page_footer>
        <table align="left" border="0" cellspacing=0 cellpadding=0>
            <tr>
                <th class="W550">Privada Leona Vicario 10, Santa Rosa 30 centro, C.P.: 62772, Tlaltizapán Morelos.</th>
                <th class="W40"></th>
                <th class="text_left W200">
                    <a href="www.asbombeo.com" target="_blank" rel="noopener noreferrer">www.asbombeo.com</a> <br>
                    <a href="mailto:ventas@asbombeo.com" target="_blank" rel="noopener noreferrer">ventas@asbombeo.com</a> <br>
                    <a href="tel:734-108-9680" target="_blank" rel="noopener noreferrer">Teléfono: 734 108 96 80</a>

                </th>
            </tr>
        </table>
    </page_footer>
</page>
<?php
$content = ob_get_clean();
require '../../../../Library/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf('P', 'LETTER', 'es', 'true', 'UTF-8');
$html2pdf->writeHTML($content);
$html2pdf->output('Orden de compras.pdf');
sqlsrv_close($conn);
?>