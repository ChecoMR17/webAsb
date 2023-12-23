let expresionRegular = /^(|[A-Za-zñÑáéíóúÁÉÍÓÚ0-9\n\" *+/,.=?¡\"#$%)(!¿& -]+)$/;
let tablaReportes;
$(document).ready(() => {
  $("#formBitacora").on("submit", function (e) {
    guardarBitacora(e);
  });
  $("#formEvidencias").on("submit", function (e) {
    guardarEvidencia(e);
  });
  obtenerProyectos();
  obtenerRegistrosB();
});

let guardarBitacora = (e) => {
  e.preventDefault();
  let data = new FormData($("#formBitacora")[0]);
  var descripcion = $("#dBitacora").val();
  if (!expresionRegular.test(descripcion)) {
    $("#mensajeError").text("Contenido invalido");
    $("#dBitacora").addClass("border border-danger");
  } else {
    $("#mensajeError").text("");
    $("#dBitacora").removeClass("border border-danger");
    Swal.fire({
      title: "¿Estás seguro(a) de guardar?",
      text: "",
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "¡Continuar!",
    }).then((Opcion) => {
      if (Opcion.isConfirmed) {
        Swal.fire({
          imageUrl: "../../img/Cargando.gif",
          imageWidth: 400,
          imageHeight: 400,
          background: "background-color: transparent",
          showConfirmButton: false,
          customClass: "transparente",
        });
        setTimeout(() => {
          $.ajax({
            type: "POST",
            url: "../Archivos/bitacora/operaciones.php?op=guardarBitacora",
            data: data,
            contentType: false,
            processData: false,
            success: function (result) {
              if (result == 200) {
                Swal.fire({
                  position: "center",
                  icon: "success",
                  title: "¡Guardado!",
                  showConfirmButton: false,
                  timer: 2500,
                });
                tablaReportes.ajax.reload();
                $("#btnLimpiar").click();
              } else if (result == 202) {
                Swal.fire({
                  position: "center",
                  icon: "warning",
                  title: "¡El cliente que intenta registrar ya existe!",
                  showConfirmButton: false,
                  timer: 2500,
                });
              } else {
                Swal.fire({
                  position: "center",
                  icon: "error",
                  title: "¡Error, Inténtalo más tarde!",
                  showConfirmButton: false,
                  timer: 1500,
                });
              }
            },
          });
        }, 250);
      } else {
        Swal.fire({
          position: "center",
          icon: "info",
          title: "¡Operación cancelada!",
          showConfirmButton: false,
          timer: 1500,
        });
      }
    });
  }
};

$("#idProyecto").change(function () {
  obtenerRegistrosB($(this).val());
  $("#btnDescargarB").attr("hidden", false);
});
let obtenerRegistrosB = (id = 0) => {
  tablaReportes = $("#tblReporteObra")
    .dataTable({
      language: {
        search: "BUSCAR",
        info: "_START_ A _END_ DE _TOTAL_ ELEMENTOS",
      },
      dom: "Bfrtip",
      buttons: ["copy", "excel", "pdf"],
      ajax: {
        url: "../Archivos/bitacora/operaciones.php?op=obtenerRegistrosB",
        type: "post",
        dataType: "json",
        data: { id },
        error: (e) => {
          console.log("Error función listar() \n" + e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 20,
      order: [[0, "asc"]],
    })
    .DataTable();
};

let obtenerDatos = (id) => {
  $.post(
    "../Archivos/bitacora/operaciones.php?op=obtenerDatos",
    { id },
    (result) => {
      $("#id").val(id);
      $("#fechaInicial").val(result.fechaInicio);
      $("#fechaFinal").val(result.FechaFinal);
      $("#dBitacora").val(result.Descripcion);
      $("#fechaInicial").change();
    },
    "json"
  );
};
let obtenerProyectos = () => {
  $.post(
    "../Archivos/bitacora/operaciones.php?op=obtenerProyectos",
    (result) => {
      $("#idProyecto").html(result);
      $("#idProyecto").selectpicker("refresh");
    }
  );
};

$("#btnLimpiar").click(function () {
  $("#dBitacora").removeClass("border border-danger");
  $("#mensajeError").text("");
  $("#id").val("");
  $("#fechaInicial").val("");
  $("#fechaFinal").val("");
  $("#dBitacora").val("");
});

$("#dBitacora").on("keyup", function () {
  if (!expresionRegular.test($(this).val())) {
    $("#mensajeError").text("Contenido invalido");
    $("#dBitacora").addClass("border border-danger");
  } else {
    $("#mensajeError").text("");
    $("#dBitacora").removeClass("border border-danger");
  }
});

$("#fechaInicial").change(function () {
  $("#fechaFinal").attr("min", $(this).val());
});

$("#btnDescargarB").click(function () {
  var id = $("#idProyecto").val();
  if (id != "") {
    url = "../Archivos/bitacora/bitacoraPdf.php?id=" + btoa(id);
    window.open(url);
  } else {
    Swal.fire({
      position: "center",
      icon: "info",
      title: "Debe seleccionar un proyecto!",
      showConfirmButton: false,
      timer: 2500,
    });
  }
});
/** ----------------------------------------------------------------------------------------------------- */
let guardarEvidencia = (e) => {
  e.preventDefault();
  let data = new FormData($("#formEvidencias")[0]);
  let idActividad = $("#idActividad").val();
  let idProyecto = $("#idProyecto").val();
  data.append("idProyecto", idProyecto);
  data.append("idActividad", idActividad);
  Swal.fire({
    title: "¿Estás seguro(a) de guardar?",
    text: "",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "¡Continuar!",
  }).then((Opcion) => {
    if (Opcion.isConfirmed) {
      Swal.fire({
        imageUrl: "../../img/Cargando.gif",
        imageWidth: 400,
        imageHeight: 400,
        background: "background-color: transparent",
        showConfirmButton: false,
        customClass: "transparente",
      });
      setTimeout(() => {
        $.ajax({
          type: "POST",
          url: "../Archivos/bitacora/operaciones.php?op=guardarEvidencia",
          data: data,
          contentType: false,
          processData: false,
          success: function (result) {
            if (result == 200) {
              Swal.fire({
                position: "center",
                icon: "success",
                title: "¡Guardado!",
                showConfirmButton: false,
                timer: 2500,
              });
              $("#btnLimpiarE").click();
              mostrarEvidencias(idActividad);
            } else if (result == 202) {
              Swal.fire({
                position: "center",
                icon: "warning",
                title: "¡El cliente que intenta registrar ya existe!",
                showConfirmButton: false,
                timer: 2500,
              });
            } else {
              Swal.fire({
                position: "center",
                icon: "error",
                title: "¡Error, Inténtalo más tarde!",
                showConfirmButton: false,
                timer: 1500,
              });
            }
          },
        });
      }, 250);
    } else {
      Swal.fire({
        position: "center",
        icon: "info",
        title: "¡Operación cancelada!",
        showConfirmButton: false,
        timer: 1500,
      });
    }
  });
};

let datosEvidencias = (id) => {
  $("#idActividad").val(id);
  mostrarEvidencias(id);
};

let mostrarEvidencias = (id) => {
  $.post(
    "../Archivos/bitacora/operaciones.php?op=mostrarEvidencias",
    { id },
    (result) => {
      $("#mostrarEvidencias").html(result);
    }
  );
};

let rotarImg = (id) => {
  let idActividad = $("#idActividad").val();
  $.post(
    "../Archivos/bitacora/operaciones.php?op=rotarImg",
    { id },
    (result) => {
      if (result == 200) {
        Swal.fire({
          position: "center",
          icon: "info",
          title: "¡La imagen se roto correctamente!",
          text: "Es necesario cerrar el modal y volver a abrirlo para poder ver el cambio",
          showConfirmButton: false,
          timer: 2000,
        });
        setTimeout(() => {
          mostrarEvidencias(idActividad);
        }, 2000);
      } else {
        Swal.fire({
          position: "center",
          icon: "error",
          title: "Ocurrió un error al rotar la imagen!",
          showConfirmButton: false,
          timer: 1500,
        });
      }
    }
  );
};

let actualizarOrden = (id, input) => {
  let orden = input;
  $.post("../Archivos/bitacora/operaciones.php?op=actualizarOrden", {
    id,
    orden,
  });
};

let actualizarNombre = (id, input) => {
  let nombreImg = input;
  $.post("../Archivos/bitacora/operaciones.php?op=actualizarNombre", {
    id,
    nombreImg,
  });
};

let eliminarEvidencia = (id) => {
  let idActividad = $("#idActividad").val();
  Swal.fire({
    title: "¿Estás seguro(a) de guardar?",
    text: "",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "¡Continuar!",
  }).then((Opcion) => {
    if (Opcion.isConfirmed) {
      Swal.fire({
        imageUrl: "../../img/Cargando.gif",
        imageWidth: 400,
        imageHeight: 400,
        background: "background-color: transparent",
        showConfirmButton: false,
        customClass: "transparente",
      });
      setTimeout(() => {
        $.post(
          "../Archivos/bitacora/operaciones.php?op=eliminarEvidencia",
          { id },
          (result) => {
            if (result == 200) {
              Swal.fire({
                position: "center",
                icon: "success",
                title: "¡Eliminado!",
                showConfirmButton: false,
                timer: 2000,
              });
              mostrarEvidencias(idActividad);
            } else {
              Swal.fire({
                position: "center",
                icon: "error",
                title: "¡Ocurrió un error al eliminar!",
                showConfirmButton: false,
                timer: 2000,
              });
            }
          }
        );
      }, 250);
    } else {
      Swal.fire({
        position: "center",
        icon: "info",
        title: "¡Operación cancelada!",
        showConfirmButton: false,
        timer: 1500,
      });
    }
  });
};
