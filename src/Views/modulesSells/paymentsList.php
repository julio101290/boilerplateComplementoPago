<!-- Modal Pacientes -->
<div class="modal fade" id="modalPaymentsList" tabindex="-1" role="dialog" aria-labelledby="modalPaymentsList"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Lista de Pagos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="table-payments" class="table table-striped table-hover va-middle tablePayments">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Fecha</th>
                                        <th>Importe Pagado</th>
                                        <th>Importe Devuelto</th>
                                        <th>Observaciones</th>
                                        <th>Tipo</th>
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


    var tableProducts = $('#table-payments').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        order: [[1, 'asc']],
        ajax: {
            url: '<?= base_url('admin/payments/getPayments') ?>/0',
            method: 'GET',
            dataType: "json"
        },
        columnDefs: [{
                orderable: false,
                targets: [6],
                searchable: false,
                targets: [6]

            }],
        columns: [{
                'data': 'id'
            },
            {
                'data': 'datePayment'
            },
            {
                'data': 'importPayment'
            },
            {
                'data': 'importBack'
            },
            {
                'data': 'observaciones'
            },

            {
                'data': 'tipo'
            },
            {
                "data": function (data) {
                    return `<td class="text-right py-0 align-middle">
                            <div class="btn-group btn-group-sm">
                                <button class="btn-danger btn-delete btnDeletePayment" data-id="${data.id}" ><i class="fas fa-trash"></i></button>
                            </div>
                            </td>`
                }
            }
        ]
    });
    /**
     * Eliminar Renglon Diagnostico
     */

    $("#table-payments").on("click", ".btnDeletePayment", function () {

        var idPago = $(this).attr("data-id");
        Swal.fire({
            title: '<?= lang('boilerplate.global.sweet.title') ?>',
            text: "<?= lang('boilerplate.global.sweet.text') ?>",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<?= lang('boilerplate.global.sweet.confirm_delete') ?>'
        })
                .then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: `<?= base_url('admin/payments/delete/') ?>/` + idPago,
                            method: 'GET',
                        }).done((data, textStatus, jqXHR) => {
                            Toast.fire({
                                icon: 'success',
                                title: jqXHR.statusText,
                            });
                            tableProducts.ajax.reload();
                            tableQuotes.ajax.reload();
                        }).fail((error) => {
                            Toast.fire({
                                icon: 'error',
                                title: error.responseJSON.messages.error,
                            });
                        })
                    }
                })
    })










</script>


<?= $this->endSection() ?>