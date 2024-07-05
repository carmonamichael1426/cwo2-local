<!DOCTYPE html>
<html ng-app="app">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CWO | <?php echo $title; ?></title>
    <link rel="icon" type="image/gif" href="<?php echo base_url(); ?>assets/img/CWO-LOGO-2.png">
    <!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css"> -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery-ui.theme.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/adminlte.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/style.css">
    <!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/dataTables.bulma.min.css" type="text/css"> -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/dataTables.bootstrap4.min.css" type="text/css">
    <!-- dataTables.bootstrap.css -->
    <!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery.dataTables.min.css" type="text/css"> -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/buttons.dataTables.min.css" type="text/css">
    <!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css"> -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>plugins/sweetalert2/sweetalert2.css">
    <style>
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
</head>

<body class="hold-transition layout-top-nav">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand-md navbar-dark navbar-light">
            <div class="container-fluid">
                <a href="#" class="navbar-brand">
                    <img src="<?php echo base_url(); ?>assets/img/CWOBG.png" alt="CWO Monitoring System" class="brand-image">
                </a>

                <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                    <!-- Left navbar links -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="<?php echo base_url(); ?>home" class="nav-link"><i class="fas fa-home"></i> Home</a>
                        </li>
                        <!-- MASTER FILE TAB -->

                        <?php if ($this->session->userdata('userType') == 'Admin' || $this->session->userdata('userType') == 'Buyer-Purchaser' || $this->session->userdata('userType') == 'Pricing' || $this->session->userdata('userType') == 'SOP') : ?>
                            <li class="nav-item dropdown">
                                <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">
                                    <i class="fas fa-file nav-icon"></i> Master Files
                                </a>
                                <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">

                                    <?php if ($this->session->userdata('userType') == 'Admin' || $this->session->userdata('userType') == 'Buyer-Purchaser' || $this->session->userdata('userType') == 'GGM Encoder') : ?>
                                        <li><a href="<?php echo base_url(); ?>masterfiles/ItemCodes" class="dropdown-item"><i class="fas fa-box"></i> Item Code Mapping</a></li>
                                    <?php endif; ?>

                                    <?php if ($this->session->userdata('userType') == 'Admin' || $this->session->userdata('userType') == 'Pricing') : ?>
                                        <li><a href="<?php echo base_url(); ?>masterfiles/VendorsDeals" class="dropdown-item"><i class="fas fa-percent"></i> Vendors Deal Setup</a></li>
                                    <?php endif; ?>
                                    <?php if ($this->session->userdata('userType') == 'Admin' || $this->session->userdata('userType') == 'SOP') : ?>
                                        <li><a href="<?php echo base_url(); ?>masterfiles/Deductions" class="dropdown-item"><i class="fab fa-less"></i> Deduction Setup</a></li>
                                    <?php endif; ?>
                                    <?php if ($this->session->userdata('userType') == 'Admin') : ?>
                                        <li><a href="<?php echo base_url(); ?>masterfiles/Suppliers" class="dropdown-item"><i class="fas fa-people-carry"></i> Supplier Setup</a></li>
                                        <li><a href="<?php echo base_url(); ?>masterfiles/Customers" class="dropdown-item"><i class="fas fa-location-arrow"></i> Location Setup</a></li>                                        
                                        <li><a href="<?php echo base_url(); ?>masterfiles/VAT" class="dropdown-item"><i class="fas fa-percent"></i> VAT Setup</a></li>
                                        <li><a href="<?php echo base_url(); ?>masterfiles/Users" class="dropdown-item"><i class="fas fa-users"></i> Users Setup</a></li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>


                        <!-- TRANSACTION TAB -->
                        <?php if ($this->session->userdata('userType') == 'Admin' || $this->session->userdata('userType') == 'Accounting' || $this->session->userdata('userType') == 'Manager' || $this->session->userdata('userType') == 'Supervisor' || $this->session->userdata('userType') == 'Section Head' || $this->session->userdata('userType') == 'Buyer-Purchaser'  || $this->session->userdata('userType') == 'SOP' || $this->session->userdata('userType') == 'Pricing' || $this->session->userdata('userType') == 'PI' || $this->session->userdata('userType') == 'IAD') : ?>
                            <li class="nav-item dropdown">
                                <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">
                                    <i class="fas fa-exchange-alt nav-icon"></i> Transactions
                                </a>
                                <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                                    <?php if ($this->session->userdata('userType') == 'Buyer-Purchaser' || $this->session->userdata('userType') == 'Pricing' || $this->session->userdata('userType') == 'Accounting' || $this->session->userdata('userType') == 'Admin') : ?>
                                        <li><a href="<?php echo base_url(); ?>transactions/POReports" class="dropdown-item"><i class="fas fa-file-alt"></i> Purchase Order Uploading</a></li>
                                    <?php endif; ?>
                                    <?php if ($this->session->userdata('userType') == 'Buyer-Purchaser' || $this->session->userdata('userType') == 'Pricing'  || $this->session->userdata('userType') == 'Accounting'  || $this->session->userdata('userType') == 'Admin') : ?>
                                        <li><a href="<?php echo base_url(); ?>transactions/POVersusProforma" class="dropdown-item"><i class="fas fa-file-alt"></i> PO VS Proforma (per Item)</a></li>
                                    <?php endif; ?>
                                    <?php if ($this->session->userdata('userType') != 'Buyer-Purchaser' && $this->session->userdata('userType') != 'Pricing' && $this->session->userdata('userType') != 'PI') : ?>
                                        <li><a href="<?php echo base_url(); ?>transactions/SOP" class="dropdown-item"><i class="fas fa-file-alt"></i> Summary of Payments (SOP)</a></li>
                                    <?php endif; ?>
                                    <?php if ($this->session->userdata('userType') != 'Buyer-Purchaser' && $this->session->userdata('userType') != 'Pricing' && $this->session->userdata('userType') != 'SOP') : ?>
                                        <li><a href="<?php echo base_url(); ?>transactions/ProformaVersusCRF" class="dropdown-item"><i class="fas fa-file-alt"></i> Proforma (per Item) VS CRF</a></li>
                                        <li><a href="<?php echo base_url(); ?>transactions/ProformaVersusPI" class="dropdown-item"><i class="fas fa-file-alt"></i> Proforma (per Item) VS PI (per Item)</a></li>

                                        <!-- <li><a href="<?php echo base_url(); ?>transactions/CWOSlip" class="dropdown-item"><i class="fas fa-file-alt"></i> CWO Slip</a></li> -->
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <!-- REPORTS TAB -->
                        <li class="nav-item dropdown">
                            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">
                                <i class="fas fa-print nav-icon"></i> Reports
                            </a>
                            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                                <li><a href="<?php echo base_url(); ?>reports/SupplierLedger" class="dropdown-item"><i class="fas fa-file-alt"></i> Supplier Ledger</a></li>
                                <li><a href="<?php echo base_url(); ?>reports/VarianceLedger" class="dropdown-item"><i class="fas fa-file-alt"></i> Variance Ledger</a></li>
                                <li><a href="<?php echo base_url(); ?>reports/IADReport" class="dropdown-item"><i class="fas fa-file-alt"></i> IAD Report</a></li>
                                <li><a href="<?php echo base_url(); ?>reports/POvProformaHistory" class="dropdown-item"><i class="fas fa-file-alt"></i> PO vs Proforma History</a></li>
                                <li><a href="<?php echo base_url(); ?>reports/ProformavCrfHistory" class="dropdown-item"><i class="fas fa-file-alt"></i> Proforma VS CRF History</a></li>
                                <li><a href="<?php echo base_url(); ?>reports/ProformavPiHistory" class="dropdown-item"><i class="fas fa-file-alt"></i> Proforma vs PI History</a></li>
                                <li><a href="<?php echo base_url(); ?>reports/SOPHistory" class="dropdown-item"><i class="fas fa-file-alt"></i> SOP History</a></li>
                                <li><a href="<?php echo base_url(); ?>reports/DeductionReport" class="dropdown-item"><i class="fas fa-file-alt"></i> Deduction Report</a></li>
                            </ul>
                        </li>

                        <?php if ($this->session->userdata('userType') == 'Admin') : ?>
                            <li class="nav-item dropdown">
                                <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">
                                    <i class="fas fa-tools"></i> Utility
                                </a>
                                <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                                    <li><a href="<?php echo base_url(); ?>utility/UploadedTransactionDocuments" class="dropdown-item"><i class="fas fa-file-alt"></i> Retrieve Uploaded Documents</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo base_url(); ?>testing" class="nav-link"><i class="fas fa-tools"></i> Testing Area</a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item">
                            <a href="<?php echo base_url(); ?>about" class="nav-link"><i class="fas fa-info-circle"></i> About</a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo base_url(); ?>contactus" class="nav-link"><i class="fa fa-phone"></i> Contact Us</a>
                        </li>
                    </ul>
                </div>

                <!-- Right navbar links -->
                <ul class="order-1 order-md-3 navbar-nav navbar-no-expand ml-auto">
                    <li class="nav-item">
                        <a class="nav-link disabled">
                            <div id="MyClockDisplay" class="clock" onload="showTime()"></div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled">
                            <i class="fas fa-ellipsis-v"></i>
                        </a>
                    </li>
                    <!-- <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                            <i class="far fa-bell"></i>
                            <span class="badge badge-warning navbar-badge">1</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0px;">
                            <span class="dropdown-item dropdown-header">1 Notifications</span>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item">
                                <i class="fas fa-hourglass-half mr-2"></i>Pending Matches
                            </a>
                        </div>
                    </li> -->
                    <li class="nav-item dropdown">
                        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle"><i class="fas fa-user-clock"></i> <?php echo $this->session->userdata('username'); ?></a>
                        <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                            <!-- <li><a href="#" class="dropdown-item">Account Settings </a></li> -->
                            <li><a href="<?php echo base_url(); ?>logout" class="dropdown-item">Log out</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <!-- /.navbar -->

         <!-- LOADING MODAL -->
        <div class="modal_loading" id="loading">
            <img id="loading-image" src="<?php echo base_url(); ?>assets/img/download.gif" alt="Loading..." />
        </div>

       
       