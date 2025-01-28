<p>
<h3><?= lang('newSell.othersDataVehicle') ?></h3>
<div class="row">


    <div class="col-4">
        <div class="form-group">
            <label for="idVehiculo"><?= lang('newSell.vehiclePlate') ?>: </label>
            <select id='idVehiculoSell' name='idVehiculoSell' class="idVehiculoSell" style='width: 80%;'>

                <?php
                if (isset($idVehiculo)) {

                    echo "   <option value='$idVehiculo'>$idVehiculo - $vehiculoNombre</option>";
                } else {

                    echo "  <option value=''>Seleccione Vehiculo</option>";
                }
                ?>

            </select>
        </div>
    </div>

    <div class="col-4">
        <div class="form-group">
            <label for="idChofer"><?= lang('newSell.driver') ?>: </label>
            <select id='idChoferSell' name='idChoferSell' class="idChoferSell" style='width: 80%;'>

                <?php
                if (isset($idChofer)) {

                    echo "   <option value='$idChofer'>$idChofer - $choferNombre</option>";
                } else {

                    echo "  <option value=''>" . lang('newSell.selectDriver') . "</option>";
                }
                ?>

            </select>
        </div>
    </div>

    <div class="col-3 ">
    </div>
    <div class="col-3 ">
        <div class="form-group">
            <label for="tipoVehiculo"><?= lang('newSell.VehicleType') ?>: </label>


            <?php
            if (isset($idChofer)) {

                echo "   <input class=\"form-control\" type=\"text\" id='tipoVehiculo' value=\"$tipoVehiculo\" name='tipoVehiculo'>";
            } else {

                echo "   <input class=\"form-control\" type=\"text\" id='tipoVehiculo' name='tipoVehiculo'>";
            }
            ?>
        </div>
    </div>


</div>



<div class="row">

    <div class="col-6">
        <div class="form-group">



            <button class="btn btn-primary btnAddVehiculos" data-toggle="modal" data-target="#modalAddVehiculos"><i class="fa fa-plus"></i>

                <?= lang('newSell.newVehicle') ?>

            </button>

            <button class="btn btn-primary btnAddChoferes" data-toggle="modal" data-target="#modalAddChoferes"><i class="fa fa-plus"></i>

                <?= lang('newSell.newDriver') ?>

            </button>
        </div>
    </div>


</div>


</p>