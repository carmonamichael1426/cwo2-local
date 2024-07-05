<!-- Main Footer -->
<footer class="main-footer">
    <!-- Default to the left -->
    <strong>&copy; ALTURAS GROUP OF COMPANIES | CWO.</strong> All rights reserved.

    <!-- to the right -->
    <div class="float-right d-none d-sm-inline-block">
        <b class="text-primary">For inquiries, you may reach us <a href="<?php echo base_url(); ?>contactus"
                class="alert-link"> here </a> or look for us <a href="<?php echo base_url(); ?>about"
                class="alert-link"> here </a> .</b>
    </div>

</footer>

<!-- ./wrapper -->
<?php include './application/views/components/myAlert.php'; ?>
<script>
    window.$base_url = `<?= base_url() ?>`
</script>

<!-- REQUIRED SCRIPTS -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-3.6.0.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/adminlte.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/dataTables.bootstrap4.min.js"></script>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/jszip.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/pdfmake.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/angular.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/angular-sanitize.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/myplugin.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>plugins/sweetalert2/sweetalert2.all.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>plugins/chart.js/Chart.bundle.min.js"></script>

<!-- MODULE SCRIPTS -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/root.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/supplier.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/customer.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/po.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/povsproforma.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/proformavspi.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/proformavscrf.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/sop.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/itemcodes.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/supplierledger.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/iadreport.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/users.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/povsproformahistory.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/proformavscrfhistory.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/proformavspihistory.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/sophistory.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/vendorsdeal.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/deduction.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/charges.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/vat.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/testing.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/deductionreport.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/uploadedtransaction.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/varianceledger.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/adjustment.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/chart.js"></script>
<!-- <script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/poaging.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/controllers/vis.js"></script> -->
<!-- MODULE SCRIPTS-->

<!-- BOOTBOX-->
<script type="text/javascript" src="<?php echo base_url() ?>plugins/bootbox/bootbox.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>plugins/bootbox/bootbox.locales.min.js"></script>

<script>
    $(window).on('load', function () {
        $('#loading').hide();
    })
</script>

</body>

</html>