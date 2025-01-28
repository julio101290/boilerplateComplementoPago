<!-- Modal Pacientes -->
<div class="modal fade" id="modalListXML" tabindex="-1" role="dialog" aria-labelledby="modalListXML"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lista de XML Timbrados</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    <input type="hidden" id="idVentaSinAsignar" name="idVentaSinAsignar" value="">
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="table-listXML" class="table table-striped table-hover va-middle tableListXML">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha</th>
                                        <th>RFC</th>
                                        <th>Nombre Receptor</th>
                                        <th>UUID</th>
                                        <th>Serie</th>
                                        <th>folio</th>
                                        <th>total</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <?= lang('boilerplate.global.close') ?>
                </button>

            </div>
        </div>
    </div>
</div>



<?= $this->section('js') ?>


<script>


    var tableListXML = $('#table-listXML').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        order: [[1, 'asc']],
        ajax: {
            url: '<?= base_url('admin/xml/xmlSinAsignar/I') ?>',
            method: 'GET',
            dataType: "json"
        },
        columnDefs: [{
                orderable: false,
                targets: [8],
                searchable: false,
                targets: [8]

            }],
        columns: [{
                'data': 'id'
            },
            {
                'data': 'fecha'
            },
            {
                'data': 'rfcReceptor'
            },
            {
                'data': 'nombreReceptor'
            },
            {
                'data': 'uuidTimbre'
            },

            {
                'data': 'serie'
            },

            {
                'data': 'folio'
            },

            {
                'data': 'total'
            },

            {
                "data": function (data) {
                    return `<td class="text-right py-0 align-middle">
                            <div class="btn-group btn-group-sm">
                                <button class="btn-success btn-addXMLSell" data-id="${data.uuidTimbre}" ><i class="fas fa-plus-circle"></i></button>
                            </div>
                            </td>`
                }
            }
        ]
    });
    /**
     * Eliminar Renglon Diagnostico
     */

    $("#table-listXML").on("click", ".btn-addXMLSell", function () {

        var uuidTimbre = $(this).attr("data-id");
        var uuidVenta = $("#idVentaSinAsignar").val();
        
       

        var datos = new FormData();
        datos.append("uuidTimbre", uuidTimbre);
        datos.append("uuidVenta", uuidVenta);

        $.ajax({

            url: "<?= base_url('admin/xmlenlace/enlazaVenta') ?>",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            success: function (respuesta) {
                if (respuesta.match(/Correctamente.*/)) {

                    Toast.fire({
                        icon: 'success',
                        title: "Guardado Correctamente"
                    });

                    tableListXML.ajax.reload();
       


                   // $('#modalAddCustumers').modal('hide');
                } else {

                    Toast.fire({
                        icon: 'error',
                        title: respuesta
                    });

                    //$("#btnSaveCustumers").removeAttr("disabled");


                }

            }
        })
        
        
        
       
    });










</script>


<?= $this->endSection() ?>