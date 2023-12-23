<?php
session_start();

if (!isset($_SESSION['Id_Empleado'])) {
    header('location: ../index.php');
}
include "../../global/Header.php";
?>
<!--    Título    -->
<title>Presupuestos</title>
</head>

<body>

    <!--    MENU    -->
    <?php include '../global/menu.php'; ?>
    <style>
        .ir-arriba {
            display: none;
            padding: 10.3px;
            background: #024959;
            font-size: 12px;
            color: #fff;
            cursor: pointer;
            position: fixed;
            bottom: 15px;
            right: 10px;
        }

        .bootstrap-select .dropdown-menu {
            max-width: 200% !important;
        }

        table {
            font-size: small;
        }
    </style>
    <!--Contenido-->
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="col-md-12">
            <div class="box">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="divProgramaciones">
                    <div class="d-flex justify-content-center row">
                        <h2 class="box-title text-success">Presupuestos</h2>
                    </div>

                    <!--    Formulario de presupuestos   -->
                    <form id="formPres" name="formPres">
                        <div class="form-row col-lg-12 col-md-12 col-sm-12 col-xs-12 d-flex justify-content-start row">
                            <!--    Datos de la orden  -->
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-0 pb-0">
                                <h6 class="box-title text-success">Datos del presupuesto</h6>
                            </div>

                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                <label for="Num_OT">OT <span class="text-danger">*</span> </label>
                                <select name="Num_OT" id="Num_OT" class="form-control form-control-sm" title='Num_OT' data-live-search='true' required></select>
                            </div>

                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                <label for="Cons">No. Cotización <span class="text-danger">*</span></label>
                                <select name="Cons" id="Cons" class="form-control form-control-sm"></select>
                            </div>

                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2">
                                <label for="Num_Cot">Cotización </label>
                                <input type="text" class="form-control form-control-sm" placeholder="Num_Cot" id="Num_Cot" name="Num_Cot" readonly>
                            </div>

                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2">
                                <label for="Imp_CD">Costo directo </label>
                                <input type="text" class="form-control form-control-sm" placeholder="$0.00" id="Imp_CD" name="Imp_CD" readonly>
                            </div>

                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2">
                                <label for="Imp_CI">Costo indirecto </label>
                                <input type="number" class="form-control form-control-sm" step="any" min='0' placeholder="0.00%" id="Imp_CI" name="Imp_CI">
                            </div>

                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2">
                                <label for="Imp_Fin">Financiamiento </label>
                                <input type="number" class="form-control form-control-sm" step="any" min='0' placeholder="0.00%" id="Imp_Fin" name="Imp_Fin">
                            </div>

                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2">
                                <label for="Imp_Util">Utilidad </label>
                                <input type="number" class="form-control form-control-sm" step="any" min='0' placeholder="0.00%" id="Imp_Util" name="Imp_Util">
                            </div>

                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2">
                                <label for="Imp_Otro">Otro </label>
                                <input type="number" class="form-control form-control-sm" step="any" min='0' placeholder="0.00%" id="Imp_Otro" name="Imp_Otro">
                            </div>

                            <div class="form-group col-lg-1 col-md-2 col-sm-4 col-xs-12 mb-2" title="Porcentaje de descuento">
                                <label for="Por_Desc">Descuento </label>
                                <input type="number" class="form-control form-control-sm" step="any" min='0' max='100' placeholder="0.00%" id="Por_Desc" name="Por_Desc">
                                <small class="text-muted" id='Imp_Desc' name='Imp_Desc'></small>
                            </div>

                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2">
                                <label for="TotalCD">Total </label>
                                <input type="text" class="form-control form-control-sm" placeholder="$0.00" id="TotalCD" name="TotalCD" readonly>
                            </div>

                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2">
                                <label for="TotalIVA">Total(IVA) </label>
                                <input type="text" class="form-control form-control-sm" placeholder="$0.00" id="TotalIVA" name="TotalIVA" readonly>
                            </div>

                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2">
                                <label for="Status">Estado </label>
                                <input type="text" class="form-control form-control-sm" placeholder="Estado" id="Status" name="Status" readonly>
                                <small class="text-muted" id='U_Aut'></small>
                            </div>

                            <div class="form-group col-lg-1 col-md-2 col-sm-4 col-xs-12 mb-2">
                                <label for="User">Usuario </label>
                                <input type="text" class="form-control form-control-sm" placeholder="User" id="User" name="User" readonly>
                            </div>

                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-2">
                                <label for="Concep">Presupuesto por: <span class="text-danger">*</span></label>
                                <textarea type="text" class="form-control form-control-sm" maxlength="300" placeholder="Concepto..." id="Concep" name="Concep"></textarea>
                            </div>

                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-2 divCte">
                                <label for="Obs">Observaciones: </label>
                                <input type="text" class="form-control form-control-sm" maxlength="100" placeholder="Observaciones..." id="Obs" name="Obs"></textarea>
                            </div>

                            <!--    Datos del cliente   -->
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-0 pb-0" id="divCte">
                                <hr>
                                <h6 class="box-title text-success">Datos del cliente</h6>
                            </div>

                            <div class="form-group col-lg-5 col-md-5 col-sm-12 col-xs-12 divCte">
                                <label for="Obra">Obra </label>
                                <input type="text" class="form-control form-control-sm" placeholder="Obra" id="Obra" name="Obra" readonly>
                            </div>

                            <div class="form-group col-lg-7 col-md-7 col-sm-12 col-xs-12 divCte">
                                <label for="Cliente">Cliente </label>
                                <input type="text" class="form-control form-control-sm" placeholder="Cliente" id="Cliente" name="Cliente" readonly>
                            </div>

                            <div class="form-row col-lg-4 col-md-4 col-sm-12 col-xs-12 divCte">
                                <div class="form-check">
                                    <input class="form-check-input" checked type="checkbox" id="Calle" name="Calle" value="S">
                                    <label class="form-check-label text-primary" for="Calle" id='calle'>Calle: </label>
                                </div>
                            </div>

                            <div class="form-row col-lg-4 col-md-4 col-sm-12 col-xs-12 divCte">
                                <div class="form-check">
                                    <input class="form-check-input" checked type="checkbox" id="Colonia" name="Colonia" value="S">
                                    <label class="form-check-label text-primary" for="Colonia" id='colonia'>Colonia: </label>
                                </div>
                            </div>

                            <div class="form-row col-lg-4 col-md-4 col-sm-12 col-xs-12 divCte">
                                <div class="form-check">
                                    <input class="form-check-input" checked type="checkbox" id="Poblacion" name="Poblacion" value="S">
                                    <label class="form-check-label text-primary" for="Poblacion" id='poblacion'>Población: </label>
                                </div>
                            </div>

                            <div class="form-group col-lg-5 col-md-5 col-sm-12 col-xs-12 divCte">
                                <label for="Contacto">Contacto </small></label>
                                <input type="text" class="form-control form-control-sm" placeholder="Contacto" id="Contacto" name="Contacto" readonly>
                            </div>

                            <div class="form-group col-lg-7 col-md-7 col-sm-12 col-xs-12 mb-2 divCte">
                                <label for="Proyecto">Proyecto </small></label>
                                <input type="text" class="form-control form-control-sm" placeholder="Proyecto" id="Proyecto" name="Proyecto" readonly>
                            </div>

                            <div class="form-row col-lg-4 col-md-4 col-sm-12 col-xs-12 divCte">
                                <div class="form-check">
                                    <input class="form-check-input" checked type="checkbox" id="Tel" name="Tel" value="S">
                                    <label class="form-check-label text-primary" for="Tel" id='tel'>Telefono: </label>
                                </div>
                            </div>

                            <div class="form-row col-lg-4 col-md-4 col-sm-12 col-xs-12 divCte">
                                <div class="form-check">
                                    <input class="form-check-input" checked type="checkbox" id="Correo" name="Correo" value="S">
                                    <label class="form-check-label text-primary" for="Correo" id='correo'>Correo: </label>
                                </div>
                            </div>

                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-2 divCte">
                                <label for="Ubicacion">Ubicación: <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" maxlength="100" placeholder="Ubicación..." id="Ubicacion" name="Ubicacion"></textarea>
                            </div>


                            <!--    Condiciones comerciales   -->
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-0 pb-0" id='divCondcom'>
                                <hr>
                                <h6 class="box-title text-success">Condiciones comerciales</h6>
                            </div>

                            <div class="form-group col-lg-8 col-md-8 col-sm-12 col-xs-12 divCondcom">
                                <label for="Fpago">Forma de pago <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" placeholder="Forma de pago" maxlength="50" id="Fpago" name="Fpago" required>
                            </div>

                            <div class="form-group col-lg-4 col-md-4 col-sm-12 col-xs-12 divCondcom">
                                <label for="Vigencia">Vigencia <span class="text-danger">*</span></label>
                                <input type="number" min="1" class="form-control form-control-sm" placeholder="Dias" id="Vigencia" name="Vigencia" required>
                            </div>

                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 divCondcom">
                                <label for="TiempoEnt">Tiempo de entrega <span class="text-danger">*</span></label>
                                <textarea type="text" class="form-control form-control-sm" placeholder="Tiempo de entrega..." rows="1" maxlength="200" id="TiempoEnt" name="TiempoEnt" required></textarea>
                            </div>

                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 divCondcom">
                                <label for="Nota">Nota</label>
                                <textarea type="text" class="form-control form-control-sm" placeholder="Nota..." id="Nota" name="Nota" maxlength="450"></textarea>
                            </div>
                        </div>

                        <div class="form-row col-lg-12 col-md-12 col-sm-12 col-xs-12 d-flex justify-content-center row">
                            <div class='form-group col-xs-1' title="Guardar">
                                <button type="submit" class="btn btn-outline-primary btn-sm" id='guardarPres'>Guardar <i class="fas fa-save"></i></button>
                            </div>

                            <div class='form-group col-xs-1'>
                                <!--div class='form-group col-xs-1' title="Imprimir">
                                <button type="button" class="btn btn-outline-danger btn-sm" id='pdf_General'>Imprimir <i class="fa-solid fa-file-pdf fa-beat"></i></button>
                            </div-->

                                <div class="btn-group" title="Reportes">
                                    <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        Reportes <i class="fas fa-file-contract"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a id='pdf_General' class="dropdown-item"><i class="fas fa-bars"></i> Con Materiales <span class="text-danger"><i class="fas fa-file-pdf"></i></span></a>
                                        <a id='pdf_Materiales' class="dropdown-item"><i class="fas fa-stream"></i> Sin materiales <span class="text-danger"><i class="fas fa-file-pdf"></i></span></a>
                                        <!--a id='pdf_Pro' class="dropdown-item"><i class="fas fa-stream"></i> Con prorrateo <span class="text-danger"><i class="fas fa-file-pdf"></i></span></a>
                                    <a id='pdf_Insumos' class="dropdown-item"><i class="fas fa-list-ul"></i> Lista de materiales <span class="text-danger"><i class="fas fa-file-pdf"></i></span></a>
                                    <div class="dropdown-divider"></div>
                                    <a id='excel_Servicios' class="dropdown-item" title="Cotización"><i class="fas fa-bars"></i> Exportar a Excel <span class="text-success"><i class="fas fa-file-excel"></i></span></a>
                                    <a id='btnExport' class="dropdown-item" title="Materiales"><i class="fas fa-bars"></i> Requerimiento <span class="text-success"><i class="fas fa-file-excel"></i></span></a>
                                    <a id='pres_analisis' class="dropdown-item" title="Analisis de Precuos Unitarios"><i class="fas fa-bars"></i> Análisis PU <span class="text-success"><i class="fas fa-file-excel"></i></span></a-->
                                    </div>
                                </div>
                            </div>

                            <div class='form-group col-xs-1' title="Limpiar">
                                <button type="button" class="btn btn-outline-dark btn-sm" id="limpiar">Limpiar <i class="fas fa-eraser"></i></button>
                            </div>

                            <div class='form-group col-xs-1' title="Borrar titulos, subtitulos, matrices y materiales">
                                <button type="button" class="btn btn-outline-danger btn-sm" id="btnDelete">Borrar <i class="fas fa-times-circle"></i></button>
                            </div>

                            <div class='form-group col-xs-1' title="Nueva Cotización">
                                <button type="button" class="btn btn-outline-info btn-sm" id="btnNueva">Nueva Cotización <i class="fas fa-plus-circle"></i></button>
                            </div>

                            <div class='form-group col-xs-1'>
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalCCotizacion" id="cCotizacion" hidden>Copiar Cotización <i class="fa-regular fa-copy"></i></i></button>
                            </div>

                            <div class='form-group col-xs-1' title="Autorizar">
                                <button type="button" class="btn btn-outline-success btn-sm" id="btnAutorizar">Autorizar <i class="fas fa-check-circle"></i></button>
                            </div>

                            <!--div class='form-group col-xs-1' title="Cargar materiales a inventario">
                            <button type="button" class="btn btn-outline-warning btn-sm text-dark" id="btnLiberar" hidden>Liberar materiales <i class="fas fa-file-upload"></i></button>
                        </div>

                        <div class='form-group col-xs-1' title="Obtener cotización">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="btnCotizar"
                                data-toggle="modal" data-target="#Cotizar">Obtener cotización <i class="fas fa-file-contract"></i></button>
                        </div-->
                        </div>
                    </form>

                    <!--        Modal para obtener cotización         -->
                    <div class="modal fade" id="Cotizar" tabindex="-1" aria-labelledby="CotizarLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-xl">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-light">
                                    <h6 class="modal-title" id="CotizarLabel">Cotizar <span id='obr'></span></h6>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="formCotizar" name="formCotizar">
                                        <div class="form-row col-lg-12 col-md-12 col-sm-12 col-xs-12 d-flex justify-content-center row">
                                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                                <label for="OT">OT <span class="text-danger">*</span> </label>
                                                <select name="OT" id="OT" class="form-control form-control-sm" data-live-search='true' required></select>
                                            </div>

                                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                                <label for="Cons2">No. Cotización <span class="text-danger">*</span></label>
                                                <select name="Cons2" id="Cons2" class="form-control form-control-sm"></select>
                                            </div>

                                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12">
                                                <label for="NewCot">Cotización <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm" id="NewCot" name="NewCot" placeholder="Cotización..." readonly />
                                            </div>

                                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2">
                                                <label for="Imp_CD2">Costo directo </label>
                                                <input type="text" class="form-control form-control-sm" placeholder="$0.00" id="Imp_CD2" name="Imp_CD2" readonly>
                                            </div>

                                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2">
                                                <label for="Imp_CI2">Costo indirecto </label>
                                                <input type="number" class="form-control form-control-sm" step="any" min='0' placeholder="0.00%" id="Imp_CI2" name="Imp_CI2" readonly>
                                            </div>

                                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2">
                                                <label for="Imp_Fin2">Financiamiento </label>
                                                <input type="number" class="form-control form-control-sm" step="any" min='0' placeholder="0.00%" id="Imp_Fin2" name="Imp_Fin2" readonly>
                                            </div>

                                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2">
                                                <label for="Imp_Util2">Utilidad </label>
                                                <input type="number" class="form-control form-control-sm" step="any" min='0' placeholder="0.00%" id="Imp_Util2" name="Imp_Util2" readonly>
                                            </div>

                                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2">
                                                <label for="Imp_Otro2">Otro </label>
                                                <input type="number" class="form-control form-control-sm" step="any" min='0' placeholder="0.00%" id="Imp_Otro2" name="Imp_Otro2" readonly>
                                            </div>

                                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2" title="Porcentaje de descuento">
                                                <label for="Por_Desc2">Descuento </label>
                                                <input type="number" class="form-control form-control-sm" step="any" min='0' max='100' placeholder="0.00%" id="Por_Desc2" name="Por_Desc2" readonly>
                                                <small class="text-muted" id='Imp_Desc2' name='Imp_Desc2'></small>
                                            </div>

                                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2">
                                                <label for="TotalCD2">Total </label>
                                                <input type="text" class="form-control form-control-sm" placeholder="$0.00" id="TotalCD2" name="TotalCD2" readonly>
                                            </div>

                                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2">
                                                <label for="TotalIVA2">Total(IVA) </label>
                                                <input type="text" class="form-control form-control-sm" placeholder="$0.00" id="TotalIVA2" name="TotalIVA2" readonly>
                                            </div>

                                            <div class="form-group col-lg-2 col-md-2 col-sm-4 col-xs-12 mb-2">
                                                <label for="User2">Usuario </label>
                                                <input type="text" class="form-control form-control-sm" placeholder="$0.00" id="User2" name="User2" readonly>
                                            </div>

                                            <!--    Datos del cliente   -->
                                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-0 pb-0">
                                                <hr>
                                                <h6 class="box-title text-success">Datos del cliente</h6>
                                            </div>

                                            <div class="form-group col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                                <label for="Obra2">Obra </label>
                                                <input type="text" class="form-control form-control-sm" placeholder="Obra" id="Obra2" name="Obra2" readonly>
                                            </div>

                                            <div class="form-group col-lg-7 col-md-7 col-sm-12 col-xs-12">
                                                <label for="Cliente2">Cliente </label>
                                                <input type="text" class="form-control form-control-sm" placeholder="Cliente" id="Cliente2" name="Cliente2" readonly>
                                            </div>

                                            <div class="form-group col-lg-5 col-md-5 col-sm-12 col-xs-12">
                                                <label for="Contacto2">Contacto </small></label>
                                                <input type="text" class="form-control form-control-sm" placeholder="Contacto" id="Contacto2" name="Contacto2" readonly>
                                            </div>

                                            <div class="form-group col-lg-7 col-md-7 col-sm-12 col-xs-12 mb-2">
                                                <label for="Proyecto2">Proyecto </small></label>
                                                <input type="text" class="form-control form-control-sm" placeholder="Proyecto" id="Proyecto2" name="Proyecto2" readonly>
                                            </div>

                                            <!--    Condiciones comerciales   -->
                                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-0 pb-0">
                                                <hr>
                                                <h6 class="box-title text-success">Condiciones comerciales</h6>
                                            </div>

                                            <div class="form-group col-lg-5 col-md-5 col-sm-5 col-xs-12">
                                                <label for="Fpago2">Forma de pago <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm" placeholder="Forma de pago" maxlength="50" id="Fpago2" name="Fpago2" readonly>
                                            </div>

                                            <div class="form-group col-lg-5 col-md-5 col-sm-5 col-xs-12">
                                                <label for="TiempoEnt2">Tiempo de entrega <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm" placeholder="Tiempo de entrega" maxlength="50" id="TiempoEnt2" name="TiempoEnt2" readonly>
                                            </div>

                                            <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12">
                                                <label for="Vigencia2">Vigencia <span class="text-danger">*</span></label>
                                                <input type="number" min="1" class="form-control form-control-sm" placeholder="Dias" id="Vigencia2" name="Vigencia2" readonly>
                                            </div>
                                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <label for="Nota2">Nota</label>
                                                <textarea type="text" rows="1" class="form-control form-control-sm" placeholder="Nota..." id="Nota2" name="Nota2" maxlength="200" readonly></textarea>
                                            </div>

                                            <div class='form-group col-xs-1' title="Guardar">
                                                <div>
                                                    <button type="submit" class="btn btn-outline-primary btn-sm">Guardar <i class="fas fa-save"></i></button>
                                                </div>
                                            </div>
                                            <div class='form-group col-xs-1' title="Guardar">
                                                <div>
                                                    <button type="button" class="btn btn-outline-dark btn-sm" data-dismiss="modal">
                                                        Cerrar <i class="fas fa-times-circle"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--    Formulario de titulos   -->
                    <form id="formTitulos" name="formTitulos">
                        <div class="form-row col-lg-12 col-md-12 col-sm-12 col-xs-12 d-flex justify-content-start">
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-0 pb-0 text-center" id='divTit'>
                                <hr>
                                <h4 class="box-title text-success">Títulos</h4>
                            </div>

                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-0 pb-0 divTit" title="Sugerencias para titulos" id='divSug'></div>

                            <div class="form-group col-lg-1 col-md-2 col-sm-12 col-xs-12 divTit">
                                <label for="Clave">Clave<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="Clave" name="Clave" maxlength="3" required readonly>
                            </div>

                            <div class="form-group col-lg-10 col-md-10 col-sm-12 col-xs-12 divTit">
                                <label for="Titulo">Titulo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="Titulo" name="Titulo" maxlength="200" required>
                            </div>

                            <!--div class="form-row col-lg-12 col-md-12 col-sm-12 col-xs-12 d-flex justify-content-center row"-->
                            <div class='form-group col-xs-1 divTit' title="Guardar">
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-outline-primary btn-sm mt-2" id='GuardarTitulo' disabled>
                                        Guardar <i class="fas fa-save"></i></button>
                                </div>
                            </div>
                            <!--/div-->
                        </div>
                    </form>

                    <div class="form-row col-lg-12 col-md-12 col-sm-12 col-xs-12 divTit" id="divTitulos">
                        <div class="table-responsive col-12">
                            <table class="table table-sm table-hover table-striped compact" id="tblTitulos">
                                <thead class="text-white bg-success">
                                    <th>Clave</th>
                                    <th>Titulo</th>
                                    <th>Opciones</th>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <!--        Modal Subtitulos         -->
                    <div class="modal fade" id="Subtitulos" tabindex="-1" aria-labelledby="SubtitulosLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-xl">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-light">
                                    <h6 class="modal-title" id="SubtitulosLabel">Subtitulos</h6>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="formSubTitulos" name="formSubTitulos">
                                        <div class="form-row col-lg-12 col-md-12 col-sm-12 col-xs-12 d-flex justify-content-center row">
                                            <div class="form-group col-lg-1 col-md-2 col-sm-12 col-xs-12">
                                                <label for="Clave1">Clave<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm" id="Clave1" name="Clave1" maxlength="5" required readonly />
                                            </div>

                                            <div class="form-group col-lg-11 col-md-10 col-sm-12 col-xs-12">
                                                <label for="Subtitulo">Subtitulo <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm" id="Subtitulo" name="Subtitulo" maxlength="200" required placeholder="Subtitulo..." />
                                            </div>

                                            <div class='form-group col-xs-1' title="Guardar">
                                                <div>
                                                    <button type="submit" class="btn btn-outline-primary btn-sm" id='GuardarSubTitulo' disabled>
                                                        Guardar <i class="fas fa-save"></i></button>
                                                </div>
                                            </div>
                                            <div class='form-group col-xs-1' title="Guardar">
                                                <div>
                                                    <button type="button" class="btn btn-outline-dark btn-sm" data-dismiss="modal">
                                                        Cerrar <i class="fas fa-times-circle"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <!--        Tablda de subtitulos            -->
                                    <div class="form-row col-lg-12 col-md-12 col-sm-12 col-xs-12" id="divSubtitulos">
                                        <div class="table-responsive col-12">
                                            <table class="table table-sm table-hover table-striped compact" id="tblSubtitulos">
                                                <thead class="text-white bg-success">
                                                    <th>Clave</th>
                                                    <th>Subtitulo</th>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--    Sección de partidas ( Matrices )   -->
                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12 mb-0 pb-0 text-center" id='Partidas'>
                    <hr>
                    <h4 class="box-title text-success">Partidas
                        <small> <a href="./pres_Matrices.php" target="_Blank" class="text-success"> (Matrices Exsistenetes)</a></small>
                    </h4>
                </div>

                <!--    Formulario de Partidas   -->
                <form id="formPartidas" name="formPartidas">
                    <div class="form-row col-lg-12 col-md-12 col-sm-12 col-xs-12 d-flex justify-content-start row ">
                        <div class="form-group col-lg-2 col-md-3 col-sm-6 col-xs-12" title="Glosario de prefijos">
                            <div><small><b class="text-primary">I: </b> INSTALACIÓN</small></div>
                            <div><small><b class="text-primary">S: </b> SUMINISTRO</small></div>
                            <div><small><b class="text-primary">R: </b> REPARACIÓN</small></div>
                            <div><small><b class="text-primary">X: </b> SUMINISTRO E INSTALACIÓN</small></div>
                        </div>

                        <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12" title="Prefijo de material">
                            <label for="Pref">Prefijo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="Pref" name="Pref" maxlength="1" required>
                        </div>

                        <div class="form-group col-lg-3 col-md-4 col-sm-6 col-xs-12" title="Código de material">
                            <label for="Cve">Código <span class="text-danger">*</span></label>
                            <input list='ListCve' class="form-control form-control-sm" name="Cve" id="Cve" data-live-search="true" required></input>
                            <datalist id='ListCve'></datalist>
                        </div>

                        <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12" title="Unidad de medida">
                            <label for="UM">Unidad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="UM" name='UM' placeholder="Unidad" maxlength="20" required>
                        </div>

                        <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12" title="Título">
                            <label for="Clv">Subtítulo <span class="text-danger">*</span></label>
                            <select class="form-control form-control-sm" name="Clv" id="Clv" required></select>
                        </div>

                        <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12" title="Cantidad">
                            <label for="Cant">Cantidad <span class="text-danger">*</span></label>
                            <input type="number" step="any" min='0' class="form-control form-control-sm" id="Cant" name='Cant' placeholder="0.00" required>
                        </div>

                        <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12" title="Herramienta y equipo">
                            <label for="HE">HE(%) <span class="text-danger">*</span></label>
                            <input type="number" step="any" min='0' max='20' class="form-control form-control-sm" id="HE" name='HE' placeholder="0.00%" required>
                        </div>

                        <div class="form-group col-lg-2 col-md-3 col-sm-6 col-xs-12" title="Precio unitario">
                            <label for="PU">Precio unitario </label>
                            <input type="text" class="form-control form-control-sm" id="PU" name='PU' placeholder="$0.00" readonly>
                        </div>

                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label for="Descripcion">Descripcion <span class="text-danger">*</span></label>
                            <textarea type="text" class="form-control form-control-sm" placeholder="Descripción..." maxlength="1450" id="Descripcion" name="Descripcion" required></textarea>
                        </div>

                        <div class="form-row col-lg-12 col-md-12 col-sm-12 col-xs-12 d-flex justify-content-center row">
                            <div class='form-group col-xs-1' title="Guardar">
                                <button type="submit" class="btn btn-outline-primary btn-sm" id='GuardarPartida' disabled>
                                    Guardar <i class="fas fa-save"></i></button>
                            </div>
                            <div class='form-group col-xs-1' title="Limpiar">
                                <button type="button" class="btn btn-outline-dark btn-sm" onclick="limpiarPartida()">Limpiar <i class="fas fa-eraser"></i></button>
                            </div>

                            <div class='form-group col-xs-1' title="Recargar Tabla">
                                <button type="button" class="btn btn-outline-info btn-sm" id='Recargar'>Recargar <i class="fas fa-sync-alt"></i></button>
                            </div>

                            <div class='form-group col-xs-1'>
                                <button type="button" class="btn btn-success btn-sm" id='cargarExcel' data-toggle="modal" data-target="#modalCargarE" hidden disabled>Cargar Excel <i class="fa-solid fa-file-excel"></i></button>
                            </div>

                            <!--div class='form-group col-xs-1' title="Exportar Materiales">
                            <button type="button" class="btn btn-outline-success btn-sm" id='btnExport'>Exportar Mat <i class="fas fa-file-excel"></i></button>
                        </div-->
                        </div>
                    </div>
                </form>

                <!--        Listado de matrices         -->
                <div class="col-12 table-responsive mb-3" id="divMatrices">
                    <table class="table table-sm table-hover table-striped compact" id="tblMatrices">
                        <thead class="text-white bg-success">
                            <th>Código</th>
                            <th>Orden</th>
                            <th>Sub</th>
                            <th>Descripción completa</th>
                            <th>Unidad</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>HE(%)</th>
                            <th>Importe</th>
                            <th>Opciones</th>
                        </thead>
                    </table>
                </div>


                <!--    Modal    -->
                <div class="modal fade" id="modal" data-keyboard="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable modal-xl">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-light">
                                <h6 class="modal-title" id="modalLabel">Mano de obra e insumos</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="text" class="form-control form-control-sm" id="Cod" name="Cod" hidden> <!-- Codigo oculto -->

                                <ul class="nav nav-tabs d-flex justify-content-center" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="Manual-tab" data-toggle="tab" href="#Manual" role="tab" aria-controls="Manual" aria-selected="true">Cargar Manual</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="Materiales-tab" data-toggle="tab" href="#Materiales" role="tab" aria-controls="Materiales" aria-selected="false">Listado de Materiales</a>
                                    </li>
                                </ul>


                                <div class="tab-content" id="myTabContent">
                                    <!--        Cargar manual    -->
                                    <div class="tab-pane fade show active" id="Manual" role="tabpanel" aria-labelledby="Manual-tab">
                                        <form name='formMateriales' id='formMateriales'>
                                            <div class="d-flex justify-content-center row">
                                                <div class="form-group col-lg-4 col-md-4 col-sm-6 col-xs-12">
                                                    <label for="Cve_Mat">Clave <span class="text-danger">*</span></label>
                                                    <select class="form-control form-control-sm" id="Cve_Mat" data-live-search="true" name='Cve_Mat' required></select>
                                                </div>

                                                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                    <label for="UMM">Unidad <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm" id="UMM" name='UMM' placeholder="Unidad" required>
                                                </div>

                                                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                    <label for="Cantidad">Cantidad <span class="text-danger">*</span></label>
                                                    <input type="number" step="any" min='0' class="form-control form-control-sm" id="Cantidad" name='Cantidad' placeholder="0.00" required></select>
                                                </div>

                                                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-xs-12 dropdown" title="Presupuesto">
                                                    <label for="PUM" class="dropdown-toggle" type='button' data-toggle="dropdown" aria-expanded="false">Precio <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control form-control-sm" id="PUM" name='PUM' placeholder="$0.00">
                                                    <small class="text-muted" id="Fec_Mod"></small>
                                                    <div class="dropdown-menu" id='divPrecios'></div>
                                                </div>

                                                <div class="form-group col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                                    <label for="Total">Total </label>
                                                    <input type="text" class="form-control form-control-sm" id="Total" name='Total' placeholder="$0.00" readonly>
                                                </div>

                                                <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                    <label for="Concepto">Descripcion <span class="text-danger">*</span></label>
                                                    <textarea type="text" class="form-control form-control-sm" placeholder="Descripción..." maxlength="2000" id="Concepto" name="Concepto" readonly></textarea>
                                                </div>

                                                <div class='form-group col-xs-1' title="Guardar">
                                                    <div>
                                                        <button type="submit" class="btn btn-outline-primary btn-sm" id='guardarMat'>Guardar <i class="fas fa-plus-circle"></i></button>
                                                    </div>
                                                </div>

                                                <div class='form-group col-xs-1 ml-2' title="Cancelar">
                                                    <div>
                                                        <button type="button" class="btn btn-outline-dark btn-sm" data-dismiss="modal">Cerrar <i class="fas fa-times-circle"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                        <div class="col-12 table-responsive" id="divMatriz">
                                            <table class="table table-sm table-hover table-striped compact" id="tblMatriz">
                                                <thead class="text-white bg-success">
                                                    <th>Código</th>
                                                    <th>Tipo</th>
                                                    <th>Descripción completa</th>
                                                    <th>Unidad</th>
                                                    <th>Cantidad</th>
                                                    <th>Precio</th>
                                                    <th>Importe</th>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                    <!--        Listado de materiales    -->
                                    <div class="tab-pane fade active" id="Materiales" role="tabpanel" aria-labelledby="Materiales-tab">
                                        <div class="form-row col-12 col-lg-12 col-md-12 col-sm-12 col-xs-12" id="divMateriales">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover table-striped compact" id="tblMateriales">
                                                    <thead class="text-white bg-success">
                                                        <th>Clave</th>
                                                        <th>Descripción</th>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--    BOTON IR ARRIBA  -->
                <span class="ir-arriba" title="Subir"><i class="fas fa-chevron-up"></i></span>
                <!--Fin centro -->
            </div><!-- /.box -->
        </div>
    </div><!-- /.content-wrapper -->
    <!--Fin-Contenido-->
    <!--js usuariosConectados-->

    <!-- Modal -->
    <div class="modal fade" id="modalCCotizacion" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCCotizacionLabel">Copiar cotizaciones</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="modalCopyC" onclick="limpiarCopy()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-start d-flex row" id="formCopyCotizacion">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">
                            <label for="OtCotizacion">OT</label>
                            <select name="OtCotizacion" id="OtCotizacion" class="form-control form-control-sm" data-live-search="true" title='Num_OT' required></select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">
                            <label for="">No. Cotización</label>
                            <select name="OtNCotizacion" id="OtNCotizacion" class="form-control form-control-sm"></select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">
                            <label for="">Cotización</label>
                            <input type="text" name="cotizacionN" id="cotizacionN" class="form-control form-control-sm" readonly>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-end d-flex mt-3">
                            <button type="submit" class="btn btn-outline-primary btn-sm mr-2" id="btnCopy">Copy <i class="fa-solid fa-floppy-disk fa-beat"></i></button>
                            <button type="button" class="btn btn-outline-secondary btn-sm mr-2" id="btnLimpiarPCopy" onclick="limpiarCopy()">Limpiar <i class="fa-solid fa-eraser fa-beat"></i></button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalCargarE" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Cargar Matrices</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="btnLimpiarE()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-start d-flex row" id="formExcel">
                        <div class="col-10 col-sm-10 col-md-10 col-lg-10 col-xl-10">
                            <label for=""><i class="fa-solid fa-circle-info"></i></label>
                            <input type="file" name="archivoExcelM" id="archivoExcelM" class="form-control form-control-sm" accept=".xlsx" required>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex mt-3">
                            <button type="submit" class="btn btn-outline-success btn-sm mr-2">Guardar <i class="fa-solid fa-floppy-disk fa-beat"></i></button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="btnLimpiarE()">Limpiar <i class="fa-solid fa-eraser fa-beat"></i></button>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table-responsive mt-4" id="tblResultE">
                        </div>
                    </form>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>





    <?php include "../../global/Fooder.php"; ?>
    <script src="../js/presupuestos.js"></script>

</body>

</html>