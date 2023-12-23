<?php
session_start();
if (isset($_SESSION['Id_Empleado'])) {
    include "../../global/Header.php";
?>
    <title>Ordenes de compra</title>
    </head>

    <body>
        <?php include "../global/menu.php"; ?>
        <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-left d-flex row was-validated" id="Form_Compras" name="Form_Compras">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-center">
                <h1 class="alert alert-success rounded-pill" role="alert">Ordenes de compra <i class="fa-solid fa-file-invoice fa-beat"></i></h1>
            </div>

            <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2" title="Número de venta" hidden>
                <label for="Id">Id</label>
                <input type="text" id='Id' name='Id' class='form-control form-control-sm Form_Limp' readonly>
            </div>

            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                <label for="Num_ot">Ordenes de trabajo</label>
                <select name="Num_ot" id="Num_ot" class="form-control form-control-sm" title="-------------------------------------" onchange="Mostrar_Tabla_OC()" data-live-search="true" required></select>
            </div>

            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                <label for="Proveedor">Proveedor</label>
                <select name="Proveedor" id="Proveedor" class="form-control form-control-sm" title="-------------------------------------" onchange="Buscar_Sucursales()" data-live-search="true" required></select>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                <label for="Sucursal">Sucursal</label>
                <select name="Sucursal" id="Sucursal" class="form-control form-control-sm" title="-------------------------------------" data-live-search="true" required></select>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                <label for="Cuenta">Cuenta</label>
                <select name="Cuenta" id="Cuenta" class="form-control form-control-sm" title="-------------------------------------" data-live-search="true" required></select>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                <label for="F_Pago">Forma de pago</label>
                <select name="F_Pago" id="F_Pago" class="form-control form-control-sm selectpicker" title="-------------------------------------" data-live-search="true" required>
                    <option class="text-dark" value="Efectivo">Efectivo</option>
                    <option class="text-dark" value="Deposito">Deposito</option>
                    <option class="text-dark" value="Cheque">Cheque</option>
                    <option class="text-dark" value="Transferencia electrónica">Transferencia electrónica</option>
                </select>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2" title="Número de venta">
                <label for="Fec_Ent">Fecha de entrega</label>
                <input type="date" id='Fec_Ent' name='Fec_Ent' class='form-control form-control-sm Form_Limp' required>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-1 col-xl-1" title="Número de venta">
                <label for="Descuento">¿Descuento?</label>
                <select name="Descuento" id="Descuento" class="form-control form-control-sm selectpicker" onchange="Validar_D()" title="-------------------------------------" required>
                    <option class="text-dark" value="SI">SI</option>
                    <option class="text-dark" value="NO">NO</option>
                </select>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-1 col-xl-1">
                <label for="P_Descuento">Descuento</label>
                <input type="number" step="0.001" min="1" class="form-control form-control-sm" id="P_Descuento" name="P_Descuento" readonly>
            </div>

            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <label for="Observaciones">Observaciones</label>
                <textarea name="Observaciones" id="Observaciones" class="form-control" cols="5"></textarea>
            </div>

            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex mt-3">
                <button type="submit" class="btn btn-outline-success btn-sm mr-2">Guardar <i class="fa-solid fa-floppy-disk fa-beat"></i></button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="Btn_Limpiar">Limpiar <i class="fa-solid fa-eraser fa-beat"></i></button>
            </div>


            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table-responsive mt-4">
                <table class="table table-hover table-sm" id="Tbl_OC">
                    <thead>
                        <tr>
                            <th>N° OC</th>
                            <th>Proveedor</th>
                            <th>Sucursal</th>
                            <th>Forma de pago</th>
                            <th>Fecha de entrega</th>
                            <th>Descuento</th>
                            <th>Status</th>
                            <th>----</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </form>

        <!-- Modal -->
        <div class="modal fade" id="Modal_Materiales" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-primary" id="">Materiales</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3 mt-3" hidden>
                            <label for="Id_OCT">Id</label>
                            <input type="text" class="form-control form-control-sm" id="Id_OCT" name="Id_OCT" readonly>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center row was-validated">

                            <ul class="nav nav-tabs justify-content-center col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="nav_MPendiente" data-toggle="tab" href="#M_Pendiente" role="tab" aria-controls="M_Pendiente" aria-selected="true" onclick="Mostrar_Tabla_MPendientes()">Material pendiente</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="nav_Partidas" data-toggle="tab" href="#Partidas" role="tab" aria-controls="Partidas" aria-selected="false" onclick="Mostrar_Tabla_Parciales()">Partidas</a>
                                </li>
                                <!--li class="nav-item">
                                    <a class="nav-link" id="nav_II" data-toggle="tab" href="#Inventario_Interno" role="tab" aria-controls="Inventario_Interno" aria-selected="true" onclick="">Inventario interno</a>
                                </li-->
                            </ul>

                            <div class="col-12">
                                <div class="tab-content" id="v-pills-tabContent">
                                    <!--------------------------------------------------------------------------------------------------------------------------->
                                    <div class="tab-pane fade show active" id="M_Pendiente" role="tabpanel" aria-labelledby="M_Pendiente-tab">
                                        <form id="Form_Material_Pendiente" name="Form_Material_Pendiente" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center row was-validated">

                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-end d-flex mt-3">
                                                <button type="submit" class="btn btn-outline-success btn-sm mr-2">Guardar <i class="fa-solid fa-floppy-disk fa-beat"></i></button>
                                                <button type="reset" class="btn btn-outline-secondary btn-sm" id="Btn_MP">Limpiar <i class="fa-solid fa-eraser fa-beat"></i></button>
                                            </div>

                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table-responsive mt-4">
                                                <table class="table table-hover table-sm" id="Tbl_MPendiente">
                                                    <thead>
                                                        <tr>
                                                            <th>N°</th>
                                                            <th>Clave</th>
                                                            <th>Material</th>
                                                            <th>C. Pendiente</th>
                                                            <th>Precio</th>
                                                            <th>Familia</th>
                                                            <th>----</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </form>

                                    </div>
                                    <!--------------------------------------------------------------------------------------------------------------------------->
                                    <div class="tab-pane fade" id="Partidas" role="tabpanel" aria-labelledby="Partidas-tab">

                                        <form id="" name="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center row was-validated">
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table-responsive text-center mt-4" hidden id="Div_Alert">
                                                <h4 class="alert alert-success" role="alert" id="alert_parciales"></h4>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table-responsive mt-4">
                                                <table class="table table-hover table-sm" id="Tbl_Parciales">
                                                    <thead>
                                                        <tr>
                                                            <th>N°</th>
                                                            <th>Clave</th>
                                                            <th>Material</th>
                                                            <th>C. Pendiente</th>
                                                            <th>Precio</th>
                                                            <th>Familia</th>
                                                            <th>----</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </form>
                                    </div>
                                    <!--------------------------------------------------------------------------------------------------------------------------->
                                    <!--div class="tab-pane fade" id="Inventario_Interno" role="tabpanel" aria-labelledby="Partidas-tab">

                                        <form id="" name="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center row was-validated">
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table-responsive text-center mt-4" hidden id="Div_Alert">
                                                <h4 class="alert alert-success" role="alert" id="alert_parciales"></h4>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table-responsive mt-4">
                                                <table class="table table-hover table-sm" id="">
                                                    <thead>
                                                        <tr>
                                                            <th>N°</th>
                                                            <th>Clave</th>
                                                            <th>Material</th>
                                                            <th>C. Pendiente</th>
                                                            <th>Precio</th>
                                                            <th>Familia</th>
                                                            <th>----</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </form>
                                    </div-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>

        <?php include "../../global/Fooder.php"; ?>
        <script src="../js/ordenes_compra.js"></script>
    </body>

    </html>

<?php
} else {
    header("location:../index.php");
}
?>