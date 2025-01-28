<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

 namespace julio101290\boilerplatesells\Controllers;

/**
 * Description of PDFLayout
 *
 * @author hp
 */
class PDFLayoutSells extends \TCPDF
{

    public $nombreEmpresa = "";
    public $direccion = "";
    public $usuario = "";
    public $nombreDocumento = "";
    public $logo = "";
    public $telefono = "";
    public $email = "";

    public $folio = "";

    public function __construct()
    {
        parent::__construct();
    }

    public function Header()
    {
        $fecha = date('d/m/Y');

        /*
        $documento = "Hoja de Inspeccion";
        $logo = ROOTPATH."public/logo.png";

        $direccion1 = "Ave. Estrella Sadhala No. 204, Santiago";
        $telefono1 = "(809)-724-4300";

        $direccion2 = "Ave. Charles Summer No. 21, Santo Domingo";
        $telefono2 = "(809)-566-6191";

        $email = "ohtsudelcaribe@gmail.com";
        */

        $direccion = $this->direccion;
        $telefono = $this->telefono;
        $email = $this->email;
        $logo = $this->logo;

        //$this->writeHTML($fecha, false, false, false, false, 'L');
        $this->SetFont('dejavusans', 'B', 14);
        $this->writeHTML($this->nombreDocumento, true, false, false, false, 'C');
        
      
        $this->SetFont('dejavusans', '', 12);

 
        $this->writeHTML($this->nombreEmpresa,true, false, false, false, 'C');
        $this->Image($logo, 15, 6, 25, '', 'png', '', 'T', false, 300, '', false, false, 0, false, false, false);
        
        $this->SetY(16);
        $this->writeHTML($this->direccion,true, false, false, false, 'C');
        
        $this->Image($logo, 15, 6, 25, '', 'png', '', 'T', false, 300, '', false, false, 0, false, false, false);
      
        $this->SetFont('dejavusans', 'B', 14);    
        $this->SetY(8);
        $this->Cell(350, 25, 'Folio', 0, false, 'C', 0, '', 0, false, 'M', 'M');

        $this->SetY(15);
        $this->SetTextColor(255,0,0);
        $this->Cell(350, 25, $this->folio, 0, false, 'C', 0, '', 0, false, 'M', 'M');


       
    }

    // Page footer
    public function Footer()
    {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 10, $this->nombreDocumento . " " . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

}