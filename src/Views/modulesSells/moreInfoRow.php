<!-- Modal More Info -->
<div class="modal fade" id="modelMoreInfoRow" tabindex="-1" role="dialog" aria-labelledby="modelMoreInfoRow" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Información del Renglon</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


                <ul class="nav nav-tabs" id="myTab" role="tablist">

                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="sat-tab" data-toggle="tab" data-target="#datosSAT" type="button" role="tab" aria-controls="home" aria-selected="true">Datos SAT</button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="impuestos-tab" data-toggle="tab" data-target="#impuestos" type="button" role="tab" aria-controls="impuestos" aria-selected="false">Impuestos</button>
                    </li>



                </ul>
                <form id="form-otrosDatosRenglon" class="form-horizontal">


                    <div class="tab-content" id="myTabContent">
                        <div class="tab-content" id="myTabContent">


                            <!-- TAB PARA LOS DATOS DEL SAT -->
                            <div class="tab-pane fade show active" id="datosSAT" role="tabpanel" aria-labelledby="datosSAT">

                                <p>
                                <h3>Datos Generales</h3>
                                <div class="form-group row">
                                    <label for="unidadSATRow" class="col-sm-2 col-form-label">
                                        Clave Unidad SAT
                                    </label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                            </div>
                                            <select name="unidadSATRow" id="unidadSATRow" style="width: 90%;" class="form-control unidadSAT form-controlProducts">
                                                <option value="0" selected>
                                                    Seleccione Clave De Unidad SAT
                                                </option>
                                            </select>


                                        </div>
                                    </div>

                                </div>

                                <div class="form-group row">
                                    <label for="claveProductoSATRow" class="col-sm-2 col-form-label">
                                        Clave Producto SAT
                                    </label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                            </div>
                                            <select name="claveProductoSATRow" id="claveProductoSATRow" style="width: 90%;" class="form-control claveProductoSAT form-controlProducts">
                                                <option value="0" selected>
                                                    Seleccione Clave de Producto SAT
                                                </option>

                                            </select>


                                        </div>
                                    </div>

                                </div>




                                <div class="form-group row">
                                    <label for="claveProductoSATRow" class="col-sm-2 col-form-label">
                                        Unidad Producto
                                    </label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                            </div>
                                            <input name="unidadProducto" id="unidadProducto" class="form-control unidadProducto form-controlProducts">

                                        </div>
                                    </div>

                                </div>


                                <div class="form-group row">
                                    <label for="claveProductoSATRow" class="col-sm-2 col-form-label">
                                        Predial
                                    </label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                            </div>
                                            <input name="predialRenglon" id="predialRenglon" class="form-control predialRenglon form-controlProducts">

                                        </div>
                                    </div>

                                </div>

                                </p>

                            </div>

                            <!-- TAB PARA LOS IMPUESTOS -->

                            <div class="tab-pane fade" id="impuestos" role="tabpanel" aria-labelledby="impuestos">

                                <p>
                                <h3>Impuestos</h3>

                                <div class="form-group row">
                                    <label for="stock" class="col-sm-2 col-form-label">
                                        Tasa Exenta
                                    </label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <div class="input-group-prepend">

                                            </div>
                                            <input type="checkbox" id="tasaExcentaRenglon" name="tasaExcentaRenglon" class="tasaExcentaRenglon" data-width="250" data-height="40" checked data-toggle="toggle" data-on="Exenta" data-off="No exenta" data-onstyle="success" data-offstyle="danger">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="porcentTax" class="col-sm-2 col-form-label">
                                        <?= lang('products.fields.porcentTax') ?>
                                    </label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                            </div>
                                            <input type="number" name="porcentTaxRenglon" id="porcentTaxRenglon" class="form-control  form-controlProducts <?= session('error.porcentTax') ? 'is-invalid' : '' ?>" value="" placeholder="<?= lang('products.fields.porcentTax') ?>" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="porcentTax" class="col-sm-2 col-form-label">
                                        Iva Retenido
                                    </label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                            </div>
                                            <input type="number" name="porcentIVARetenidoRenglon" id="porcentIVARetenidoRenglon" class="form-control form-controlProducts <?= session('error.porcentIVARetenido') ? 'is-invalid' : '' ?>" value="" placeholder="Iva Retenido" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="porcentTax" class="col-sm-2 col-form-label">
                                        ISR Retenido
                                    </label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                            </div>
                                            <input type="number" name="porcentISRRetenidoRenglon" id="porcentISRRetenidoRenglon" class="form-control form-controlProducts <?= session('error.porcentISRRetenido') ? 'is-invalid' : '' ?>" value="" placeholder="ISR Retenido" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                </p>

                            </div>

                        </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><?= lang('boilerplate.global.close') ?></button>
                <button type="button" class="btn btn-primary btn-sm btnGuardarRenglon" id="btnGuardarRenglon"  data-dismiss="modal">Guardar</button>
            </div>
        </div>
    </div>
</div>
</div>
<?= $this->section('js') ?>


<script>
    /* 
     * AL hacer click al editar
     */



    $(document).on('click', '.btnGuardarRenglon', function (e) {


        nombreDiv.parent().parent().find(".claveUnidadSatR").val($("#unidadSATRow").val());
        nombreDiv.parent().parent().find(".claveProductoSATR").val($("#claveProductoSATRow").val());
        nombreDiv.parent().parent().find(".porcentIVARetenido").val($("#unidadProducto").val());

        /**
         * Impuestos
         */

        nombreDiv.parent().parent().find(".porcentTax").val($("#porcentTaxRenglon").val());
        nombreDiv.parent().parent().find(".porcentIVARetenido").val($("#porcentIVARetenidoRenglon").val());
        nombreDiv.parent().parent().find(".porcentISRRetenido").val($("#porcentISRRetenidoRenglon").val());

        nombreDiv.parent().parent().find(".predialR").val($("#predialRenglon").val());
        // nombreDiv.parent().parent().find(".claveUnidadSatR").val($("#porcentISRRetenidoRenglon").val());

        /*
         * Agregamos si lleva tasa Exenta
         */

        if ($("#tasaExcentaRenglon").is(':checked')) {

            var tasaExentaRenglon = "on";

        } else {

            var tasaExentaRenglon = "off";

        }


        nombreDiv.parent().parent().find(".tasaExcenta").val(tasaExentaRenglon);

        nombreDiv.parent().parent().find(".cant").trigger("change");

        listProducts();
    });



    /**
     * Categorias por empresa
     */

    $(".unidadSATRow").select2({
        ajax: {
            url: "<?= base_url('admin/products/getUnidadSATAjax') ?>",
            type: "post",
            dataType: 'json',
            delay: 250,

            data: function (params) {
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // CSRF Token name
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                var idEmpresa = $('.idEmpresa').val(); // CSRF hash

                return {
                    searchTerm: params.term, // search term
                    [csrfName]: csrfHash, // CSRF Token
                    idEmpresa: idEmpresa // search term
                };
            },
            processResults: function (response) {

                // Update CSRF Token
                $('.txt_csrfname').val(response.token);

                return {
                    results: response.data
                };
            },

            cache: true
        }
    });


    /**
     * Categorias por empresa
     */

    $(".claveProductoSATRow").select2({
        ajax: {
            url: "<?= base_url('admin/products/getProductosSATAjax') ?>",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // CSRF Token name
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                var idEmpresa = $('.idEmpresa').val(); // CSRF hash

                return {
                    searchTerm: params.term, // search term
                    [csrfName]: csrfHash, // CSRF Token
                    idEmpresa: idEmpresa // search term
                };
            },
            processResults: function (response) {

                // Update CSRF Token
                $('.txt_csrfname').val(response.token);

                return {
                    results: response.data
                };
            },
            cache: true
        }
    });

    /**
     *  Al cambiar
     */


</script>


<?= $this->endSection() ?>