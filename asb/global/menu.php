<?php
include "../global/conexion.php";

$Credenciales = array();
// Cargamos los permisos del usuario
$permisos = ejecutarConsulta("SELECT * FROM permisos_user WHERE Id_Usuario='" . $_SESSION['Id_Usuario'] . "';");
while ($fila = mysqli_fetch_object($permisos)) {
    $Credenciales[$fila->Id_Permiso] = "Permiso_$fila->Id_Permiso";
}

?>

<nav class="navbar navbar-expand-lg mb-4" id="main_navbar">
    <picture><img src="../../img/logo.avif" width="70" height="70" class="img-fluid ${3|rounded-top,rounded-right,rounded-bottom,rounded-left,rounded-circle,|}" alt="asBombeo"></picture>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="fas fa-water text-light"></i></button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto" id="ulMenu">
            <li class="nav-item dropdown">
                <a class="dropdown-item" href="./home.php">Home <i class="fa-solid fa-house-chimney fa-beat"></i></a>
            </li>
            <?php if (!empty($Credenciales[1])) { ?>
                <li class="nav-item dropdown">
                    <a class="dropdown-item" href="./Empleados.php">Personal <i class="fa-solid fa-person-circle-check fa-beat"></i></a>
                </li>
            <?php }
            if (!empty($Credenciales[2])) {
            ?>
                <li class="nav-item dropdown">
                    <a class="dropdown-item" href="./clientes.php">Clientes <i class="fa-solid fa-people-group fa-beat"></i></a>
                </li>
            <?php }
            if (!empty($Credenciales[3])) {
            ?>
                <li class="nav-item dropdown">
                    <a class="dropdown-item" href="./proveedores.php">Proveedores <i class="fa-solid fa-people-carry-box fa-beat"></i></a>
                </li>
            <?php }
            if (!empty($Credenciales[4]) || !empty($Credenciales[9]) || !empty($Credenciales[10]) || !empty($Credenciales[11])) { ?>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle Sistemas" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Ordenes de trabajo <i class="fa-solid fa-helmet-safety fa-beat"></i>
                    </a>
                    <ul class="dropdown-menu triangulo_fijo" aria-labelledby="navbarDropdown">
                        <?php if (!empty($Credenciales[11])) { ?>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item" href="./presupuestos.php">Presupuestos <i class="fa-solid fa-file-signature fa-beat"></i></a>
                            </li>
                        <?php } ?>

                        <?php if (!empty($Credenciales[4])) { ?>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item" href="./ordenes_trabajo.php">Alta de ordenes de trabajo <i class="fa-solid fa-person-digging fa-beat"></i></a>
                            </li>
                        <?php }
                        if (!empty($Credenciales[9])) { ?>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item" href="./Inventario_OT.php">Alta de inventario <i class="fa-solid fa-file-invoice fa-beat"></i></a>
                            </li>
                        <?php }
                        if (!empty($Credenciales[10])) { ?>
                            <li class="nav-item dropdown">
                                <a class="dropdown-item" href="./ordenes_compra.php">Ordenes de compra <i class="fa-solid fa-store fa-beat"></i></a>
                            </li>
                        <?php } ?>
                        <li class="nav-item dropdown">
                            <a class="dropdown-item" href="./bitacora.php">Bitácora <i class="fa-solid fa-calendar-week"></i></a>
                        </li>
                    </ul>
                </li>
            <?php }
            if (!empty($Credenciales[5])) { ?>
                <li class="nav-item dropdown">
                    <a class="dropdown-item" href="./cat_materiales.php">Inventario <i class="fa-solid fa-warehouse fa-beat"></i></a>
                </li>
            <?php }
            if (!empty($Credenciales[6])) {
            ?>
                <li class="nav-item dropdown">
                    <a class="dropdown-item" href="./ventas.php">Ventas <i class="fa-solid fa-dollar-sign fa-beat"></i></a>
                </li>
            <?php } ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Configuración <i class="fa-solid fa-gear"></i>
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item text-dark" href="../Archivos/sesion/Operaciones_Sesion.php?op=Cerrar_Sesion">Cerrar sesión <i class="fa-solid fa-right-from-bracket"></i></a>
                </ul>
            </li>
        </ul>
    </div>
</nav>