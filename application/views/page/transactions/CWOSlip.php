<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="cwoslip-controller">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-style-1">
                        <div class="card-header bg-dark rounded-0">
                            <div class="content-header" style="padding: 0px">
                                <div class="panel panel-default">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="panel-body"><i class="fas fa-receipt"></i> <strong>CASH WITH ORDER SLIP</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row form-group">
                                            <label for="cwoNo" class="col-lg-4 col-form-label text-right">CWO No.: </label>
                                            <div class="col-lg-8">
                                                <input type="text" class="form-control rounded-0" ng-model="cwoNo" name="cwoNo" required autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="row form-group" ng-init="getSuppliers()">
                                            <label for="supplierName" class="col-lg-4 col-form-label text-right">Supplier Name: </label>
                                            <div class="col-lg-8">
                                                <select class="form-control rounded-0" ng-model="supplierName" name="supplierName" required ng-change="getPurchaseOrder()">
                                                    <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                                    <option ng-repeat="s in suppliers" value="{{s.supplier_id}}">{{s.supplier_name}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row form-group">
                                            <label for="slipDate" class="col-lg-4 col-form-label text-right">Date: </label>
                                            <div class="col-lg-8">
                                                <input type="date" class="form-control rounded-0" ng-model="slipDate" name="slipDate" required>
                                            </div>
                                        </div>

                                        <div class="row form-group" ng-init="getCustomers()">
                                            <label for="locationName" class="col-lg-4 col-form-label text-right">Requesting Dept: </label>
                                            <div class="col-lg-8">
                                                <select class="form-control rounded-0" ng-model="locationName" name="locationName" required ng-change="getPurchaseOrder()">
                                                    <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                                    <option ng-repeat="c in customers" value="{{c.customer_code}}">{{c.customer_name}}</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="row form-group">
                                            <label for="poNumber" class="col-lg-4 col-form-label text-right">P.O Number</label>
                                            <div class="col-lg-8">
                                                <select class="form-control rounded-0" ng-model="poNumber" name="poNumber" required multiple ng-change="getInvoices()">
                                                    <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                                    <option ng-repeat="p in podata" value="{{p.po_header_id}}">{{p.po_no}}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row form-group">
                                            <label for="invoiceNumber" class="col-lg-4 col-form-label text-right">Invoice Number: </label>
                                            <div class="col-lg-8">
                                                <select class="form-control rounded-0" ng-model="invoiceNumber" name="invoiceNumber" required multiple>
                                                    <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                                    <option></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="row form-group" ng-init="getCustomers()">
                                            <label for="locationName" class="col-lg-4 col-form-label text-right">Discount Type: </label>
                                            <div class="col-lg-8">
                                                <select class="form-control rounded-0" name="locationName" required>
                                                    <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                                    <option>Less: WHT</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row form-group">
                                            <label for="slipDate" class="col-lg-4 col-form-label text-right">Discount: </label>
                                            <div class="col-lg-8">
                                                <input type="text" class="form-control rounded-0 currency text-right" name="slipDate" required value="0.00">
                                            </div>
                                        </div>

                                        <div class="row form-group">
                                            <label for="slipDate" class="col-lg-4 col-form-label text-right">Total Net: </label>
                                            <div class="col-lg-8">
                                                <input type="text" class="form-control rounded-0 currency text-right" name="slipDate" required value="0.00">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3 col-lg-8">
                                    <div class="col-lg-6"></div>
                                    <div class="col-lg-6">
                                        <button type="button" class="btn bg-gradient-primary btn-block btn-flat" ng-click="getPendingMatches()" ng-disabled="!supplierName && !locationName">GENERATE CWO SLIP</button>
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

    <?php include './application/views/components/managersKey.php'; ?>
</div>
<!-- /.content-wrapper -->