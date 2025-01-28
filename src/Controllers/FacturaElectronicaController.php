<?php

namespace julio101290\boilerplatesells\Controllers;

use App\Controllers\BaseController;
use \App\Models\UserModel;
use julio101290\boilerplatelog\Models\LogModel;
use julio101290\boilerplatequotes\Models\QuotesModel;

use CfdiUtils\Elements\Cfdi40\Concepto;
use CodeIgniter\API\ResponseTrait;
use julio101290\boilerplatecompanies\Models\EmpresasModel;
use julio101290\boilerplatecustumers\Models\CustumersModel;
use julio101290\boilerplatesells\Models\SellsModel;
use julio101290\boilerplatesells\Models\SellsDetailsModel;
use PhpParser\Node\Stmt\Foreach_;
use PhpCfdi\Credentials\Credential;
use julio101290\boilerplateCFDI\Models\XmlModel;
use CURLFile;
use julio101290\boilerplatesells\Models\EnlacexmlModel;
use julio101290\boilerplateCFDIElectronicSeries\Models\SeriesfacturaelectronicaModel;
use CfdiUtils\Elements\Pagos20\Pago;
// use App\Models\PagosModel; ponerlo cuando se pongan los pagos
use julio101290\boilerplatesells\Models\PaymentsModel;
use \CfdiUtils\SumasPagos20\Calculator;
use \CfdiUtils\SumasPagos20\Currencies;
use \CfdiUtils\SumasPagos20\PagosWriter;
//use App\Models\CartaPorteModel; PUT WHEN ADD PORT LETTER
use CfdiUtils\Elements\CartaPorte30\CartaPorte;
use RegRev\RegRev;

//use App\Models\NotascreditoModel; // ADD WHEN ADD CREDIT NOTES

class FacturaElectronicaController extends BaseController {

    use ResponseTrait;

    protected $sells;
    protected $sellsDetails;
    protected $empresa;
    protected $xml;
    protected $xmlEnlace;
    protected $serieElectronica;
    protected $pagos;
    protected $payments;
    protected $cartePorteModel;
    protected $notaCredito;

    public function __construct() {
        $this->sells = new SellsModel();
        $this->empresa = new EmpresasModel();
        $this->xml = new XmlModel();
        $this->xmlEnlace = new EnlacexmlModel();
        $this->serieElectronica = new SeriesfacturaelectronicaModel();
        //$this->pagos = new PagosModel();
        $this->payments = new PaymentsModel();
        //$this->cartePorteModel = new CartaPorteModel();
        //$this->notaCredito = new NotascreditoModel();
        //$this->sellsDetails = new SellsDetailsModel();

        helper('menu');
        helper('utilerias');
    }

    public function timbrar($uuidVenta) {


        $venta = $this->sells->select("*")->where("UUID ", $uuidVenta)->first();

        /*
          $facturaExistentes = $this->xmlEnlace
          ->select("id")
          ->where("idDocumento", $venta["id"])
          ->where("status<>","cancelado")
          ->countAllResults();
         * 
         * 
         */


        $facturaExistentes = $this->xmlEnlace->mdlGetVentaTieneFactura($venta["id"]);

        if ($facturaExistentes > 0) {

            echo "success";
            return;
        }



        $serie = $this->serieElectronica->select("*")
                        ->where("idEmpresa", $venta["idEmpresa"])
                        ->where("tipoSerie", "ven")
                        ->where("sucursal", $venta["idSucursal"])
                        ->where("desdeFolio <=", $venta["folio"])
                        ->where("hastaFolio >=", $venta["folio"])->first();

// VERIFICAMOS SI YA TIENE VENTA

        if (!isset($serie)) {

            echo "No hay serie electronica configurada";
            return;
        }

        $empresa = $this->empresa->find($venta["idEmpresa"]);

        if (file_exists(ROOTPATH . "writable/uploads/certificates/$empresa[certificadoCSD]")) {

            try {


                $certificado = new \CfdiUtils\Certificado\Certificado(ROOTPATH . "writable/uploads/certificates/$empresa[certificadoCSD]");
            } catch (Exception $e) {

                echo $e->getMessage();
                return;
            } finally {

                if (!isset($certificado)) {
                    echo "No se encontro el certificado";
                    return;
                }
            }
        } else {

            echo "No se ha cargado certificado";
            return;
        }


        if ($venta["usoCFDI"] == "" || $venta["usoCFDI"] == "NULL") {

            echo "Falta capturar el uso del CFDI";
            return;
        }

        $cerfile = ROOTPATH . "writable/uploads/certificates/$empresa[certificadoCSD]";
        $keyfile = ROOTPATH . "writable/uploads/certificates/$empresa[archivoKeyCSD]";
        $passPhrase = $empresa["contraCertificadoCSD"];

        if (!file_exists($keyfile)) {

            echo "No se ha cargado certificado el archivo key";
            return;
        }


        $csd = Credential::openFiles($cerfile, $keyfile, $passPhrase);

        $comprobanteAtributos = [
            'Serie' => $serie["serie"],
            'Folio' => $venta["folio"],
            'Fecha' => fechaMySQLADateTimeHTML5(fechaHoraActual()),
            'FormaPago' => $venta["formaPago"],
            'SubTotal' => $venta["subTotal"],
            'Total' => $venta["total"],
            'Moneda' => 'MXN',
            'TipoDeComprobante' => 'I',
            'MetodoPago' => $venta["metodoPago"],
            'LugarExpedicion' => $empresa["codigoPostal"],
            'Exportacion' => '01',
            'Sello' => '',
        ];

        $creator = new \CfdiUtils\CfdiCreator40($comprobanteAtributos, $certificado);

        $comprobante = $creator->comprobante();

        /*
          $comprobante->addInformacionGlobal([
          'Periodicidad' => '01',
          'Meses' => '06',
          'Año' => '2023',
          ]);

         */


        if ($venta["tipoDocumentoRelacionado"] == "01" || $venta["tipoDocumentoRelacionado"] == "02" || $venta["tipoDocumentoRelacionado"] == "03" || $venta["tipoDocumentoRelacionado"] == "04" || $venta["tipoDocumentoRelacionado"] == "05" || $venta["tipoDocumentoRelacionado"] == "06" || $venta["tipoDocumentoRelacionado"] == "07"
        ) {

            $cfdiRelacionado = $comprobante->addCfdiRelacionados([
                'TipoRelacion' => $venta["tipoDocumentoRelacionado"], // Tipo Relacion
            ]);

            $cfdiRelacionado->addCfdiRelacionado([
                'UUID' => $venta["UUIDRelacion"], // Tipo Relacion
            ]);
        }




        // No agrego (aunque puedo) el Rfc y Nombre porque uso los que están establecidos en el certificado
        $comprobante->addEmisor([
            'RegimenFiscal' => $empresa["regimenFiscal"], // General de Ley Personas Morales
            'Nombre' => $empresa["razonSocial"], // Agregamos el Nombre por que en el certificado viene SA DE CV
        ]);

        //$comprobante->addReceptor([/* Atributos del receptor */]);

        $comprobante->addReceptor([
            'Rfc' => $venta["RFCReceptor"],
            'Nombre' => $venta["razonSocialReceptor"],
            'DomicilioFiscalReceptor' => $venta["codigoPostalReceptor"],
            'RegimenFiscalReceptor' => $venta["regimenFiscalReceptor"],
            'UsoCFDI' => $venta["usoCFDI"],
        ]);

        if ($venta["esFacturaGlobal"] == "on" && $venta["RFCReceptor"] != "XAXX010101000") {

            echo "Si el el RFC del Receptor no es XAXX010101000 no se puede generar con factura global";
            return;
        }

        if ($venta["esFacturaGlobal"] == "on") {

            $comprobante->addInformacionGlobal([
                'Periodicidad' => $venta["periodicidad"],
                'Meses' => $venta["mes"],
                'Año' => $venta["anio"],
            ]);
        }


        $listProducts = json_decode($venta["listProducts"], true);

        foreach ($listProducts as $key => $value) {

            $concepto = null;

            if ($value["unidad"] == "" || $value["unidad"] == "NULL") {

                echo "Falta capturar la unidad del producto" . $value["description"];
                return;
            }

            if ($value["claveProductoSAT"] == "" || $value["claveProductoSAT"] == "NULL") {

                echo "Falta capturar la clave del producto o servicio del producto" . $value["description"];
                return;
            }

            if ($value["claveUnidadSAT"] == "" || $value["claveUnidadSAT"] == "NULL") {

                echo "Falta capturar la clave de la unidad del producto" . $value["description"];
                return;
            }

            // Verificamos si lleva Impuesto
            $objetoImpuesto = "01";

            if ($value["porcentTax"] > 0) {


                $objetoImpuesto = "02";
            }


            if ($value["importeExento"] > 0) {


                $objetoImpuesto = "02";
            }


            if ($value["porcentIVARetenido"] > 0) {

                $objetoImpuesto = "02";
            }

            if ($value["porcentISRRetenido"] > 0) {

                $objetoImpuesto = "02";
            }

            $concepto = $comprobante->addConcepto([
                'ClaveProdServ' => $value["claveProductoSAT"],
                'Cantidad' => $value["cant"],
                'ClaveUnidad' => $value["claveUnidadSAT"],
                'Unidad' => $value["unidad"],
                'Descripcion' => $value["description"],
                'ValorUnitario' => $value["price"],
                'Importe' => number_format($value["total"], 2, ".", ''),
                'ObjetoImp' => $objetoImpuesto,
            ]);

            if (isset($value["predial"])) {


                if ($value["predial"] == "null") {

                    echo "El predial no puede ser nulo";
                    return;
                }

                if ($value["predial"] != "") {



                    $concepto->addCuentaPredial([
                        'Numero' => $value["predial"],
                    ]);
                }
            }


            if ($value["porcentTax"] > 0) {


                $porc = number_format(($value["porcentTax"] / 100), 6, ".", '');

                $importeImpuesto = number_format($value["total"], 2, ".", '') * $porc;
                $concepto->addTraslado([
                    'Base' => number_format($value["total"], 2, ".", ''),
                    'Impuesto' => '002',
                    'TipoFactor' => 'Tasa',
                    'TasaOCuota' => $porc,
                    'Importe' => number_format($importeImpuesto, 2, ".", ''),
                ]);
            }


            if ($value["importeExento"] > 0) {


                $porc = number_format(($value["porcentTax"] / 100), 6, ".", '');

                $importeImpuesto = number_format($value["total"], 2, ".", '') * $porc;
                $concepto->addTraslado([
                    'Base' => number_format($value["importeExento"], 2, ".", ''),
                    'Impuesto' => '002',
                    'TipoFactor' => 'Exento',
                ]);
            }

            if ($value["porcentIVARetenido"] > 0) {


                $porc = number_format(($value["porcentIVARetenido"] / 100), 6, ".");

                $importeImpuesto = number_format($value["total"], 2, ".", '') * $porc;
                $concepto->addRetencion([
                    'Base' => number_format($value["total"], 2, ".", ''),
                    'Impuesto' => '002',
                    'TipoFactor' => 'Tasa',
                    'TasaOCuota' => $porc,
                    'Importe' => number_format($importeImpuesto, 2, ".", ''),
                ]);
            }

            if ($value["porcentISRRetenido"] > 0) {


                $porc = number_format(($value["porcentISRRetenido"] / 100), 6, ".");

                $importeImpuesto = 0;

                $importeImpuesto = number_format($value["total"], 2, ".", '') * number_format($porc, 6, ".");
                $concepto->addRetencion([
                    'Base' => number_format($value["total"], 2, ".", ''),
                    'Impuesto' => '001',
                    'TipoFactor' => 'Tasa',
                    'TasaOCuota' => $porc,
                    'Importe' => number_format($importeImpuesto, 2, ".", ''),
                ]);
            }
        }


        $creator->addSumasConceptos(null, 2);
        // método de ayuda para mover las declaraciones de espacios de nombre al nodo raíz
        $creator->moveSatDefinitionsToComprobante();

        // método de ayuda para validar usando las validaciones estándar de creación de la librería
        $asserts = $creator->validate();
        if ($asserts->hasErrors()) { // contiene si hay o no errores
            $error = $asserts->errors();
            print_r(['errors' => $asserts->errors()]);
            return;
        }



        // método de ayuda para generar el xml y retornarlo como un string
        $creator->addSello($csd->privateKey()->pem(), $csd->privateKey()->passPhrase());
        $xml = $creator->asXml();

        $pac = "swsapien";

        if ($pac == "finkok") {

            $settings = new FinkokSettings('julio', 'bc7377cd15625be2cb3998de10e3f8d355a2cb76f23787b6cdb378b5fc36', FinkokEnvironment::makeDevelopment());
            $finkok = new QuickFinkok($settings);

            $archivoDestino = $xml;

            $nombreXML = $uuidVenta . ".xml";

            file_put_contents($nombreXML, $archivoDestino);

            // el PreCFDI a firmar, podría venir de CfdiUtils ;) $creator->asXml()
            $precfdi = file_get_contents($nombreXML);

            $stampResult = $finkok->stamp($precfdi); // <- aquí contactamos a Finkok


            if ($stampResult->hasAlerts()) { // stamp es un objeto con propiedades nombradas
                foreach ($stampResult->alerts() as $alert) {
                    echo $alert->id() . ' - ' . $alert->message() . PHP_EOL;

                    return $this->fail(lang('boilerplate.menu.msg.msg_fail_order'));
                }
            } else {

                $respuesta["data"]["cfdi"] = $stampResult->xml();
                //file_put_contents($stampResult->uuid() . '.xml', $stampResult->xml()); // CFDI firmado
            }
        }


        if ($pac == "swsapien") {

            if ($serie["ambienteTimbrado"] == "on") {

                $url = "https://services.sw.com.mx/v4/cfdi33/issue/v4";
                $token = $serie["tokenProduccion"];
            } else {

                $url = "https://services.test.sw.com.mx/v4/cfdi33/issue/v4";
                $token = $serie["tokenPruebas"];
            }

            $archivoDestino = $xml;

            $nombreXML = $uuidVenta . ".xml";

            file_put_contents($nombreXML, $archivoDestino);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('xml' => new CURLFILE($nombreXML)),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $token . '',
                    'customid: ' . $uuidVenta . fechaHoraActual() . '',
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $respuesta = json_decode($response, true);

            if ($respuesta["status"] != "success") {

                echo "error al timbrar " . $response;
                return;
            }
        }

        unlink($nombreXML);

        // iniciamos transacccion
        $this->xml->db->transBegin();

        //file_put_contents("xmlTimbrado.xml", $respuesta["data"]["cfdi"]);
        // Si se timbro correctamente extraemos los datos y lo guardamos

        $xml = \PhpCfdi\CfdiCleaner\Cleaner::staticClean($respuesta["data"]["cfdi"]);

        // create the main node structure
        $comprobante = \CfdiUtils\Nodes\XmlNodeUtils::nodeFromXmlString($xml);

        // create the CfdiData object, it contains all the required information
        $cfdiData = (new \PhpCfdi\CfdiToPdf\CfdiDataBuilder())
                ->build($comprobante);

        $tfd = $cfdiData->timbreFiscalDigital();
        $emisor = $cfdiData->emisor();
        $receptor = $cfdiData->receptor();

        $datosXML["base16"] = "0.00";
        $datosXML["totalImpuestos16"] = "0.00";

        $datosXML["base8"] = "0.00";
        $datosXML["totalImpuestos8"] = "0.00";

        $datosXML["tasaExenta"] = "0.00";

        $impuestos = $comprobante->searchNode('cfdi:Impuestos');

        if (isset($impuestos)) {

            $traslados = $impuestos->searchNodes('cfdi:Traslados', 'cfdi:Traslado');

            foreach ($traslados as $key) {


                if ($key["TasaOCuota"] == 0.16) {

                    $datosXML["base16"] = $datosXML["base16"] + $key["Base"];
                    $datosXML["totalImpuestos16"] = $datosXML["totalImpuestos16"] + $key["Importe"];
                }

                if ($key["TasaOCuota"] == 0.08) {

                    $datosXML["base8"] = $datosXML["base8"] + $key["Base"];
                    $datosXML["totalImpuestos8"] = $datosXML["totalImpuestos8"] + $key["Importe"];
                }


                if ($key["TipoFactor"] == "Exento") {

                    $datosXML["tasaExenta"] = $datosXML["tasaExenta"] + $key["Base"];
                }
            }
        }


        //$tfd['UUID']



        $datosXML["uuidTimbre"] = $tfd['UUID'];
        $datosXML["uuidPaquete"] = "SISTEMA";
        $datosXML["idEmpresa"] = $venta["idEmpresa"];
        $datosXML["archivoXML"] = $respuesta["data"]["cfdi"];

        //  $jsonXML = JsonConverter::convertToJson($content);
        //  $arregloXML = json_decode($jsonXML, true);
        $datosXML["fecha"] = $comprobante["Fecha"];
        $datosXML["fechaTimbrado"] = $tfd['FechaTimbrado'];
        $datosXML["total"] = $comprobante["Total"];
        $datosXML["tipoComprobante"] = $comprobante["TipoDeComprobante"];

        $datosXML["rfcEmisor"] = $emisor['Rfc'];
        $datosXML["rfcReceptor"] = $receptor['Rfc'];

        $datosXML["nombreEmisor"] = $emisor['Nombre'];
        $datosXML["nombreReceptor"] = $receptor['Nombre'];

        $datosXML["metodoPago"] = $comprobante['MetodoPago'];
        $datosXML["formaPago"] = $comprobante['FormaPago'];
        $datosXML["usoCFDI"] = $receptor['UsoCFDI'];
        $datosXML["exportacion"] = $comprobante['Exportacion'];
        $datosXML["emitidoRecibido"] = "emitido";

        if (isset($comprobante["Serie"])) {

            $datosXML["serie"] = $comprobante["Serie"];
        } else {

            $datosXML["serie"] = "";
        }

        if (isset($comprobante["Folio"])) {

            $datosXML["folio"] = $comprobante["Folio"];
        } else {

            $datosXML["folio"] = "";
        }

        $totalRen = $this->xml->selectCount("id")->where("uuidTimbre", $datosXML["uuidTimbre"])->first();
        $totalRen = $totalRen["id"];
        if ($totalRen == 0) {

            if ($this->xml->insert($datosXML) === false) {

                $errores = $this->xml->errors();

                $listErrors = "";

                foreach ($errores as $field => $error) {

                    $listErrors .= $error . " ";
                }

                $this->xml->db->transRollback();
                echo $listErrors;

                return;
            }
        }



        /*
         * Insertamos en enlace
         */

        $datosEnlace["idDocumento"] = $venta["id"];
        $datosEnlace["uuidXML"] = $datosXML["uuidTimbre"];
        $datosEnlace["tipo"] = "ven";
        $datosEnlace["importe"] = $comprobante["Total"];

        if ($this->xmlEnlace->insert($datosEnlace) === false) {

            $errores = $this->xmlEnlace->errors();

            $listErrors = "";

            foreach ($errores as $field => $error) {

                $listErrors .= $error . " ";
            }

            $this->xml->db->transRollback();
            echo $listErrors;

            return;
        }


        $this->xml->db->transCommit();
        echo "success";
    }

    public function timbrarNotaCredito($uuidNotaCredito) {


        $notaCredito = $this->notaCredito->select("*")->where("UUID", $uuidNotaCredito)->first();

        $facturaExistentes = $this->xmlEnlace->mdlGetVentaTieneFacturaNotaCredito($notaCredito["id"]);

        if ($facturaExistentes > 0) {

            echo "success";
            return;
        }


        $serie = $this->serieElectronica->select("*")
                        ->where("idEmpresa", $notaCredito["idEmpresa"])
                        ->where("tipoSerie", "bon")
                        ->where("sucursal", $notaCredito["idSucursal"])
                        ->where("desdeFolio <=", $notaCredito["folio"])
                        ->where("hastaFolio >=", $notaCredito["folio"])->first();

// VERIFICAMOS SI YA TIENE VENTA

        if (!isset($serie)) {

            echo "No hay serie electronica configurada";
            return;
        }

        $empresa = $this->empresa->find($notaCredito["idEmpresa"]);

        if (file_exists(ROOTPATH . "writable/uploads/certificates/$empresa[certificadoCSD]")) {

            try {


                $certificado = new \CfdiUtils\Certificado\Certificado(ROOTPATH . "writable/uploads/certificates/$empresa[certificadoCSD]");
            } catch (Exception $e) {

                echo $e->getMessage();
                return;
            } finally {

                if (!isset($certificado)) {
                    echo "No se encontro el certificado";
                    return;
                }
            }
        } else {

            echo "No se ha cargado certificado";
            return;
        }


        if ($notaCredito["usoCFDI"] == "" || $notaCredito["usoCFDI"] == "NULL") {

            echo "Falta capturar el uso del CFDI";
            return;
        }

        $cerfile = ROOTPATH . "writable/uploads/certificates/$empresa[certificadoCSD]";
        $keyfile = ROOTPATH . "writable/uploads/certificates/$empresa[archivoKeyCSD]";
        $passPhrase = $empresa["contraCertificadoCSD"];

        if (!file_exists($keyfile)) {

            echo "No se ha cargado certificado el archivo key";
            return;
        }


        $csd = Credential::openFiles($cerfile, $keyfile, $passPhrase);

        $comprobanteAtributos = [
            'Serie' => $serie["serie"],
            'Folio' => $notaCredito["folio"],
            'Fecha' => fechaMySQLADateTimeHTML5(fechaHoraActual()),
            'FormaPago' => $notaCredito["formaPago"],
            'SubTotal' => $notaCredito["subTotal"],
            'Total' => $notaCredito["total"],
            'Moneda' => 'MXN',
            'TipoDeComprobante' => 'E',
            'MetodoPago' => $notaCredito["metodoPago"],
            'LugarExpedicion' => $empresa["codigoPostal"],
            'Exportacion' => '01',
            'Sello' => '',
        ];

        $creator = new \CfdiUtils\CfdiCreator40($comprobanteAtributos, $certificado);

        $comprobante = $creator->comprobante();

        /*
          Documentos Relacionados

         */

        $ventasNotaCredito = $this->payments
                ->select("*")
                ->where("idNotaCredito", $notaCredito["id"])
                ->findAll();

        $cfdiRelacionado = $comprobante->addCfdiRelacionados([
            'TipoRelacion' => "01", // Tipo Relacion
        ]);

        foreach ($ventasNotaCredito as $key => $value) {


            $enlaceXMLVenta = $this->xmlEnlace
                            ->where("idDocumento", $value["idSell"])
                            ->where("tipo", "ven")->first();

            if (count($enlaceXMLVenta) == 0) {

                echo "No se encontro documento enlazado para la id de venta " . $value["idSell"];
                return;
            }


            $cfdiRelacionado->addCfdiRelacionado([
                'UUID' => $enlaceXMLVenta["uuidXML"], // Tipo Relacion
            ]);
        }

        // No agrego (aunque puedo) el Rfc y Nombre porque uso los que están establecidos en el certificado
        $comprobante->addEmisor([
            'RegimenFiscal' => $empresa["regimenFiscal"], // General de Ley Personas Morales
            'Nombre' => $empresa["razonSocial"], // Agregamos el Nombre por que en el certificado viene SA DE CV
        ]);

        //$comprobante->addReceptor([/* Atributos del receptor */]);

        $comprobante->addReceptor([
            'Rfc' => $notaCredito["RFCReceptor"],
            'Nombre' => $notaCredito["razonSocialReceptor"],
            'DomicilioFiscalReceptor' => $notaCredito["codigoPostalReceptor"],
            'RegimenFiscalReceptor' => $notaCredito["regimenFiscalReceptor"],
            'UsoCFDI' => $notaCredito["usoCFDI"],
        ]);

        /*
          if ($notaCredito["esFacturaGlobal"] == "on" && $notaCredito["RFCReceptor"] != "XAXX010101000") {

          echo "Si el el RFC del Receptor no es XAXX010101000 no se puede generar con factura global";
          return;
          }

          if ($venta["esFacturaGlobal"] == "on") {

          $comprobante->addInformacionGlobal([
          'Periodicidad' => $notaCredito["periodicidad"],
          'Meses' => $notaCredito["mes"],
          'Año' => $notaCredito["anio"],
          ]);
          }

         */
        $listPagos = json_decode($notaCredito["listPagos"], true);

        foreach ($listPagos as $key => $value) {

            $concepto = null;

            $value["claveProductoSAT"] = "84111506";

            $value["claveUnidadSAT"] = "ACT";

            if ($value["claveProductoSAT"] == "" || $value["claveProductoSAT"] == "NULL") {

                echo "Falta capturar la clave del producto o servicio del producto" . $value["description"];
                return;
            }

            if ($value["claveUnidadSAT"] == "" || $value["claveUnidadSAT"] == "NULL") {

                echo "Falta capturar la clave de la unidad del producto" . $value["description"];
                return;
            }

            $venta = $this->sells->select("*")->where("id", $value["idSell"])->first();
            $enlace = $this->xmlEnlace->select("*")->where("idDocumento", $notaCredito["id"])->first();
            $xmlVenta = $this->xml->select("*")->where("uuidTimbre", $enlace["uuidXML"])->first();

            $IVAVenta16 = $this->sellsDetails->selectSum("tax")
                    ->where("idSell", $venta["id"])
                    ->where("porcentTax", 16)
                    ->first();

            $IVAVenta16 = esCero($IVAVenta16["tax"]);

            $IVAVenta8 = $this->sellsDetails->selectSum("tax")
                    ->where("idSell", $venta["id"])
                    ->where("porcentTax", 8)
                    ->first();

            $IVAVenta8 = esCero($IVAVenta8["tax"]);

            $totalIVARetenido = esCero($venta["IVARetenido"]);

            $totalISRRetenido = esCero($venta["ISRRetenido"]);

            if (count($xmlVenta) == 0) {

                echo "No se encontro el xml de la venta con UUID: " . $enlace["uuidXML"];
                return;
            }

            // Verificamos si lleva Impuesto
            $objetoImpuesto = "01";

            if ($IVAVenta16 > 0 || $IVAVenta8 > 0) {


                $objetoImpuesto = "02";
            }


            if ($xmlVenta["tasaExenta"] > 0) {

                $objetoImpuesto = "02";
            }


            if ($totalIVARetenido > 0) {

                $objetoImpuesto = "02";
            }

            if ($totalISRRetenido > 0) {

                $objetoImpuesto = "02";
            }
            $value["description"] = "Nota de Crédito de la factura con folio de venta " . $venta["folio"];

            $value["cant"] = "1";

            $concepto = $comprobante->addConcepto([
                'ClaveProdServ' => $value["claveProductoSAT"],
                'Cantidad' => $value["cant"],
                'ClaveUnidad' => $value["claveUnidadSAT"],
                'Descripcion' => $value["description"],
                'ValorUnitario' => $value["importeAPagar"],
                'Importe' => number_format($value["importeAPagar"], 2, ".", ''),
                'ObjetoImp' => $objetoImpuesto,
            ]);

            if ($IVAVenta16 > 0) {


                $porc = number_format((16 / 100), 6, ".", '');

                /**
                 * Calculo de la base
                 * $xmlVenta
                 */
                $baseImporte = (number_format($value["importeAPagar"], 2, ".", '')) / (1 + $porc);

                $impuesto16NotaCredito = number_format($baseImporte, 2, ".", '') * $porc;

                $importeImpuesto = $baseImporte * $porc;

                $concepto->addTraslado([
                    'Base' => number_format($baseImporte, 2, ".", ''),
                    'Impuesto' => '002',
                    'TipoFactor' => 'Tasa',
                    'TasaOCuota' => $porc,
                    'Importe' => number_format($importeImpuesto, 2, ".", ''),
                ]);
            }


            /*
             * IVA AL 8 porciento
             */


            if ($IVAVenta8 > 0) {


                $porc = number_format((8 / 100), 6, ".", '');

                /**
                 * Calculo de la base
                 * $xmlVenta
                 */
                $baseImporte = (number_format($value["importeAPagar"], 2, ".", '')) / (1 + $porc);

                $impuesto8NotaCredito = number_format($baseImporte, 2, ".", '') * $porc;

                $impuesto8NotaCredito = $baseImporte * $porc;

                $concepto->addTraslado([
                    'Base' => number_format($baseImporte, 2, ".", ''),
                    'Impuesto' => '002',
                    'TipoFactor' => 'Tasa',
                    'TasaOCuota' => $porc,
                    'Importe' => number_format($impuesto8NotaCredito, 2, ".", ''),
                ]);
            }


            if ($xmlVenta["tasaExenta"] > 0) {






                /*
                  $baseImporte = (number_format($value["total"], 2, ".", '') / number_format($xmlVenta["total"], 2, ".", '')) * number_format($value["total"], 2, ".", '');

                  $baseImporte = ((number_format($baseImporte, 2, ".", '')) / number_format($xmlVenta["tasaExenta"], 2, ".", ''));
                 */
                $concepto->addTraslado([
                    'Base' => number_format($baseImporte, 2, ".", ''),
                    'Impuesto' => '002',
                    'TipoFactor' => 'Exento',
                ]);
            }

            if ($totalIVARetenido > 0) {


                $impuestosIvaRetenido = $this->sells->mdlIVARetenidoTotales($venta["id"]);

                foreach ($impuestosIvaRetenido as $key => $value2) {

                    $porc = number_format(($value2["porcentIVARetenido"] / 100), 6, ".", '');

                    /**
                     * Calculo de la base
                     * $xmlVenta
                     */
                    $baseImporte = (number_format($value["importeAPagar"], 2, ".", '')) / (1 + $porc);

                    $importeImpuesto = number_format($baseImporte, 2, ".", '') * $porc;

                    $concepto->addRetencion([
                        'Base' => number_format($baseImporte, 2, ".", ''),
                        'Impuesto' => '002',
                        'TipoFactor' => 'Tasa',
                        'TasaOCuota' => $porc,
                        'Importe' => number_format($importeImpuesto, 2, ".", ''),
                    ]);
                }
            }

            if ($totalISRRetenido > 0) {

                $impuestosISRRetenido = $this->sells->mdlISRRetenidoTotales($venta["id"]);

                foreach ($impuestosISRRetenido as $key => $value2) {


                    $porc = number_format(($value2["porcentISRRetenido"] / 100), 6, ".", '');

                    /**
                     * Calculo de la base
                     * $xmlVenta
                     */
                    $baseImporte = (number_format($value["importeAPagar"], 2, ".", '')) / (1 + $porc);

                    $importeImpuesto = number_format($baseImporte, 2, ".", '') * $porc;

                    $concepto->addRetencion([
                        'Base' => number_format($baseImporte, 2, ".", ''),
                        'Impuesto' => '001',
                        'TipoFactor' => 'Tasa',
                        'TasaOCuota' => $porc,
                        'Importe' => number_format($importeImpuesto, 2, ".", ''),
                    ]);
                }
            }
        }


        $creator->addSumasConceptos(null, 2);
        // método de ayuda para mover las declaraciones de espacios de nombre al nodo raíz
        $creator->moveSatDefinitionsToComprobante();

        // método de ayuda para validar usando las validaciones estándar de creación de la librería
        $asserts = $creator->validate();
        if ($asserts->hasErrors()) { // contiene si hay o no errores
            $error = $asserts->errors();
            print_r(['errors' => $asserts->errors()]);
            return;
        }



        // método de ayuda para generar el xml y retornarlo como un string
        $creator->addSello($csd->privateKey()->pem(), $csd->privateKey()->passPhrase());
        $xml = $creator->asXml();

        $pac = "swsapien";

        if ($pac == "finkok") {

            $settings = new FinkokSettings('julio', 'bc7377cd15625be2cb3998de10e3f8d355a2cb76f23787b6cdb378b5fc36', FinkokEnvironment::makeDevelopment());
            $finkok = new QuickFinkok($settings);

            $archivoDestino = $xml;

            $nombreXML = $uuidVenta . ".xml";

            file_put_contents($nombreXML, $archivoDestino);

            // el PreCFDI a firmar, podría venir de CfdiUtils ;) $creator->asXml()
            $precfdi = file_get_contents($nombreXML);

            $stampResult = $finkok->stamp($precfdi); // <- aquí contactamos a Finkok


            if ($stampResult->hasAlerts()) { // stamp es un objeto con propiedades nombradas
                foreach ($stampResult->alerts() as $alert) {
                    echo $alert->id() . ' - ' . $alert->message() . PHP_EOL;

                    return $this->fail(lang('boilerplate.menu.msg.msg_fail_order'));
                }
            } else {

                $respuesta["data"]["cfdi"] = $stampResult->xml();
                //file_put_contents($stampResult->uuid() . '.xml', $stampResult->xml()); // CFDI firmado
            }
        }


        if ($pac == "swsapien") {

            if ($serie["ambienteTimbrado"] == "on") {

                $url = "https://services.sw.com.mx/v4/cfdi33/issue/v4";
                $token = $serie["tokenProduccion"];
            } else {

                $url = "https://services.test.sw.com.mx/v4/cfdi33/issue/v4";
                $token = $serie["tokenPruebas"];
            }

            $archivoDestino = $xml;

            $nombreXML = $uuidNotaCredito . ".xml";

            file_put_contents($nombreXML, $archivoDestino);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('xml' => new CURLFILE($nombreXML)),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . $token . '',
                    'customid: ' . $uuidNotaCredito . fechaHoraActual() . '',
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $respuesta = json_decode($response, true);

            if ($respuesta["status"] != "success") {

                echo "error al timbrar " . $response;
                return;
            }
        }

        unlink($nombreXML);

        // iniciamos transacccion
        $this->xml->db->transBegin();

        //file_put_contents("xmlTimbrado.xml", $respuesta["data"]["cfdi"]);
        // Si se timbro correctamente extraemos los datos y lo guardamos

        $xml = \PhpCfdi\CfdiCleaner\Cleaner::staticClean($respuesta["data"]["cfdi"]);

        // create the main node structure
        $comprobante = \CfdiUtils\Nodes\XmlNodeUtils::nodeFromXmlString($xml);

        // create the CfdiData object, it contains all the required information
        $cfdiData = (new \PhpCfdi\CfdiToPdf\CfdiDataBuilder())
                ->build($comprobante);

        $tfd = $cfdiData->timbreFiscalDigital();
        $emisor = $cfdiData->emisor();
        $receptor = $cfdiData->receptor();

        $datosXML["base16"] = "0.00";
        $datosXML["totalImpuestos16"] = "0.00";

        $datosXML["base8"] = "0.00";
        $datosXML["totalImpuestos8"] = "0.00";

        $datosXML["tasaExenta"] = "0.00";

        $impuestos = $comprobante->searchNode('cfdi:Impuestos');

        if (isset($impuestos)) {

            $traslados = $impuestos->searchNodes('cfdi:Traslados', 'cfdi:Traslado');

            foreach ($traslados as $key) {


                if ($key["TasaOCuota"] == 0.16) {

                    $datosXML["base16"] = $datosXML["base16"] + $key["Base"];
                    $datosXML["totalImpuestos16"] = $datosXML["totalImpuestos16"] + $key["Importe"];
                }

                if ($key["TasaOCuota"] == 0.08) {

                    $datosXML["base8"] = $datosXML["base8"] + $key["Base"];
                    $datosXML["totalImpuestos8"] = $datosXML["totalImpuestos8"] + $key["Importe"];
                }


                if ($key["TipoFactor"] == "Exento") {

                    $datosXML["tasaExenta"] = $datosXML["tasaExenta"] + $key["Base"];
                }
            }
        }


        //$tfd['UUID']



        $datosXML["uuidTimbre"] = $tfd['UUID'];
        $datosXML["uuidPaquete"] = "SISTEMA";
        $datosXML["idEmpresa"] = $venta["idEmpresa"];
        $datosXML["archivoXML"] = $respuesta["data"]["cfdi"];

        //  $jsonXML = JsonConverter::convertToJson($content);
        //  $arregloXML = json_decode($jsonXML, true);
        $datosXML["fecha"] = $comprobante["Fecha"];
        $datosXML["fechaTimbrado"] = $tfd['FechaTimbrado'];
        $datosXML["total"] = $comprobante["Total"];
        $datosXML["tipoComprobante"] = $comprobante["TipoDeComprobante"];

        $datosXML["rfcEmisor"] = $emisor['Rfc'];
        $datosXML["rfcReceptor"] = $receptor['Rfc'];

        $datosXML["nombreEmisor"] = $emisor['Nombre'];
        $datosXML["nombreReceptor"] = $receptor['Nombre'];

        $datosXML["metodoPago"] = $comprobante['MetodoPago'];
        $datosXML["formaPago"] = $comprobante['FormaPago'];
        $datosXML["usoCFDI"] = $receptor['UsoCFDI'];
        $datosXML["exportacion"] = $comprobante['Exportacion'];
        $datosXML["emitidoRecibido"] = "emitido";

        if (isset($comprobante["Serie"])) {

            $datosXML["serie"] = $comprobante["Serie"];
        } else {

            $datosXML["serie"] = "";
        }

        if (isset($comprobante["Folio"])) {

            $datosXML["folio"] = $comprobante["Folio"];
        } else {

            $datosXML["folio"] = "";
        }

        $totalRen = $this->xml->selectCount("id")->where("uuidTimbre", $datosXML["uuidTimbre"])->first();
        $totalRen = $totalRen["id"];
        if ($totalRen == 0) {

            if ($this->xml->insert($datosXML) === false) {

                $errores = $this->xml->errors();

                $listErrors = "";

                foreach ($errores as $field => $error) {

                    $listErrors .= $error . " ";
                }

                $this->xml->db->transRollback();
                echo $listErrors;

                return;
            }
        }



        /*
         * Insertamos en enlace
         */

        $datosEnlace["idDocumento"] = $notaCredito["id"];
        $datosEnlace["uuidXML"] = $datosXML["uuidTimbre"];
        $datosEnlace["tipo"] = "NCR";
        $datosEnlace["importe"] = $comprobante["Total"];

        if ($this->xmlEnlace->insert($datosEnlace) === false) {

            $errores = $this->xmlEnlace->errors();

            $listErrors = "";

            foreach ($errores as $field => $error) {

                $listErrors .= $error . " ";
            }

            $this->xml->db->transRollback();
            echo $listErrors;

            return;
        }


        $this->xml->db->transCommit();
        echo "success";
    }

    public function timbrarCartaPorte($uuidCartaPorte) {


        $cartaPorte = $this->cartePorteModel->select("*")->where("UUID", $uuidCartaPorte)->first();

        $facturaExistentes = $this->xmlEnlace->select("id")
                ->where("idDocumento", $cartaPorte["id"])
                ->where("tipo", "tra")
                ->countAllResults();

        if ($facturaExistentes > 0) {

            echo "success";
            return;
        }



        $serie = $this->serieElectronica->select("*")
                        ->where("idEmpresa", $cartaPorte["idEmpresa"])
                        ->where("tipoSerie", "tra")
                        ->where("sucursal", $cartaPorte["idSucursal"])
                        ->where("desdeFolio <=", $cartaPorte["folio"])
                        ->where("hastaFolio >=", $cartaPorte["folio"])->first();

// VERIFICAMOS SI YA TIENE VENTA

        if (!isset($serie)) {

            echo "No hay serie electronica configurada";
            return;
        }

        $empresa = $this->empresa->find($cartaPorte["idEmpresa"]);

        if (file_exists(ROOTPATH . "writable/uploads/certificates/$empresa[certificadoCSD]")) {

            try {


                $certificado = new \CfdiUtils\Certificado\Certificado(ROOTPATH . "writable/uploads/certificates/$empresa[certificadoCSD]");
            } catch (Exception $e) {

                echo $e->getMessage();
                return;
            } finally {

                if (!isset($certificado)) {
                    echo "No se encontro el certificado";
                    return;
                }
            }
        } else {

            echo "No se ha cargado certificado";
            return;
        }


        if ($cartaPorte["usoCFDI"] == "" || $cartaPorte["usoCFDI"] == "NULL") {

            echo "Falta capturar el uso del CFDI";
            return;
        }

        $cerfile = ROOTPATH . "writable/uploads/certificates/$empresa[certificadoCSD]";
        $keyfile = ROOTPATH . "writable/uploads/certificates/$empresa[archivoKeyCSD]";
        $passPhrase = $empresa["contraCertificadoCSD"];

        if (!file_exists($keyfile)) {

            echo "No se ha cargado certificado el archivo key";
            return;
        }


        $csd = Credential::openFiles($cerfile, $keyfile, $passPhrase);

        //CARTA PORTE
        $cartaPorteCFDI = new \CfdiUtils\Elements\CartaPorte30\CartaPorte();

        $atributosCartaPorte["TranspInternac"] = $cartaPorte["TranspInternac"];
        $atributosCartaPorte["TotalDistRec"] = $cartaPorte["TotalDistRec"];
        $atributosCartaPorte["IdCCP"] = RegRev::generate('[C]{3}[a-f0-9A-F]{5}-[a-f0-9A-F]{4}-[a-f0-9A-F]{4}-[a-f0-9A-F]{4}-[a-f0-9A-F]{12}');

        $cartaPorteCFDI->addAttributes($atributosCartaPorte);

        $autotransporte = new \CfdiUtils\Elements\CartaPorte30\Autotransporte();

        $atributosTransporte["PermSCT"] = $cartaPorte["PermSCT"];
        $atributosTransporte["NumPermisoSCT"] = $cartaPorte["NumPermisoSCT"];

        $autotransporte->addAttributes($atributosTransporte);

        $attributosIdentificacionVehicular["ConfigVehicular"] = $cartaPorte["ConfigVehicular"];
        $attributosIdentificacionVehicular["PesoBrutoVehicular"] = $cartaPorte["PesoBrutoVehicular"];
        $attributosIdentificacionVehicular["PlacaVM"] = $cartaPorte["PlacaVM"];
        $attributosIdentificacionVehicular["AnioModeloVM"] = $cartaPorte["AnioModeloVM"];

        $autotransporte->addIdentificacionVehicular($attributosIdentificacionVehicular);

        $atributosSeguroVehiculo["AseguraRespCivil"] = $cartaPorte["AseguraRespCivil"];
        $atributosSeguroVehiculo["PolizaRespCivil"] = $cartaPorte["PolizaRespCivil"];

        $autotransporte->addSeguros($atributosSeguroVehiculo);

        if ($cartaPorte["remolqueCartaPorte"] != "") {

            $attributoRemolque["SubTipoRem"] = $cartaPorte["SubTipoRem"];
            $attributoRemolque["Placa"] = $cartaPorte["PlacaSubTipoRemolque"];

            $autotransporte->addRemolques()->addRemolque($attributoRemolque);
        }



        // Figura
        $figuraCartaPorte = $cartaPorteCFDI->getFiguraTransporte();

        $domicilioFigura = new \CfdiUtils\Elements\CartaPorte30\Domicilio();

        $atributosFigura["TipoFigura"] = $cartaPorte["TipoFigura"];
        $atributosFigura["RFCFigura"] = $cartaPorte["RFCFigura"];
        $atributosFigura["NumLicencia"] = $cartaPorte["NumLicencia"];
        $atributosFigura["NombreFigura"] = $cartaPorte["NombreFigura"] . " " . $cartaPorte["apellidoFigura"];
        $atributosFiguraDomicilio["Municipio"] = $cartaPorte["MunicipioFigura"];
        $atributosFiguraDomicilio["Estado"] = $cartaPorte["EstadoFigura"];
        $atributosFiguraDomicilio["Pais"] = $cartaPorte["PaisFigura"];
        $atributosFiguraDomicilio["CodigoPostal"] = $cartaPorte["CodigoPostalFigura"];

        $domicilioFigura->addAttributes($atributosFiguraDomicilio);
        $figuraCartaPorte->addTiposFigura($atributosFigura)->addChild($domicilioFigura);

        // $figuraCartaPorte->addTiposFigura(



        $ubicaciones = $cartaPorteCFDI->getUbicaciones();

        $ubicacion = new \CfdiUtils\Elements\CartaPorte30\Ubicacion();

        // Ubicaciones Origenes
        $ubicacionesOrigen["TipoUbicacion"] = "Origen";
        $ubicacionesOrigen["IDUbicacion"] = "OR" . str_pad($cartaPorte["IDUbicacionOrigen"], 6, "0", STR_PAD_LEFT);

        if ($cartaPorte["RFCRemitenteDestinatarioOrigen"] != "") {

            $ubicacionesOrigen["RFCRemitenteDestinatario"] = $cartaPorte["RFCRemitenteDestinatarioOrigen"];
        }
        if ($cartaPorte["nombreRazonSocialUbicacionOrigen"] != "") {

            $ubicacionesOrigen["NombreRemitenteDestinatario"] = $cartaPorte["nombreRazonSocialUbicacionOrigen"];
        }


        if ($cartaPorte["FechaHoraSalidaLlegadaOrigen"] != "") {

            $ubicacionesOrigen["FechaHoraSalidaLlegada"] = fechaMySQLADateTimeHTML5($cartaPorte["FechaHoraSalidaLlegadaOrigen"]);
        }


        if ($cartaPorte["LocalidadOrigen"] != "") {

            $ubicacionesOrigenDomicilio["Referencia"] = $cartaPorte["LocalidadOrigen"];
        }

        if ($cartaPorte["LocalidadOrigen"] != "") {

            $ubicacionesOrigenDomicilio["Localidad"] = $cartaPorte["LocalidadOrigen"];
        }

        if ($cartaPorte["MunicipioOrigen"] != "") {

            $ubicacionesOrigenDomicilio["Municipio"] = $cartaPorte["MunicipioOrigen"];
        }

        if ($cartaPorte["EstadoOrigen"] != "") {

            $ubicacionesOrigenDomicilio["Estado"] = $cartaPorte["EstadoOrigen"];
        }

        if ($cartaPorte["PaisOrigen"] != "") {

            $ubicacionesOrigenDomicilio["Pais"] = $cartaPorte["PaisOrigen"];
        }

        if ($cartaPorte["CodigoPostalOrigen"] != "") {

            $ubicacionesOrigenDomicilio["CodigoPostal"] = $cartaPorte["CodigoPostalOrigen"];
        }

        if ($cartaPorte["DistanciaRecorridaOrigen"] != "") {

            $ubicacionesOrigen["DistanciaRecorrida"] = $cartaPorte["DistanciaRecorridaOrigen"];
        }

        //$ubicacion->addAttributes($ubicacionesOrigen);
        $cartaPorteCFDI->addUbicaciones()->addUbicacion($ubicacionesOrigen)->addDomicilio($ubicacionesOrigenDomicilio);

        // Ubicaciones Destino
        $ubicacionesDestino["TipoUbicacion"] = "Destino";
        $ubicacionesDestino["IDUbicacion"] = "DE" . str_pad($cartaPorte["IDUbicacionDestino"], 6, "0", STR_PAD_LEFT);

        if ($cartaPorte["RFCRemitenteDestinatarioDestino"] != "") {


            $ubicacionesDestino["RFCRemitenteDestinatario"] = $cartaPorte["RFCRemitenteDestinatarioDestino"];
        }
        if ($cartaPorte["FechaHoraSalidaLlegadaDestino"] != "") {

            $ubicacionesDestino["FechaHoraSalidaLlegada"] = fechaMySQLADateTimeHTML5($cartaPorte["FechaHoraSalidaLlegadaDestino"]);
        }

        if ($cartaPorte["DistanciaRecorridaDestino"] != "") {

            $ubicacionesDestino["DistanciaRecorrida"] = $cartaPorte["DistanciaRecorridaDestino"];
        }

        if ($cartaPorte["LocalidadDestino"] != "") {

            $ubicacionesDestinoDomicilio["Localidad"] = $cartaPorte["LocalidadDestino"];
        }

        if ($cartaPorte["ReferenciaDestino"] != "") {

            $ubicacionesDestinoDomicilio["Referencia"] = $cartaPorte["ReferenciaDestino"];
        }

        if ($cartaPorte["MunicipioDestino"] != "") {

            $ubicacionesDestinoDomicilio["Municipio"] = $cartaPorte["MunicipioDestino"];
        }

        if ($cartaPorte["EstadoDestino"] != "") {

            $ubicacionesDestinoDomicilio["Estado"] = $cartaPorte["EstadoDestino"];
        }

        if ($cartaPorte["PaisDestino"] != "") {

            $ubicacionesDestinoDomicilio["Pais"] = $cartaPorte["PaisDestino"];
        }

        if ($cartaPorte["CodigoPostalDestino"] != "") {

            $ubicacionesDestinoDomicilio["CodigoPostal"] = $cartaPorte["CodigoPostalDestino"];
        }



        $ubicaciones->addUbicacion($ubicacionesDestino)->addDomicilio($ubicacionesDestinoDomicilio);

        $listMercancias = json_decode($cartaPorte["listMercancias"], true);

        $mercancias = $cartaPorteCFDI->getMercancias();
        $mercancia = new \CfdiUtils\Elements\CartaPorte30\Mercancia();

        $atributosMercancias["PesoBrutoTotal"] = 0;
        $atributosMercancias["UnidadPeso"] = "KGM";
        $atributosMercancias["NumTotalMercancias"] = 0;

        $pesoBrutoTotal = 0;

        foreach ($listMercancias as $key => $value) {

            $atributosMercancias["NumTotalMercancias"]++;
        }

        foreach ($listMercancias as $key => $value) {



            $atributosMercancia["BienesTransp"] = $value["BienesTransp"];

            $atributosMercancia["Descripcion"] = $value["Descripcion"];
            $atributosMercancia["Cantidad"] = $value["Cantidad"];
            $atributosMercancia["ClaveUnidad"] = $value["ClaveUnidad"];
            $atributosMercancia["Unidad"] = $value["Unidad"];

            // $atributosMercancia["MaterialPeligroso"] = $value["MaterialPeligroso"];
            $atributosMercancia["PesoEnKg"] = $value["PesoEnKg"];

            $mercancia->addAttributes($atributosMercancia);

            $atributosMercanciaTransporta["Cantidad"] = $value["Cantidad"];
            $atributosMercanciaTransporta["IDOrigen"] = "OR" . str_pad($cartaPorte["IDUbicacionOrigen"], 6, "0", STR_PAD_LEFT);
            $atributosMercanciaTransporta["IDDestino"] = "DE" . str_pad($cartaPorte["IDUbicacionDestino"], 6, "0", STR_PAD_LEFT);

            $pesoBrutoTotal = $pesoBrutoTotal + $atributosMercancia["PesoEnKg"];

            //$mercancia->addCantidadTransporta($atributosMercanciaTransporta);



            $mercancias->addMercancia($atributosMercancia)->addCantidadTransporta($atributosMercanciaTransporta);
        }

        $atributosMercancias["PesoBrutoTotal"] = $pesoBrutoTotal;

        $mercancias->addAttributes($atributosMercancias);

        $mercancias->addChild($autotransporte);

        // $cartaPorteCFDI->addMercancias($mercancias);

        $comprobanteAtributos = [
            'Serie' => $serie["serie"],
            'Folio' => $cartaPorte["folio"],
            'Fecha' => fechaMySQLADateTimeHTML5(fechaHoraActual()),
            'SubTotal' => "0",
            'Total' => "0",
            'Moneda' => 'XXX',
            'TipoDeComprobante' => 'T',
            'LugarExpedicion' => $empresa["codigoPostal"],
            'Exportacion' => '01',
            'Sello' => '',
        ];

        $creator = new \CfdiUtils\CfdiCreator40($comprobanteAtributos, $certificado);

        $comprobante = $creator->comprobante();

        /*
          $comprobante->addInformacionGlobal([
          'Periodicidad' => '01',
          'Meses' => '06',
          'Año' => '2023',
          ]);

         */

        // No agrego (aunque puedo) el Rfc y Nombre porque uso los que están establecidos en el certificado
        $comprobante->addEmisor([
            'RegimenFiscal' => $empresa["regimenFiscal"], // General de Ley Personas Morales
            'Nombre' => $empresa["razonSocial"], // Agregamos el Nombre por que en el certificado viene SA DE CV
        ]);

        //$comprobante->addReceptor([/* Atributos del receptor */]);

        $comprobante->addReceptor([
            'Rfc' => $empresa["rfc"],
            'Nombre' => $empresa["razonSocial"],
            'DomicilioFiscalReceptor' => $empresa["codigoPostal"],
            'RegimenFiscalReceptor' => $empresa["regimenFiscal"],
            'UsoCFDI' => $cartaPorte["usoCFDI"],
        ]);

        $listProducts = json_decode($cartaPorte["listProducts"], true);

        foreach ($listProducts as $key => $value) {

            $concepto = null;

            if ($value["unidad"] == "" || $value["unidad"] == "NULL") {

                echo "Falta capturar la unidad del producto" . $value["description"];
                return;
            }

            if ($value["claveProductoSAT"] == "" || $value["claveProductoSAT"] == "NULL") {

                echo "Falta capturar la clave del producto o servicio del producto" . $value["description"];
                return;
            }

            if ($value["claveUnidadSAT"] == "" || $value["claveUnidadSAT"] == "NULL") {

                echo "Falta capturar la clave de la unidad del producto" . $value["description"];
                return;
            }

            // Verificamos si lleva Impuesto
            $objetoImpuesto = "01";

            if ($value["porcentTax"] > 0) {


                $objetoImpuesto = "02";
            }


            if ($value["porcentIVARetenido"] > 0) {

                $objetoImpuesto = "02";
            }

            if ($value["porcentISRRetenido"] > 0) {

                $objetoImpuesto = "02";
            }

            $concepto = $comprobante->addConcepto([
                'ClaveProdServ' => $value["claveProductoSAT"],
                'Cantidad' => $value["cant"],
                'ClaveUnidad' => $value["claveUnidadSAT"],
                'Unidad' => $value["unidad"],
                'Descripcion' => $value["description"],
                'ValorUnitario' => $value["price"],
                'Importe' => number_format($value["total"], 2, "."),
                'ObjetoImp' => $objetoImpuesto,
            ]);

            if ($value["porcentTax"] > 0) {


                $porc = number_format(($value["porcentTax"] / 100), 6, ".");

                $importeImpuesto = number_format($value["total"], 2, ".") * $porc;
                $concepto->addTraslado([
                    'Base' => number_format($value["total"], 2, "."),
                    'Impuesto' => '002',
                    'TipoFactor' => 'Tasa',
                    'TasaOCuota' => $porc,
                    'Importe' => number_format($importeImpuesto, 2, "."),
                ]);
            }

            if ($value["porcentIVARetenido"] > 0) {


                $porc = number_format(($value["porcentIVARetenido"] / 100), 6, ".");

                $importeImpuesto = number_format($value["total"], 2, ".") * $porc;
                $concepto->addRetencion([
                    'Base' => number_format($value["total"], 2, "."),
                    'Impuesto' => '002',
                    'TipoFactor' => 'Tasa',
                    'TasaOCuota' => $porc,
                    'Importe' => number_format($importeImpuesto, 2, "."),
                ]);
            }

            if ($value["porcentISRRetenido"] > 0) {


                $porc = number_format(($value["porcentISRRetenido"] / 100), 6, ".");

                $importeImpuesto = number_format($value["total"], 2, ".") * $porc;
                $concepto->addRetencion([
                    'Base' => number_format($value["total"], 2, "."),
                    'Impuesto' => '001',
                    'TipoFactor' => 'Tasa',
                    'TasaOCuota' => $porc,
                    'Importe' => number_format($importeImpuesto, 2, "."),
                ]);
            }
        }

        $comprobante->addComplemento($cartaPorteCFDI);

        //$creator->addSumasConceptos(null, 2);
        // método de ayuda para mover las declaraciones de espacios de nombre al nodo raíz
        $creator->moveSatDefinitionsToComprobante();

        // método de ayuda para validar usando las validaciones estándar de creación de la librería
        $asserts = $creator->validate();
        if ($asserts->hasErrors()) { // contiene si hay o no errores
            print_r(['errors' => $asserts->errors()]);
            return;
        }



        // método de ayuda para generar el xml y retornarlo como un string
        $creator->addSello($csd->privateKey()->pem(), $csd->privateKey()->passPhrase());
        $xml = $creator->asXml();

        if ($serie["ambienteTimbrado"] == "on") {

            $url = "https://services.sw.com.mx/v4/cfdi33/issue/v4";
            $token = $serie["tokenProduccion"];
        } else {

            $url = "https://services.test.sw.com.mx/v4/cfdi33/issue/v4";
            $token = $serie["tokenPruebas"];
        }

        $archivoDestino = $xml;

        $nombreXML = $uuidCartaPorte . ".xml";

        file_put_contents($nombreXML, $archivoDestino);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('xml' => new CURLFILE($nombreXML)),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token . '',
                'customid: ' . $uuidCartaPorte . fechaHoraActual() . '',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        $respuesta = json_decode($response, true);

        if ($respuesta["status"] != "success") {

            echo "error al timbrar " . $response;
            return;
        }

        //unlink($nombreXML);
        // iniciamos transacccion
        $this->xml->db->transBegin();

        //file_put_contents("xmlTimbrado.xml", $respuesta["data"]["cfdi"]);
        // Si se timbro correctamente extraemos los datos y lo guardamos

        $xml = \PhpCfdi\CfdiCleaner\Cleaner::staticClean($respuesta["data"]["cfdi"]);

        // create the main node structure
        $comprobante = \CfdiUtils\Nodes\XmlNodeUtils::nodeFromXmlString($xml);

        // create the CfdiData object, it contains all the required information
        $cfdiData = (new \PhpCfdi\CfdiToPdf\CfdiDataBuilder())
                ->build($comprobante);

        $tfd = $cfdiData->timbreFiscalDigital();
        $emisor = $cfdiData->emisor();
        $receptor = $cfdiData->receptor();

        $datosXML["base16"] = "0.00";
        $datosXML["totalImpuestos16"] = "0.00";

        $datosXML["base8"] = "0.00";
        $datosXML["totalImpuestos8"] = "0.00";

        $impuestos = $comprobante->searchNode('cfdi:Impuestos');

        if (isset($impuestos)) {

            $traslados = $impuestos->searchNodes('cfdi:Traslados', 'cfdi:Traslado');

            foreach ($traslados as $key) {


                if ($key["TasaOCuota"] == 0.16) {

                    $datosXML["base16"] = $datosXML["base16"] + $key["Base"];
                    $datosXML["totalImpuestos16"] = $datosXML["totalImpuestos16"] + $key["Importe"];
                }

                if ($key["TasaOCuota"] == 0.08) {

                    $datosXML["base8"] = $datosXML["base8"] + $key["Base"];
                    $datosXML["totalImpuestos8"] = $datosXML["totalImpuestos8"] + $key["Importe"];
                }
            }
        }


        //$tfd['UUID']



        $datosXML["uuidTimbre"] = $tfd['UUID'];
        $datosXML["uuidPaquete"] = "SISTEMA";
        $datosXML["idEmpresa"] = $cartaPorte["idEmpresa"];
        $datosXML["archivoXML"] = $respuesta["data"]["cfdi"];

        //  $jsonXML = JsonConverter::convertToJson($content);
        //  $arregloXML = json_decode($jsonXML, true);
        $datosXML["fecha"] = $comprobante["Fecha"];
        $datosXML["fechaTimbrado"] = $tfd['FechaTimbrado'];
        $datosXML["total"] = $comprobante["Total"];
        $datosXML["tipoComprobante"] = $comprobante["TipoDeComprobante"];

        $datosXML["rfcEmisor"] = $emisor['Rfc'];
        $datosXML["rfcReceptor"] = $receptor['Rfc'];

        $datosXML["nombreEmisor"] = $emisor['Nombre'];
        $datosXML["nombreReceptor"] = $receptor['Nombre'];

        $datosXML["metodoPago"] = $comprobante['MetodoPago'];
        $datosXML["formaPago"] = $comprobante['FormaPago'];
        $datosXML["usoCFDI"] = $receptor['UsoCFDI'];
        $datosXML["exportacion"] = $comprobante['Exportacion'];
        $datosXML["emitidoRecibido"] = "emitido";

        if (isset($comprobante["Serie"])) {

            $datosXML["serie"] = $comprobante["Serie"];
        } else {

            $datosXML["serie"] = "";
        }

        if (isset($comprobante["Folio"])) {

            $datosXML["folio"] = $comprobante["Folio"];
        } else {

            $datosXML["folio"] = "";
        }

        $totalRen = $this->xml->selectCount("id")->where("uuidTimbre", $datosXML["uuidTimbre"])->first();
        $totalRen = $totalRen["id"];
        if ($totalRen == 0) {

            if ($this->xml->insert($datosXML) === false) {

                $errores = $this->xml->errors();

                $listErrors = "";

                foreach ($errores as $field => $error) {

                    $listErrors .= $error . " ";
                }

                $this->xml->db->transRollback();
                echo $listErrors;

                return;
            }
        }



        /*
         * Insertamos en enlace
         */

        $datosEnlace["idDocumento"] = $cartaPorte["id"];
        $datosEnlace["uuidXML"] = $datosXML["uuidTimbre"];
        $datosEnlace["tipo"] = "tra";
        $datosEnlace["importe"] = $comprobante["Total"];

        if ($this->xmlEnlace->insert($datosEnlace) === false) {

            $errores = $this->xmlEnlace->errors();

            $listErrors = "";

            foreach ($errores as $field => $error) {

                $listErrors .= $error . " ";
            }

            $this->xml->db->transRollback();
            echo $listErrors;

            return;
        }


        $this->xml->db->transCommit();
        echo "success";
    }

    public function timbrarPago($uuidPago) {


        if (!isset($catalogos) || !($catalogos instanceof \PhpCfdi\CfdiToPdf\Catalogs\CatalogsInterface)) {
            $catalogos = new \PhpCfdi\CfdiToPdf\Catalogs\StaticCatalogs();
        }


        $pago = $this->pagos->select("*")->where("UUID ", $uuidPago)->first();

        $facturaExistentes = $this->xmlEnlace->select("id")
                ->where("idDocumento", $pago["id"])
                ->where("tipo", "pag")
                ->countAllResults();

        if ($facturaExistentes > 0) {

            echo "success";
            return;
        }



        $serie = $this->serieElectronica->select("*")
                        ->where("idEmpresa", $pago["idEmpresa"])
                        ->where("tipoSerie", "pag")
                        ->where("sucursal", $pago["idSucursal"])
                        ->where("desdeFolio <=", $pago["folio"])
                        ->where("hastaFolio >=", $pago["folio"])->first();

// VERIFICAMOS SI YA TIENE VENTA

        if (!isset($serie)) {

            echo "No hay serie electronica configurada";
            return;
        }

        $empresa = $this->empresa->find($pago["idEmpresa"]);

        $certificado = new \CfdiUtils\Certificado\Certificado(ROOTPATH . "writable/uploads/certificates/$empresa[certificadoCSD]");

        if ($pago["usoCFDI"] == "" || $pago["usoCFDI"] == "NULL") {

            echo "Falta capturar el uso del CFDI";
            return;
        }

        $cerfile = ROOTPATH . "writable/uploads/certificates/$empresa[certificadoCSD]";
        $keyfile = ROOTPATH . "writable/uploads/certificates/$empresa[archivoKeyCSD]";
        $passPhrase = $empresa["contraCertificadoCSD"];

        $csd = Credential::openFiles($cerfile, $keyfile, $passPhrase);

        $comprobanteAtributos = [
            'Serie' => $serie["serie"],
            'Folio' => $pago["folio"],
            'Fecha' => fechaMySQLADateTimeHTML5(fechaMySQLADateHTML5(fechaHoraActual())),
            'SubTotal' => "0",
            'Total' => "0",
            'Moneda' => 'XXX',
            'TipoDeComprobante' => 'P',
            'LugarExpedicion' => $empresa["codigoPostal"],
            'Exportacion' => '01',
            'Sello' => '',
        ];

        $creator = new \CfdiUtils\CfdiCreator40($comprobanteAtributos, $certificado);

        $comprobante = $creator->comprobante();

        /*
          $comprobante->addInformacionGlobal([
          'Periodicidad' => '01',
          'Meses' => '06',
          'Año' => '2023',
          ]);

         */

        // No agrego (aunque puedo) el Rfc y Nombre porque uso los que están establecidos en el certificado
        $comprobante->addEmisor([
            'RegimenFiscal' => $empresa["regimenFiscal"], // General de Ley Personas Morales
            'Nombre' => $empresa["razonSocial"], // Agregamos el Nombre por que en el certificado viene SA DE CV
        ]);

        //$comprobante->addReceptor([/* Atributos del receptor */]);

        $comprobante->addReceptor([
            'Rfc' => $pago["RFCReceptor"],
            'Nombre' => $pago["razonSocialReceptor"],
            'DomicilioFiscalReceptor' => $pago["codigoPostalReceptor"],
            'RegimenFiscalReceptor' => $pago["regimenFiscalReceptor"],
            'UsoCFDI' => $pago["usoCFDI"],
        ]);

        $ventasPagadas = $this->payments->select("*")->where("idComplemento", $pago["id"])->findAll();

        $concepto = $comprobante->addConcepto([
            'ClaveProdServ' => "84111506",
            'Cantidad' => "1",
            'ClaveUnidad' => "ACT",
            'Descripcion' => "Pago",
            'ValorUnitario' => "0",
            'Importe' => "0",
            'ObjetoImp' => "01",
        ]);

        $pagosElemento = new \CfdiUtils\Elements\Pagos20\Pagos();

        $montoAPagar = $pago["total"];

        $pagoDocto = $pagosElemento->addPago([
            'Monto' => number_format($montoAPagar, 2, ".", ""),
            'MonedaP' => "MXN",
            'TipoCambioP' => "1",
            'FormaDePagoP' => $pago["formaPago"],
            'FechaPago' => fechaMySQLADateTimeHTML5(fechaMySQLADateTimeHTML5($pago["date"]))
        ]);

        $creator->hasXmlResolver();
        foreach ($ventasPagadas as $key => $value) {


            $venta = $this->sells->select("*")->where("id", $value["idSell"])->first();
            $enlace = $this->xmlEnlace->select("*")->where("idDocumento", $venta["id"])->first();
            $xmlVenta = $this->xml->select("*")->where("uuidTimbre", $enlace["uuidXML"])->first();

            $totalRetenido = $venta["IVARetenido"] + $venta["ISRRetenido"];

            $ivaRetenido = $venta["IVARetenido"];

            $parcialidad = $this->xml->mdlParcialidadVenta($venta["id"], $pago["id"]) + 1;

            $saldo = $this->xml->mdlSaldo($venta["id"], $pago["id"]);

            $totalDeVenta = $venta["total"];

            $totalDeFactura = $xmlVenta["total"];

            if ($totalDeVenta > $totalDeFactura) {

                if (($totalDeFactura - $value["importPayment"]) <= 0.1) {

                    $value["importPayment"] = $totalDeFactura;
                }
            }


            $porcentajePagado = $xmlVenta["total"] / $value["importPayment"];

            $xml = \PhpCfdi\CfdiCleaner\Cleaner::staticClean($xmlVenta["archivoXML"]);

            // create the main node structure
            $comprobanteFactura = \CfdiUtils\Nodes\XmlNodeUtils::nodeFromXmlString($xml);

            // create the CfdiData object, it contains all the required information
            $cfdiData = (new \PhpCfdi\CfdiToPdf\CfdiDataBuilder())
                    ->build($comprobanteFactura);

            $comprobanteFactura = $cfdiData->comprobante();

            $totalImpuestosTrasladados = $comprobante->searchAttribute('cfdi:Impuestos', 'TotalImpuestosTrasladados');
            $totalImpuestosRetenidos = $comprobante->searchAttribute('cfdi:Impuestos', 'TotalImpuestosRetenidos');

            $conceptos = $comprobanteFactura->searchNodes('cfdi:Conceptos', 'cfdi:Concepto');

            foreach ($conceptos as $concepto) {
                $conceptoTraslados = $concepto->searchNodes('cfdi:Impuestos', 'cfdi:Traslados', 'cfdi:Traslado');
                $conceptoRetenciones = $concepto->searchNodes('cfdi:Impuestos', 'cfdi:Retenciones', 'cfdi:Retencion');
            }

            //Objeto de impuestos documento relacionado
            $ObjetoImpDR = "01";

            //Tasa Excenta calculo
            if ($xmlVenta["tasaExenta"] > 0) {



                $ObjetoImpDR = "01";
                $porcBase16 = $xmlVenta["tasaExenta"] / $xmlVenta["total"];
                $montoPagoTasaExenta = number_format($montoAPagar * $porcBase16, 6);

                $trastalado["TipoFactorDR"] = "Exento";
                $trastalado["ImpuestoDR"] = "002";
                $trastalado["BaseDR"] = $montoPagoTasaExenta;
            }

            /*
              if ($xmlVenta["base16"] > 0) {



              $ObjetoImpDR = "02";

              $totalRetenido = $totalRetenido * $porcentajePagado;

              $porcBase16 = "0.160000";
              $montoPagoIVA16 = number_format($xmlVenta["totalImpuestos16"] * $porcentajePagado, 6, ".", "");
              $basePago16 = number_format($xmlVenta["base16"] * $porcentajePagado, 6, ".", "");

              $trastalado["ImporteDR"] = $montoPagoIVA16;
              $trastalado["TasaOCuotaDR"] = "0.160000";
              $trastalado["TipoFactorDR"] = "Tasa";
              $trastalado["ImpuestoDR"] = "002";
              $trastalado["BaseDR"] = $basePago16;
              }


              if ($xmlVenta["base8"] > 0) {



              $ObjetoImpDR = "02";

              $totalRetenido = $totalRetenido * $porcentajePagado;

              $porcBase08 = "0.080000";
              $montoPagoIVA08 = number_format($xmlVenta["totalImpuestos8"] * $porcentajePagado, 6, ".", "");
              $basePago08 = number_format($xmlVenta["base8"] * $porcentajePagado, 6, ".", "");

              $trastalado["ImporteDR"] = $montoPagoIVA08;
              $trastalado["TasaOCuotaDR"] = "0.080000";
              $trastalado["TipoFactorDR"] = "Tasa";
              $trastalado["ImpuestoDR"] = "002";
              $trastalado["BaseDR"] = $basePago08;
              }



             */

            foreach ($conceptoTraslados as $impuesto) {

                $ObjetoImpDR = "02";
            }

            foreach ($conceptoRetenciones as $impuesto) {

                $ObjetoImpDR = "02";
            }

            $doctumentoRelacionado = $pagoDocto->addDoctoRelacionado([
                'Folio' => $xmlVenta["folio"],
                'Serie' => $xmlVenta["serie"],
                'IdDocumento' => $xmlVenta["uuidTimbre"],
                'MonedaDR' => "MXN",
                'ObjetoImpDR' => $ObjetoImpDR,
                'EquivalenciaDR' => "1",
                'NumParcialidad' => $parcialidad,
                'ImpSaldoAnt' => $totalDeFactura - $saldo,
                'ImpSaldoInsoluto' => number_format(($totalDeFactura - $saldo) - $value["importPayment"],2,".",""),
                'ImpPagado' => number_format($value["importPayment"], 2, ".", ""),
            ]);
            /*
              if ($xmlVenta["base16"] > 0) {
              $doctumentoRelacionado->addImpuestosDR()->addTrasladosDR()->addTrasladoDR($trastalado);
              }
             */

            foreach ($conceptoTraslados as $impuesto) {


                if (isset($impuesto['Importe'])) {


                    $ObjetoImpDR = "02";

                    $total = $totalRetenido * $porcentajePagado;

                    $porcBase = $this->e($impuesto['TasaOCuota']);
                    $montoPagoIVA = $impuesto['Importe'] * $porcentajePagado;
                    $basePago = number_format($this->e($impuesto['Base']) * $porcentajePagado, 6, ".", "");

                    if ($basePago * ($this->e($impuesto['TasaOCuota']) * 0.01) != number_format($montoPagoIVA, 6, ".", "")) {


                        $impuestoImporte = number_format($basePago * ($this->e($impuesto['TasaOCuota'])), 6, ".", "");
                    } else {


                        $impuestoImporte = number_format($montoPagoIVA, 6, ".", "");
                    }


                    $trastalado["ImporteDR"] = number_format($impuestoImporte, 6, ".", "");
                    $trastalado["TasaOCuotaDR"] = $this->e($impuesto['TasaOCuota']);
                    $trastalado["TipoFactorDR"] = $this->e($impuesto['TipoFactor']);
                    $trastalado["ImpuestoDR"] = $this->e($impuesto['Impuesto']);
                    $trastalado["BaseDR"] = $basePago;

                    $doctumentoRelacionado->addImpuestosDR()->addTrasladosDR()->addTrasladoDR($trastalado);
                }
            }


            foreach ($conceptoRetenciones as $impuesto) {


                $ObjetoImpDR = "02";

                $total = $totalRetenido * $porcentajePagado;

                $porcBase = $this->e($impuesto['TasaOCuota']);
                $montoPagoIVA = $this->e($impuesto['Importe']) * $porcentajePagado;
                $basePago = number_format($this->e($impuesto['Base']) * $porcentajePagado, 6, ".", "");

                //Verificamos calculo

                if ($basePago * ($this->e($impuesto['TasaOCuota']) * 0.01) != number_format($montoPagoIVA, 6, ".", "")) {


                    $impuestoImporte = number_format($basePago * ($this->e($impuesto['TasaOCuota'])), 6, ".", "");
                } else {


                    $impuestoImporte = number_format($montoPagoIVA, 6, ".", "");
                }

                $retenciones["ImporteDR"] = number_format($impuestoImporte, 6, ".", "");
                $retenciones["TasaOCuotaDR"] = $this->e($impuesto['TasaOCuota']);
                $retenciones["TipoFactorDR"] = $this->e($impuesto['TipoFactor']);
                $retenciones["ImpuestoDR"] = $this->e($impuesto['Impuesto']);
                $retenciones["BaseDR"] = $basePago;

                $doctumentoRelacionado->addImpuestosDR()->addRetencionesDR()->addRetencionDR($retenciones);
            }
        }

        PagosWriter::calculateAndPut($pagosElemento);

        /*
          // Se puede calcular y mandar a escribir
          $pagosCalculator = new Calculator(
          2, // Decimales a usar en los impuestos de los pagos
          new Currencies(['MXN' => 2, 'USD' => '2', 'EUR' => 2]) // Monedas con decimales
          );
          $result = $pagosCalculator->calculate($pagosElemento);
          $pagosWriter = new PagosWriter($pagosElemento);
          $pagosWriter->writePago($pagosElemento);
         */


// $creator->addSumasConceptos(null, 2);
        // método de ayuda para mover las declaraciones de espacios de nombre al nodo raíz
        $comprobante->addComplemento($pagosElemento);
        $creator->moveSatDefinitionsToComprobante();

        $xml = $creator->asXml();
        $archivoDestino = $xml;

        $nombreXML = $uuidPago . ".xml";

        file_put_contents($nombreXML, $archivoDestino);

        // método de ayuda para validar usando las validaciones estándar de creación de la librería
        $asserts = $creator->validate();
        if ($asserts->hasErrors()) { // contiene si hay o no errores
            print_r(['errors' => $asserts->errors()]);
            return;
        }



        // método de ayuda para generar el xml y retornarlo como un string
        $creator->addSello($csd->privateKey()->pem(), $csd->privateKey()->passPhrase());

        if ($serie["ambienteTimbrado"] == "on") {

            $url = "https://services.sw.com.mx/v4/cfdi33/issue/v4";
            $token = $serie["tokenProduccion"];
        } else {

            $url = "https://services.test.sw.com.mx/v4/cfdi33/issue/v4";
            $token = $serie["tokenPruebas"];
        }

        $archivoDestino = $xml;

        $nombreXML = $uuidPago . ".xml";

        file_put_contents($nombreXML, $archivoDestino);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('xml' => new CURLFILE($nombreXML)),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $token . '',
                'customid: ' . $uuidPago . fechaHoraActual() . '',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if (!$response) {

            echo "Error al conectar con el PAC";

            return;
        }

        $respuesta = json_decode($response, true);

        if ($respuesta["status"] != "success") {

            echo "error al timbrar " . $response;
            return;
        }

        unlink($nombreXML);

        // iniciamos transacccion
        $this->xml->db->transBegin();

        //file_put_contents("xmlTimbrado.xml", $respuesta["data"]["cfdi"]);
        // Si se timbro correctamente extraemos los datos y lo guardamos

        $xml = \PhpCfdi\CfdiCleaner\Cleaner::staticClean($respuesta["data"]["cfdi"]);

        // create the main node structure
        $comprobante = \CfdiUtils\Nodes\XmlNodeUtils::nodeFromXmlString($xml);

        // create the CfdiData object, it contains all the required information
        $cfdiData = (new \PhpCfdi\CfdiToPdf\CfdiDataBuilder())
                ->build($comprobante);

        $tfd = $cfdiData->timbreFiscalDigital();
        $emisor = $cfdiData->emisor();
        $receptor = $cfdiData->receptor();

        $datosXML["base16"] = "0.00";
        $datosXML["totalImpuestos16"] = "0.00";

        $datosXML["base8"] = "0.00";
        $datosXML["totalImpuestos8"] = "0.00";

        $impuestos = $comprobante->searchNode('cfdi:Impuestos');

        if (isset($impuestos)) {

            $traslados = $impuestos->searchNodes('cfdi:Traslados', 'cfdi:Traslado');

            foreach ($traslados as $key) {


                if ($key["TasaOCuota"] == 0.16) {

                    $datosXML["base16"] = $datosXML["base16"] + $key["Base"];
                    $datosXML["totalImpuestos16"] = $datosXML["totalImpuestos16"] + $key["Importe"];
                }

                if ($key["TasaOCuota"] == 0.08) {

                    $datosXML["base8"] = $datosXML["base8"] + $key["Base"];
                    $datosXML["totalImpuestos8"] = $datosXML["totalImpuestos8"] + $key["Importe"];
                }
            }
        }


        //$tfd['UUID']



        $datosXML["uuidTimbre"] = $tfd['UUID'];
        $datosXML["uuidPaquete"] = "SISTEMA";
        $datosXML["idEmpresa"] = $venta["idEmpresa"];
        $datosXML["archivoXML"] = $respuesta["data"]["cfdi"];

        //  $jsonXML = JsonConverter::convertToJson($content);
        //  $arregloXML = json_decode($jsonXML, true);
        $datosXML["fecha"] = $comprobante["Fecha"];
        $datosXML["fechaTimbrado"] = $tfd['FechaTimbrado'];
        $datosXML["total"] = $comprobante["Total"];
        $datosXML["tipoComprobante"] = $comprobante["TipoDeComprobante"];

        $datosXML["rfcEmisor"] = $emisor['Rfc'];
        $datosXML["rfcReceptor"] = $receptor['Rfc'];

        $datosXML["nombreEmisor"] = $emisor['Nombre'];
        $datosXML["nombreReceptor"] = $receptor['Nombre'];

        $datosXML["metodoPago"] = $comprobante['MetodoPago'];
        $datosXML["formaPago"] = $comprobante['FormaPago'];
        $datosXML["usoCFDI"] = $receptor['UsoCFDI'];
        $datosXML["exportacion"] = $comprobante['Exportacion'];
        $datosXML["emitidoRecibido"] = "emitido";

        if (isset($comprobante["Serie"])) {

            $datosXML["serie"] = $comprobante["Serie"];
        } else {

            $datosXML["serie"] = "";
        }

        if (isset($comprobante["Folio"])) {

            $datosXML["folio"] = $comprobante["Folio"];
        } else {

            $datosXML["folio"] = "";
        }

        $totalRen = $this->xml->selectCount("id")->where("uuidTimbre", $datosXML["uuidTimbre"])->first();
        $totalRen = $totalRen["id"];
        if ($totalRen == 0) {

            if ($this->xml->insert($datosXML) === false) {

                $errores = $this->xml->errors();

                $listErrors = "";

                foreach ($errores as $field => $error) {

                    $listErrors .= $error . " ";
                }

                $this->xml->db->transRollback();
                echo $listErrors;

                return;
            }
        }



        /*
         * Insertamos en enlace
         */

        $datosEnlace["idDocumento"] = $pago["id"];
        $datosEnlace["uuidXML"] = $datosXML["uuidTimbre"];
        $datosEnlace["tipo"] = "pag";
        $datosEnlace["importe"] = $montoAPagar;

        if ($this->xmlEnlace->insert($datosEnlace) === false) {

            $errores = $this->xmlEnlace->errors();

            $listErrors = "";

            foreach ($errores as $field => $error) {

                $listErrors .= $error . " ";
            }

            $this->xml->db->transRollback();
            echo $listErrors;

            return;
        }


        $this->xml->db->transCommit();
        echo "success";
    }

    public function e($string, $functions = null) {
        return $this->escape($string, $functions);
    }

    /**
     * Apply multiple functions to variable.
     * @param  mixed  $var
     * @param  string $functions
     * @return mixed
     */
    public function batch($var, $functions) {
        foreach (explode('|', $functions) as $function) {
            if ($this->engine->doesFunctionExist($function)) {
                $var = call_user_func(array($this, $function), $var);
            } elseif (is_callable($function)) {
                $var = call_user_func($function, $var);
            } else {
                throw new LogicException(
                                'The batch function could not find the "' . $function . '" function.'
                );
            }
        }

        return $var;
    }

    /**
     * Escape string.
     * @param  string      $string
     * @param  null|string $functions
     * @return string
     */
    public function escape($string, $functions = null) {
        static $flags;

        if (!isset($flags)) {
            $flags = ENT_QUOTES | (defined('ENT_SUBSTITUTE') ? ENT_SUBSTITUTE : 0);
        }

        if ($functions) {
            $string = $this->batch($string, $functions);
        }

        return htmlspecialchars($string ?? '', $flags, 'UTF-8');
    }
}
