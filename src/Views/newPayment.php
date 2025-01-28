<?= $this->include('load/toggle') ?>
<?= $this->include('julio101290\boilerplate\Views\load\select2') ?>
<?= $this->include('julio101290\boilerplate\Views\load\datatables') ?>
<?= $this->include('julio101290\boilerplate\Views\load\nestable') ?>
<!-- Extend from layout index -->
<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>

<!-- Section content -->
<?= $this->section('content') ?>

<?= $this->include('modulesPayment/dataHeadPayment') ?>
<?= $this->include('modulesPayment/moreInfoRow') ?>
<?= $this->include('modulesChoferes/modalCaptureChoferes') ?>
<?= $this->include('modulesVehiculos/modalCaptureVehiculos') ?>

<?= $this->endSection() ?>
