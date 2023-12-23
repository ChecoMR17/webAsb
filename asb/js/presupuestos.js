let tblMatrices = "";
let tblMatriz = "";
let tblTitulo = "";
let tblSubtitulos = "";
let tabMat = "";
let tblCat_Mat = ""; // tabla para catalogo de matreriales
let ClvAnt = ""; // Clave anterior de partida
let estado = ""; // status de la OT
let Material = ""; // Tipo de material (ING)
let Total = 0; // Total de presupuesto

// Eventos para visualización de la información
$("#divCte").click(function () {
  if ($(".divCte").attr("hidden") == undefined) {
    $(".divCte").attr("hidden", true);
  } else {
    $(".divCte").attr("hidden", false);
  }
});

$("#divCondcom").click(function () {
  if ($(".divCondcom").attr("hidden") == undefined) {
    $(".divCondcom").attr("hidden", true);
  } else {
    $(".divCondcom").attr("hidden", false);
  }
});

$("#divTit").click(function () {
  if ($(".divTit").attr("hidden") == undefined) {
    $(".divTit").attr("hidden", true);
  } else {
    $(".divTit").attr("hidden", false);
  }
});

let init = () => {
  // Formulario de presupuesto
  $("#formPres").on("submit", function (e) {
    guardar(e);
  });

  // Formulario para obtener cotizaciónes
  $("#formCotizar").on("submit", function (e) {
    guardarCotizacion(e);
  });

  // Formulario de títulos
  $("#formTitulos").on("submit", function (e) {
    guardarTitulo(e);
  });

  // Formulario de subtítulos
  $("#formSubTitulos").on("submit", function (e) {
    guardarSub(e);
  });

  // Formulario de Partidas
  $("#formPartidas").on("submit", function (e) {
    agregaPartida(e);
  });

  // Formulario modal Carga Manual
  $("#formMateriales").on("submit", function (e) {
    guardarMaterial(e);
  });

  // Formulario para copear una cotización
  $("#formCopyCotizacion").on("submit", function (e) {
    guardarCopyCotizacion(e);
  });

  $("#formExcel").on("submit", function (e) {
    guardarExcel(e);
  });

  limpiar(); // Vaciamos los campos
  ordenes(); // Listamos las ordenes de trabajo
  materiales(); // Listamos las claves de los materiales
};

// Funcion para ir arriba
$(".ir-arriba").click(function () {
  $("body, html").animate({ scrollTop: "0px" }, 500);
});

$(window).scroll(function () {
  if ($(this).scrollTop() > 0) {
    $(".ir-arriba").slideDown(300);
  } else {
    $(".ir-arriba").slideUp(300);
  }
});

// Boton limpir
$("#limpiar").click(function (e) {
  e.preventDefault();

  limpiar();
  $("#divSug").html("");
  $(".ir-arriba").click();
  $("#Num_OT").selectpicker("refresh");
});

// Boton para eliminar titulos, subtitulos, matrices y materiales
$("#btnDelete").click(function (e) {
  e.preventDefault();

  let Num_OT = $("#Num_OT").val();
  let Num_Cot = $("#Num_Cot").val();

  if (Num_Cot != "") {
    swal
      .fire({
        title: "Se eliminarán los titulos, subtitulos, matrices y materiales.",
        text: "",
        showCancelButton: true,
        confirmButtonColor: "#17a2b8",
        confirmButtonText: "Confirmar",
        cancelButtonText: "Cancelar",
        closeOnConfirm: false,
      })
      .then((result) => {
        if (result.isConfirmed) {
          $.post(
            "../Archivos/Presupuestos/pres_Servicios.php?op=deleteAll",
            { Num_Cot: Num_Cot },
            (data) => {
              data = JSON.parse(data);

              let msg = data.msg;
              let msg2 = data.msg2;

              if (msg.includes("correctamente")) {
                swal.fire(msg, "", "success");
                $("#limpiar").click();
              } else if (msg.includes("parcialmente")) {
                swal.fire(msg, msg2, "warning");
              } else {
                swal.fire(msg, msg2, "error");
              }
              updateCD(Num_Cot);

              setTimeout(() => {
                dataCotizacion(Num_Cot);
              }, 1000);
            }
          );
        }
      });
  }
});

/*--------------------------------------------------------------------------------------------------------------------------------------------\
 *                                                      Funciones para obtener una cotización existente                                       |
 *-------------------------------------------------------------------------------------------------------------------------------------------*/

$("#btnCotizar").click(function (e) {
  e.preventDefault();

  $.post("../Archivos/Presupuestos/pres_Servicios.php?op=otAut", (data) => {
    $("#OT").html(data);
    $("#OT").selectpicker("refresh");
  });
});

$("#OT").change(function (e) {
  e.preventDefault();

  let Num_OT = $(this).val();
  let Obra = $("#OT option:selected").data("subtext");
  $("#obr").html(Obra);

  // Obtenemos informacion de la Orden
  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=otData",
    { Num_OT },
    (data) => {
      $("#Obra2").val(data.Nom_Obra);
      $("#Cliente2").val(data.Nom_Cte);
      $("#Contacto2").val(data.Nom_Cont);
      $("#Proyecto2").val(data.Proyecto);
    },
    "json"
  );

  // Consultamos la cotizaciones autorizadas
  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=cot_Aut",
    { Num_OT },
    (data) => {
      $("#Cons2").html(data);
      $("#Cons2").change();
    }
  );
});

$("#Cons2").change(function (e) {
  e.preventDefault();

  let Cons = $(this).val();
  let Num_OT = $("#OT").val();

  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=Num_Cot",
    { Num_OT, Cons },
    (data) => {
      $("#NewCot").val(Num_Cot);

      // Obtenemos informacion de la cotización
      $.post(
        "../Archivos/Presupuestos/pres_Servicios.php?op=dataCotizacion",
        { Num_Cot },
        (data) => {
          $("#Imp_CD2").val(data.Imp_CD);
          $("#Imp_CI2").val(data.Imp_CI);
          $("#Imp_Fin2").val(data.Imp_Fin);
          $("#Imp_Util2").val(data.Imp_Util);
          $("#Por_Desc2").val(data.Por_Desc);
          $("#Imp_Desc2").html(data.Imp_Desc);
          $("#Imp_Otro2").val(data.Imp_Otro);
          $("#Fpago2").val(data.Fpago);
          $("#TiempoEnt2").val(data.TiempoEnt);
          $("#Vigencia2").val(data.Vigencia);
          $("#TotalCD2").val(data.TotalCD);
          $("#TotalIVA2").val(data.TotalIVA);
          $("#User2").val(data.User);
          $("#Nota2").val(data.Nota);
        },
        "json"
      );
    },
    "json"
  );
});

// Función para guardar cotizaciones
let guardarCotizacion = (e) => {
  e.preventDefault();

  let Cons = $("#Cons").val();
  let Num_OT = $("#Num_OT").val();
  let NewCot = $("#Num_Cot").val();
  let Num_Cot = $("#NewCot").val();

  swal
    .fire({
      title:
        "Se creará una nueva cotización en " +
        NewCot +
        " a partir de la cotización " +
        Num_Cot,
      text: "¡La informacion actual será reemplazada!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#10ABB4",
      confirmButtonText: "SI, ESTOY SEGURO",
      cancelButtonText: "NO, CANCELAR",
      closeOnConfirm: false,
      closeOnCancel: true,
    })
    .then((result) => {
      if (result.isConfirmed) {
        swal.fire({
          title:
            "<h3>Cargando información en " +
            NewCot +
            '</h3><br><img src="../../img/Cargando.gif" width="150"></img>',
          html: "",
          showCloseButton: false,
          showConfirmButton: false,
          showCancelButton: false,
        });

        // Primero eliminamos la informacion del presupuesto actual
        $.post("../Archivos/Presupuestos/pres_Servicios.php?op=deleteCot", {
          Num_Cot: NewCot,
        });

        // Despues cargamos la información de la cotización selecionada
        setTimeout(() => {
          $.post(
            "../Archivos/Presupuestos/pres_Servicios.php?op=cotizar",
            { Num_OT, Cons, NewCot },
            (data) => {
              console.log(data);
              if (data.includes("Se guardó")) {
                $("#Cotizar").modal("hide");
                updateCD(Num_Cot);
                swal.fire(data, "", "success");
              } else {
                swal.fire(data, "", "error");
              }
            }
          );
        }, 200);
      }
    });
};

// Se cierra el modal
$("#Cotizar").on("hidden.bs.modal", function () {
  $("#formCotizar .form-control").val("");
  $("#Cons2").html("");
  $("#obr").html("");
  // Recargamos la cotizacion actual
  let Num_Cot = $("#Num_Cot").val();

  matrices(Num_Cot); // Listamos matrices
  titulos(Num_Cot); // Listamos subtitulos
  dataCotizacion(Num_Cot); // Obtenemos informaciuon de la cotización
});

/*-------------------------------------------------------------------------------------------------------------------------------------------\
 *                                                      Funciones para formulario de cotizaciones                                             |
 *-------------------------------------------------------------------------------------------------------------------------------------------*/

$("#Num_OT").change(function (e) {
  e.preventDefault();

  let Num_OT = this.value;
  $("#cCotizacion").attr("hidden", false);

  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=otData",
    { Num_OT },
    (data) => {
      $("#Num_OT").val(Num_OT);
      $("#Obra").val(data.Nom_Obra);
      $("#Cliente").val(data.Nom_Cte);
      $("#Contacto").val(data.Nom_Cont);
      $("#Proyecto").val(data.Proyecto);
      $("#calle").html("Calle: <b>" + data.Calle_Cte + "</b>");
      $("#colonia").html("Colonia: <b>" + data.Colonia + "</b>");
      $("#poblacion").html("Población: <b>" + data.Poblacion + "</b>");
      $("#tel").html("Telefono: <b>" + data.Tel + "</b>");
      $("#correo").html("Correo: <b>" + data.Correo + "</b>");
      $("#cargarExcel").attr("hidden", false);
      $("#cargarExcel").attr("disabled", false);
      if (estado == "C") {
        swal.fire("Esta orden ha sido concluida", "", "info");
      }
    },
    "json"
  );

  // Obtenemos el numero de cotización
  numCot(Num_OT);
});

// Función para listar ordenes
let ordenes = () => {
  $.post("../Archivos/Presupuestos/pres_Servicios.php?op=Num_OT", (data) => {
    $("#Num_OT").html(data);
    $("#Num_OT").selectpicker("refresh");

    $("#OtCotizacion").html(data);
    $("#OtCotizacion").selectpicker("refresh");
  });
};

// Función para obtener el número de cotización
let numCot = (Num_OT) => {
  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=numCot",
    { Num_OT: Num_OT },
    (data) => {
      data = JSON.parse(data);

      $("#Num_Cot").val(data.Num_Cot);
      $("#Cons").html(data.NoCot);

      // Listamos las matrices si las hay
      matrices(data.Num_Cot);

      // Listamos los titulos
      titulos(data.Num_Cot);

      // Obtenemos los datos de la cotización
      dataCotizacion(data.Num_Cot);

      // Obtenemos sugerencias para titulos
      sugerencias(data.Num_Cot);
    }
  );
};

$("#Cons").change(function (e) {
  e.preventDefault();

  let Cons = $(this).val();
  let Num_OT = $("#Num_OT").val();

  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=Num_Cot",
    { Num_OT, Cons },
    (data) => {
      $("#Num_Cot").val(data);
      dataCotizacion(data);
      matrices(data);
      titulos(data);
      sugerencias(data);
    },
    "json"
  );
});

// Metodo para calular el descuento
$("#Por_Desc").keyup(function (e) {
  e.preventDefault();

  CalcullarDesc($(this).val());
});

$("#Por_Desc").change(function (e) {
  e.preventDefault();

  CalcullarDesc($(this).val());
});

let CalcullarDesc = (Descuento) => {
  if (Descuento > 0 && Total > 0) {
    Imp_Desc = Total * (Descuento / 100);
    Descuento = Total - Imp_Desc;
    IVA = Descuento * 1.16;
    $("#Imp_Desc").html("$" + Imp_Desc.toLocaleString());
    $("#TotalCD").val("$" + Descuento.toLocaleString());
    $("#TotalIVA").val("$" + IVA.toLocaleString());
  } else {
    IVA = Total * 1.16;
    $("#Imp_Desc").html("$0.00");
    $("#TotalCD").val("$" + Total.toLocaleString());
    $("#TotalIVA").val("$" + IVA.toLocaleString());
  }
};

// Obtenemos los datos de la cotización
let dataCotizacion = (Num_Cot) => {
  console.log("Buscar clave: ");
  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=dataCotizacion",
    { Num_Cot },
    (data) => {
      if (data != null) {
        $("#Imp_CD").val(data.Imp_CD);
        $("#Imp_CI").val(data.Imp_CI);
        $("#Imp_Fin").val(data.Imp_Fin);
        $("#Imp_Util").val(data.Imp_Util);
        $("#Por_Desc").val(data.Por_Desc);
        $("#Imp_Desc").html(data.Imp_Desc);
        $("#Imp_Otro").val(data.Imp_Otro);
        $("#Status").val(data.Status);
        $("#Fpago").val(data.Fpago);
        $("#TiempoEnt").val(data.TiempoEnt);
        $("#Vigencia").val(data.Vigencia);
        $("#Concep").val(data.Concepto);
        $("#Ubicacion").val(data.Ubicacion);

        $("#TotalCD").val(data.TotalCD);
        Total = data.Total;
        $("#TotalIVA").val(data.TotalIVA);

        $("#User").val(data.User);
        $("#U_Aut").html(data.Aut);
        $("#Obs").val(data.Obs);

        data.Calle == "S"
          ? $("#Calle").prop("checked", true)
          : $("#Calle").prop("checked", false);
        data.Colonia == "S"
          ? $("#Colonia").prop("checked", true)
          : $("#Colonia").prop("checked", false);
        data.Poblacion == "S"
          ? $("#Poblacion").prop("checked", true)
          : $("#Poblacion").prop("checked", false);
        data.Tel == "S"
          ? $("#Tel").prop("checked", true)
          : $("#Tel").prop("checked", false);
        data.Correo == "S"
          ? $("#Correo").prop("checked", true)
          : $("#Correo").prop("checked", false);
        data.Ret == "S"
          ? $("#Ret").prop("checked", true)
          : $("#Ret").prop("checked", false);

        $("#Nota").val(data.Nota);

        $("#btnLiberar").attr("hidden", true);

        if (estado != "C") {
          // Orden no concluida
          if (data.Status == "En ejecución") {
            data.Imp_CD != "$0.00"
              ? $("#btnAutorizar").attr("hidden", false)
              : "";
            $("#btnNueva").attr("hidden", true);
            $("#guardarPres").attr("disabled", false);
            $("#GuardarPartida").attr("disabled", false);
            $("#Ing-tab").attr("hidden", false);
            $("#Srv-tab").attr("hidden", false);
            $("#GuardarTitulo").attr("disabled", false);
            $("#GuardarSubTitulo").attr("disabled", false);
            $("#guardarMat").attr("disabled", false);
            $("#btnExcel").attr("disabled", false);
            $("#btnExistente").attr("disabled", false);
            $("#Ingenieria-tab").attr("hidden", false);
            $("#guardarIngMat").attr("disabled", false);
            $("#Xls-tab").attr("hidden", false);
            $("#btnDelete").attr("disabled", false);
            $("#btnDesaut").attr("disabled", true);
            $("#btnCotizar").attr("disabled", false);
          } else {
            // Autorizacion Cte     Reparaciones    Insumos
            if (estado == "U" || estado == "R" || estado == "I") {
              $("#btnLiberar").attr("hidden", false);
            } else {
              $("#btnLiberar").attr("hidden", true);
            }

            $("#btnAutorizar").attr("hidden", true);
            $("#btnNueva").attr("hidden", false);
            $("#guardarPres").attr("disabled", true);
            $("#GuardarPartida").attr("disabled", true);
            $("#Ing-tab").attr("hidden", true);
            $("#Srv-tab").attr("hidden", true);
            $("#GuardarTitulo").attr("disabled", true);
            $("#GuardarSubTitulo").attr("disabled", true);
            $("#guardarMat").attr("disabled", true);
            $("#btnExcel").attr("disabled", true);
            $("#btnExistente").attr("disabled", true);
            $("#Ingenieria-tab").attr("hidden", true);
            $("#guardarIngMat").attr("disabled", true);
            $("#Xls-tab").attr("hidden", true);
            $("#btnDelete").attr("disabled", true);
            $("#btnDesaut").attr("disabled", false);
            $("#btnCotizar").attr("disabled", true);
          }
        } else {
          // Orden concluida
          $("#btnAutorizar").attr("hidden", true);
          $("#btnNueva").attr("hidden", true);
          $("#guardarPres").attr("disabled", true);
          $("#GuardarPartida").attr("disabled", true);
          $("#Ing-tab").attr("hidden", true);
          $("#Srv-tab").attr("hidden", true);
          $("#GuardarTitulo").attr("disabled", true);
          $("#GuardarSubTitulo").attr("disabled", true);
          $("#guardarMat").attr("disabled", true);
          $("#btnExcel").attr("disabled", true);
          $("#btnExistente").attr("disabled", true);
          $("#guardarPres").attr("disabled", true);
          $("#Ingenieria-tab").attr("hidden", true);
          $("#guardarIngMat").attr("disabled", true);
          $("#Xls-tab").attr("hidden", true);
          $("#btnDesaut").attr("disabled", true);
          $("#btnCotizar").attr("disabled", true);
        }
        // Obtenemos la clve para el siguiente titulo
        $.post(
          "../Archivos/Presupuestos/pres_Servicios.php?op=claveTitulo",
          { Num_Cot },
          (data) => {
            $("#Clave").val(data.Clave);
            $("#Titulo").val(data.Titulo);
          },
          "json"
        );
      } else {
        $("#Imp_CD").val("");
        $("#Imp_CI").val("");
        $("#Imp_Fin").val("");
        $("#Imp_Util").val("");
        $("#Imp_Otro").val("");
        $("#Status").val("");
        $("#Fpago").val("");
        $("#TiempoEnt").val("");
        $("#Vigencia").val("");
        $("#Concep").val("");
        $("#Ubicacion").val("");
        $("#TotalCD").val("");
        $("#TotalIVA").val("");
        $("#User").val("");
        $("#Obs").val("");
        $(".form-check-input").prop("checked", true);
        $("#Ret").prop("checked", false);

        if (estado == "C") {
          $("#btnAutorizar").attr("hidden", true);
          $("#btnNueva").attr("hidden", true);
          $("#guardarPres").attr("disabled", true);
          $("#GuardarPartida").attr("disabled", true);
          $("#Ing-tab").attr("hidden", true);
          $("#Srv-tab").attr("hidden", true);
          $("#GuardarTitulo").attr("disabled", true);
          $("#GuardarSubTitulo").attr("disabled", true);
          $("#guardarMat").attr("disabled", true);
          $("#btnExcel").attr("disabled", true);
          $("#btnExistente").attr("disabled", true);
          $("#Ingenieria-tab").attr("hidden", true);
          $("#guardarIngMat").attr("disabled", true);
          $("#Xls-tab").attr("hidden", true);
          $("#btnDesaut").attr("disabled", true);
          $("#btnCotizar").attr("disabled", true);
        } else {
          $("#guardarPres").attr("disabled", false);
          $("#btnAutorizar").attr("hidden", true);
          $("#btnNueva").attr("hidden", true);
          $("#GuardarPartida").attr("disabled", true);
          $("#Ing-tab").attr("hidden", false);
          $("#Srv-tab").attr("hidden", false);
          $("#GuardarTitulo").attr("disabled", true);
          $("#GuardarSubTitulo").attr("disabled", true);
          $("#guardarIngMat").attr("disabled", true);
          $("#btnDesaut").attr("disabled", true);
          $("#btnCotizar").attr("disabled", false);
        }
      }
    },
    "json"
  );
};

// Funcion para listar las claves de los materiales
let materiales = () => {
  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=materiales",
    (data) => {
      $("#Cve_Mat").html(data);
      $("#Cve_Mat").selectpicker("refresh");
    }
  );

  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=listmateriales",
    (data) => {
      $("#ListCve").html(data);
    }
  );
};

// Datos mano de obra
let moData = (Cve) => {
  $("#Cantidad").val(1);
  $("#UMM").val("JOR");

  switch (Cve) {
    // PEON
    case "MO011":
      $("#PUM").val(459.68);
      break;
    // AYUDANTE GENERAL
    case "MO021":
      $("#PUM").val(475.71);
      break;
    // AYUDANTE ESPECIALIZADO
    case "MO031":
      $("#PUM").val(491.78);
      break;
    // OFICIAL ALBAÑIL
    case "MO041":
      $("#PUM").val(767.03);
      break;
    // OFICIAL FIERRERO
    case "MO051":
      $("#PUM").val(799.4);
      break;
    // OFICIAL CARPINTERO DE O. NEGRA
    case "MO052":
      $("#PUM").val(831.8);
      break;
    // OFICIAL PINTOR
    case "MO053":
      $("#PUM").val(742.74);
      break;
    // OFICIAL HERRERO
    case "MO061":
      $("#PUM").val(799.4);
      break;
    // OFICIAL YESERO
    case "MO062":
      $("#PUM").val(767.03);
      break;
    // OFICIAL AZULEJERO
    case "MO063":
      $("#PUM").val(767.04);
      break;
    // OFICIAL COLOCADOR
    case "MO064":
      $("#PUM").val(799.4);
      break;
    // OFICIAL BARNIZADOR
    case "MO065":
      $("#PUM").val(799.4);
      break;
    // OFICIAL VIDRIERO
    case "MO066":
      $("#PUM").val(742.74);
      break;
    // OPERADOR DE MAQUINARIA MENOR
    case "MO067":
      $("#PUM").val(588.88);
      break;
    // OFICIAL CARPINTERO DE O. BLANCA
    case "MO071":
      $("#PUM").val(831.8);
      break;
    // OFICIAL ALUMINIERO
    case "MO081":
      $("#PUM").val(831.8);
      break;
    // CABO DE OFICIOS
    case "MO082":
      $("#PUM").val(831.8);
      break;
    // OFICIAL PLOMERO
    case "MO083":
      $("#PUM").val(831.8);
      break;
    // OFICIAL ELECTRICISTA
    case "MO084":
      $("#PUM").val(831.8);
      break;
    // OFICIAL DE INSTALACIONES
    case "MO085":
      $("#PUM").val(831.8);
      break;
    // OFICIAL TUBERO
    case "MO086":
      $("#PUM").val(831.8);
      break;
    // OFICIAL SOLDADOR
    case "MO091":
      $("#PUM").val(896.58);
      break;
    // TOPOGRAFO
    case "MO092":
      $("#PUM").val(896.58);
      break;
    // OPERADOR DE MAQUINARIA PESADA
    case "MO093":
      $("#PUM").val(864.19);
      break;
    // SOBRESTANTE
    case "MO094":
      $("#PUM").val(896.58);
      break;
    // TECNICO ESPECIALIZADO
    case "MO111":
      $("#PUM").val(644.76);
      break;
    // INGENIERO PROYECTISTA
    case "MO001-5":
      $("#PUM").val(857.21);
      break;
    // SUPERVISOR
    case "MO112":
      $("#PUM").val(399.97);
      break;
    //AYUDANTE DE EQUIPO Y MAQUINARIA
    case "MO-MAQ-01-2":
      $("#PUM").val(491.78);
      break;
    // PEON (URBANIZACION)
    case "MO-URB-01-2":
      $("#PUM").val(459.68);
      break;
    // AYUDANTE GENERAL (URBANIZACION)
    case "MO-URB-02-2":
      $("#PUM").val(491.76);
      break;
    // AYUDANTE ESPECIALIZADO (URBANIZACION)
    case "MO-URB-03-2":
      $("#PUM").val(491.78);
      break;
    // OFICIAL ALBAÑIL (URBANIZACION)
    case "MO-URB-04-2":
      $("#PUM").val(767.03);
      break;
    // MANDO INTERMEDIO (URBANIZACION)
    case "MO-URB-10-2":
      $("#PUM").val(831.8);
      break;
    // TOPOGRAFO (URBANIZACION)
    case "MO-URB-11-2":
      $("#PUM").val(896.58);
      break;
  }
};

// Modificacmos el concepto
$("#Pref").keyup(function (e) {
  e.preventDefault();

  if ($(this).val() != "" && $("#Cve").val() != null) {
    // Cambiamos la descripción
    $("#Descripcion").val(tipo);
  }
});

// Obtenemos el concepto y la unidad de medida
$("#Cve").change(function (e) {
  e.preventDefault();
  let tipo = prefijos($("#Pref").val());

  // Obtenemos la unidad de medida y el precio unitario
  let Cve = $(this).val();
  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=mat",
    { Cve },
    (data) => {
      $("#UM").val(data.Desc_UM);
      $("#Descripcion").val(tipo + data.Desc_Mat);
    },
    "json"
  );
});

// prefijos
let prefijos = (prefijo) => {
  let pref = "";

  switch (prefijo) {
    case "I":
      pref = "INSTALACIÓN DE ";
      break;
    case "S":
      pref = "SUMINISTRO DE ";
      break;
    case "R":
      pref = "REPARACIÓN DE ";
      break;
    case "X":
      pref = "SUMINISTRO E INSTALACIÓN DE ";
      break;
    default:
      pref = "SUMINISTRO E INSTALACIÓN DE ";
      break;
  }

  return pref;
};

// Función para vaciar campos
let limpiar = () => {
  $("#cCotizacion").attr("hidden", true);
  $(".form-control").val("");
  $("#Cons").html("");
  $("#calle").html("Calle:");
  $("#colonia").html("Colonia:");
  $("#poblacion").html("Población:");
  $("#tel").html("Telefono:");
  $("#correo").html("Correo:");
  $("#U_Aut").html("");
  $("#Obs").val("");
  $("#Pref").attr("disabled", false);
  $("#Cve").attr("disabled", false);

  $(".form-check-input").prop("checked", true);
  $("#Ret").prop("checked", false);

  $("#btnDelete").attr("disabled", true);
  $("#btnDesaut").attr("disabled", true);
  $("#btnAutorizar").attr("hidden", true);
  $("#btnCotizar").attr("disabled", true);
  $("#btnNueva").attr("hidden", true);
  $("#Imp_Desc").html("");

  $(".divCte").attr("hidden", false);
  $(".divCondcom").attr("hidden", false);
  $(".divTit").attr("hidden", false);

  estado = "";
  ClvAnt = "";
  Total = 0;

  //matrices(0);
  //titulos(0);
};

// Función para guardar cotizaciones
let guardar = (e) => {
  e.preventDefault();

  let data = new FormData($("#formPres")[0]);
  let Num_Cot = $("#Num_Cot").val();

  $.ajax({
    type: "post",
    url: "../Archivos/Presupuestos/pres_Servicios.php?op=guardar",
    data: data,
    contentType: false,
    processData: false,
    success: (resp) => {
      if (resp.includes("correctamente")) {
        swal.fire(resp, "", "success");
        dataCotizacion(Num_Cot);
      } else {
        swal.fire(resp, "", "error");
      }
    },
  });
};

/*--------------------------------------------------------------------------------------------------------------------------------------------\
 *                                                      Funciones para formulario de Títulos                                                  |
 *-------------------------------------------------------------------------------------------------------------------------------------------*/

// Función para listado de sugerencias
let sugerencias = (Num_Cot) => {
  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=sugerencias",
    { Num_Cot: Num_Cot },
    (data) => {
      $("#divSug").html(data);
    }
  );
};

let sug = (Titulo) => {
  let Sug = Titulo.innerHTML;

  $("#Titulo").val(Sug);
  Titulo.remove();
};

let guardarTitulo = (e) => {
  e.preventDefault();

  let data = new FormData($("#formTitulos")[0]);
  let Num_Cot = $("#Num_Cot").val();
  data.append("Num_Cot", Num_Cot);

  $.ajax({
    type: "post",
    url: "../Archivos/Presupuestos/pres_Servicios.php?op=guardarTitulo",
    data: data,
    contentType: false,
    processData: false,
    success: function (resp) {
      if (resp.includes("correctamente")) {
        swal.fire(resp, "", "success");
        tblTitulo.ajax.reload();
        dataCotizacion(Num_Cot);
        sugerencias(Num_Cot);
        $("#Titulo").val("");
        claves(Num_Cot);
      } else {
        swal.fire(resp, "", "error");
      }
    },
  });
};

let titulos = (Num_Cot) => {
  setTimeout(function () {
    tblTitulo = $("#tblTitulos")
      .dataTable({
        aProcessing: true, //Activamos el procesamiento del datatables
        aServerSide: true, //Paginación y filtrado realizados por el servidor
        dom: "Bfrtip", //Definimos los elementos del control de tabla
        buttons: [""],
        columnDefs: [{ width: "75%", targets: 1 }],
        ajax: {
          url: "../Archivos/Presupuestos/pres_Servicios.php?op=titulos",
          type: "post",
          dataType: "json",
          data: { Num_Cot },
        },
        bDestroy: true,
        iDisplayLength: 10, //Paginación
        order: [[0, "asc"]], //Ordenar (columna,orden)
      })
      .DataTable();
  }, 100);
};

// Listado de claves de titulos y subtitulos
let claves = (Num_Cot) => {
  // Subtitulos
  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=clavesSub",
    { Num_Cot },
    (data) => {
      $("#Clv").html(data);
    }
  );
};

// Función para editar titulos
let verTitulo = (Clave) => {
  $("#Clave").val(Clave);
  Num_Cot = $("#Num_Cot").val();

  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=verTitulo",
    { Num_Cot, Clave },
    (Titulo) => {
      $("#Titulo").val(Titulo);
      $("#SubtitulosLabel").html(Titulo);
    }
  );
};

// Función para borrar titulos
let borrarTitulo = (Num_Cot, Clave) => {
  swal
    .fire({
      title: "Se borrará el titulo selecionado",
      text: "Se borrarán los subtitulos para este título, incluyendo sus matrices y materiales",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#10ABB4",
      confirmButtonText: "SI, ESTOY SEGURO",
      cancelButtonText: "NO, CANCELAR",
      closeOnConfirm: false,
      closeOnCancel: true,
    })
    .then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../Archivos/Presupuestos/pres_Servicios.php?op=borrarTitulo",
          { Num_Cot: Num_Cot, Clave: Clave },
          (data) => {
            if (data.includes("correctamente")) {
              dataCotizacion(Num_Cot);
              tblTitulo.ajax.reload();
              tblMatrices.ajax.reload();
              swal.fire(data, "", "success");
              sugerencias(Num_Cot);
            } else {
              swal.fire(data, "", "error");
            }
          }
        );
      }
    });
};

/*-------------------------------------------------------------------------------------------------------------------------------------------\
 *                                               Funciones para formulario de Subtítulos                                                       |
 *-------------------------------------------------------------------------------------------------------------------------------------------*/

// Función para guardar dubtitulos
let guardarSub = (e) => {
  e.preventDefault();

  let Num_Cot = $("#Num_Cot").val();
  let Clave = $("#Clave").val();
  let Clv = $("#Clave1").val();
  let Subtitulo = $("#Subtitulo").val();

  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=guardarSub",
    { Num_Cot, Clave, Clv, Subtitulo },
    (data) => {
      if (data.includes("correctamente")) {
        swal.fire(data, "", "success");
        tblSubtitulos.ajax.reload();
        $("#Subtitulo").val("");
        claveSub(Num_Cot, Clave);
      } else {
        swal.fire(data, "", "error");
      }
    }
  );
};

// Función para listado de subtitulos
let subtitulos = (Num_Cot, Clave, Titulo) => {
  verTitulo(Clave);

  setTimeout(function () {
    tblSubtitulos = $("#tblSubtitulos")
      .dataTable({
        aProcessing: true, //Activamos el procesamiento del datatables
        aServerSide: true, //Paginación y filtrado realizados por el servidor
        dom: "Bfrtip", //Definimos los elementos del control de tabla
        buttons: [],
        columnDefs: [{ width: "85%", targets: 1 }],
        ajax: {
          url: "../Archivos/Presupuestos/pres_Servicios.php?op=subtitulos",
          type: "post",
          dataType: "json",
          data: { Num_Cot, Clave },
        },
        bDestroy: true,
        iDisplayLength: 100, //Paginación
        order: [[0, "asc"]], //Ordenar (columna,orden)
      })
      .DataTable();
  }, 250);
};

// Función para editar subtitulos
let verSubtitulo = (Clv) => {
  $("#Clave1").val(Clv);
  Num_Cot = $("#Num_Cot").val();

  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=verSubtitulo",
    { Num_Cot: Num_Cot, Clv: Clv },
    (Subtitulo) => {
      $("#Subtitulo").val(Subtitulo);
    }
  );
};

// Función para borrar titulos
let borrarSubtitulo = (Num_Cot, Clave, Clv) => {
  swal
    .fire({
      title:
        "Se borrará el subtítulotitulo selecionado, incluyendo matrices y materiales",
      text: "",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#10ABB4",
      confirmButtonText: "SI, ESTOY SEGURO",
      cancelButtonText: "NO, CANCELAR",
      closeOnConfirm: false,
      closeOnCancel: true,
    })
    .then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../Archivos/Presupuestos/pres_Servicios.php?op=borrarSubtitulo",
          { Num_Cot, Clave, Clv },
          (data) => {
            if (data.includes("correctamente")) {
              tblSubtitulos.ajax.reload();
              tblMatrices.ajax.reload();
              swal.fire(data, "", "success");
              claveSub(Num_Cot, Clave);
            } else {
              swal.fire(data, "", "error");
            }
          }
        );
      }
    });
};

// Función para obtener la clave del subtitulo
let claveSub = (Num_Cot, Clave) => {
  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=claveSub",
    { Num_Cot, Clave },
    (data) => {
      $("#Clave1").val(data);
    }
  );
};

// Se muestra el modal Subtitulos
$("#Subtitulos").on("shown.bs.modal", function () {
  let Clave = $("#Clave").val();
  let Num_Cot = $("#Num_Cot").val();
  claveSub(Num_Cot, Clave);
});

// Se cierra el modal Subtitulos
$("#Subtitulos").on("hidden.bs.modal", function () {
  dataCotizacion($("#Num_Cot").val());
  claves($("#Num_Cot").val());
  $("#Titulo").val("");
});

/*-------------------------------------------------------------------------------------------------------------------------------------------\
 *                                                 Funciones para agregar matrices por excel                                                  |
 *-------------------------------------------------------------------------------------------------------------------------------------------*/
$("#Nuevo-tab").click(function (e) {
  e.preventDefault();

  $("#Archivo").val("");
});

let inserMatrices = (e) => {
  e.preventDefault();

  swal.fire({
    title:
      '<h3>Cargando información...</h3><br><img src="../../img/Cargando.gif" width="150"></img>',
    html: "Html",
    showCloseButton: false,
    showConfirmButton: false,
    showCancelButton: false,
  });

  let Num_Cot = $("#Num_Cot").val();
  let data = new FormData($("#formXls")[0]);
  data.append("Num_Cot", Num_Cot);

  $.ajax({
    type: "post",
    url: "../Archivos/Presupuestos/inserMatrices.php",
    data: data,
    processData: false,
    contentType: false,
    success: (resp) => {
      resp = JSON.parse(resp);
      if (resp.msg.includes("correctamente")) {
        swal.fire(resp.msg, resp.msg2, "success");
        $("#Archivo").val("");

        updateCD(Num_Cot);
        setTimeout(() => {
          dataCotizacion(Num_Cot);
        }, 100);

        if (resp.Cve != null && resp.Cve != "") {
          Cve = resp.Cve;
          Cant = resp.Cant;
          setTimeout(function () {
            window.open(
              "../Archivos/Presupuestos/excel_MatPendiente.php?Num_Cot=" +
                btoa(Num_Cot) +
                "&Cod=" +
                btoa(Cod) +
                "&Cve=" +
                btoa(Cve) +
                "&Cant=" +
                btoa(Cant),
              "_blank"
            );
          }, 1200);
        }
      } else {
        swal.fire(resp.msg, resp.msg2, "error");
        if (resp.Cve != null && resp.Cve != "") {
          Cve = resp.Cve;
          Cant = resp.Cant;
          setTimeout(function () {
            window.open(
              "../Archivos/Presupuestos/excel_MatPendiente.php?Num_Cot=" +
                btoa(Num_Cot) +
                "&Cod=" +
                btoa(Cod) +
                "&Cve=" +
                btoa(Cve) +
                "&Cant=" +
                btoa(Cant),
              "_blank"
            );
          }, 1200);
        }
      }

      // Recargamos las tablas
      tblMatrices.ajax.reload();
      tblTitulo.ajax.reload();
    },
  });
};

/*-------------------------------------------------------------------------------------------------------------------------------------------\
 *                                          Funciones para agregar nuevos materiales al catalogo                                              |
 *-------------------------------------------------------------------------------------------------------------------------------------------*/

$("#Nuevo-tab").click(function (e) {
  e.preventDefault();

  limpiarNMat(); // Limpiamos el formulario
  if (!$.fn.DataTable.isDataTable("#tblCat_Mat")) {
    cat_Materiales(); // Listamos el catalogo de materiales
  }
});

// Función para listado de unidades
let Unidades = () => {
  $.post("../Archivos/Presupuestos/pres_Servicios.php?op=Unidades", (data) => {
    $("#Id_UM1").html(data);
    $("#Id_UM1").selectpicker("refresh");

    $("#Id_UM2").html(data);
    $("#Id_UM2").selectpicker("refresh");
  });
};

// Función para listado de Proveedores
let Proveedores = () => {
  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=Proveedores",
    (data) => {
      $("#Id_Prov").html(data);
      $("#Id_Prov").selectpicker("refresh");
    }
  );
};

// Función para listado de Familias
let Familias = () => {
  $.post("../Archivos/Presupuestos/pres_Servicios.php?op=Familias", (data) => {
    $("#Id_Fam").html(data);
    $("#Id_Fam").selectpicker("refresh");
    $("#Id_Fam2").html(data);
    $("#Id_Fam2").selectpicker("refresh");
  });
};

// Funcionpara listado de catalogo de materiales
let cat_Materiales = () => {
  setTimeout(function () {
    tblCat_Mat = $("#tblCat_Mat")
      .dataTable({
        aProcessing: true, //Activamos el procesamiento del datatables
        aServerSide: true, //Paginación y filtrado realizados por el servidor
        dom: "Bfrtip", //Definimos los elementos del control de tabla
        buttons: ["copyHtml5", "excelHtml5", "csvHtml5"],
        columnDefs: [{ width: "70%", targets: 1 }],
        ajax: {
          url: "../Archivos/Presupuestos/pres_Servicios.php?op=cat_Materiales",
          type: "post",
          dataType: "json",
        },
        bDestroy: true,
        iDisplayLength: 100, //Paginación
        order: [[0, "asc"]], //Ordenar (columna,orden)
      })
      .DataTable();
  }, 300);
};

// Función para obtener el valor del un registro del catalogo de materiales
let verCat_Mat = (Cve_Mat) => {
  subir();
  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=verCat_Mat",
    { Cve_Mat },
    (data) => {
      let Descripcion = data.Desc_Mat;
      Descripcion = Descripcion.replaceAll("&QUOT;", '"');
      Descripcion = Descripcion.replaceAll("&quot;", '"');

      $("#CMat").val(Cve_Mat);
      $("#Id_Fam").val(data.Id_Fam);
      $("#Id_Fam").selectpicker("refresh");
      $("#Id_UM1").val(data.Id_UM1);
      $("#Id_UM1").selectpicker("refresh");
      $("#Id_UM2").val(data.Id_UM2);
      $("#Id_UM2").selectpicker("refresh");
      $("#Id_Prov").val(data.Id_Prov);
      $("#Id_Prov").selectpicker("refresh");
      $("#Desc_Mat").val(Descripcion);

      // Mostramos la información en la pestaña servicios
      $("#Pref").val("R");
      $("#Cve").val(Cve_Mat);
      $("#Cve").change();
      $("#Descripcion").val("SUMINISTRO E INSTALACIÓN DE " + Descripcion);
      $("#Cant").val(1);
      $("#HE").val(3);
    },
    "json"
  );
};

//  Función para guardar nuvos materiales en el catatlogo
let guardarNMat = (e) => {
  e.preventDefault();

  let Cve_Mat = $("#CMat").val();
  let data = new FormData($("#formNuevo")[0]);

  data.append("Cve_Mat", Cve_Mat);

  $.ajax({
    type: "post",
    url: "../Archivos/Presupuestos/pres_Servicios.php?op=NewMat",
    data: data,
    contentType: false,
    processData: false,
    success: (rsp) => {
      if (rsp.includes("correctamente")) {
        swal.fire(rsp, "", "success");
        tblCat_Mat.ajax.reload();
        materiales(); // Listamos las claves de los materiales
        setTimeout(() => {
          verCat_Mat(Cve_Mat);
          $("#Servicios-tab").click();
        }, 1200);
      } else {
        swal.fire(rsp, "", "error");
      }
    },
  });
};

// Función para lipiar fomulario
let limpiarNMat = () => {
  $("#formNuevo .form-control").val("");
  $("#Id_UM1").selectpicker("refresh");
  $("#Id_UM2").selectpicker("refresh");
  $("#Id_Fam").selectpicker("refresh");
  $("#Id_Prov").selectpicker("refresh");
};

/*-------------------------------------------------------------------------------------------------------------------------------------------\
 *                                                       Funciones para Formulario de matrices                                                |
 *-------------------------------------------------------------------------------------------------------------------------------------------*/

// Función para vaciar campos de partidas
let limpiarPartida = () => {
  $("#formPartidas .form-control").val("");
  $("#Pref").attr("disabled", false);
  $("#Cve").attr("disabled", false);
  $("#Cve").selectpicker("refresh");
  ClvAnt = "";
};

// Función para agregar partidas a las cotizaciones
let agregaPartida = (e) => {
  e.preventDefault();

  let Cod = $("#Pref").val() + $("#Cve").val();
  let Num_Cot = $("#Num_Cot").val();

  let data = new FormData($("#formPartidas")[0]);

  data.append("Cod", Cod);
  data.append("Num_Cot", Num_Cot);
  data.append("ClvAnt", ClvAnt);

  $.ajax({
    type: "post",
    url: "../Archivos/Presupuestos/pres_Servicios.php?op=agregaPartida",
    data: data,
    contentType: false,
    processData: false,
    success: function (resp) {
      tblMatrices.ajax.reload();
      updateCD(Num_Cot);
      swal.fire(resp, "", "success");
      setTimeout(() => {
        dataCotizacion(Num_Cot);
      }, 150);

      if (resp.includes("correctamente")) {
        limpiarPartida();
      } else {
        swal.fire(resp, "", "error");
      }
    },
  });
};

// Función para mostrar la información de la matriz
let editmatriz = (Num_Cot, Cod, Clave) => {
  subir();

  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=editmatriz",
    { Num_Cot: Num_Cot, Cod: Cod, Clave: Clave },
    (data) => {
      data = JSON.parse(data);

      let cod = data.Cod;
      let Descripcion = data.Descripcion;
      Descripcion = Descripcion.replaceAll("&QUOT;", '"');
      Descripcion = Descripcion.replaceAll("&quot;", '"');

      $("#Pref").val(cod.substr(0, 1));
      $("#Pref").attr("disabled", true);
      $("#Cve").val(cod.substr(1));
      //$('#Cve').attr('disabled', true)
      //$('#Cve').selectpicker('refresh')
      $("#UM").val(data.UM);
      $("#Clv").val(data.Clv);
      $("#Cant").val(data.Cant);
      $("#HE").val(data.HE);
      $("#PU").val(data.PU);
      $("#Descripcion").val(Descripcion);

      ClvAnt = Clave;
    }
  );
};

// Función para listar las matrices
let matrices = (Num_Cot) => {
  claves(Num_Cot);

  setTimeout(function () {
    tblMatrices = $("#tblMatrices")
      .dataTable({
        aProcessing: true, //Activamos el procesamiento del datatables
        aServerSide: true, //Paginación y filtrado realizados por el servidor
        dom: "Bfrtip", //Definimos los elementos del control de tabla
        buttons: ["copyHtml5", "excelHtml5", "csvHtml5"],
        columnDefs: [
          { width: "12%", targets: 0 },
          { width: "30%", targets: 3 },
          { width: "10%", targets: 9 },
        ],
        ajax: {
          url: "../Archivos/Presupuestos/pres_Servicios.php?op=matrices",
          type: "post",
          dataType: "json",
          data: { Num_Cot },
        },
        bDestroy: true,
        iDisplayLength: 1000, //Paginación
        order: [[2, "asc"]], //Ordenar (columna,orden)
      })
      .DataTable();
  }, 100);
};

// Funcion para actualizar el orden de las matrices
let updateOrden = (Orden, Cod, Clv) => {
  let Num_Cot = $("#Num_Cot").val();

  setTimeout(function () {
    if (Orden != "") {
      $.post("../Archivos/Presupuestos/pres_Servicios.php?op=updateOrden", {
        Num_Cot,
        Cod,
        Orden,
        Clv,
      });
    }
  }, 500);
};

// Recargamos la tabla de matrices
$("#Recargar").click(function (e) {
  e.preventDefault();

  let Num_Cot = $("#Num_Cot").val();
  if (Num_Cot != "") {
    //matrices(Num_Cot)
    dataCotizacion(Num_Cot);

    setTimeout(() => {
      tblTitulo.ajax.reload();
      tblMatrices.ajax.reload();
    }, 1000);
  }
});

// Función para guardar matriz en el catalogo de matrices
let crearMat = (Num_Cot, Cod, Clave) => {
  swal
    .fire({
      title: "Se guardará esta matriz en el catalogo de matrices",
      text: "",
      icon: "info",
      showCancelButton: true,
      confirmButtonColor: "#10ABB4",
      confirmButtonText: "SI, ESTOY SEGURO",
      cancelButtonText: "NO, CANCELAR",
      closeOnConfirm: false,
      closeOnCancel: true,
    })
    .then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../Archivos/Presupuestos/pres_Servicios.php?op=crearMat",
          { Num_Cot, Cod, Clave },
          (data) => {
            if (data.includes("correctamente")) {
              tblMatrices.ajax.reload();
              swal.fire(data, "", "success");
            } else {
              swal.fire(data, "", "error");
            }
          }
        );
      }
    });
};

/*-------------------------------------------------------------------------------------------------------------------------------------------\
 *                                                       Eliminar materiales                                                                  |
 *-------------------------------------------------------------------------------------------------------------------------------------------*/

// Función para borrar materiales de las matrices
let deleteMatriz = (Num_Cot, Cod, Clave) => {
  swal
    .fire({
      title: "Se eliminara la matriz selecionada",
      text: "",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#10ABB4",
      confirmButtonText: "SI, ESTOY SEGURO",
      cancelButtonText: "NO, CANCELAR",
      closeOnConfirm: false,
      closeOnCancel: true,
    })
    .then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../Archivos/Presupuestos/pres_Servicios.php?op=deleteMatriz",
          { Num_Cot, Cod, Clave },
          (data) => {
            if (data.includes("correctamente")) {
              tblMatrices.ajax.reload();
              updateCD(Num_Cot);
              setTimeout(() => {
                dataCotizacion(Num_Cot);
              }, 300);
              swal.fire(data, "", "success");
            } else {
              swal.fire(data, "", "error");
            }
          }
        );
      }
    });
};

/*-------------------------------------------------------------------------------------------------------------------------------------------\
 *                                                       Funciones Modal materiales                                                           |
 *-------------------------------------------------------------------------------------------------------------------------------------------*/
$("#Manual-tab").click(function (e) {
  e.preventDefault();
  setTimeout(() => {
    tblMatriz.ajax.reload();
  }, 250);
});

// Función para listar los materiales de la matriz
let matriz = (Num_Cot, Cod, Clave) => {
  $("#Cod").val(Cod);
  totalMat(Num_Cot, Cod, Clave);
  // listamos las matrices diponibles para agregar
  Existentes(Num_Cot, Cod);

  ClvAnt = Clave;

  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=editmatriz",
    { Num_Cot: Num_Cot, Cod: Cod, Clave: Clave },
    (data) => {
      data = JSON.parse(data);
      console.log(data);
      let Descripcion = data.Descripcion;
      $("#modalLabel").html(Descripcion.replaceAll("&QUOT;", '"'));
    }
  );

  setTimeout(() => {
    tblMatriz = $("#tblMatriz")
      .dataTable({
        aProcessing: true, //Activamos el procesamiento del datatables
        aServerSide: true, //Paginación y filtrado realizados por el servidor
        dom: "Bfrtip", //Definimos los elementos del control de tabla
        buttons: ["copyHtml5", "excelHtml5", "csvHtml5"],
        columnDefs: [{ width: "14%", targets: 0 }],
        ajax: {
          url: "../Archivos/Presupuestos/pres_Servicios.php?op=matriz",
          type: "post",
          dataType: "json",
          data: { Num_Cot, Cod, Clave },
        },
        bDestroy: true,
        iDisplayLength: 100, //Paginación
        order: [[0, "asc"]], //Ordenar (columna,orden)
      })
      .DataTable();
  }, 250);
};

// Función para mostrar datos del material
let mostrar = (Num_Cot, Cod, Cve, Clave) => {
  $("#Cod").val(Cod);
  let Num_OT = $("#Num_OT").val();

  var target_offset = $("#modal").offset();
  var target_top = target_offset.top;
  $("html,body").animate({ scrollTop: target_top }, { duration: "slow" });

  // Consultamos los precios en las cotrizaciones de la OT
  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=preciosCot",
    { Cve, Num_OT },
    (data) => {
      $("#divPrecios").html(data);
    },
    "html"
  );

  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=mostrar",
    { Num_Cot, Cod, Cve, Clave },
    (data) => {
      let Descripcion = data.Descripcion;
      Descripcion = Descripcion.replaceAll("&quot;", '"');

      $("#Cve_Mat").val(data.Cve);
      $("#Cve_Mat").selectpicker("refresh");
      $("#UMM").val(data.UM);
      $("#Cantidad").val(data.Cant);
      $("#PUM").val(data.PU);

      $("#Concepto").val(Descripcion.replaceAll("&QUOT;", '"'));

      $.post(
        "../Archivos/Presupuestos/pres_Servicios.php?op=mat",
        { Cve },
        (data) => {
          let Id_Mat = data.Id_Mat;

          $.post(
            "../Archivos/Compras/opciones.php?op=costos_Mat",
            { Id_Mat },
            (data) => {
              $("#Cto_Ult").val(data.Cto_Ult);
              $("#Fecha_UC").html(data.Fecha_UC);

              $("#Cto_Prom").val(data.Cto_Prom);
              $("#Cto_Min").val(data.Cto_Min);
              $("#Cto_Max").val(data.Cto_Max);
              $("#Id_Proveedor").val(data.Proveedor);
            },
            "json"
          );
        },
        "json"
      );
    },
    "json"
  );
};

// Funcion para obtener la unidad de medida y costo
$("#Cve_Mat").change(function (e) {
  e.preventDefault();
  let Cve = $(this).val();
  let Num_OT = $("#Num_OT").val();

  // Consultamos los precios en las cotrizaciones de la OT
  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=preciosCot",
    { Cve, Num_OT },
    (data) => {
      $("#divPrecios").html(data);
    },
    "html"
  );

  // Obtenemos la unidad de medida y el precio unitario
  if (Cve.substring(0, 2) == "MO") {
    moData(Cve);
  } else {
    $.post(
      "../Archivos/Presupuestos/pres_Servicios.php?op=mat",
      { Cve },
      (data) => {
        $("#UMM").val(data.Desc_UM);
        $("#PUM").val(data.Costo);
        $("#Fec_Mod").html(data.Fec_Mod);
      },
      "json"
    );
  }

  let desc = $("#Cve_Mat option:selected").data("subtext");
  $("#Concepto").val(desc);
});

let setPrecio = (PU) => {
  $("#PUM").val(PU);
};

let guardarMaterial = (e) => {
  e.preventDefault();

  let Num_Cot = $("#Num_Cot").val();
  let Num_OT = $("#Num_OT").val();
  let Cod = $("#Cod").val();
  let Cve = $("#Cve_Mat").val();
  let UM = $("#UMM").val();
  let Cant = $("#Cantidad").val();
  let PU = $("#PUM").val();
  let Tipo = Cve.substring(0, 2) == "MO" ? "MANO DE OBRA" : "INSUMO";
  let Descripcion = $("#Concepto").val();
  let Clave = ClvAnt;

  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=guardarMaterial",
    { Num_Cot, Cod, Cve, UM, Cant, PU, Tipo, Descripcion, Clave },
    (data) => {
      if (data.includes("correctamente")) {
        tblMatrices.ajax.reload();
        tblMatriz.ajax.reload();
        cleanModal();
        updateCD(Num_Cot);
        totalMat(Num_Cot, Cod, Clave);
        setTimeout(() => {
          dataCotizacion(Num_Cot);
        }, 200);
        swal.fire(data, "", "success");
      } else {
        swal.fire(data, "", "error");
      }
    }
  );

  // Agregamos el precio sugerido
  $.post("../Archivos/Presupuestos/pres_Servicios.php?op=addSug", {
    Num_Cot,
    Num_OT,
    Cod,
    Cve,
    PU,
  });
};

// Función pra limpiar el modal
let cleanModal = () => {
  $("#Cve_Mat").val("");
  $("#Cve_Mat").selectpicker("refresh");
  $("#UMM").val("");
  $("#Cantidad").val("");
  $("#PUM").val("");
  $("#Concepto").val("");
  $("#Fecha_UC").html("");
  $("#Cto_Prom").val("");
  $("#Cto_Min").val("");
  $("#Cto_Max").val("");
  $("#Id_Proveedor").val("");
  $("#Cto_Ult").val("");
  $("#Fec_Mod").html("");
  $("#divPrecios").html("");
};

// Función para actualizar el costo directo
let updateCD = (Num_Cot) => {
  $.post("../Archivos/Presupuestos/pres_Servicios.php?op=updateCD", {
    Num_Cot,
  });
};

//  Función para impimir las cotizaciones
$("#pdf_General").click(function (e) {
  e.preventDefault();

  let Num_OT = $("#Num_OT").val();
  let Num_Cot = $("#Num_Cot").val();

  if (Num_Cot != "") {
    swal
      .fire({
        title: "Seleccione el reporte a mostrar",
        text: "",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#206CAD",
        confirmButtonText: "Con precios",
        cancelButtonText: "Sin precios",
        closeOnConfirm: true,
        closeOnCancel: true,
      })
      .then((result) => {
        if (result.isConfirmed) {
          window.open(
            "../Archivos/Presupuestos/Pres_Obra.php?Num_OT=" +
              btoa(Num_OT) +
              "&Num_Cot=" +
              btoa(Num_Cot) +
              "&Precios=" +
              btoa("S"),
            "_blank"
          );
        } else {
          window.open(
            "../Archivos/Presupuestos/Pres_Obra.php?Num_OT=" +
              btoa(Num_OT) +
              "&Num_Cot=" +
              btoa(Num_Cot) +
              "&Precios=" +
              btoa("N"),
            "_blank"
          );
        }
      });
  }
});

$("#pdf_Materiales").click(function (e) {
  e.preventDefault();

  let Num_OT = $("#Num_OT").val();
  let Num_Cot = $("#Num_Cot").val();

  if (Num_Cot != "") {
    window.open(
      "../Archivos/Presupuestos/Pres_Obra_Mat.php?Num_OT=" +
        btoa(Num_OT) +
        "&Num_Cot=" +
        btoa(Num_Cot),
      "_blank"
    );
  }
});

$("#pdf_Pro").click(function (e) {
  e.preventDefault();

  let Num_OT = $("#Num_OT").val();
  let Num_Cot = $("#Num_Cot").val();

  if (Num_Cot != "") {
    swal
      .fire({
        title: "Seleccione el reporte a mostrar",
        text: "",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#206CAD",
        confirmButtonText: "Con Titulos",
        cancelButtonText: "Con subtitulos",
        closeOnConfirm: true,
        closeOnCancel: true,
      })
      .then((result) => {
        if (result.isConfirmed) {
          window.open(
            "../Archivos/Presupuestos/Pres_Obra_Prerroteo.php?Num_OT=" +
              btoa(Num_OT) +
              "&Num_Cot=" +
              btoa(Num_Cot) +
              "&Titulos=" +
              btoa("S"),
            "_blank"
          );
        } else {
          window.open(
            "../Archivos/Presupuestos/Pres_Obra_Prerroteo.php?Num_OT=" +
              btoa(Num_OT) +
              "&Num_Cot=" +
              btoa(Num_Cot),
            "_blank"
          );
        }
      });
  }
});

$("#pdf_Insumos").click(function (e) {
  e.preventDefault();

  let Num_OT = $("#Num_OT").val();
  let Num_Cot = $("#Num_Cot").val();

  if (Num_Cot != "") {
    swal
      .fire({
        title: "Seleccione el reporte a mostrar",
        text: "",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#206CAD",
        confirmButtonText: "Con Titulos",
        cancelButtonText: "Con subtitulos",
        closeOnConfirm: true,
        closeOnCancel: true,
      })
      .then((result) => {
        if (result.isConfirmed) {
          window.open(
            "../Archivos/Presupuestos/pdf_Insumos.php?Num_OT=" +
              btoa(Num_OT) +
              "&Num_Cot=" +
              btoa(Num_Cot) +
              "&Titulos=" +
              btoa("S"),
            "_blank"
          );
        } else {
          window.open(
            "../Archivos/Presupuestos/pdf_Insumos.php?Num_OT=" +
              btoa(Num_OT) +
              "&Num_Cot=" +
              btoa(Num_Cot),
            "_blank"
          );
        }
      });
  }
});

// Boton para exportar el reporte de presupuestos a Excel
$("#excel_Servicios").click(function (e) {
  e.preventDefault();

  let Num_OT = $("#Num_OT").val();
  let Num_Cot = $("#Num_Cot").val();

  if (Num_Cot != "") {
    swal
      .fire({
        title: "Seleciona el archivo a exportar",
        text: "",
        type: "info",
        showCancelButton: true,
        confirmButtonColor: "#206CAD",
        confirmButtonText: "Con factores",
        cancelButtonText: "Sin factores",
        closeOnConfirm: true,
        closeOnCancel: true,
      })
      .then((result) => {
        if (result.isConfirmed) {
          window.open(
            "../Archivos/Presupuestos/excel_Servicios.php?Num_OT=" +
              btoa(Num_OT) +
              "&Num_Cot=" +
              btoa(Num_Cot) +
              "&Factores=" +
              btoa("S"),
            "_blank"
          );
        } else {
          window.open(
            "../Archivos/Presupuestos/excel_Servicios.php?Num_OT=" +
              btoa(Num_OT) +
              "&Num_Cot=" +
              btoa(Num_Cot),
            "_blank"
          );
        }
      });
  }
});

// Boton para generar el an{alisis de precios unitarios}
$("#pres_analisis").click(function (e) {
  e.preventDefault();

  let Num_OT = $("#Num_OT").val();
  let Num_Cot = $("#Num_Cot").val();

  if (Num_Cot != "") {
    window.open(
      "../Archivos/Presupuestos/pres_analisis.php?Num_OT=" +
        btoa(Num_OT) +
        "&Num_Cot=" +
        btoa(Num_Cot),
      "_blank"
    );
  }
});

//  Función para exportar materiales
$("#btnExport").click(function (e) {
  e.preventDefault();

  let Num_Cot = $("#Num_Cot").val();

  setTimeout(function () {
    window.open(
      "../Archivos/Presupuestos/export_Mat.php?Num_Cot=" + btoa(Num_Cot),
      "_blank"
    );
  }, 100);
});

/*-------------------------------------------------------------------------------------------------------------------------------------------\
 *                                              Cargar materiales de una matriz existente                                                     |
 *-------------------------------------------------------------------------------------------------------------------------------------------*/
$("#Existente-tab").click(function (e) {
  e.preventDefault();

  $("#Existentes").val("");
});

// Listado de matrices existentes
let Existentes = (Num_Cot, Cod) => {
  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=matrizExistente",
    { Num_Cot: Num_Cot, Cod: Cod },
    (data) => {
      $("#Existentes").html(data);
    }
  );
};

// Función para agregar nateriales desde matriz existente
let guardarExistente = (e) => {
  e.preventDefault();

  let Num_Cot = $("#Num_Cot").val();
  let Cod = $("#Cod").val();
  let Cve = $("#Existentes").val();
  let Clv = ClvAnt;

  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=agregarExistente",
    { Num_Cot: Num_Cot, Cod: Cod, Cve: Cve, Clv: Clv },
    (msg) => {
      if (msg.includes("correctamente")) {
        tblMatrices.ajax.reload();
        tblMatriz.ajax.reload();
        swal.fire(msg, "", "success");
        $("#Existentes").val("");
        updateCD(Num_Cot);
        setTimeout(() => {
          dataCotizacion(Num_Cot);
        }, 200);
      } else {
        swal.fire(msg, "", "error");
      }
    }
  );
};

/*-------------------------------------------------------------------------------------------------------------------------------------------\
 *                                                       Eliminar materiales                                                                  |
 *-------------------------------------------------------------------------------------------------------------------------------------------*/

// Función para borrar materiales de las matrices
let deleteMat = (Num_Cot, Cod, Cve, Clave) => {
  swal
    .fire({
      title: "Se eliminara el registro selecionado",
      text: "",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#10ABB4",
      confirmButtonText: "SI, ESTOY SEGURO",
      cancelButtonText: "NO, CANCELAR",
      closeOnConfirm: false,
      closeOnCancel: true,
    })
    .then((result) => {
      if (result.isConfirmed) {
        $.post(
          "../Archivos/Presupuestos/pres_Servicios.php?op=deleteMat",
          { Num_Cot: Num_Cot, Cod: Cod, Cve: Cve, Clave: Clave },
          (data) => {
            if (data.includes("correctamente")) {
              tblMatrices.ajax.reload();
              tblMatriz.ajax.reload();
              cleanModal();
              updateCD(Num_Cot);
              totalMat(Num_Cot, Cod, Clave);
              setTimeout(() => {
                dataCotizacion(Num_Cot);
              }, 100);
              swal.fire(data, "", "success");
            } else {
              swal.fire(data, "", "error");
            }
          }
        );
      }
    });
};

// Función para obtener el total de la matriz
let totalMat = (Num_Cot, Cod, Clave) => {
  console.log(Num_Cot, Cod, Clave);
  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=totalMat",
    { Num_Cot, Cod, Clave },
    (data) => {
      console.log("Total: ", data);
      $("#Total").val(data);
    }
  );
};

/*-------------------------------------------------------------------------------------------------------------------------------------------\
 *                                                       Autorizar Cotizaciones                                                               |
 *-------------------------------------------------------------------------------------------------------------------------------------------*/

$("#btnAutorizar").click(function (e) {
  e.preventDefault();

  let Num_Cot = $("#Num_Cot").val();
  let Num_OT = $("#Num_OT").val();

  if (Imp_CD.value == 0 || Imp_CD.value == "") {
    swal.fire(
      "No se puede autorizar una cotización sin un importe!",
      "Debe contener al meno una partida conun importe mayor a 0",
      "warning"
    );
  } else {
    swal
      .fire({
        title: "Se autorizará la cotización " + Num_Cot,
        text: "Una vez autorizada no se no se permitiran modificaciones para esta cotización.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#10ABB4",
        confirmButtonText: "SI, ESTOY SEGURO",
        cancelButtonText: "NO, CANCELAR",
        closeOnConfirm: false,
        closeOnCancel: true,
      })
      .then((result) => {
        if (result.isConfirmed) {
          $.post(
            "../Archivos/Presupuestos/pres_Servicios.php?op=autorizar",
            { Num_Cot: Num_Cot, Num_OT: Num_OT },
            (data) => {
              console.log(data);
              if (data.includes("correctamente")) {
                updateCD(Num_Cot);
                setTimeout(() => {
                  dataCotizacion(Num_Cot);
                }, 200);
                swal.fire(data, "", "success");
              } else {
                swal.fire(data, "", "error");
              }
            }
          );
        }
      });
  }
});

// Función para cargar materiales del presupuesto autorizado al inventario
$("#btnLiberar").click(function (e) {
  e.preventDefault();

  let Num_Cot = $("#Num_Cot").val();
  let Num_OT = $("#Num_OT").val();

  // Validamos la existencia de materiales
  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=validarMat",
    { Num_Cot },
    (data) => {
      if (data > 0) {
        swal
          .fire({
            title: "Se cargarán los insumos al inventario de la OT " + Num_OT,
            text: "Se eliminaran los materiales cargados anteriormente",
            type: "info",
            showCancelButton: true,
            confirmButtonColor: "#10ABB4",
            confirmButtonText: "SI, ESTOY SEGURO",
            cancelButtonText: "NO, CANCELAR",
            closeOnConfirm: false,
            closeOnCancel: true,
          })
          .then((result) => {
            if (result.isConfirmed) {
              swal.fire({
                title:
                  '<h3>Cargando información...</h3><br><img src="../../img/Cargando.gif" width="150"></img>',
                html: "Html",
                showCloseButton: false,
                showConfirmButton: false,
                showCancelButton: false,
              });

              $.post(
                "../Archivos/Presupuestos/pres_Servicios.php?op=cargaInventario",
                { Num_OT, Num_Cot },
                (data) => {
                  msg = data.msg;
                  msg2 = data.msg2;

                  if (msg.includes("correctamente")) {
                    swal.fire(msg, msg2, "success");
                  } else {
                    swal.fire(msg, msg2, "error");
                  }
                },
                "json"
              );
            }
          });
      } else {
        swal.fire("No hay materiales por liberar", "", "error");
      }
    }
  );
});

/*-------------------------------------------------------------------------------------------------------------------------------------------\
 *                                                       Crear siguiente Cotización                                                           |
 *-------------------------------------------------------------------------------------------------------------------------------------------*/

$("#btnNueva").click(function (e) {
  e.preventDefault();

  let Num_OT = $("#Num_OT").val();
  let Num_Cot = $("#Num_Cot").val();

  swal
    .fire({
      title: "Se creará una nueva cotización a partir de esta. ",
      text: "",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#10ABB4",
      confirmButtonText: "SI, ESTOY SEGURO",
      cancelButtonText: "NO, CANCELAR",
      closeOnConfirm: false,
      closeOnCancel: true,
    })
    .then((result) => {
      if (result.isConfirmed) {
        swal.fire({
          title:
            '<h3>Cargando información...</h3><br><img src="../../img/Cargando.gif" width="150"></img>',
          html: "",
          showCloseButton: false,
          showConfirmButton: false,
          showCancelButton: false,
        });

        $.post(
          "../Archivos/Presupuestos/pres_Servicios.php?op=nueva",
          { Num_OT, Num_Cot },
          (data) => {
            console.log(data);
            if (data.includes("Se creó")) {
              updateCD(Num_Cot);
              $("#Num_OT").change();
              swal.fire(data, "", "success");
            } else {
              swal.fire(data, "", "error");
            }
          }
        );
      }
    });
});

/*-------------------------------------------------------------------------------------------------------------------------------------------\
 *                                                       Listado de materiales                                                                |
 *-------------------------------------------------------------------------------------------------------------------------------------------*/

$("#Materiales-tab").click(function (e) {
  e.preventDefault();

  setTimeout(() => {
    tabMat.ajax.reload();
  }, 100);
});

// Función para listado de materiales
let cat_Mat = () => {
  tabMat = $("#tblMateriales")
    .dataTable({
      aProcessing: true, //Activamos el procesamiento del datatables
      aServerSide: true, //Paginación y filtrado realizados por el servidor
      dom: "Bfrtip", //Definimos los elementos del control de tabla
      buttons: [],
      columnDefs: [{ width: "88%", targets: 1 }],
      ajax: {
        url: "../Archivos/Presupuestos/pres_Servicios.php?op=cat_Mat",
        type: "post",
        dataType: "json",
      },
      bDestroy: true,
      iDisplayLength: 15, //Paginación
      order: [[0, "asc"]], //Ordenar (columna,orden)
    })
    .DataTable();
};

// Función para copiar las claves de los materiales al portapapeles
let copyToClipboard = (text) => {
  $("#Manual-tab").click();
  $("#Cve_Mat").val(text);
  $("#Cve_Mat").change();
};

// Se muestra el modal
$("#modal").on("shown.bs.modal", function () {
  if (!$.fn.DataTable.isDataTable("#tblMateriales")) {
    cat_Mat(); // Listamos los materiales del catalogo
  }
});

// Se cierra el modal
$("#modal").on("hidden.bs.modal", function () {
  cleanModal();
  $("#Total").val("");
  Material = "";
  $(".Material").prop("checked", false);
  $("#Partida").html("");
  $("#Cv").html("");
  $("#Par").html("");
  $("#C").html("");
  ClvAnt = "";
});

// Funcion para subir al formulario de partidas
let subir = () => {
  var target_offset = $("#Partidas").offset();
  var target_top = target_offset.top;
  $("html,body").animate({ scrollTop: target_top }, { duration: "slow" });
};

// Funcion para validar numeros con 2 decimales
function NumCheck(e, field) {
  key = e.keyCode ? e.keyCode : e.which;
  // backspace
  if (key == 8) return true;
  // 0-9
  if (key > 47 && key < 58) {
    if (field.value == "") return true;
    regexp = /.[0-9]{9}$/; // Validamos la entrada de 8 digitos
    return !regexp.test(field.value);
  }
  // . (Decimales)
  if (key == 46) {
    if (field.value == "") return false;
    regexp = /^[0-9]+$/;
    return regexp.test(field.value);
  }
  return false;
}

/**---------------------------------------------------------------------- */
let guardarCopyCotizacion = (e) => {
  e.preventDefault();
  let numOtA = $("#Num_OT").val();
  let numOtC = $("#OtCotizacion").val();
  let numCons = $("#OtNCotizacion").val();
  let numCotizacion = $("#cotizacionN").val();
  console.log("Valor de la ot al que se copiara: " + numOtA);
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
      "../Archivos/Presupuestos/pres_Servicios.php?op=copyCotizacion",
      { numOtA, numOtC, numCons, numCotizacion },
      (data) => {
        if (data.includes("Se creó")) {
          $("#Num_OT").change();
          updateCD(numCotizacion);
          Swal.fire({
            position: "center",
            icon: "success",
            title: data,
            showConfirmButton: false,
            timer: 2500,
          });
          $("#modalCopyC").click();
        } else {
          Swal.fire({
            position: "center",
            icon: "error",
            title: data,
            showConfirmButton: false,
            timer: 2500,
          });
        }
      }
    );
  }, 500);
};

$("#OtCotizacion").change(function (e) {
  e.preventDefault();
  let Num_OT = this.value;

  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=numCot",
    { Num_OT: Num_OT },
    (data) => {
      data = JSON.parse(data);
      $("#cotizacionN").val(data.Num_Cot);
      $("#OtNCotizacion").html(data.NoCot);
    }
  );
});

$("#OtNCotizacion").change(function (e) {
  e.preventDefault();
  let Cons = $(this).val();
  let Num_OT = $("#OtCotizacion").val();
  $.post(
    "../Archivos/Presupuestos/pres_Servicios.php?op=Num_Cot",
    { Num_OT, Cons },
    (data) => {
      console.log(data);
      $("#cotizacionN").val(data);
    },
    "json"
  );
});

let limpiarCopy = () => {
  console.log("Eliminar");
  $("#OtCotizacion").val("");
  $("#OtNCotizacion").html("");
  $("#cotizacionN").val("");
  $("#OtCotizacion").selectpicker("refresh");
  //$("#OtNCotizacion").selectpicker("refresh");
};

/**------------------------------------------------------------------ */

let guardarExcel = (e) => {
  e.preventDefault();
  let data = new FormData($("#formExcel")[0]);
  let Num_Cot = $("#Num_Cot").val();
  data.append("Num_Cot", Num_Cot);
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
          url: "../Archivos/Presupuestos/saveMatrices.php",
          data: data,
          contentType: false,
          processData: false,
          success: function (result) {
            console.log(result);
            result = JSON.parse(result);
            $("#Recargar").click();
            if (result.icon.includes("info")) {
              Swal.fire({
                position: "center",
                icon: "info",
                title: result.msg,
                showConfirmButton: false,
                timer: 2500,
              });
              updateCD(Num_Cot);
              setTimeout(() => {
                dataCotizacion(Num_Cot);
              }, 300);
              $("#tblResultE").html(result.tbl);
              $("#archivoExcelM").val("");
            } else {
              Swal.fire({
                position: "center",
                icon: result.icon,
                title: result.msg,
                showConfirmButton: false,
                timer: 1500,
              });
              $("#tblResultE").html("");
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

let btnLimpiarE = () => {
  $("#tblResultE").html("");
  $("#archivoExcelM").val("");
};
init();
