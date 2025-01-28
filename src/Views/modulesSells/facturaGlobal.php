<p>
<h3><?= lang('newSell.globalInvoice') ?></h3>
<div class="row">

    <div class="col-3">
        <div class="form-group">
            <label for="quoteRFCReceptorTo">Es Factura Global: </label>
            <input type="checkbox" id="esFacturaGlobal" name="esFacturaGlobal" class="esFacturaGlobal" data-width="250" data-height="40" checked data-toggle="toggle" data-on="Si" data-off="No" data-onstyle="success" data-offstyle="danger">

        </div>
    </div>





</div>


<div class="row">


    <div class="col-4">
        <label for="emitidoRecibido" class="col-sm-3 col-form-label"><?= lang('newSell.period') ?></label>
        <div class="col-sm-9">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                </div>

                <select class="form-control periodicidad form-controlProducts" name="periodicidad" id="periodicidad" style="width:80%;">
                    <option value="0"><?= lang('newSell.SelectPeriod') ?></option>
                    <option value="01"><?= lang('newSell.periodDaily') ?></option>
                    <option value="02"><?= lang('newSell.periodWeekly') ?></option>  
                    <option value="03"><?= lang('newSell.periodFortnigtly') ?></option>
                    <option value="04"><?= lang('newSell.periodMonthly') ?></option>
                    <option value="05"><?= lang('newSell.periodBimonthly') ?></option>

                </select>

            </div>
        </div>
    </div>



    <div class="col-4">
        <label for="emitidoRecibido" class="col-sm-3 col-form-label"><?= lang('newSell.month') ?></label>
        <div class="col-sm-9">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                </div>

                <select class="form-control mes form-controlProducts" name="mes" id="mes" style="width:80%;">
                    <option value="0"><?= lang('newSell.selectMonth') ?></option>
                    <option value="01"><?= lang('newSell.selectMonth1') ?></option>
                    <option value="02"><?= lang('newSell.selectMonth2') ?></option>  
                    <option value="03"><?= lang('newSell.selectMonth3') ?></option>
                    <option value="04"><?= lang('newSell.selectMonth4') ?></option>
                    <option value="05"><?= lang('newSell.selectMonth5') ?></option>
                    <option value="06"><?= lang('newSell.selectMonth6') ?></option>
                    <option value="07"><?= lang('newSell.selectMonth7') ?></option>
                    <option value="08"><?= lang('newSell.selectMonth8') ?></option>
                    <option value="09"><?= lang('newSell.selectMonth9') ?></option>
                    <option value="10"><?= lang('newSell.selectMonth10') ?></option>
                    <option value="11"><?= lang('newSell.selectMonth11') ?></option>
                    <option value="12"><?= lang('newSell.selectMonth12') ?></option>
                    <option value="13"><?= lang('newSell.selectMonth13') ?></option>
                    <option value="14"><?= lang('newSell.selectMonth14') ?></option>
                    <option value="15"><?= lang('newSell.selectMonth15') ?></option>
                    <option value="16"><?= lang('newSell.selectMonth16') ?></option>
                    <option value="17"><?= lang('newSell.selectMonth17') ?></option>
                    <option value="18"><?= lang('newSell.selectMonth18') ?></option>

                </select>

            </div>
        </div>
    </div>


    <div class="col-4">
        <label for="emitidoRecibido" class="col-sm-3 col-form-label"><?= lang('newSell.year') ?></label>
        <div class="col-sm-9">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                </div>

                <input class="form-control anio form-controlProducts" name="anio" id="anio" style="width:80%;">

            </div>
        </div>
    </div>




</div>






</p>