<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="proformavscrf-controller">
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
                                            <div class="panel-body"><i class="fas fa-file-alt"></i> <strong>PRO-FORMA VS CRF/CV</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <?php if ($this->session->userdata('userType') == 'Accounting' || $this->session->userdata('userType') == 'SOPAccttg' || $this->session->userdata('userType') == 'Admin') : ?>
                                        <button class="btn bg-gradient-primary btn-flat" data-target="#addProformaVsCRFReport" data-toggle="modal"><i class="fas fa-upload"></i> Upload CRF/CV</button>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <hr>

                            <div class="container" style="padding-left: 220px; padding-right: 220px;">
                                <div class="row col-lg-12">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="supplierName">Supplier Name</label>
                                            <select class="form-control rounded-0" ng-model="supplierName" name="supplierName" required>
                                                <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                                <option ng-repeat="s in suppliers" value="{{s.supplier_id}}">{{s.supplier_name}}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="locationName">Location Name </label>
                                            <select class="form-control rounded-0" ng-model="locationName" name="locationName" required>
                                                <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                                <option ng-repeat="c in customers" value="{{c.customer_code}}">{{c.customer_name}}</option>
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
                                        <button type="button" class="btn btn-primary btn-block btn-flat" ng-click="getCrfs()" ng-disabled="!supplierName || !locationName || !dateFrom || !dateTo">GENERATE</button>
                                    </div>
                                </div>
                            </div>

                            <div ng-if="pendingCrf">
                                <table id="crfTable" class="table table-bordered table-hover table-sm">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" style="width:14%" class="text-center">Supplier</th>
                                            <th scope="col" style="width:8%" class="text-center">CRF/CV No.</th>
                                            <th scope="col" style="width:8%" class="text-center">Date</th>
                                            <th scope="col" style="width:10%" class="text-center">Amount</th>
                                            <th scope="col" style="width:22%" class="text-center">Remarks</th>
                                            <th scope="col" style="width:10%" class="text-center">Audit Status</th>
                                            <th scope="col" style="width:10%" class="text-center">Matching Status</th>
                                            <th scope="col" style="width:8%" class="text-center">CRF vs PI</th>
                                            <th scope="col" style="width:10%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="cf in crf" ng-cloak>
                                            <td class="text-center">{{cf.supplier_name}}</td>
                                            <td class="text-center">{{cf.crf_no}}</td>
                                            <td class="text-center">{{cf.crf_date | date:'mediumDate'}}</td>
                                            <td class="text-center">{{cf.crf_amt | currency:'₱ '}}</td>
                                            <td class="text-center">{{cf.remarks}}</td>
                                             <!-- AUDIT STATUS START -->
                                             <td class="text-center" ng-if="cf.audited==1">
                                                <span class="badge badge-success">AUDITED</span> 
                                            </td>
                                            <td class="text-center" ng-if="cf.audited==0">
                                                <span class="badge badge-warning">UNAUDITED</span>
                                            </td>             
                                            <!-- AUDIT STATUS END -->   
                                            <!-- MATCHING STATUS START -->
                                            <td class="text-center" ng-if="cf.status=='PENDING' ">
                                                <span class="badge badge-warning">PENDING</span>
                                            </td>
                                            <td class="text-center" ng-if="cf.status=='MATCHED' ">
                                                <span class="badge badge-success">MATCHED</span>
                                            </td>
                                            <!-- MATCHING STATUS END -->
                                            <!-- CRFVSPI STATUS START -->
                                            <td class="text-center" ng-if="cf.crfvspi=='0' ">
                                                <span class="badge badge-warning">UNCLOSED</span>
                                            </td>
                                            <td class="text-center" ng-if="cf.crfvspi=='1' ">
                                                <span class="badge badge-success">CLOSED</span>
                                            </td>
                                            <!-- CRFVSPI STATUS END -->
                                            <td class="text-center">
                                                <?php if ($this->session->userdata('userType') == 'Accounting' || $this->session->userdata('userType') == 'IAD' || $this->session->userdata('userType') == 'Manager' || $this->session->userdata('userType') == 'Admin') : ?>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" id="" class="btn bg-gradient-primary btn-flat btn-sm dropdown-toggle" data-toggle="dropdown">Action</button> 
                                                        <div class="dropdown-menu" aria-labelledby="" style="margin-right: 50px;">
                                                            <?php if ($this->session->userdata('userType') == 'Accounting' || $this->session->userdata('userType') == 'Manager' || $this->session->userdata('userType') == 'Admin') : ?>
                                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#" ng-click="applyProforma(cf)">
                                                                    <i class="fas fa-tag" style="color: green;"></i> Tagging/Matching
                                                                </a>  
                                                            <?php endif; ?> 
                                                            <?php if ($this->session->userdata('userType') == 'Accounting' || $this->session->userdata('userType') == 'Admin') : ?>
                                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#" ng-if="cf.status=='PENDING'" ng-click="crfTagAsMatched(cf)">
                                                                    <i class="fas fa-tag" style="color: red;"></i> Tag As Matched
                                                                </a>
                                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#" ng-if="cf.crfvspi=='0'" ng-click="crfTagAsClosed($event,cf)">
                                                                    <i class="fas fa-tag" style="color: red;"></i> Tag As Closed
                                                                </a>
                                                            <?php endif; ?>   
                                                            <?php if ($this->session->userdata('userType') == 'IAD' || $this->session->userdata('userType') == 'Admin') : ?>
                                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#" ng-if="cf.audited=='0'" ng-click="tagAsAudited(cf)">
                                                                    <i class="fas fa-tag" style="color: red;"></i> Tag As Audited
                                                                </a>   
                                                            <?php endif; ?>  
                                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#trackCRFModal" ng-click="trackCRF(cf)">
                                                                    <i class="fas fa-tag" style="color: red;"></i> Track CRF/CV
                                                                </a>                                                     
                                                        </div>                                                                                                    
                                                    </div>                                                                                            
                                                <?php else : ?>
                                                    <button class="btn bg-gradient-primary btn-flat"> Action</button>

                                                <?php endif;?>    
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
    <!-- MODAL UPLOAD PROFORMA VS CRF -->
    <div class="modal fade" id="addProformaVsCRFReport" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-upload"></i> Upload New CRF</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" name="uploadProCrf" id="uploadProCrf" ng-submit="uploadCrf($event)" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="selectSupplier">Supplier Name: </label>
                                    <select name="selectSupplier" ng-model="selectSupplier"  class="form-control rounded-0" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="supplier in suppliers" value="{{supplier.supplier_id}}">{{supplier.supplier_name}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="selectCustomer">Customer Name: </label>
                                    <select name="selectCustomer" ng-model="selectCustomer" ng-change="getSop(selectSupplier,selectCustomer)" class="form-control rounded-0" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="customer in customers" value="{{customer.customer_code}}">{{customer.customer_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="selectSop">SOP No: </label>
                                    <select name="selectSop" ng-model="selectSop" class="form-control rounded-0" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-if="sops ==''" value="" disabled="" selected="" style="display:none">No Data Found</option>
                                        <option ng-repeat="s in sops" value="{{s.sop_id}}">{{s.sop_no}}-{{s.net_amount | currency:''}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="crfFile">CRF/CV : </label>
                                    <input type="file" class="form-control rounded-0" style="height:45px" id="crfFile" ng-model="crfFile" name="crfFile" onchange="angular.element(this).scope().checkExt(this)" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn bg-gradient-primary btn-flat"><i class="fas fa-upload"></i> Upload</button>
                            <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal" ng-click="closeCrf()">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL UPLOAD PROFORMA VS CRF -->

    <!-- EDIT APPLY PROFORMA TO CRF -->
    <div class="modal fade" id="applyProformaToCrf" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-0 modal-md">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-file-alt"></i> PRO-FORMA vs CRF</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- <form id="applyMatchForm">                -->
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="crfNo" class="col-sm-12 col-form-label">CRF/CV No</label>
                                    <input type="text" style="border:none" ng-model="crfNo" value="" class="form-control rounded-0" readonly>
                                    <input type="hidden" ng-model="hiddenSupplier">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="crfDate" class="col-sm-12 col-form-label">Date</label>
                                    <input type="text" style="border:none" value="{{crfDate | date:'mediumDate' }}" value="" class="form-control rounded-0" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="crfAmount" class="col-sm-12 col-form-label">Amount</label>
                                    <input type="text" style="border:none" value="{{crfAmount | currency:'₱ '}}" class="form-control rounded-0" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sopNo" class="col-sm-12 col-form-label">SOP No</label>
                                    <input type="text" style="border:none" ng-model="sopNo" class="form-control rounded-0" readonly>
                                </div>
                            </div>
                            <div class="col-md-12" style="overflow-y: scroll; height: 200px;">
                                <table id="appliedProforma" class="table table-bordered table-sm">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" class="text-center">Pro-forma Code</th>
                                            <th scope="col" class="text-center">Date</th>
                                            <th scope="col" class="text-center">PO No</th>
                                            <th scope="col" class="text-center">Item</th>
                                            <th scope="col" class="text-center">Add'l/Less</th>
                                            <th scope="col" class="text-center">Total</th>
                                            <th scope="col" class="text-center">Vendor Deal</th>
                                            <th scope="col" class="text-center"><i class="fas fa-bars"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="a in applied" ng-cloak>
                                            <td class="text-center">{{a.proforma_code}}</td>
                                            <td class="text-center">{{a.delivery_date}}</td>
                                            <td class="text-center">{{a.po_no}}</td>
                                            <td class="text-center">{{a.item_total | currency : ''}}</td>
                                            <td class="text-center">{{a.add_less | currency : ''}}</td>
                                            <td class="text-center">{{a.total | currency : ''}}</td>
                                            <td class="text-center">
                                                <select id="vendorsdeal" name="vendorsdeal" ng-model="a.dealId" class="form-control rounded-0" ng-disabled="hasDeal == '0' " required>
                                                    <option value="" selected="" style="display:none">Please Select One</option>
                                                    <option ng-repeat="deal in vendorsDeal" value="{{deal.vendor_deal_head_id}}">{{deal.vendor_deal_code}}</option>
                                                </select>
                                            </td>
                                            <td class="text-center" >
                                                <a       
                                                   href="#"                                            
                                                   style="color:red"
                                                   title= "Untag Proforma"
                                                   ng-disabled="!proceedMatch"
                                                   ng-click="untagProforma(a)">
                                                   <i class="fas fa-unlink"></i>                                                
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-md-10">
                                <label for="searchProforma">Search PRO-FORMA under Supplier : {{sName}} </label>
                                <input type="text" ng-model="searchedProf" id="searchProforma" ng-keyup="searchProf($event)" placeholder="Search by Proforma Code or Invoice No or PO No or PO Reference" class="form-control rounded-0" autocomplete="off" >
                                <div class="search-results" ng-repeat="s in searchResult " ng-if="hasResults == 1">
                                    <a 
                                        href="#" 
                                        ng-repeat="s in searchResult track by $index"                                        
                                        ng-click="getProf(s)">
                                        {{s.proforma_header_id}} - {{s.so_no}} -  {{s.po_no}} - {{s.posting_date}}<br>
                                    </a>                                  
                                </div>
                                <div class="search-results" ng-repeat="s in searchResult " ng-if="hasResults == 0">
                                    <a 
                                        href="#" 
                                        ng-repeat="s in searchResult">
                                        {{s.proforma_header_id}} <br>
                                    </a>                                  
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="btnTag" style="color:white">Pro-forma</label>
                                    <div class="button-group">
                                        <button type="button" id="btnTag" title="Apply Proforma under this CRF" class="btn btn-danger btn-flat" ng-disabled="proceedApply == 0" ng-click="applyProf()">
                                            <i class="fas fa-tag"></i> Tag
                                        </button>
                                    </div>
                                    <div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button 
                                    type="button" 
                                    id="btnMatch" 
                                    class="btn btn-success btn-flat btn-block" 
                                    ng-disabled="proceedMatch == 0" 
                                    ng-click="matchProformaVsCrf($event)">
                                    <i class="fas fa-link"></i> Match PRO-FORMA VS CRF
                                </button>
                            </div>
                            <div class="col-md-12">
                                <div class="modal-footer">
                                    <button type="button" id="btnReplace" class="btn btn-danger btn-flat" data-toggle="modal" data-target="#replaceCRF" ng-if="crfStatus == 0"> Replace CRF/CV</button>
                                    <button type="button" id="btnClose" class="btn btn-dark btn-flat" ng-click="resetProfVsCrf()" data-dismiss="modal"> Close</button>
                                </div>
                            </div>
                            <!-- </form>                   -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- EDIT APPLY PROFORMA TO CRF -->
        </div>
    </div>
    
    <!-- MODAL REPLACE CRF -->
    <div class="modal fade" id="replaceCRF" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-upload"></i> Replace CRF/CV</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" name="" id="replaceOldCRF" ng-submit="replaceCrf($event)" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="crfno">CRF/CV No: </label>                                    
                                    <input type="text" style="border:none;text-align:left;font-weight:bold" ng-model="crfNo" name="crfno" class="form-control rounded-0" readonly>                                    
                                </div>
                            </div>   
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="crfFile">New CRF/CV : </label>
                                    <input type="file" class="form-control rounded-0" style="height:45px" id="newCrf" ng-model="newCrf" name="newCrf" onchange="angular.element(this).scope().checkExt(this)" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn bg-gradient-primary btn-flat"><i class="fas fa-upload"></i> Upload</button>
                            <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal" ng-click="closeCrf()">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL REPLACE CRF -->

    
    <!-- MODAL REMINDER - TAG AS CLOSED -->
    <div class="modal fade" id="closeCRFModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"> CLOSE CRF/CV</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="closeCRFForm" ng-submit="closeCRF($event)" enctype="multipart/form-data">
                        <div class="row">
                            <div class="alert alert-warning" role="alert">
                                <h4 class="alert-heading">Reminder!</h4>
                                <p>Please check <strong>ProformavsPi</strong> matching and make sure it is <strong>MATCHED</strong>  before you make this action. You won't be able to revert the status to <strong>UNCLOSED</strong> once you tag this CRF/CV as <strong>CLOSED</strong>.</p>                                
                            </div>  
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="crfNoClose">CRF/CV No: </label>                                    
                                    <input type="text" style="border:none;text-align:left;font-weight:bold" ng-model="crfNoClose" name="crfNoClose" class="form-control rounded-0" readonly> 
                                </div>
                            </div>                     
                        </div>                        
                        <div class="modal-footer">
                            <button type="submit" class="btn bg-gradient-primary btn-flat"><i class="fas fa-tag"></i> Tag as Closed</button>
                            <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL REMINDER - TAG AS CLOSED -->

     <!-- MODAL TRACK CRF LINE  -->
    <div class="modal fade" id="trackCRFModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-info-circle"></i> CRF/CV - PSI & PI </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body modal-xl">
                    <form action="">
                        <div class="row">
                            <table class="table table-bordered table-sm">
                                <thead class="bg-dark">
                                    <tr>
                                        <th scope="col" style="width:20%" class="text-center">CRF/CV No</th>
                                        <th scope="col" style="width:20%" class="text-center">Date</th>
                                        <th scope="col" style="width:20%" class="text-center">Amount</th>
                                        <th scope="col" style="width:20%" class="text-center">SOP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-cloak>
                                        <td class="text-center">{{ tcrfNo }}</td>
                                        <td class="text-center">{{ tdate }}</td>
                                        <td class="text-center">{{ tamount | currency:'₱ '}}</td>
                                        <td class="text-center">{{ tsop  }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <strong>PRO-FORMA SALES INVOICE</strong>
                            <table id="proformaLine" class="table table-bordered table-sm table-hover">
                                <thead class="bg-dark">
                                    <tr>
                                        <th scope="col" style="width:20%" class="text-center">Proforma</th>
                                        <th scope="col" style="width:20%" class="text-center">Date</th>
                                        <th scope="col" style="width:20%" class="text-center">PO No</th>
                                        <th scope="col" style="width:20%" class="text-center">PO Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="pf in tprof" ng-cloak>
                                        <td class="text-center">{{pf.so_no}}</td>
                                        <td class="text-center">{{pf.profDate}}</td>                                        
                                        <td class="text-center">{{pf.po_no}}</td>
                                        <td class="text-center">{{pf.po_reference}}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <strong>PURCHASE INVOICE</strong>
                            <table id="piLine" class="table table-bordered table-sm table-hover">
                                <thead class="bg-dark">
                                    <tr>
                                        <th scope="col" style="width:20%" class="text-center">PI No</th>
                                        <th scope="col" style="width:20%" class="text-center">PI Date</th>
                                        <th scope="col" style="width:20%" class="text-center">PO No</th>
                                        <th scope="col" style="width:20%" class="text-center">PO Reference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="pi in tpi" ng-cloak>
                                        <td class="text-center">{{pi.pi_no}}</td>
                                        <td class="text-center">{{pi.posting_date}}</td>                                        
                                        <td class="text-center">{{pi.po_no}}</td>
                                        <td class="text-center">{{pi.po_reference}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal" ng-click=""> Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--  MODAL TRACK CRF LINE  -->

</div>
<!-- /.content-wrapper -->