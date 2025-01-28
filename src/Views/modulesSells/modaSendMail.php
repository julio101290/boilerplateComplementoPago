<!-- Modal Vehicles -->
<div class="modal fade" id="modalSendMail" tabindex="-1" role="dialog" aria-labelledby="modalSendMail" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Envio Correo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-paciente" class="form-horizontal">

                    <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">Correos</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                                <select name="correos" id="correos" multiple="multiple" class="form-control correos" value="" placeholder="correos" autocomplete="off" style="width:80%;">
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">UUID</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                </div>
                                <input type="text" readonly="" name="uuidMail" id="uuidMail" class="form-control <?= session('error.uuidMail') ? 'is-invalid' : '' ?>" value="" placeholder="UUID Registro" autocomplete="off">
                            </div>
                        </div>
                    </div>




                    <div class="form-group row">
                        <label for="tireType" class="col-sm-2 col-form-label">Folio Venta</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-mobile"></i></span>
                                </div>
                                <input type="text" readonly="" name="folioVentaMail" id="folioVentaMail" class="form-control <?= session('error.folioVentaMail') ? 'is-invalid' : '' ?>" value="" placeholder="" autocomplete="off">

                            </div>
                        </div>
                    </div>




                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><?= lang('boilerplate.global.close') ?></button>
                <button type="button" class="btn btn-primary btn-sm btnSendMailConfirm" id="btnSendMailConfirm">Enviar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->section('js') ?>


<script>


    /* 
     * AL hacer click al editar
     */



    $(document).on('click', '.btnSendMailConfirm', function (e) {

        console.log("Enviar correo");

        $(".btnSendMailConfirm").attr("disabled", true);

        var uuid = $("#uuidMail").val();

        var correos = $("#correos").val();

        $.ajax({

            url: "<?= base_url('admin/mailSettings/sendMailVenta/') ?>" + uuid + '/' + correos,
            method: "GET",

            cache: false,
            contentType: false,
            processData: false,
            //dataType:"json",
            success: function (respuesta) {


                if (respuesta.match(/Correctamente.*/)) {


                    Toast.fire({
                        icon: 'success',
                        title: "Enviado Correctamente"
                    });



                    $(".btnSendMailConfirm").removeAttr("disabled");


                    $('#modalSendMail').modal('hide');
                } else {

                    Toast.fire({
                        icon: 'error',
                        title: respuesta
                    });

                    $(".btnSendMailConfirm").removeAttr("disabled");

                }

            }

        }

        )


    });


    $(".correos").select2({
        tags: true,
        tokenSeparators: [',', ' ']
    });

</script>


<?= $this->endSection() ?>