<?php
namespace julio101290\boilerplatecomplementopago\Controllers;

use App\Controllers\BaseController;
use julio101290\boilerplateproducts\Models\ProductsModel;
use \App\Models\UserModel;
use julio101290\boilerplatelog\Models\LogModel;
use julio101290\boilerplatequotes\Models\QuotesModel;
use julio101290\boilerplatesells\Models\SellsModel;
use julio101290\boilerplatecompanies\Models\EmpresasModel;
use julio101290\boilerplatestorages\Models\StoragesModel;
use julio101290\boilerplatesells\Models\SellsDetailsModel;
use CodeIgniter\API\ResponseTrait;
use julio101290\boilerplatecustumers\Models\CustumersModel;
use julio101290\boilerplatesells\Models\PaymentsModel;
use julio101290\boilerplatecomprobanterd\Models\Comprobantes_rdModel;
use julio101290\boilerplatevehicles\Models\VehiculosModel;
use julio101290\boilerplatedrivers\Models\ChoferesModel;
use julio101290\boilerplatevehicles\Models\TipovehiculoModel;
use julio101290\boilerplatebranchoffice\Models\BranchofficesModel;
use julio101290\boilerplatecashtonnage\Models\ArqueoCajaModel;
use julio101290\boilerplateinventory\Models\SaldosModel;
use julio101290\boilerplatecomplementopago\Models\PagosModel;
use julio101290\boilerplatesells\Models\EnlacexmlModel;

class PagosController extends BaseController {

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
    protected $pagos;
    protected $enlaceXML;

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
        $this->pagos = new PagosModel();
        $this->enlaceXML = new EnlacexmlModel();

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


            $datos = $this->pagos->mdlGetPagos($empresasID);

            return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);
        }


        $tiposVehiculo = $this->tiposVehiculo->mdlGetTipovehiculoArray($empresasID);

        $titulos["tiposVehiculo"] = $tiposVehiculo;
        $titulos["listaTitle"] = "Administracion de ventas";
        $titulos["listaSubtitle"] = "Muestra la lista de ventas";

        //$data["data"] = $datos;
        return view('julio101290\boilerplatecomplementopago\Views\pagos', $titulos);
    }

    public function pagosFilters($desdeFecha, $hastaFecha, $todas, $empresa, $sucursal, $cliente) {


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


            $datos = $this->pagos->mdlGetPagosFilters($empresasID, $desdeFecha, $hastaFecha, $todas, $empresa, $sucursal, $cliente);

            return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);
        }
    }
    
    
    
        /*
     * ZML ENLAZADOS POR DOCUMENTO
     */

    public function getXMLEnlazadosPagos($uuid) {

        try {

            $datosPagos = $this->pagos->select("*")->where("UUID", $uuid)->first();

            if (isset($datosPagos)) {

                $datosXMLEnlazados = $this->enlaceXML->select("id,idDocumento,uuidXML,tipo,importe")
                        ->where("idDocumento", $datosPagos["id"])
                        ->where("tipo", "pag");
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

    public function newPago() {
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

        $titulos["folioComprobanteRD"] = "0";

        $titulos["uuid"] = generaUUID();

        $tiposVehiculo = $this->tiposVehiculo->mdlGetTipovehiculoArray($empresasID);

        $titulos["title"] = "Complemento de Pago"; //lang('registerNew.title');
        $titulos["subtitle"] = "Captura de Complementos de pago"; // lang('registerNew.subtitle');
        $titulos["tiposVehiculo"] = $tiposVehiculo;

        return view('julio101290\boilerplatecomplementopago\Views\newPayment', $titulos);
    }



    /**
     * Get Last Code
     */
    public function getLastCode() {

        $idEmpresa = $this->request->getPost("idEmpresa");
        $idSucursal = $this->request->getPost("idSucursal");
        $result = $this->pagos->selectMax("folio")
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


        $result = $this->pagos->selectMax("folio")
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

    public function editPago($uuid) {

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


        $pago = $this->pagos->mdlGetPagoUUID($uuid, $empresasID);

        $listPagos = json_decode($pago["listPagos"], true);

        $titulos["idPago"] = $pago["id"];
        $titulos["folio"] = $pago["folio"];
        $titulos["idCustumer"] = $pago["idCustumer"];
        $titulos["nameCustumer"] = $pago["nameCustumer"];
        $titulos["idEmpresa"] = $pago["idEmpresa"];
        $titulos["nombreEmpresa"] = $pago["nombreEmpresa"];

        $titulos["idUser"] = $idUser;
        $titulos["userName"] = $userName;
        $titulos["listPagos"] = $listPagos;

        $titulos["total"] = number_format($pago["total"], 2, ".");
        $titulos["fecha"] = $pago["date"];
        $titulos["dateVen"] = $pago["dateVen"];
        $titulos["quoteTo"] = $pago["quoteTo"];
        $titulos["observations"] = $pago["generalObservations"];
        $titulos["uuid"] = $pago["UUID"];
        $titulos["formaPago"] = $this->catalogosSAT->formasDePago40()->searchByField("texto", "%%", 99999);
        $titulos["usoCFDI"] = $this->catalogosSAT->usosCfdi40()->searchByField("texto", "%%", 99999);
        $titulos["metodoPago"] = $this->catalogosSAT->metodosDePago40()->searchByField("texto", "%%", 99999);
        $titulos["regimenFiscal"] = $this->catalogosSAT->regimenesFiscales40()->searchByField("texto", "%%", 99999);

        $titulos["RFCReceptor"] = $pago["RFCReceptor"];
        $titulos["regimenFiscalReceptor"] = $pago["regimenFiscalReceptor"];
        $titulos["usoCFDIReceptor"] = $pago["usoCFDI"];
        $titulos["metodoPagoReceptor"] = $pago["metodoPago"];
        $titulos["formaPagoReceptor"] = $pago["formaPago"];
        $titulos["razonSocialReceptor"] = $pago["razonSocialReceptor"];
        $titulos["codigoPostalReceptor"] = $pago["codigoPostalReceptor"];

        $titulos["idSucursal"] = $pago["idSucursal"];
        $sucursal = $this->sucursales->select("*")->where("id", $titulos["idSucursal"])->first();
        $titulos["nombreSucursal"] = $sucursal["key"] . " " . $sucursal["name"];

        $titulos["title"] = "Editar Complemento de Pago";
        $titulos["subtitle"] = "Edición de complemento de pago";

        return view('julio101290\boilerplatecomplementopago\Views\newPayment', $titulos);
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

        $this->pagos->db->transBegin();

        $existsPago = $this->pagos->where("UUID", $datos["UUID"])->countAllResults();

        $listPagos = json_decode($datos["listPagos"], true);

        $datosSucursal = $this->sucursales->find($datos["idSucursal"]);

        //$datos["idArqueoCaja"] = 0;
        /*
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
         */
        /**
         * if is new sell
         */
        if ($existsPago == 0) {


            $ultimoFolio = $this->getLastCodeInterno($datos["idEmpresa"], $datos["idSucursal"]);

            $empresa = $this->empresa->find($datos["idEmpresa"]);

            $datos["folio"] = $ultimoFolio;

            try {


                if ($this->pagos->save($datos) === false) {

                    $errores = $this->pagos->errors();

                    $listErrors = "";

                    foreach ($errores as $field => $error) {

                        $listErrors .= $error . " ";
                    }

                    echo $listErrors;

                    return;
                }

                $idPaymentInserted = $this->pagos->getInsertID();

                // save datail

                foreach ($listPagos as $key => $value) {

                    if ($value["importeAPagar"] == 0) {

                        continue;
                    }

                    $pagosDetalle["idComplemento"] = $idPaymentInserted;
                    $pagosDetalle["idSell"] = $value["idSell"];
                    $pagosDetalle["importPayment"] = $value["importeAPagar"];
                    $pagosDetalle["importBack"] = "0.00";
                    $pagosDetalle["datePayment"] = $datos["date"];

                    if ($this->payments->save($pagosDetalle) === false) {

                        $errores = $this->payments->errors();

                        $listErrors = "";

                        foreach ($errores as $field => $error) {

                            $listErrors .= $error . " ";
                        }

                        echo $listErrors;

                        echo "error al insertar el pago $pagosDetalle[importPayment] ".$listErrors;

                        $this->pagos->db->transRollback();
                        return;
                    }

                    $obtenerVenta = $this->sells->where("id", $value["idSell"])->first();

                    $datosVenta["balance"] = $obtenerVenta["balance"];

                    $datosVenta["balance"] = $datosVenta["balance"] - ($pagosDetalle["importPayment"] - $pagosDetalle["importBack"]);

                    if ($this->sells->update($value["idSell"], $datosVenta) === false) {

                        echo "error al insertar el saldo de la venta $value[idSell]";

                        $this->pagos->db->transRollback();
                        return;
                    }
                }

                $datosBitacora["description"] = "Se guardo el pago con los siguientes datos" . json_encode($pagosDetalle);
                $datosBitacora["user"] = $userName;

                $this->log->save($datosBitacora);

                $this->sellsDetail->db->transCommit();
                echo "Guardado Correctamente";
            } catch (\PHPUnit\Framework\Exception $ex) {


                echo "Error al guardar " . $ex->getMessage();
            }
        } else {




            $backPago = $this->pagos->where("UUID", $datos["UUID"])->first();
            $listPagosBack = json_decode($backPago["listPagos"], true);

            $datos["folio"] = $backPago["folio"];

            if ($this->pagos->update($backPago["id"], $datos) == false) {

                $errores = $this->pago->errors();
                $listError = "";
                foreach ($errores as $field => $error) {

                    $listError .= $error . " ";
                }

                echo $listError;

                return;
            } else {



                //DEJAMOS EL STOCK COMO ESTABA ANTES

                foreach ($listPagosBack as $key => $value) {

                    //BUSCAMOS STOCK DEL PRODUCTO
                    $sell = $this->sells->find($value["idSell"]);

                    if ($value["importeAPagar"] == 0) {

                        continue;
                    }

                    $balanceBack["balance"] = $sell["balance"] + $value["importeAPagar"];

                    if ($this->sells->update($sell["id"], $balanceBack) == false) {

                        $errores = $this->sells->errors();
                        $listError = "";
                        foreach ($errores as $field => $error) {

                            $listError .= $error . " ";
                        }

                        echo $listError;

                        return;
                    }
                }

                $this->payments->select("*")->where("idComplemento", $backPago["id"])->delete();
                $this->payments->purgeDeleted();

                foreach ($listPagos as $key => $value) {

                    $pagosDetalle["idComplemento"] = $backPago["id"];
                    $pagosDetalle["idSell"] = $value["idSell"];
                    $pagosDetalle["importPayment"] = $value["importeAPagar"];
                    $pagosDetalle["importBack"] = "0.00";
                    $pagosDetalle["datePayment"] = $datos["date"];

                    if ($this->payments->save($pagosDetalle) === false) {

                        echo "error al insertar el pago $pagosDetalle[idSell]";

                        $this->pagos->db->transRollback();
                        return;
                    }

                    $datosVenta = $this->sells->select("*")->where("id", $value["idSell"])->first();

                    $nuevoBalance["balance"] = $datosVenta["balance"] - ($value["importeAPagar"]);

                    if ($this->sells->update($value["idSell"], $nuevoBalance) === false) {

                        echo "error al actualizar el saldo de la venta $datos[idSell]";

                        $this->pagos->db->transRollback();
                        return;
                    }
                }


                $datosBitacora["description"] = "Se hizo el pago" . json_encode($datos) .
                        " Los datos anteriores son" . json_encode($backPago);
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
         * Verificamos que no este timbrada
         * 
         */
        
        $enlace = $this->enlace
                        ->select("*")
                        ->where("idDocumento",$id)
                        ->where("tipo","pag")->countAllResults();
        
       
        if($enlace>0){
            
             return $this->failNotFound('No se puede eliminar, el complemento de pago, tiene timbres enlazados');
        }


        /**
         * 
         */
        if ($this->pagos->select("*")->whereIn("idEmpresa", $empresasID)->where("id", $id)->countAllResults() == 0) {

            return $this->failNotFound('Acceso Prohibido');
        }

        $this->pagos->db->transBegin();

        $infoPago = $this->pagos->find($id);

        if (!$found = $this->pagos->delete($id)) {
            $this->pagos->db->transRollback();
            return $this->failNotFound('Error al eliminar');
        }

        //Borramos quotesdetails

        if ($this->payments->select("*")->where("idComplemento", $id)->delete() === false) {

            $this->pagos->db->transRollback();
            return $this->failNotFound('Error al eliminar el detalle');
        }

        $this->pagos->purgeDeleted();

        $listPagos = json_decode($infoPago["listPagos"], true);
        $this->sells->purgeDeleted();

        //Devolvemos el Stock

        foreach ($listPagos as $key => $value) {

            $sell = $this->sells->select("*")->where("id", $value["idSell"])->first();

            $nuevoSaldo["balance"] = $sell["balance"] + $value["importeAPagar"];

            if ($this->sells->update($sell["id"], $nuevoSaldo) === false) {

                $this->pagos->db->transRollback();
                return $this->failNotFound('Error al corregir saldo');
            }
        }


        $datosBitacora["description"] = 'Se elimino el pago' . json_encode($listPagos);

        $this->log->save($datosBitacora);

        $this->sells->db->transCommit();
        return $this->respondDeleted($found, 'Eliminado Correctamente');
    }

    /**
     * Reporte Consulta
     */
    public function report($uuid, $isMail = 0) {

        $pdf = new PDFLayout(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $dataSells = $this->sells->where("uuid", $uuid)->first();

        $listProducts = json_decode($dataSells["listProducts"], true);

        $user = $this->user->where("id", $dataSells["idUser"])->first()->toArray();

        $custumer = $this->custumer->where("id", $dataSells["idCustumer"])->where("deleted_at", null)->first();

        $datosEmpresa = $this->empresa->select("*")->where("id", $dataSells["idEmpresa"])->first();
        $datosEmpresaObj = $this->empresa->select("*")->where("id", $dataSells["idEmpresa"])->asObject()->first();

        $pdf->nombreDocumento = "Nota De Venta";
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
        $pdf->usuario = $user["firstname"] . " " . $user["lastname"];
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
        $cliente = "Cliente: ";
        $folioRegistro = " Folio: ";
        $fecha = " Fecha:";

        $pdf->SetY(45);
        //ETIQUETAS
        $cliente = "Cliente: ";
        $folioRegistro = " Folio: ";
        $fecha = " Fecha:";

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
               <td style="width: 50%; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white;">ATENCION A
               </td>
               <td style="width: 50%; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white;">OBSERVACIONES
               </td>
            </tr>
            <tr>
    
                <td >
    
    
                Cliente: $custumer[firstname] $custumer[lastname] 
    
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
    
                <td style="width: 25%; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white;">VENDEDOR
                </td>
    
                <td style="width: 24%; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white;">FECHA
                </td>
                <td style="width: 30%; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white;">FECHA DE VENCIMIENTO
                </td>
    
    
                <td style="width: 21%; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white;">VIGENCIA
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
    
            <td style="width: 100px; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white; text-align:center">Código</td>
            <td style="width: 200px; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white; text-align:center">Descripción</td>
                     <td style="width: 60px; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white; text-align:center">Cant</td>
    
            <td style="width: 80px; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white; text-align:center">Precio</td>
            <td style="width: 100px; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white; text-align:center">SubTotal</td>
            <td style="width: 100px; background-color:#2c3e50; padding: 4px 4px 4px; font-weight:bold;  color:white; text-align:center">Total</td>
    
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
                    IVA Retenido:
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
                    ISR Retenido:
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
              Subtotal:
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
                  Total:
              </td>
  
              <td style="border: 0px solid #666; color:#333; background-color:white; width:100px; text-align:right">
                  $ $total
              </td>
  
          </tr>
  
  
      </table>
      <br>
      <div style="font-size:11pt;text-align:center;font-weight:bold">Gracias por su compra!</div>
  <br><br>
                  
          <div style="font-size:8.5pt;text-align:left;font-weight:ligth">UUID DOCUMENTO: $dataSells[UUID]</div>
          
     
      <div style="font-size:8.5pt;text-align:left;font-weight:ligth">ES RESPONSABILIDAD DEL CLIENTE REVISAR A DETALLE ESTA COTIZACION PARA SU POSTERIOR SURTIDO, UNA VEZ CONFIRMADA, NO HAY CAMBIOS NI DEVOLUCIONES.</div>
  
      
  
  
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

    public function obtenerFacturasPendientes() {

        $datos = $this->request->getPost();

        $empresa = $datos["idEmpresa"];
        $sucursal = $datos["idSucursal"];
        $cliente = $datos["idCustumers"];

        $listaFacturas = $this->pagos->mdlObtenerVentasFacturadasPendientesDePago($empresa, $sucursal, $cliente);
        $facturas = "";

        foreach ($listaFacturas as $key => $value) {

            $facturas .= <<<EOF
                <div class="form-group row nuevoProduct\">
                <div class ="col-1"> <button type="button" class="btn btn-danger quitProduct" ><span class="far fa-trash-alt"></span></button>
                <button type="button"  data-toggle="modal" data-target="#modelMoreInfoRow" class="btn btn-primary  btnInfo" ><span class="fa fa-fw fa-pencil-alt"></span></button> </div>
                <div class ="col-1"> <input disabled type="text" id="serie" class="form-control serie"  name="serie" value="$value[serie]" required=""> 
                <input disabled type="hidden" id="idSell" class="form-control idSell"  name="idSell" value="$value[id]" required="">    </div>
                <div class ="col-5"> <input disabled type="text" id="folio" class="form-control folio"  name="folio" value="$value[folio]" required=""> </div>
                <div class ="col-1"> <input disabled type="text" id="fecha" class="form-control fecha" name="fecha" value="$value[date]" =""></div>
                <div class ="col-1"> <input disabled type="text" id="price" class="form-control fechaVen" name="fechaVen" value="$value[dateVen]" required="">  </div>
                <div class ="col-1"> <input disabled type="text" id="total" class="form-control total" name="total" value="$value[total]" required=""> </div>
                <div class ="col-1"> <input disabled type="text" id="saldo" class="form-control saldo" name="total" value="$value[balance]" required=""> </div>        
                <div class ="col-1"> <input  type="number" id="importeAPagar" class="form-control importeAPagar" name="importeAPagar" value="0.00" required=""> </div></div>
            EOF;
        }

        echo $facturas;
    }
}
