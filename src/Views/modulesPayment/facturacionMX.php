<p>
<h3><?= lang("newPayment.invoiceData") ?></h3>
<div class="row">



    <div class="col-3">
        <div class="form-group">
            <label for="quoteRFCReceptorTo"><?= lang("newPayment.RFCReceiver") ?>: </label>
            <input class="form-control" type="text" id='RFCReceptor' name='RFCReceptor' value="<?=  $RFCReceptor  ?>">

        </div>
    </div>

    <div class="col-3">
        <div class="form-group">
            <label for="metodoPagoVenta"><?= lang("newPayment.useCFDI") ?> </label>
            <select id='usoCFDIVenta' name='usoCFDIVenta' class="usoCFDIVenta" style='width: 100%;'>

                <?php

                foreach ($usoCFDI as $key => $value) {

                    echo "<option value='" . $value->id() . "'>" . $value->id() . " - " . $value->texto();
                }

                ?>

            </select>
        </div>
    </div>



    <div class="col-3">
        <div class="form-group">
            <label for="metodoPagoVenta"><?= lang("newPayment.paymentMethod") ?>  </label>
            <select id='metodoPagoVenta' name='metodoPagoVenta' class="metodoPagoVenta" style='width: 100%;'>

                <?php

                foreach ($metodoPago as $key => $value) {

                    echo "<option value='" . $value->id() . "'>" . $value->id() . " - " . $value->texto();
                }

                ?>

            </select>
        </div>
    </div>

    <div class="col-3">
        <div class="form-group">
            <label for="formaPagoVenta"><?= lang("newPayment.paymentForm") ?> </label>
            <select id='formaPagoVenta' name='formaPagoVenta' class="formaPagoVenta" style='width: 100%;'>

                <?php

                foreach ($formaPago as $key => $value) {

                    echo "<option value='" . $value->id() . "'>" . $value->id() . " - " . $value->texto();
                }

                ?>

            </select>
        </div>
    </div>




</div>


<div class="row">



    <div class="col-3">
        <div class="form-group">
            <label for="quoteRFCReceptorTo"><?= lang("newPayment.receiverSocialReason") ?>: </label>
            <input class="form-control" type="text" id='razonSocialReceptor' name='razonSocialReceptor' value="<?=  $razonSocialReceptor  ?>">

        </div>
    </div>





    <div class="col-3">
        <div class="form-group">
            <label for="quoteRFCReceptorTo"><?= lang("newPayment.receiverPostalCode") ?>: </label>
            <input class="form-control" type="text" id='codigoPostalReceptor' name='codigoPostalReceptor' value="<?=  $codigoPostalReceptor  ?>">

        </div>
    </div>


    <div class="col-3">
        <div class="form-group">
            <label for="regimenFiscalReceptor"><?= lang("newPayment.receiverFiscalRegimen") ?> </label>
            <select id='regimenFiscalReceptor' name='regimenFiscalReceptor' class="regimenFiscalReceptor" style='width: 100%;'>

                <?php

                foreach ($regimenFiscal as $key => $value) {

                    echo "<option value='" . $value->id() . "'>" . $value->id() . " - " . $value->texto();
                }

                ?>

            </select>
        </div>
    </div>

</div>


</div>



</p>