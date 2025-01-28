<!-- Modal Vehicles -->
<div class="modal fade" id="modalPayment" tabindex="-1" role="dialog" aria-labelledby="modalPayment" aria-hidden="true">
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
                                <input type="date"  name="datePayment" id="datePayment" class="form-control <?= session('error.datePayment') ? 'is-invalid' : '' ?>" value="<?php echo date('Y-m-d'); ?>" placeholder="datePayment" autocomplete="off">
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
                                <select  name="metodoPago" id="metodoPago" class="form-control <?= session('error.uuidMail') ? 'is-invalid' : '' ?>" value="" placeholder="UUID Registro" autocomplete="off">
                                    <option value="1"> Efectivo</option>
                                    <option value="1"> Tarjeta</option>

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
                                <input type="number"  name="pago" id="pago" class="form-control <?= session('error.datePayment') ? 'is-invalid' : '' ?>" value="0.00" placeholder="datePayment" autocomplete="off">
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




                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><?= lang('boilerplate.global.close') ?></button>
                <button type="button" class="btn btn-primary btn-sm btnSavePayment" id="btnSavePayment">Guardar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->section('js') ?>


<script>




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