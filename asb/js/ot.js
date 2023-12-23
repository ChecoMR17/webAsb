let Tbl_Actividades, TblParametros, Tbl_Clasificaciones, Tbl_OT, TblParametrosS;
const clientId = `brokerAsb:${Math.floor(Math.random() * (10000 - 1 + 1) + 1)}`;
let mqttUrl = `ws://www.sistema-asbombeo.com:8083/mqtt`;
let rutaP = "http://localhost:5000";
const opcionesMqtt = {
  clientId: clientId,
  clean: true,
};
$(document).ready(() => {
  $("#Form_Ordenes_Trabajo").on("submit", function (e) {
    Guardar_Ordenes_Trabajo(e);
  });
  $("#Form_Clasificaciones").on("submit", function (e) {
    Guardar_Clasificacion(e);
  });
  $("#Form_Actividades").on("submit", function (e) {
    Guardar_Actividad(e);
  });

  $("#Form_Documentos").on("submit", function (e) {
    Guardar_Documentos(e);
  });

  $("#formTelemetriaDispositivos").on("submit", function (e) {
    GuardarDispositivo(e);
  });

  $("#formTelemetriaParametros").on("submit", function (e) {
    GuardarParametros(e);
  });

  $("#formSubParametros").on("submit", function (e) {
    formSubParametros(e);
  });

  $("#formTelemetriaUsuarios").on("submit", function (e) {
    GuardarTelemetriaUsuarios(e);
  });

  $("#FormPanelControl").on("submit", function (e) {
    GuardarFormPanelControl(e);
  });

  Mostrar_Clientes();
  Mostrar_Lista_OT();
  Buscar_Clasificacion();
});

let GuardarFormPanelControl = (e) => {
  e.preventDefault();
  let data = new FormData($("#FormPanelControl")[0]);
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
          url: "../Archivos/Ordenes/Operaciones.php?op=guardarPanel",
          data: data,
          contentType: false,
          processData: false,
          success: function (result) {
            console.log(result);
            if (result == 200) {
              Swal.fire({
                position: "center",
                icon: "success",
                title: "¡Guardado!",
                showConfirmButton: false,
                timer: 2500,
              });
              $("#btnLimpiarPanel").click();
              MostrarPanelesControl();
            } else if (result == 203) {
              Swal.fire({
                position: "center",
                icon: "warning",
                title: "Los datos que intentas ingresar ya existen",
                showConfirmButton: false,
                timer: 1500,
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

let MostrarPanelesControl = () => {
  let indicador = "N";
  let Id = 0;
  $.post(
    "../Archivos/Ordenes/Operaciones.php?op=mostrarPanel",
    { indicador, Id },
    (result) => {
      //console.log(result);
      $("#IdMostrarPaneles").html(result);
    }
  );
};

let editarImg = (id, nombre) => {
  $("#IdPanel").val(id);
  $("#namePanel").val(nombre);
};

/*
let listarPaneles = () => {
  $.post("../Archivos/Ordenes/Operaciones.php?op=listarPaneles", (result) => {
    $("#panelControl").html(result);
  });
};*/
/*
$("#panelControl").change(function () {
  let Id = $(this).val();
  $.post(
    "../Archivos/Ordenes/Operaciones.php?op=buscarImg",
    { Id },
    (result) => {
      $("#muestra").html(result);
    }
  );
});*/

let mostrarModalPD = (idP) => {
  let id = $("#idDispositivo").val();
  $("#panelControl").val(idP);
  setTimeout(() => {
    consultarParametros(id, idP);
  }, 500);
};

let pasarIdParametro = (Id) => {
  console.log(Id);
  $("#IdParametroS").val(Id);

  setTimeout(() => {
    TblParametrosS = $("#tablaSubParametros")
      .dataTable({
        language: {
          search: "BUSCAR",
          info: "_START_ A _END_ DE _TOTAL_ ELEMENTOS",
        },
        dom: "Bfrtip",
        buttons: ["copy", "pdf"],
        autoFill: true,
        colReorder: true,
        rowReorder: true,
        ajax: {
          url: "../Archivos/Ordenes/Operaciones.php?op=consultarSubParametros",
          type: "post",
          dataType: "json",
          data: { Id },
          error: (e) => {
            console.log("Error función listar() \n" + e.responseText);
          },
        },
        bDestroy: true,
        iDisplayLength: 200,
        order: [[0, "desc"]],
      })
      .DataTable();
  }, 500);
};
let Guardar_Ordenes_Trabajo = (e) => {
  e.preventDefault();
  let data = new FormData($("#Form_Ordenes_Trabajo")[0]);
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
          url: "../Archivos/Ordenes/Operaciones.php?op=Guardar_Ordenes_Trabajo",
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
              Limpiar_F_OT();
              Tbl_OT.ajax.reload();
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

let Ejecucion_Ot = (Id) => {
  Swal.fire({
    title: "¿Estás seguro(a) de ejecutar la orden de trabajo?",
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
          "../Archivos/Ordenes/Operaciones.php?op=Ejecucion_Ot",
          { Id },
          (result) => {
            if (result == 200) {
              Swal.fire({
                position: "center",
                icon: "success",
                title: "!En ejecución¡",
                showConfirmButton: false,
                timer: 2500,
              });
              Tbl_OT.ajax.reload();
            } else {
              Swal.fire({
                position: "center",
                icon: "error",
                title: "¡Error, inténtelo mas tarde¡",
                showConfirmButton: false,
                timer: 2500,
              });
            }
          }
        );
      }, 250);
    } else {
      Swal.fire({
        position: "center",
        icon: "info",
        title: "!Operación cancelada¡",
        showConfirmButton: false,
        timer: 1500,
      });
    }
  });
};
let Datos_Modificar = (Id) => {
  $.post(
    "../Archivos/Ordenes/Operaciones.php?op=Datos_Modificar",
    { Id },
    (result) => {
      result = JSON.parse(result);
      Swal.fire({
        position: "center",
        icon: "info",
        title: "¡Listo para modificar!",
        showConfirmButton: false,
        timer: 1000,
      });
      $("#Id").val(Id);
      $("#Cliente").val(result.Id_Cliente);
      $("#Cliente").selectpicker("refresh");
      $("#Prioridad").val(result.Prioridad);
      $("#Prioridad").selectpicker("refresh");
      $("#Proyecto").val(result.Proyecto);
      $("#Fecha_Inicio").val(result.Fecha_Inicio);
      $("#Fecha_Final").val(result.Fecha_Final);
      $("#Observaciones").val(result.Observaciones);
      $("#Clasificacion").val(result.Id_Clasificacion);
      $(".form-check-input").attr("disabled", false);
      if (result.Status == "U") {
        $("#S_Ejecucion").prop("checked", true);
      } else if (result.Status == "C") {
        $("#S_Concluido").prop("checked", true);
      } else if (result.Status == "B") {
        $("#S_Cancelado").prop("checked", true);
      }
      Buscar_Obras();
      Buscar_cotizaciones(Id);
      setTimeout(() => {
        $("#N_Cotizacion").val(result.N_Cotizacion);
        $("#N_Cotizacion").selectpicker("refresh");
      }, 250);
      setTimeout(() => {
        $("#Obras").val(result.Id_Obra);
        $("#Contactos").val(result.Id_Contacto);
        $("#Clasificacion").selectpicker("refresh");
        $("#Obras").selectpicker("refresh");
        $("#Contactos").selectpicker("refresh");
      }, 250);
    }
  );
};

let Buscar_cotizaciones = (Id) => {
  $.post(
    "../Archivos/Ordenes/Operaciones.php?op=Buscar_cotizaciones",
    { Id },
    (result) => {
      $("#N_Cotizacion").html(result);
      $("#N_Cotizacion").selectpicker("refresh");
    }
  );
};

let Mostrar_Lista_OT = () => {
  Tbl_OT = $("#Tbl_Ordenes_Trabajo")
    .dataTable({
      language: {
        search: "BUSCAR",
        info: "_START_ A _END_ DE _TOTAL_ ELEMENTOS",
      },
      dom: "Bfrtip",
      buttons: ["copy", "excel", "pdf"],
      autoFill: true,
      colReorder: true,
      rowReorder: true,
      ajax: {
        url: "../Archivos/Ordenes/Operaciones.php?op=Mostrar_Lista_OT",
        type: "post",
        dataType: "json",
        error: (e) => {
          console.log("Error función listar() \n" + e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 200,
      order: [[0, "desc"]],
    })
    .DataTable();
};

let Mostrar_Clientes = () => {
  $.post(
    "../Archivos/Ordenes/Operaciones.php?op=Mostrar_Clientes",
    (result) => {
      $("#Cliente").html(result);
      $("#Cliente").selectpicker("refresh");
    }
  );
};

let Buscar_Obras = () => {
  Cliente = $("#Cliente").val();
  $.post(
    "../Archivos/Ordenes/Operaciones.php?op=Buscar_Obras",
    { Cliente },
    (result) => {
      $("#Obras").html(result);
      $("#Obras").selectpicker("refresh");
      Buscar_Contactos(Cliente);
    }
  );
};
let Buscar_Contactos = (Cliente) => {
  $.post(
    "../Archivos/Ordenes/Operaciones.php?op=Buscar_Contactos",
    { Cliente },
    (result) => {
      $("#Contactos").html(result);
      $("#Contactos").selectpicker("refresh");
    }
  );
};

let Limpiar_F_OT = () => {
  $("#Id").val("");
  $("#Cliente").val("");
  $("#Obras").html("");
  $("#Contactos").html("");
  $("#Prioridad").val("");
  $("#Proyecto").val("");
  $("#Fecha_Inicio").val("");
  $("#Fecha_Final").val("");
  $("#Observaciones").val("");
  $("#Clasificacion").val("");
  $("#N_Cotizacion").html("");
  $("#S_Concluido").attr("disabled", true);
  $("#S_Cancelado").attr("disabled", true);
  $("#N_Cotizacion").selectpicker("refresh");
  $("#Clasificacion").selectpicker("refresh");
  $("#Cliente").selectpicker("refresh");
  $("#Prioridad").selectpicker("refresh");
  $("#Obras").selectpicker("refresh");
  $("#Contactos").selectpicker("refresh");
};

/** --------------------------------------------- CLASIFICACIONES -------------------------------------------------------- */
let Guardar_Clasificacion = (e) => {
  e.preventDefault();
  let data = new FormData($("#Form_Clasificaciones")[0]);
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
          url: "../Archivos/Ordenes/Operaciones.php?op=Guardar_Clasificacion",
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
              Limpiar_Formulario_Calcificaciones();
              Tbl_Clasificaciones.ajax.reload();
              Buscar_Clasificacion();
            } else if (result == 202) {
              Swal.fire({
                position: "center",
                icon: "warning",
                title: "¡La clasificación que intenta registrar ya existe!",
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

let Mostrar_Tbl_Clasificaciones = () => {
  setTimeout(() => {
    Tbl_Clasificaciones = $("#Tbl_Clasificaciones")
      .dataTable({
        language: {
          search: "BUSCAR",
          info: "_START_ A _END_ DE _TOTAL_ ELEMENTOS",
        },
        dom: "Bfrtip",
        buttons: ["copy", "excel", "pdf"],
        autoFill: true,
        colReorder: true,
        rowReorder: true,
        ajax: {
          url: "../Archivos/Ordenes/Operaciones.php?op=Mostrar_Tbl_Clasificaciones",
          type: "post",
          dataType: "json",
          error: (e) => {
            console.log("Error función listar() \n" + e.responseText);
          },
        },
        bDestroy: true,
        iDisplayLength: 20,
        order: [[0, "desc"]],
      })
      .DataTable();
  }, 250);
};

let Datos_Clasificacion = (Id_Clasificacion) => {
  $.post(
    "../Archivos/Ordenes/Operaciones.php?op=Datos_Clasificacion",
    { Id_Clasificacion },
    (result) => {
      result = JSON.parse(result);
      $("#Id_Clasificacion").val(Id_Clasificacion);
      $("#Nombre_Clasificacion").val(result.Nombre);
    }
  );
};

let Eliminar_Clasificacion = (Id_Clasificacion) => {
  Swal.fire({
    title: "¿Estás seguro(a) de eliminar la clasificación?",
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
          "../Archivos/Ordenes/Operaciones.php?op=Eliminar_Clasificacion",
          { Id_Clasificacion },
          (result) => {
            if (result == 200) {
              Swal.fire({
                position: "center",
                icon: "success",
                title: "¡La clasificación se elimino!",
                showConfirmButton: false,
                timer: 1500,
              });
              Tbl_Clasificaciones.ajax.reload();
              Buscar_Clasificacion();
            } else {
              Swal.fire({
                position: "center",
                icon: "error",
                title: "¡Error, Inténtalo más tarde!",
                showConfirmButton: false,
                timer: 1500,
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

let Buscar_Clasificacion = () => {
  $.post(
    "../Archivos/Ordenes/Operaciones.php?op=Buscar_Clasificacion",
    (result) => {
      $("#Clasificacion").html(result);
      $("#Clasificacion").selectpicker("refresh");
    }
  );
};

let Limpiar_Formulario_Calcificaciones = () => {
  $("#Id_Clasificacion").val("");
  $("#Nombre_Clasificacion").val("");
};

/** --------------------------------------------- ACTIVIDADES -------------------------------------------------------- */

let Mostrar_Id = (Id, btn) => {
  btn == 0
    ? $("#Btn_GA").attr("disabled", true)
    : $("#Btn_GA").attr("disabled", false);
  $("#OT").val(Id);
  setTimeout(() => {
    Tbl_Actividades = $("#Tbl_Actividades")
      .dataTable({
        language: {
          search: "BUSCAR",
          info: "_START_ A _END_ DE _TOTAL_ ELEMENTOS",
        },
        dom: "Bfrtip",
        buttons: ["copy", "excel", "pdf"],
        autoFill: true,
        colReorder: true,
        rowReorder: true,
        ajax: {
          url: "../Archivos/Ordenes/Operaciones.php?op=Mostrar_Tbl_Actividades",
          type: "post",
          dataType: "json",
          data: { Id },
          error: (e) => {
            console.log("Error función listar() \n" + e.responseText);
          },
        },
        bDestroy: true,
        iDisplayLength: 20,
        order: [[0, "desc"]],
      })
      .DataTable();
  }, 250);
};

let Guardar_Actividad = (e) => {
  e.preventDefault();
  let data = new FormData($("#Form_Actividades")[0]);
  Id = $("#OT").val();
  data.append("Id", Id);

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
          url: "../Archivos/Ordenes/Operaciones.php?op=Guardar_Actividad",
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
              Tbl_Actividades.ajax.reload();
              Btn_Limpiar_A();
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

let Datos_Modificar_A = (Id) => {
  $.post(
    "../Archivos/Ordenes/Operaciones.php?op=Datos_Modificar_A",
    { Id },
    (result) => {
      result = JSON.parse(result);
      Swal.fire({
        position: "center",
        icon: "info",
        title: "¡Listo para modificar!",
        showConfirmButton: false,
        timer: 1000,
      });
      $("#Id_Actividad").val(Id);
      $("#Fecha_Actividad").val(result.Fecha_Actividad);
      $("#Nombre_Actividad").val(result.Actividad);
      $("#Descripcion_Actividad").val(result.Descripcion);
    }
  );
};

let Elimiar_Actividad = (Id_Actividad) => {
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
          "../Archivos/Ordenes/Operaciones.php?op=Elimiar_Actividad",
          { Id_Actividad },
          (result) => {
            if (result == 200) {
              Swal.fire({
                position: "center",
                icon: "success",
                title: "Eliminado!",
                showConfirmButton: false,
                timer: 1500,
              });
              Tbl_Actividades.ajax.reload();
            } else {
              Swal.fire({
                position: "center",
                icon: "error",
                title: "¡Error al eliminar!",
                showConfirmButton: false,
                timer: 1500,
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

let Btn_Limpiar_A = () => {
  $("#Id_Actividad").val("");
  $("#Fecha_Actividad").val("");
  $("#Nombre_Actividad").val("");
  $("#Descripcion_Actividad").val("");
};

/** --------------------------------------------- GUARDAR DOCUMENTOS -------------------------------------------------------- */

let Mostrar_D_D = (Id, validar) => {
  $("#OT_D").val(Id);
  Buscar_Archivos(Id);
};
let Guardar_Documentos = (e) => {
  e.preventDefault();
  let data = new FormData($("#Form_Documentos")[0]);
  Id = $("#OT_D").val();
  data.append("Id", Id);
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
          url: "../Archivos/Ordenes/Operaciones.php?op=Guardar_Documentos",
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
              Buscar_Archivos(Id);
              Limpiar_Form_Archivos();
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

let Buscar_Archivos = (Id) => {
  $.post(
    "../Archivos/Ordenes/Operaciones.php?op=Buscar_Archivos",
    { Id },
    (result) => {
      $("#Id_Mostrar_Documentos").html(result);
    }
  );
};

let Descargar_Archivo = (Ruta, nombre) => {
  var a = document.createElement("a");
  a.download = nombre;
  a.target = "_blank";
  a.href = Ruta;
  a.click();
};

let Ampliar_Archivo = (Ruta) => {
  window.open(Ruta, "_blank");
};

let Eliminar_Archivo = (Id) => {
  Swal.fire({
    title: "¿Estás seguro(a) de eliminar el archivo?",
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
          "../Archivos/Ordenes/Operaciones.php?op=Eliminar_Archivo",
          { Id },
          (result) => {
            if (result == 200) {
              Swal.fire({
                position: "center",
                icon: "success",
                title: "Eliminado!",
                showConfirmButton: false,
                timer: 2500,
              });
              Buscar_Archivos(Id);
              Limpiar_Form_Archivos();
            } else {
              Swal.fire({
                position: "center",
                icon: "error",
                title: "¡Error, Inténtalo más tarde!",
                showConfirmButton: false,
                timer: 1500,
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

let Limpiar_Form_Archivos = () => {
  $("#Nombre_documento").val("");
  $("#Documento").val("");
  $("#Descripcion_Documento").val("");
};

/** ----------------------------------------------- TELEMETRIA ---------------------------------------------------- */
let GuardarDispositivo = (e) => {
  e.preventDefault();
  let data = new FormData($("#formTelemetriaDispositivos")[0]);
  var claveDispositivo = $("#claveDispositivo").val();
  var idProyecto = $("#idProyecto").val();
  var idDispositivo = $("#idDispositivo").val();
  data.append("Id", idProyecto);
  data.append("idDispositivo", idDispositivo);
  data.append("clave", claveDispositivo);

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
          url: "../Archivos/Ordenes/Operaciones.php?op=guardarDispositivo",
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
              idDispositivo == "" ? $("#btnLimpiarD").click() : "";
              idDispositivo == "" ? listaDispositivos(idProyecto) : "";
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

let GuardarParametros = (e) => {
  e.preventDefault();
  let data = new FormData($("#formTelemetriaParametros")[0]);
  var idDispositivo = $("#idDispositivo").val();
  var panelControl = $("#panelControl").val();

  if (idDispositivo != null) {
    data.append("idDispositivo", idDispositivo);
    data.append("panelControl", panelControl);
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
            url: "../Archivos/Ordenes/Operaciones.php?op=GuardarParametros",
            data: data,
            contentType: false,
            processData: false,
            success: function (result) {
              console.log(result);
              if (result == 200) {
                Swal.fire({
                  position: "center",
                  icon: "success",
                  title: "¡Guardado!",
                  showConfirmButton: false,
                  timer: 2500,
                });
                $("#btnLimpiarP").click();
                TblParametros.ajax.reload();
                mostrarPanelD(idDispositivo);
              } else if (result == 203) {
                Swal.fire({
                  position: "center",
                  icon: "warning",
                  title:
                    "El registro que intentas ingresar ya existe en algún panel de control!",
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
          timer: 2000,
        });
      }
    });
  } else {
    Swal.fire({
      position: "center",
      icon: "warning",
      title: "Favor de seleccionar un dispositivo!",
      showConfirmButton: false,
      timer: 1500,
    });
  }
};

let formSubParametros = (e) => {
  e.preventDefault();
  let data = new FormData($("#formSubParametros")[0]);
  var idDispositivo = $("#idDispositivo").val();
  var IdParametroS = $("#IdParametroS").val();
  data.append("idDispositivo", idDispositivo);
  data.append("IdParametroS", IdParametroS);
  console.log(data);
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
          url: "../Archivos/Ordenes/Operaciones.php?op=GuardarSubParametros",
          data: data,
          contentType: false,
          processData: false,
          success: function (result) {
            console.log(result);
            if (result == 200) {
              Swal.fire({
                position: "center",
                icon: "success",
                title: "¡Guardado!",
                showConfirmButton: false,
                timer: 2500,
              });
              $("#btnLimpiarPS").click();
              TblParametrosS.ajax.reload();
            } else if (result == 203) {
              Swal.fire({
                position: "center",
                icon: "warning",
                title:
                  "¡La dirección que intentas ingresar ya existe en algún panel de control!",
                showConfirmButton: false,
                timer: 2500,
              });
              $("#btnLimpiarPS").click();
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

let listaDispositivos = (Id) => {
  $.post(
    "../Archivos/Ordenes/Operaciones.php?op=listaDispositivos",
    { Id },
    (data) => {
      $("#idDispositivo").html(data);
    }
  );
};

$("#idDispositivo").on("change", function () {
  var id = $(this).val();
  if (id != "") {
    $("#claveDispositivo").attr("readonly", true);
    consultarDispositivo(id);
    //consultarParametros(id);
    mostrarPanelD(id);
    $("#btnUserT").attr("hidden", false);
  } else {
    $("#mostrarPanelD").html("");
    $("#btnUserT").attr("hidden", true);
    $("#claveDispositivo").attr("readonly", false);
    $("#btnLimpiarD").click();
  }
});
let consultarDispositivo = (Id) => {
  $.post(
    "../Archivos/Ordenes/Operaciones.php?op=consultarDispositivo",
    { Id },
    (data) => {
      data = JSON.parse(data);
      $("#claveDispositivo").val(data.clave);
      $("#hostMqtt").val(data.mqttHost);
      $("#puertoMqtt").val(data.mqttPort);
      $("#timeMqtt").val(data.mqttTime);
      $("#hostPlc").val(data.plcHost);
      $("#puertoPlc").val(data.plcPort);
      $("#longitud").val(data.longitud);
      $("#latitud").val(data.latitud);
      $("#guardadoLocal").val(data.mysqlTime);
      $("#licencia").val(data.licencia);
    }
  );
};

let consultarParametros = (Id, panelControl) => {
  TblParametros = $("#tablaParametros")
    .dataTable({
      language: {
        search: "BUSCAR",
        info: "_START_ A _END_ DE _TOTAL_ ELEMENTOS",
      },
      dom: "Bfrtip",
      buttons: ["copy", "pdf"],
      autoFill: true,
      colReorder: true,
      rowReorder: true,
      ajax: {
        url: "../Archivos/Ordenes/Operaciones.php?op=consultarParametros",
        type: "post",
        dataType: "json",
        data: { Id, panelControl },
        error: (e) => {
          console.log("Error función listar() \n" + e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 200,
      order: [[0, "desc"]],
    })
    .DataTable();
};

let mostrarPanelD = (Id) => {
  console.log("Dispositivo: ", Id);
  let indicador = "P";
  $.post(
    "../Archivos/Ordenes/Operaciones.php?op=mostrarPanel",
    { Id, indicador },
    (result) => {
      $("#mostrarPanelD").html(result);
    }
  );
};

let consultarParametro = (Id) => {
  $.post(
    "../Archivos/Ordenes/Operaciones.php?op=consultarParametro",
    { Id },
    (data) => {
      $("#IdParametro").val(data.id);
      $("#direccionesP").val(data.addr);
      $("#tipoParametro").val(data.tipo);
      $("#nombreParametro").val(data.nombre);
      $("#unidadM").val(data.um);
      $("#permiso").val(data.permiso);
      $("#descripcionP").val(data.descripcion);
      $("#clasificacionParametro").val(data.clasificacion);
    }
  );
};

let validarImpar = (number) => {
  return number % 2 == 0 ? true : false;
};

$("#direccionesP").keyup(function () {
  if ($(this).val() != "") {
    validarImpar($(this).val())
      ? $("#mDireccion").text("Se recomienda ingresar un numero impar")
      : $("#mDireccion").text("");
  } else {
    $("#mDireccion").text("");
  }
});

$("#direccionesPS").keyup(function () {
  if ($(this).val() != "") {
    validarImpar($(this).val())
      ? $("#mDireccionS").text("Se recomienda ingresar un numero impar")
      : $("#mDireccionS").text("");
  } else {
    $("#mDireccionS").text("");
  }
});

let ModificarSubParametro = (Id, addr, nombre) => {
  $("#IdSubParametro").val(Id);
  $("#direccionesPS").val(addr);
  $("#nombreParametroS").val(nombre);
};

let eliminarSubParametro = (Id) => {
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
          "../Archivos/Ordenes/Operaciones.php?op=eliminarSubParametro",
          { Id },
          (result) => {
            if (result == 200) {
              Swal.fire({
                position: "center",
                icon: "success",
                title: "¡Eliminado!",
                showConfirmButton: false,
                timer: 2500,
              });
              TblParametrosS.ajax.reload();
            } else {
              Swal.fire({
                position: "center",
                icon: "error",
                title: "¡Error, al eliminar!",
                showConfirmButton: false,
                timer: 1500,
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
let valoresTelemetria = (id) => {
  listaDispositivos(id);
  //listarPaneles();
  $("#idProyecto").val(id);
  /*
  setTimeout(() => {
    consultarParametros("");
  }, 500);*/
  //$("#claveDispositivo").val("device" + id);
};
let validarDireccionIP = (ip) => {
  var patronIPv4 =
    /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
  if (ip.match(patronIPv4)) {
    return true;
  } else {
    return false;
  }
};

let validarPuertos = (puerto) => {
  var patronPuerto = /^[1-9]\d{0,4}$/;
  if (
    puerto.match(patronPuerto) &&
    parseInt(puerto) >= 1 &&
    parseInt(puerto) <= 65535
  ) {
    return true;
  } else {
    return false;
  }
};

$("#hostPlc").keyup(function () {
  validarDireccionIP($(this).val())
    ? $("#mensajeHost").text("")
    : $("#mensajeHost").text("Dirección ip invalida");
});

$("#puertoPlc").on("keyup", function () {
  validarPuertos($(this).val())
    ? $("#mensajePuertoP").text("")
    : $("#mensajePuertoP").text("Puerto invalida");
});

$("#puertoMqtt").on("keyup", function () {
  validarPuertos($(this).val())
    ? $("#mensajePuertoM").text("")
    : $("#mensajePuertoM").text("Puerto invalida");
});

$("#btnLimpiarD").click(function () {
  consultarParametros("");
});

$("#cerrarModalT").click(function () {
  $("#btnLimpiarD").click();
  $("#btnLimpiarP").click();
  consultarParametros("");
  $("#mostrarPanelD").html("");
});

$("#btnDownload").click(function () {
  var clave = $("#claveDispositivo").val();
  var Id = $("#idProyecto").val();
  if (clave != "") {
    $.post(
      "../Archivos/Ordenes/Operaciones.php?op=descargarInstalacion",
      { clave, Id },
      (data) => {
        if (data.status === "success") {
          var ruta = data.ruta;
          var link = document.createElement("a");
          link.href = ruta;
          link.download = clave + ".zip";
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
          // eliminar
          setTimeout(() => {
            $.post(
              "../Archivos/Ordenes/Operaciones.php?op=eliminarArchivo",
              { ruta },
              (result) => {}
            );
          }, 500);
        } else {
          Swal.fire({
            position: "center",
            icon: "info",
            title: data.message,
            showConfirmButton: false,
            timer: 2000,
          });
        }
      }
    );
  } else {
    Swal.fire({
      position: "center",
      icon: "warning",
      title: "Favor de seleccionar un dispositivo!",
      showConfirmButton: false,
      timer: 2000,
    });
  }
});

/**---------------------------------------------------------- MQTT --------------------------------------------- */
let actualizarPMqtt = (Id) => {
  let clave = $("#claveDispositivo").val();
  let folio = $("#idProyecto").val();
  topicBase = `asb/proyecto${folio}/${clave}/sql/parametros`;
  clientMQTT = mqtt.connect(mqttUrl, opcionesMqtt);
  clientMQTT.on("connect", () => {
    $.post(
      "../Archivos/Ordenes/Operaciones.php?op=consultarParametro",
      { Id },
      (data) => {
        let sendData = {
          id: data.id,
          tipo: data.tipo,
          addr: data.addr,
          nombre: data.nombre,
          descripcion: data.descripcion,
          permiso: data.permiso,
          um: data.um,
        };

        suscribirseATopico(clientMQTT, `${topicBase}/#`, () => {
          publicarMensaje(
            clientMQTT,
            `${topicBase}/update`,
            JSON.stringify(sendData),
            () => {
              setTimeout(() => {
                clientMQTT.end();
                // Maneja el evento de desconexión MQTT
                clientMQTT.on("close", () => {
                  console.log("Conexión MQTT cerrada");
                });
              }, 300);
            }
          );
        });
      }
    );
  });
};

let actualizarD = () => {
  let Id = $("#idDispositivo").val();
  let clave = $("#claveDispositivo").val();
  let folio = $("#idProyecto").val();
  topicBase = `asb/proyecto${folio}/${clave}/sql/dispositivos`;
  clientMQTT = mqtt.connect(mqttUrl, opcionesMqtt);
  if (Id != null && Id != "") {
    $.post(
      "../Archivos/Ordenes/Operaciones.php?op=consultarDispositivo",
      { Id },
      (data) => {
        data = JSON.parse(data);
        clientMQTT.on("connect", () => {
          let sendData = {
            clave: data.clave,
            mqttTime: data.mqttTime,
            mqttHost: data.mqttHost,
            mqttPort: data.mqttPort,
            plcHost: data.plcHost,
            plcPort: data.plcPort,
            mysqlTime: data.mqttTime,
            latitud: data.latitud,
            longitud: data.longitud,
            licencia: data.licencia,
            idProyecto: data.idProyecto,
            id: data.id,
          };

          suscribirseATopico(clientMQTT, `${topicBase}/#`, () => {
            publicarMensaje(
              clientMQTT,
              `${topicBase}/update`,
              JSON.stringify(sendData),
              () => {
                setTimeout(() => {
                  clientMQTT.end();
                  // Maneja el evento de desconexión MQTT
                  clientMQTT.on("close", () => {
                    console.log("Conexión MQTT cerrada");
                  });
                }, 300);
              }
            );
          });
        });
      }
    );
  } else {
    Swal.fire({
      position: "center",
      icon: "warning",
      title: "Debes seleccionar un dispositivo",
      showConfirmButton: false,
      timer: 2000,
    });
  }
};
let suscribirseATopico = (clientMQTT, topic, callback) => {
  clientMQTT.subscribe(topic, (error) => {
    if (!error) {
      //console.log(`data publicado en ${topic}`);
      if (typeof callback === "function") {
        callback();
      }
    } else {
      console.log(`Error al suscribirse`, error);
    }
  });
};

let publicarMensaje = (clientMQTT, topic, data, callback) => {
  clientMQTT.publish(topic, data, (error) => {
    if (!error) {
      //console.log(`data publicado en ${topic}: ${data}`);
      if (typeof callback === "function") {
        callback();
      }
    } else {
      console.log(`Error al publicar mensaje`, error);
    }
  });
};

var tableQ = $("#tablaQuery");
$("#enviarC").click(function () {
  let Id = $("#idDispositivo").val();
  let clave = $("#claveDispositivo").val();
  let folio = $("#idProyecto").val();
  let sqlQ = $("#sqlQ").val();
  if (clave != "") {
    if (sqlQ != "") {
      $("#sqlQ").val("");
      topicBase = `asb/proyecto${folio}/${clave}/sql/query`;
      clientMQTT = mqtt.connect(mqttUrl, opcionesMqtt);
      suscribirseATopico(clientMQTT, `${topicBase}/#`, () => {
        publicarMensaje(
          clientMQTT,
          `${topicBase}/data`,
          JSON.stringify(sqlQ),
          () => {
            setTimeout(() => {
              clientMQTT.end();
              clientMQTT.on("close", () => {
                console.log("Conexión MQTT cerrada");
              });
            }, 3000);
          }
        );
      });
      clientMQTT.on("message", (topic, message) => {
        message = JSON.parse(message);
        if (topic.includes(`${topicBase}/result`)) {
          $("#queryB").html("");
          $("#queryH").html("");
          var valores = message.data;
          var keys = Object.keys(valores[0]);
          var headerRow = $("<tr></tr>");
          keys.forEach(function (key) {
            headerRow.append($("<th></th>").text(key));
          });
          $("#queryH").html(headerRow);

          valores.forEach(function (item) {
            var row = $("<tr></tr>");
            keys.forEach(function (key) {
              var textV = esFechaValida(item[key])
                ? formatearF(item[key])
                : item[key];
              row.append($("<td></td>").text(textV));
            });
            $("#queryB").append(row);
          });
          if ($.fn.DataTable.isDataTable(tableQ)) {
            tableQ.DataTable().destroy();
          }
          tableQ.dataTable({
            language: {
              search: "BUSCAR",
              info: "_START_ A _END_ DE _TOTAL_ ELEMENTOS",
            },
            dom: "Bfrtip",
            buttons: ["copy", "excel"],
            iDisplayLength: 50,
            order: [[0, "desc"]],
          });
        }
      });
    } else {
      Swal.fire({
        position: "center",
        icon: "warning",
        title: "Debes ingresar una consulta",
        showConfirmButton: false,
        timer: 2000,
      });
    }
  } else {
    Swal.fire({
      position: "center",
      icon: "warning",
      title: "Debes seleccionar un dispositivo",
      showConfirmButton: false,
      timer: 2000,
      willClose: () => {
        $("#sqlQ").val("");
      },
    });
  }
});

let esFechaValida = (fechaISO) => {
  const fecha = new Date(fechaISO);
  return (
    fecha instanceof Date && !isNaN(fecha) && fecha.toISOString() === fechaISO
  );
};

let formatearF = (fechaISO) => {
  const fecha = new Date(fechaISO);
  if (!(fecha instanceof Date) || isNaN(fecha)) {
    return "Fecha inválida";
  }
  const dia = ("0" + fecha.getDate()).slice(-2);
  const mes = ("0" + (fecha.getMonth() + 1)).slice(-2);
  const año = fecha.getFullYear();
  const fechaFormateada = `${dia}-${mes}-${año}`;
  return fechaFormateada;
};

let GuardarTelemetriaUsuarios = (e) => {
  e.preventDefault();
  var user = $("#userT").val();
  var pass = $("#passT").val();
  var idProyecto = $("#idProyecto").val();
  var tipoUsuario = $("#perfilUserT").val();
  var idUsuario = $("#userIdT").val();
  const apiUrlGuardar = `${rutaP}/data/v1/asb/guardar/usuario/telemetria`;
  const apiUrlActualizar = `${rutaP}/data/v1/asb/actualizar/usuario/telemetria`;
  const datos = {
    user,
    pass,
    tipoUsuario,
    idProyecto,
  };
  if (idUsuario != "") {
    datos.idUsuario = idUsuario;
    consultarUrl(apiUrlActualizar, datos, "PUT")
      .then((datos) => {
        var icon = datos.message == "Guardado" ? "success" : "info";
        Swal.fire({
          position: "center",
          icon: icon,
          title: datos.message,
          showConfirmButton: false,
          timer: 1500,
        });
        listaUsuarios(idProyecto);
        $("#btnLimpiarUt").click();
      })
      .catch((error) => {
        Swal.fire({
          position: "center",
          icon: "error",
          title: "Ocurrió un error al guardar",
          showConfirmButton: false,
          timer: 1500,
        });
      });
  } else {
    consultarUrl(apiUrlGuardar, datos, "POST")
      .then((datos) => {
        var icon = datos.message == "Guardado" ? "success" : "info";
        Swal.fire({
          position: "center",
          icon: icon,
          title: datos.message,
          showConfirmButton: false,
          timer: 1500,
        });
        listaUsuarios(idProyecto);
        $("#btnLimpiarUt").click();
      })
      .catch((error) => {
        Swal.fire({
          position: "center",
          icon: "error",
          title: "Ocurrió un error al guardar",
          showConfirmButton: false,
          timer: 1500,
        });
      });
  }
};

$("#btnUserT").on("click", function () {
  var idProyecto = $("#idProyecto").val();
  listaUsuarios(idProyecto);
});

let listaUsuarios = (idProyecto) => {
  var list = "";
  const apiUrl = `${rutaP}/data/v1/asb/lista/usuarios/telemetria/${idProyecto}`;
  consultarGet(apiUrl)
    .then((datos) => {
      datos.data.forEach((element) => {
        var btnE =
          element.status == "A"
            ? `<button type="button" class="btn btn-outline-info btn-sm mr-1" title="Editar" onclick='editarUsuarios(${element.id})'><i class="fa-solid fa-user-pen"></i></button>`
            : "";
        var btnB =
          element.status == "A"
            ? `<button type="button" class="btn btn-outline-danger btn-sm mr-1" title="Baja" onclick="statusUsuarios('B',${element.id},${idProyecto})"><i class="fa-solid fa-xmark"></i></button>`
            : "";
        var btnA =
          element.status == "B"
            ? `<button type="button" class="btn btn-outline-success btn-sm" onclick="statusUsuarios('A',${element.id},${idProyecto})" title="Alta"><i class="fa-solid fa-check"></i></button>`
            : "";
        var perfil = element.perfil == "A" ? "Admin" : "Estándar";

        list += `<li class="list-group-item d-flex justify-content-between align-items-center">
                    Usuario: ${element.nameUser} <br> Perfil: ${perfil}
                    <span class="badge badge-pill">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 col-xl-12 justify-content-end d-flex mt-3">
                            ${btnE}
                            ${btnB}
                            ${btnA}
                        </div>
                    </span>
                </li>`;
      });
      $("#listUser").html(list);
    })
    .catch((error) => {
      console.log(error);
      Swal.fire({
        position: "center",
        icon: "error",
        title: "Ocurrió un error al obtener los usuarios",
        showConfirmButton: false,
        timer: 1500,
      });
    });
};

statusUsuarios = (status, id, idProyecto) => {
  var datos = [status, id];
  const apiUrl = `${rutaP}/data/v1/asb/status/usuarios/telemetria`;
  let mensaje =
    status == "A"
      ? "Alta efectuada correctamente"
      : "Baja efectuada correctamente";
  consultarUrl(apiUrl, datos, "PUT")
    .then((datos) => {
      Swal.fire({
        position: "center",
        icon: "success",
        title: mensaje,
        showConfirmButton: false,
        timer: 1500,
      });
      listaUsuarios(idProyecto);
    })
    .catch((error) => {
      Swal.fire({
        position: "center",
        icon: "error",
        title: "Ocurrió un error al guardar",
        showConfirmButton: false,
        timer: 1500,
      });
    });
};
editarUsuarios = (id) => {
  const apiUrl = `${rutaP}/data/v1/asb/datos/usuarios/telemetria/${id}`;
  consultarGet(apiUrl)
    .then((datos) => {
      var valores = datos.data[0];
      $("#userIdT").val(id);
      $("#userT").val(valores.nameUser);
      $("#perfilUserT").val(valores.perfil);
    })
    .catch((error) => {
      Swal.fire({
        position: "center",
        icon: "error",
        title: "Ocurrió un error al consultar la información",
        showConfirmButton: false,
        timer: 1500,
      });
    });
};
consultarGet = async (url) => {
  console.log("Url de consulta", url);
  try {
    const respuesta = await fetch(url);

    if (!respuesta.ok) {
      throw new Error(
        `Error en la solicitud a la API. Código: ${respuesta.status}`
      );
    }

    const datos = await respuesta.json();
    return datos;
  } catch (error) {
    console.error(`Error al consultar la API: ${error.message}`);
    throw error;
  }
};

consultarUrl = async (url, datosDeEnvio, method) => {
  try {
    const respuesta = await fetch(url, {
      method: method,
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(datosDeEnvio),
    });
    if (!respuesta.ok) {
      throw new Error(
        `Error en la solicitud a la API. Código: ${respuesta.status}`
      );
    }
    const datos = await respuesta.json();
    return datos;
  } catch (error) {
    throw error;
  }
};

$("#btnLimpiarP").click(function () {
  $("#muestra").html("");
});
