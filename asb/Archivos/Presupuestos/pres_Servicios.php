<?php session_start();
if (!isset($_SESSION['Id_Empleado'])) {
    header('location: ../../../index.php');
}

// Conexión a la BD
include '../../global/conexion.php';

// Variables de sessión

// Variables para Presupuestos
$Num_OT = isset($_POST['Num_OT']) ? $_POST['Num_OT'] : "";
$Num_Cot = isset($_POST['Num_Cot']) ? $_POST['Num_Cot'] : "";
$Cons = isset($_POST['Cons']) ? $_POST['Cons'] : "";
$Imp_CI = isset($_POST['Imp_CI']) ? floatval($_POST['Imp_CI']) : 0;
$Imp_Fin = isset($_POST['Imp_Fin']) ? floatval($_POST['Imp_Fin']) : 0;
$Imp_Util = isset($_POST['Imp_Util']) ? floatval($_POST['Imp_Util']) : 0;
$Imp_Otro = isset($_POST['Imp_Otro']) ? floatval($_POST['Imp_Otro']) : 0;
$Fpago = isset($_POST['Fpago']) ? $_POST['Fpago'] : "";
$TiempoEnt = isset($_POST['TiempoEnt']) ? $_POST['TiempoEnt'] : "";
$Vigencia = isset($_POST['Vigencia']) ? $_POST['Vigencia'] : 1;
$Calle = isset($_POST['Calle']) ? $_POST['Calle'] : "N";
$Colonia = isset($_POST['Colonia']) ? $_POST['Colonia'] : "N";
$Poblacion = isset($_POST['Poblacion']) ? $_POST['Poblacion'] : "N";
$Tel = isset($_POST['Tel']) ? $_POST['Tel'] : "N";
$Correo = isset($_POST['Correo']) ? $_POST['Correo'] : "N";
$Nota = isset($_POST['Nota']) ? $_POST['Nota'] : "N";
$Orden = isset($_POST['Orden']) ? $_POST['Orden'] : "1";
$Concepto = isset($_POST['Concep']) ? $_POST['Concep'] : "";
$Ubicacion = isset($_POST['Ubicacion']) ? $_POST['Ubicacion'] : "";
$Por_Desc = isset($_POST['Por_Desc']) ? floatval($_POST['Por_Desc']) : "";
$Id_Usr = $_SESSION['Id_Empleado'];
$Fec_Alta = date('Y-m-d');
$Obs = isset($_POST['Obs']) ? $_POST['Obs'] : "";

/*
            *              A   ->  En jecución
            * Status =>    U   ->  Autorizado
            *              
            */


// Variables para Titulos
$Clave = isset($_POST['Clave']) ? $_POST['Clave'] :  '';
$Titulo = isset($_POST['Titulo']) ? $_POST['Titulo'] : "";

// Variables para subtítulos
$Clv = isset($_POST['Clv']) ? $_POST['Clv'] : '';
$Subtitulo = isset($_POST['Subtitulo']) ? $_POST['Subtitulo'] : "";

// Variables para matrices
$Cod = isset($_POST['Cod']) ? $_POST['Cod'] : "";
$Cve = isset($_POST['Cve']) ? $_POST['Cve'] : "";
$Cant = isset($_POST['Cant']) ? $_POST['Cant'] : 0;
$PU = isset($_POST['PU']) ? $_POST['PU'] : 0;
$UM = isset($_POST['UM']) ? $_POST['UM'] : "";
$HE = isset($_POST['HE']) ? $_POST['HE'] : 3;
$Tipo = isset($_POST['Tipo']) ? $_POST['Tipo'] : "";
$Descripcion = isset($_POST['Descripcion']) ? $_POST['Descripcion'] : "";
$Descripcion = str_replace("'", "''", $Descripcion);

// Variables para materiales de ingeniería
$Material = isset($_POST['Material']) ? $_POST['Material'] : "";

// Precios sugeridos
$Id_Sug = isset($_POST['Id_Sug']) ? $_POST['Id_Sug'] : "";

switch ($_GET['op']) {
        // Listado de OTs autorizadas en presupuestos-servicios
    case 'otAut':
        $sql = ejecutarConsulta("SELECT DISTINCT (S.Num_OT), O.Nom_Obra FROM Presupuesto S LEFT JOIN V_Ordenes O ON (S.Num_OT = O.Num_OT)
                    WHERE S.Status != 'A' AND O.Status != 'B' ORDER BY Num_OT DESC");
        $op = '';

        while ($rst = $sql->fetch_object()) {
            $op .= "<option value='$rst->Num_OT' class='text-dark' data-subtext='$rst->Nom_Obra'>$rst->Num_OT</option>";
        }

        echo "<option disabled value='' selected>Seleccionar...</option>$op";
        break;


        // Caso para guardar y /o actualizar datos del presupuesto
    case 'guardar':
        // Valida mos si la cotizacion existe
        $val = ejecutarConsultaSimpleFila("SELECT COUNT(*) COUNT FROM Presupuesto WHERE Num_Cot='$Num_Cot'")['COUNT'];

        if ($val == 0) {
            $insert = ejecutarConsulta("INSERT INTO Presupuesto (Num_OT, Num_Cot, Cons, Fec_Alta, Fec_Aut, Imp_CD, Imp_CI, Imp_Fin, Imp_Util, Imp_Otro, Fpago
                    , TiempoEnt, Vigencia, Calle, Colonia, Poblacion, Tel, Correo, Nota, Status, Concepto, Ubicacion, U_Alta, U_Aut, Por_Desc, Obs)
                     VALUES ($Num_OT,'$Num_Cot', $Cons, '$Fec_Alta', NULL,'0','$Imp_CI',
                        '$Imp_Fin', '$Imp_Util','$Imp_Otro','$Fpago','$TiempoEnt','$Vigencia','$Calle','$Colonia','$Poblacion','$Tel','$Correo',
                        '$Nota', 'A', '$Concepto', '$Ubicacion', $Id_Usr, NULL,'$Por_Desc', '$Obs')");

            echo $insert ? "El presupuesto se agregó correctamente" : "Ocurrio un error al guardar los datos :(";
        } else {
            $update = ejecutarConsulta("UPDATE Presupuesto SET Imp_CI='$Imp_CI', Imp_Fin='$Imp_Fin', Imp_Util='$Imp_Util', Imp_Otro='$Imp_Otro', Fpago='$Fpago'
                        , TiempoEnt='$TiempoEnt', Vigencia='$Vigencia', Calle='$Calle', Colonia='$Colonia' , Poblacion='$Poblacion', Tel='$Tel', Correo='$Correo'
                        , Nota='$Nota', Concepto='$Concepto', Ubicacion='$Ubicacion', Por_Desc='$Por_Desc', Obs='$Obs' WHERE Num_Cot='$Num_Cot'");
            echo $update ? "El presupuesto se actualizó correctamente" : "Ocurrio un error al actualizar los datos :(";
        }
        break;


        // Caso para retornar el numero de cotización por constante
    case 'Num_Cot':
        echo json_encode(ejecutarConsultaSimpleFila("SELECT Num_Cot FROM Presupuesto WHERE Num_OT=$Num_OT AND Cons=$Cons")['Num_Cot']);
        break;


        // Caso para retornar sugerencia de clave para el título
    case 'claveTitulo':
        //$Clave = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Presupuesto_Titulos WHERE Num_Cot='$Num_Cot'")['Count'];
        //$alphabet = range('A', 'Z'); // Array de claves sugeridas
        $letra_siguiente = "";
        $Titulo = "";
        $let = ejecutarConsultaSimpleFila("SELECT Clave FROM Presupuesto_Titulos WHERE Num_Cot='$Num_Cot' ORDER BY Clave DESC limit 1")[0];
        $letra_siguiente = $let == "" ? "A" : chr(ord($let) + 1);
        switch ($letra_siguiente) {
            case 'A':
                $Titulo = "PROYECTO";
                break;
            case 'B':
                $Titulo = "OBRA CIVIL";
                break;
            case 'C':
                $Titulo = "EQUIPAMIENTO MECANICO";
                break;
            case 'D':
                $Titulo = "EQUIPAMIENTO ELECTRICO";
                break;
            case 'E':
                $Titulo = "PUESTA EN MARCHA";
                break;
        }
        echo json_encode(array(
            "Clave" => $letra_siguiente,
            "Titulo" => $Titulo
        ));
        break;


        // Caso para retornar sugerencias para titulos
    case 'sugerencias':
        $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Titulos WHERE Num_Cot='$Num_Cot'");
        $sug = "<div class='d-flez justify-content-center row'>";
        $onClick = " onClick='sug(this)' ";

        $t1 = true;
        $t2 = true;
        $t3 = true;
        $t4 = true;
        $t5 = true;

        while ($rst = $sql->fetch_object()) {
            $rst->Titulo == 'PROYECTO' ? $t1 = false : "";
            $rst->Titulo == 'OBRA CIVIL' ? $t2 = false : "";
            $rst->Titulo == 'EQUIPAMIENTO MECANICO' ? $t3 = false : "";
            $rst->Titulo == 'EQUIPAMIENTO ELECTRICO' ? $t4 = false : "";
            $rst->Titulo == 'PUESTA EN MARCHA' ? $t5 = false : "";
        }

        $t1 ? $sug .= "<div class='alert alert-info mr-4 sug' $onClick>PROYECTO</div>" : "";
        $t2 ? $sug .= "<div class='alert alert-info mr-4 sug' $onClick>OBRA CIVIL</div>" : "";
        $t3 ? $sug .= "<div class='alert alert-info mr-4 sug' $onClick>EQUIPAMIENTO MECANICO</div>" : "";
        $t4 ? $sug .= "<div class='alert alert-info mr-4 sug' $onClick>EQUIPAMIENTO ELECTRICO</div>" : "";
        $t5 ? $sug .= "<div class='alert alert-info mr-4 sug' $onClick>PUESTA EN MARCHA</div>" : "";

        $sug .= "</div>";

        echo $sug;
        break;


        // Caso para guardar y/o editar títulos
    case 'guardarTitulo':
        $val = ejecutarConsultaSimpleFila("SELECT COUNT(*) COUNT FROM Presupuesto_Titulos WHERE Num_Cot='$Num_Cot' AND Clave='$Clave'")['COUNT'];

        if ($val == 0) {
            $insert = ejecutarConsulta("INSERT INTO Presupuesto_Titulos VALUES ('$Num_Cot', '$Clave', '$Titulo')");
            echo $insert ? "El título se guardó correctamente" : "Ocurrio un error al guardar el titulo :(";
        } else {
            $update = ejecutarConsulta("UPDATE Presupuesto_Titulos SET Titulo='$Titulo' WHERE Num_Cot='$Num_Cot' AND Clave='$Clave'");
            echo $update ? "El título se actualizó correctamente" : "Ocurrio un error al actualizar el titulo :(";
        }
        break;


        // Caso para retornar la clve del subtutulo
    case 'claveSub':
        //$Cons = ejecutarConsultaSimpleFila("SELECT Cons FROM Presupuesto_Subtitulos WHERE Num_Cot='$Num_Cot' AND Clave='$Clave' ORDER BY Cons DESC LIMIT 1")['Cons'] + 1;
        $Cons = ejecutarConsultaSimpleFila("SELECT COUNT(*) COUNT FROM Presupuesto_Subtitulos WHERE Num_Cot='$Num_Cot' AND Clave='$Clave'")['COUNT'] + 1;
        echo $Clave . $Cons;
        break;


        // Caso para guardar y/o editar subtítulos
    case 'guardarSub':
        $val = ejecutarConsultaSimpleFila("SELECT COUNT(*) COUNT FROM Presupuesto_Subtitulos WHERE Num_Cot='$Num_Cot' AND Clave='$Clave' AND Clv='$Clv'")['COUNT'];

        if ($val == 0) {
            $Cons = ejecutarConsultaSimpleFila("SELECT COUNT(*) COUNT FROM Presupuesto_Subtitulos WHERE Num_Cot='$Num_Cot' AND Clave='$Clave'")['COUNT'] + 1;
            $insert = ejecutarConsulta("INSERT INTO Presupuesto_Subtitulos VALUES ('$Num_Cot', '$Clave', '$Clv', '$Subtitulo', $Cons)");
            echo $insert ? "El título se guardó correctamente" : "Ocurrio un error al guardar el titulo :(";
        } else {
            $update = ejecutarConsulta("UPDATE Presupuesto_Subtitulos SET Subtitulo='$Subtitulo' WHERE Num_Cot='$Num_Cot' AND Clave='$Clave' AND Clv='$Clv'");
            echo $update ? "El subtítulo se actualizó correctamente" : "Ocurrio un error al actualizar el subtítulo :(";
        }
        break;


        // Caso para agregar partidas
    case 'agregaPartida':
        // Validamos si la clave existe en el catalogo de materiales
        $existe = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Cat_Materiales WHERE Cve_Mat='$Cve'")['Count'];

        $Costo = 0;
        if ($existe > 0) {
            $Costo = ejecutarConsultaSimpleFila("SELECT Costo FROM Cat_Materiales WHERE Cve_Mat='$Cve'")['Costo'];
        }

        $ClvAnt = $_POST['ClvAnt']; // OBTENEMOS LA CLAVE ANTERIOR

        $cv =  empty($ClvAnt) ? $Clv : $ClvAnt;

        $val = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clv='$cv'")['Count'];

        if ($val == 0) {
            // OBTENEMOS EL SIGUIENTE ORDEN
            $Orden = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot'
                            AND Clv='$Clv' ORDER BY Orden DESC")['Count'] + 1;
            // Insertamos en la relacion de Matrices y presupuestos
            $query = ejecutarConsulta("INSERT INTO Presupuesto_Matrices_Cot (Num_Cot, Cod, Descripcion, UM, Cant, PU, HE, Orden, Clv)
                    VALUES('$Num_Cot', '$Cod', '$Descripcion', '$UM', '$Cant', '0', '$HE', '$Orden', '$Clv')");

            if ($query) {
                $val = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Presupuesto_Matrices WHERE Cod='$Cod'")['Count'];
                // Validamos la existencia de la matriz en en la base de matrices
                if ($val > 0) {
                    // Insertamos los materiales de la matriz existen en la matriz de cotización
                    $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Mat WHERE Cod='$Cod'");
                    $PU = 0;

                    while ($rst = $sql->fetch_object()) {
                        $PU = $rst->Cant * $rst->PU;
                        ejecutarConsulta("INSERT INTO Presupuesto_Mat_Cot VALUES ('$Num_Cot','$Cod', '$rst->Cve', $rst->Cant, $rst->PU, '$Clv')");
                    }

                    // Actualizamos el PU EN Presupuesto_Matrices_Cot
                    $PU = round($PU, 4);
                    ejecutarConsulta("UPDATE Presupuesto_Matrices_Cot SET PU='$PU' WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clv='$Clv'");
                } else {

                    if ($existe > 0) {
                        // Si la matriz no existe la creamos solo en la cotización
                        // Insertamos en la relacion de Matrices y materiales
                        ejecutarConsulta("INSERT INTO Presupuesto_Mat_Cot VALUES ('$Num_Cot', '$Cod', '$Cve', 1, '$Costo', '$Clv')");
                    }
                }
            }

            echo $query ? "La partida se agregó correctamente" : "Ocurrio un error al guardar la partida :(";
        } else {
            if ($ClvAnt != $Clv) { // Si la clave es diferente validamos su existencia
                $val = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clv='$Clv'")['Count'];
                if ($val == 0) {
                    empty($ClvAnt) ? $ClvAnt = $Clv : "";
                    // Actualizamos la Matriz
                    $query = ejecutarConsulta("UPDATE Presupuesto_Matrices_Cot SET Cant='$Cant', UM='$UM', Descripcion='$Descripcion',
                                        Clv='$Clv' WHERE Num_COT='$Num_Cot' AND Cod='$Cod' AND Clv='$ClvAnt'");
                    if ($query) {
                        // Actualizamos la clave en el listado de materiales
                        ejecutarConsulta("UPDATE Presupuesto_Mat_Cot SET Clave='$Clv' WHERE Num_COT='$Num_Cot' AND Cod='$Cod' AND Clave='$ClvAnt'");

                        echo "La partida se actualizó correctamente";
                    } else {
                        echo "Ocurrio un error al actualizar la partida :(";
                    }
                } else {
                    $query = false;
                    echo "Ya existe una matriz con este codigo y subtitulo";
                }
            } else { // SI No la clave no es diferente actualizamos el registro
                empty($ClvAnt) ? $ClvAnt = $Clv : "";
                // Actualizamos la Matriz
                $query = ejecutarConsulta("UPDATE Presupuesto_Matrices_Cot SET Cant='$Cant', UM='$UM', Descripcion='$Descripcion',
                                Clv='$Clv', HE='$HE' WHERE Num_COT='$Num_Cot' AND Cod='$Cod' AND Clv='$ClvAnt'");

                echo $query ? "La partida se actualizó correctamente" : "Ocurrio un error al actualizar la partida :(";
            }
        }

        if ($query) {
            // Actualizamos el importe en la matriz
            $imp = ejecutarConsultaSimpleFila("SELECT SUM(PU*Cant) Imp FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clave='$Clv'")['Imp'];
            is_null($imp) ? $imp = 0 : "";
            $imp = round($imp, 4);
            ejecutarConsulta("UPDATE Presupuesto_Matrices_Cot SET PU='$imp' WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clv='$Clv'");
        }
        break;


        // Listado de matrices existente
    case 'matrizExistente':
        $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot' AND Cod!='$Cod' ORDER BY Clv ASC");
        $op = "";

        while ($rst = $sql->fetch_object()) {
            $op .= "<option value='$rst->Cod'>$rst->Cod - $rst->Descripcion</option>";
        }

        echo "<option selected disabled value=''>Seleccionar matriz...</option>$op";
        break;


        // Caso para cargar materiales de matriz existente
    case 'agregarExistente':
        $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cve'");
        $up = '';
        while ($rst = $sql->fetch_object()) {
            if (sqlsrv_has_rows(ejecutarConsulta("SELECT * FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Cve='$rst->Cve' AND Clave='$Clv'"))) {
                $Cant = ejecutarConsultaSimpleFila("SELECT Cant FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Cve='$rst->Cve' AND Clave='$Clv'")['Cant'];
                $Cant += $rst->Cant;
                ejecutarConsulta("UPDATE Presupuesto_Mat_Cot SET Cant='$Cant' WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Cve='$rst->Cve' AND Clave='$Clv'");
            } else {
                ejecutarConsulta("INSERT INTO Presupuesto_Mat_Cot VALUES('$Num_Cot', '$Cod', '$rst->Cve', '$rst->Cant', '$rst->PU', '$Clv')");
            }
        }

        // Actualizamos el importe en la matriz
        $imp = ejecutarConsultaSimpleFila("SELECT SUM(PU*Cant) Imp FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clave='$Clv'")['Imp'];
        $imp = round($imp, 4);
        ejecutarConsulta("UPDATE Presupuesto_Matrices_Cot SET PU='$imp' WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clv='$Clv'");

        echo "Los materiales se agregaron correctamente correctamente";
        break;


        // Caso para actualizar el orden de la matriz
    case 'updateOrden':
        ejecutarConsulta("UPDATE Presupuesto_Matrices_Cot SET Orden=$Orden WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clv='$Clv'");
        break;


        // Caso para guardar materiales
    case 'guardarMaterial':
        $val = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Cve='$Cve' AND Clave='$Clave'")['Count'];
        if ($val == 0) {
            $query = ejecutarConsulta("INSERT INTO Presupuesto_Mat_Cot VALUES ('$Num_Cot', '$Cod', '$Cve', '$Cant', '$PU', '$Clave')");
            echo $query ? 'El material se agregó correctamente' : 'Ocurrio un error al agregar el registro';
        } else {
            $query = ejecutarConsulta("UPDATE Presupuesto_Mat_Cot SET Cant='$Cant', PU='$PU' WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Cve='$Cve' AND Clave='$Clave'");
            // Actualizamos el precio en todas las matrices
            ejecutarConsulta("UPDATE Presupuesto_Mat_Cot SET PU='$PU' WHERE Num_Cot='$Num_Cot' AND Cve='$Cve'");
            echo $query ? 'El material se actualizó correctamente' : 'Ocurrio un error al actualizar el registro';
        }


        if ($query) {
            // Actualizamos el importe en las matrices con el mismos material
            $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Cve='$Cve'");

            while ($rst = $sql->fetch_object()) {
                $imp = ejecutarConsultaSimpleFila("SELECT SUM(PU * Cant) Imp FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$rst->Cod' AND Clave='$rst->Clave'")["Imp"];
                $imp = round($imp, 4);
                ejecutarConsulta("UPDATE Presupuesto_Matrices_Cot SET PU='$imp' WHERE Num_Cot='$Num_Cot' AND Cod='$rst->Cod' AND Clv='$rst->Clave'");
            }
        }
        break;


        // caso para actualizar el costo directo
    case 'updateCD':
        // Obtenemos los factores
        $Imp_Sub = 0; // TOTAL COSTO DIRECTO

        // CALCULAMOS MANO DE OBRA E INSUMOS(MATERIALES) DE SERVICIOS
        $Imp_Com = 0;   // INSUMOS SERVICIOS
        $Imp_MOS = 0;   // MANO DE OBRA SERVICIOS
        $HE = 0;

        // Consultamos matrices
        $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot'");
        //$Contador = 1;
        while ($rst = $sql->fetch_object()) {

            // Consutamos materiales por matriz
            $sql2 = ejecutarConsulta("SELECT PM.*, Tipo FROM Presupuesto_Mat_Cot PM LEFT JOIN Cat_Materiales CM ON (PM.Cve = CM.Cve_Mat)
                                                WHERE Num_Cot='$Num_Cot' AND Cod='$rst->Cod' AND Clave='$rst->Clv'");

            $val = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$rst->Cod' AND Clave='$rst->Clv'")['Count'];
            $he = $rst->HE / 100; // obtenemos el porcentaje de herramienta y equipo

            if ($val == 0) {
                $herr = ejecutarConsultaSimpleFila("SELECT SUM(Cant * PM.PU * $rst->HE/100) AS HE FROM Presupuesto_Mat_Cot PM LEFT JOIN Cat_Materiales CM
                                ON (PM.Cve = CM.Cve_Mat) WHERE Num_Cot = '$Num_Cot' AND Cod='$rst->Cod' AND Clave='$rst->Clv' AND Tipo!='I'")['HE'];
                $Imp_Com += ($rst->Cant * $rst->PU) + ($rst->Cant * $herr);

                $HE += $rst->Cant * $herr;
            } else {
                // Obtenemos los costos de mano de obra e insumos
                while ($rst2 = $sql2->fetch_object()) {

                    // Obtenemos la cantidad total [Matriz * Material]
                    $Cant = $rst->Cant * $rst2->Cant;

                    if ($rst2->Tipo == 'I') {
                        // INCEMENTAMOS IMPORTE DE ONSUMOS
                        $Imp_Com += $Cant * $rst2->PU;
                        //$Contador++;
                    } else {
                        //$Contador++;
                        // INCREMENTAMOS IMPORTE DE MANO DE OBRA
                        $Imp_MOS += $Cant * $rst2->PU;
                        // Herramienta y equipo se aplica a insumos
                        $HE += ($Cant * $rst2->PU) * $he;
                    }
                }
            }
        }

        // Agregamos herramient y equipo al Importe de Insumoa
        $Imp_Com += $HE;

        //Sub total   [M.O Serv]    [Insumos Serv]
        $Imp_Sub    = $Imp_MOS  +   $Imp_Com;

        // Posteriormente aplicamos los factores
        $Imp_CD = round($Imp_Sub, 4);     // Costo Directo (Sin Factores)

        ejecutarConsulta("UPDATE Presupuesto SET Imp_CD=$Imp_CD WHERE Num_Cot='$Num_Cot'");

        // Actualizamos el importe en el presupuestos
        /*$sql = ejecutarConsulta("SELECT * FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot'");
            $imp = 0;*/
        break;


        // Listado de OT
    case 'Num_OT':
        $sql = ejecutarConsulta("SELECT OT.Id, Nombre_Obra FROM Ordenes_Trabajo OT LEFT JOIN Obras O ON (OT.Id_Obra=O.Id)
                                    WHERE OT.Status!='B' ORDER BY Id DESC");

        while ($rst = $sql->fetch_object()) {
            echo "<option value='$rst->Id' class='text-dark' data-subtext='$rst->Nombre_Obra'>$rst->Id</option>";
        }
        break;

        // Funcion para retornar la obra y el cliente de la OT
    case 'otData':
        echo json_encode(
            ejecutarConsultaSimpleFila(
                "SELECT Proyecto, CO.Nombre_Obra Nom_Obra, CONCAT(CC.Nombre, ' ', CC.Apellido_P, ' ', CC.Apellido_M) Nom_Cte, CO.Calle Calle_Cte, CO.Colonia
                        , CONCAT(CM.Nombre,', ',CE.Nombre) AS Poblacion,
                        CONCAT(CCT.Nombre, ' ', CCT.Apellido_P, ' ', CCT.Apellido_M) Nom_Cont, CCT.Telefono AS Tel, CONCAT(CCT.Correo_C, ' ', CCT.Correo_P) AS Correo
                        FROM Ordenes_Trabajo O LEFT JOIN Obras CO ON (O.Id_Obra = CO.Id) LEFT JOIN Clientes CC ON (CO.Id_Cliente = CC.Id)
                        LEFT JOIN Contactos_Clientes CCT ON (O.Id_Contacto = CCT.Id) LEFT JOIN Estados CE ON (CE.Id_Estado = CO.Id_Estado)
                        LEFT JOIN Municipios CM ON (CO.Id_Municipios=CM.Id_Municipios AND CM.Id_Estado = CO.Id_Estado) WHERE O.Id =$Num_OT;"
            )
        );

        //$data = ejecutarConsulta("SELECT OT.* FROM Ordenes_Trabajo OT LEFT JOIN Obras O ON (OT.Id_Obra=O.Id) ");
        break;


        // datos de cotización
    case 'dataCotizacion':
        $data = null;
        // Valida mos si la cotizacion existe
        $val = ejecutarConsultaSimpleFila("SELECT COUNT(*) COUNT FROM Presupuesto WHERE Num_Cot='$Num_Cot'")['COUNT'];

        if ($val > 0) {
            $data = ejecutarConsultaSimpleFila("SELECT PS.*, U.Usuario User, UW.Usuario Aut FROM Presupuesto PS
                        LEFT JOIN User U ON (PS.U_Alta=U.Id) LEFT JOIN User UW ON (PS.U_Aut=UW.Id) WHERE Num_Cot='$Num_Cot'");

            $total = $data['Imp_CD'];

            $Imp_CI = $total + ($total * $data['Imp_CI'] / 100);
            $total = $Imp_CI;

            $Imp_Fin = $total + ($total * $data['Imp_Fin'] / 100);
            $total = $Imp_Fin;

            $Imp_Util = $total + ($total * $data['Imp_Util'] / 100);
            $total = $Imp_Util;

            $Imp_Otro = $total + ($total * $data['Imp_Otro'] / 100);
            $total = $Imp_Otro;

            $Imp_Desc = 0;
            // Aplicamos el descuento si lo hay
            if ($data['Por_Desc'] > 0) {
                $Imp_Desc = $total * ($data['Por_Desc'] / 100);
                $total -= $Imp_Desc;
            }

            $data['Total'] = $total + $Imp_Desc;
            $data['Imp_Desc'] = "$" . number_format($Imp_Desc, 2);
            $data['TotalCD'] = "$" . number_format($total, 2);
            $data['TotalIVA'] = "$" . number_format($total * 1.16, 2);

            $data['Imp_CD'] = "$" . number_format($data['Imp_CD'], 2);
            $data['Status'] == 'A' ? $data['Status'] = 'En ejecución' : $data['Status'] = 'Autorizado';
        }

        echo json_encode($data);
        break;


        // Listado de claves de subtitulostitulos para matrices
    case 'clavesSub':
        $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Titulos WHERE Num_Cot='$Num_Cot' ORDER BY Clave ASC");
        $op = "";

        while ($rst = $sql->fetch_object()) {
            $op .= "<optgroup label='$rst->Clave'>";

            $sql2 = ejecutarConsulta("SELECT * FROM Presupuesto_Subtitulos WHERE Num_Cot='$Num_Cot' AND Clave='$rst->Clave' ORDER BY Clv ASC");
            while ($rst2 = $sql2->fetch_object()) {
                $op .= "<option value='$rst2->Clv'>$rst2->Clv</option>";
            }
            $op .= "</optgroup>";
        }

        echo "<option value='' disabled selected>Seleccionar...</option>" . $op;
        break;


        // Listado de claves de titulos para matrices de ingeniería
    case 'claves':
        $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Titulos WHERE Num_Cot='$Num_Cot' ORDER BY Clave");
        $op = "";

        while ($rst = $sql->fetch_object()) {
            $op .= "<option value='$rst->Clave'>$rst->Clave</option>";
        }
        echo "<option value='' disabled selected>. . .</option>" . $op;
        break;

        // datos de matirz
    case 'editmatriz':
        $data = ejecutarConsultaSimpleFila("SELECT * FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clv='$Clave'");
        $data['PU'] = "$" . number_format(($data['PU'] * ($data['HE'] / 100)) + $data['PU'], 2);
        echo json_encode($data);
        break;

        // caso para retornar el numero de cotización
    case 'numCot':
        $sql = ejecutarConsultaSimpleFila("SELECT COUNT(*) COUNT FROM Presupuesto WHERE Num_OT = $Num_OT")['COUNT'];

        if ($sql > 0) {
            $noCot = '';

            for ($i = 1; $i <= $sql; $i++) {
                $noCot .= "<option value='$i'>Cotización $i</option>";
            }

            $Num_Cot = ejecutarConsultaSimpleFila("SELECT Num_Cot FROM Presupuesto WHERE Num_OT = $Num_OT AND Cons = 1")['Num_Cot'];
        } else {
            $fecha = date('Y');
            $mes = date('m');
            $fecha = str_replace("20", "", $fecha);

            $noCot = '<option value="1">Cotización 1</option>';
            $Num_Cot = $fecha . $mes . "0" . $Num_OT;
        }

        echo json_encode(array("NoCot" => $noCot, "Num_Cot" => $Num_Cot));
        break;

        // caso para Consultar las cotizaciones autorizadas
    case 'cot_Aut':
        $sql = ejecutarConsulta("SELECT * FROM Presupuesto WHERE Num_OT = $Num_OT AND Status='U'");
        $noCot = "";

        while ($rst = $sql->fetch_object()) {
            $noCot .= "<option value='$rst->Cons'>Cotización $rst->Cons</option>";
        }

        echo $noCot;
        break;

        // caso para retornar las claves de los materiales
    case 'materiales':
        $sql = ejecutarConsulta("SELECT Cve_Mat, Desc_Mat FROM Cat_Materiales WHERE Status = 'A' ORDER BY Cve_Mat");
        $op = '';

        while ($rst = $sql->fetch_object()) {
            $op .= "<option value='$rst->Cve_Mat' data-subtext='$rst->Desc_Mat' class='text-dark'>$rst->Cve_Mat</option>";
        }

        echo "<option selected disabled value=''>Seleccionar...</option>$op";

        /*echo "<option selected disabled value=''>Seleccionar...</option>$op
                    <option value='MO001-5' data-subtext='INGENIERO PROYECTISTA' class='text-dark'>MO001-5</option>
                    <option value='MO112' data-subtext='SUPERVISOR' class='text-dark'>MO112</option>
                    
                    <option value='Código' data-subtext='Descripción completa' class='text-dark'>Código</option>
                    <option value='MO011' data-subtext='PEON' class='text-dark'>MO011</option>
                    <option value='MO021' data-subtext='AYUDANTE GENERAL' class='text-dark'>MO021</option>
                    <option value='MO031' data-subtext='AYUDANTE ESPECIALIZADO' class='text-dark'>MO031</option>
                    <option value='MO041' data-subtext='OFICIAL ALBAÑIL' class='text-dark'>MO041</option>
                    <option value='MO051' data-subtext='OFICIAL FIERRERO' class='text-dark'>MO051</option>
                    <option value='MO052' data-subtext='OFICIAL CARPINTERO DE O. NEGRA' class='text-dark'>MO052</option>
                    <option value='MO053' data-subtext='OFICIAL PINTOR' class='text-dark'>MO053</option>
                    <option value='MO061' data-subtext='OFICIAL HERRERO' class='text-dark'>MO061</option>
                    <option value='MO062' data-subtext='OFICIAL YESERO' class='text-dark'>MO062</option>
                    <option value='MO063' data-subtext='OFICIAL AZULEJERO' class='text-dark'>MO063</option>
                    <option value='MO064' data-subtext='OFICIAL COLOCADOR' class='text-dark'>MO064</option>
                    <option value='MO065' data-subtext='OFICIAL BARNIZADOR' class='text-dark'>MO065</option>
                    <option value='MO066' data-subtext='OFICIAL VIDRIERO' class='text-dark'>MO066</option>
                    <option value='MO067' data-subtext='OPERADOR DE MAQUINARIA MENOR' class='text-dark'>MO067</option>
                    <option value='MO071' data-subtext='OFICIAL CARPINTERO DE O. BLANCA' class='text-dark'>MO071</option>
                    <option value='MO081' data-subtext='OFICIAL ALUMINIERO' class='text-dark'>MO081</option>
                    <option value='MO082' data-subtext='CABO DE OFICIOS' class='text-dark'>MO082</option>
                    <option value='MO083' data-subtext='OFICIAL PLOMERO' class='text-dark'>MO083</option>
                    <option value='MO084' data-subtext='OFICIAL ELECTRICISTA' class='text-dark'>MO084</option>
                    <option value='MO085' data-subtext='OFICIAL DE INSTALACIONES' class='text-dark'>MO085</option>
                    <option value='MO086' data-subtext='OFICIAL TUBERO' class='text-dark'>MO086</option>
                    <option value='MO091' data-subtext='OFICIAL SOLDADOR' class='text-dark'>MO091</option>
                    <option value='MO092' data-subtext='TOPOGRAFO' class='text-dark'>MO092</option>
                    <option value='MO093' data-subtext='OPERADOR DE MAQUINARIA PESADA' class='text-dark'>MO093</option>
                    <option value='MO094' data-subtext='SOBRESTANTE' class='text-dark'>MO094</option>
                    <option value='MO111' data-subtext='TECNICO ESPECIALIZADO' class='text-dark'>MO111</option>
                    <option value='MO-MAQ-01-2' data-subtext='AYUDANTE DE EQUIPO Y MAQUINARIA' class='text-dark'>MO-MAQ-01-2</option>
                    <option value='MO-URB-01-2' data-subtext='PEON (URBANIZACION)' class='text-dark'>MO-URB-01-2</option>
                    <option value='MO-URB-02-2' data-subtext='AYUDANTE GENERAL (URBANIZACION)' class='text-dark'>MO-URB-02-2</option>
                    <option value='MO-URB-03-2' data-subtext='AYUDANTE ESPECIALIZADO (URBANIZACION)' class='text-dark'>MO-URB-03-2</option>
                    <option value='MO-URB-04-2' data-subtext='OFICIAL ALBAÑIL (URBANIZACION)' class='text-dark'>MO-URB-04-2</option>
                    <option value='MO-URB-10-2' data-subtext='MANDO INTERMEDIO (URBANIZACION)' class='text-dark'>MO-URB-10-2</option>
                    <option value='MO-URB-11-2' data-subtext='TOPOGRAFO (URBANIZACION)' class='text-dark'>MO-URB-11-2</option>";*/
        break;

    case 'listmateriales':
        $sql = ejecutarConsulta("SELECT Cve_Mat, Desc_Mat FROM Cat_Materiales WHERE Status = 'A' ORDER BY Cve_Mat");

        while ($rst = $sql->fetch_object()) {
            echo "<option value='$rst->Cve_Mat'>$rst->Cve_Mat";
        }
        break;


        // Caso paar retornar la unidad de medida
    case 'mat':
        $data = ejecutarConsultaSimpleFila("SELECT Desc_UM, Desc_Mat, Id_Mat, Costo FROM Cat_Unidad_Medida UM, Cat_Materiales CM 
                                                WHERE UM.Id_UM=CM.Id_UM2 AND Cve_Mat='$Cve'");
        echo json_encode($data);
        break;

        // caso para listado de titulos
    case 'titulos':
        $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Titulos WHERE Num_Cot = '$Num_Cot'");
        $data = array();

        while ($rst = $sql->fetch_object()) {
            $status = ejecutarConsultaSimpleFila("SELECT Status FROM Presupuesto WHERE Num_Cot = '$Num_Cot'")['Status'];
            // Boton borrar
            $borrar = "";
            // Boton editar
            $edit = $rst->Clave;

            if ($status == 'A') {
                $borrar = "<div class='ml-2'>
                            <button type='button' class='btn btn-outline-secondary btn-sm' title='Borrar título'
                                onclick='borrarTitulo(" . '"' . $Num_Cot . '"' . "," . '"' . $rst->Clave . '"' . ")'> <i class='fa fa-trash-alt'></i></button>
                        </div>";

                $edit = "<div class='d-flex justify-content-center'> <button type='button' class='btn btn-outline-primary btn-sm' title='Editar título'
                            onclick='verTitulo(" . '"' . $rst->Clave . '"' . ")'>$rst->Clave <i class='fa fa-edit'></i></button>
                        </div>";
            }

            // Boton subtitulos
            $btn = "<div class='d-flex justify-content-center'>
                            <div> <button type='button' class='btn btn-outline-primary btn-sm' data-toggle='modal' data-target='#Subtitulos'
                                onclick='subtitulos(" . '"' . $Num_Cot . '"' . "," . '"' . $rst->Clave . '"' . ")'>
                                <i class='fas fa-ellipsis-v'></i> Subtitulos</button>
                            </div> $borrar
                        </div>";

            $data[] = array(
                "0" => $edit,
                "1" => $rst->Titulo,
                "2" => $btn
            );
        }

        $results = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data
        );

        echo json_encode($results);
        break;


        // Caso para ver subtitulos
    case 'verTitulo':
        echo ejecutarConsultaSimpleFila("SELECT Titulo FROM Presupuesto_Titulos WHERE Num_Cot='$Num_Cot' AND Clave='$Clave'")['Titulo'];
        break;


        // caso para listado de subtítulos
    case 'subtitulos':
        $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Subtitulos WHERE Num_Cot = '$Num_Cot' AND Clave='$Clave' ORDER BY Clv ASC");
        $status = ejecutarConsultaSimpleFila("SELECT Status FROM Presupuesto WHERE Num_Cot = '$Num_Cot'")['Status'];
        $data = array();

        while ($rst = $sql->fetch_object()) {
            // Boton borrar
            $borrar = "";
            // Boton editar
            $edit = $rst->Clv;

            if ($status == 'A') {
                $edit = "<div class='d-flex justify-content-center'> <button type='button' class='btn btn-outline-primary btn-sm' title='Editar subtítulo'
                            onclick='verSubtitulo(" . '"' . $rst->Clv . '"' . ")'>$rst->Clv <i class='fa fa-edit'></i></button>
                        </div>";

                $borrar = "<div class='ml-2'>
                            <button type='button' class='btn btn-outline-secondary btn-sm' title='Borrar subtítulo'
                                onclick='borrarSubtitulo(" . '"' . $Num_Cot . '"' . "," . '"' . $rst->Clave . '"' . ", " . '"' . $rst->Clv . '"' . ")'>
                                <i class='fa fa-trash-alt'></i></button>
                        </div>";
            }

            // Boton Opciones
            $btn = "<div class='d-flex justify-content-center'> $edit $borrar </div>";

            $data[] = array(
                "0" => $btn,
                "1" => $rst->Subtitulo
            );
        }

        $results = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data
        );

        echo json_encode($results);
        break;


        // Caso para ver subtitulos
    case 'verSubtitulo':
        echo ejecutarConsultaSimpleFila("SELECT Subtitulo FROM Presupuesto_Subtitulos WHERE Num_Cot='$Num_Cot' AND Clv='$Clv'")['Subtitulo'];
        break;


        // caso para listar matrices (partidas)
    case 'matrices':
        $sql = ejecutarConsulta("SELECT PM.*, CONCAT(Subtitulo, '  (', Titulo, ')') AS Titulo FROM Presupuesto_Matrices_Cot PM
                LEFT JOIN Presupuesto_Subtitulos PS ON (PM.Num_Cot=PS.Num_Cot AND PM.Clv=PS.Clv)
                LEFT JOIN Presupuesto_Titulos PT ON (PM.Num_Cot=PT.Num_Cot AND PS.Clave=PT.Clave)
                WHERE PM.Num_Cot = '$Num_Cot' ORDER BY PT.Clave DESC, Orden DESC");

        $data = array();

        while ($rst = $sql->fetch_object()) {
            $status = ejecutarConsultaSimpleFila("SELECT Status FROM Presupuesto WHERE Num_Cot='$Num_Cot'")['Status'];

            $input = $rst->Orden;
            $borrar = "";
            $crear = "";
            $HE = 0;

            $count = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Presupuesto_Matrices WHERE Cod='$rst->Cod'")['Count'];

            if ($count == 0) {
                $crear = "<div class='ml-2'> <button type='button' class='btn btn-outline-success btn-sm' title='Guardar en el catalogo de matrices'
                            onclick='crearMat(" . '"' . $Num_Cot . '"' . "," . '"' . $rst->Cod . '"' . "," . '"' . $rst->Clv . '"' . ")'> Crear <i class='fas fa-save'></i></button>
                        </div>";
            }
            if ($status == 'A') {
                $borrar = "<button class='btn btn-outline-secondary btn-sm d-flex justify-content-between'
                            title='Borrar matriz'
                            onclick='deleteMatriz(" . '"' . $Num_Cot . '"' . "," . '"' . $rst->Cod . '"' . "," . '"' . $rst->Clv . '"' . ")'>
                            <div><i class='fas fa-trash-alt'></i></div>
                        </button>";

                // Input Orden
                $input = '<div class="form-group">
                            <input type="text" class="form-control form-control-sm" maxlength="6"
                                onkeyup="updateOrden(this.value, ' . "'" . $rst->Cod . "'" . ', ' . "'" . $rst->Clv . "'" . ')"
                                onkeypress="return NumCheck(event, this)"
                                style="text-align: center;"
                                value="' . $rst->Orden . '" placeholder="Orden">
                        </div>';
            }

            // Boton ver 
            $btn = "<div class='d-flex justify-content-center'>
                            <div> <button type='button' class='btn btn-outline-primary btn-sm' data-toggle='modal' data-target='#modal'
                                onclick='matriz(" . '"' . $Num_Cot . '"' . "," . '"' . $rst->Cod . '"' . "," . '"' . $rst->Clv . '"' . ")'> Ver</button>
                            </div>$crear
                        </div>";

            // Boton Editar
            $edit = "<div class='d-flex justify-content-between'>
                        <button type='button' class='btn btn-outline-primary btn-sm d-flex justify-content-center' title='Editar'
                            onclick='editmatriz(" . '"' . $Num_Cot . '"' . "," . '"' . $rst->Cod . '"' . "," . '"' . $rst->Clv . '"' . ")'>
                            <div><i class='fas fa-edit'></i></div><div><span> $rst->Cod</span></div>
                        </button>$borrar
                    </div>";

            $HE = ejecutarConsultaSimpleFila("SELECT SUM(Cant * PM.PU * $rst->HE/100) AS HE FROM Presupuesto_Mat_Cot PM LEFT JOIN Cat_Materiales PInv
                                ON (PM.Cve = PInv.Cve_Mat) WHERE Num_Cot='$Num_Cot' AND Cod='$rst->Cod' AND Clave='$rst->Clv' AND Tipo!='I'")['HE'];

            $HE *= $rst->Cant;

            $Imp = ($rst->Cant * $rst->PU) + $HE;

            $data[] = array(
                "0" => $edit,
                "1" => $input,
                "2" => "<div title='$rst->Titulo'>$rst->Clv</div>",
                "3" => "<div style='font-size: smaller;'>" . $rst->Descripcion . "</div>",
                "4" => $rst->UM,
                "5" => number_format($rst->Cant, 2),
                "6" => "$" . number_format($rst->PU, 2),
                "7" => "$" . number_format($HE, 2),
                "8" => "$" . number_format($Imp, 2),
                "9" => $btn
            );
        }

        $results = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data
        );

        echo json_encode($results);
        break;


        // caso para listar la mtriz de la partida
    case 'matriz':
        $sql = ejecutarConsulta("SELECT M.*, Desc_UM UM, Desc_Mat Descripcion, Tipo FROM Presupuesto_Mat_Cot M LEFT JOIN Cat_Materiales CM ON (M.Cve = CM.Cve_Mat)
                    LEFT JOIN Cat_Unidad_Medida UM ON (CM.Id_UM2=UM.Id_UM) WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clave='$Clave'");

        $status = ejecutarConsultaSimpleFila("SELECT Status FROM Presupuesto WHERE Num_Cot = '$Num_Cot'")['Status'];
        $data = array();

        while ($rst = $sql->fetch_object()) {
            $borrar = "";

            if ($status == 'A') {
                $borrar = "<button class='btn btn-outline-secondary btn-sm d-flex justify-content-between'
                            title='Borrar'
                            onclick='deleteMat(" . '"' . $Num_Cot . '"' . "," . '"' . $Cod . '"' . ", " . '"' . $rst->Cve . '"' . ", " . '"' . $rst->Clave . '"' . ")'>
                            <div><i class='fas fa-trash-alt'></i></div>
                        </button>";
            }

            $btn = "<div class='d-flex justify-content-between'>
                            <button class='btn btn-outline-info btn-sm' title='Editar'
                                onclick='mostrar(" . '"' . $Num_Cot . '"' . "," . '"' . $Cod . '"' . ", " . '"' . $rst->Cve . '"' . ", " . '"' . $rst->Clave . '"' . ")'>
                                <div class='d-flex justify-content-between'>
                                    <div> <i class='fas fa-edit'></i></div>
                                    <div><span> $rst->Cve</span></div>
                                </div>
                            </button>$borrar
                        </div>";

            $Imp = $rst->Cant * $rst->PU;
            $Tipo = $rst->Tipo == 'I' ? 1 : 2;

            $data[] = array(
                "0" => $btn,
                "1" => $Tipo,
                "2" => "<div style='font-size: smaller;'>" . $rst->Descripcion . "</div>",
                "3" => $rst->UM,
                "4" => number_format($rst->Cant, 2),
                "5" => "$" . number_format($rst->PU, 2),
                "6" => "$" . number_format($Imp, 2),
            );
        }

        $results = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data
        );

        echo json_encode($results);
        break;


        // Retornamos el la información de un material  
    case 'mostrar':
        $data = ejecutarConsultaSimpleFila("SELECT M.*, Desc_UM UM, Desc_Mat Descripcion, Tipo FROM Presupuesto_Mat_Cot M
                LEFT JOIN Cat_Materiales CM ON (M.Cve = CM.Cve_Mat) LEFT JOIN Cat_Unidad_Medida UM ON (CM.Id_UM2=UM.Id_UM)
                    WHERE Num_Cot='$Num_Cot' AND Cod = '$Cod' AND M.Cve = '$Cve' AND Clave='$Clave'");

        echo json_encode($data);
        break;


        // Caso para eliminar materiales de las matrices por cotización
    case 'deleteMat':
        $delete = ejecutarConsulta("DELETE FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Cve='$Cve' AND Clave='$Clave'");

        if ($delete) { // Actualizamos el importe en la matriz
            $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clave='$Clave'");
            $imp = 0;

            while ($rst = $sql->fetch_object()) {
                $imp += $rst->Cant * $rst->PU;
            }
            $imp = round($imp, 4);
            ejecutarConsulta("UPDATE Presupuesto_Matrices_Cot SET PU='$imp' WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clv='$Clave'");
        }

        echo $delete ? "El registro se borró correctamente" : "Ocurrio un error al borrar el registro";
        break;

        // Caso para matrices por cotización
    case 'deleteMatriz':
        $delete = ejecutarConsulta("DELETE FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clv='$Clave'");

        if ($delete) {
            // eliminamos los materiales de la matriz
            ejecutarConsulta("DELETE FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clave='$Clave'");
        }

        echo $delete ? "La matriz se borró correctamente" : "Ocurrio un error al borrar la matriz";
        break;


        // caso para guardar matriz de presupuesto en el catalogo de matrices
    case 'crearMat':
        $mat = ejecutarConsultaSimpleFila("SELECT * FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clv='$Clave'");
        $Descripcion = $mat['Descripcion'];
        $UM = $mat['UM'];
        $PU = $mat['PU'];

        $insert = ejecutarConsulta("INSERT INTO Presupuesto_Matrices VALUES('$Cod', '$Descripcion', '$UM', '$PU')");

        if ($insert) {
            $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clave='$Clave'");

            while ($rst = $sql->fetch_object()) {
                ejecutarConsulta("INSERT INTO Presupuesto_Mat VALUES('$Cod', '$rst->Cve', '$rst->Cant', '$rst->PU')");
            }

            echo "La matriz '$Cod' se guardó correctamente en el catalogo de matrices";
        } else {
            echo "Ocurrio un error al crear la matriz :(";
        }
        break;


        // Caso para retornar el total de la matriz
    case 'totalMat':
        // Obtenemos el total de la matriz
        $Matriz = ejecutarConsultaSimpleFila("SELECT Cant*PU Imp, HE FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clv='$Clave'");
        $Total = $Matriz['Imp'];
        // Obtenemos el importe de herramienta y equipo
        $HE = ejecutarConsultaSimpleFila("SELECT SUM(Cant * PU * " . $Matriz['HE'] . "/100) AS HE FROM Presupuesto_Mat_Cot PM LEFT JOIN
                    Cat_Materiales CM ON (PM.Cve = CM.Cve_Mat) WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clave='$Clave' AND Tipo!='I'")['HE'];
        $Total += $HE;
        echo "$" . number_format($Total, 2);
        break;


        // Caso para autorizar cotizaciones
    case 'autorizar':
        $Fec_Aut = date('Y-m-d');
        $update = ejecutarConsulta("UPDATE Presupuesto SET Status='U', Fec_Aut='$Fec_Aut', U_Aut='$Id_Usr' WHERE Num_Cot='$Num_Cot'");

        if ($update) {
            // Obtenemos los factores
            $presData = ejecutarConsultaSimpleFila("SELECT * FROM Presupuesto WHERE Num_Cot='$Num_Cot'");

            $Imp_Sub = 0; // TOTAL COSTO DIRECTO

            $IU_Mod = $Id_Usr;

            // CALCULAMOS MANO DE OBRA E INSUMOS(MATERIALES) DE SERVICIOS
            $Imp_Com = 0;   // INSUMOS SERVICIOS
            $Imp_MOS = 0;   // MANO DE OBRA SERVICIOS
            $HE = 0;


            // Consultamos matrices
            $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot'");

            while ($rst = $sql->fetch_object()) {
                // Consutamos materiales por matriz
                $sql2 = ejecutarConsulta("SELECT PM.*, Tipo FROM Presupuesto_Mat_Cot PM LEFT JOIN Cat_Materiales PInv ON (PM.Cve = PInv.Cve_Mat)
                                                    WHERE Num_Cot = '$Num_Cot' AND Cod = '$rst->Cod' AND Clave = '$rst->Clv'");

                $he = $rst->HE / 100; // obtenemos el porcentaje de herramienta y equipo

                $val = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Presupuesto_Mat_Cot PM LEFT JOIN Cat_Materiales PInv ON (PM.Cve = PInv.Cve_Mat)
                                                        WHERE Num_Cot='$Num_Cot' AND Cod='$rst->Cod' AND Clave='$rst->Clv'")['Count'];

                if ($val == 0) {
                    $herr = ejecutarConsultaSimpleFila("SELECT SUM(Cant * PM.PU * $rst->HE/100) AS HE FROM Presupuesto_Mat_Cot PM LEFT JOIN
                                Cat_Materiales PInv ON (PM.Cve = PInv.Cve_Mat) WHERE Num_Cot = '$Num_Cot' AND Cod='$rst->Cod' AND Clave='$rst->Clv' AND Tipo='MANO DE OBRA'")['HE'];
                    $Imp_Com += ($rst->Cant * $rst->PU) + ($rst->Cant * $herr);

                    $HE += $rst->Cant * $herr;
                } else {
                    // Obtenemos los costos de mano de obra e insumos
                    while ($rst2 = $sql2->fetch_object()) {
                        // Obtenemos la cantidad total [Matriz * Material]
                        $Cant = $rst->Cant * $rst2->Cant;

                        if ($rst2->Tipo == 'I') {
                            // INCEMENTAMOS IMPORTE DE ONSUMOS
                            $Imp_Com += $Cant * $rst2->PU;
                        } else {
                            // INCREMENTAMOS IMPORTE DE MANO DE OBRA
                            $Imp_MOS += $Cant * $rst2->PU;
                            // Herramienta y equipo se aplica a insumos
                            $HE += ($Cant * $rst2->PU) * $he;
                        }
                    }
                }
            }

            // Agregamos herramient y equipo al Importe de Insumoa
            $Imp_Com += $HE;

            // Redondeamos Insumos y mano de obra a 2 decimales
            $Imp_Com = round($Imp_Com, 2);
            $Imp_MOS = round($Imp_MOS, 2);


            //Sub total   [M.O Serv]    [Insumos Serv]
            $Imp_Sub    = $Imp_MOS  +   $Imp_Com;

            // Posteriormente aplicamos los factores
            $Imp_CD = $Imp_Sub;     // Costo Directo (Sin Factores)


            /* Aplicamos factores
                    *  Imp_CI %
                    *  Imp_Fin %
                    *  Imp_Util %
                    *  Imp_Otro %
                    */

            // Calculamos el costo indirecto
            $Porc_CI = $presData['Imp_CI']; // Porcentaje de Costo Indirecto
            $Imp_CI = $Imp_Sub * ($Porc_CI / 100);   // Total Costo Indirecto
            $Imp_Sub += $Imp_CI;    // Nuevo Subtotal

            // Calculamos el financiamiento
            $Porc_Fin = $presData['Imp_Fin'];   // Porcentaje de Financiamiento
            $Imp_Fin = $Imp_Sub * ($Porc_Fin / 100);    // Total financiamiento
            $Imp_Sub += $Imp_Fin;   // Nuevo Subtotal

            // Calculamos la utiliodad
            $Porc_Util = $presData['Imp_Util']; // Porcentaje de utilidad
            $Imp_Util = $Imp_Sub * ($Porc_Util / 100);  // Total utiliodad

            $Imp_Sub += $Imp_Util;  // Nuevo Subtotal

            // Calculamos otros
            $Porc_Otro = $presData['Imp_Otro']; // Porcentaje Otros
            $Imp_Otro = $Imp_Sub * ($Porc_Otro / 100);

            $Imp_Sub += $Imp_Otro;  // Subtotal (TOTAL SIN IVA)

            // Aplicamos descuento si lo hay
            $Descuento = $presData['Por_Desc'];
            if ($Descuento > 0) {
                $Descuento /= 100;
                $Descuento *= $Imp_Sub;
            }

            $Imp_Sub -= $Descuento;
            $Imp_Neto = $Imp_Sub * 1.16;  // GRANTOTAL (IVA)

            // Redondeamos variables a 2 decimales
            $Imp_CI = round($Imp_CI, 2);
            $Imp_Fin = round($Imp_Fin, 2);
            $Imp_Util = round($Imp_Util, 2);
            $Imp_Otro = round($Imp_Otro, 2);

            $Imp_Sub = round($Imp_Sub, 2);
            $Imp_Neto = round($Imp_Neto, 2);

            $Imp_Util2 = round(($Imp_Sub - $Imp_CI), 2);  // Importe2 de Financiamiento (calcular-diferencia [Imp_Sub - Imp_CI])


            $Total = number_format($Imp_MOS + $Imp_Com, 2);
            $Imp_MOS = number_format($Imp_MOS, 2);
            $Imp_Com = number_format($Imp_Com, 2);

            echo "Se autorizó correctamente\n M.O = $$Imp_MOS \nInsumos = $$Imp_Com\nTotal = $$Total";
        } else {
            echo "Ocurrio un error al autorizar";
        }
        break;


        // Caso para crear nuevas cotizaciones
    case 'nueva':
        $Cons = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Presupuesto WHERE Num_OT=$Num_OT")['Count'];
        $year = date('Y');
        $year = str_replace("20", "", $year);
        $Month = date('m');
        // Obtenemos el nuevo numero de cotización
        $NewCot = $year . $Month . "0" . $Num_OT . "-" . $Cons;
        $Cons++; // Incrementamos la constante

        // Insertamos la nueva cotización
        $insert = ejecutarConsulta("INSERT INTO Presupuesto (Num_OT, Num_Cot, Cons, Fec_Alta, Fec_Aut, Imp_CD, Imp_CI, Imp_Fin, Imp_Util, Imp_Otro, Fpago, TiempoEnt,
                Vigencia, Calle, Colonia, Poblacion, Tel, Correo, Nota, Status, Concepto, Ubicacion, U_Alta, U_Aut, Por_Desc, Obs)
                SELECT $Num_OT, '$NewCot', $Cons, '$Fec_Alta', NULL, Imp_CD, Imp_CI, Imp_Fin, Imp_Util, Imp_Otro, Fpago, TiempoEnt,
                Vigencia, Calle, Colonia, Poblacion, Tel, Correo, Nota, 'A', Concepto, Ubicacion, '$Id_Usr', NULL, Por_Desc, Obs FROM Presupuesto WHERE Num_Cot='$Num_Cot'");

        if ($insert) {
            // Insertmamos titulos
            ejecutarConsulta("INSERT INTO Presupuesto_Titulos (Num_Cot, Clave, Titulo) SELECT '$NewCot', Clave, Titulo FROM Presupuesto_Titulos WHERE Num_Cot='$Num_Cot'");

            // Insertmamos subtitulos
            ejecutarConsulta("INSERT INTO Presupuesto_Subtitulos (Num_Cot, Clave, Clv, Subtitulo, Cons) SELECT '$NewCot', Clave, Clv, Subtitulo, Cons FROM Presupuesto_Subtitulos WHERE Num_Cot='$Num_Cot'");

            // Insertamos las matrices
            ejecutarConsulta("INSERT INTO Presupuesto_Matrices_Cot (Num_Cot, Cod, Descripcion, UM, Cant, PU, HE, Orden, Clv)
                    SELECT '$NewCot', Cod, Descripcion, UM, Cant, PU, HE, Orden, Clv FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot'");

            // Insertamos los materiales
            ejecutarConsulta("INSERT INTO Presupuesto_Mat_Cot (Num_Cot, Cod, Cve, Cant, PU, Clave) SELECT '$NewCot', Cod, Cve, Cant, PU, Clave FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot'");

            echo "Se creó la cotización '$NewCot' para la OT $Num_OT";
        } else {
            echo "Ocurrio un error al crear la nueva cotización :(";
        }
        break;


        // Caso para borrar la cotización para obtener una cotización existente
    case 'deleteCot':
        ejecutarConsulta("DELETE FROM Presupuesto_Titulos WHERE Num_Cot='$Num_Cot'");        // Eliminamos titulos
        ejecutarConsulta("DELETE FROM Presupuesto_Subtitulos WHERE Num_Cot='$Num_Cot'");     // Eliminamos Subtitulos
        ejecutarConsulta("DELETE FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot'");   // Eliminamos matrices
        ejecutarConsulta("DELETE FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot'");        // Eliminamos materiales
        ejecutarConsulta("DELETE FROM Presupuesto WHERE Num_Cot='$Num_Cot'");      // Eliminamos la cotización
        break;


        // Caso para obtener una cotización
    case 'cotizar':
        $year = date('Y');
        // Obtenemos el nuevo numero de cotización
        $NewCot = $_POST['NewCot'];

        // Insertamos la nueva cotización
        $insert = ejecutarConsulta("INSERT INTO Presupuesto
                    SELECT $Num_OT, '$NewCot', '$Cons', '$Fec_Alta', '', Imp_CD, Imp_CI, Imp_Fin, Imp_Util, Imp_Otro, Fpago, TiempoEnt,
                    Vigencia, Calle, Colonia, Poblacion, Tel, Correo, Nota, 'A', Concepto, Ubicacion, '$Id_Usr', '', '', '', '', Por_Desc, '', ''
                    FROM Presupuesto WHERE Num_Cot='$Num_Cot'");

        if ($insert) {
            // Insertmamos titulos
            ejecutarConsulta("INSERT INTO Presupuesto_Titulos SELECT '$NewCot', Clave, Titulo FROM Presupuesto_Titulos WHERE Num_Cot='$Num_Cot'");

            // Insertmamos subtitulos
            ejecutarConsulta("INSERT INTO Presupuesto_Subtitulos SELECT '$NewCot', Clave, Clv, Subtitulo, Cons FROM Presupuesto_Subtitulos WHERE Num_Cot='$Num_Cot'");

            // Insertamos las matrices
            ejecutarConsulta("INSERT INTO Presupuesto_Matrices_Cot SELECT '$NewCot', Cod, Descripcion, UM, Cant, PU, HE, Orden, Clave FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot'");

            // Insertamos los materiales
            ejecutarConsulta("INSERT INTO Presupuesto_Mat_Cot SELECT '$NewCot', Cod, Cve, Cant, PU, Clave FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot'");

            echo "Se guardó la cotización '$NewCot' para la OT $Num_OT";
        } else {
            echo "Ocurrio un error al crear la cotización :(";
        }
        break;


        //Caso para borrar títulos
    case 'borrarSubtitulo':
        $delete = ejecutarConsulta("DELETE FROM Presupuesto_Subtitulos WHERE Num_Cot='$Num_Cot' AND Clave='$Clave' AND Clv='$Clv'");

        if ($delete) {
            ejecutarConsulta("DELETE FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot' AND Clave='$Clv'");   // Eliminamos las matrices
            ejecutarConsulta("DELETE FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Clave='$Clv'");        // Eliminamos los materiales

            echo "El subtítulo se borro correctamente";
        } else {
            echo "Ocurrio un error al borrar el subtítulo";
        }
        break;


        //Caso para borrar títulos
    case 'borrarTitulo':
        $delete = ejecutarConsulta("DELETE FROM Presupuesto_Titulos WHERE Num_Cot='$Num_Cot' AND Clave='$Clave'");
        if ($delete) {
            // Consultamos los subtituloas
            $sql = ejecutarConsulta("SELECT * FROM Presupuesto_Subtitulos WHERE Num_Cot='$Num_Cot' AND Clave='$Clave'");

            while ($rst = $sql->fetch_object()) {
                ejecutarConsulta("DELETE FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot' AND Clv='$rst->Clv'");   // Eliminamos las matrices
                ejecutarConsulta("DELETE FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Clave='$rst->Clv'");        // Eliminamos los materiales
            }

            // Eliminamos subtitulos
            ejecutarConsulta("DELETE FROM Presupuesto_Subtitulos WHERE Num_Cot='$Num_Cot' AND Clave='$Clave'");

            echo "El título se borro correctamente";
        } else {
            echo "Ocurrio un error al borrar el título";
        }
        break;


        // Caso para listado de materiales
    case 'cat_Mat':
        $sql = ejecutarConsulta("SELECT Cve_Mat, Desc_Mat AS Descripcion FROM Cat_Materiales WHERE Status='A' ORDER BY Cve_Mat ASC");
        $data = array();

        while ($rst = $sql->fetch_object()) {
            $data[] = array(
                "0" => "<div class='d-flex justify-content-center'>
                                    <button class='btn btn-outline-primary btn-sm' title='Copiar clave'
                                        onclick='copyToClipboard(" . '"' . $rst->Cve_Mat . '"' . ")'>
                                        $rst->Cve_Mat</button>
                                </div>",
                "1" => "<div class='col-12' style='font-size: smaller'>" . $rst->Descripcion . "</div>"
            );
        }

        $results = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data
        );

        echo json_encode($results);
        break;



        // Caso para listado de catalogo de materiales
    case 'cat_Materiales':
        $sql = ejecutarConsulta("SELECT CM.*, UM1.Abrev AS Desc_UM1, UM2.Abrev AS Desc_UM2 FROM Cat_Materiales CM LEFT JOIN Cat_Unidad_Medida UM1
                    ON (CM.Id_UM1 = UM1.Id_UM) LEFT JOIN Cat_Unidad_Medida UM2 ON (CM.Id_UM2 = UM2.Id_UM) WHERE CM.Status = 'A' ORDER BY Desc_Mat DESC");

        $data = array();

        while ($rst = $sql->fetch_object()) {
            $btn = "<button class='btn btn-outline-primary btn-sm d-flex justify-content-between'
                            title='Ver material' onclick='verCat_Mat(" . '"' . $rst->Cve_Mat . '"' . ")'>$rst->Cve_Mat
                        </button>";

            $data[] = array(
                "0" => $btn,
                "1" => "<div style='font-size: smaller;'>$rst->Desc_Mat</div>",
                "2" => $rst->Desc_UM1,
                "3" => $rst->Desc_UM2,
            );
        }

        $results = array(
            "sEcho" => 1, //Información para el datatables
            "iTotalRecords" => count($data), //enviamos el total registros al datatable
            "iTotalDisplayRecords" => count($data), //enviamos el total registros a visualizar
            "aaData" => $data
        );

        echo json_encode($results);
        break;


        // CASO PARA RETONAR LA INFORMACIÓN DE UN MATERIAL
    case 'verCat_Mat':
        $Cve_Mat = isset($_POST['Cve_Mat']) ? $_POST['Cve_Mat'] : "";

        $data = ejecutarConsultaSimpleFila("SELECT * FROM Cat_Materiales WHERE Cve_Mat = '$Cve_Mat' ORDER BY Desc_Mat ASC");

        echo json_encode($data);
        break;


        // Caso para cargar carmar materiales al inventario
    case 'cargaInventario':
        // Obtenemos la fecha y la hora
        $Fecha = date('Y-m-d');
        $hora = date('H:i', time());

        $msg = "";
        $msg2 = "";

        // Validamos la exitexia de materiales en partidas (Compras)
        $sql = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM OT_OC_Partidas WHERE Num_OT = $Num_OT")['Count'];
        if ($sql == 0) {
            // Eliminamos los materiañes del presupuesto anterior autorizado
            ejecutarConsulta("DELETE FROM Inventarios WHERE Num_OT = $Num_OT");
            ejecutarConsulta("DELETE FROM Inv_Pendientes WHERE Num_OT = $Num_OT");

            // Consultamos loa materiales a insertar
            $materiales =  ejecutarConsulta("SELECT DISTINCT (Cve) FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Cve NOT LIKE 'MO0%' AND Cve NOT LIKE 'MO1%'");

            // Obtenemos la siguiente constante pare el inventario
            $Cons = ejecutarConsultaSimpleFila("SELECT Cons FROM Inventarios WHERE Num_OT=$Num_OT ORDER BY Cons DESC LIMIT 1")['Cons'] + 1;

            while ($rst = $materiales->fetch_object()) {
                // Obtenemos la cantidad requerida
                $Cant_Req = 0;

                $select = ejecutarConsulta("SELECT * FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Cve='$rst->Cve'");

                while ($rst2 = $select->fetch_object()) {
                    $Cant = ejecutarConsultaSimpleFila("SELECT Cant FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$rst2->Cod' AND Clv='$rst2->Clave'")['Cant'];
                    $Cant_Req += $Cant * $rst2->Cant;
                }

                // Obtenemos el precio
                $Pre_Cte = ejecutarConsultaSimpleFila("SELECT PU FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Cve='$rst->Cve' ORDER BY PU DESC LIMIT 1")['PU'];

                // obtener id del material
                $Id_Mat = ejecutarConsultaSimpleFila("SELECT Id_Mat FROM Cat_Materiales WHERE Cve_Mat='$rst->Cve'")['Id_Mat'];

                $insert = ejecutarConsulta("INSERT INTO Inventarios(Num_OT,Cons,Id_Mat,Fec_Req,Cant_Req,Pre_Cte,Hora_Req,Cant_Surt,Cant_Desp,IU_Req,Status,Fec_Ent,Lug_Ent,Cant_Sol,Status_C, Id_Area)
                            VALUES($Num_OT, $Cons, $Id_Mat, '$Fecha',$Cant_Req, $Pre_Cte, '$hora', 0, 0,'$Id_Usr', 1, '','','','P', '')");

                $insert ? $Cons++ : "";
            }

            $msg = "Los insumos se cargaron correctamente al inventario";
        } else {
            $msg = "No se cargaron los materiales :(";
            $msg2 = "Algunos materiales del inventario ya se encuentran en ordenes de compra";
        }

        echo json_encode(array(
            "msg" => $msg,
            "msg2" => $msg2
        ));
        break;


        // Caso paara notificar por correo la carga del inventario
    case 'correos':
        enviar($Num_OT);
        break;


        //  Caso para validar la existencia de materiales en la cotización
    case 'validarMat':
        $sql = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Presupuesto_Mat_Cot WHERE Num_Cot = '$Num_Cot'")['Count'];
        echo $sql;
        break;


        // caso para eliminar titulos, subtitulos, matrices, materiales
    case 'deleteAll':
        $msg = "";
        $msg2 = "";

        $delete1 = ejecutarConsulta("DELETE FROM Presupuesto_Titulos WHERE Num_Cot='$Num_Cot'");
        $delete2 = ejecutarConsulta("DELETE FROM Presupuesto_Subtitulos WHERE Num_Cot='$Num_Cot'");
        $delete3 = ejecutarConsulta("DELETE FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$Num_Cot'");
        $delete4 = ejecutarConsulta("DELETE FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot'");

        if ($delete1 && $delete2 && $delete3 && $delete4) {
            $data = ejecutarConsultaSimpleFila("SELECT * FROM Presupuesto WHERE Num_Cot='$Num_Cot'");
            $Num_OT = $data['Num_OT'];
            $Cons1 = $data['Cons'];
            $Status = $data['Status'];

            $Cons2 = ejecutarConsultaSimpleFila("SELECT Cons FROM Presupuesto WHERE Num_OT='$Num_OT' ORDER BY Cons DESC LIMIT 1")['Cons'];

            if ($Cons1 ==  $Cons2 and $Status == 'A') {
                // Eliminamos la información de la cotización
                ejecutarConsulta("DELETE FROM Presupuesto WHERE Num_Cot='$Num_Cot'");
            }

            $msg = "La eliminación se ejecuto correctamente";
        } else if ($delete1 || $delete2 || $delete3 || $delete4) {
            $msg = "La eliminación se ejecuto paqrcialmente";
            $msg2 = "Algunas tablas no fueron eliminadas";
        } else {
            $msg = "Ocurrio un error al ejecutar la operación";
            $msg2 = "No se eliminó nada :(";
        }

        echo json_encode(array("msg" => $msg, "msg2" => $msg2));
        break;


        // caso para actualizar la cantidad de un material en una matriz
    case 'update_mat_cant':
        ejecutarConsulta("UPDATE Presupuesto_Mat_Cot SET Cant='$Cant' WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clave='$Clave' AND Cve='$Cve'");

        // Actualizamos el total de la matriz
        if ($update) {
            $imp = ejecutarConsultaSimpleFila("SELECT SUM(PU * Cant) PU FROM Presupuesto_Mat_Cot WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clave='$Clave'")['PU'];
            $imp = round($imp, 4);
            ejecutarConsulta("UPDATE Presupuesto_Matrices_Cot SET PU='$imp' WHERE Num_Cot='$Num_Cot' AND Cod='$Cod' AND Clv='$Clave'");
        }

        break;
    case 'copyCotizacion';
        $numOtA = isset($_POST['numOtA']) ? $_POST['numOtA'] : "";
        $numOtC = isset($_POST['numOtC']) ? $_POST['numOtC'] : "";
        $numCons = isset($_POST['numCons']) ? $_POST['numCons'] : "";
        $numCotizacion = isset($_POST['numCotizacion']) ? $_POST['numCotizacion'] : "";

        $Cons = ejecutarConsultaSimpleFila("SELECT COUNT(*) Count FROM Presupuesto WHERE Num_OT=$numOtA")['Count'];
        $year = date('Y');
        $year = str_replace("20", "", $year);
        $Month = date('m');
        // Obtenemos el nuevo numero de cotización
        $NewCot = $Cons == 0 ? $year . $Month . "0" . $numOtA : $year . $Month . "0" . $numOtA . "-" . $Cons;
        $Cons++;
        // Insertamos la nueva cotización
        $insert = ejecutarConsulta("INSERT INTO Presupuesto (Num_OT, Num_Cot, Cons, Fec_Alta, Fec_Aut, Imp_CD, Imp_CI, Imp_Fin, Imp_Util, Imp_Otro, Fpago, TiempoEnt,
        Vigencia, Calle, Colonia, Poblacion, Tel, Correo, Nota, Status, Concepto, Ubicacion, U_Alta, U_Aut, Por_Desc, Obs)
        SELECT $numOtA, '$NewCot', $Cons, '$Fec_Alta', NULL, Imp_CD, Imp_CI, Imp_Fin, Imp_Util, Imp_Otro, Fpago, TiempoEnt,
        Vigencia, Calle, Colonia, Poblacion, Tel, Correo, Nota, 'A', Concepto, Ubicacion, '$Id_Usr', NULL, Por_Desc, Obs FROM Presupuesto WHERE Num_Cot='$numCotizacion'");

        if ($insert) {
            // Insertmamos titulos
            ejecutarConsulta("INSERT INTO Presupuesto_Titulos (Num_Cot, Clave, Titulo) SELECT '$NewCot', Clave, Titulo FROM Presupuesto_Titulos WHERE Num_Cot='$numCotizacion'");

            // Insertmamos subtitulos
            ejecutarConsulta("INSERT INTO Presupuesto_Subtitulos (Num_Cot, Clave, Clv, Subtitulo, Cons) SELECT '$NewCot', Clave, Clv, Subtitulo, Cons FROM Presupuesto_Subtitulos WHERE Num_Cot='$numCotizacion'");

            // Insertamos las matrices
            ejecutarConsulta("INSERT INTO Presupuesto_Matrices_Cot (Num_Cot, Cod, Descripcion, UM, Cant, PU, HE, Orden, Clv)
                            SELECT '$NewCot', Cod, Descripcion, UM, Cant, PU, HE, Orden, Clv FROM Presupuesto_Matrices_Cot WHERE Num_Cot='$numCotizacion'");

            // Insertamos los materiales
            ejecutarConsulta("INSERT INTO Presupuesto_Mat_Cot (Num_Cot, Cod, Cve, Cant, PU, Clave) SELECT '$NewCot', Cod, Cve, Cant, PU, Clave FROM Presupuesto_Mat_Cot WHERE Num_Cot='$numCotizacion'");

            echo "Se creó la cotización '$NewCot' para la OT $numOtA";
        } else {
            echo "Ocurrio un error al crear la nueva cotización :(";
        }
        break;
}
