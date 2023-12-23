<?php session_start();
    if (!isset($_SESSION['Id_Empleado'])) {
        /* Redirigir al usuario a la página index.php. */
        header("location: ../../index.php");
    }


    // Conexión a la BD
    include '../../global/conexion.php';

    // Variables para matrices
    $Cod = isset($_POST['Cod']) ? strtoupper($_POST['Cod']) : "" ;
    $Cve = isset($_POST['Cve']) ? strtoupper($_POST['Cve']) : "" ;
    $Cant = isset($_POST['Cant']) ? $_POST['Cant'] : 0 ;
    $PU = isset($_POST['PU']) ? $_POST['PU'] : 0 ;
    $UM = isset($_POST['UM']) ? $_POST['UM'] : "" ;
    $Tipo = isset($_POST['Tipo']) ? $_POST['Tipo'] : "" ;
    $Descripcion = isset($_POST['Descripcion']) ? strtoupper($_POST['Descripcion']) : "" ;
    $Fecha = date('Y-m-d');

    /*
     *              A   ->  En jecución
     * Status =>    U   ->  AUtorizado
     *              
     */

    switch($_GET['op']){
        // Caso para agregar matrices
        case 'guardarMAtriz':
            $val = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Presupuesto_Matrices WHERE Cod='$Cod'")['Count'];
            // Validamos la existencia de la matriz en en la base de matrices
            if($val == 0){
                $insert = ejecutarConsulta("INSERT INTO Presupuesto_Matrices VALUES ('$Cod', '$Descripcion', '$UM', '$precio')");
                
                if ($insert) {
                    // Insertamos en la relacion de Matrices y materiales
                    ejecutarConsulta("INSERT INTO Presupuesto_Mat VALUES ('$Cod', '$Cve', '1', '$precio')");
                }

                echo $insert ? "La partida se agregó correctamente" : "Ocurrio un error al guardar la partida :(";
            } else {
                echo "La matriz $Cod ya exite";
            }
            break;


        // Caso para guardar materiales
        case 'guardarMaterial':
            $val = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Presupuesto_Mat WHERE Cod='$Cod' AND Cve='$Cve'")['Count'];
            if($val == 0){
                $query = ejecutarConsulta("INSERT INTO Presupuesto_Mat VALUES ('$Cod', '$Cve', '$Cant', '$PU')");
                echo $query? 'el registro se agregó correctamente' : 'Ocurrio un error al agregar el registro';
            } else {
                $query = ejecutarConsulta("UPDATE Presupuesto_Mat SET Cant='$Cant', PU='$PU' WHERE Cod='$Cod' AND Cve='$Cve'");
                echo $query? 'el registro se actualizó correctamente' : 'Ocurrio un error al actualizar el registro';
            }

            
            if ($query){
                $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Mat WHERE Cod='$Cod'");
                $imp = 0;
                // Actualizamos el importe en la matriz
                while($rst = $sql -> fetch_object()){ $imp += $rst -> Cant * $rst -> PU; }
                ejecutarConsulta("UPDATE Presupuesto_Matrices SET PU='$imp' WHERE Cod='$Cod'");
            }
            break;

        // caso para retornar las claves de los materiales
        case 'materiales':
            $sql = ejecutarConsulta("SELECT Cve_Mat, Desc_Mat FROM Cat_Materiales WHERE Status = 'A' ORDER BY Cve_Mat");
            
            echo "<option selected disabled value=''>Seleccionar...</option>";
            while($rst = $sql -> fetch_object()){
                echo "<option value='$rst->Cve_Mat'>$rst->Cve_Mat";
            }
            break;
            

        // Caso paar retornar la unidad de medida
        case 'mat':
            $data = ejecutarConsultaSimpleFila("SELECT Desc_UM, Costo FROM Cat_Unidad_Medida UM, Cat_Materiales CM WHERE UM.Id_UM=CM.Id_UM2 AND Cve_Mat='$Cve'");
            echo json_encode($data);
            break;

        // caso para listar matrices (partidas)
        case 'matrices':
            $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Matrices");
            $data = array();

            while ($rst = $sql -> fetch_object()){
                $desc = str_replace('"', '', $rst -> Descripcion);

                $btn = "<div class='d-flex justify-content-center'>
                    <button type='button' class='btn btn-outline-primary btn-sm' data-toggle='modal' data-target='#modal'
                        onclick='matriz(".'"'.$rst->Cod.'"'.")'> Ver</button>
                    </div>";
                    
                $edit = "<div class='d-flex justify-content-center'>
                    <button type='button' class='btn btn-outline-primary btn-sm'
                        onclick='editmatriz(".'"'.$rst->Cod.'"'.")'><i class='fas fa-edit'></i> $rst->Cod</button>
                    </div>";

                $data [] = array(
                    "0" => $rst -> Cod,
                    "1" => "<div style='font-size: smaller;'>".$rst -> Descripcion."</div>",
                    "2" => $rst -> UM,
                    "3" => "$".number_format($rst -> PU, 2),
                    "4" => $btn
                );
            }

            $results = array(
                "sEcho"=>1, //Información para el datatables
                "iTotalRecords"=>count($data), //enviamos el total registros al datatable
                "iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
                "aaData"=>$data);
                
            echo json_encode($results);
            break;

        // caso para listar la mtriz de la partida
        case 'matriz':
            $sql = ejecutarConsulta("SELECT M.*, Desc_UM, Desc_Mat, Tipo FROM Presupuesto_Mat M LEFT JOIN Cat_Materiales I ON (M.Cve = I.Cve_Mat)
                LEFT JOIN Cat_Unidad_Medida UM ON (UM.Id_UM = I.Id_UM2) WHERE Cod = '$Cod'");

            $data = array();

            while ($rst = $sql -> fetch_object()){
                $btn = "<div class='d-flex justify-content-between'>
                    <button class='btn btn-outline-info btn-sm' title='Editar'
                        onclick='mostrar(".'"'.$Cod.'"'.", ".'"'.$rst->Cve.'"'.")'>
                        <div class='d-flex justify-content-between'>
                            <div> <i class='fas fa-edit'></i></div>
                            <div><span> $rst->Cve</span></div>
                        </div>
                    </button>
                    <button class='btn btn-outline-secondary btn-sm d-flex justify-content-between'
                        title='Borrar'
                        onclick='deleteMat(".'"'.$Cod.'"'.", ".'"'.$rst->Cve.'"'.")'>
                        <div><i class='fas fa-trash-alt'></i></div>
                    </button>
                </div>";

                $Imp = $rst -> Cant * $rst -> PU;

                $data [] = array(
                    "0" => $btn,
                    "1" => $rst -> Tipo,
                    "2" => "<div style='font-size: smaller;'>".$rst -> Desc_Mat."</div>",
                    "3" => $rst -> Desc_UM,
                    "4" => number_format($rst -> Cant, 2),
                    "5" => "$".number_format($rst -> PU, 2),
                    "6" => "$".number_format($Imp, 2),
                );
            }

            $results = array(
                "sEcho"=>1, //Información para el datatables
                "iTotalRecords"=>count($data), //enviamos el total registros al datatable
                "iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
                "aaData"=>$data);
                
            echo json_encode($results);
            break;

        // caso para retonar registros de un codigo
        case 'mostrar':
            echo json_encode(ejecutarConsultaSimpleFila("SELECT M.*, UM, Descripcion, Tipo FROM Presupuesto_Mat M LEFT JOIN Cat_Materiales I ON (M.Cve = I.Cve_Mat)
                LEFT JOIN Cat_Unidad_Medida UM ON (UM.Id_UM = I.Id_UM2) WHERE Cod = '$Cod' AND M.Cve = '$Cve'"));
            break;

        // Caso para eliminar materiales de las matrices por cotización
        case 'deleteMat':
            $delete = ejecutarConsulta("DELETE FROM Presupuesto_Mat WHERE Cod='$Cod' AND Cve='$Cve'");

            if ($delete){ // Actualizamos el importe en la matriz
                $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Mat WHERE Cod='$Cod'");
                $imp = 0;
                
                while($rst = $sql -> fetch_object()){ $imp += $rst -> Cant * $rst -> PU; }
                ejecutarConsulta("UPDATE Presupuesto_Matrices SET PU='$imp' WHERE Cod='$Cod'");
            }
            
            echo $delete ? "El registro se borró correctamente" : "Ocurrio un error al borrar el registro";
            break;


        // Caso para retornar el total de la matriz
        case 'totalMat':
            $total = ejecutarConsultaSimpleFila("SELECT PU FROM Presupuesto_Matrices WHERE Cod='$Cod'")[0];
            echo "$".number_format($total, 2);
            break;

        // Caseo para retornar la informacion de la matriz
        case 'mostrarMat':
            echo json_encode(ejecutarConsultaSimpleFila("SELECT * FROM Presupuesto_Matrices WHERE Cod='$Cod'"));
            break;
    }
    
?>
