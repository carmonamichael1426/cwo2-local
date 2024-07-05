<div ng-controller="testing-controller">
    <div class="container-fluid mt-3">
        <h4 class="text-center">TESTING AREA</h4>
    </div>
    <div class="card">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 mt-3 mb-2">
                    <a type="button" class="btn btn-primary btn-block" ng-click="sweet()">Sweet ALert</a>
                </div>
                <div class="col-lg-12 mb-2">
                    <a type="button" class="btn btn-primary btn-block" ng-click="sweet()">My Alert</a>
                </div>
                <div class="col-lg-12 mb-2">
                    <a type="button" class="btn btn-primary btn-block" href="<?php echo base_url(); ?>testPDF">Print</a>
                </div>
                <div class="col-lg-12 mb-2">
                    <a type="button" class="btn btn-primary btn-block" href="<?php echo base_url(); ?>cwoSlip">Test CWO SLIP</a>
                </div>
                <div class="col-lg-12 mb-2">
                    <a type="button" class="btn btn-info btn-block" href="" ng-click="managersKey('managersKey', 'somethingModal')">Managers Key</a>
                </div>
                <!-- <div class="col-lg-12 mb-2">
                    <a type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#SOPModal">Open DBF</a>
                </div> -->
                <div class="col-lg-12 mb-2">
                    <a type="button" class="btn btn-primary btn-block" href="<?php echo base_url(); ?>printSOP">SOP</a>
                </div>
                <div class="col-lg-12 mb-2">
                    <a type="button" class="btn btn-primary btn-block" href="<?php echo base_url(); ?>printProfVSCRF">ProfvsCRF</a>
                </div>  
                <div class="col-lg-12 mb-2">
                    <a type="button" class="btn btn-primary btn-block" href="<?php echo base_url(); ?>exportExcel">Export to Excel</a>
                </div>  
                      
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="customSwitch1" ng-model="switch" ng-change="toggleSwitch()">
                    <label class="custom-control-label" for="customSwitch1" ng-bind="label" style="user-select: none"></label>
                </div>
            </div>
        </div>
        <!-- <iframe width="420" height="315"
            src="https://www.youtube.com/embed/I9nYSumuaLA">
        </iframe> -->
    </div>
    <?php include './application/views/components/managersKey.php'; ?>
    <!-- Modal -->
    <div class="modal fade" id="somethingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-key"></i> Something</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data" ng-submit="authorize($event)">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-12">
                                    <span>SUCCESS</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-flat"><i class="fas fa-key"></i> Authorize</button>
                        <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- VIEW SOP DETAILS -->
    <div class="modal fade" id="watermark" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalCenterTitle"><i class="fas fa-info-circle"></i> SOP Details </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="container"></div>
                <div class="modal-body ">
                    <!-- <form action="" method="POST" id="newSopForm" ng-submit="submitNewSop($event)" enctype="multipart/form-data"> -->
                    <div class="watermark">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="selectSupplier">Supplier Name : </label>
                                    <input type="text" style="border:none;text-align:left;font-weight:bold" ng-model="sopSupplier" class="form-control rounded-0" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="selectSupplier">SOP Number : </label>
                                    <input type="text" style="border:none;text-align:left;font-weight:bold" ng-model="sopNumber" class="form-control rounded-0" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="selectSupplier">Location Name : </label>
                                    <input type="text" style="border:none;text-align:left;font-weight:bold" ng-model="sopCustomer" class="form-control rounded-0" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="selectSupplier">Date : </label>
                                    <input type="text" style="border:none;text-align:left;font-weight:bold" ng-model="sopDate" class="form-control rounded-0" readonly>
                                </div>
                            </div>

                        </div>

                        <hr>

                        <div class="col-md-12">
                            <!-- <div class="col-md-12"> -->
                            <div class="row">
                                <table class="table table-bordered table-sm">                                
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" style="width:25%" class="text-center">PO #</th>
                                            <th scope="col" style="width:15%" class="text-center">PO DATE</th>
                                            <th scope="col" style="width:25%" class="text-center">INVOICE #</th>
                                            <th scope="col" style="width:15%" class="text-center">INVOICE DATE</th>
                                            <th scope="col" style="width:20%" class="text-center">AMOUNT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="input-group input-group-sm rounded-0 ">
                                                    <input type="text" class="form-control rounded-0 text-center text-bold" value="SMGM00399356"  style="border: none; " readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm rounded-0 ">
                                                    <input type="text" class="form-control rounded-0 text-center text-bold" value="2021-09-28"  style="border: none; " readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm rounded-0 ">
                                                    <input type="text" class="form-control rounded-0 text-center text-bold" value="SMG-CPO-0378787" style="border: none; " readonly>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="input-group input-group-sm rounded-0 ">
                                                    <input type="text" class="form-control rounded-0 text-center text-bold" value="2021-09-28"  style="border: none;  " readonly>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm rounded-0 ">
                                                    <input type="text" class="form-control rounded-0 text-center text-bold" value="885,600.00" style="border: none;  " readonly>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr style="font-weight:bold">
                                            <td>TOTAL INVOICE </td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-center"> 885,600.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <hr>

                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Less: DEDUCTION</h5>
                                    <table class="table table-bordered table-sm">
                                        <thead class="bg-dark">
                                            <tr>
                                                <th scope="col" style="width:55%" class="text-center">DEDUCTION</th>
                                                <th scope="col" style="width:45%" class="text-center">AMOUNT</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr ng-repeat="ded in sopDeduction" ng-cloak>
                                                <td>
                                                    <div class="input-group input-group-sm rounded-0 ">
                                                        <input type="text" class="form-control rounded-0 text-center text-bold" ng-model="ded.description" style="border: none; " readonly>
                                                    </div>
                                                <td>
                                                    <div class="input-group input-group-sm rounded-0 ">
                                                        <input type="text" class="form-control rounded-0 text-center text-bold" ng-model="ded.deduction_amount | currency:' '" style="border: none;  " readonly>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr style="font-weight:bold">
                                                <td>TOTAL DEDUCTION</td>
                                                <td class="text-center">{{sopTotalDeductionAmount | currency :' '}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Add: CHARGES</h5>
                                    <table class="table table-bordered table-sm">
                                        <thead class="bg-dark">
                                            <tr>
                                                <th scope="col" style="width:45%" class="text-center">DESCRIPTION</th>
                                                <th scope="col" style="width:45%" class="text-center">AMOUNT</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr ng-repeat="charge in sopCharges" ng-cloak>
                                                <td>
                                                    <div class="input-group input-group-sm rounded-0 ">
                                                        <input type="text" class="form-control rounded-0 text-center text-bold" ng-model="charge.description" style="border: none; " readonly>
                                                    </div>
                                                <td>
                                                    <div class="input-group input-group-sm rounded-0 ">
                                                        <input type="text" class="form-control rounded-0 text-center text-bold" ng-model="charge.chargeAmount | currency:' '" style="border: none;  " readonly>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr style="font-weight:bold">
                                                <td>TOTAL CHARGES</td>
                                                <td class="text-center">{{sopTotalChargesAmount | currency :' '}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <hr>
                        

                        <div class="row">
                            <div class="col-sm-6"></div>
                            <div class="col-sm-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend rounded-0">
                                        <span class="input-group-text rounded-0" style="border: 0; background-color: white;"><strong>TOTAL INVOICE :</strong></span>
                                    </div>
                                    <input type="text" style="border:none;text-align:right;font-weight:bold" ng-model="sopTotalInvoiceAmount | currency :'₱ '" class="form-control rounded-0" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6"></div>
                            <div class="col-sm-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend rounded-0">
                                        <span class="input-group-text rounded-0" style="border: 0; background-color: white;"><strong>TOTAL DEDUCTION :</strong></span>
                                    </div>
                                    <input type="text" style="border:none;text-align:right;font-weight:bold" ng-model="sopTotalDeductionAmount | currency :'₱ ' " class="form-control rounded-0" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6"></div>
                            <div class="col-sm-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend rounded-0">
                                        <span class="input-group-text rounded-0" style="border: 0; background-color: white;"><strong>TOTAL CHARGES :</strong></span>
                                    </div>
                                    <input type="text" style="border:none;text-align:right;font-weight:bold" ng-model="sopTotalChargesAmount | currency :'₱ ' " class="form-control rounded-0" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6"></div>
                            <div class="col-sm-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend rounded-0">
                                        <span class="input-group-text rounded-0" style="border: 0; background-color: white; font-weight:bold; font-size: 120%;">NET PAYABLE AMOUNT :</span>
                                    </div>
                                    <input type="text" style="border:none;text-align:right;font-weight:bold; font-size: 180%;" ng-model="sopTotalNetPayableAmount | currency :'₱ ' " class="form-control rounded-0" readonly>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="watermark">watermark</div> -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal">Close</button>
                    </div>
                    <!-- </form> -->
                </div>
            </div>
        </div>
    </div>
    <!-- VIEW SOP DETAILS -->