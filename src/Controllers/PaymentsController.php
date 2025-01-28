<?php

namespace julio101290\boilerplatesells\Controllers;

use App\Controllers\BaseController;
use julio101290\boilerplatesells\Models\{
    PaymentsModel
};
use julio101290\boilerplatelog\Models\LogModel;
use CodeIgniter\API\ResponseTrait;
use julio101290\boilerplatesells\Models\SellsModel;
use Exception;

class PaymentsController extends BaseController {

    use ResponseTrait;

    protected $log;
    protected $payments;
    protected $sells;

    public function __construct() {
        $this->payments = new PaymentsModel();
        $this->log = new LogModel();
        $this->sells = new SellsModel();
        helper('menu');
    }

    public function index() {
        if ($this->request->isAJAX()) {
            $datos = $this->payments->select('id,idSell,importPayment,importBack,datePayment,metodPayment,created_at,updated_at,deleted_at')->where('deleted_at', null);
            return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);
        }
        $titulos["title"] = lang('payments.title');
        $titulos["subtitle"] = lang('payments.subtitle');
        return view('payments', $titulos);
    }

    /**
     * Read Payments
     */
    public function getPayments() {
        $idPayments = $this->request->getPost("idPayments");
        $datosPayments = $this->payments->find($idPayments);
        echo json_encode($datosPayments);
    }

    /**
     * Save or update Payments
     */
    public function save() {
        helper('auth');
        $userName = user()->username;
        $idUser = user()->id;
        $datos = $this->request->getPost();

        $auth = service('authentication');
        if (!$auth->check()) {

            echo "No ha iniciado Session";
            return;
        }

        helper('auth');
        $userName = user()->username;
        $idUser = user()->id;

        $this->payments->db->transBegin();

        $datosVenta = $this->sells->select("*")->where("UUID", $datos["UUID"])->asArray()->first();

        $datos["idSell"] = $datosVenta["id"];

        try {



            if ($this->payments->save($datos) === false) {

                $errores = $this->payments->errors();
                foreach ($errores as $field => $error) {
                    echo $error . " ";
                }

                $this->payments->DB::rollback();
                return;
            }
            $dateLog["description"] = lang("vehicles.logDescription") . json_encode($datos);
            $dateLog["user"] = $userName;
            $this->log->save($dateLog);

            try {

                $datosVenta["balance"] = $datosVenta["balance"] - ($datos["importPayment"] - $datos["importBack"]);

                echo $datosVenta["balance"];
                $this->sells->update($datosVenta["id"], $datosVenta);

                $this->payments->transCommit();
            } catch (Exception $e) {
                
            }


            echo "Guardado Correctamente";
        } catch (\PHPUnit\Framework\Exception $ex) {
            echo "Error al guardar " . $ex->getMessage();
            $this->payments->DB::rollback();
        }

        return;
    }

    public function ctrGetPayments($uuid) {

        helper('auth');
        $userName = user()->username;
        $idUser = user()->id;
        $datos = $this->request->getPost();

        $auth = service('authentication');
        if (!$auth->check()) {

            echo "No ha iniciado Session";
            return;
        }

        $sell = $this->sells->select("*")->where("UUID", $uuid)->first();

        if (!isset($sell["id"])) {

            $sell["id"] = 0;
        }

        $datos = $this->payments
                ->select('id'
                        . ',idSell'
                        . ',importPayment'
                        . ',importBack'
                        . ',datePayment'
                        . ',metodPayment'
                        . ',observaciones'
                        . ',tipo'
                        . ',idNotaCredito'
                        . ',created_at'
                        . ',updated_at'
                        . ',deleted_at')
                ->where('deleted_at', null)
                ->where('idSell', $sell["id"]);

        return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);
    }

    /**
     * Delete Payments
     * @param type $id
     * @return type
     */
    public function delete($id) {
        
        $infoPayments = $this->payments->find($id);
        
        $dataSell = $this->sells->find($infoPayments["idSell"]);
        
        
        
        $totalPago = $infoPayments["importPayment"] - $infoPayments["importBack"];
        
        $dataSellSave["balance"] = $dataSell["balance"] + $totalPago;
        
        
        
                
        helper('auth');

        $auth = service('authentication');
        if (!$auth->check()) {
            
            echo "no conectado";
            return redirect()->route('login');
        }
        $userName = user()->username;
        if (!$found = $this->payments->delete($id)) {
            return $this->failNotFound(lang('payments.msg.msg_get_fail'));
        }
        
        /**
         * Actualizamos saldo
         */
        
        $resultVenta = $this->sells->update($dataSell["id"],$dataSellSave);
        
        $this->payments->purgeDeleted();
        $logData["description"] = lang("payments.logDeleted") . json_encode($infoPayments);
        $logData["user"] = $userName;
        $this->log->save($logData);
        return $this->respondDeleted($found, lang('payments.msg_delete'));
    }

}
