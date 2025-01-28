<?php

namespace julio101290\boilerplatequotes\Controllers;

use App\Controllers\BaseController;
use App\Database\Migrations\Storages;
use \App\Models\UserModel;
use julio101290\boilerplatelog\Models\LogModel;
use julio101290\boilerplatequotes\Models\QuotesModel;
use julio101290\boilerplatestorages\Models\StoragesModel;
use julio101290\boilerplatequotes\Models\QuotesDetailsModel;
use CodeIgniter\API\ResponseTrait;
use julio101290\boilerplatecompanies\Models\EmpresasModel;
use julio101290\boilerplatecustumers\Models\CustumersModel;
use  julio101290\boilerplatebranchoffice\Models\BranchofficesModel;

class QuotesController extends BaseController {

    use ResponseTrait;

    protected $log;
    protected $Quotes;
    protected $storages;
    protected $quotesDetail;
    protected $empresa;
    protected $user;
    protected $custumer;
    protected $sucursales;

    public function __construct() {
        $this->log = new LogModel();

        $this->Quotes = new QuotesModel();
        $this->quotesDetail = new QuotesDetailsModel();
        $this->empresa = new EmpresasModel();
        $this->user = new UserModel();
        $this->custumer = new CustumersModel();
        $this->sucursales = new BranchofficesModel();

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


            $datos = $this->Quotes->mdlGetQuotes($empresasID);

            return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);
        }


        $titulos["listaTitle"] = "Administracion de cotizaciones";
        $titulos["listaSubtitle"] = "Muestra la lista de cotizaciones";

        //$data["data"] = $datos;
        return view('julio101290\boilerplatequotes\Views\quotes', $titulos);
    }

    public function quotesFilters($desdeFecha, $hastaFecha) {


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


            $datos = $this->Quotes->mdlGetQuotesFilters($empresasID, $desdeFecha, $hastaFecha);

            return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);
        }
    }

    /*
      public function showRegistersPerCustumers($custumer){

      $datos = $this->Quotes->select("id,date,uuid,contact")->where("custumer",$custumer);

      return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);

      }
     */
    /*
     * Nueva Requisicion
     */

    public function newQuote() {
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
        $titulos["idQuote"] = $idMax;
        $titulos["folio"] = "0";
        $titulos["fecha"] = $fechaActual;
        $titulos["userName"] = $userName;
        $titulos["idUser"] = $idUser;
        $titulos["contact"] = "";
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

        $titulos["uuid"] = generaUUID();

        $titulos["title"] = "Nueva Cotizacion"; //lang('registerNew.title');
        $titulos["subtitle"] = "Captura de Cotizaciones"; // lang('registerNew.subtitle');

        return view('julio101290\boilerplatequotes\Views\newQuote', $titulos);
    }

    /**
     * Get Last Code
     */
    public function getLastCode() {

        $idEmpresa = $this->request->getPost("idEmpresa");
        $idSucursal = $this->request->getPost("idSucursal");
        $result = $this->Quotes->selectMax("folio")
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


        $result = $this->Quotes->selectMax("folio")
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

    public function editQuote($uuid) {

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


        $quote = $this->Quotes->mdlGetQuoteUUID($uuid, $empresasID);

        $listProducts = json_decode($quote["listProducts"], true);

        $titulos["idQuote"] = $quote["id"];
        $titulos["folio"] = $quote["folio"];
        $titulos["idCustumer"] = $quote["idCustumer"];
        $titulos["nameCustumer"] = $quote["nameCustumer"];
        $titulos["idEmpresa"] = $quote["idEmpresa"];
        $titulos["nombreEmpresa"] = $quote["nombreEmpresa"];

        $titulos["idUser"] = $idUser;
        $titulos["userName"] = $userName;
        $titulos["listProducts"] = $listProducts;
        $titulos["taxes"] = number_format($quote["taxes"], 2, ".");
        $titulos["IVARetenido"] = number_format($quote["IVARetenido"], 2, ".");
        $titulos["ISRRetenido"] = number_format($quote["ISRRetenido"], 2, ".");
        $titulos["subTotal"] = number_format($quote["subTotal"], 2, ".");
        $titulos["total"] = number_format($quote["total"], 2, ".");
        $titulos["fecha"] = $quote["date"];
        $titulos["dateVen"] = $quote["dateVen"];
        $titulos["quoteTo"] = $quote["quoteTo"];
        $titulos["observations"] = $quote["generalObservations"];

        $titulos["formaPago"] = $this->catalogosSAT->formasDePago40()->searchByField("texto", "%%", 99999);
        $titulos["usoCFDI"] = $this->catalogosSAT->usosCfdi40()->searchByField("texto", "%%", 99999);
        $titulos["metodoPago"] = $this->catalogosSAT->metodosDePago40()->searchByField("texto", "%%", 99999);
        $titulos["regimenFiscal"] = $this->catalogosSAT->regimenesFiscales40()->searchByField("texto", "%%", 99999);

        $titulos["RFCReceptor"] = $quote["RFCReceptor"];
        $titulos["regimenFiscalReceptor"] = $quote["regimenFiscalReceptor"];
        $titulos["usoCFDIReceptor"] = $quote["usoCFDI"];
        $titulos["metodoPagoReceptor"] = $quote["metodoPago"];
        $titulos["formaPagoReceptor"] = $quote["formaPago"];
        $titulos["razonSocialReceptor"] = $quote["razonSocialReceptor"];
        $titulos["codigoPostalReceptor"] = $quote["codigoPostalReceptor"];
        $titulos["idSucursal"] = $quote["idSucursal"];
        $sucursal = $this->sucursales->select("*")->where("id", $titulos["idSucursal"])->first();
        $titulos["nombreSucursal"] = $sucursal["key"] . " " . $sucursal["name"];

        $titulos["uuid"] = $quote["UUID"];
        $titulos["title"] = "Editar Cotizacion";
        $titulos["subtitle"] = "Edición de cotizaciones";

        return view('julio101290\boilerplatequotes\Views\newQuote', $titulos);
    }

    public function convertQuote($uuid) {

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

        $quote = $this->Quotes->mdlGetQuoteUUID($uuid, $empresasID);

        $listProducts = json_decode($quote["listProducts"], true);

        $titulos["idQuote"] = $quote["id"];
        $titulos["folio"] = $quote["folio"];
        $titulos["idCustumer"] = $quote["idCustumer"];
        $titulos["nameCustumer"] = $quote["nameCustumer"];
        $titulos["idEmpresa"] = $quote["idEmpresa"];
        $titulos["nombreEmpresa"] = $quote["nombreEmpresa"];

        $titulos["idUser"] = $idUser;
        $titulos["userName"] = $userName;
        $titulos["listProducts"] = $listProducts;
        $titulos["taxes"] = number_format($quote["taxes"], 2, ".");
        $titulos["IVARetenido"] = number_format($quote["IVARetenido"], 2, ".");
        $titulos["ISRRetenido"] = number_format($quote["ISRRetenido"], 2, ".");
        $titulos["subTotal"] = number_format($quote["subTotal"], 2, ".");
        $titulos["total"] = number_format($quote["total"], 2, ".");
        $titulos["fecha"] = $quote["date"];
        $titulos["dateVen"] = $quote["dateVen"];
        $titulos["quoteTo"] = $quote["quoteTo"];
        $titulos["observations"] = $quote["generalObservations"];
        $titulos["folioComprobanteRD"] = "";
        $titulos["uuid"] = generaUUID();

        $titulos["formaPago"] = $this->catalogosSAT->formasDePago40()->searchByField("texto", "%%", 99999);
        $titulos["usoCFDI"] = $this->catalogosSAT->usosCfdi40()->searchByField("texto", "%%", 99999);
        $titulos["metodoPago"] = $this->catalogosSAT->metodosDePago40()->searchByField("texto", "%%", 99999);
        $titulos["regimenFiscal"] = $this->catalogosSAT->regimenesFiscales40()->searchByField("texto", "%%", 99999);

        $titulos["RFCReceptor"] = $quote["RFCReceptor"];
        $titulos["regimenFiscalReceptor"] = $quote["regimenFiscalReceptor"];
        $titulos["usoCFDIReceptor"] = $quote["usoCFDI"];
        $titulos["metodoPagoReceptor"] = $quote["metodoPago"];
        $titulos["formaPagoReceptor"] = $quote["formaPago"];
        $titulos["razonSocialReceptor"] = $quote["razonSocialReceptor"];
        $titulos["codigoPostalReceptor"] = $quote["codigoPostalReceptor"];
        $titulos["folioComprobanteRD"] = "0";

        $titulos["idSucursal"] = $quote["idSucursal"];
        $sucursal = $this->sucursales->select("*")->where("id", $titulos["idSucursal"])->first();
        $titulos["nombreSucursal"] = $sucursal["key"] . " " . $sucursal["name"];
        $titulos["permisoAgregarArticulo"] = $permisoAgregarArticulo;

        $titulos["title"] = "Generar Venta";
        $titulos["subtitle"] = "Convierte cotización a venta";

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

        $this->Quotes->db->transBegin();

        $existsQuote = $this->Quotes->where("UUID", $datos["UUID"])->countAllResults();

        $listProducts = json_decode($datos["listProducts"], true);

        if ($existsQuote == 0) {


            $ultimoFolio = $this->getLastCodeInterno($datos["idEmpresa"], $datos["idSucursal"]);

            $datos["folio"] = $ultimoFolio;

            try {


                if ($this->Quotes->save($datos) === false) {

                    $errores = $this->Quotes->errors();

                    $listErrors = "";

                    foreach ($errores as $field => $error) {

                        $listErrors .= $error . " ";
                    }

                    echo $listErrors;

                    return;
                }

                $idQuoteInserted = $this->Quotes->getInsertID();

                // save datail

                foreach ($listProducts as $key => $value) {
                    $datosDetalle["idQuote"] = $idQuoteInserted;
                    $datosDetalle["idAlmacen"] = $value["idAlmacen"];
                    $datosDetalle["lote"] = $value["lote"];
                    $datosDetalle["idProduct"] = $value["idProduct"];
                    $datosDetalle["description"] = $value["description"];
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
                    $datosDetalle["unidad"] = $value["unidad"];

                    $datosDetalle["tax"] = $value["tax"];
                    $datosDetalle["total"] = $value["total"];
                    $datosDetalle["neto"] = $value["neto"];

                    if ($this->quotesDetail->save($datosDetalle) === false) {

                        $errores = $this->quotesDetail->errors();

                        $listErrors = "";

                        foreach ($errores as $field => $error) {

                            $listErrors .= $error . " ";
                        }

                        $listErrors;

                        echo "error al insertar el producto $datosDetalle[idProduct] $listErrors";

                        $this->Quotes->db->transRollback();
                        return;
                    }
                }

                $datosBitacora["description"] = "Se guardo la cotizacion con los siguientes datos" . json_encode($datos);
                $datosBitacora["user"] = $userName;

                $this->log->save($datosBitacora);

                $this->Quotes->db->transCommit();
                echo "Guardado Correctamente";
            } catch (\PHPUnit\Framework\Exception $ex) {


                echo "Error al guardar " . $ex->getMessage();
            }
        } else {




            $backQuote = $this->Quotes->where("UUID", $datos["UUID"])->first();

            $datos["folio"] = $backQuote["folio"];

            if ($this->Quotes->update($backQuote["id"], $datos) == false) {

                $errores = $this->Quotes->errors();
                $listError = "";
                foreach ($errores as $field => $error) {

                    $listError .= $error . " ";
                }

                echo $listError;

                return;
            } else {

                $this->quotesDetail->select("*")->where("idQuote", $backQuote["id"])->delete();
                $this->quotesDetail->purgeDeleted();
                foreach ($listProducts as $key => $value) {

                    $datosDetalle["idQuote"] = $backQuote["id"];
                    $datosDetalle["idProduct"] = $value["idProduct"];
                    $datosDetalle["idAlmacen"] = $value["idAlmacen"];
                    $datosDetalle["lote"] = $value["lote"];
                    $datosDetalle["description"] = $value["description"];
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
                    $datosDetalle["unidad"] = $value["unidad"];

                    $datosDetalle["tax"] = $value["tax"];
                    $datosDetalle["total"] = $value["total"];
                    $datosDetalle["neto"] = $value["neto"];

                    if ($this->quotesDetail->save($datosDetalle) === false) {

                        echo "error al insertar el producto $datosDetalle[idProducto]";

                        $this->Quotes->db->transRollback();
                        return;
                    }
                }


                $datosBitacora["description"] = "Se actualizo" . json_encode($datos) .
                        " Los datos anteriores son" . json_encode($backQuote);
                $datosBitacora["user"] = $userName;
                $this->log->save($datosBitacora);

                echo "Actualizado Correctamente";
                $this->Quotes->db->transCommit();
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


        if ($this->Quotes->select("*")->whereIn("idEmpresa", $empresasID)->where("id", $id)->countAllResults() == 0) {

            return $this->failNotFound('Acceso Prohibido');
        }

        $this->Quotes->db->transBegin();

        if (!$found = $this->Quotes->delete($id)) {
            $this->Quotes->db->transRollback();
            return $this->failNotFound('Error al eliminar');
        }

        //Borramos quotesdetails

        if ($this->quotesDetail->select("*")->where("idQuote", $id)->delete() === false) {

            $this->Quotes->db->transRollback();
            return $this->failNotFound('Error al eliminar el detalle');
        }

        $this->quotesDetail->purgeDeleted();

        $infoConsulta = $this->Quotes->find($id);

        $this->Quotes->purgeDeleted();

        $datosBitacora["description"] = 'Se elimino el Registro' . json_encode($infoConsulta);

        $this->log->save($datosBitacora);

        $this->Quotes->db->transCommit();
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

        $pdf = new PDFLayoutQuotes(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $dataQuote = $this->Quotes->where("uuid", $uuid)->first();

        $listProducts = json_decode($dataQuote["listProducts"], true);

        $user = $this->user->where("id", $dataQuote["idUser"])->first()->toArray();

        $custumer = $this->custumer->where("id", $dataQuote["idCustumer"])->where("deleted_at", null)->first();

        $datosEmpresa = $this->empresa->select("*")->where("id", $dataQuote["idEmpresa"])->first();
        $datosEmpresaObj = $this->empresa->select("*")->where("id", $dataQuote["idEmpresa"])->asObject()->first();

        $pdf->nombreDocumento = "Cotización";
        $pdf->direccion = $datosEmpresaObj->direccion;

        if ($datosEmpresaObj->logo == NULL || $datosEmpresaObj->logo == "") {

            $pdf->logo = ROOTPATH . "public/images/logo/default.png";
        } else {

            $pdf->logo = ROOTPATH . "public/images/logo/" . $datosEmpresaObj->logo;
        }
        $pdf->folio = str_pad($dataQuote["folio"], 5, "0", STR_PAD_LEFT);

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
                    $dataQuote[generalObservations]
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
                    $dataQuote[date]
                    </td>
                    <td>
                    $dataQuote[dateVen]
                    </td>
                    <td>
                    $dataQuote[delivaryTime]
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
        $subTotal = number_format($dataQuote["subTotal"], 2, ".");

        $IVARetenido = number_format($dataQuote["IVARetenido"], 2, ".");
        $ISRRetenido = number_format($dataQuote["ISRRetenido"], 2, ".");

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



        $impuestos = number_format($dataQuote["taxes"], 2, ".");
        $total = number_format($dataQuote["total"], 2, ".");
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
                  
          <div style="font-size:8.5pt;text-align:left;font-weight:ligth">UUID DOCUMENTO: $dataQuote[UUID]</div>
          
     
      <div style="font-size:8.5pt;text-align:left;font-weight:ligth">ES RESPONSABILIDAD DEL CLIENTE REVISAR A DETALLE ESTA COTIZACION PARA SU POSTERIOR SURTIDO, UNA VEZ CONFIRMADA, NO HAY CAMBIOS NI DEVOLUCIONES.</div>
  
      
  
  
  EOF;

        $pdf->writeHTML($bloque5, false, false, false, false, 'R');

        if ($isMail == 0) {
            $this->response->setHeader("Content-Type", "application/pdf");
            $pdf->Output('cotizacion.pdf', 'I');
        } else {

            $attachment = $pdf->Output('cotizacion.pdf', 'S');

            return $attachment;
        }


        //============================================================+
        // END OF FILE
        //============================================================+
    }
}
