<!DOCTYPE html>
<html ng-app="app">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CWO |
        <?php echo $title; ?>
    </title>
    <link rel="icon" type="image/gif" href="<?php echo base_url(); ?>assets/img/CWO-LOGO-2.png">
    <!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/bootstrap.min.css"> -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>plugins/font-awesome-pro-5/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/jquery-ui.theme.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/adminlte.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/dataTables.bootstrap4.min.css"
        type="text/css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/buttons.dataTables.min.css"
        type="text/css">
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
                    <img src="<?php echo base_url(); ?>assets/img/CWOBG.png" alt="CWO Monitoring System"
                        class="brand-image">
                </a>

                <button class="navbar-toggler order-1" type="button" data-toggle="collapse"
                    data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse order-3" id="navbarCollapse">
                    <!-- Left navbar links -->
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="<?php echo base_url(); ?>home" class="nav-link"><i class="fas fa-home"></i>
                                Home</a>
                        </li>
                        <!-- MASTER FILE TAB -->

                        <?php if ($this->session->userdata('userType') == 'Admin' || $this->session->userdata('userType') == 'Buyer-Purchaser' || $this->session->userdata('userType') == 'Pricing' || $this->session->userdata('userType') == 'GGM Encoder' || $this->session->userdata('userType') == 'SOP'): ?>
                            <li class="nav-item dropdown">
                                <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false" class="nav-link dropdown-toggle">
                                    <i class="fas fa-file nav-icon"></i> Master Files
                                </a>
                                <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">

                                    <?php if ($this->session->userdata('userType') == 'Admin' || $this->session->userdata('userType') == 'Buyer-Purchaser' || $this->session->userdata('userType') == 'GGM Encoder'): ?>
                                        <li><a href="<?php echo base_url(); ?>masterfiles/ItemCodes" class="dropdown-item"><i
                                                    class="fas fa-box"></i> Item Code Mapping</a></li>
                                    <?php endif; ?>

                                    <?php if ($this->session->userdata('userType') == 'Admin' || $this->session->userdata('userType') == 'Pricing'): ?>
                                        <li><a href="<?php echo base_url(); ?>masterfiles/VendorsDeals" class="dropdown-item"><i
                                                    class="fas fa-percent"></i> Vendors Deal Setup</a></li>
                                    <?php endif; ?>
                                    <?php if ($this->session->userdata('userType') == 'Admin' || $this->session->userdata('userType') == 'SOP'): ?>
                                        <li><a href="<?php echo base_url(); ?>masterfiles/Deductions" class="dropdown-item"><i
                                                    class="fab fa-less"></i> Deduction Setup</a></li>
                                    <?php endif; ?>
                                    <?php if ($this->session->userdata('userType') == 'Admin' || $this->session->userdata('userType') == 'SOP'): ?>
                                        <li><a href="<?php echo base_url(); ?>masterfiles/Charges" class="dropdown-item"><i
                                                    class="fas fa-cart-plus"></i>
                                                Charges Setup</a></li>
                                    <?php endif; ?>
                                    <?php if ($this->session->userdata('userType') == 'Admin'): ?>
                                        <li><a href="<?php echo base_url(); ?>masterfiles/Suppliers" class="dropdown-item"><i
                                                    class="fas fa-people-carry"></i> Supplier Setup</a></li>
                                        <li><a href="<?php echo base_url(); ?>masterfiles/Customers" class="dropdown-item"><i
                                                    class="fas fa-location-arrow"></i> Location Setup</a></li>
                                        <li><a href="<?php echo base_url(); ?>masterfiles/VAT" class="dropdown-item"><i
                                                    class="fas fa-percent"></i> VAT Setup</a></li>
                                        <li><a href="<?php echo base_url(); ?>masterfiles/Users" class="dropdown-item"><i
                                                    class="fas fa-users"></i> Users Setup</a></li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>


                        <!-- TRANSACTION TAB -->
                        <?php if ($this->session->userdata('userType') == 'Admin' || $this->session->userdata('userType') == 'Accounting' || $this->session->userdata('userType') == 'Manager' || $this->session->userdata('userType') == 'Supervisor' || $this->session->userdata('userType') == 'Section Head' || $this->session->userdata('userType') == 'Buyer-Purchaser' || $this->session->userdata('userType') == 'SOP' || $this->session->userdata('userType') == 'Pricing' || $this->session->userdata('userType') == 'PI' || $this->session->userdata('userType') == 'Receiving Clerk' || $this->session->userdata('userType') == 'SOPAccttg' || $this->session->userdata('userType') == 'IAD'): ?>
                            <li class="nav-item dropdown">
                                <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false" class="nav-link dropdown-toggle">
                                    <i class="fas fa-exchange-alt nav-icon"></i> Transactions
                                </a>
                                <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                                    <?php if ($this->session->userdata('userType') == 'Buyer-Purchaser' || $this->session->userdata('userType') == 'Pricing' || $this->session->userdata('userType') == 'Accounting' || $this->session->userdata('userType') == 'Admin' || $this->session->userdata('userType') == 'Manager'): ?>
                                        <li><a href="<?php echo base_url(); ?>transactions/POReports" class="dropdown-item"><i
                                                    class="fas fa-file-alt"></i> Purchase Order Uploading</a></li>
                                    <?php endif; ?>
                                    <?php if ($this->session->userdata('userType') == 'Buyer-Purchaser' || $this->session->userdata('userType') == 'PI' || $this->session->userdata('userType') == 'Pricing' || $this->session->userdata('userType') == 'SOPAccttg' || $this->session->userdata('userType') == 'Accounting' || $this->session->userdata('userType') == 'Receiving Clerk' || $this->session->userdata('userType') == 'Manager' || $this->session->userdata('userType') == 'Admin'): ?>
                                        <li><a href="<?php echo base_url(); ?>transactions/POVersusProforma"
                                                class="dropdown-item"><i class="fas fa-file-alt"></i> PO VS Proforma (per
                                                Item)</a></li>
                                    <?php endif; ?>
                                    <?php if ($this->session->userdata('userType') != 'Buyer-Purchaser' && $this->session->userdata('userType') != 'Receiving Clerk' && $this->session->userdata('userType') != 'Pricing' && $this->session->userdata('userType') != 'PI'): ?>
                                        <li><a href="<?php echo base_url(); ?>transactions/SOP" class="dropdown-item"><i
                                                    class="fas fa-file-alt"></i> Summary of Payments (SOP)</a></li>
                                    <?php endif; ?>
                                    <?php if ($this->session->userdata('userType') != 'Buyer-Purchaser' && $this->session->userdata('userType') != 'Receiving Clerk' && $this->session->userdata('userType') != 'Pricing' && $this->session->userdata('userType') != 'SOP'): ?>
                                        <?php if ($this->session->userdata('userType') != 'PI'): ?>
                                            <li><a href="<?php echo base_url(); ?>transactions/ProformaVersusCRF"
                                                    class="dropdown-item"><i class="fas fa-file-alt"></i> Proforma (per Item) VS
                                                    CRF</a></li>
                                        <?php endif; ?>
                                        <li><a href="<?php echo base_url(); ?>transactions/ProformaVersusPI"
                                                class="dropdown-item"><i class="fas fa-file-alt"></i> Proforma (per Item) VS PI
                                                (per Item)</a></li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <!-- REPORTS TAB -->
                        <li class="nav-item dropdown">
                            <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false" class="nav-link dropdown-toggle">
                                <i class="fas fa-print nav-icon"></i> Reports
                            </a>
                            <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                                <!-- <li><a href="<?php echo base_url(); ?>reports/SupplierLedger" class="dropdown-item"><i
                                            class="fas fa-file-alt"></i> Supplier Ledger</a></li> -->
                                <li><a href="<?php echo base_url(); ?>reports/VarianceLedger" class="dropdown-item"><i
                                            class="fas fa-file-alt"></i> Variance Ledger</a></li>
                                <!-- <li><a href="<?php echo base_url(); ?>reports/IADReport" class="dropdown-item"><i
                                            class="fas fa-file-alt"></i> IAD Report</a></li> -->
                                <li><a href="<?php echo base_url(); ?>reports/POvProformaHistory"
                                        class="dropdown-item"><i class="fas fa-file-alt"></i> PO vs Proforma History</a>
                                </li>
                                <li><a href="<?php echo base_url(); ?>reports/ProformavCrfHistory"
                                        class="dropdown-item"><i class="fas fa-file-alt"></i> Proforma VS CRF
                                        History</a></li>
                                <li><a href="<?php echo base_url(); ?>reports/ProformavPiHistory"
                                        class="dropdown-item"><i class="fas fa-file-alt"></i> Proforma vs PI History</a>
                                </li>
                                <li><a href="<?php echo base_url(); ?>reports/SOPHistory" class="dropdown-item"><i
                                            class="fas fa-file-alt"></i> SOP History</a></li>
                                <li><a href="<?php echo base_url(); ?>reports/DeductionReport" class="dropdown-item"><i
                                            class="fas fa-file-alt"></i> Deduction Report</a></li>
                                <li><a href="<?php echo base_url(); ?>reports/PoAging" class="dropdown-item"><i
                                            class="fas fa-file-alt"></i> PO Aging Report</a></li>
                            </ul>
                        </li>

                        <?php if ($this->session->userdata('userType') == 'Admin' || $this->session->userdata('userType') == 'Accounting'): ?>
                            <!-- UTILITY TAB -->
                            <li class="nav-item dropdown">
                                <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false" class="nav-link dropdown-toggle">
                                    <i class="fas fa-tools"></i> Utility
                                </a>
                                <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                                    <?php if ($this->session->userdata('userType') == 'Admin'): ?>
                                        <li><a href="<?php echo base_url(); ?>utility/UploadedTransactionDocuments"
                                                class="dropdown-item"><i class="fas fa-file-alt"></i> Retrieve Uploaded
                                                Documents</a></li>
                                        <li class="dropdown-submenu dropdown-hover">
                                            <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false"
                                                class="dropdown-item dropdown-toggle"><i class="fas fa-file-alt"></i> SOP</a>
                                            <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
                                                <li><a href="<?php echo base_url(); ?>utility/ChangeSOPStatus"
                                                        class="dropdown-item"><i class="fas fa-edit"></i> Change Status</a></li>
                                            </ul>
                                        </li>
                                    <?php endif; ?>
                                    <li><a href="<?php echo base_url(); ?>utility/Adjustment" class="dropdown-item"><i
                                                class="fas fa-file-alt"></i> Adjustment</a></li>

                                </ul>
                            </li>
                            <!-- UTILITY TAB -->
                            <!-- Testing Area -->
                            <li class="nav-item">
                                <a href="<?php echo base_url(); ?>testing" class="nav-link"><i class="fas fa-tools"></i>
                                    Testing Area</a>
                            </li>
                            <!-- Testing Area -->
                        <?php endif; ?>

                        <!-- ABOUT TAB -->
                        <li class="nav-item">
                            <a href="<?php echo base_url(); ?>about" class="nav-link"><i class="fas fa-info-circle"></i>
                                About</a>
                        </li>

                        <!-- CONTACT US TAB -->
                        <li class="nav-item">
                            <a href="<?php echo base_url(); ?>contactus" class="nav-link"><i class="fa fa-phone"></i>
                                Contact Us</a>
                        </li>

                        <!-- USERS GUIDE -->
                        <li class="nav-item">
                            <?php if ($this->session->userdata('userType') == 'Accounting'): ?>
                                <a href="<?php echo base_url(); ?>files/UsersGuide/ACCOUNTING.pdf" class="nav-link"
                                    target="_blank"><i class="fas fa-file-pdf"></i>
                                    <u>User's Guide</u></a>
                            <?php endif; ?>

                            <?php if ($this->session->userdata('userType') == 'Buyer-Purchaser'): ?>
                                <a href="<?php echo base_url(); ?>files/UsersGuide/BUYER-PURCHASER.pdf" class="nav-link"
                                    target="_blank"><i class="fas fa-file-pdf"></i>
                                    <u>User's Guide</u></a>
                            <?php endif; ?>

                            <?php if ($this->session->userdata('userType') == 'IAD'): ?>
                                <a href="<?php echo base_url(); ?>files/UsersGuide/IAD.pdf" class="nav-link"
                                    target="_blank"><i class="fas fa-file-pdf"></i>
                                    <u>User's Guide</u></a>
                            <?php endif; ?>

                            <?php if ($this->session->userdata('userType') == 'PI'): ?>
                                <a href="<?php echo base_url(); ?>files/UsersGuide/PI.pdf" class="nav-link"
                                    target="_blank"><i class="fas fa-file-pdf"></i>
                                    <u>User's Guide</u></a>
                            <?php endif; ?>

                            <?php if ($this->session->userdata('userType') == 'Pricing'): ?>
                                <a href="<?php echo base_url(); ?>files/UsersGuide/PRICING.pdf" class="nav-link"
                                    target="_blank"><i class="fas fa-file-pdf"></i>
                                    <u>User's Guide</u></a>
                            <?php endif; ?>

                            <?php if ($this->session->userdata('userType') == 'SOP'): ?>
                                <a href="<?php echo base_url(); ?>files/UsersGuide/SOP.pdf" class="nav-link"
                                    target="_blank"><i class="fas fa-file-pdf"></i>
                                    <u>User's Guide</u></a>
                            <?php endif; ?>

                            <?php if ($this->session->userdata('userType') == 'SOPAccttg'): ?>
                                <a href="<?php echo base_url(); ?>files/UsersGuide/SOP-ACCOUNTING.pdf" class="nav-link"
                                    target="_blank"><i class="fas fa-file-pdf"></i>
                                    <u>User's Guide</u></a>
                            <?php endif; ?>
                        </li>

                        <!-- USERS GUIDE FOR ADMIN-->
                        <?php if ($this->session->userdata('userType') == 'Admin'): ?>
                            <li class="nav-item">
                                <a href="#" class="nav-link" data-toggle="modal" data-target="#pdfModal"
                                    onclick="loadPdf('<?php echo base_url(); ?>files/UsersGuide/CWO.pdf')">
                                    <i class="fas fa-file-pdf"></i>
                                    <u>User's Guide</u>
                                </a>
                            </li>
                        <?php endif; ?>

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
                        <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false" class="nav-link dropdown-toggle"><i class="fas fa-user-clock"></i>
                            <?php echo $this->session->userdata('username'); ?>
                        </a>
                        <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
                            <li><a href="#" class="dropdown-item" data-toggle="modal" data-target="#changepass">Change
                                    Password </a></li>
                            <li><a href="#" class="dropdown-item" data-toggle="modal"
                                    data-target="#changeusername">Change Username </a>
                            </li>
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


        <div class="modal fade" id="changepass" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true" ng-controller="users-controller">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content rounded-0">
                    <div class="modal-header bg-dark rounded-0">
                        <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-key"></i> Change Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="" name="changepassform" ng-submit="changepass($event)">
                            <div class="col-md-12">
                                <div class="input-group mb-3">
                                    <label for="oldpass" class="col-sm-3 col-form-label">Old Password</label>
                                    <input type="password" class="form-control" id="oldpass" name="oldpass"
                                        ng-model="oldpass" ng-keyup="getoldpass()" ng-required="true">
                                </div>
                                <div class="input-group mb-3">
                                    <label for="password" class="col-sm-3 col-form-label">New Password</label>
                                    <input type="password" class="form-control password" name="password"
                                        ng-model="newpass" ng-disabled="!retrieveoldpass" ng-keyup="checkClass()"
                                        maxlength="16" ng-required="true">
                                    <div class="input-group-append" style="cursor: pointer;">
                                        <div class="input-group-text toggle-pass">
                                            <span class="far fa-eye-slash"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group mb-3">
                                    <label for="confirmpass" class="col-sm-3 col-form-label">Confirm New
                                        Password</label>
                                    <input type="password" class="form-control" name="confirmpass"
                                        ng-model="confirmpass" password-confirm match-target="newpass"
                                        ng-disabled="!newpass || patternOk !=5" ng-required="true"
                                        aria-describedby="passwordHelpBlock">
                                    <small id="passwordHelpBlock"
                                        ng-show="changepassform.confirmpass.$error.match && newpass && confirmpass && patternOk ==5"
                                        class="form-text error-display">Your password must be 8-16 characters long,
                                        contains atleast 1 upper and lower case letters, and must contain atleast 1
                                        number and special character.</small>
                                </div>
                            </div>
                            <div class="col-md-12" id="passwordclass" ng-show="!confirmpass">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small id="Length" class="form-text pattern-error">Must be 8-16 characters
                                            long.</small>
                                        <small id="UpperCase" class="form-text pattern-error">Must have atleast 1 upper
                                            case character.</small>
                                        <small id="LowerCase" class="form-text pattern-error">Must have atleast 1 lower
                                            case character.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <small id="Numbers" class="form-text pattern-error">Must have atleast 1 numeric
                                            character.</small>
                                        <small id="Symbols" class="form-text pattern-error">Must have atleast 1 special
                                            character.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary btn-flat"
                                    ng-disabled="changepassform.$invalid">Submit</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="changeusername" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true" ng-controller="users-controller">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content rounded-0">
                    <div class="modal-header bg-dark rounded-0">
                        <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-user-circle"></i> Change
                            Username</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" ng-init="getOldUsername()">
                        <form action="" name="changeuserForm" ng-submit="changeusername($event)">
                            <div class="col-md-12">
                                <div class="input-group mb-3">
                                    <label for="olduser" class="col-sm-3 col-form-label">Old Username</label>
                                    <input type="text" class="form-control" id="olduser" name="olduser"
                                        ng-model="olduser" ng-readonly="true">
                                </div>
                                <div class="input-group mb-3">
                                    <label for="newuser" class="col-sm-3 col-form-label">New Username</label>
                                    <input type="text" class="form-control" name="newuser" ng-model="newuser"
                                        maxlength="20" ng-required="true">
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary btn-flat"
                                    ng-disabled="changeuserForm.$invalid">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<!-- Users Guide Modal -->
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-dark rounded-0">
                <h5 class="modal-title" id="pdfModalLabel"><i class="fas fa-users-cog mr-1"></i>User's
                    Guide
                </h5>
                <div class="form-inline">
                    <button type="button" class="btn btn-secondary" onclick="toggleFullScreen()"
                        title="Expand/Unexpand"><i class="fas fa-expand"></i></button>
                </div>
            </div>

            <div class="modal-body">
                <!-- The iframe to embed the PDF file -->
                <iframe id="pdfIframe" width="100%" height="600" frameborder="0"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"
                    onclick="removeFullScreen()">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Users Guide Modal -->

<script>
    function loadPdf(pdfUrl) {
        var pdfIframe = document.getElementById('pdfIframe');
        pdfIframe.src = pdfUrl;
    }

    function toggleFullScreen() {
        var modal = $('#pdfModal');
        var isFullScreen = modal.hasClass('modal-fullscreen');

        modal.toggleClass('modal-fullscreen', !isFullScreen);

        if (!isFullScreen) {
            console.log('Modal is now full-screen');
        } else {
            console.log('Exited full-screen mode');
        }
    }

    function removeFullScreen() {
        var modal = $('#pdfModal');
        modal.removeClass('modal-fullscreen');
    }

</script>