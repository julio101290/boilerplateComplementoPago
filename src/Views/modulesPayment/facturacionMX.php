<p>
<h3>Datos Facturación</h3>
<div class="row">



    <div class="col-3">
        <div class="form-group">
            <label for="quoteRFCReceptorTo">RFC Receptor: </label>
            <input class="form-control" type="text" id='RFCReceptor' name='RFCReceptor' value="<?=  $RFCReceptor  ?>">

        </div>
    </div>

    <div class="col-3">
        <div class="form-group">
            <label for="metodoPagoVenta">Uso CFDI </label>
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
            <label for="metodoPagoVenta">Metodo de Pago </label>
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
            <label for="formaPagoVenta">Forma de Pago </label>
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
            <label for="quoteRFCReceptorTo">Razón Social Receptor: </label>
            <input class="form-control" type="text" id='razonSocialReceptor' name='razonSocialReceptor' value="<?=  $razonSocialReceptor  ?>">

        </div>
    </div>





    <div class="col-3">
        <div class="form-group">
            <label for="quoteRFCReceptorTo">Código Postal Receptor: </label>
            <input class="form-control" type="text" id='codigoPostalReceptor' name='codigoPostalReceptor' value="<?=  $codigoPostalReceptor  ?>">

        </div>
    </div>


    <div class="col-3">
        <div class="form-group">
            <label for="regimenFiscalReceptor">Regimen Fiscal Receptor </label>
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