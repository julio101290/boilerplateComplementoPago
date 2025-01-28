<p>
<h3>Datos Generales</h3>


<div class="row">


    <div class="col-lg-2  col-xs-12 col-md-2">
        <div class="form-group">
            <label for="datetime"><?= lang('newSell.date') ?></label>
            <input type="date" id="date" name="date" value="<?= $fecha ?>">
        </div>
    </div>
    <div class="col-4" col-lg-12>
        <div class="form-group">
            <label for="idEmpresaSells"><?= lang('newSell.companie') ?> </label>
            <select id='idEmpresaSells' name='idEmpresaSells' class="idEmpresaSells" style='width: 80%;'>

                <?php
                if (isset($idEmpresa)) {

                    echo "   <option value='$idEmpresa'>$idEmpresa - $nombreEmpresa</option>";
                } else {

                    echo "  <option value=''>".lang('newSell.selectCompanie')."</option>";

                    foreach ($empresas as $key => $value) {

                        echo "<option value='$value[id]'>$value[id] - $value[nombre] </option>  ";
                    }
                }
                ?>

            </select>
        </div>
    </div>



    <div class="col-lg-2  col-xs-12 col-md-2">
        <div class="form-group">
            <label for="idSucursal"><?= lang('newSell.branchoffice') ?> </label>
            <select id='idSucursal' name='idSucursal' class="idSucursal" style='width: 80%;'>

                <?php
                if (isset($idSucursal)) {

                    echo "   <option value='$idSucursal'>$idSucursal - $nombreSucursal</option>";
                }
                ?>

            </select>
        </div>
    </div>

   
    <div class="col-lg-4  col-xs-12 col-md-4 pull-right" style="
         text-align: right;
         ">
        <div class="form-group ">
            <label for="codeSell"><?= lang('newSell.folio') ?></label>


            <input type="text" id="codeSell" name="codeSell" disabled value="<?= $folio ?>">
        </div>
    </div>


</div>


<div class="row comprobantesRD" hidden>

    <div class="col-6">
        <div class="form-group">
            <label for="tipoComprobanteRD"><?= lang('newSell.typeVoucher') ?> </label>
            <select id='tipoComprobanteRD' name='tipoComprobanteRD' class="tipoComprobanteRD" style='width: 100%;'>

                <?php
                if (isset($tipoComprobanteRDID)) {

                    echo "   <option value='$tipoComprobanteRDID'>$tipoComprobanteRDPrefijo - $tipoComprobanteRDNombre</option>";
                } else {

                    echo "  <option value=''>".lang('newSell.selectTypeVoucher')."</option>";
                }
                ?>

            </select>
        </div>
    </div>


    <div class="col-6 pull-right" style="text-align: right;">
        <div class="form-group ">
            <label for="folioComprobanteRD"><?= lang('newSell.typeVoucher') ?></label>


            <input type="text" id="folioComprobanteRD" name="folioComprobanteRD" disabled value="<?= $folioComprobanteRD ?>">
        </div>
    </div>

</div>


<div class="row">

    <div class="col-lg-2  col-xs-12 col-md-2">
        <div class="form-group">
            <label for="custumerSell"><?= lang('newSell.custumer') ?> </label>
            <select id='custumerSell' name='custumerSell' class="custumerSell" style='width: 100%;'>

                <?php
                if (isset($idCustumer)) {

                    echo "   <option value='$idCustumer'>$idCustumer - $nameCustumer</option>";
                } else {

                    echo "  <option value=''>".lang('newSell.selectCustumer') ."</option>";
                }
                ?>

            </select>
        </div>
    </div>

    <div class="col-lg-1  col-xs-12 col-md-1">
        <div class="form-group">
            <label for="datetime"><?= lang('newSell.expirationDate') ?></label>
            <input type="date" id="dateVen" name="dateVen" value="<?= $fecha ?>">

            <input type="hidden" id="titulo" name="titulo" value="<?= $title ?>">
        </div>
    </div>

    <div class="col-3">

    </div>
    <div class="col-lg-6  col-xs-12 col-md-6 pull-right" style="
         text-align: right;
         ">



        <div class="form-group ">
            <label for="doctor">Realizada por </label>
            <input type="text" id="user" name="user" disabled value="<?= $userName ?>">
            <input type="hidden" id="idUser" name="idUser" value="<?= $idUser ?>">
            <input type="hidden" id="idRegister" name="idRegister" value="0">
            <input type="hidden" id="idQuote" name="idQuote" value="<?= $idQuote ?>">
            <input type="hidden" id="uuid" name="uuid" value="<?= $uuid ?>">
        </div>
    </div>

</div>

<div class="row">

    <div class="col-6">
        <div class="form-group">



            <button type="button" class="btn btn-default btnAddArticle" data-toggle="modal" data-target="#modalAddbtnAddArticle">Agregar Articulo</button>

            <?php if ($permisoAgregarArticulo) : ?>
                <button class="btn btn-primary btnAddProducts" data-toggle="modal" data-target="#modalAddProducts"><i class="fa fa-plus"></i>

                    <?= lang('newSell.addArticle') ?>

                </button>
            <?php endif; ?>

            <button class="btn btn-primary btnAddCustumers" data-toggle="modal" data-target="#modalAddCustumers"><i class="fa fa-plus"></i>

                <?= lang('newSell.newCustumer') ?>

            </button>
        </div>
    </div>


</div>
</p>