<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="deductionreport-controller">
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
                                            <div class="panel-body"><i class="fas fa-file-alt"></i> <strong>Deduction Report</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body card-body-style">
                            <form action="" method="post" name="DeductionReportForm" enctype="multipart/form-data" ng-submit="generateDeductionRep($event)">
                                <!-- <div class="padding-style-1"></div> -->
                                <div class="row padding-style mb-2">
                                    <label for="staticEmail" class="col-md-3 col-form-label text-right">Search By: </label>
                                    <div class="col-md-4">
                                        <select class="form-control rounded-0" ng-change="search()" ng-model="searchBy" name="searchBy" required>
                                            <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                            <option>All Supplier</option>
                                            <option>Supplier</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row padding-style mb-2" ng-if="searchBy == 'Supplier'" ng-cloak>
                                    <label for="supplierSelect" class="col-md-3 col-form-label text-right">Supplier Name: </label>
                                    <div class="col-md-4">
                                        <select class="form-control rounded-0" ng-model="supplierSelect" name="supplierSelect" required>
                                            <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                            <option ng-repeat="s in suppliers" value="{{s.supplier_id}}">{{s.supplier_name}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row padding-style mb-2" ng-if="searchBy" ng-init="loadDeductionType()">
                                    <label for="dedtype" class="col-md-3 col-form-label text-right">Deduction: </label>
                                    <div class="col-md-4">
                                        <select class="form-control rounded-0" ng-model="dedtype" name="dedtype" required>
                                            <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                            <option ng-repeat="d in deductionTypes" value="{{d.deduction_type_id}}">{{d.type}}</option>                                          
                                        </select>
                                    </div>
                                </div>
                                <div class="row padding-style mb-2" ng-if="searchBy">
                                    <label for=" dateFrom" class="col-md-3 col-form-label text-right">Date From: </label>
                                    <div class="col-md-4">
                                        <input type="text" id="dateFrom" class="form-control rounded-0" name="dateFrom" ng-model="dateFrom" placeholder="YYYY-MM-DD" readonly required>
                                    </div>
                                </div>
                                <div class="row padding-style mb-2" ng-if="searchBy">
                                    <label for=" dateTo" class="col-md-3 col-form-label text-right">Date To: </label>
                                    <div class="col-md-4">
                                        <input type="text" id="dateTo" class="form-control rounded-0" name="dateTo" ng-model="dateTo" placeholder="YYYY-MM-DD" readonly required>
                                    </div>
                                </div>
                                <div class="row padding-style">
                                    <label for="staticEmail" class="col-md-3 col-form-label text-right"></label>
                                    <div class="col-md-4">
                                        <button type="submit" ng-disabled="!searchBy" class="btn bg-gradient-primary btn-flat btn-block">Generate</button>
                                        <!-- <a class="btn bg-gradient-primary btn-flat btn-block" href="<?php echo base_url(); ?>generateDeductionReport/{{searchBy}}/{{supplierSelect}}/{{dedtype}}/{{dateFrom}}/{{dateTo}}" > Generate</a>  -->
                                    </div>
                                </div>
                            </form>
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