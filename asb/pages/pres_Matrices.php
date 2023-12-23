<?php
session_start();

if (!isset($_SESSION['Id_Empleado'])) {
    header('location: ../index.php');
}
include "../../global/Header.php";
?>

<!--    Título    -->
<title>Cat Matrices</title>
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

        table {
            font-size: small;
        }

        .bootstrap-select .dropdown-menu {
            max-width: 250% !important;
        }
    </style>
    <!--Contenido-->
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <div class="col-md-12">
            <div class="box">

                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="divProgramaciones">
                    <div class="d-flex justify-content-center row">
                        <h2 class="box-title text-success">Catalogo de matrices</h2>
                    </div>

                    <!--    Formulario de Matrices   -->
                    <form id="formPartidas" name="formPartidas">
                        <div class="form-row col-lg-12 col-md-12 col-sm-12 col-xs-12 d-flex justify-content-start row">

                            <div class="form-group col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                <div><small><b class="text-primary">I: </b> INSTALACIÓN</small></div>
                                <div><small><b class="text-primary">S: </b> SUMINISTRO</small></div>
                                <div><small><b class="text-primary">R: </b> REPARACIÓN</small></div>
                                <div><small><b class="text-primary">X: </b> SUMINISTRO E INSTALACIÓN</small></div>
                            </div>

                            <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
                                <label for="Pref">Prefijo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="Pref" name="Pref" maxlength="1" required>
                            </div>

                            <div class="form-group col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <label for="Cve">Codigo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="Cve" name='Cve' placeholder="Codigo..." required></select>
                                <datalist id='Codigo'><datalist>
                            </div>

                            <div class="form-group col-lg-1 col-md-2 col-sm-6 col-xs-12">
                                <label for="UM">Unidad <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm" id="UM" name='UM' placeholder="Unidad" required></select>
                            </div>

                            <div class="form-group col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                <label for="PU">Importe </label>
                                <input type="text" class="form-control form-control-sm" id="PU" name='PU' placeholder="$0.00" readonly></select>
                            </div>

                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <label for="Descripcion">Descripcion <span class="text-danger">*</span></label>
                                <textarea type="text" class="form-control form-control-sm" placeholder="Descripción..." maxlength="1000" id="Descripcion" name="Descripcion" required></textarea>
                            </div>
                        </div>

                        <div class="form-row col-lg-12 col-md-12 col-sm-12 col-xs-12 d-flex justify-content-center row">
                            <div class='form-group col-xs-1' title="Agregar">
                                <button type="submit" class="btn btn-outline-info btn-sm">Agregar <i class="fas fa-plus-circle"></i></button>
                            </div>

                            <div class='form-group col-xs-1' title="Limpiar">
                                <button type="button" class="btn btn-outline-dark btn-sm" onclick="limpiar()">Limpiar <i class="fas fa-eraser"></i></button>
                            </div>
                        </div>
                    </form>

                    <div class="form-row col-lg-12 col-md-12 col-sm-12 col-xs-12" id="divMatrices">
                        <div class="col-12">
                            <table class="table table-sm table-hover table-striped compact" id="tblMatrices">
                                <thead class="bg-success text-white">
                                    <th>Codigo</th>
                                    <th>Descripción</th>
                                    <th>Unidad</th>
                                    <th>Importe</th>
                                    <th>Ver</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <!--    Modal    -->
                <div class="modal fade" id="modal" data-keyboard="false" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header bg-success text-light">
                                <h5 class="modal-title" id="modalLabel">Mano de obra e insumos</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <input type="text" class="form-control form-control-sm" id="Cod" name="Cod" hidden> <!-- Codigo oculto -->
                                <form name='formMateriales' id='formMateriales'>
                                    <div class="d-flex justify-content-center row">
                                        <!--div class="form-group col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                    <label for="Tipo">Tipo <span class="text-danger">*</span></label>
                                    <select class="form-control form-control-sm" id="Tipo" name='Tipo' required>
                                        <option value="" selected disabled>Seleccionar...</option>
                                        <option value="INSUMO">INSUMO</option>
                                        <option value="MANO DE OBRA">MANO DE OBRA</option>
                                    </select>
                                </div-->

                                        <div class="form-group col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                            <label for="Cve_Mat">Clave <span class="text-danger">*</span></label>
                                            <select class="form-control form-control-sm" id="Cve_Mat" data-live-search="true" name='Cve_Mat' required></select>
                                        </div>

                                        <div class="form-group col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                            <label for="UMM">Unidad <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-sm" id="UMM" name='UMM' placeholder="Unidad" required></select>
                                        </div>

                                        <div class="form-group col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                            <label for="Cantidad">Cantidad <span class="text-danger">*</span></label>
                                            <input type="number" step="any" min='0' class="form-control form-control-sm" id="Cantidad" name='Cantidad' placeholder="0.00" required></select>
                                        </div>

                                        <div class="form-group col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                            <label for="PUM">Precio <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control form-control-sm" id="PUM" name='PUM' placeholder="$0.00"></select>
                                            <small class="text-muted" id="Fecha_UC"></small>
                                        </div>

                                        <div class="form-group col-lg-2 col-md-4 col-sm-6 col-xs-12">
                                            <label for="Total">Total </label>
                                            <input type="text" class="form-control form-control-sm" id="Total" name='Total' placeholder="$0.00" readonly></select>
                                        </div>

                                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <label for="Concepto">Descripcion <span class="text-danger">*</span></label>
                                            <textarea type="text" class="form-control form-control-sm" placeholder="Descripción..." maxlength="2000" id="Concepto" name="Concepto" required></textarea>
                                        </div>

                                        <div class='form-group col-xs-1' title="Guardar">
                                            <div>
                                                <button type="submit" class="btn btn-outline-info btn-sm">Guardar <i class="fas fa-plus-circle"></i></button>
                                            </div>
                                        </div>
                                        <div class='form-group col-xs-1 ml-2' title="Cancelar">
                                            <div>
                                                <button type="button" class="btn btn-outline-dark btn-sm" data-dismiss="modal">Cancelar <i class="fas fa-times-circle"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                                <div class="form-row" id="divMatriz">
                                    <div class="table-responsive col-12">
                                        <table class="table table-sm table-hover table-striped compact" id="tblMatriz">
                                            <thead class="text-white bg-success">
                                                <th>Clave</th>
                                                <th>Tipo</th>
                                                <th>Descripción</th>
                                                <th>Unidad</th>
                                                <th>Cantidad</th>
                                                <th>Precio</th>
                                                <th>Importe</th>
                                            </thead>
                                        </table>
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

    <?php include "../../global/Fooder.php"; ?>
    <script src="../js/pres_Matrices.js"></script>

</body>

</html>