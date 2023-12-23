<?php
session_start();
if (isset($_SESSION['Id_Empleado'])) {
    include "../../global/Header.php";
?>
    <title>Inventario de ordenes de trabajo</title>
    </head>

    <body>
        <?php include "../global/menu.php"; ?>
        <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-left d-flex row was-validated" id="Form_Inventario" name="Form_Inventario">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-center">
                <h1 class="alert alert-success rounded-pill" role="alert">Inventario <i class="fa-solid fa-file-invoice fa-beat"></i></h1>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                <label for="Num_ot">Ordenes de trabajo</label>
                <select name="Num_ot" id="Num_ot" class="form-control form-control-sm" title="-------------------------------------" data-live-search="true" required onchange="Mostrar_Tabla_Inventario()"></select>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                <label for="Id_Material">Materiales</label>
                <select name="Id_Material" id="Id_Material" class="form-control form-control-sm Form_Limp" title="-------------------------------------" data-live-search="true" required></select>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2" title="Número de venta">
                <label for="Cantidad">Cantidad</label>
                <input type="number" min=0 placeholder='0.00' id='Cantidad' name='Cantidad' class='form-control form-control-sm Form_Limp' required>
            </div>

            <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2" title="Número de venta">
                <label for="Precio">Precio</label>
                <input type="number" step="0.001" min="1" id='Precio' name='Precio' class='form-control form-control-sm Form_Limp' placeholder='$0.00' Required>
            </div>

            <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2" title="Número de venta">
                <label for="Fec_Ent">Fecha de entrega</label>
                <input type="date" id='Fec_Ent' name='Fec_Ent' class='form-control form-control-sm Form_Limp' required>
            </div>

            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex mt-3">
                <button type="submit" class="btn btn-outline-success btn-sm mr-2">Guardar <i class="fa-solid fa-floppy-disk fa-beat"></i></button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="Btn_Limpiar">Limpiar <i class="fa-solid fa-eraser fa-beat"></i></button>
            </div>

            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-end d-flex mt-3">
                <button type="button" class="btn btn-outline-warning btn-sm mr-2" data-toggle="modal" data-target="#Material_S" onclick="Mostrar_Tabla_Inventario_S()">MATERIAL EXCEDENTE <i class="fa-solid fa-toolbox fa-beat"></i></button>
            </div>


            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table-responsive mt-4">
                <table class="table table-hover table-sm" id="Tbl_Inventario">
                    <thead>
                        <tr>
                            <th>N°</th>
                            <th>Clave</th>
                            <th>Material</th>
                            <th>Cantidades</th>
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
        <div class="modal fade" id="Material_S" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-primary" id="">Agregar material Sobrante</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-left d-flex row was-validated mb-5" id="Form_Inventario_S" name="Form_Inventario_S">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3" hidden>
                                <label for="Id_MS">Id</label>
                                <input type="text" id='Id_MS' name='Id_MS' class='form-control form-control-sm' readonly>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                <label for="Id_MaterialS">Materiales</label>
                                <select name="Id_MaterialS" id="Id_MaterialS" class="form-control form-control-sm" title="-------------------------------------" data-live-search="true" required></select>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2" title="Número de venta">
                                <label for="Cantidad_MS">Cantidad</label>
                                <input type="number" min=0 placeholder='0.00' id='Cantidad_MS' name='Cantidad_MS' class='form-control form-control-sm' required>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex mt-3">
                                <button type="submit" class="btn btn-outline-success btn-sm mr-2">Guardar <i class="fa-solid fa-floppy-disk fa-beat"></i></button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="Btn_LimMS">Limpiar <i class="fa-solid fa-eraser fa-beat"></i></button>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table-responsive mt-5">
                                <table class="table table-hover table-sm" id="Tbl_Inventario_S">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Clave</th>
                                            <th>Material</th>
                                            <th>Cantidades</th>
                                            <th>----</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer"></div>
                </div>
            </div>
        </div>

        <?php include "../../global/Fooder.php"; ?>
        <script src="../js/Inventario_OT.js"></script>
    </body>

    </html>

<?php
} else {
    header("location:../index.php");
}
?>