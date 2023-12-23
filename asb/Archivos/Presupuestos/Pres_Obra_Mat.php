<?php
session_start();
if (!isset($_SESSION['Id_Empleado'])) {
    header("location: ../../index.php");
}

include '../../global/conexion.php';
include '../../../global/numaLetras.php';

$Num_OT = isset($_GET['Num_OT']) ? base64_decode($_GET['Num_OT']) : 0;
$Num_Cot = isset($_GET['Num_Cot']) ? base64_decode($_GET['Num_Cot']) : '';

$caracteres = array(",", "S.A. DE C.V.");

$otData = ejecutarConsultaSimpleFila(
    "SELECT Proyecto, CO.Nombre_Obra Nom_Obra, CONCAT(CC.Nombre, ' ', CC.Apellido_P, ' ', CC.Apellido_M) Nom_Cte, CO.Calle Calle_Cte, CO.Colonia
            , CONCAT(CM.Nombre,', ',CE.Nombre) AS Poblacion,
            CONCAT(CCT.Nombre, ' ', CCT.Apellido_P, ' ', CCT.Apellido_M) Nom_Cont, CCT.Telefono AS Tel, CONCAT(CCT.Correo_C, ' ', CCT.Correo_P) AS Correo
            FROM Ordenes_Trabajo O LEFT JOIN Obras CO ON (O.Id_Obra = CO.Id) LEFT JOIN Clientes CC ON (CO.Id_Cliente = CC.Id)
            LEFT JOIN Contactos_Clientes CCT ON (O.Id_Contacto = CCT.Id) LEFT JOIN Estados CE ON (CE.Id_Estado = CO.Id_Estado)
            LEFT JOIN Municipios CM ON (CO.Id_Municipios=CM.Id_Municipios AND CM.Id_Estado = CO.Id_Estado) WHERE O.Id ='$Num_OT';"
);

$presData = ejecutarConsultaSimpleFila("SELECT PS.*, U.Usuario User, UW.Usuario Aut FROM Presupuesto PS LEFT JOIN User U ON (PS.U_Alta=U.Id) LEFT JOIN User UW ON (PS.U_Aut=UW.Id) WHERE Num_Cot='$Num_Cot';");
// Concepto
empty($presData['Concepto']) ? $Concepto = $otData['Proyecto'] : $Concepto = $presData['Concepto'];
// Ubicación
empty($presData['Ubicacion']) ? $Ubicacion = $otData['Poblacion'] : $Ubicacion = $presData['Ubicacion'];

$Fec_Aut = empty($presData['Fec_Aut']) ? "" : $presData['Fec_Aut'];

if ($Fec_Aut != "") {
    $Fec_Aut = fechaEs($Fec_Aut);
} else {
    $Fec_Aut = "Autorización pendiente";
}

$leyenda = "";

if ($presData['Cons'] > 1) {
    $Cons = $presData['Cons'];
    $pres_Ant = ejecutarConsultaSimpleFila("SELECT * FROM Presupuesto WHERE Num_OT=$Num_OT AND  Cons!=$Cons AND Status='U' ORDER BY Fec_Aut DESC LIMIT 1");
    $Fec = $pres_Ant['Fec_Aut'];
    $leyenda = ". Este presupuesto remplaza al '" . $pres_Ant['Num_Cot'] . "' con fecha $Fec";
}

function fechaEs($fecha)
{
    $fecha = substr($fecha, 0, 10);
    $numeroDia = date('d', strtotime($fecha));
    $dia = date('l', strtotime($fecha));
    $mes = date('F', strtotime($fecha));
    $anio = date('Y', strtotime($fecha));
    $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
    $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
    $nombredia = str_replace($dias_EN, $dias_ES, $dia);
    $meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
    return "$nombredia, $numeroDia de $nombreMes de $anio";
}

// Validamos la emresa
$membretado = "../../img/Logo.png";

/*  Aplicar:
        *  Imp_CI %
        *  Imp_Fin %
        *  Imp_Util %
        *  Imp_Otro %
        *  Descuento %
    */

$Imp_CI = 0;
$Imp_Fin = 0;
$Imp_Util = 0;
$Imp_Otro = 0;

$total = 0;
$Descuento = 0;
$Imp_CI = $presData['Imp_CD'] + ($presData['Imp_CD'] * $presData['Imp_CI'] / 100);
$total = $Imp_CI;

$Imp_Fin = $total + (($total * $presData['Imp_Fin']) / 100);
$total = $Imp_Fin;

$Imp_Util = $total + (($total * $presData['Imp_Util']) / 100);
$total = $Imp_Util;

$Imp_Otro = $total + (($total * $presData['Imp_Otro']) / 100);
$total = $Imp_Otro;

// Aplicamos el descuento si lo hay
if ($presData['Por_Desc'] > 0) {
    $Descuento = $total * ($presData['Por_Desc'] / 100);
}

$titulos = "";

$sql = ejecutarConsulta("SELECT * FROM Presupuesto_Titulos WHERE Num_Cot='$Num_Cot' ORDER BY Clave ASC");
while ($rst = $sql->fetch_object()) {
    $tot = 0;

    // Consultamos matrices
    $sql2 = ejecutarConsulta("SELECT PM.* FROM Presupuesto_Matrices_Cot PM LEFT JOIN Presupuesto_Subtitulos PS ON (PS.Num_Cot=PM.Num_Cot AND PM.Clv=PS.Clv)
            LEFT JOIN Presupuesto_Titulos PT ON (PM.Num_Cot=PT.Num_Cot AND PS.Clave=PT.Clave) WHERE PM.Num_Cot='$Num_Cot' AND PS.Clave='$rst->Clave'");

    while ($rst2 = $sql2->fetch_object()) {
        $HE = ejecutarConsultaSimpleFila("SELECT SUM(Cant * PM.PU * $rst2->HE/100) AS HE FROM Presupuesto_Mat_Cot PM LEFT JOIN
                Cat_Materiales PInv ON (PM.Cve = PInv.Cve_Mat) WHERE Num_Cot = '$Num_Cot' AND Cod='$rst2->Cod' AND Clave='$rst2->Clv' AND Tipo='M'")[0];
        $importe = ($rst2->PU * $rst2->Cant) + ($HE * $rst2->Cant);
        $porcentaje = $importe / $presData['Imp_CD'] * 100;

        $tot += $total * $porcentaje / 100;
    }

    $titulos .= "<tr>
            <th height=50 class='borderL ' width=80 align='center'>$rst->Clave</th>
            <th height=50 class='' width=440>$rst->Titulo</th>
            <th height=50 class='borderR ' width=110 align='right'>$" . number_format($tot, 2) . " &nbsp;&nbsp;&nbsp;&nbsp;</th>
        </tr>";
}

$Nombre = ejecutarConsultaSimplefila("SELECT CONCAT(Nombre, ' ', Apellido_P, ' ', Apellido_M) AS Nombre FROM User U LEFT JOIN Presupuesto P
                        ON (U.Id_Empleado=P.U_Alta) LEFT JOIN Empleados E ON (U.Id_Empleado=E.Id_Empleado) WHERE Num_Cot='$Num_Cot'")['Nombre'];
ob_start();
?>
<style>
    .bg-blue {
        background-color: lightgreen;
    }

    .bg-lightblue {
        background-color: #90ee90;
    }

    .text-center {
        text-align: center;
    }

    .text-right {
        text-align: right;
    }

    .negritas {
        font-weight: bold;
    }

    .text-left {
        text-align: left;
    }

    .T8 {
        font-size: 8px;
    }

    .T9 {
        font-size: 9px;
    }

    .T10 {
        font-size: 10px;
    }

    .T11 {
        font-size: 11px;
    }

    .T12 {
        font-size: 12px;
    }

    .T13 {
        font-size: 13px;
    }

    .T14 {
        font-size: 14px;
    }

    .T16 {
        font-size: 16px;
    }

    .T20 {
        font-size: 20px;
    }

    .W160 {
        width: 160px;
    }

    .W166 {
        width: 166px;
    }

    .W12 {
        width: 12px;
    }

    .W200 {
        width: 200px;
    }

    .W218 {
        width: 218px;
    }

    .W387 {
        width: 383px;
    }

    .W427 {
        width: 427px;
    }

    .W400 {
        width: 400px;
    }

    .W467 {
        width: 467px;
    }

    .W500 {
        width: 500px;
    }

    .W530 {
        width: 530px;
    }

    .W573 {
        width: 573px;
    }

    .W600 {
        width: 600px;
    }

    .W654 {
        width: 654px;
    }

    .H12 {
        height: 12px;
    }

    .H15 {
        height: 15px;
    }

    .H25 {
        height: 25px;
    }

    .H40 {
        height: 40px;
    }

    .H55 {
        height: 55px;
    }

    .H70 {
        height: 70px;
    }

    .H30 {
        height: 30px;
    }

    .W40 {
        width: 40px;
    }

    .W50 {
        width: 50px;
    }

    .W60 {
        width: 60px;
    }

    .W80 {
        width: 80px;
    }

    .border {
        border: 1px solid green;
    }

    .borderT {
        border-top: 1px solid green;
    }

    .borderL {
        border-left: 1px solid green;
    }

    .borderR {
        border-right: 1px solid green;
    }

    .borderB {
        border-bottom: 1px solid green;
    }

    .underline {
        text-decoration: underline;
    }

    .bg-green {
        background-color: green;
        color: white;
    }

    table,
    td,
    th {
        vertical-align: middle;
    }
</style>
<page backtop="50mm" backbottom="10mm" backleft="0mm" backright="0mm">
    <page_header>
        <table border='0' align="center" cellspacing=0 cellpadding=0 class='border'>
            <tr>
                <th class='' rowspan=2><img src="../../../img/Logo.png" alt="logo" width=120></th>
                <th class='T20' width=600>Automatización y Sistemas de Bombeo</th>
            </tr>
            <tr>
                <td class='T12'>Ingeniería aplicada a sistemas de bombeo y tratamiento de <br> aguas residuales. <br><br></td>
            </tr>
        </table>

        <table border='0' align="center" cellspacing=0 cellpadding=0 class='T10'>
            <tr>
                <td width=50 class=' '>Cliente:</td>
                <td class='' width=360 colspan='2'><?php echo $otData['Nom_Cte']; ?></td>
                <td class=' text-right' width=100>Presupuesto:</td>
                <th class='  text-center' style="color: red" width=100><?php echo $Num_Cot; ?></th>
            </tr>
            <tr>
                <td width=50 class=''>Ubicación:</td>
                <td class='' colspan='2'><?php echo $presData['Poblacion'] == 'S' ? $otData['Poblacion'] : ''; ?></td>
                <td class='text-center' colspan='2'>Tlaltizapán Morelos</td>
            </tr>
            <tr>
                <td width=50 class=''>Contacto:</td>
                <td class='' colspan=2><?php echo $otData['Nom_Cont']; ?></td>
                <td class='text-center' colspan='2'><?php echo $Fec_Aut; ?></td>
            </tr>
            <tr>
                <td width=50 class=' '>E-mail:</td>
                <td class='' width=250><a href='mailto:<?php echo $presData['Correo'] == 'S' ? $otData['Correo'] : ''; ?>'><?php echo $presData['Correo'] == 'S' ? $otData['Correo'] : ''; ?></a></td>
                <td class='' colspan='2'>Telefono: <a href="tel:<?php echo $presData['Tel'] == 'S' ? $otData['Tel'] : ''; ?>"><?php echo $presData['Tel'] == 'S' ? $otData['Tel'] : ''; ?></a></td>
                <td class=' '></td>
            </tr>
        </table>

        <br>

        <table border='0' align="center" cellspacing=0 cellpadding=0 class='T12'>
            <tr>
                <th width=630 class=' '>Equipamiento: <?php echo $otData['Proyecto']; ?></th>
            </tr>

            <tr>
                <th class=''>Obra: <?php echo $otData['Nom_Obra']; ?></th>
            </tr>

            <tr>
                <th class=''>Lugar: <?php echo $presData['Poblacion'] == 'S' ? $otData['Poblacion'] : ''; ?></th>
            </tr>
        </table>
    </page_header>

    <h3 class='text-center'> PRESUPUESTO </h3>
    <table border='0' align="center" cellspacing=0 cellpadding=0 class='T12'>
        <tr class='bg-green text-center'>
            <th height=20 width=50>Clave</th>
            <th width=300>Descripción</th>
            <th width=80>Total</th>
        </tr>

        <?php echo $titulos; ?>

        <tr>
            <th height=15 align='right' class='borderL' colspan=2>Subtotal: </th>
            <th height=15 align='right' class='borderR'>$<?php echo number_format($total, 2); ?> &nbsp;&nbsp;&nbsp;&nbsp;</th>
        </tr>

        <?php if ($Descuento > 0) { ?>
            <tr>
                <th height=15 align='right' class='borderL' colspan=2>Descuento: </th>
                <th height=15 align='right' class='borderR'>$<?php echo number_format($Descuento, 2); ?> &nbsp;&nbsp;&nbsp;&nbsp;</th>
            </tr>

            <tr>
                <th height=15 align='right' class='borderL' colspan=2>16 % del IVA: </th>
                <th height=15 align='right' class='borderR'>$<?php echo number_format(($total - $Descuento) * .16, 2); ?> &nbsp;&nbsp;&nbsp;&nbsp;</th>
            </tr>

            <tr>
                <th height=15 align='right' class='borderL' colspan=2>Total: </th>
                <th height=15 align='right' class='borderR'>$<?php echo number_format(($total - $Descuento) * 1.16, 2); ?> &nbsp;&nbsp;&nbsp;&nbsp;</th>
            </tr>
        <?php } else { ?>
            <tr>
                <th height=15 align='right' class='borderL' colspan=2>16 % del IVA: </th>
                <th height=15 align='right' class='borderR'>$<?php echo number_format($total * .16, 2); ?> &nbsp;&nbsp;&nbsp;&nbsp;</th>
            </tr>

            <tr>
                <th height=15 align='right' class='borderL' colspan=2>Total: </th>
                <th height=15 align='right' class='borderR'>$<?php echo number_format($total * 1.16, 2); ?> &nbsp;&nbsp;&nbsp;&nbsp;</th>
            </tr>

        <?php }
        $presData['Nota'] = str_replace("&QUOT;", '"', $presData['Nota']);
        ?>

        <tr>
            <th height=15 align='right' class='borderL borderR' colspan=3>(*<?php echo convertir((($total - $Descuento) * 1.16), "MXN", 0); ?>*)</th>
        </tr>

        <tr>
            <th height=30 width=600 align='' class='borderL borderR borderB' colspan=3 width=420><br><br>&nbsp;&nbsp; Notas: <?php echo $presData['Nota']; ?></th>
        </tr>
    </table>
    <br>
    <table border='0' align="center" cellspacing=0 cellpadding=0 class='T11'>
        <tr>
            <th height=20 colspan=2 class='border bg-green' width=630> Condiciones comerciales:</th>
        </tr>

        <tr>
            <td height=30 width=60 class='borderL'>&nbsp;&nbsp;&nbsp;&nbsp; Forma de pago:</td>
            <td height=30 class='borderR'><?php echo $presData['Fpago']; ?></td>
        </tr>

        <tr>
            <td height=30 width=60 class='borderL'>&nbsp;&nbsp;&nbsp;&nbsp; Tiempo de entrega:</td>
            <td height=30 class='borderR'><?php echo $presData['TiempoEnt']; ?></td>
        </tr>

        <tr>
            <td height=30 width=60 class='borderL BorderB'>&nbsp;&nbsp;&nbsp;&nbsp; Vigencia:</td>
            <td height=30 class='borderB borderR'><?php echo $presData['Vigencia']; ?> días</td>
        </tr>

    </table>
    <page_footer>
        <table align='center' class='text-center'>
            <tr>
                <td>Gracias por confiar en nuestro servicio, estamos atentos a su favorable respuesta.</td>
            </tr>
            <tr>
                <td>Atentamente</td>
            </tr>
            <tr>
                <td class='borderb'><br><br><br></td>
            </tr>
            <tr>
                <td><input type="text" value="<?php echo $Nombre; ?>" width=300></td>
            </tr>
            <tr>
                <td>Ventas</td>
            </tr>
        </table>

        <br>

        <table align='center'>
            <tr>
                <td width=550>
                    Hidalgo No. 10, Col. Luis Echeverria, Santa Rosa 30 centro, CP.: <br>
                    62772, Tlatizapan Morelos
                </td>
                <td class='text-right'>
                    <a href='www.asbombeo.com'>www.asbombeo.com</a> <br>
                    <a href='mailto:ventas@asbombeo.com'>ventas@asbombeo.com</a> <br>
                    <a href="tel:734-108-9680">Tel:734-108-9680</a>
                </td>
            </tr>
        </table>
    </page_footer>
</page>

<page backimg="" backtop="9mm" backbottom="38mm" backleft="0mm" backright="0mm">
    <page_header>
        <table align="right">
            <tr>
                <td align="right">Página [[page_cu]] / [[page_nb]]</td>
            </tr>
        </table>

        <table border='0' align="center" cellspacing=0 cellpadding=0 class="T10">
            <tr class="text-center">
                <td class="borderB W60">Código</td>
                <td class="borderB " style='width: 270px;'>Concepto</td>
                <td class="borderB W60">Unidad</td>
                <td class="borderB W60">Cantidad</td>
                <td class="borderB W60">P.Unitario</td>
                <td class="borderB W60">Importe</td>
                <td class="borderB W60">%</td>
            </tr>
        </table>
    </page_header>

    <table border='0' align="center" cellspacing=0 cellpadding=0 class="T10">
        <?php
        $titulos = '';
        $subtitulos = '';
        $tr = '';
        $Tot = 0; // Total por titulo
        $Porc = 0; // Total % por titulo
        $TotSubt = 0; // Total por subtitulo
        $PorcSub = 0; // Total % por subtitulo

        // Listamos los titulos
        $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Titulos WHERE Num_Cot='$Num_Cot' ORDER BY Clave ASC");

        while ($rst = $sql->fetch_object()) {
            // Subtitulos
            $sql2 = ejecutarConsulta("SELECT * FROM Presupuesto_Subtitulos WHERE Num_Cot='$Num_Cot' AND Clave='$rst->Clave' ORDER BY Cons ASC");

            while ($rst2 = $sql2->fetch_object()) {
                $sql3 = ejecutarConsulta("SELECT PM.* FROM Presupuesto_Matrices_Cot PM LEFT JOIN Presupuesto_Subtitulos PS ON (PS.Num_Cot=PM.Num_Cot AND PM.Clv=PS.Clv)
                                    LEFT JOIN Presupuesto_Titulos PT ON (PM.Num_Cot=PT.Num_Cot AND PS.Clave=PT.Clave) WHERE PM.Num_Cot='$Num_Cot' AND PS.Clv='$rst2->Clv' ORDER BY Orden ASC");

                // Matrices
                while ($rst3 = $sql3->fetch_object()) {
                    $HE = ejecutarConsultaSimpleFila("SELECT SUM(Cant * PM.PU * $rst3->HE/100) AS HE FROM Presupuesto_Mat_Cot PM LEFT JOIN
                            Cat_Materiales PInv ON (PM.Cve=PInv.Cve_Mat) WHERE Num_Cot = '$Num_Cot' AND Cod='$rst3->Cod' AND Clave='$rst3->Clv' AND Tipo='MANO DE OBRA'")['HE'];
                    $importe = ($rst3->PU * $rst3->Cant) + ($HE * $rst3->Cant);
                    $porcentaje = $importe / $presData['Imp_CD'] * 100;

                    $totalPrtida = $total * $porcentaje / 100;
                    $Precio = $totalPrtida / $rst3->Cant; // Precio unitario

                    $rst3->Cod = str_replace("-RIOS", "", $rst3->Cod);
                    $rst3->Descripcion = str_replace("\r\n", "<br>", $rst3->Descripcion);
                    $rst3->Descripcion = str_replace("&QUOT;", '"', $rst3->Descripcion);

                    $tr .= "<tr class='text-center bg-lightblue'>
                            <td style='padding-top: 5px;' class='W60 '>$rst3->Cod</td>
                            <td style='padding-top: 5px; width: 200px; font-size: smaller; text-align: justify'>$rst3->Descripcion</td>
                            <td style='padding-top: 5px;' class='W60 '>$rst3->UM</td>
                            <td style='padding-top: 5px;' class='W60 '>$rst3->Cant</td>
                            <td style='padding-top: 5px;' class='W60 text-right'>$" . number_format($Precio, 2) . "</td>
                            <td style='padding-top: 5px;' class='W60 text-right'>$" . number_format($totalPrtida, 2) . "</td>
                            <td style='padding-top: 5px;' class='W60 text-right'>" . number_format($porcentaje, 2) . "%</td>
                        </tr>";

                    //Totales por subtitulo
                    $TotSubt += $totalPrtida;
                    $PorcSub += $porcentaje;

                    //Totales por titulo
                    $Tot += $totalPrtida;
                    $Porc += $porcentaje;

                    $sql4 = ejecutarConsulta("SELECT M.*, Desc_UM UM, Desc_Mat Descripcion, Tipo FROM Presupuesto_Mat_Cot M LEFT JOIN Cat_Materiales I
                                ON (M.Cve = I.Cve_Mat) LEFT JOIN Cat_Unidad_Medida U ON (U.Id_UM=I.Id_UM2) WHERE Num_Cot='$Num_Cot' AND Cod = '$rst3->Cod' AND Clave='$rst3->Clv'");

                    // Materiales
                    while ($rst4 = $sql4->fetch_object()) {

                        $rst4->Descripcion = str_replace("&QUOT;", '"', $rst4->Descripcion);

                        $importe = ($rst4->PU * $rst4->Cant) * $rst3->Cant;
                        $porcentaje = ($importe / $presData['Imp_CD']) * 100;

                        $totalCons = $total * $porcentaje / 100;
                        $Precio = $totalCons / $rst4->Cant; // Precio unitario
                    }
                }

                $subtot = "";

                $val = ejecutarConsultaSimplefila("SELECT COUNT(*) Count FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot' AND Clv='$rst2->Clv'")['Count'];

                if ($val > 0) {
                    $subtot = "<tr class='text-center'>
                            <td style='border-top: 0.5px; padding-bottom: 5px;' colspan='5' align='right' class='W530'>$rst2->Clv TOTAL $rst2->Subtitulo&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td style='border-top: 0.5px; padding-bottom: 5px;' class='text-right'>$" . number_format($TotSubt, 2) . "</td>
                            <td style='border-top: 0.5px; padding-bottom: 5px;' class='text-right'>" . number_format($PorcSub, 2) . "%</td>
                        </tr>";
                }

                $subtitulos .= "<tr class='bg-blue'>
                            <td class='text-center W60'>$rst2->Clv</td>
                            <td colspan='6' class='W530'>$rst2->Subtitulo</td>
                        </tr>$tr
                        $subtot";

                $tr = '';
                $TotSubt = 0;
                $PorcSub = 0;
            }

            $titulos .= "<tr class='bg-green'>
                        <td class='text-center W60'>$rst->Clave</td>
                        <td colspan='6' class='W530'>$rst->Titulo</td>
                    </tr>
                    $subtitulos
                    <tr class='text-center'>
                        <th colspan='5' align='right' style='width: 500px;'>$rst->Clave TOTAL $rst->Titulo&nbsp;&nbsp;&nbsp;&nbsp;</th>
                        <th style='border-top: 0.5px;' class='text-right'>$" . number_format($Tot, 2) . "</th>
                        <th style='border-top: 0.5px;' class='text-right'>" . number_format($Porc, 2) . "%</th>
                    </tr>";

            echo $titulos;

            // Reiniciamos variables
            $titulos = '';
            $subtitulos = '';

            $Tot = 0;
            $Porc = 0;
        }
        ?>
    </table>
    <br>
    <table border='0' align="center" cellspacing=0 cellpadding=0 class="T10 negritas">
        <tr>
            <td class="W600 H12">TOTAL DEL PRESUPUESTO MOSTRADO SIN IVA</td>
            <td class="text-right H12">$<?php echo number_format($total, 2); ?></td>
        </tr>
        <!--    Validamos el descuento  -->
        <?php if ($Descuento > 0) { ?>
            <tr>
                <td class="W600 H12">Descuento <?php echo number_format($presData['Por_Desc'], 2); ?>% </td>
                <td class="text-right H12">$<?php echo number_format($Descuento, 2); ?></td>
            </tr>

            <tr>
                <td class="W600 H12">Subtotal </td>
                <td class="text-right H12">$<?php echo number_format(($total - $Descuento), 2); ?></td>
            </tr>
        <?php } ?>
        <tr>
            <td class="H12">IVA 16.00%</td>
            <td class="text-right H12">$<?php echo number_format(($total - $Descuento) * 0.16, 2); ?></td>
        </tr>
        <tr>
            <td class="H12">TOTAL DEL PRESUPUESTO MOSTRADO</td>
            <td class="text-right H12">$<?php echo number_format(($total - $Descuento) * 1.16, 2); ?></td>
        </tr>
        <tr>
            <td colspan="2" class="H12">
                (* <?php echo convertir((($total - $Descuento) * 1.16), "MXN", 0); ?> *)
            </td>
        </tr>
    </table>
</page>

<?php
$content = ob_get_clean();
require '../../../Library/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf('P', 'LETTER', 'es', 'true', 'UTF-8');
$html2pdf->writeHTML($content);
$html2pdf->output('Cotizacion_No_' . $Num_Cot . '.pdf');
?>