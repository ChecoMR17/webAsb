<?php
session_start();
include '../../global/conexion.php';
include_once '../../../Library/PHPExcel/vendor/autoload.php';

$Archivo = $_FILES['archivoExcelM']['tmp_name'];
$Num_Cot = $_POST['Num_Cot'];
$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$result = '
 <table class="table table-hover table-sm" id="">
    <thead>
        <tr>
            <th class="text-center">Codigo</th>
            <th class="text-center">Unidad</th>
            <th class="text-center">Subtitulo</th>
            <th class="text-center">Cantidad</th>
            <th class="text-center">HE</th>
            <th class="text-center">Descripción</th>
            <th class="text-center">Precio</th>
            <th class="text-center">--------</th>
        </tr>
    </thead>
    <tbody>
';
$filas = "";
try {
    $spreadsheet = $reader->load($Archivo);
    $worksheet = $spreadsheet->getActiveSheet();
    $highestRow = $worksheet->getHighestRow();
    if ($highestRow > 1) {
        for ($row = 2; $row <= $highestRow; $row++) {
            $Costo = 0;
            $prefijo = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
            $codigo = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
            $um = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
            $subtitulo = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
            $cantidad = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
            $he = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
            $precio = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
            $descripcion = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
            $descripcionCs = str_replace("'", '&apos;', $descripcion);
            $descripcionCd = str_replace('"', '&quot;', $descripcionCs);

            try {
                // Validamos si la clave existe en el catalogo de materiales
                if ($prefijo != "" || $codigo != "" || $subtitulo != "" || $cantidad != "" || $he != "" || $descripcion != "" || $precio != "") {
                    $Cod = $prefijo . "" . $codigo;
                    $existe = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Cat_Materiales WHERE Cve_Mat='$codigo'")['Count'];
                    $val = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clv='$subtitulo'")['Count'];
                    // OBTENEMOS EL SIGUIENTE ORDEN 
                    $Orden = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot'
                            AND Clv='$subtitulo' ORDER BY Orden DESC")['Count'] + 1;
                    // VALIDAMOS QUE EL SUBTITULO EXISTA
                    $countS = ejecutarConsultaSimpleFila("SELECT COUNT(*) count FROM Presupuesto_Subtitulos WHERE Num_Cot='$Num_Cot' AND Clv='$subtitulo'")[0];
                    if ($countS > 0) {
                        // VALIDAMOS QUE EL CODIGO NO EXISTA
                        $coutCod = ejecutarConsultaSimpleFila("SELECT COUNT(*) count FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clv='$subtitulo'")[0];
                        if ($coutCod == 0) {
                            $query = ejecutarConsulta("INSERT INTO Presupuesto_Matrices_Cot (Num_Cot, Cod, Descripcion, UM, Cant, PU, HE, Orden, Clv)
                        VALUES('$Num_Cot', '$Cod', '$descripcionCd', '$um', '$cantidad', '$precio', '$he', '$Orden', '$subtitulo')");
                            if ($query) {
                                $filas .= "
                                <tr>
                                    <td>$Cod</td>
                                    <td>$um</td>
                                    <td>$subtitulo</td>
                                    <td>$cantidad</td>
                                    <td>$he</td>
                                    <td>$descripcionCd</td>
                                    <td>$$precio</td>
                                    <td><span class='text-success'>GUARDADO</span></td>
                                </tr>
                                ";
                            } else {
                                $filas .= "
                            <tr>
                                <td>$Cod</td>
                                <td>$um</td>
                                <td>$subtitulo</td>
                                <td>$cantidad</td>
                                <td>$he</td>
                                <td>$descripcionCd</td>
                                <td>$$precio</td>
                                <td><span class='text-danger'>ERROR</span></td>
                            </tr>
                            ";
                            }
                        } else {
                            $filas .= "
                                <tr>
                                    <td>$Cod</td>
                                    <td>$um</td>
                                    <td>$subtitulo</td>
                                    <td>$cantidad</td>
                                    <td>$he</td>
                                    <td>$descripcionCd</td>
                                    <td>$$precio</td>
                                    <td><span class='text-secondary'>MATRIZ EXISTENTE </span></td>
                                </tr>
                                ";
                        }
                    } else {
                        $filas .= "
                    <tr>
                        <td>$Cod</td>
                        <td>$um</td>
                        <td>$subtitulo</td>
                        <td>$cantidad</td>
                        <td>$he</td>
                        <td>$descripcionCd</td>
                        <td>$$precio</td>
                        <td><span class='text-warning'>SUBTITULO NO ENCONTRADO</span></td>
                    </tr>
                    ";
                    }
                }
            } catch (PDOException $e) {
                $filas .= "
                            <tr>
                                <td>$Cod</td>
                                <td>$um</td>
                                <td>$subtitulo</td>
                                <td>$cantidad</td>
                                <td>$he</td>
                                <td>$descripcionCd</td>
                                <td>$$precio</td>
                                <td><span class='text-danger'>ERROR DE SQL</span></td>
                            </tr>
                            ";
            }
        }
        echo json_encode(array("msg" => "Guardado", "icon" => "info", "tbl" => $result . $filas . "</tbody></table"));
    } else {
        echo json_encode(array("msg" => "No se encontraron datos", "icon" => "warning", "tbl" => ""));
    }
} catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
    //die('Error al cargar el archivo: ' . $e->getMessage());
    echo json_encode(array("msg" => "Error al cargar el archivo", "icon" => "error", "tbl" => ""));
}
