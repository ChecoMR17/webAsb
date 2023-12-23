<?php
session_start();

include "../../global/Header.php"; ?>
<title>Home</title>
</head>

<body>
    <?php include "../global/menu.php"; ?>
    <form action="" class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-center d-flex row">
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

    <?php include "../../global/Fooder.php"; ?>
    <script src="../js/home.js"></script>
</body>

</html>