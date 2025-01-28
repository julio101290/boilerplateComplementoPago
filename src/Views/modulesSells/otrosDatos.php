<p>
<h3><?= lang('newSell.others') ?></h3>
<div class="row">


    <div class="col-6">
        <div class="form-group">
            <label for="quoteTo"><?= lang('newSell.quoteTo') ?>: </label>
            <input class="form-control" type="text" id='quoteTo' name='quoteTo'>

        </div>
    </div>

    <div class="col-3 ">
    </div>
    <div class="col-3 ">
        <div class="form-group">
            <label for="delivaryTime"><?= lang('newSell.deleveryTime') ?>: </label>
            <input class="form-control" type="text" id='delivaryTime' name='delivaryTime'>

        </div>
    </div>


</div>


<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label><?= lang('newSell.sellsObservations') ?>:</label>
            <textarea class="form-control" rows="3" placeholder="Observaciones" id="obsevations" name="obsevations" value="<?= $observations ?>"></textarea>
        </div>
    </div>
</div>


</p>