<p>
<h3>Documentos Relacionados</h3>
<div class="row">


    <div class="col-3">
        <div class="form-group">
            <label for="metodoPagoVenta">Tipo Relacion </label>
            <select id='tipoDocumentoRelacionado' name='tipoDocumentoRelacionado' class="tipoDocumentoRelacionado" style='width: 100%;'>

                <option value="0" >00 Sin relación </option>
                <option value="01" >01 Nota de crédito de los documentos relacionados </option>
                <option value="02" >02 Nota de débito de los documentos relacionados </option>
                <option value="03" >03 Devolución de mercancía sobre facturas o traslados previos </option>
                <option value="04" >04 Sustitución de los CFDI previos </option>
                <option value="05" >05 Traslados de mercancias facturados previamente </option>
                <option value="06" >06 Factura generada por los traslados previos </option>
                <option value="07" >07 CFDI por aplicación de anticipo </option>

            </select>
        </div>
    </div>


    <div class="col-3">
        <div class="form-group">
            <label for="UUIDRelacion">UUID Relación: </label>
            <input class="form-control" type="text" id='UUIDRelacion' name='UUIDRelacion' value="<?= $uuidRelacion ?>">

        </div>
    </div>

</div>



</div>



</p>