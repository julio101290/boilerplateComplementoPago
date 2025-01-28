<?php

namespace julio101290\boilerplatesells\Controllers;

use App\Controllers\BaseController;
use julio101290\boilerplateproducts\Models\ProductsModel;
use \App\Models\UserModel;
use julio101290\boilerplatelog\Models\LogModel;
use julio101290\boilerplatequotes\Models\QuotesModel;
use julio101290\boilerplatesells\Models\SellsModel;
use julio101290\boilerplatestorages\Models\StoragesModel;
use julio101290\boilerplatesells\Models\SellsDetailsModel;
use CodeIgniter\API\ResponseTrait;
use julio101290\boilerplatecompanies\Models\EmpresasModel;
use julio101290\boilerplatecustumers\Models\CustumersModel;
use julio101290\boilerplatesells\Models\PaymentsModel;
use julio101290\boilerplatecomprobanterd\Models\Comprobantes_rdModel;
use julio101290\boilerplatevehicles\Models\VehiculosModel;
use julio101290\boilerplatedrivers\Models\ChoferesModel;
use julio101290\boilerplatevehicles\Models\TipovehiculoModel;
use julio101290\boilerplatebranchoffice\Models\BranchofficesModel;
use julio101290\boilerplatecashtonnage\Models\ArqueoCajaModel;
use julio101290\boilerplateinventory\Models\SaldosModel;
use julio101290\boilerplatesells\Models\EnlacexmlModel;
use julio101290\boilerplateCFDI\Models\XmlModel;
use julio101290\boilerplateCFDI\Controllers\XmlController;

class SellsController extends BaseController {

    use ResponseTrait;

    protected $log;
    protected $sells;
    protected $storages;
    protected $sellsDetail;
    protected $sucursales;
    protected $empresa;
    protected $user;
    protected $custumer;
    protected $payments;
    protected $products;
    protected $quotes;
    protected $comprobantesRD;
    protected $vehiculos;
    protected $choferes;
    protected $tiposVehiculo;
    protected $arqueoCaja;
    protected $saldos;
    protected $xmlEnlace;
    protected $enlaceXML;
    protected $xml;
    protected $xmlController;

    public function __construct() {
        $this->log = new LogModel();

        $this->sells = new SellsModel();
        $this->sellsDetail = new SellsDetailsModel();
        $this->empresa = new EmpresasModel();
        $this->user = new UserModel();
        $this->custumer = new CustumersModel();
        $this->payments = new PaymentsModel();
        $this->products = new ProductsModel();
        $this->quotes = new QuotesModel();
        $this->comprobantesRD = new Comprobantes_rdModel();
        $this->vehiculos = new VehiculosModel();
        $this->choferes = new ChoferesModel();
        $this->tiposVehiculo = new TipovehiculoModel();
        $this->sucursales = new BranchofficesModel();
        $this->arqueoCaja = new ArqueoCajaModel();
        $this->saldos = new SaldosModel();
        $this->xmlEnlace = new EnlacexmlModel();
        $this->enlaceXML = new EnlacexmlModel();
        $this->xml = new XmlModel();
        $this->xmlController = new XmlController();
        helper('menu');
        helper('utilerias');
    }

    public function index() {

        $auth = service('authentication');

        if (!$auth->check()) {

            return redirect()->route('admin');
        }


        helper('auth');

        $idUser = user()->id;

        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }


        if ($this->request->isAJAX()) {


            $datos = $this->sells->mdlGetSells($empresasID);

            return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);
        }




        $tiposVehiculo = $this->tiposVehiculo->mdlGetTipovehiculoArray($empresasID);

        $titulos["tiposVehiculo"] = $tiposVehiculo;
        $titulos["listaTitle"] = "Administracion de ventas";
        $titulos["listaSubtitle"] = "Muestra la lista de ventas";

        //$data["data"] = $datos;
        return view('julio101290\boilerplatesells\Views\sells', $titulos);
    }

    public function sellsFilters($desdeFecha, $hastaFecha, $todas, $empresa, $sucursal, $cliente) {


        $auth = service('authentication');
        if (!$auth->check()) {

            return redirect()->route('admin');
        }


        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }


        if ($this->request->isAJAX()) {


            $datos = $this->sells->mdlGetSellsFilters($empresasID, $desdeFecha, $hastaFecha, $todas, $empresa, $sucursal, $cliente);

            return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);
        }
    }

    public function sellsListFilters($desdeFecha, $hastaFecha, $todas, $empresa, $sucursal, $cliente) {


        $auth = service('authentication');
        if (!$auth->check()) {

            return redirect()->route('admin');
        }


        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }


        if ($this->request->isAJAX()) {


            $datos = $this->sells->mdlGetSellsFilters($empresasID, $desdeFecha, $hastaFecha, $todas, $empresa, $sucursal, $cliente);

            return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);
        }

        $titulos["desdeFecha"] = $desdeFecha;
        $titulos["hastaFecha"] = $hastaFecha;
        $titulos["todas"] = $todas;
        $titulos["empresa"] = $empresa;
        $titulos["sucursal"] = $sucursal;
        $titulos["cliente"] = $cliente;

        return view('sells', $titulos);
    }

    /**
     * 
     * @param type $desdeFecha
     * @param type $hastaFecha
     * @param type $todas
     * @return type
     * 
     * Get Report Sells per products
     */
    public function sellsReport($idEmpresa = 0
            , $idSucursal = 0
            , $idProducto = 0
            , $from
            , $to
            , $cliente) {


        $auth = service('authentication');
        if (!$auth->check()) {

            return redirect()->route('admin');
        }


        helper('auth');

        $idUser = user()->id;

        /**
         * Vemos las Empresa a la que tiene acceso
         */
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }


        /**
         * Vemos a las sucursales a las que tiene accesio
         */
        $sucursales = $this->sucursales->mdlSucursalesPorUsuario($idUser);

        if (count($sucursales) == "0") {

            $sucursalesID[0] = "0";
        } else {

            $sucursalesID = array_column($sucursales, "id");
        }


        if ($this->request->isAJAX()) {


            $datos = $this->sells->mdlVentasPorProductos($idEmpresa
                    , $idSucursal
                    , $idProducto
                    , $from
                    , $to
                    , $empresasID
                    , $sucursalesID
                    , $cliente);

            return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);
        }
    }

    public function generaPDFDesdeVenta($uuidVenta) {

        // buscamos el id de la venta

        $datosVenta = $this->sells->select("*")->where("UUID", $uuidVenta)->first();

        //Buscamo el uuid del xml en xml enlazados

        $enlaceXML = $this->enlaceXML->select("*")
                        ->where("idDocumento", $datosVenta["id"])
                        ->where("tipo", "ven")->first();

        $this->xmlController->generarPDF($enlaceXML["uuidXML"]);
    }

    public function newSell() {
        $auth = service('authentication');
        if (!$auth->check()) {

            return redirect()->route('admin');
        }

        helper('auth');
        $userName = user()->username;
        $idUser = user()->id;

        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }

        $authorize = $auth = service('authorization');
        $permisoAgregarArticulo = $authorize->hasPermission('capturaarticulodesdeventa', $idUser);

        $fechaActual = fechaMySQLADateHTML5(fechaHoraActual());

        $idMax = "0";

        $titulos["idMax"] = $idMax;
        $titulos["idSell"] = $idMax;
        $titulos["folio"] = "0";
        $titulos["fecha"] = $fechaActual;
        $titulos["userName"] = $userName;
        $titulos["idUser"] = $idUser;
        $titulos["contact"] = "";
        $titulos["idQuote"] = "0";
        $titulos["codeCustumer"] = "";
        $titulos["observations"] = "";
        $titulos["taxes"] = "0.00";
        $titulos["IVARetenido"] = "0.00";
        $titulos["ISRRetenido"] = "0.00";
        $titulos["subTotal"] = "0.00";
        $titulos["total"] = "0.00";
        $titulos["formaPago"] = $this->catalogosSAT->formasDePago40()->searchByField("texto", "%%", 99999);
        $titulos["usoCFDI"] = $this->catalogosSAT->usosCfdi40()->searchByField("texto", "%%", 99999);
        $titulos["metodoPago"] = $this->catalogosSAT->metodosDePago40()->searchByField("texto", "%%", 99999);
        $titulos["regimenFiscal"] = $this->catalogosSAT->regimenesFiscales40()->searchByField("texto", "%%", 99999);

        $titulos["RFCReceptor"] = "";
        $titulos["regimenFiscalReceptor"] = "";
        $titulos["usoCFDIReceptor"] = "";
        $titulos["metodoPagoReceptor"] = "";
        $titulos["formaPagoReceptor"] = "";
        $titulos["razonSocialReceptor"] = "";
        $titulos["codigoPostalReceptor"] = "";

        $titulos["permisoAgregarArticulo"] = $permisoAgregarArticulo;

        $titulos["folioComprobanteRD"] = "0";

        $titulos["uuid"] = generaUUID();

        $titulos["uuidRelacion"] = "";

        $tiposVehiculo = $this->tiposVehiculo->mdlGetTipovehiculoArray($empresasID);

        $titulos["title"] = "Nueva Venta"; //lang('registerNew.title');
        $titulos["subtitle"] = "Captura de Ventas"; // lang('registerNew.subtitle');
        $titulos["tiposVehiculo"] = $tiposVehiculo;

        $titulos["totalExento"] = "0";

        return view('julio101290\boilerplatesells\Views\newSell', $titulos);
    }

    public function reportSellsProducts() {
        $auth = service('authentication');
        if (!$auth->check()) {

            return redirect()->route('admin');
        }

        helper('auth');
        $userName = user()->username;
        $idUser = user()->id;

        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }




        $titulos["title"] = lang('newSell.sellsReportsTitle');
        $titulos["subtitle"] = lang('newSell.sellsReportsSubTitle');

        return view('julio101290\boilerplatesells\Views\reportSellsProducts', $titulos);
    }

    public function getXMLEnlazados($uuidVenta) {

        try {

            $datosVenta = $this->sells->select("*")->where("UUID", $uuidVenta)->first();

            if (isset($datosVenta)) {

                $datosXMLEnlazados = $this->enlaceXML->mdlGetEnlacexml2($datosVenta["id"]);

                return \Hermawan\DataTables\DataTable::of($datosXMLEnlazados)->toJson(true);
            } else {

                $datosXMLEnlazados = $this->enlaceXML->select("id,idDocumento,uuidXML,tipo,importe")->where("idDocumento", 0);
                return \Hermawan\DataTables\DataTable::of($datosXMLEnlazados)->toJson(true);
            }
        } catch (Exception $ex) {

            return $ex->getMessage();
        }
    }

    /**
     * Get Last Code
     */
    public function getLastCode() {

        $idEmpresa = $this->request->getPost("idEmpresa");
        $idSucursal = $this->request->getPost("idSucursal");
        $result = $this->sells->selectMax("folio")
                ->where("idEmpresa", $idEmpresa)
                ->where("idSucursal", $idSucursal)
                ->first();

        if ($result["folio"] == null) {

            $result["folio"] = 1;
        } else {

            $result["folio"] = $result["folio"] + 1;
        }

        echo json_encode($result);
    }

    /**
     * Get Last Code
     */
    public function getLastCodeInterno($idEmpresa, $idSucursal) {


        $result = $this->sells->selectMax("folio")
                ->where("idEmpresa", $idEmpresa)
                ->where("idSucursal", $idSucursal)
                ->first();

        if ($result["folio"] == null) {

            $result["folio"] = 1;
        } else {

            $result["folio"] = $result["folio"] + 1;
        }

        return $result["folio"];
    }

    /*
     * Editar Cotizacion
     */

    public function editSell($uuid) {

        helper('auth');
        $userName = user()->username;
        $idUser = user()->id;

        $auth = service('authentication');
        if (!$auth->check()) {

            return redirect()->route('admin');
        }


        $auth = service('authentication');
        if (!$auth->check()) {

            return redirect()->route('admin');
        }

        helper('auth');
        $userName = user()->username;
        $idUser = user()->id;

        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }

        $authorize = $auth = service('authorization');
        $permisoAgregarArticulo = $authorize->hasPermission('capturaarticulodesdeventa', $idUser);

        $sell = $this->sells->mdlGetSellUUID($uuid, $empresasID);

        $listProducts = json_decode($sell["listProducts"], true);

        $titulos["idSell"] = $sell["id"];
        $titulos["folio"] = $sell["folio"];
        $titulos["idCustumer"] = $sell["idCustumer"];
        $titulos["nameCustumer"] = $sell["nameCustumer"];
        $titulos["idEmpresa"] = $sell["idEmpresa"];
        $titulos["nombreEmpresa"] = $sell["nombreEmpresa"];

        $titulos["idUser"] = $idUser;
        $titulos["userName"] = $userName;
        $titulos["listProducts"] = $listProducts;
        $titulos["taxes"] = number_format($sell["taxes"], 2, ".");
        $titulos["IVARetenido"] = number_format($sell["IVARetenido"], 2, ".");
        $titulos["ISRRetenido"] = number_format($sell["ISRRetenido"], 2, ".");
        $titulos["subTotal"] = number_format($sell["subTotal"], 2, ".");
        $titulos["total"] = number_format($sell["total"], 2, ".");
        $titulos["fecha"] = $sell["date"];
        $titulos["dateVen"] = $sell["dateVen"];
        $titulos["quoteTo"] = $sell["quoteTo"];
        $titulos["observations"] = $sell["generalObservations"];
        $titulos["uuid"] = $sell["UUID"];
        $titulos["idQuote"] = $sell["idQuote"];
        $titulos["formaPago"] = $this->catalogosSAT->formasDePago40()->searchByField("texto", "%%", 99999);
        $titulos["usoCFDI"] = $this->catalogosSAT->usosCfdi40()->searchByField("texto", "%%", 99999);
        $titulos["metodoPago"] = $this->catalogosSAT->metodosDePago40()->searchByField("texto", "%%", 99999);
        $titulos["regimenFiscal"] = $this->catalogosSAT->regimenesFiscales40()->searchByField("texto", "%%", 99999);

        $titulos["RFCReceptor"] = $sell["RFCReceptor"];
        $titulos["regimenFiscalReceptor"] = $sell["regimenFiscalReceptor"];
        $titulos["usoCFDIReceptor"] = $sell["usoCFDI"];
        $titulos["metodoPagoReceptor"] = $sell["metodoPago"];
        $titulos["formaPagoReceptor"] = $sell["formaPago"];
        $titulos["razonSocialReceptor"] = $sell["razonSocialReceptor"];
        $titulos["codigoPostalReceptor"] = $sell["codigoPostalReceptor"];
        $titulos["permisoAgregarArticulo"] = $permisoAgregarArticulo;

        $titulos["totalExento"] = $sell["tasaCero"];

        $titulos["idVehiculo"] = $sell["idVehiculo"];

        $titulos["uuidRelacion"] = $sell["UUIDRelacion"];

        $datosVehiculo = $this->vehiculos->select("*")->where("id", $sell["idVehiculo"])->first();

        $titulos["vehiculoNombre"] = $sell["idVehiculo"];
        $datosVehiculo = $this->vehiculos->select("*")->where("id", $sell["idVehiculo"])->first();

        if (isset($datosVehiculo["descripcion"])) {

            $titulos["vehiculoNombre"] = $sell["tipoVehiculo"] . " " . $datosVehiculo["placas"] . " " . $datosVehiculo["descripcion"];
        } else {

            $titulos["vehiculoNombre"] = "Seleccione Vehiculo";
        }


        $titulos["idChofer"] = $sell["idChofer"];

        $datosChofer = $this->choferes->select("*")->where("id", $sell["idChofer"])->first();

        if (isset($datosChofer["nombre"])) {

            $titulos["choferNombre"] = $datosChofer["nombre"] . " " . $datosChofer["Apellido"];
        } else {

            $titulos["choferNombre"] = "Seleccione Chofer";
        }


        $titulos["tipoVehiculo"] = $sell["tipoVehiculo"];
        $tiposVehiculo = $this->tiposVehiculo->mdlGetTipovehiculoArray($empresasID);

        $titulos["tiposVehiculo"] = $tiposVehiculo;

        $titulos["idSucursal"] = $sell["idSucursal"];
        $sucursal = $this->sucursales->select("*")->where("id", $titulos["idSucursal"])->first();
        $titulos["nombreSucursal"] = $sucursal["key"] . " " . $sucursal["name"];

        if ($sell["tipoComprobanteRD"] > 0) {

            $comprobante = $this->comprobantesRD->find($sell["tipoComprobanteRD"]);
            $titulos["folioComprobanteRD"] = $sell["folioComprobanteRD"];
            $titulos["tipoComprobanteRDID"] = $comprobante["id"];
            $titulos["tipoComprobanteRDNombre"] = $comprobante["nombre"];
            $titulos["tipoComprobanteRDPrefijo"] = $comprobante["prefijo"];
        } else {

            $titulos["folioComprobanteRD"] = "0";
            $titulos["tipoComprobanteRDID"] = "0";
            $titulos["tipoComprobanteRDNombre"] = "0";
            $titulos["tipoComprobanteRDPrefijo"] = "0";
        }
        $titulos["title"] = "Editar Venta";
        $titulos["subtitle"] = "Edición de Ventas";

        return view('julio101290\boilerplatesells\Views\newSell', $titulos);
    }

    /*
     * Save or Update
     */

    public function save() {

        $auth = service('authentication');

        if (!$auth->check()) {
            $this->session->set('redirect_url', current_url());
            return redirect()->route('admin');
        }

        helper('auth');
        $userName = user()->username;
        $idUser = user()->id;

        $datos = $this->request->getPost();

        $this->sells->db->transBegin();

        $existsSell = $this->sells->where("UUID", $datos["UUID"])->countAllResults();

        $listProducts = json_decode($datos["listProducts"], true);

        $datosSucursal = $this->sucursales->find($datos["idSucursal"]);

        $datos["idArqueoCaja"] = 0;

        if ($datosSucursal["arqueoCaja"] == "on") {


            $datosArqueoCaja = $this->arqueoCaja->mdlObtenerIdArqueo($datos["idEmpresa"], $datos["idSucursal"], $datos["date"]);

            if (!isset($datosArqueoCaja["id"])) {


                $this->sells->db->transRollback();

                echo "No hay habilitado arqueo de caja";

                return;
            } else {


                $datos["idArqueoCaja"] = $datosArqueoCaja["id"];
            }
        }

        /**
         * if is new sell
         */
        if ($existsSell == 0) {


            $ultimoFolio = $this->getLastCodeInterno($datos["idEmpresa"], $datos["idSucursal"]);

            $empresa = $this->empresa->find($datos["idEmpresa"]);

            $comprobante = $this->comprobantesRD->find($datos["tipoComprobanteRD"]);

            if ($empresa["facturacionRD"] == "on") {


                if ($datos["tipoComprobanteRD"] == "") {

                    $this->sells->db->transRollback();

                    echo "No se selecciono tipo comprobante";
                    return;
                }


                if ($datos["folioComprobanteRD"] == "") {

                    $this->sells->db->transRollback();

                    echo "No hay folio Comprobante";
                    return;
                }


                if ($datos["folioComprobanteRD"] > $comprobante["folioFinal"]) {

                    $this->sells->db->transRollback();

                    echo "Se agotaron los folio son hasta  $comprobante[folioFinal] y van en $datos[folioComprobanteRD]";
                    return;
                }

                if ($datos["folioComprobanteRD"] < $comprobante["folioInicial"]) {

                    $this->sells->db->transRollback();

                    echo "Folio fuera de rango  $comprobante[folioInicial] y van en $datos[folioComprobanteRD]";
                    return;
                }


                if ($datos["date"] < $comprobante["desdeFecha"]) {

                    $this->sells->db->transRollback();

                    echo "fecha fuera de rango limite inferior $comprobante[desdeFecha] fecha venta $datos[date]";
                    return;
                }


                if ($datos["date"] > $comprobante["hastaFecha"]) {

                    $this->sells->db->transRollback();

                    echo "fecha fuera de rango,  limite superior $comprobante[desdeFecha]  fecha venta $datos[date]";
                    return;
                }
            }


            $datos["folio"] = $ultimoFolio;

            $datos["balance"] = $datos["total"] - ($datos["importPayment"] - $datos["importBack"]);

            try {


                if ($this->sells->save($datos) === false) {

                    $errores = $this->sells->errors();

                    $listErrors = "";

                    foreach ($errores as $field => $error) {

                        $listErrors .= $error . " ";
                    }

                    echo $listErrors;

                    return;
                }

                $idSellInserted = $this->sells->getInsertID();

                // save datail

                foreach ($listProducts as $key => $value) {

                    $datosDetalle["idSell"] = $idSellInserted;
                    $datosDetalle["idProduct"] = $value["idProduct"];
                    $datosDetalle["description"] = $value["description"];
                    $datosDetalle["unidad"] = $value["unidad"];
                    $datosDetalle["codeProduct"] = $value["codeProduct"];
                    $datosDetalle["cant"] = $value["cant"];
                    $datosDetalle["price"] = $value["price"];
                    $datosDetalle["porcentTax"] = $value["porcentTax"];

                    $datosDetalle["porcentIVARetenido"] = $value["porcentIVARetenido"];
                    $datosDetalle["porcentISRRetenido"] = $value["porcentISRRetenido"];
                    $datosDetalle["IVARetenido"] = $value["IVARetenido"];
                    $datosDetalle["ISRRetenido"] = $value["ISRRetenido"];

                    $datosDetalle["claveProductoSAT"] = $value["claveProductoSAT"];
                    $datosDetalle["claveUnidadSAT"] = $value["claveUnidadSAT"];

                    $datosDetalle["lote"] = $value["lote"];
                    $datosDetalle["idAlmacen"] = $value["idAlmacen"];

                    $datosDetalle["tax"] = $value["tax"];
                    $datosDetalle["total"] = $value["total"];
                    $datosDetalle["importeExento"] = $value["importeExento"];
                    $datosDetalle["neto"] = $value["neto"];

                    $datosDetalle["predial"] = $value["predial"];

                    //Valida Stock
                    $products = $this->products->find($datosDetalle["idProduct"]);

                    if ($products["validateStock"] == "on") {

                        if ($products["stock"] < $datosDetalle["cant"]) {

                            echo "Stock agotado en el producto " . $datosDetalle["description"];
                            $this->sellsDetail->db->transRollback();
                            return;
                        }
                    }

                    if ($products["inventarioRiguroso"] == "on") {

                        $datosSaldo["idEmpresa"] = $datos["idEmpresa"];
                        $datosSaldo["idAlmacen"] = $datosDetalle["idAlmacen"];
                        $datosSaldo["idProducto"] = $datosDetalle["idProduct"];
                        $datosSaldo["lote"] = $datosDetalle["lote"];

                        /**
                         * Verificamos saldo
                         */
                        $datosNuevosSaldo = $this->saldos->select("*")->where($datosSaldo)->first();

                        if ($datosNuevosSaldo["cantidad"] < $datosDetalle["cant"]) {

                            echo "Stock agotado en el producto " . $datosDetalle["description"];
                            $this->inventory->db->transRollback();
                            return;
                        }

                        $datosNuevosSaldo["cantidad"] = $datosNuevosSaldo["cantidad"] - $datosDetalle["cant"];

                        $existenciaProducto["stock"] = $products["stock"] - $datosDetalle["cant"];

                        if ($this->products->update($products["id"], $existenciaProducto) === false) {

                            echo "error al actualizar el saldo $datosDetalle[idProduct]";

                            $this->inventory->db->transRollback();
                            return;
                        }


                        if ($this->saldos->update($datosNuevosSaldo["id"], $datosNuevosSaldo) === false) {



                            $errores = $this->inventory->errors();

                            $listErrors = "";

                            foreach ($errores as $field => $error) {

                                $listErrors .= $error . " ";
                            }

                            echo $listErrors . " error al actualizar el saldo $datosDetalle[idProduct]";
                            ;

                            $this->inventory->db->transRollback();
                            return;
                        }
                    }


                    if ($this->sellsDetail->save($datosDetalle) === false) {

                        echo "error al insertar el producto $datosDetalle[idProducto]";

                        $this->sellsDetail->db->transRollback();
                        return;
                    } else {


                        if ($products["validateStock"] == "on") {

                            // ACTUALIZA STOCK
                            $newStock = $products["stock"] - $datosDetalle["cant"];

                            $updateDataStock["stock"] = $newStock;
                            if ($this->products->update($datosDetalle["idProduct"], $updateDataStock) === false) {

                                echo "error al actualizar el stock del producto $datosDetalle[idProducto]";

                                $this->sellsDetail->db->transRollback();
                                return;
                            }
                        }
                    }
                }


                if ($datos["idQuote"] > 0) {

                    echo "Inserted" . $idSellInserted;
                    $newSellQuote["idSell"] = $idSellInserted;

                    if ($this->quotes->update($datos["idQuote"], $newSellQuote) === false) {

                        echo "error al actualizar el stock del producto $datosDetalle[idProducto]";

                        $this->sellsDetail->db->transRollback();

                        return;
                    }
                }


                /**
                 * if Payments i mayor to cero
                 */
                if ($datos["importPayment"] > 0) {

                    $dataPayment["idSell"] = $idSellInserted;
                    $dataPayment["importPayment"] = $datos["importPayment"];
                    $dataPayment["importBack"] = $datos["importBack"];
                    $dataPayment["datePayment"] = $datos["datePayment"];
                    $dataPayment["metodPayment"] = $datos["metodoPago"];
                    $dataPayment["observaciones"] = $datos["observacionesPago"];

                    try {


                        if ($this->payments->save($dataPayment) === false) {

                            echo "error al insertar el pago ";

                            $this->sellsDetail->db->transRollback();
                            return;
                        }
                    } catch (\Exception $e) {


                        $this->sellsDetail->db->transRollback();
                        echo $e->getMessage();
                        return;
                    }
                }

                //ACTUALIZAMOS FOLIO ACTUAL COMPROBANTE

                if ($empresa["facturacionRD"] == "on") {

                    $comprobante = $this->comprobantesRD->find($datos["tipoComprobanteRD"]);

                    $folio = $comprobante["folioActual"] + 1;

                    $datosComprobante["folioActual"] = $folio;

                    if ($this->comprobantesRD->update($datos["tipoComprobanteRD"], $datosComprobante))
                        ;
                }


                $datosBitacora["description"] = "Se guardo la cotizacion con los siguientes datos" . json_encode($datos);
                $datosBitacora["user"] = $userName;

                $this->log->save($datosBitacora);

                $this->sellsDetail->db->transCommit();
                echo "Guardado Correctamente";
            } catch (\PHPUnit\Framework\Exception $ex) {


                echo "Error al guardar " . $ex->getMessage();
            }
        } else {




            $backSell = $this->sells->where("UUID", $datos["UUID"])->first();
            $listProductsBack = json_decode($backSell["listProducts"], true);

            //BUSCAMOS SI TIENE PAGOS

            $pagos = $this->payments->select("*")->where("idSell", $backSell["id"])->countAllResults();

            if ($pagos > 0) {

                echo "No se puede modificar ya que hay pagos enlazados, favor de eliminar los pagos primero";

                return;
            }

            $datos["folio"] = $backSell["folio"];

            $datos["balance"] = $datos["total"];

            if ($this->sells->update($backSell["id"], $datos) == false) {

                $errores = $this->sells->errors();
                $listError = "";
                foreach ($errores as $field => $error) {

                    $listError .= $error . " ";
                }

                echo $listError;

                return;
            } else {



                //DEJAMOS EL STOCK COMO ESTABA ANTES

                foreach ($listProductsBack as $key => $value) {

                    //BUSCAMOS STOCK DEL PRODUCTO
                    $products = $this->products->find($value["idProduct"]);

                    if ($products["validateStock"] == "on") {

                        // ACTUALIZA STOCK
                        $newStock = $products["stock"] + $value["cant"];

                        $updateDataStock["stock"] = $newStock;
                        if ($this->products->update($value["idProduct"], $updateDataStock) === false) {

                            echo "error al actualizar el stock del producto $value[idProducto]";

                            $this->sellsDetail->db->transRollback();
                            return;
                        }
                    }


                    /**
                     * Devolvemos el saldo 
                     */
                    if ($products["inventarioRiguroso"] == "on") {

                        //DEVOLVEMOS EL SALDO
                        $datosSaldo["idEmpresa"] = $backSell["idEmpresa"];
                        $datosSaldo["idAlmacen"] = $value["idAlmacen"];
                        $datosSaldo["idProducto"] = $value["idProduct"];
                        $datosSaldo["lote"] = $value["lote"];

                        $datosNuevosSaldo = $this->saldos->select("*")->where($datosSaldo)->first();

                        $datosNuevosSaldo["cantidad"] = $datosNuevosSaldo["cantidad"] + $value["cant"];

                        if ($this->saldos->update($datosNuevosSaldo["id"], $datosNuevosSaldo) === false) {

                            echo "error al actualizar el saldo $value[idProducto]";

                            $this->inventory->db->transRollback();
                            return;
                        }
                    }
                }

                $this->sellsDetail->select("*")->where("idSell", $backSell["id"])->delete();
                $this->sellsDetail->purgeDeleted();
                foreach ($listProducts as $key => $value) {

                    $datosDetalle["idSell"] = $backSell["id"];
                    $datosDetalle["idProduct"] = $value["idProduct"];
                    $datosDetalle["description"] = $value["description"];
                    $datosDetalle["unidad"] = $value["unidad"];
                    $datosDetalle["codeProduct"] = $value["codeProduct"];
                    $datosDetalle["cant"] = $value["cant"];
                    $datosDetalle["price"] = $value["price"];
                    $datosDetalle["porcentTax"] = $value["porcentTax"];

                    $datosDetalle["porcentIVARetenido"] = $value["porcentIVARetenido"];
                    $datosDetalle["porcentISRRetenido"] = $value["porcentISRRetenido"];
                    $datosDetalle["IVARetenido"] = $value["IVARetenido"];
                    $datosDetalle["ISRRetenido"] = $value["ISRRetenido"];

                    $datosDetalle["claveProductoSAT"] = $value["claveProductoSAT"];
                    $datosDetalle["claveUnidadSAT"] = $value["claveUnidadSAT"];
                    $datosDetalle["lote"] = $value["lote"];
                    $datosDetalle["idAlmacen"] = $value["idAlmacen"];

                    $datosDetalle["tax"] = $value["tax"];
                    $datosDetalle["total"] = $value["total"];
                    $datosDetalle["neto"] = $value["neto"];

                    if ($this->sellsDetail->save($datosDetalle) === false) {

                        $errores = $this->sellsDetail->errors();
                        $listError = "";
                        foreach ($errores as $field => $error) {

                            $listError .= $error . " ";
                        }

                        echo "error al insertar el producto $datosDetalle[idProduct] $errores";

                        $this->sells->db->transRollback();
                        return;
                    } else {


                        if ($products["validateStock"] == "on") {

                            $products = $this->products->find($value["idProduct"]);
                            if ($products["stock"] < $datosDetalle["cant"]) {

                                echo "Stock agotado en el producto " . $datosDetalle["description"];
                                $this->sellsDetail->db->transRollback();
                                return;
                            }
                            //BUSCAMOS STOCK DEL PRODUCTO
                            $products = $this->products->find($value["idProduct"]);
                            // ACTUALIZA STOCK
                            $newStock = $products["stock"] - $datosDetalle["cant"];

                            $updateDataStock["stock"] = $newStock;
                            if ($this->products->update($datosDetalle["idProduct"], $updateDataStock) === false) {

                                echo "error al actualizar el stock del producto $datosDetalle[idProducto]";

                                $this->sellsDetail->db->transRollback();
                                return;
                            }
                        }



                        /**
                         * Devolvemos el saldo 
                         */
                        if ($products["inventarioRiguroso"] == "on") {

                            //DEVOLVEMOS EL SALDO
                            $datosSaldo["idEmpresa"] = $datos["idEmpresa"];
                            $datosSaldo["idAlmacen"] = $datosDetalle["idAlmacen"];
                            $datosSaldo["idProducto"] = $datosDetalle["idProduct"];
                            $datosSaldo["lote"] = $datosDetalle["lote"];

                            $datosNuevosSaldo = $this->saldos->select("*")->where($datosSaldo)->first();

                            if ($datosNuevosSaldo["cantidad"] < $datosDetalle["cant"]) {

                                echo "No hay stock suficiente en el producto  $datosSaldo[idProduct]";
                                $this->inventory->db->transRollback();
                                return;
                            }



                            $datosNuevosSaldo["cantidad"] = $datosNuevosSaldo["cantidad"] - $datosDetalle["cant"];

                            if ($this->saldos->update($datosNuevosSaldo["id"], $datosNuevosSaldo) === false) {

                                echo "error al actualizar el saldo $value[idProducto]";

                                $this->inventory->db->transRollback();
                                return;
                            }
                        }
                    }
                }


                $datosBitacora["description"] = "Se actualizo" . json_encode($datos) .
                        " Los datos anteriores son" . json_encode($backSell);
                $datosBitacora["user"] = $userName;
                $this->log->save($datosBitacora);

                echo "Actualizado Correctamente";
                $this->sells->db->transCommit();
                return;
            }
        }

        return;
    }

    public function delete($id) {
        helper('auth');
        $userName = user()->username;
        $idUser = user()->id;

        $auth = service('authentication');
        if (!$auth->check()) {

            return redirect()->route('admin');
        }


        $auth = service('authentication');
        if (!$auth->check()) {

            return redirect()->route('admin');
        }

        helper('auth');
        $userName = user()->username;
        $idUser = user()->id;

        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }



        /**
         * 
         */
        if ($this->sells->select("*")->whereIn("idEmpresa", $empresasID)->where("id", $id)->countAllResults() == 0) {

            return $this->failNotFound('Acceso Prohibido');
        }








        $this->sells->db->transBegin();

        $infoSell = $this->sells->find($id);

        /**
         * Verificamos que no tenga enlazado XML
         */
        if ($this->xmlEnlace->select("*")->where("idDocumento", $infoSell["id"])->countAllResults() > 0) {

            $this->sells->db->transRollback();
            return $this->failNotFound('La Venta no se puede eliminar por que ya tiene timbre enlazado');
        }

        /**
         * Verificamos que no tenga Pagos Enlazados
         */
        if ($this->payments->select("*")->where("idSell", $infoSell["id"])->countAllResults() > 0) {

            $this->sells->db->transRollback();
            return $this->failNotFound('La Venta no se puede eliminar por que ya tiene pagos ');
        }


        if (!$found = $this->sells->delete($id)) {
            $this->sells->db->transRollback();
            return $this->failNotFound('Error al eliminar');
        }

        //Borramos quotesdetails

        if ($this->sellsDetail->select("*")->where("idSell", $id)->delete() === false) {

            $this->sellsDetail->db->transRollback();
            return $this->failNotFound('Error al eliminar el detalle');
        }

        $this->sellsDetail->purgeDeleted();

        $listProducts = json_decode($infoSell["listProducts"], true);
        $this->sells->purgeDeleted();

        //Devolvemos el Stock

        foreach ($listProducts as $key => $value) {

            $product = $this->products->find($value["idProduct"]);

            $stock = $product["stock"] + $value["cant"];

            $newStock["stock"] = $stock;

            if ($this->products->update($value["idProduct"], $newStock) === false) {

                $this->sells->db->transRollback();
                return $this->failNotFound('Error al actualizar el Stock');
            }



            /**
             * Devolvemos el saldo 
             */
            if ($product["inventarioRiguroso"] == "on") {

                //DEVOLVEMOS EL SALDO
                $datosSaldo["idEmpresa"] = $infoSell["idEmpresa"];
                $datosSaldo["idAlmacen"] = $value["idAlmacen"];
                $datosSaldo["idProducto"] = $value["idProduct"];
                $datosSaldo["lote"] = $value["lote"];

                $datosNuevosSaldo = $this->saldos->select("*")->where($datosSaldo)->first();

                $datosNuevosSaldo["cantidad"] = $datosNuevosSaldo["cantidad"] + $value["cant"];

                if ($this->saldos->update($datosNuevosSaldo["id"], $datosNuevosSaldo) === false) {

                    echo "error al actualizar el saldo $value[idProducto]";

                    $this->inventory->db->transRollback();
                    return;
                }
            }
        }


        $datosBitacora["description"] = 'Se elimino el Registro' . json_encode($infoSell);

        $this->log->save($datosBitacora);

        $this->sells->db->transCommit();
        return $this->respondDeleted($found, 'Eliminado Correctamente');
    }

    /**
     * Descarga XML
     */
    public function descargaAcuseCancelacion($uuid) {

        $datosXML = $this->xml->select("*")->where("uuidTimbre", $uuid)->find();

        $this->response->setHeader("Content-Type", "text/xml");
        echo $datosXML[0]["acuseCancelacion"];
    }

    /**
     * Funcion para enlazar venta con XML Put in Sells
     *      */
    public function enlazaVenta() {

        $auth = service('authentication');

        if (!$auth->check()) {
            $this->session->set('redirect_url', current_url());

            echo "No se ha iniciado Session";
            return;
        }

        helper('auth');
        $userName = user()->username;
        $idUser = user()->id;

        $request = service('request');
        $postData = $request->getPost();

        //Buscamos los datos de la venta
        $venta = $this->sells->select("*")->where("UUID", $postData["uuidVenta"])->first();

        $xml = $this->xml->select("*")->where("uuidTimbre", $postData["uuidTimbre"])->first();

        $datos["idDocumento"] = $venta["id"];
        $datos["uuidXML"] = $postData["uuidTimbre"];
        $datos["tipo"] = "ven";
        $datos["importe"] = $xml["total"];

        if ($this->enlaceXML->save($datos) === false) {

            $errores = $this->enlaceXML->errors();

            $listErrors = "";

            foreach ($errores as $field => $error) {

                $listErrors .= $error . " ";
            }

            echo $listErrors;

            return;
        }


        /**
         * Registramos en bitacora
         */
        $datosBitacora["description"] = "Se enlazo el XML $postData[uuidTimbre] con la venta $postData[uuidVenta]" . json_encode($datos);
        $datosBitacora["user"] = $userName;

        $this->log->save($datosBitacora);

        echo "Guardado Correctamente";
    }

    public function xmlSinAsignar($tipo) {


        helper('auth');
        $userName = user()->username;
        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }

        $empresasRFC = array_column($titulos["empresas"], "rfc");

        if ($this->request->isAJAX()) {
            $datos = $this->xml->mdlXMLSinAsignar($empresasID, $tipo);

            return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);
        }
    }

    /*

      public function delete($id) {

      if (!$found = $this->register->delete($id)) {
      return $this->failNotFound('Error al eliminar');
      }

      $infoConsukta = $this->register->find($id);

      $this->register->purgeDeleted();

      $datosBitacora["description"] = 'Se elimino el Registro' . json_encode($infoConsukta);

      $this->log->save($datosBitacora);
      return $this->respondDeleted($found, 'Eliminado Correctamente');
      }

      /**
     * Trae en formato JSON los pacientes para el select2
     * @return type
     */

    /*
      public function traerPacientesAjax() {

      $request = service('request');
      $postData = $request->getPost();

      $response = array();

      // Read new token and assign in $response['token']
      $response['token'] = csrf_hash();

      if (!isset($postData['searchTerm'])) {
      // Fetch record
      $pacientes = new PacientesModel();
      $listaPacientes = $pacientes->select('id,nombres,apellidos')
      ->orderBy('nombres')
      ->findAll(10);
      } else {
      $searchTerm = $postData['searchTerm'];

      // Fetch record
      $pacientes = new PacientesModel();
      $listaPacientes = $pacientes->select('id,nombres,apellidos')
      ->where("deleted_at", null)
      ->like('nombres', $searchTerm)
      ->orLike('apellidos', $searchTerm)
      ->orderBy('nombres')
      ->findAll(10);
      }

      $data = array();
      foreach ($listaPacientes as $paciente) {
      $data[] = array(
      "id" => $paciente['id'],
      "text" => $paciente['nombres'] . ' ' . $paciente['apellidos'],
      );
      }

      $response['data'] = $data;

      return $this->response->setJSON($response);
      } */

    /**
     * Reporte Consulta
     */
    public function report($uuid, $isMail = 0) {

        $pdf = new PDFLayoutSells(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $dataSells = $this->sells->where("uuid", $uuid)->first();

        $listProducts = json_decode($dataSells["listProducts"], true);

        $user = $this->user->where("id", $dataSells["idUser"])->first()->toArray();

        $custumer = $this->custumer->where("id", $dataSells["idCustumer"])->where("deleted_at", null)->first();

        $datosEmpresa = $this->empresa->select("*")->where("id", $dataSells["idEmpresa"])->first();
        $datosEmpresaObj = $this->empresa->select("*")->where("id", $dataSells["idEmpresa"])->asObject()->first();

        $pdf->nombreDocumento = lang('newSell.sellNote');
        $pdf->direccion = $datosEmpresaObj->direccion;

        if ($datosEmpresaObj->logo == NULL || $datosEmpresaObj->logo == "") {

            $pdf->logo = ROOTPATH . "public/images/logo/default.png";
        } else {

            $pdf->logo = ROOTPATH . "public/images/logo/" . $datosEmpresaObj->logo;
        }
        $pdf->folio = str_pad($dataSells["folio"], 5, "0", STR_PAD_LEFT);

        $folioConsulta = "Folio Consulta";
        $fecha = " Fecha: ";

        // set document information
        $pdf->nombreEmpresa = $datosEmpresa["nombre"];
        $pdf->direccion = $datosEmpresa["direccion"];
        $pdf->usuario = ""; //  $user["firstname"] . " " . $user["lastname"];
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($user["username"]);
        $pdf->SetTitle('CI4JCPOS');
        $pdf->SetSubject('CI4JCPOS');
        $pdf->SetKeywords('CI4JCPOS, PDF, PHP, CodeIgniter, CESARSYSTEMS.COM.MX');

        // set default header data
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // ---------------------------------------------------------
        // add a page
        $pdf->AddPage();

        $pdf->SetY(45);
        //ETIQUETAS
        $cliente = lang('newSell.custumer') . " ";
        $folioRegistro = lang('newSell.folio') . " ";
        $fecha = lang('newSell.date') . "";
        $fechaVencimiento = lang('newSell.expirationDate') . "";

        $atencionA = lang('newSell.quoteTo') . ":";
        $observaciones = lang('newSell.sellsObservations') . ":";
        $vendedor = lang('newSell.seller') . "";
        $vigencia = lang('newSell.validity') . "";
        $codigo = lang('newSell.fields.code') . "";
        $descripcion = lang('newSell.fields.description') . "";
        $cantidad = lang('newSell.fields.amount') . "";
        $precio = lang('newSell.fields.price') . "";
        $lblSubtotal = lang('newSell.subTotal') . "";
        $lblTotal = lang('newSell.fields.total') . "";

        $impuestos = lang('newSell.quoteTo') . "";
        $lblIvaRetenido = lang('newSell.VATWithholding') . "";
        $lblISRRetenido = lang('newSell.ISRWithholding') . "";
        $atencionA = lang('newSell.quoteTo') . "";

        $lblMsgThanks = lang('newSell.thanks');
        $lblMsgSellNote = lang('newSell.msgSellNote');
        $lblUUIDocument = lang('newSell.documendUUID');
        

        $pdf->SetY(45);
        //ETIQUETAS
        // set font
        //$pdf->SetFont('times', '', 12);

        if ($datosEmpresa["facturacionRD"] == "on" && $dataSells["folioComprobanteRD"] > 0) {


            $comprobante = $this->comprobantesRD->find($dataSells["tipoComprobanteRD"]);
            if ($comprobante["tipoDocumento"] == "COF") {
                $tipoDocumento = "FACTURA PARA CONSUMIDOR FINAL";
            }

            if ($comprobante["tipoDocumento"] == "CF") {
                $tipoDocumento = "FACTURA PARA CREDITO FISCAL";
            }

            $comprobanteFactura = $comprobante["prefijo"] . str_pad($dataSells["folioComprobanteRD"], 10, "0", STR_PAD_LEFT);
            $fechaVencimiento = "AUTORIZADO POR DGII :" . $comprobante["hastaFecha"];
        } else {

            $tipoDocumento = "";
            $comprobanteFactura = "";
            $fechaVencimiento = "";
        }

        $bloque2 = <<<EOF

    
        <table style="font-size:10px; padding:0px 10px;">
    
             <tr>
               <td style="width: 50%; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white;">$atencionA
               </td>
               <td style="width: 50%; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white;">$observaciones
               </td>
            </tr>
            <tr>
    
                <td >
    
    
                $cliente: $custumer[firstname] $custumer[lastname] 
    
                    <br>
                    Telefono: 000
                    <br>
                    E-Mail: $custumer[email]
                    <br>
                </td>
                <td >
                    $dataSells[generalObservations]
                    $tipoDocumento  <br>
                    $comprobanteFactura  <br>
                    $fechaVencimiento <br>
                </td>
    
    
            </tr>
    
            <tr>
    
                <td style="width: 25%; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white;">$vendedor
                </td>
    
                <td style="width: 24%; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white;">$fecha
                </td>
                <td style="width: 30%; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white;">$fechaVencimiento
                </td>
    
    
                <td style="width: 21%; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white;">$vigencia
                </td>
    
            </tr>
            <tr>
                    <td>
                        $user[firstname] $user[lastname]
                    </td>
                    <td>
                    $dataSells[date]
                    </td>
                    <td>
                    $dataSells[dateVen]
                    </td>
                    <td>
                    $dataSells[delivaryTime]
                    </td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid #666; background-color:white; width:640px"></td>
            </tr>
        </table>
    EOF;

        $pdf->writeHTML($bloque2, false, false, false, false, '');

        $bloque3 = <<<EOF

        <table style="font-size:10px; padding:5px 10px;">
    
            <tr>
    
            <td style="width: 100px; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white; text-align:center"> $codigo</td>
            <td style="width: 200px; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white; text-align:center"> $descripcion</td>
                     <td style="width: 60px; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white; text-align:center">$cantidad</td>
    
            <td style="width: 80px; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white; text-align:center">$precio</td>
            <td style="width: 100px; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white; text-align:center">$lblSubtotal</td>
            <td style="width: 100px; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white; text-align:center">$lblTotal</td>
    
            </tr>
    
        </table>
    
    EOF;

        $pdf->writeHTML($bloque3, false, false, false, false, '');

        $contador = 0;
        foreach ($listProducts as $key => $value) {



            if ($contador % 2 == 0) {
                $clase = 'style=" background-color:#ecf0f1; padding: 3px 4px 3px; ';
            } else {
                $clase = 'style="background-color:white; padding: 3px 4px 3px; ';
            }

            $precio = number_format($value["price"], 2, ".");
            $subTotal = number_format($value["total"], 2, ".");
            $total = number_format($value["neto"], 2, ".");
            $bloque4 = <<<EOF
    
        <table style="font-size:10px; padding:5px 10px;">
    
            <tr>
    
                <td  $clase width:100px; text-align:center">
                    $value[codeProduct]
                </td>
    
    
                <td  $clase width:200px; text-align:center">
                    $value[description]
                </td>
    
                <td $clase width:60px; text-align:center">
                    $value[cant]
                </td>
    
                <td $clase width:80px; text-align:right">
                    $precio
                </td>
    
                <td $clase width:100px; text-align:center">
                $subTotal
            </td>
    
                <td $clase width:100px; text-align:right">
                $total
                </td>
    
               
    
    
            </tr>
    
        </table>
    
    
    EOF;
            $contador++;
            $pdf->writeHTML($bloque4, false, false, false, false, '');
        }




        /**
         * TOTALES
         */
        $pdf->Setx(43);
        $subTotal = number_format($dataSells["subTotal"], 2, ".");
        $impuestos = number_format($dataSells["taxes"], 2, ".");
        $total = number_format($dataSells["total"], 2, ".");
        $IVARetenido = number_format($dataSells["IVARetenido"], 2, ".");
        $ISRRetenido = number_format($dataSells["ISRRetenido"], 2, ".");

        if ($IVARetenido > 0) {

            $bloqueIVARetenido = <<<EOF
                    <tr>
            
                    <td style="border-right: 0px solid #666; color:#333; background-color:white; width:340px; text-align:right"></td>
    
                    <td style="border: 0px solid #666; background-color:white; width:100px; text-align:right">
                   $lblIvaRetenido:
                    </td>
    
                    <td style="border: 0px solid #666; color:#333; background-color:white; width:100px; text-align:right">
                        $IVARetenido
                    </td>
    
                </tr>
    
            EOF;
        } else {

            $bloqueIVARetenido = "";
        }


        if ($ISRRetenido > 0) {

            $bloqueISRRetenido = <<<EOF
                    <tr>
            
                    <td style="border-right: 0px solid #666; color:#333; background-color:white; width:340px; text-align:right"></td>
    
                    <td style="border: 0px solid #666; background-color:white; width:100px; text-align:right">
                    $lblISRRetenido:
                    </td>
    
                    <td style="border: 0px solid #666; color:#333; background-color:white; width:100px; text-align:right">
                        $ISRRetenido
                    </td>
    
                </tr>
    
            EOF;
        } else {

            $bloqueISRRetenido = "";
        }





        $bloque5 = <<<EOF

      <table style="font-size:10px; padding:5px 10px;">
  
          <tr>
  
              <td style="color:#333; background-color:white; width:340px; text-align:right"></td>
  
              <td style="border-bottom: 0px solid #666; background-color:white; width:100px; text-align:right"></td>
  
              <td style="border-bottom: 0px solid #666; color:#333; background-color:white; width:100px; text-align:right"></td>
  
          </tr>
  
          <tr>
  
              <td style="border-right: 0px solid #666; color:#333; background-color:white; width:340px; text-align:right"></td>
  
              <td style="border: 0px solid #666;  background-color:white; width:100px; text-align:right">
              $lblSubtotal:
              </td>
  
              <td style="border: 0px solid #666; color:#333; background-color:white; width:100px; text-align:right">
                   $subTotal
              </td>
  
          </tr>
  
          <tr>
  
              <td style="border-right: 0px solid #666; color:#333; background-color:white; width:340px; text-align:right"></td>
  
              <td style="border: 0px solid #666; background-color:white; width:100px; text-align:right">
               IVA:
              </td>
  
              <td style="border: 0px solid #666; color:#333; background-color:white; width:100px; text-align:right">
                   $impuestos
              </td>
  
          </tr>
  
  
          $bloqueIVARetenido
          $bloqueISRRetenido
  
  
          <tr>
  
              <td style="border-right: 0px solid #666; color:#333; background-color:white; width:340px; text-align:right"></td>
  
              <td style="border: 0px solid #666; background-color:white; width:100px; text-align:right">
                  $lblTotal:
              </td>
  
              <td style="border: 0px solid #666; color:#333; background-color:white; width:100px; text-align:right">
                  $ $total
              </td>
  
          </tr>
  
  
      </table>
      <br>
      <div style="font-size:11pt;text-align:center;font-weight:bold">$lblMsgThanks!</div>
  <br><br>
                  
          <div style="font-size:8.5pt;text-align:left;font-weight:ligth">$lblUUIDocument: $dataSells[UUID]</div>
          
     
      <div style="font-size:8.5pt;text-align:left;font-weight:ligth">$lblMsgSellNote</div>
  
      
  
  
  EOF;

        $pdf->writeHTML($bloque5, false, false, false, false, 'R');

        if ($isMail == 0) {
            ob_end_clean();
            $this->response->setHeader("Content-Type", "application/pdf");
            $pdf->Output('notaVenta.pdf', 'I');
        } else {

            $attachment = $pdf->Output('notaVenta.pdf', 'S');

            return $attachment;
        }


        //============================================================+
        // END OF FILE
        //============================================================+
    }
}
