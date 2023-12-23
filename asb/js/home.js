let Tbl_OT;
$(document).ready(() => {
  Mostrar_Lista_OT();
});

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
        url: "../Archivos/home/operaciones.php?op=Mostrar_Lista_OT",
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
