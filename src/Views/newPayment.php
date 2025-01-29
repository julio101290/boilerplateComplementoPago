<?= $this->include('julio101290\boilerplate\Views\load/toggle') ?>
<?= $this->include('julio101290\boilerplate\Views\load\select2') ?>
<?= $this->include('julio101290\boilerplate\Views\load\datatables') ?>
<?= $this->include('julio101290\boilerplate\Views\load\nestable') ?>
<!-- Extend from layout index -->
<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>

<!-- Section content -->
<?= $this->section('content') ?>

<?= $this->include('julio101290\boilerplatecomplementopago\Views\modulesPayment/dataHeadPayment') ?>
<?= $this->include('julio101290\boilerplatecomplementopago\Views\modulesPayment/moreInfoRow') ?>
<?= $this->include('julio101290\boilerplatedrivers\Views\modulesChoferes/modalCaptureChoferes') ?>
<?= $this->include('julio101290\boilerplatevehicles\Views\modulesVehiculos/modalCaptureVehiculos') ?>

<?= $this->endSection() ?>
