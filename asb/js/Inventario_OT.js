let Tabla_Inventario;
let Tabla_IS;
$(document).ready(() => {
  $("#Form_Inventario").on("submit", function (e) {
    Guardar_Materiales(e);
  });

  $("#Form_Inventario_S").on("submit", function (e) {
    Guardar_IS(e);
  });

  Mostrar_OT();
  Mostrar_Materiales();
  Mostrar_Tabla_Inventario();
});

let Guardar_IS = (e) => {
  e.preventDefault();
  let data = new FormData($("#Form_Inventario_S")[0]);
  //console.log(data);
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
          url: "../Archivos/Ordenes/Inventario.php?op=Guardar_IS",
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
              $("#Btn_LimMS").click();
              Tabla_IS.ajax.reload();
            } else if (result == 201) {
              Swal.fire({
                position: "center",
                icon: "error",
                title: "¡Error, Inténtalo más tarde!",
                showConfirmButton: false,
                timer: 1500,
              });
            } else if (result == 202) {
              Swal.fire({
                position: "center",
                icon: "warning",
                title: "¡El material que intenta ingresar ya existe,!",
                text: "Si necesita actualizar la cantidad presione el botón de editar",
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
        timer: 1500,
      });
    }
  });
};

let Guardar_Materiales = (e) => {
  e.preventDefault();
  let data = new FormData($("#Form_Inventario")[0]);
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
          url: "../Archivos/Ordenes/Inventario.php?op=Guardar_Inventario",
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
              Tabla_Inventario.ajax.reload();
              $("#Btn_Limpiar").click();
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

let Mostrar_Tabla_Inventario = () => {
  Num_ot = $("#Num_ot").val();
  console.log(Num_ot);
  Tabla_Inventario = $("#Tbl_Inventario")
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
        url: "../Archivos/Ordenes/Inventario.php?op=Mostrar_Tabla_Inventario",
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

let Mostrar_Tabla_Inventario_S = () => {
  setTimeout(() => {
    Tabla_IS = $("#Tbl_Inventario_S")
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
          url: "../Archivos/Ordenes/Inventario.php?op=Mostrar_Tabla_Inventario_S",
          type: "post",
          dataType: "json",
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

let Datos_IS = (Id_MS) => {
  $.post(
    "../Archivos/Ordenes/Inventario.php?op=Datos_IS",
    { Id_MS },
    (result) => {
      result = JSON.parse(result);
      $("#Id_MS").val(Id_MS);
      $("#Id_MaterialS").val(result.Id_Material);
      $("#Cantidad_MS").val(result.Cantidad);
      $("#Id_MaterialS").selectpicker("refresh");
      console.log(result);
    }
  );
};
let Mostrar_OT = () => {
  $.post("../Archivos/Ordenes/Inventario.php?op=Mostrar_OT", (result) => {
    $("#Num_ot").html(result);
    $("#Num_ot").selectpicker("refresh");
  });
};

let Mostrar_Materiales = () => {
  $.post(
    "../Archivos/Ordenes/Inventario.php?op=Mostrar_Materiales",
    (result) => {
      $("#Id_Material").html(result);
      $("#Id_MaterialS").html(result);
      $("#Id_MaterialS").selectpicker("refresh");
      $("#Id_Material").selectpicker("refresh");
    }
  );
};

let Autorizar_Material = (Id_Material) => {
  Swal.fire({
    title: "¿Estás seguro(a) de autorizar el material?",
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
          "../Archivos/Ordenes/Inventario.php?op=Autorizar_Material",
          { Id_Material },
          (result) => {
            console.log(result);
            if (result == 200) {
              Swal.fire({
                position: "center",
                icon: "success",
                title: "¡Guardado!",
                showConfirmButton: false,
                timer: 2500,
              });
              Tabla_Inventario.ajax.reload();
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

let Cancelar_Material = (Id_Material) => {
  Swal.fire({
    title: "¿Estás seguro(a) de cancelar el material?",
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
          "../Archivos/Ordenes/Inventario.php?op=Cancelar_Material",
          { Id_Material },
          (result) => {
            if (result == 200) {
              Swal.fire({
                position: "center",
                icon: "success",
                title: "¡Guardado!",
                showConfirmButton: false,
                timer: 2500,
              });
              Tabla_Inventario.ajax.reload();
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

$("#Btn_Limpiar").click(function () {
  $(".Form_Limp").val("");
  $("#Id_Material").selectpicker("refresh");
});

$("#Btn_LimMS").click(function () {
  $("#Id_MS").val("");
  $("#Id_MaterialS").val("");
  $("#Cantidad_MS").val("");
  $("#Id_MaterialS").selectpicker("refresh");
});
