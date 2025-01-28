<?php

$reportSell["title"] = "Sales Report";
$reportSell["subtitle"] = "Sales by Company, Branch, Product";
$reportSell["companie"] = "Company";
$reportSell["allCompanies"] = "All Companies";
$reportSell["branchOffice"] = "Branch";
$reportSell["allBranchOffice"] = "All Branches";
$reportSell["products"] = "Products";
$reportSell["allProducts"] = "All Products";

$reportSell["custumer"] = "Customer"; // Corrected spelling
$reportSell["allCustumes"] = "All Customers"; // Corrected spelling and pluralized
$reportSell["load"] = "Load";
$reportSell["field"]["row"] = "#";
$reportSell["field"]["companie"] = "Company";
$reportSell["field"]["branchOffice"] = "Branch";
$reportSell["field"]["folio"] = "Folio"; // Folio is often kept as is, but "Reference Number" or "Invoice Number" could also be used depending on context.

$reportSell["field"]["date"] = "Date";
$reportSell["field"]["nameCustumer"] = "Customer Name";
$reportSell["field"]["lastNameCustumer"] = "Customer Last Name";
$reportSell["field"]["socialReasonCustumer"] = "Customer Business Name"; // Or "Customer Company Name"
$reportSell["field"]["idProduct"] = "Product ID";

$reportSell["field"]["codeProduct"] = "Product Code";
$reportSell["field"]["description"] = "Description";
$reportSell["field"]["amount"] = "Quantity";
$reportSell["field"]["price"] = "Price";
$reportSell["field"]["total"] = "Total";

$reportSell["field"]["tax"] = "Tax";
$reportSell["field"]["granTotal"] = "Net Total"; // Or "Grand Total" if it includes tax. "Net" usually means before tax.

return $reportSell;
