<?php
session_start();
include "../../global/Header.php"; ?>
<title>Bitacora</title>
</head>

<body>
    <?php include "../global/menu.php"; ?>

    <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex row" id="formBitacora" name="formBitacora">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-center">
            <h2 class="alert alert-primary" role="alert">
                Reporte de obra
            </h2>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-1 col-xl-1" hidden>
            <label for="id">Id</label>
            <input type="text" name="id" id="id" class="form-control form-control-sm" readonly>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8">
            <label for="idProyecto">Proyecto <span class="text-danger">*</span></label>
            <select name="idProyecto" id="idProyecto" data-live-search="true" class="form-control form-control-sm" title="------------------------------" required>
            </select>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2">
            <label for="fechaInicial">Fecha inicial <span class="text-danger">*</span></label>
            <input type="date" name="fechaInicial" id="fechaInicial" min="<?php echo date('Y-m-d', strtotime('-1 week')); ?>" class="form-control form-control-sm" required>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2">
            <label for="fechaFinal">Fecha final <span class="text-danger">*</span></label>
            <input type="date" name="fechaFinal" id="fechaFinal" min="<?php echo date('Y-m-d', strtotime('-1 week')); ?>" max="<?php echo date('Y-m-d', strtotime('+4 week')); ?>" class="form-control form-control-sm" required>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
            <label for="dBitacora">Descripción <span class="text-danger">*</label>
            <textarea name="dBitacora" id="dBitacora" class="form-control" rows="8" required></textarea>
            </span> <span id="mensajeError" class="text-danger"></span>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex mt-3">
            <button type="submit" class="btn btn-outline-success btn-sm mr-2">Guardar <i class="fa-solid fa-floppy-disk"></i></button>
            <button type="button" class="btn btn-outline-secondary btn-sm mr-2" id="btnLimpiar">Limpiar <i class="fa-solid fa-eraser"></i></button>
            <button type="button" class="btn btn-danger btn-sm" id="btnDescargarB" hidden>Bitacora <i class="fa-solid fa-file-arrow-down"></i></button>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table-responsive mt-4">
            <table class="table table-hover table-sm" id="tblReporteObra">
                <thead>
                    <tr>
                        <th>Responsable</th>
                        <th>Fechas</th>
                        <th>Descripción</th>
                        <th>--------</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </form>

    <!-- Modal -->
    <div class="modal fade" id="modalEvidencias" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Evidencias</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2" hidden>
                        <label for="idActividad">Actividad</label>
                        <input type="text" name="idActividad" id="idActividad" class="form-control form-control-sm" readonly>
                    </div>
                    <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex row" id="formEvidencias">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2" hidden>
                            <label for="idEvidencia">id</label>
                            <input type="text" name="idEvidencia" id="idEvidencia" class="form-control form-control-sm" readonly>
                        </div>

                        <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8">
                            <label for="nombreImg">Nombre</label>
                            <input type="text" name="nombreImg" id="nombreImg" class="form-control form-control-sm">
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">
                            <label for="">Archivo <span class="text-danger">*</span></label>
                            <input type="file" accept="image/*,.pdf" id="evidencias[]" name="evidencias[]" multiple class="form-control form-control-sm" required>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex mt-3">
                            <button type="submit" class="btn btn-outline-success btn-sm mr-2">Guardar <i class="fa-solid fa-floppy-disk"></i></button>
                            <button type="reset" class="btn btn-outline-secondary btn-sm mr-2" id="btnLimpiarE">Limpiar <i class="fa-solid fa-eraser"></i></button>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-left d-flex row mt-3" id="mostrarEvidencias"></div>
                    </form>
                </div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>


    <?php include "../../global/Fooder.php"; ?>
    <script src="../js/bitacora.js"></script>
</body>

</html>