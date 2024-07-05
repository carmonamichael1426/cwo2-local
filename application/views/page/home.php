<!-- Content Wrapper. Contains page content -->

<style>
    .alert-info {
        background-color: #d2eef7;
        border-color: #b8daff;
        border-left-color: #148ea1;
    }

    .alert-info h5,
    p {
        color: #31708f;
    }

    .alert-danger {
        background-color: #f2dede;
        border-color: #ebccd1;
        border-left-color: #d32535;
    }

    .alert-danger h5 {
        color: #a94442;
    }
</style>

<div class="content-wrapper body-bg">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-outline card-style-1">
                        <div class="card-header bg-dark rounded-0">
                            <div class="content-header" style="padding: 0px">
                                <div class="panel panel-default">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="panel-body"><i class="fas fa-home"></i> <strong>HOME |
                                                    CWO</strong></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="panel-body float-right">
                                                <?php echo date('F d, Y'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div>
                                <h4>WELCOME TO CASH WITH ORDER MONITORING SYSTEM</h4>
                            </div>
                            <hr>
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card rounded-0">
                                            <div class="card-body">

                                                <div class="row">
                                                    <div class="col-md-12" ng-controller="chart-controller">
                                                        <div class="row">
                                                            <div class="col-md-3">
                                                                <div class="card card-outline bg-light">
                                                                    <div class="card-header border-bottom-0">
                                                                        <p>PRO-FORMA SALES INVOICE <span
                                                                                class="font-italic">(Pricing)</span></p>
                                                                    </div>
                                                                    <div class="card-body pt-0">
                                                                        <div>
                                                                            <p align="center">
                                                                                <canvas id="profcanvas" height="200"
                                                                                    aria-label="Pro-forma Sales Invoice Chart"
                                                                                    role="img"></canvas>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-footer">
                                                                        <div class="text-center text-muted"
                                                                            ng-show="proftotal > 0" ng-cloak>
                                                                            TOTAL NO. OF RECORDS : {{proftotal}}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="card card-outline bg-light">
                                                                    <div class="card-header border-bottom-0">
                                                                        <p>SUMMARY OF PAYMENTS <span
                                                                                class="font-italic">(IAD)</span></p>
                                                                    </div>
                                                                    <div class="card-body pt-0">
                                                                        <div>
                                                                            <p align="center">
                                                                                <canvas id="sopcanvas" height="200"
                                                                                    aria-label="SOP Chart"
                                                                                    role="img"></canvas>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-footer">
                                                                        <div class="text-center text-muted"
                                                                            ng-show="soptotal > 0" ng-cloak>
                                                                            TOTAL NO. OF RECORDS : {{soptotal}}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="card card-outline bg-light">
                                                                    <div class="card-header border-bottom-0">
                                                                        <p>CHECK REQUEST FORM / CHECK VOUCHER <span
                                                                                class="font-italic">(Accounting)</span>
                                                                        </p>
                                                                    </div>
                                                                    <div class="card-body pt-0">
                                                                        <div class="col-md-12">
                                                                            <p align="center">
                                                                                <canvas id="crfcanvas" height="200"
                                                                                    aria-label="CRF Chart"
                                                                                    role="img"></canvas>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-footer">
                                                                        <div class="text-center text-muted"
                                                                            ng-show="crftotal > 0" ng-cloak>
                                                                            TOTAL NO. OF RECORDS : {{crftotal}}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="card card-outline bg-light">
                                                                    <div class="card-header border-bottom-0">
                                                                        <p>PURCHASE INVOICE <span
                                                                                class="font-italic">(Accounting)</span>
                                                                        </p>
                                                                    </div>
                                                                    <div class="card-body pt-0">
                                                                        <div class="col-md-12">
                                                                            <p align="center">
                                                                                <canvas id="picanvas" height="200"
                                                                                    aria-label="Purchase Invoice Chart"
                                                                                    role="img"></canvas>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-footer">
                                                                        <div class="text-center text-muted"
                                                                            ng-show="pitotal > 0" ng-cloak>
                                                                            TOTAL NO. OF RECORDS : {{pitotal}}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="alert alert-danger alert-dismissible">
                                                            <button type="button" class="close" data-dismiss="alert"
                                                                aria-hidden="true">&times;</button>
                                                            <h5><i class="fas fa-exclamation-circle"></i> TOR
                                                                REQUISITION</h5>
                                                            <p class="text-justify">When requesting for untagging PRICE
                                                                CHECKED status, please refrain from using the phrase
                                                                "UNTAG THE PRICE" because it is incorrect and we will
                                                                not execute the request for this reason. Please use
                                                                "UNTAG PRICE CHECKED STATUS" instead. Please make your
                                                                requests as specific as possible.<br>Thank you.</p>

                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="alert alert-info alert-dismissible">
                                                            <button type="button" class="close" data-dismiss="alert"
                                                                aria-hidden="true">&times;</button>
                                                            <h5><i class="icon fas fa-info"></i>INFORMATION</h5>
                                                            <p class="text-justify"><a
                                                                    href="<?php echo base_url(); ?>masterfiles/pricelist"
                                                                    style="color:blue">List</a> of suppliers implemented
                                                                by batch with their corresponding TYPE OF PRO-FORMA
                                                                ENTRY.</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!--<div class="row">
                                                    <div class="col-md-6">
                                                        <div class="alert alert-danger alert-dismissible">
                                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                            <h5><i class="fas fa-exclamation-circle"></i> ANNOUNCEMENT</h5>
                                                            <p class="text-justify">Please be advised that starting May 19, 2023, 7th batch of suppliers will be implemented. <br>All transactions must be uploaded/created in the CWO system, beginning with PO and continuing with subsequent transactions e.g., PSI, SOP, CRF and PI.<br>Thank you.</p>
                                                            <p> 1.   AEO INTERNATIONAL FOOD CORPORATION <br>
                                                                2.   CAMILUZ ENTERPRISES INC <br>
                                                                3.   CM AND SONS FOOD PRODUCTS <br>
                                                                4.   ENERLIFE PHILIPPINES INC <br>
                                                                5.   MANXING ENTERPRISES CORP. <br>
                                                                6.   ODELON A. MIRANDA <br>
                                                                7.   PACIFIC SYNERGY FOOD & BEVERAGE CORP. <br>
                                                                8.   PURE SNACK FOOD HOUSE CORP <br>
                                                                9.   SERMASISON CORPORATION <br>
                                                                10.  SUNPRIDE FOODS INC
                                                                </p>
                                                        </div>
                                                    </div>                                                 
                                                </div>  -->

                                                <!-- <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card card-danger collapsed-card">
                                                            <div class="card-header">
                                                                <h3 class="card-title">ANNOUNCEMENT</h3>
                                                                <div class="card-tools">
                                                                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="callout callout-info col-md-12">
                                                                    <p class="text-justify"><a href="<?php echo base_url(); ?>masterfiles/pricelist">List</a> of suppliers implemented by batch.</p>
                                                                </div>
                                                                <div class="callout callout-danger col-md-12">
                                                                    <p class="text-justify">Starting April 13, 2023 your password will be reset to default as we change the system's password algorithm. Your username will remain and your password will be Cwo_2021. You may change the default password when you are logged in. <br>Thank you.</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col-md-6 -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->


</div>
<!-- /.content-wrapper -->