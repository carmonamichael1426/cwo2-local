<style>
    .search-crf {
        box-shadow: 5px 5px 5px #ccc;
        margin-top: 1px;
        margin-left: 0px;
        background-color: #ffffff;
        width: 97%;
        border-radius: 3px 3px 3px 3px;
        font-size: 18x;
        padding: 8px 10px;
        display: block;
        position: absolute;
        z-index: 9999;
        max-height: 300px;
        overflow-y: scroll;
        overflow: auto;
    }

</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="adjustment-controller">
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
                                            <div class="panel-body"><i class="fas fa-adjust"></i> <strong>ADJUSTMENT</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php if ($this->session->userdata('userType') == 'Accounting' || $this->session->userdata('userType') == 'Admin') : ?>
                                    <div class="col-md-12 mb-4">
                                        <button class="btn bg-gradient-primary btn-flat" data-target="#createAdjustment" data-toggle="modal"><i class="fas fa-plus-circle"></i> Create Adjustment </button>
                                    </div>                                    
                                <?php endif; ?>
                            </div>

                            <hr>

                            <div class="container" style="padding-left: 220px; padding-right: 220px;">
                                <div class="row col-lg-12">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="supplierName">Supplier Name</label>
                                            <select class="form-control rounded-0" ng-model="supplierName" name="supplierName" required>
                                                <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                                <option ng-repeat="s in suppliers" value="{{s.supplier_id}}">{{s.supplier_name}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row col-lg-12">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="supplierName">Date From</label>
                                            <input type="text" id="dateFrom" class="form-control rounded-0" name="dateFrom" ng-model="dateFrom" placeholder="YYYY-MM-DD" readonly required>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="locationName">Date To </label>
                                            <input type="text" id="dateTo" class="form-control rounded-0" name="dateTo" ng-model="dateTo" placeholder="YYYY-MM-DD" readonly required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3 col-lg-8">
                                    <div class="col-lg-6"></div>
                                    <div class="col-lg-6">
                                        <button type="button" class="btn bg-gradient-primary btn-block btn-flat" ng-click="adjustmentTable($event)" ng-disabled="!supplierName || !dateFrom || !dateTo" >GENERATE</button>
                                    </div>
                                    </div>
                            </div>

                            <div ng-if="adjlists">
                                <table id="adjTable" class="table table-bordered table-sm table-hover">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" class="text-center">Adjustment No</th>
                                            <th scope="col" class="text-center">Date</th>
                                            <th scope="col" class="text-center">Description</th>
                                            <th scope="col" class="text-center">Amount</th>
                                            <th scope="col" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="adj in adjustments" ng-cloak>
                                            <td class="text-center">{{adj.adj_no}} </td>
                                            <td class="text-center">{{adj.adj_date | date:'mediumDate'}}</td>
                                            <td class="text-center">{{adj.description}}</td>
                                            <td class="text-center">{{adj.amount | currency :''}}</td> 
                                            
                                            <td class="text-center" style="width:100px">
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn bg-gradient-primary btn-flat btn-sm dropdown-toggle" data-toggle="dropdown">Action
                                                    </button>
                                                    <div class="dropdown-menu" style="margin-right: 50px;">
                                                        <a class="dropdown-item" title="View Item" href="#" data-toggle="modal" data-target="#viewDetails" ng-click="viewDetails(adj)">
                                                            <i class="fas fa-search" style="color: green;"></i> View Details
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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

    <!-- MODAL CREATE ADJUSTMENT -->
    <div class="modal fade" id="createAdjustment" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-plus-circle"></i> Create New Adjustment </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" name="newAdjustmntForm" ng-submit="createAdjustment($event)" enctype="multipart/form-data">
                        <div class="row">                           
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="selectSupplier">Supplier Name: </label>
                                    <select ng-model="selectSupplier" name="selectSupplier" class="form-control rounded-0" ng-change="changeSupplier()" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="supplier in suppliers" value="{{supplier.supplier_id}}">{{supplier.supplier_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="searchVariance">CRF/CV No: </label>
                                            <input type="text" placeholder="Search by CRF No." id="searchVariance" name="searchVariance" ng-value="searchVariance" ng-keyup="searchVar($event)" ng-disabled="!selectSupplier" value="" class="form-control rounded-0" autocomplete="off">
                                            <div class="search-crf" ng-repeat="c in searchedCRF track by $index" ng-if="hasResults2 == 1">
                                                <a 
                                                    href="#" 
                                                    ng-repeat="c in searchedCRF track by $index"                                        
                                                    ng-click="displayresult(c)">{{c.crf_no}} - {{c.crf_date}}<br>
                                                </a>                                  
                                            </div>
                                            <div class="search-crf" ng-repeat="r in searchedCRF track by $index " ng-if="hasResults2 == 0">
                                                <a 
                                                    href="#" 
                                                    ng-repeat="r in searchedCRF track by $index">
                                                    {{r.id}} <br>
                                                </a>                                  
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="crfdate">CRF/CV Date: </label>
                                            <input type="text"  style="border:none;" class="form-control rounded-0" ng-value="crfdate" readonly>
                                        </div>
                                    </div>
                                </div>  
                            </div>                              
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="varianceamt">Variance Amount: </label>
                                            <input type="text" style="border:none;" class="form-control rounded-0 text-right" ng-value="varianceamt | number : 2" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="balanceamt">Balance Amount: </label>
                                            <input type="text" style="border:none;" class="form-control rounded-0 text-righ" ng-value="balanceamt | number: 2" readonly>
                                        </div>
                                    </div>  
                                </div>
                            </div> 
                           
                            <div class="col-md-12">
                            <fieldset class="border p-2">
                                <legend  class="float-none w-auto p-2">ADJUSTMENT DETAILS</legend>

                                <div class="col-md-12 mb-4">
                                    <button type="button" ng-class=" positive ? 'btn btn-block btn-flat btn-primary' :  'btn btn-block btn-flat btn-danger' " ng-init="buttonText = 'POSITIVE ADJUSTMENT'" ng-click="settype($event)">{{buttonText}}</button>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="adjamt">Particulars/Description: </label>
                                        <input type="text" class="form-control rounded-0" name="desc" ng-model="desc" ng-disabled="!searchVariance" ng-required="true" autocomplete="off" >
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="adjamt">Adjustment Amount: </label>
                                        <input type="number" class="form-control rounded-0" name="adjamt" step="any" ng-model="adjamt" ng-disabled="!desc" >
                                    </div>
                                </div>                                  
                            </fieldset>
                            </div> 
                        </div>
                        
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success btn-flat" ng-disabled="newAdjustmntForm.$invalid">Submit</button>
                            <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal" ng-click="closeAdjForm()">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL VIEW ADJUSTMENT DETAILS  -->
    <div class="modal fade" id="viewDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-info-circle"></i> Adjustment Details </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="adj_crfno">CRF/CV No: </label>
                                <input type="text"  style="border:none;" class="form-control rounded-0" name="adj_crfno" ng-value="adj_crfno" readonly>
                            </div>
                        </div>   
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="adj_crfdate">Date: </label>
                                <input type="text"  style="border:none;" class="form-control rounded-0" name="adj_crfdate" ng-value="adj_crfdate" readonly>
                            </div>
                        </div>                         
                    </div>                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="adj_no">Adjustment No: </label>
                                <input type="text"  style="border:none;" class="form-control rounded-0" name="adj_no" ng-value="adj_no" readonly>
                            </div>
                        </div>     
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="adj_date">Date: </label>
                                <input type="text"  style="border:none;" class="form-control rounded-0" name="adj_date" ng-value="adj_date" readonly>
                            </div>
                        </div>                       
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="adj_type">Type: </label>
                                <input type="text"  style="border:none;" class="form-control rounded-0" name="adj_type" ng-value="adj_type" readonly>
                            </div>
                        </div>     
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="adj_part">Particulars/Description: </label>
                                <input type="text"  style="border:none;" class="form-control rounded-0" name="adj_part" ng-value="adj_part" readonly>
                            </div>
                        </div>     
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="adjamount">Adjustment Amount: </label>
                                <input type="text"  style="border:none; " class="form-control rounded-0 text-right" name="adj_amount" ng-value="adj_amount" readonly>
                            </div>
                        </div>     
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal"> Close</button>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL VIEW ADJUSTMENT DETAILS -->
                            
   
</div>
<!-- /.content-wrapper -->