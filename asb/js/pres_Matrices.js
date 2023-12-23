let tblMatrices = ''
let tblMatriz = ''

let init = () => {
    // Formulario de Partidas
    $('#formPartidas').on('submit', function(e){ guardarMAtriz(e) })

    // Formulario modal
    $('#formMateriales').on('submit', function(e){ guardarMaterial(e) })

    matrices() // Listamos las matrices existentes
    //limpiar() // Vaciamos los campos
    materiales() // Listamos las clves de los materiales
}


// Función para listar ordenes
let ordenes = () => {
    $.post('../Archivos/Presupuestos/pres_Matrices.php?op=Num_OT',
        data => {
            $('#Num_OT').html(data)
            $('#Num_OT').selectpicker('refres')
        }
    )
}

// Funcion para listar las claves de los materiales
let materiales = () => {
    $.post('../Archivos/Presupuestos/pres_Matrices.php?op=materiales',
        data => {
            $('#Codigo').html(data)
            $('#Cve_Mat').html(data)
            $('#Cve_Mat').selectpicker('refres')
        }
    )
}


// Datos mano de obra
let moData = (Cve) => {
    $('#Cantidad').val(1)
    $('#UMM').val('JOR')

    switch(Cve){
        // AYUDANTE GENERAL
        case 'MO021': $('#PUM').val(218.93); break;
        // OFICIAL SOLDADOR
        case 'MO091': $('#PUM').val(349.52); break;
        // TECNICO ESPECIALIZADO
        case 'MO111': $('#PUM').val(328.67); break;
        // INGENIERO PROYECTISTA
        case 'MO001-5': $('#PUM').val(857.21); break;
        // SUPERVISOR
        case 'MO112': $('#PUM').val(399.97); break;
    }
}

// Modificacmos el concepto
$('#Pref').keyup(function (e) {
    e.preventDefault()

    if ($(this).val() != '' && $('#Cve').val() != null){
        let desc = $('#Cve option:selected').data('subtext')
        let tipo = prefijos($('#Pref').val())
        // Cambiamos la descripción
        $('#Descripcion').val(tipo + desc)
    }
})


// Obtenemos el concepto y la unidad de medida
$('#Cve').change(function(e) {
    e.preventDefault()
    
    let desc = $('#Cve option:selected').data('subtext')
    let tipo = prefijos($('#Pref').val())

    $('#Descripcion').val(tipo + desc)

    // Obtenemos la unidad de medida y el precio unitario
    let Cve = $(this).val()
    $.post('../Archivos/Presupuestos/pres_Matrices.php?op=mat',
        {Cve:Cve}, data => {

            data = JSON.parse(data)
            $('#UM').val(data.Desc_UM)
            $('#PU').val(data.Cto_Ult)
        }
    )
})


// prefijos
let prefijos = (prefijo) => {
    let pref = ''

    switch(prefijo){
        case 'I': pref = 'INSTALACIÓN DE '; break;
        case 'S': pref = 'SUMINISTRO DE '; break;
        case 'R': pref = 'REPARACIÓN DE '; break;
        case 'X': pref = 'SUMINISTRO E INSTALACIÓN '; break;
        default:  pref = 'SUMINISTRO E INSTALACIÓN '; break;
    }

    return pref;
}


// Función para vaciar campos
let limpiar = () => {
    $('.form-control').val('')
    $('#Cve').selectpicker('refresh')
}

// Función para agregar partidas a las cotizaciones
let guardarMAtriz = (e) => {
    e.preventDefault()

    let Cod = $('#Pref').val() + $('#Cve').val()
    let data = new FormData($('#formPartidas')[0])

    data.append('Cod', Cod)

    $.ajax({
        type: "post",
        url: "../Archivos/Presupuestos/pres_Matrices.php?op=guardarMAtriz",
        data: data,
        contentType: false,
        processData: false,
        success: function (resp) {
            if(resp.includes('correctamente')){
                limpiar()
                tblMatrices.ajax.reload()
                swal(resp, '', 'success')
            } else {
                swal(resp, '', 'error')
            }
        }
    })
}


// Función para listar las matrices
let matrices = () => {
    setTimeout(() => {   
        tblMatrices = $('#tblMatrices').dataTable({
            "aProcessing": true,//Activamos el procesamiento del datatables
            "aServerSide": true,//Paginación y filtrado realizados por el servidor
            dom: 'Bfrtip',//Definimos los elementos del control de tabla
            buttons: [	'copyHtml5', 'excelHtml5', 'csvHtml5' ],
            "ajax": {
                        url: '../Archivos/Presupuestos/pres_Matrices.php?op=matrices',
                        type : "post",
                        dataType : "json",
                        error: e => {
                            console.log("Error función matrices()\n"+e.responseText);	
                        }
                    },
            "bDestroy": true,
            "iDisplayLength": 20,//Paginación
            "order": [[ 0, "asc" ]]//Ordenar (columna,orden)
        }).DataTable()
    }, 50)
}


// Función para listar los materiales de la matriz
let matriz = (Cod) => {
    totalMat(Cod)
    $('#Cod').val(Cod)

    $.post('../Archivos/Presupuestos/pres_Matrices.php?op=mostrarMat',
        { Cod: Cod }, data => {
            data = JSON.parse(data)
            let Descripcion = data.Descripcion
            $('#modalLabel').html(Descripcion.replaceAll("&QUOT;", '"'))
        }
    )

    setTimeout(() => {
        tblMatriz = $('#tblMatriz').dataTable({
            "aProcessing": true,//Activamos el procesamiento del datatables
            "aServerSide": true,//Paginación y filtrado realizados por el servidor
            dom: 'Bfrtip',//Definimos los elementos del control de tabla
            buttons: [	'copyHtml5', 'excelHtml5', 'csvHtml5' ],
            "ajax": {
                        url: '../Archivos/Presupuestos/pres_Matrices.php?op=matriz',
                        type : "post",
                        dataType : "json",
                        data: { Cod: Cod },
                        error: e => {
                            console.log("Error función matrices()\n"+e.responseText);	
                        }
                    },
            "bDestroy": true,
            "iDisplayLength": 20,//Paginación
            "order": [[ 0, "asc" ]]//Ordenar (columna,orden)
        }).DataTable()
    }, 200)
}


// Función para mostrar datos del material
let mostrar = (Cod, Cve) => {
    $.post('../Archivos/Presupuestos/pres_Matrices.php?op=mostrar',
        {Cod: Cod, Cve: Cve}, data => {
            data = JSON.parse(data)

            let Descripcion = data.Descripcion

            $('#Cve_Mat').val(data.Cve)
            $('#Cve_Mat').selectpicker('refresh')
            $('#UMM').val(data.UM)
            $('#Cantidad').val(data.Cant)
            $('#PUM').val(data.PU)
            $('#Tipo').val(data.Tipo)

            $('#Concepto').val(Descripcion.replaceAll("&QUOT;", '"'))

            $.post('../Archivos/Presupuestos/pres_Matrices.php?op=mat',
                {Cve:Cve}, data => {
                    data = JSON.parse(data)
                    $('#Fecha_UC').html(data.Fecha_UC)
                }
            )
        }
    )
}


// Funcion para obtener la unidad de medida y costo
$('#Cve_Mat').change(function(e) {
    e.preventDefault()
    let Cve = $(this).val()

    // Obtenemos la unidad de medida y el precio unitario
    if(Cve.substring(0, 2) == 'MO'){
        moData(Cve)
    } else {
        $.post('../Archivos/Presupuestos/pres_Matrices.php?op=mat',
            {Cve:Cve}, data => {
                data = JSON.parse(data)

                $('#UMM').val(data.Desc_UM)
                $('#PUM').val(data.Cto_Ult)
                $('#Fecha_UC').html(data.Fecha_UC)
            }
        )
    }
    
    let desc = $('#Cve_Mat option:selected').data('subtext')
    $('#Concepto').val(desc)
})

let guardarMaterial = (e) => {
    e.preventDefault()
    let Cod = $('#Cod').val()
    let Cve = $('#Cve_Mat').val()
    let UM = $('#UMM').val()
    let Cant = $('#Cantidad').val()
    let PU = $('#PUM').val()
    let Tipo = Cve.substring(0, 2) == 'MO'? "MANO DE OBRA": "INSUMO";
    let Descripcion = $('#Concepto').val()

    $.post("../Archivos/Presupuestos/pres_Matrices.php?op=guardarMaterial",
        {
            Cod : Cod,
            Cve : Cve,
            UM : UM,
            Cant : Cant,
            PU : PU,
            Tipo : Tipo,
            Descripcion : Descripcion
        }, data => {
            if(data.includes('correctamente')){
                tblMatrices.ajax.reload()
                tblMatriz.ajax.reload()
                cleanModal()
                totalMat(Cod)
                swal(data, '', 'success')
            } else {
                swal(data, '', 'error')
            }
        }
    )
}


// Función pra limpiar el modal
let cleanModal = () => {
    $('#Tipo').val('')
    $('#Cve_Mat').val('')
    $('#Cve_Mat').selectpicker('refresh')
    $('#UMM').val('')
    $('#Cantidad').val('')
    $('#PUM').val('')
    $('#Total').val('')
    $('#Concepto').val('')
    $('#Fecha_UC').html('')
}


// Se cierra el modal
$('#modal').on('hidden.bs.modal', function (e) {
    cleanModal()
})


// Función para borrar materiales de las matrices
let deleteMat = (Cod, Cve) => {
    swal({
        title: "Se eliminara el registro selecionado",
        text: "",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#10ABB4',
        confirmButtonText: 'SI, ESTOY SEGURO',
        cancelButtonText: "NO, CANCELAR",
        closeOnConfirm: false,
        closeOnCancel: true
      },
      isConfirm => {
        if (isConfirm) { 
            $.post('../Archivos/Presupuestos/pres_Matrices.php?op=deleteMat',
                {Cod: Cod, Cve: Cve}, data => {
                    if(data.includes('correctamente')){
                        tblMatrices.ajax.reload()
                        tblMatriz.ajax.reload()
                        cleanModal()
                        totalMat(Cod)
                        swal(data, '', 'success')
                    } else {
                        swal(data, '', 'error')
                    }
                }
            )
        } 
    })
}


// Función para obtener el total de la matriz
let totalMat = (Cod) => {
    $.post('../Archivos/Presupuestos/pres_Matrices.php?op=totalMat',
        {Cod: Cod}, data => { $('#Total').val(data) }
    )
}

init()
