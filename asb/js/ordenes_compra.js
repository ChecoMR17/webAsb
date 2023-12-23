let Tabla_OC;
let Tabla_MP;
let Tabla_P;
$(document).ready(() => {
  $("#Form_Compras").on("submit", function (e) {
    Guardar_OC(e);
  });

  $("#Form_Material_Pendiente").on("submit", function (e) {
    Guardar_MP(e);
  });

  Mostrar_OT();
  Mostrar_Proveedores();
  Mostrar_Tabla_OC();
});

let Guardar_MP = (e) => {
  e.preventDefault();
  Save_Data = [];
  Num_ot = $("#Num_ot").val();
  Id = $("#Id_OCT").val();
  $("#Tbl_MPendiente tr").each(function () {
    $(this)
      .find('input[type="checkbox"]')
      .each(function () {
        if ($(this).is(":checked")) {
          Save_Data.push($(this).val());
        }
      });
  });

  if (Save_Data.length > 0) {
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
            "../Archivos/Ordenes/compras.php?op=Guardar_MP",
            { Save_Data, Num_ot, Id },
            (result) => {
              console.log(result);
              if (result == 200) {
                Swal.fire({
                  position: "center",
                  icon: "success",
                  title: "¡Guardado!",
                  showConfirmButton: false,
                  timer: 2000,
                });
                $("#Btn_MP").click();
                Tabla_OC.ajax.reload();
                Tabla_MP.ajax.reload();
              } else {
                Swal.fire({
                  position: "center",
                  icon: "warning",
                  title:
                    "¡Podrían haber materiales que no se ingresaron correctamente a partidas!",
                  showConfirmButton: false,
                  timer: 2000,
                });
                $("#Btn_MP").click();
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
          timer: 2000,
        });
      }
    });
  } else {
    Swal.fire({
      position: "center",
      icon: "warning",
      title:
        "¡No se puede guardar ya que no se encontró ningún material seleccionado!",
      showConfirmButton: false,
      timer: 2000,
    });
  }
};

let Guardar_OC = (e) => {
  e.preventDefault();
  let data = new FormData($("#Form_Compras")[0]);
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
          url: "../Archivos/Ordenes/compras.php?op=Guardar_OC",
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
              $("#Btn_Limpiar").click();
              Tabla_OC.ajax.reload();
            } else {
              Swal.fire({
                position: "center",
                icon: "error",
                title: "¡Error, Inténtalo más tarde!",
                showConfirmButton: false,
                timer: 2000,
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
};

let Datos_Modificacion = (Id) => {
  console.log("Modificación " + Id);
  $.post(
    "../Archivos/Ordenes/compras.php?op=Datos_Modificacion",
    { Id },
    (result) => {
      result = JSON.parse(result);
      console.log(result);
      $("#Id").val(Id);
      $("#Proveedor").val(result.Id_Prov);
      $("#Proveedor").selectpicker("refresh");
      $("#F_Pago").val(result.Form_Pago);
      $("#F_Pago").selectpicker("refresh");
      $("#Fec_Ent").val(result.Fec_Ent);
      result.Descuento > 0
        ? $("#Descuento").val("SI")
        : $("#Descuento").val("NO");
      $("#Descuento").selectpicker("refresh");
      result.Descuento = result.Descuento > 0 ? result.Descuento : "";
      $("#P_Descuento").val(result.Descuento);
      $("#Observaciones").val(result.Obs);
      Buscar_Sucursales();
      Validar_D();
      setTimeout(() => {
        $("#Sucursal").val(result.Cons_Suc);
        $("#Cuenta").val(result.Cons_Cta);
        $("#Sucursal").selectpicker("refresh");
        $("#Cuenta").selectpicker("refresh");
      }, 250);
    }
  );
};
let Mostrar_Tabla_OC = () => {
  Num_ot = $("#Num_ot").val();
  Tabla_OC = $("#Tbl_OC")
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
        url: "../Archivos/Ordenes/compras.php?op=Mostrar_Tabla_OC",
        type: "post",
        dataType: "json",
        data: { Num_ot },
        error: (e) => {
          console.log("Error función listar() \n" + e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 100,
      order: [[0, "desc"]],
    })
    .DataTable();
};

let Mostrar_Tabla_MPendientes = () => {
  Num_ot = $("#Num_ot").val();
  Id = $("#Id_OCT").val();
  setTimeout(() => {
    Tabla_MP = $("#Tbl_MPendiente")
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
          url: "../Archivos/Ordenes/compras.php?op=Mostrar_Tabla_MPendientes",
          type: "post",
          dataType: "json",
          data: { Num_ot, Id },
          error: (e) => {
            console.log("Error función listar() \n" + e.responseText);
          },
        },
        bDestroy: true,
        iDisplayLength: 100,
        order: [[0, "desc"]],
      })
      .DataTable();
  }, 250);
};

let Mostrar_Tabla_Parciales = () => {
  Num_ot = $("#Num_ot").val();
  Id = $("#Id_OCT").val();
  setTimeout(() => {
    Tabla_P = $("#Tbl_Parciales")
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
          url: "../Archivos/Ordenes/compras.php?op=Mostrar_Tabla_Parciales",
          type: "post",
          dataType: "json",
          data: { Num_ot, Id },
          error: (e) => {
            console.log("Error función listar() \n" + e.responseText);
          },
        },
        bDestroy: true,
        iDisplayLength: 100,
        order: [[0, "desc"]],
      })
      .DataTable();
  }, 250);
};

let Mostar_Datos = (Id) => {
  $("#Id_OCT").val(Id);
  Mostrar_Tabla_MPendientes();
};
let Mostrar_OT = () => {
  $.post("../Archivos/Ordenes/Inventario.php?op=Mostrar_OT", (result) => {
    $("#Num_ot").html(result);
    $("#Num_ot").selectpicker("refresh");
  });
};

let Mostrar_Proveedores = () => {
  $.post("../Archivos/Ordenes/compras.php?op=Mostrar_Proveedores", (result) => {
    $("#Proveedor").html(result);
    $("#Proveedor").selectpicker("refresh");
  });
};

let Buscar_Sucursales = () => {
  Id = $("#Proveedor").val();
  $.post(
    "../Archivos/Ordenes/compras.php?op=Buscar_Sucursales",
    { Id },
    (result) => {
      $("#Sucursal").html(result);
      $("#Sucursal").selectpicker("refresh");
      Buscar_Cuentas();
    }
  );
};

let Buscar_Cuentas = () => {
  Id = $("#Proveedor").val();
  $.post(
    "../Archivos/Ordenes/compras.php?op=Buscar_Cuentas",
    { Id },
    (result) => {
      $("#Cuenta").html(result);
      $("#Cuenta").selectpicker("refresh");
    }
  );
};

let Validar_D = () => {
  if ($("#Descuento").val() == "SI") {
    $("#P_Descuento").attr("readonly", false);
    $("#P_Descuento").attr("required", true);
  } else {
    $("#P_Descuento").val("");
    $("#P_Descuento").attr("readonly", true);
    $("#P_Descuento").attr("required", false);
  }
};

$("#Btn_Limpiar").click(function () {
  $("#Id").val("");
  $("#Proveedor").val("");
  $("#Sucursal").html("");
  $("#Cuenta").html("");
  $("#F_Pago").val("");
  $("#Fec_Ent").val("");
  $("#Descuento").val("");
  $("#P_Descuento").val("");
  $("#Observaciones").val("");
  $("#Proveedor").selectpicker("refresh");
  $("#Sucursal").selectpicker("refresh");
  $("#Cuenta").selectpicker("refresh");
  $("#F_Pago").selectpicker("refresh");
  $("#Descuento").selectpicker("refresh");
});

$(document).on("keyup", ".cantidad_material", function (e) {
  e.preventDefault();
  Cantidad = parseFloat(
    limpiarT($(this).parents("tr").find("#Cantidad_M").val())
  );
  Precio = parseFloat(limpiarT($(this).parents("tr").find("#Precio_M").val()));
  Id = parseInt(limpiarT($(this).parents("tr").find("#Id_mat_p").html()));
  // Buscamos el valor maximo de este material solicitado y asi poder valida
  $.post(
    "../Archivos/Ordenes/compras.php?op=Validar_Cant",
    { Id },
    (result) => {
      //console.log(result);
      // Validamos que la cantidad se ha mayor a 0
      setTimeout(() => {
        if (Cantidad > 0) {
          if (Cantidad > result) {
            $("#Div_Alert").attr("hidden", false);
            $("#alert_parciales").text("El valor máximo admitido es " + result);
            $(this).val(result);
          } else {
            $("#Div_Alert").attr("hidden", true);
            $("#alert_parciales").text("");
            // Actualizamos la nueva cantidad
            $.post(
              "../Archivos/Ordenes/compras.php?op=Actualizar_Cant",
              { Cantidad, Precio, Id },
              (Actualizar) => {
                //console.log(Actualizar);
              }
            );
          }
        } else {
          $("#Div_Alert").attr("hidden", false);
          $("#alert_parciales").text("El valor mínimo admitido es 1");
          $(this).val(result);
        }
      }, 1500);
    }
  );
});

let Autorizar_OC = (Id) => {
  Swal.fire({
    title: "¿Estás seguro(a) de autorizar la OC?",
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
          "../Archivos/Ordenes/compras.php?op=Autorizar_OC",
          { Id },
          (result) => {
            console.log(result);
            if (result == 200) {
              Swal.fire({
                position: "center",
                icon: "success",
                title: "Autorizado!",
                showConfirmButton: false,
                timer: 2500,
              });
              Tabla_OC.ajax.reload();
            } else {
              Swal.fire({
                position: "center",
                icon: "error",
                title: "¡Error, Inténtalo más tarde!",
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
        timer: 2000,
      });
    }
  });
};

let Cancelar_OC = (Id) => {
  Swal.fire({
    title: "¿Estás seguro(a) de cancelar la OC?",
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
          "../Archivos/Ordenes/compras.php?op=Cancelar_OC",
          { Id },
          (result) => {
            console.log(result);
            if (result == 200) {
              Swal.fire({
                position: "center",
                icon: "success",
                title: "Cancelado!",
                showConfirmButton: false,
                timer: 2500,
              });
              Tabla_OC.ajax.reload();
            } else {
              Swal.fire({
                position: "center",
                icon: "error",
                title: "¡Error, Inténtalo más tarde!",
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
        timer: 2000,
      });
    }
  });
};

let Eliminar_MP = (Id) => {
  Swal.fire({
    title: "¿Estás seguro(a) de eliminar el material de la OC?",
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
          "../Archivos/Ordenes/compras.php?op=Eliminar_MP",
          { Id },
          (result) => {
            console.log(result);
            if (result == 200) {
              Swal.fire({
                position: "center",
                icon: "success",
                title: "¡Eliminado!",
                showConfirmButton: false,
                timer: 2500,
              });
              Tabla_OC.ajax.reload();
              Tabla_P.ajax.reload();
              Tabla_MP.ajax.reload();
            } else {
              Swal.fire({
                position: "center",
                icon: "error",
                title: "¡Error, Inténtalo más tarde!",
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
        timer: 2000,
      });
    }
  });
};

let limpiarT = (text) => {
  let limpio = text.replace("<br>", "");
  return limpio;
};

function filterFloat(evt, input) {
  var key = window.Event ? evt.which : evt.keyCode;
  var chark = String.fromCharCode(key);
  var tempValue = input.value + chark;
  if (key >= 48 && key <= 57) {
    if (filter(tempValue) === false) {
      return false;
    } else {
      return true;
    }
  } else {
    if (key == 8 || key == 13 || key == 0) {
      return true;
    } else if (key == 46) {
      if (filter(tempValue) === false) {
        return false;
      } else {
        return true;
      }
    } else {
      return false;
    }
  }
}
function filter(__val__) {
  var preg = /^([0-9]+\.?[0-9]{0,5})$/;
  if (preg.test(__val__) === true) {
    return true;
  } else {
    return false;
  }
}
