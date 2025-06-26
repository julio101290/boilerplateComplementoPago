<?= $this->include('julio101290\boilerplate\Views\load/daterangapicker') ?>
<?= $this->include('julio101290\boilerplate\Views\load/toggle') ?>
<?= $this->include('julio101290\boilerplate\Views\load\datatables') ?>
<?= $this->include('julio101290\boilerplate\Views\load\select2') ?>


<!-- Extend from layout index -->
<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>


<!-- Section content -->
<?= $this->section('content') ?>

<?= $this->include('julio101290\boilerplatecomplementopago\Views\modulesPayment/modaSendMail') ?>


<?= $this->include('julio101290\boilerplatecomplementopago\Views\modulesPayment/listaFacturas') ?>
<?= $this->include('julio101290\boilerplatecomplementopago\Views\modulesPayment/xmlList') ?>


<!-- SELECT2 EXAMPLE -->
<div class="card card-default">
    <div class="card-header">

        <div class="float-left">
            <div class="btn-group">

                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                </div>


            </div>

            <div class="btn-group">



                <div class="form-group">
                    <label for="idEmpresa"><?= lang('payments.companie') ?></label>
                    <select id='idEmpresa' name='idEmpresa' class="idEmpresa" style='width: 80%;'>

                        <?php
                        if (isset($idEmpresa)) {

                            echo "   <option value='$idEmpresa'>$idEmpresa - $nombreEmpresa</option>";
                        } else {

                            echo "  <option value='0'>" . lang('payments.allCompanies') . "</option>";

                            foreach ($empresas as $key => $value) {

                                echo "<option value='$value[id]'>$value[id] - $value[nombre] </option>  ";
                            }
                        }
                        ?>

                    </select>
                </div>

            </div>


            <div class="btn-group">



                <div class="form-group">
                    <label for="idSucursal"><?= lang("payments.branchoffice") ?> </label>
                    <select id='idSucursal' name='idSucursal' class="idSucursal" style='width: 100%;'>

                        <?php
                        echo "  <option value='0'>" . lang('payments.AllBranchoffice') . "</option>";
                        if (isset($idSucursal)) {

                            echo "   <option value='$idSucursal'>$idSucursal - $nombreSucursal</option>";
                        }
                        ?>

                    </select>
                </div>

            </div>




            <div class="btn-group">



                <div class="form-group">
                    <label for="productos"><?= lang("payments.custumer") ?></label>
                    <select id='clientes' name='clientes' class="clientes" style='width: 100%;'>

                        <?php
                        echo "  <option value='0'>" . lang("payments.allCustumers") . "</option>";
                        ?>

                    </select>
                </div>

            </div>

            <div class="btn-group" hidden>



                <input type="checkbox" id="chkTodasLasVentas" name="chkTodasLasVentas" class="chkTodasLasVentas" data-width="250" data-height="40" checked data-toggle="toggle" data-on="Todas las ventas" data-off="Pendientes de Pago" data-onstyle="success" data-offstyle="danger">

            </div>


        </div>

        <div class="float-right">
            <div class="btn-group">

                <a href="<?= base_url("admin/newPago") ?>" class="btn btn-primary btnAddCustumers" data-target="#modalAddCustumers"><i class="fa fa-plus"></i>

                    <?= lang("payments.newPayment") ?>

                </a>

            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">

            <div class="col-md-12">

                <div class="table-responsive">

                    <table id="tablePagos" class="table table-striped table-hover va-middle tablePagos">

                        <thead>

                            <tr>

                                <th>#</th>
                                <th>
                                    <?= lang("payments.fields.folio") ?>
                                </th>
                                <th>
                                    <?= lang("payments.fields.custumer") ?>
                                </th>
                                <th>
                                    <?= lang("payments.fields.date") ?>
                                </th>

                                <th>
                                    <?= lang("payments.fields.total") ?>
                                </th>

                                <th>
                                    <?= lang("payments.fields.created_at") ?>
                                </th>
                                <th>
                                    <?= lang("payments.fields.updated_at") ?>
                                </th>

                                <th>
                                    <?= lang("payments.fields.actions") ?>
                                </th>

                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.card -->

<?= $this->endSection() ?>


<?= $this->section('js') ?>
<script>
    /**
     * Cargamos la tabla
     */

    var tableQuotes = $('#tablePagos').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        order: [
            [1, 'desc']
        ],

        ajax: {
            url: '<?= base_url('admin/pagos') ?>',
            method: 'GET',
            dataType: "json"
        },
        columnDefs: [{
                orderable: false,
                targets: [7],
                searchable: false,
                targets: [7]

            }],
        columns: [{
                'data': 'id'
            },

            {
                'data': 'folio'
            },
            {
                'data': 'nameCustumer'
            },

            {
                'data': 'date'
            },

            {
                'data': 'total'
            },

            {
                'data': 'created_at'
            },

            {
                'data': 'updated_at'

            },

            {
                "data": function (data) {



                    return `<td class="text-right py-0 align-middle">
                         <div class="btn-group btn-group-sm">
                             <a href="<?= base_url('admin/editPago') ?>/${data.UUID}" class="btn btn-primary btn-edit"><i class="fas fa-pencil-alt"></i></a>
                             <button class="btn btn-success btnSendMail" data-toggle="modal" correoCliente ="${data.correoCliente}" uuid="${data.UUID}" folio="${data.folio}" data-toggle="modal" data-target="#modalSendMail"  >  <i class=" fas fa-envelope"></i></button>
                             <button class="btn bg-warning btnImprimirVenta" uuid="${data.UUID}" ><i class="far fa-file-pdf"></i></button>
                             <button class="btn bg-maroon btnTimbrar" uuid="${data.UUID}" ><i class="fas fa-qrcode"></i></button>
                             <button class="btn btn-danger btn-delete" data-id="${data.id}"><i class="fas fa-trash"></i></button>
                           
                             <button class="btn bg-success btnInvoiceList" data-toggle="modal"  uuid="${data.UUID}" data-toggle="modal" data-target="#modalInvoiceList"  >  <i class="fas fa-search"></i></button>
                             <button class="btn bg-gray btnListXML" data-toggle="modal"  uuid="${data.UUID}" data-toggle="modal" data-target="#modalListXML"  >  <i class="fas fa-list"></i></button>
                         </div>
                         </td>`
                }
            }
        ]
    });




    $("#idEmpresa").select2();

    $("#idSucursal").select2({
        ajax: {
            url: "<?= site_url('admin/sucursales/getSucursalesAjax') ?>",
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

    $("#productos").select2({
        ajax: {
            url: "<?= site_url('admin/products/getProductsAjax') ?>",
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


    // Initialize select2 storages
    $("#clientes").select2({
        ajax: {
            url: "<?= site_url('admin/custumers/getCustumersTodosAjax') ?>",
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


    /**@abstract
     * 
     * Al cambiar la el rango de fecha
     */

    $("#chkTodasLasVentas").on("change", function () {

        var datePicker = $('#reportrange').data('daterangepicker');
        var desdeFecha = datePicker.startDate.format('YYYY-MM-DD');
        var hastaFecha = datePicker.endDate.format('YYYY-MM-DD');
        var idEmpresa = $("#idEmpresa").val();
        var idSucursal = $("#idSucursal").val();
        var idCliente = $("#clientes").val();


        if ($(this).is(':checked')) {

            todas = true;

        } else {

            todas = false;

        }

        tableQuotes.ajax.url(`<?= base_url('admin/pagos') ?>/` + desdeFecha + '/' + hastaFecha + '/' + todas + '/' + idEmpresa + '/' + idSucursal + '/' + idCliente).load();

    });







    /*=============================================
     Carga XML sin asignar
     =============================================*/

    $(".tablePagos").on("click", '.btnListXML', function () {


        var uuid = $(this).attr("uuid");

        $("#idVentaSinAsignar").val(uuid);

        tableListXML.ajax.url(`<?= base_url('admin/xml/xmlSinAsignar/p') ?>`).load();

    });


    /*=============================================
     Load invouce List 
     =============================================*/

    $(".tablePagos").on("click", '.btnInvoiceList', function () {


        var uuid = $(this).attr("uuid");

        console.log(uuid);

        tableInvoice.ajax.url(`<?= base_url('admin/xmlenlace/getXMLEnlazadosPagos') ?>/` + uuid).load();

    });


    /*=============================================
     ENVIAR CORREO  
     =============================================*/

    $(".tablePagos").on("click", '.btnSendMail', function () {

        var uuid = $(this).attr("uuid");
        var folio = $(this).attr("folio");
        var correo = $(this).attr("correocliente");


        var newOption = new Option(correo, correo);
        $('#correos').append(newOption).trigger('change');

        $("#uuidMail").val(uuid);
        $("#folioVentanMail").val(folio);


    });


    /*=============================================
     ENVIAR CORREO  
     =============================================*/

    $(".tablePagos").on("click", '.btnTimbrar', function () {

        var uuid = $(this).attr("uuid");



        timbrarComplemento(uuid);


    });


    /**
     * Imprimir factura desde la lista de facturas
     */

    $(".tableInvoice").on("click", '.btn-printInvoice', function () {

        var uuid = $(this).attr("data-id");
        window.open("<?= base_url('admin/xml/generarPDF') ?>" + "/" + uuid, "_blank");

    });

    /**
     * Imprimir factura desde la lista de facturas
     */

    $(".tableInvoice").on("click", '.btnDeleteEnlace', function () {

        var idEnlace = $(this).attr("data-id");
        Swal.fire({
            title: '<?= lang('boilerplate.global.sweet.title') ?>',
            text: "<?= lang('boilerplate.global.sweet.text') ?>",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<?= lang('boilerplate.global.sweet.confirm_delete') ?>'
        })
                .then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: `<?= base_url('admin/enlacexml/delete/') ?>/` + idEnlace,
                            method: 'GET',
                        }).done((data, textStatus, jqXHR) => {
                            Toast.fire({
                                icon: 'success',
                                title: jqXHR.statusText,
                            });
                            tableInvoice.ajax.reload();
                        }).fail((error) => {
                            Toast.fire({
                                icon: 'error',
                                title: error.responseJSON.messages.error,
                            });
                        })
                    }
                })

    });





    function timbrarComplemento(UUID) {





        $(".btnTimbrar").attr("disabled", true);



        $.ajax({

            url: "<?= base_url('admin/timbrarComplemento/') ?>" + UUID,
            method: "GET",
            cache: false,
            contentType: false,
            processData: false,
            //dataType:"json",
            success: function (respuesta) {


                if (respuesta.match(/success.*/)) {


                    Toast.fire({
                        icon: 'success',
                        title: "Timbrada Correctamente"
                    });

                    $(".btnTimbrar").removeAttr("disabled");


                    window.open("<?= base_url('admin/xml/generarPDFDesdeComplemento') ?>" + "/" + UUID, "_blank");
                    return true;

                } else {

                    Toast.fire({
                        icon: 'error',
                        title: respuesta
                    });

                    $(".btnTimbrar").removeAttr("disabled");

                    return false;


                }

            }

        }

        )

        return true;
    }







    /*=============================================
     IMPRIMIR VEnta
     =============================================*/

    $(".tablePagos").on("click", '.btnImprimirVenta', function () {

        var uuid = $(this).attr("uuid");


        window.open("<?= base_url('admin/pagos/report') ?>" + "/" + uuid, "_blank");

    });


    /*=============================================
     ELIMINAR custumers
     =============================================*/
    $(".tablePagos").on("click", ".btn-delete", function () {

        var idPago = $(this).attr("data-id");

        Swal.fire({
            title: '<?= lang('boilerplate.global.sweet.title') ?>',
            text: "<?= lang('boilerplate.global.sweet.text') ?>",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<?= lang('boilerplate.global.sweet.confirm_delete') ?>'
        })
                .then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: `<?= base_url('admin/pagos') ?>/` + idPago,
                            method: 'DELETE',
                        }).done((data, textStatus, jqXHR) => {
                            Toast.fire({
                                icon: 'success',
                                title: jqXHR.statusText,
                            });


                            tableQuotes.ajax.reload();
                        }).fail((error) => {
                            Toast.fire({
                                icon: 'error',
                                title: error.responseJSON.messages.error,
                            });
                        })
                    }
                })
    })





    $(function () {

        var start = moment().subtract(29, 'days');
        var end = moment();
        var todas = true;

        function cb(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

            if ($('#chkTodasLasVentas').is(':checked')) {

                todas = true;

            } else {

                todas = false;

            }



            var desdeFecha = start.format('YYYY-MM-DD');
            var hastaFecha = end.format('YYYY-MM-DD');
            var idEmpresa = $("#idEmpresa").val();
            var idSucursal = $("#idSucursal").val();
            var idCliente = $("#clientes").val();

            tableQuotes.ajax.url(`<?= base_url('admin/pagos') ?>/` + desdeFecha + '/' + hastaFecha + '/' + todas + '/' + idEmpresa + '/' + idSucursal + '/' + idCliente).load();


        }

        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Hoy': [moment(), moment()],
                'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Ultimos 7 Dias': [moment().subtract(6, 'days'), moment()],
                'Ultimos 30 Dias': [moment().subtract(29, 'days'), moment()],
                'Este Mes': [moment().startOf('month'), moment().endOf('month')],
                'Último Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Todo': [moment().subtract(100, 'year').startOf('month'), moment().add(100, 'year').endOf('year')]
            }
        }, cb);

        cb(start, end);





    });

</script>
<?= $this->endSection() ?>