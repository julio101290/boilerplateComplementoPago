<!-- Modal Pacientes -->
<div class="modal fade" id="modalAddbtnAddArticle" tabindex="-1" role="dialog" aria-labelledby="modalAddbtnAddArticle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccione Articulo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="table-products" class="table table-striped table-hover va-middle tableProducts">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Descripcion</th>
                                        <th>Lote</th>
                                        <th>Almacen</th>
                                        <th>Stock</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <?= lang('boilerplate.global.close') ?>
                </button>

            </div>
        </div>
    </div>
</div>



<?= $this->section('js') ?>


<script>
    var tableProducts = $('#table-products').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        order: [
            [1, 'asc']
        ],

        ajax: {
            url: '<?= base_url('admin/products/getAllProducts') ?>/0',
            method: 'GET',
            dataType: "json"
        },
        columnDefs: [{
                orderable: false,
                targets: [5],
                searchable: false,
                targets: [5]

            }],
        columns: [{
                'data': 'id'
            },
            {
                'data': 'description'
            },

            {
                'data': 'lote'
            },

            {
                'data': 'almacen'
            },

            {
                'data': 'stock'
            },

            {
                "data": function (data) {
                    return `<td class="text-right py-0 align-middle">
                            <div class="btn-group btn-group-sm">
                            <button class="btn bg-blue btnAddProduct" predial="${data.predial}" idAlmacen="${data.idAlmacen}" lote="${data.lote}"  data-id="${data.id}" unidad="${data.unidad}" claveProductoSAT="${data.claveProductoSAT}" unidadSAT="${data.unidadSAT}" porcentIVARetenido="${data.porcentIVARetenido}" porcentISRRetenido="${data.porcentISRRetenido}" porcentTax="${data.porcentTax}" code="${data.code}" price = "${data.salePrice}" tasaExcenta = "${data.tasaExcenta}" description = "${data.description}"><i class="fas fa-plus"></i></button>
                            </div>
                            </td>`
                }
            }
        ]
    });


    /**
     * Evento al hacer click al boton btnAgregarDiagnostico
     */


    $("#table-products").on("click", ".btnAddProduct", function () {



        var idProduct = $(this).attr("data-id");
        var description = $(this).attr("description");
        var codeProduct = $(this).attr("code");
        var salePrice = $(this).attr("price");
        var porcentTax = $(this).attr("porcentTax");
        var porcentIVARetenido = $(this).attr("porcentIVARetenido");
        var porcentISRRetenido = $(this).attr("porcentISRRetenido");
        var claveUnidadSAT = $(this).attr("unidadSAT");
        var unidad = $(this).attr("unidad");
        var claveProductoSAT = $(this).attr("claveProductoSAT");
        var lote = $(this).attr("lote");
        var idAlmacen = $(this).attr("idAlmacen");
        var tasaExcenta = $(this).attr("tasaExcenta");
        var predial = $(this).attr("predial");




        if (porcentTax > 0) {

            var tax = ((porcentTax * 0.01)) * salePrice;



        } else {

            var tax = 0;
        }

        if (porcentIVARetenido > 0) {

            var IVARetenido = ((porcentIVARetenido * 0.01)) * salePrice;
            $(".grupoTotalRetencionIVA").removeAttr("hidden");

        } else {

            var IVARetenido = 0;

        }

        if (porcentISRRetenido > 0) {

            var ISRRetenido = ((porcentISRRetenido * 0.01)) * salePrice;
            $(".grupoTotalRetencionISR").removeAttr("hidden");

        } else {

            var ISRRetenido = 0;

        }


        var neto = (((porcentTax * 0.01) + 1) * salePrice) - (IVARetenido + ISRRetenido);


        /**
         * Agregando registros
         */
        var renglon = "<div class=\"form-group row nuevoProduct\">";
        renglon = renglon + "<div class =\"col-1\"> <button type=\"button\" class=\"btn btn-danger quitProduct\" ><span class=\"far fa-trash-alt\"></span></button> ";
        renglon = renglon + " <button type=\"button\"  data-toggle=\"modal\" data-target=\"#modelMoreInfoRow\" class=\"btn btn-primary  btnInfo\" ><span class=\"fa fa-fw fa-pencil-alt\"></span></button> </div>";
        renglon = renglon + "<div class =\"d-none d-lg-table-cell col-1\"> <input type=\"hidden\" id=\"claveProductoSATR\" class=\"form-control claveProductoSATR\"  name=\"claveProductoSATR\" value=\"" + claveProductoSAT + "\" required=\"\">";
        renglon = renglon + "<input type=\"hidden\" id=\"lote\" class=\"form-control lote\"  name=\"lote\" value=\"" + lote + "\" required=\"\">";
        renglon = renglon + "<input type=\"hidden\" id=\"idAlmacen\" class=\"form-control idAlmacen\"  name=\"idAlmacen\" value=\"" + idAlmacen + "\" required=\"\">";
        renglon = renglon + "<input type=\"hidden\" id=\"claveUnidadSatR\" class=\"form-control claveUnidadSatR\"  name=\"claveUnidadSatR\" value=\"" + claveUnidadSAT + "\" required=\"\">";
        renglon = renglon + "<input type=\"hidden\" id=\"unidad\" class=\"form-control unidad\"  name=\"unidad\" value=\"" + unidad + "\" required=\"\">";
        renglon = renglon + "<input type=\"hidden\" id=\"tasaExcenta\" class=\"form-control tasaExcenta\"  name=\"tasaExcenta\" value=\"" + tasaExcenta + "\" required=\"\">";
        renglon = renglon + "<input type=\"hidden\" id=\"predialR\" class=\"form-control predialR\"  name=\"predialR\" value=\"" + predial + "\" required=\"\">";
        renglon = renglon + "<input type=\"text\" id=\"codeProduct\" class=\"form-control codeProduct\"  name=\"codeProduct\" value=\"" + codeProduct + "\" required=\"\"> </div>";
        renglon = renglon + "<div class =\"col-7\"> <input type=\"text\" id=\"description\" class=\"form-control description\" idProducto =\"" + idProduct + "\" name=\"description\" value=\"" + description + "\" required=\"\"> </div>";
        renglon = renglon + "<div class =\"col-1\"> <input type=\"number\" id=\"cant\" class=\"form-control cant\"name=\"cant\" value=\"1\" required=\"\"><input type=\"hidden\" id=\"porcentIVARetenido\" class=\"form-control porcentIVARetenido\"name=\"porcentIVARetenido\" value=\"" + porcentIVARetenido + "\" required=\"\"><input type=\"hidden\" id=\"porcentISRRetenido\" class=\"form-control porcentISRRetenido\"name=\"porcentISRRetenido\" value=\"" + porcentISRRetenido + "\" required=\"\"> <input type=\"hidden\" id=\"porcentTax\" class=\"form-control porcentTax\"name=\"porcentTax\" value=\"" + porcentTax + "\" required=\"\"></div>";
        renglon = renglon + "<div class =\"col-1\"> <input type=\"number\" id=\"price\" class=\"form-control price\"name=\"price\" value=\"" + salePrice + "\" required=\"\"> <input type=\"hidden\" id=\"IVARetenido\" class=\"form-control IVARetenido\"name=\"IVARetenido\" value=\"" + IVARetenido + "\" required=\"\"><input type=\"hidden\" id=\"ISRRetenido\" class=\"form-control ISRRetenido\"name=\"ISRRetenido\" value=\"" + ISRRetenido + "\" required=\"\"> <input type=\"hidden\" id=\"tax\" class=\"form-control tax\"name=\"tax\" value=\"" + tax + "\" required=\"\"> </div>";


        renglon = renglon + "<div class =\"col-1\"> <input readonly type=\"number\" id=\"total\" class=\"form-control total\" name=\"total\" value=\"" + salePrice + "\" required=\"\"> <input type=\"hidden\" id=\"neto\" class=\"form-control neto\" name=\"neto\" value=\"" + neto + "\" required=\"\"></div></div>";

        $(".rowProducts").append(renglon);



        listProducts();

        $('#modalAddbtnAddArticle').modal('toggle');

    });

    var nombreDiv = "";


    /**
     * Eliminar Renglon Diagnostico
     */

    $(".rowProducts").on("click", ".quitProduct", function () {

        $(this).parent().parent().remove();

        listProducts();

    });


    /**
     * Mas datos Producto
     */

    $(".rowProducts").on("click", ".btnInfo", function () {

        nombreDiv = $(this);

        var unidadSAT = $(this).parent().parent().find(".claveUnidadSatR").val();
        var claveProductoSAT = $(this).parent().parent().find(".claveProductoSATR").val();
        var unidad = $(this).parent().parent().find(".unidad").val();
        var tasaExcenta = $(this).parent().parent().find(".tasaExcenta").val();
        var predial = $(this).parent().parent().find(".predialR").val();


        var porcIva = $(this).parent().parent().find(".porcentTax").val();

        var porcentIVARetenido = $(this).parent().parent().find(".porcentIVARetenido").val();
        var porcentISRRetenido = $(this).parent().parent().find(".porcentISRRetenido").val();

        var newOption = new Option(unidadSAT, unidadSAT, true, true);
        $('#unidadSATRow').append(newOption).trigger('change');
        $("#unidadSATRow").val(unidadSAT);

        var newOptionClaveProducto = new Option(claveProductoSAT, claveProductoSAT, true, true);
        $('#claveProductoSATRow').append(newOptionClaveProducto).trigger('change');
        $("#claveProductoSATRow").val(claveProductoSAT);

        $("#unidadProducto").val(unidad);

        $("#porcentTaxRenglon").val(porcIva);
        $("#porcentIVARetenidoRenglon").val(porcentIVARetenido);
        $("#porcentISRRetenidoRenglon").val(porcentISRRetenido);

        $("#predialRenglon").val(predial);

        $("#tasaExcentaRenglon").bootstrapToggle(tasaExcenta);
        




    });




    /**
     * Eliminar Renglon Diagnostico
     */

    $(".rowProducts").on("click", ".quitProduct", function () {



        $(this).parent().parent().remove();

        listProducts();

    });



    /**
     * Cambia Cantidad
     */

    $(".rowProducts").on("change", ".cant", function () {


        var cant = Number($(this).val());



        precio = $(this).parent().parent().find(".price").val();

        total = Number(cant) * Number(precio);

        porcIva = Number($(this).parent().parent().find(".porcentTax").val()) * 0.01;

        porcIVARetenido = Number($(this).parent().parent().find(".porcentIVARetenido").val()) * 0.01;

        porcISRRetenido = Number($(this).parent().parent().find(".porcentISRRetenido").val()) * 0.01;

        impuesto = (porcIva) * Number(total);

        IVARetenido = (porcIVARetenido) * Number(total);

        ISRRetenido = (porcISRRetenido) * Number(total);

        neto = ((porcIva + 1) * Number(total)) - (IVARetenido + ISRRetenido);

        $(this).parent().parent().find(".total").val(total);

        $(this).parent().parent().find(".neto").val(neto);

        $(this).parent().parent().find(".tax").val(impuesto);

        $(this).parent().parent().find(".IVARetenido").val(IVARetenido);

        $(this).parent().parent().find(".ISRRetenido").val(ISRRetenido);

        listProducts();

    });


    /**
     * Cambia Cantidad
     */

    $(".rowProducts").on("change", ".price", function () {


        var precio = Number($(this).val());



        cant = $(this).parent().parent().find(".cant").val();

        total = Number(cant) * Number(precio);

        porcIva = Number($(this).parent().parent().find(".porcentTax").val()) * 0.01;

        porcIVARetenido = Number($(this).parent().parent().find(".porcentIVARetenido").val()) * 0.01;

        porcISRRetenido = Number($(this).parent().parent().find(".porcentISRRetenido").val()) * 0.01;


        IVARetenido = (porcIVARetenido) * Number(total);

        ISRRetenido = (porcISRRetenido) * Number(total);

        neto = ((porcIva + 1) * Number(total)) - (IVARetenido + ISRRetenido);

        impuesto = (porcIva) * Number(total);

        $(this).parent().parent().find(".total").val(total);

        $(this).parent().parent().find(".neto").val(neto);

        $(this).parent().parent().find(".tax").val(impuesto);

        $(this).parent().parent().find(".IVARetenido").val(IVARetenido);

        $(this).parent().parent().find(".ISRRetenido").val(ISRRetenido);

        listProducts();

    });
</script>


<?= $this->endSection() ?>