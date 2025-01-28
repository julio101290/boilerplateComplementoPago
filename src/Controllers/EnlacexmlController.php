<?php

namespace julio101290\boilerplatesells\Controllers;

use App\Controllers\BaseController;
use julio101290\boilerplatesells\Models\{
    EnlacexmlModel
};
use julio101290\boilerplatelog\Models\LogModel;
use CodeIgniter\API\ResponseTrait;
use julio101290\boilerplatecompanies\Models\EmpresasModel;

class EnlacexmlController extends BaseController {

    use ResponseTrait;

    protected $log;
    protected $enlacexml;

    public function __construct() {
        $this->enlacexml = new EnlacexmlModel();
        $this->log = new LogModel();
        $this->empresa = new EmpresasModel();
        helper('menu');
        helper('utilerias');
    }

    public function index() {



        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }




        if ($this->request->isAJAX()) {
            $datos = $this->enlacexml->mdlGetEnlacexml($empresasID);

            return \Hermawan\DataTables\DataTable::of($datos)->toJson(true);
        }
        $titulos["title"] = lang('enlacexml.title');
        $titulos["subtitle"] = lang('enlacexml.subtitle');
        return view('enlacexml', $titulos);
    }

    /**
     * Read Enlacexml
     */
    public function getEnlacexml() {

        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }


        $idEnlacexml = $this->request->getPost("idEnlacexml");
        $datosEnlacexml = $this->enlacexml->whereIn('idEmpresa', $empresasID)
                        ->where("id", $idEnlacexml)->first();
        echo json_encode($datosEnlacexml);
    }

    /**
     * Save or update Enlacexml
     */
    public function save() {
        helper('auth');
        $userName = user()->username;
        $idUser = user()->id;
        $datos = $this->request->getPost();
        if ($datos["idEnlacexml"] == 0) {
            try {
                if ($this->enlacexml->save($datos) === false) {
                    $errores = $this->enlacexml->errors();
                    foreach ($errores as $field => $error) {
                        echo $error . " ";
                    }
                    return;
                }
                $dateLog["description"] = lang("vehicles.logDescription") . json_encode($datos);
                $dateLog["user"] = $userName;
                $this->log->save($dateLog);
                echo "Guardado Correctamente";
            } catch (\PHPUnit\Framework\Exception $ex) {
                echo "Error al guardar " . $ex->getMessage();
            }
        } else {
            if ($this->enlacexml->update($datos["idEnlacexml"], $datos) == false) {
                $errores = $this->enlacexml->errors();
                foreach ($errores as $field => $error) {
                    echo $error . " ";
                }
                return;
            } else {
                $dateLog["description"] = lang("enlacexml.logUpdated") . json_encode($datos);
                $dateLog["user"] = $userName;
                $this->log->save($dateLog);
                echo "Actualizado Correctamente";
                return;
            }
        }
        return;
    }

    /**
     * Delete Enlacexml
     * @param type $id
     * @return type
     */
    public function delete($id) {
        $infoEnlacexml = $this->enlacexml->find($id);
        helper('auth');
        $userName = user()->username;
        if (!$found = $this->enlacexml->delete($id)) {
            return $this->failNotFound(lang('enlacexml.msg.msg_get_fail'));
        }
        $this->enlacexml->purgeDeleted();
        $logData["description"] = lang("enlacexml.logDeleted") . json_encode($infoEnlacexml);
        $logData["user"] = $userName;
        $this->log->save($logData);
        return $this->respondDeleted($found, lang('enlacexml.msg_delete'));
    }

}
