<?php

$reportSell["title"] = "Verkaufsbericht";
$reportSell["subtitle"] = "Verkäufe nach Firma, Filiale, Produkt";
$reportSell["companie"] = "Firma";
$reportSell["allCompanies"] = "Alle Firmen";
$reportSell["branchOffice"] = "Filiale";
$reportSell["allBranchOffice"] = "Alle Filialen";
$reportSell["products"] = "Produkte";
$reportSell["allProducts"] = "Alle Produkte";

$reportSell["custumer"] = "Kunde";
$reportSell["allCustumes"] = "Alle Kunden";
$reportSell["load"] = "Laden";
$reportSell["field"]["row"] = "#";
$reportSell["field"]["companie"] = "Firma";
$reportSell["field"]["branchOffice"] = "Filiale";
$reportSell["field"]["folio"] = "Folio"; // Como en inglés, se puede dejar "Folio" o usar "Referenznummer", "Rechnungsnummer" o "Belegnummer" según el contexto.

$reportSell["field"]["date"] = "Datum";
$reportSell["field"]["nameCustumer"] = "Kundenname";
$reportSell["field"]["lastNameCustumer"] = "Kunden Nachname";
$reportSell["field"]["socialReasonCustumer"] = "Firmenname des Kunden"; // o "Gesellschaftsname des Kunden"
$reportSell["field"]["idProduct"] = "Produkt-ID";

$reportSell["field"]["codeProduct"] = "Produktcode";
$reportSell["field"]["description"] = "Beschreibung";
$reportSell["field"]["amount"] = "Menge";
$reportSell["field"]["price"] = "Preis";
$reportSell["field"]["total"] = "Gesamtbetrag";

$reportSell["field"]["tax"] = "Steuer";
$reportSell["field"]["granTotal"] = "Nettobetrag"; // o "Bruttobetrag" si incluye impuestos.

return $reportSell;
