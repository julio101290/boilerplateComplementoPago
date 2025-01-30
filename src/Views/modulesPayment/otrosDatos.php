<p>
<h3>Otros Datos</h3>
<div class="row" hidden>


    <div class="col-6">
        <div class="form-group">
            <label for="quoteTo">Cotizar a: </label>
            <input class="form-control" type="text" id='quoteTo' name='quoteTo'>

        </div>
    </div>

    <div class="col-3 ">
    </div>
    <div class="col-3 ">
        <div class="form-group">
            <label for="delivaryTime">Tiempo de entrega: </label>
            <input class="form-control" type="text" id='delivaryTime' name='delivaryTime'>

        </div>
    </div>


</div>


<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?= lang("newPayment.paymentObservations")  ?></label>
            <textarea class="form-control" rows="3" placeholder="<?= lang("newPayment.paymentObservations")  ?>" id="obsevations" name="obsevations" value="<?= $observations ?>"></textarea>
        </div>
    </div>
</div>


</p>