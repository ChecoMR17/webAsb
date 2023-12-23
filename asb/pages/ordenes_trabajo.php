<?php
session_start();
if (isset($_SESSION['Id_Empleado'])) {
    include "../../global/Header.php"; ?>
    <title>Ordenes de trabajo</title>
    </head>

    <body>
        <?php include "../global/menu.php"; ?>
        <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-start d-flex row was-validated" id="Form_Ordenes_Trabajo">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 text-center">
                <h1 class="alert alert-primary rounded-pill" role="alert">Ordenes de trabajo <i class="fa-solid fa-person-digging fa-beat"></i></h1>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-1 col-xl-1">
                <label for="Id">Folio</label>
                <input type="text" class="form-control form-control-sm" id="Id" name="Id" readonly>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12"></div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                <label for="Cliente">Cliente </label>
                <select name="Cliente" id="Cliente" class="form-control form-control-sm " onchange="Buscar_Obras()" data-live-search="true" title="----------------------------------------------" required></select>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                <label for="Obras">Obras </label>
                <select name="Obras" id="Obras" class="form-control form-control-sm " title="----------------------------------------------" required></select>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                <label for="Contactos">Contactos</label>
                <select name="Contactos" id="Contactos" class="form-control form-control-sm " title="----------------------------------------------" required></select>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                <label for="Clasificacion">Clasificación</label>
                <select name="Clasificacion" id="Clasificacion" class="form-control form-control-sm  selectpicker show-tick" title="----------------------------------------------" required></select>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                <label for="Prioridad">Prioridad</label>
                <select name="Prioridad" id="Prioridad" class="form-control form-control-sm  selectpicker show-tick" title="----------------------------------------------" required>
                    <option class="text-danger" value="Alto">Alto</option>
                    <option class="text-warning" value="Mediano">Mediano</option>
                    <option class="text-success" value="Bajo">Bajo</option>
                </select>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                <label for="Proyecto">Proyecto</label>
                <input type="text" class="form-control form-control-sm " id="Proyecto" name="Proyecto" maxlength="200" required>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                <label for="Fecha_Inicio">Fecha de inicio</label>
                <input type="date" class="form-control form-control-sm " id="Fecha_Inicio" name="Fecha_Inicio" required>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                <label for="Fecha_Final">Fecha de final</label>
                <input type="date" class="form-control form-control-sm " id="Fecha_Final" name="Fecha_Final" required>
            </div>

            <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2">
                <label for="N_Cotizacion">N° Cotización</label>
                <select name="N_Cotizacion" id="N_Cotizacion" class="form-control form-control-sm show-tick" title="----------------------------------------------"> </select>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3 justify-content-center d-flex mt-auto row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <label for="Fecha_Inicio">Status</label>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="Opciones_Status" id="S_Ejecucion" value="U">
                        <label class="form-check-label text-success" for="S_Ejecucion">En Ejecución</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="Opciones_Status" id="S_Concluido" value="C" disabled>
                        <label class="form-check-label text-secondary" for="S_Concluido">Concluido</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="Opciones_Status" id="S_Cancelado" value="B" disabled>
                        <label class="form-check-label text-danger" for="S_Cancelado">Cancelado</label>
                    </div>

                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                <label for="Observaciones">Observaciones</label>
                <textarea name="Observaciones" id="Observaciones" rows="5" class="form-control " maxlength="5000"></textarea>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex mt-3">
                <button type="submit" class="btn btn-outline-success btn-sm mr-2">Guardar <i class="fa-solid fa-floppy-disk fa-beat"></i></button>
                <button type="reset" class="btn btn-outline-secondary btn-sm" id="" onclick="Limpiar_F_OT()">Limpiar <i class="fa-solid fa-eraser fa-beat"></i></button>
            </div>

            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-end d-flex mt-3">
                <button type="button" class="btn btn-warning btn-sm mr-2" data-toggle="modal" data-target="#Guardar_Clasificaciones" onclick="Mostrar_Tbl_Clasificaciones()">Agregar clasificaciones <i class="fa-regular fa-file-lines fa-beat"></i></button>
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#GuardarPanel" onclick="MostrarPanelesControl()">Agregar panel</button>
            </div>

            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table-responsive mt-4">
                <table class="table table-hover table-sm" id="Tbl_Ordenes_Trabajo">
                    <thead>
                        <tr>
                            <th class="text-center" rowspan="2">Folio</th>
                            <th class="text-center" rowspan="2">Cliente</th>
                            <th class="text-center" colspan="4">Datos de obra</th>
                            <th class="text-center" rowspan="2">Fechas</th>
                            <th class="text-center" rowspan="2">Detalles</th>
                            <th class="text-center" rowspan="2">Status</th>
                            <th class="text-center" rowspan="2">--------</th>
                        </tr>
                        <tr>
                            <th class="text-center">Obra</th>
                            <th class="text-center">Proyecto</th>
                            <th class="text-center">Contacto</th>
                            <th class="text-center">Prioridad</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </form>

        <!-- Modal -->
        <div class="modal fade" id="Guardar_Clasificaciones" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg modal-center">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-primary" id="">Clasificaciones</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="Limpiar_Formulario_Calcificaciones()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">

                        <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex row was-validated" id="Form_Clasificaciones">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2" hidden>
                                <label for="Id_Clasificacion">Id</label>
                                <input type="text" class="form-control form-control-sm" id="Id_Clasificacion" name="Id_Clasificacion" readonly>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <label for="Nombre_Clasificacion">Nombre </label>
                                <input type="text" class="form-control form-control-sm " id="Nombre_Clasificacion" name="Nombre_Clasificacion" maxlength="150" required>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex mt-3">
                                <button type="submit" class="btn btn-outline-success btn-sm mr-2">Guardar <i class="fa-solid fa-floppy-disk fa-beat"></i></button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="Limpiar_Formulario_Calcificaciones()">Limpiar <i class="fa-solid fa-eraser fa-beat"></i></button>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table-responsive mt-4">
                                <table class="table table-hover table-sm" id="Tbl_Clasificaciones">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Nombre</th>
                                            <th>--------</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="GuardarPanel" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg modal-center">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-primary">Panel de control</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">

                        <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex row" id="FormPanelControl">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2" hidden>
                                <label for="IdPanel">Id</label>
                                <input type="text" class="form-control form-control-sm" id="IdPanel" name="IdPanel" readonly>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <label for="namePanel">Nombre </label>
                                <input type="text" class="form-control form-control-sm " id="namePanel" name="namePanel" maxlength="150" required>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <label for="imagenPanel">Imagen <i class="fa-solid fa-circle-info" title="Las imágenes deben deben de estar en formato avif"></i></label>
                                <input type="file" class="form-control form-control-sm " id="imagenPanel" name="imagenPanel" accept="image/avif" required>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex mt-3">
                                <button type="submit" class="btn btn-outline-success btn-sm mr-2">Guardar <i class="fa-solid fa-floppy-disk fa-beat"></i></button>
                                <button type="reset" class="btn btn-outline-secondary btn-sm" id="btnLimpiarPanel">Limpiar <i class="fa-solid fa-eraser fa-beat"></i></button>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex row mt-3" id="IdMostrarPaneles"></div>
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="Guardar_Actividades" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl modal-center">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-primary" id="">Lista de actividades</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="Btn_Limpiar_A()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2" hidden>
                            <label for="OT">Id</label>
                            <input type="text" class="form-control form-control-sm" id="OT" name="OT" readonly>
                        </div>

                        <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-start d-flex row was-validated" id="Form_Actividades">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2" hidden>
                                <label for="Id_Actividad">Id</label>
                                <input type="text" class="form-control form-control-sm" id="Id_Actividad" name="Id_Actividad" readonly>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-3">
                                <label for="Fecha_Actividad">Fecha </label>
                                <input type="date" class="form-control form-control-sm " id="Fecha_Actividad" name="Fecha_Actividad" required>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-9">
                                <label for="Nombre_Actividad">Actividad </label>
                                <input type="text" class="form-control form-control-sm " id="Nombre_Actividad" name="Nombre_Actividad" maxlength="200" required>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <label for="Descripcion_Actividad">Descripción </label>
                                <textarea name="Descripcion_Actividad" id="Descripcion_Actividad" rows="5" class="form-control " maxlength="5000"></textarea>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex mt-3">
                                <button type="submit" class="btn btn-outline-success btn-sm mr-2" id="Btn_GA">Guardar <i class="fa-solid fa-floppy-disk fa-beat"></i></button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="Btn_Limpiar_A()">Limpiar <i class="fa-solid fa-eraser fa-beat"></i></button>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table-responsive mt-4">
                                <table class="table table-hover table-sm" id="Tbl_Actividades">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Nombre</th>
                                            <th>Descripción</th>
                                            <th>--------</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="Guardar_Documentos" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl modal-center">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-primary" id="">Subir documentos</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="Limpiar_Form_Archivos()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2" hidden>
                            <label for="OT_D">Id</label>
                            <input type="text" class="form-control form-control-sm" id="OT_D" name="OT_D" readonly>
                        </div>

                        <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-start d-flex row was-validated" id="Form_Documentos">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <label for="Nombre_documento">Tipo de documento </label>
                                <input type="text" class="form-control form-control-sm " id="Nombre_documento" name="Nombre_documento" maxlength="100" required>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <label for="Documento">Archivo </label>
                                <input type="file" class="form-control form-control-sm " id="Documento" name="Documento" accept=".pdf,.png,.jpg" required>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <label for="Descripcion_Documento">Observaciones </label>
                                <textarea name="Descripcion_Documento" id="Descripcion_Documento" rows="5" class="form-control " maxlength="1000"></textarea>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex mt-3">
                                <button type="submit" class="btn btn-outline-success btn-sm mr-2" id="Btn_GA">Guardar <i class="fa-solid fa-floppy-disk fa-beat"></i></button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="Limpiar_Form_Archivos()">Limpiar <i class="fa-solid fa-eraser fa-beat"></i></button>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex mt-3" id="Id_Mostrar_Documentos"></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalTelemetria" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl modal-center">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-primary" id="">Telemetria</h4>
                        <button type="button" class="close" data-dismiss="modal" id="cerrarModalT" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-start d-flex row">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <h4 class="alert alert-success" role="alert">Alta de dispositivos</h4>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2" hidden>
                                <label for="">proyecto</label>
                                <input type="text" name="idProyecto" id="idProyecto" class="form-control form-control-sm" readonly>
                            </div>
                        </div>

                        <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-start d-flex row" id="formTelemetriaDispositivos">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                <label for="">Dispositivo</label>
                                <select name="idDispositivo" id="idDispositivo" class="form-control form-control-sm"></select>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-9 col-xl-9"></div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                <label for="">Clave</label>
                                <input type="text" name="claveDispositivo" id="claveDispositivo" class="form-control form-control-sm">
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                <label for="">Host MQTT</label>
                                <input type="text" name="hostMqtt" id="hostMqtt" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                <label for="">Puerto MQTT</label>
                                <input type="text" name="puertoMqtt" id="puertoMqtt" class="form-control form-control-sm" required>
                                <span id="mensajePuertoM" class="text-danger"></span>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                <label for="">Tiempo de envió MQTT</label>
                                <input type="text" name="timeMqtt" id="timeMqtt" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                <label for="">Host PLC</label>
                                <input type="text" name="hostPlc" id="hostPlc" class="form-control form-control-sm" required>
                                <span id="mensajeHost" class="text-danger"></span>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                <label for="">Puerto del PLC</label>
                                <input type="text" name="puertoPlc" id="puertoPlc" class="form-control form-control-sm" required>
                                <span id="mensajePuertoP" class="text-danger"></span>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                <label for="">Longitud</label>
                                <input type="text" name="longitud" id="longitud" class="form-control form-control-sm">
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                <label for="">Latitud</label>
                                <input type="text" name="latitud" id="latitud" class="form-control form-control-sm">
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                <label for="">Tiempo de guardado en local</label>
                                <input type="text" name="guardadoLocal" id="guardadoLocal" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3">
                                <label for="">Licencia</label>
                                <input type="date" name="licencia" id="licencia" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-end d-flex mt-3 mb-3">
                                <button type="submit" class="btn btn-outline-success btn-sm mr-2">Guardar <i class="fa-solid fa-floppy-disk fa-beat"></i></button>
                                <button type="reset" class="btn btn-outline-secondary btn-sm mr-2" id="btnLimpiarD">Limpiar <i class="fa-solid fa-eraser fa-beat"></i></button>
                                <button type="button" class="btn btn-outline-info btn-sm" id="btnUserT" data-toggle="modal" data-target="#modalUsuarios" hidden>Usuarios <i class="fa-solid fa-users"></i></button>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-end d-flex mt-3 mb-3">
                                <button type="button" class="btn btn-info btn-sm mr-2" onclick="actualizarD()">Actualizar dispositivo <i class="fa-solid fa-cloud-arrow-up"></i></button>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <h4 class="alert alert-primary" role="alert">Lista de paneles de control</h4>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex row mt-3" id="mostrarPanelD"></div>
                        </form>

                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalPanelesControl" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl modal-center">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-primary" id="">Alta de parámetros</h4>
                        <button type="button" class="close" data-dismiss="modal" id="cerrarModalT" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4" hidden>
                            <label for="">Panel de control</label>
                            <input type="text" id="panelControl" name="panelControl" class="form-control form-control-sm" readonly>
                        </div>
                        <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-start d-flex row mb-5 mt-3" id="formTelemetriaParametros">

                            <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-3" hidden>
                                <label for="">Id</label>
                                <input type="text" name="IdParametro" id="IdParametro" class="form-control form-control-sm">
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2">
                                <label for="">Dirección</label>
                                <input type="text" name="direccionesP" id="direccionesP" class="form-control form-control-sm" pattern="[0-9]{3,4}" title="Ingresa un número de 3 a 4 cifras" required>
                                <span id="mDireccion" class="text-warning"></span>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2">
                                <label for="">Tipo</label>
                                <select name="tipoParametro" id="tipoParametro" class="form-control form-control-sm" required>
                                    <option value="INT">INT</option>
                                    <option value="FLOAT">FLOAT</option>
                                    <option value="BIT">BIT</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2">
                                <label for="">Clasificación</label>
                                <select name="clasificacionParametro" id="clasificacionParametro" class="form-control form-control-sm" required>
                                    <option value="BOMBA">BOMBA</option>
                                    <option value="BOTON">BOTON</option>
                                    <option value="VOLTAJE">VOLTAJE</option>
                                    <option value="CORRIENTE">CORRIENTE</option>
                                    <option value="FRECUENCIA">FRECUENCIA</option>
                                    <option value="POTENCIA">POTENCIA</option>
                                    <option value="PRESION">PRESION</option>
                                    <option value="ALARMA">ALARMA</option>
                                    <option value="PRESION">0-600</option>

                                </select>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <label for="">Nombre del parámetro</label>
                                <input type="text" name="nombreParametro" id="nombreParametro" class="form-control form-control-sm" required>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2">
                                <label for="">Unidad de medida</label>
                                <input type="text" name="unidadM" id="unidadM" class="form-control form-control-sm">
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-2 col-xl-2">
                                <label for="">Permisos</label>
                                <select name="permiso" id="permiso" class="form-control form-control-sm" required>
                                    <option value="N">Ninguno</option>
                                    <option value="L">Lectura</option>
                                    <option value="E">Escritura</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <label for="">Descripción</label>
                                <input type="text" name="descripcionP" id="descripcionP" maxlength="255" class="form-control form-control-sm">
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-end d-flex mt-3">
                                <button type="submit" class="btn btn-outline-primary btn-sm mr-2">Guardar <i class="fa-solid fa-floppy-disk fa-beat"></i></button>
                                <button type="reset" class="btn btn-outline-secondary btn-sm mr-2" id="btnLimpiarP">Limpiar <i class="fa-solid fa-eraser fa-beat"></i></button>
                            </div>

                            <div class="btn-group col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-end d-flex mt-3" role="group" aria-label="Button group with nested dropdown">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        Acciones Mqtt
                                    </button>
                                    <div class="dropdown-menu">
                                        <button type="button" class="dropdown-item" id="btnDownload">Archivos de instalación <i class="fa-solid fa-file-zipper"></i></button>
                                        <button type="button" class="dropdown-item" id="btnUsuariosT" data-toggle="modal" data-target="#dataD">Consultar <i class="fa-solid fa-file-import"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex row mt-3" id="mostrarPanelD"></div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table-responsive mt-4">
                                <table class="table table-hover table-sm" id="tablaParametros">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Clasificación</th>
                                            <th class="text-center">Tipo</th>
                                            <th class="text-center">Nombre</th>
                                            <th class="text-center">UM</th>
                                            <th class="text-center">Permisos</th>
                                            <th class="text-center">Descripción</th>
                                            <th class="text-center">--------</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>


                        </form>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalSubRegistros" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg modal-center">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-primary" id="">Alta de SubParámetros</h4>
                        <button type="button" class="close" data-dismiss="modal" id="cerrarModalT" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6" hidden>
                            <label for="">Id parametro</label>
                            <input type="text" name="IdParametroS" id="IdParametroS" class="form-control form-control-sm" readonly>
                        </div>
                        <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-start d-flex row mb-5 mt-3" id="formSubParametros">

                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6" hidden>
                                <label for="">Id</label>
                                <input type="text" name="IdSubParametro" id="IdSubParametro" class="form-control form-control-sm" readonly>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-4 col-xl-4">
                                <label for="">Dirección</label>
                                <input type="text" name="direccionesPS" id="direccionesPS" class="form-control form-control-sm" pattern="[0-9]{3,4}" title="Ingresa un número de 3 a 4 cifras" required>
                                <span id="mDireccionS" class="text-warning"></span>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-8 col-xl-8">
                                <label for="">Nombre del parámetro</label>
                                <input type="text" name="nombreParametroS" id="nombreParametroS" class="form-control form-control-sm" required>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-end d-flex mt-3">
                                <button type="submit" class="btn btn-outline-primary btn-sm mr-2">Guardar <i class="fa-solid fa-floppy-disk fa-beat"></i></button>
                                <button type="reset" class="btn btn-outline-secondary btn-sm mr-2" id="btnLimpiarPS">Limpiar <i class="fa-solid fa-eraser fa-beat"></i></button>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table-responsive mt-4">
                                <table class="table table-hover table-sm" id="tablaSubParametros">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Nombre</th>
                                            <th class="text-center">--------</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>


                        </form>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal -->
        <div class="modal fade" id="dataD" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl modal-center">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-primary" id="">Consultas</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeCsql"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">

                        <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-start d-flex row" id="Form_Documentos">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12">
                                <label for="">sql query</label>
                                <input type="text" name="sqlQ" id="sqlQ" maxlength="255" class="form-control form-control-sm">
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-end d-flex mt-3">
                                <button type="button" class="btn btn-outline-primary btn-sm mr-2" id="enviarC">Enviar <i class="fa-solid fa-floppy-disk fa-beat"></i></button>
                                <button type="reset" class="btn btn-outline-secondary btn-sm mr-2" id="btnLimpiarP">Limpiar <i class="fa-solid fa-eraser fa-beat"></i></button>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table-responsive mt-4">
                                <table class="table table-hover table-sm" id="tablaQuery">
                                    <thead id="queryH">
                                    </thead>
                                    <tbody id="queryB">
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalUsuarios" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-md modal-center">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title text-primary" id="">Alta de usuarios</h4>
                        <button type="button" class="close" data-dismiss="modal" id="cerrarModalT" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-start d-flex row" id="formTelemetriaUsuarios">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6" hidden>
                                <label for="">Id usuario</label>
                                <input type="text" name="userIdT" id="userIdT" class="form-control form-control-sm" readonly>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <label for="">Usuario</label>
                                <input type="text" name="userT" id="userT" class="form-control form-control-sm" required>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <label for="">Contraseña</label>
                                <input type="password" name="passT" id="passT" class="form-control form-control-sm" required>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
                                <label for="">Perfil</label>
                                <select name="perfilUserT" id="perfilUserT" class="form-control form-control-sm" required>
                                    <option value="A">Admin</option>
                                    <option value="E">Estándar</option>
                                </select>
                            </div>

                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-end d-flex mt-3">
                                <button type="submit" class="btn btn-outline-success btn-sm mr-2">Guardar <i class="fa-solid fa-floppy-disk fa-beat"></i></button>
                                <button type="reset" class="btn btn-outline-secondary btn-sm" id="btnLimpiarUt">Limpiar <i class="fa-solid fa-eraser fa-beat"></i></button>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 table-responsive mt-4">
                                <ul class="list-group list-group-flush" id="listUser"></ul>
                            </div>
                        </form>

                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>

        <?php include "../../global/Fooder.php"; ?>
        <script src="../Library/mqtt/mqtt.min.js"></script>
        <script src="../js/ot.js"></script>
    </body>

    </html>
<?php
} else {
    header("location:../index.php");
}
?>