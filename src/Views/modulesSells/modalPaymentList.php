<!-- Modal Vehicles -->
<div class="modal fade" id="modalPayment" class="modalPayment" tabindex="-1" role="dialog" aria-labelledby="modalPayment" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Total a Pagar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-paciente" class="form-horizontal">

                    <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">Fecha Pago</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                </div>
                                <input type="date" name="datePayment" id="datePayment" class="form-control <?= session('error.datePayment') ? 'is-invalid' : '' ?>" value="<?php echo date('Y-m-d'); ?>" placeholder="datePayment" autocomplete="off">
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">Método de Pago</label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                </div>
                                <select name="metodoPago" id="metodoPago" class="form-control <?= session('error.uuidMail') ? 'is-invalid' : '' ?>" value="" placeholder="UUID Registro" autocomplete="off">
                                    <option value="1"> Efectivo</option>
                                    <option value="2"> Tarjeta</option>
                                    <option value="3"> Transferencia</option>

                                    <select>

                                        </div>
                                        </div>
                                        </div>


                                        <div class="form-group row">
                                            <label for="inputName" class="col-sm-2 col-form-label">Pago</label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                                    </div>
                                                    <input type="hidden" name="uuidSellPayment" id="uuidSellPayment" class="form-control <?= session('error.uuidSellPayment') ? 'is-invalid' : '' ?>" value="0.00" placeholder="uuidSellPayment" autocomplete="off">
                                                    <input type="hidden" name="granTotal" id="granTotal" class="form-control " value="0.00" autocomplete="off">
                                                    <input type="number" name="pago" id="pago" class="form-control <?= session('error.datePayment') ? 'is-invalid' : '' ?>" value="0.00" placeholder="datePayment" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="tireType" class="col-sm-2 col-form-label">Cambio</label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-hand-holding"></i></span>
                                                    </div>
                                                    <input type="number" readonly="" name="cambio" id="cambio" class="form-control <?= session('error.folioVentaMail') ? 'is-invalid' : '' ?>" value="0.00" placeholder="" autocomplete="off">

                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="tireType" class="col-sm-2 col-form-label">Observaciones</label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-hand-holding"></i></span>
                                                    </div>
                                                    <input type="text"  name="observacionesPago" id="observacionesPago" class="form-control " value="" placeholder="Observaciones pago" autocomplete="off">

                                                </div>
                                            </div>
                                        </div>



                                        </form>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm btnCerrar" data-dismiss="modal"><?= lang('boilerplate.global.close') ?></button>
                                            <button type="button" data-dismiss="modal"  class="btn btn-primary btn-sm btnSavePayment" id="btnSavePayment"  >Guardar</button>
                                        </div>
                                        </div>
                                        </div>
                                        </div>

                                        <?= $this->section('js') ?>


                                        <script>
                                            /**
                                             * Save Payment
                                             */


                                            $(".btnSavePayment").on("click", function () {


                                                savePayment();


                                            });




                                            function savePayment() {


                                                var UUID = $("#uuidSellPayment").val();
                                                var pago = $("#pago").val();
                                                var cambio = $("#cambio").val();
                                                var datePayment = $("#datePayment").val();
                                                var metodoPago = $("#metodoPago").val();
                                                var observaciones = $("#observacionesPago").val();





                                                $(".btnSavePayment").attr("disabled", true);


                                                var datos = new FormData();
                                                datos.append("UUID", UUID);
                                                datos.append("importPayment", pago);
                                                datos.append("importBack", cambio);
                                                datos.append("datePayment", datePayment);
                                                datos.append("metodoPago", metodoPago);
                                                datos.append("observaciones", observaciones);


                                                $.ajax({

                                                    url: "<?= base_url('admin/payments/save') ?>",
                                                    method: "POST",
                                                    data: datos,
                                                    cache: false,
                                                    contentType: false,
                                                    processData: false,
                                                    //dataType:"json",
                                                    success: function (respuesta) {


                                                        if (respuesta.match(/Correctamente.*/)) {


                                                            Toast.fire({
                                                                icon: 'success',
                                                                title: "Guardado Correctamente"
                                                            });

                                                            $(".btnSavePayment").removeAttr("disabled");



                                                            tableQuotes.ajax.reload();
                                                            $(".btnCerrar").trigger("click");

                                                        } else {

                                                            Toast.fire({
                                                                icon: 'error',
                                                                title: respuesta
                                                            });

                                                            $(".btnSavePayment").removeAttr("disabled");


                                                        }

                                                    }

                                                })


                                            }
                                            /*=============================================
                                             CAMBIO EN EFECTIVO
                                             =============================================*/
                                            $("#pago").on("keyup", function () {

                                                console.log("asdasd");


                                                var efectivo = $(this).val();
                                                var total = $("#granTotal").val();


                                                var cambio = Number(efectivo) - Number(total);

                                                if (Number(efectivo) < Number(total)) {
                                                    cambio = 0;
                                                }


                                                $("#cambio").val(cambio);



                                            })
                                        </script>


                                        <?= $this->endSection() ?>