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
                <form id="form-otrosDatosRenglon" class="form-horizontal">




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



                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><?= lang('boilerplate.global.close') ?></button>
                <button type="button" class="btn btn-primary btn-sm btnGuardarRenglon" id="btnGuardarRenglon"  data-dismiss="modal">Guardar</button>
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
        nombreDiv.parent().parent().find(".unidad").val($("#unidadProducto").val());

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
</script>


<?= $this->endSection() ?>