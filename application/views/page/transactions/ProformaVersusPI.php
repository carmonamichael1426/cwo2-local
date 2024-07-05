<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="proformavspi-controller">
    
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
                                            <div class="panel-body"><i class="fas fa-file-alt"></i> <strong>PRO-FORMA VS PI</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php if ($this->session->userdata('userType') == 'PI' || $this->session->userdata('userType') == 'Admin') : ?>
                                    <div class="col-md-12 mb-4">
                                        <button 
                                            class="btn bg-gradient-primary btn-flat"
                                            data-target="#addProformaVSPI"
                                            data-toggle="modal"><i class="fas fa-upload"></i> Upload PI 
                                        </button>
                                        <button 
                                            class="btn bg-gradient-primary btn-flat"
                                            data-target="#createPi"
                                            data-toggle="modal"><i class="fas fa-upload"></i> Upload PI - Tubigon
                                        </button>
                                    </div>
                                    
                                <?php endif; ?>
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
                                        <button type="button" class="btn btn-primary btn-block btn-flat" ng-click="loadPi(supplierName,locationName)" ng-disabled="!supplierName || !locationName || !dateFrom || !dateTo">GENERATE</button>
                                    </div>
                                </div>
                            </div>

                            <!-- PI TABLE  -->
                            <div ng-show="pendingPi">
                                
                                <table id="proformaVspiTable" class="table table-bordered table-hover table-sm">
                                    <thead class="bg-dark">
                                        <tr>                                  
                                            <th scope="col" style="width:15%" class="text-center">Location</th>  
                                            <th scope="col" style="width:10%" class="text-center">PI No.</th>
                                            <th scope="col" style="width:10%" class="text-center">Ref No.</th>
                                            <th scope="col" style="width:10%" class="text-center">PI Date</th>
                                            <th scope="col" style="width:10%" class="text-center">PO</th>
                                            <th scope="col" style="width:10%" class="text-center">PO Date</th>
                                            <th scope="col" style="width:10%" class="text-center">Credit Memo</th>
                                            <th scope="col" style="width:10%" class="text-center">Audit Status</th>
                                            <th scope="col" style="width:10%" class="text-center">Matching Status</th>
                                            <th scope="col" style="width:5%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-if="pi" ng-repeat="p in pi" ng-cloak>                                            
                                            <td class="text-center">{{p.customer_name}}</td>
                                            <td class="text-center">{{p.pi_no}}</td>
                                            <td class="text-center">{{p.vendor_invoice_no}}</td>
                                            <td class="text-center">{{p.date | date:'mediumDate' }}</td>
                                            <td class="text-center">{{p.po_no}}</td>  
                                            <td class="text-center">{{p.piDate | date:'mediumDate'}}</td> 
                                            <!--CREDIT MEMO START -->
                                            <td class="text-center" ng-if="p.cm_no" >
                                                <a  
                                                    style="color:red;font-weight:bold"                                                                         
                                                    href="#"
                                                    title="View CM Details"
                                                    data-toggle="modal"                                                   
                                                    data-target="#viewCM"
                                                    ng-click="viewCmDetails(p)">
                                                    {{p.cm_no}}
                                                </a>  
                                            </td>
                                            <td class="text-center" ng-if="!p.cm_no">NO APPLIED CM</td> 
                                            <!--CREDIT MEMO END -->
                                            <!-- AUDIT STATUS START -->
                                            <td class="text-center" ng-if="p.audited==1">
                                                <span class="badge badge-success">AUDITED</span> 
                                            </td>
                                            <td class="text-center" ng-if="p.audited==0">
                                                <span class="badge badge-warning">UNAUDITED</span>
                                            </td>             
                                            <!-- AUDIT STATUS END -->   
                                            <!-- MATCHING STATUS START -->                           
                                            <td class="text-center" ng-if="p.status == 'PENDING' "> 
                                                <span class="badge badge-warning">PENDING</span>
                                            </td>   
                                            <td class="text-center" ng-if="p.status == 'MATCHED' "> 
                                                <span class="badge badge-success">MATCHED</span>
                                            </td>
                                            <!-- MATCHING STATUS END -->  
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <button 
                                                        type="button" 
                                                        class="btn bg-gradient-primary btn-flat btn-sm dropdown-toggle" 
                                                        data-toggle="dropdown">Action
                                                    </button>
                                                    <div class="dropdown-menu" style="margin-right: 50px;">
                                                        <?php if ($this->session->userdata('userType') == 'Manager'||
                                                                  $this->session->userdata('userType') == 'Supervisor' ||
                                                                  $this->session->userdata('userType') == 'Section Head'): ?>
                                                            <a                                                            
                                                                class="dropdown-item"                                                             
                                                                href="#"
                                                                data-toggle="modal"
                                                                title="View Item"
                                                                data-target="#viewPiDetails"
                                                                ng-click="viewPiDetails(p)">
                                                                <i class="fas fa-search" style="color: green;"></i> View
                                                            </a>

                                                        <?php endif; ?>
                                                        <?php if ($this->session->userdata('userType') == 'Accounting' || 
                                                                  $this->session->userdata('userType') == 'PI'  || 
                                                                  $this->session->userdata('userType') == 'Admin'): ?>
                                                            <a                                                            
                                                                class="dropdown-item"                                                             
                                                                href="#"
                                                                data-toggle="modal"
                                                                title="View Item"
                                                                data-target="#viewPiDetails"
                                                                ng-click="viewPiDetails(p)">
                                                                <i class="fas fa-search" style="color: green;"></i> View
                                                            </a>        
                                                            <a                                                            
                                                                class="dropdown-item"
                                                                title="Upload CM for this PI"
                                                                href="#"
                                                                data-toggle="modal" 
                                                                data-target="#uploadCMForm"
                                                                ng-if="!p.cm_no"
                                                                ng-click="applyCm(p)">
                                                                <i class="fas fa-upload" style="color: green;"></i> Upload CM
                                                            </a>  
                                                        <?php endif; ?>
                                                        <?php if ($this->session->userdata('userType') == 'Accounting' || $this->session->userdata('userType') == 'Manager' || $this->session->userdata('userType') == 'Admin') :?>
                                                            <a                                                            
                                                                class="dropdown-item"
                                                                title="Tag this PI"
                                                                href="#"
                                                                data-toggle="modal" 
                                                                data-target="#"
                                                                ng-click="tag(p)">
                                                                <i class="fas fa-tag" style="color: green;"></i> Tagging/Matching
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php if ($this->session->userdata('userType') == 'Accounting' || $this->session->userdata('userType') == 'Admin') :?>
                                                            <a                                                            
                                                                class="dropdown-item"
                                                                title="Change Status"
                                                                href="#"
                                                                data-toggle="modal" 
                                                                data-target="#" 
                                                                ng-if="p.status == 'PENDING' && p.crf_line_id" 
                                                                ng-click="changeStatus(p)">
                                                                <i class="fas fa-tag" style="color: red;"></i> Tag As Matched
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php if ($this->session->userdata('userType') == 'IAD' || $this->session->userdata('userType') == 'Admin') : ?>
                                                            <a ng-if="p.audited==0"                                                           
                                                                class="dropdown-item"
                                                                title="Tag as audited"
                                                                href="#"
                                                                data-toggle="modal" 
                                                                data-target="#" 
                                                                ng-click="tagAsAudited(p)">
                                                                <i class="fas fa-tag" style="color: red;"></i> Tag As Audited
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>                                            
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- PI TABLE  -->
                        </div>
                    </div>
                </div>
                <!-- /.col-md-6 -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->

    <!-- MODAL UPLOAD PROFORMA VS PI -->
    <div class="modal fade" id="addProformaVSPI"  data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-upload"></i> Upload New PI </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="uploadProformaPi" ng-submit="uploadPi($event)" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="selectSupplier">Supplier Name: </label>
                                    <select name= "selectSupplier" ng-model="selectSupplier" class="form-control rounded-0" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="supplier in suppliers" value="{{supplier.supplier_id}}">{{supplier.supplier_name}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="selectCustomer">Location Name: </label>
                                    <select name="selectCustomer" ng-model="selectCustomer" class="form-control rounded-0" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="customer in customers" value="{{customer.customer_code}}">{{customer.customer_name}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="piFile">Purchase Invoice : </label>
                                    <input type="file" class="form-control rounded-0" style="height:45px" id="piFile" ng-model="piFile" name="piFile[]" required multiple>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn bg-gradient-primary btn-flat" ><i class="fas fa-upload"></i> Upload</button>
                            <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal" ng-click="closeProformaPi()">Close</button>
                        </div>
                    </form>
                </div>           
            </div>
        </div>
    </div>

    <!-- MODAL VIEW PI DETAILS  -->
    <div class="modal fade" id="viewPiDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-search"></i> View Purchase Invoice Details </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body modal-xl">
                    <!-- <form action=""> -->
                        <div class="row">                                         
                            <div class="col-md-12" style="overflow-y: scroll; height: 500px;"> 
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="selectSupplier">Total Amount : </label>
                                            <input type="text" style="border:none;text-align:right;font-weight:bold" ng-value="totalAmountPi | currency :'₱ '" class="form-control rounded-0" readonly>
                                        </div>
                                    </div>
                                </div>
                                <table id="viewPiLine" class="table table-bordered table-hover table-sm">
                                    <thead class="bg-dark">
                                        <tr>                                        
                                            <th scope="col" style="width: 100px" class="text-center">Item Code</th>
                                            <th scope="col" class="text-center">Description</th>
                                            <th scope="col" class="text-center">Qty</th>
                                            <th scope="col" class="text-center">UOM</th>
                                            <th scope="col" class="text-center">Direct Unit Cost</th>
                                            <th scope="col" class="text-center">Amount</th>
                                            <th scope="col" class="text-center">Remarks</th>
                                            <th scope="col" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>               
                                        <tr ng-repeat="d in details" ng-cloak>
                                            <td class="text-center" style="color:red; font-weight: bold;">
                                                <button
                                                    title="Edit Item Details"
                                                    class="btn btn-info btn-flat btn-sm"
                                                    ng-disabled="!canEdit"
                                                    ng-click="fetchItemPrice(d)">
                                                    {{d.item_code}}
                                                </button>
                                            </td>
                                            <td class="text-center">{{d.description}}</td>
                                            <td class="text-center">{{d.qty}}</td>
                                            <td class="text-center">{{d.uom}}</td> 
                                            <td class="text-center">{{d.direct_unit_cost | currency: '₱ '}}</td> 
                                            <td class="text-center">{{d.amt_including_vat | currency:'₱ '}}</td> 
                                            <td class="text-center">{{d.remarks}}</td> 
                                            <td class="text-center">
                                                <button 
                                                    type="button"
                                                    title="View History"
                                                    class="btn btn-success btn-flat btn-sm"
                                                    data-toggle="modal"
                                                    data-target="#viewItemPriceLog"
                                                    ng-click="itemPricelog(d)">
                                                    <i class="fas fa-history"></i>
                                                </button>
                                            </td> 
                                        </tr>    
                                    </tbody>
                                </table>                               
                            </div>                      
                        </div>  
                        <div class="modal-footer">
                            <button type="button" id="updateItemBtn" class="btn btn-danger btn-flat" ng-click="managersKey($event)"><i class="fas fa-pen-square"></i> Update</button>
                            <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal" ng-click="closeViewPi()"> Close</button>
                        </div>  
                    <!-- </form>                 -->
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL VIEW PI DETAILS -->

    <!-- EDIT PRICE MODAL -->
    <div class="modal fade" id="updatePrice" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content rounded-0 modal-md">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> Edit Item Details</h5>                      
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">                       
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="itemDesc" class="col-sm-4 col-form-label">Item Description : </label>
                            <input type="text" ng-model="itemDesc" value="" class="form-control rounded-0" readonly>
                        </div>
                    </div>                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="itemQty" class="col-sm-6 col-form-label">Quantity : </label>
                            <input type="text" ng-model="itemQty" value="" class="form-control rounded-0" readonly>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="newPrice" class="col-sm-12 col-form-label">New Price (incl.VAT) : </label>
                                    <input type="text" ng-model="newPrice" value="" ng-disabled="itemQty == 0" class="form-control rounded-0" ng-change="calculate()" >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="newAmount" class="col-sm-12 col-form-label">New Amount (incl.VAT) : </label>
                                    <input type="text" ng-model="newAmount" value="" class="form-control rounded-0" readonly>
                                </div>
                            </div>
                        </div>
                    </div> 
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="itemDesc" class="col-sm-4 col-form-label">Remarks : </label>
                            <textarea ng-model="itemRemarks" cols="53" rows="3" maxlength="75"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger btn-flat" ng-click="updatePrice($event)"><i class="fas fa-save"></i> Save</button>
                        <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </div>                
            </div>
        </div>
    </div>
     <!-- EDIT PRICE MODAL -->

    <!-- MODAL ITEM PRICE LOG -->
    <div class="modal fade" id="viewItemPriceLog" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-history"></i> Item Price Log </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">     
                    <form id="itemPriceLog">            
                        <div class="col-md-12">
                            <div class="row">
                                <table class="table table-bordered table-sm">
                                    <thead class="bg-dark">
                                        <tr> 
                                            <th scope="col" class="text-center">Item</th>
                                            <th scope="col" class="text-center">Quantity</th>
                                            <th scope="col" class="text-center">UOM</th>
                                        </tr>
                                    </thead>
                                    <tbody>                                    
                                        <tr>
                                            <td class="text-center">{{ itemCode }}</td>
                                            <td class="text-center">{{ quantity }}</td>
                                            <td class="text-center">{{ uom }}</td>                                    
                                        </tr>    
                                    </tbody> 
                                </table>
                            
                                <table id="priceLogTable" class="table table-bordered table-sm">
                                    <thead class="bg-dark">
                                        <tr> 
                                            <th scope="col" class="text-center">Price(Old)</th>
                                            <th scope="col" class="text-center">Amount(Old)</th>
                                            <th scope="col" class="text-center">Date Edited</th>
                                            <th scope="col" class="text-center">Changed By</th>
                                        </tr>
                                    </thead>
                                    <tbody>                                    
                                        <tr ng-repeat="pl in pricelog">
                                            <td class="text-center">{{ pl.old_price | currency:'₱ ': '5'}}</td>
                                            <td class="text-center">{{ pl.old_amt | currency:'₱ ' }}</td>
                                            <td class="text-center">{{ pl.changed_date | date:'mediumDate'}}</td> 
                                            <td class="text-center">{{ pl.username}}</td>                                    
                                        </tr>    
                                    </tbody> 
                                </table> 
                            </div> 
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal">Close</button>
                        </div>  
                    </form>                           
                </div>                  
            </div>
        </div>
    </div>
    <!-- MODAL ITEM PRICE LOG -->

     <!-- TAG PI -->
    <div class="modal fade" id="tagPi" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-0 modal-xl">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-file-alt"></i> PRO-FORMA vs PI</h5>                      
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="applyPiForm" ng-submit="applyPi($event)" enctype="multipart/form-data">  
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group" >
                                        <label for="crf" class="col-sm-12 col-form-label">CRF/CV : </label>
                                        <input type="text" ng-model="crf" id="searchedCrfCv" ng-keyup="searchCrf($event)" placeholder="Search by CRF/CV Number" class="form-control rounded-0" autocomplete="off" >
                                        <div class="search-results" ng-repeat="s in searchResult " ng-if="hasResults == 1">
                                            <a 
                                                href="#" 
                                                ng-repeat="s in searchResult track by $index"                                        
                                                ng-click="getCrf(s);loadProfPi();">
                                                {{s.crf_id}} - {{s.crf_no}} - {{s.crf_date}}<br>
                                            </a>                                  
                                        </div>
                                        <div class="search-results" ng-repeat="s in searchResult " ng-if="hasResults == 0">
                                            <a 
                                                href="#" 
                                                ng-repeat="s in searchResult">
                                                {{s.crf_id}} <br>
                                            </a>                                  
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="crfDate" class="col-sm-12 col-form-label">Date : </label>
                                        <input type="text" style="border:none" id="crfDate" ng-model="crfDate" value="{{crfDate | date:'mediumDate'}}" value="" class="form-control rounded-0" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="crfAmount" class="col-sm-12 col-form-label">CRF/CV Amount : </label>
                                        <input type="text" style="border:none" id="crfAmount" ng-model="crfAmount" value="" value="" class="form-control rounded-0" readonly>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="sopNo" class="col-sm-12 col-form-label">SOP No : </label>
                                        <input type="text" style="border:none" id="sopNo" ng-model="sopNo " value="" class="form-control rounded-0" readonly>
                                        <input type="hidden" name="" ng-model="sopId" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" >
                                        <label for="selectVendorsDeal" class="col-sm-12 col-form-label">Vendor's Deal</label>
                                        <select id="vendorsdeal" name="vendorsdeal" ng-model="selectVendorsDeal" class="form-control rounded-0" ng-change="displayVendorsdDealToInput(selectVendorsDeal,vendorsDeal)">
                                            <option ng-if="vendorsDeal ==''" value="" disabled="" selected="" style="display:none">No Data Found</option>
                                            <option value="" selected="" style="display:none">Please Select One</option>
                                            <option ng-repeat="deal in vendorsDeal" value="{{deal.vendor_deal_head_id}}">{{deal.vendor_deal_code}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="periodFrom" class="col-sm-12 col-form-label">Period From</label>
                                        <input type="text" style="border:none" ng-model="periodFrom" value="" class="form-control rounded-0" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="periodTo" class="col-sm-12 col-form-label">Period To</label>
                                        <input type="text" style="border:none" ng-model="periodTo" class="form-control rounded-0" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>  
                        
                        <center ng-show="crf && !profPiHasLoaded"><div class="spinner"></div></center>
                            <div class="row" ng-show="crf && profPiHasLoaded">
                                <!-- PROF TABLE   -->
                                <div class="col-md-12">                                  
                                    <strong>PRO-FORMA SALES INVOICE</strong>
                                    <table id="profTable"  class="table table-bordered table-sm">
                                        <thead class="bg-dark">
                                            <tr>
                                                <th scope="col" style="width:10%" class="text-center">Location</th>
                                                <th scope="col" style="width:30%" class="text-center">Pro-forma</th>
                                                <th scope="col" style="width:20%" class="text-center">Pro-forma Date</th>
                                                <th scope="col" style="width:20%" class="text-center">Amount</th>
                                                <th scope="col" style="width:20%" class="text-center">PO No</th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody>   
                                            <tr ng-repeat="f in profInCrf" ng-cloak>
                                                <td class="text-center">{{f.loc}}</td>
                                                <td class="text-center">{{f.profCode}}</td>
                                                <td class="text-center">{{f.delivery}}</td>
                                                <td class="text-center">{{f.item_total | currency :''}}</td>                                                    
                                                <td class="text-center">{{f.po}}</td>                                                
                                            </tr> 
                                        </tbody> 
                                    </table> 
                                </div>                                 
                                <!-- PROF TABLE   -->
                                <!-- PI TABLE   -->
                                <div class="col-md-12">
                                    <strong>PURCHASE INVOICE</strong>
                                    <table id="piTable" class="table table-bordered table-sm">
                                        <thead class="bg-dark">
                                            <tr> 
                                                <th scope="col" style="width:10%"  class="text-center">Location</th>
                                                <th scope="col" style="width:30%" class="text-center">PI No</th>
                                                <th scope="col" style="width:20%" class="text-center">PI Date</th>
                                                <th scope="col" style="width:20%" class="text-center">Amount</th>
                                                <th scope="col" style="width:20%" class="text-center">PO No</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>                                    
                                            <tr ng-repeat="pc in piInCrf" ng-cloak> 
                                                <td class="text-center">{{pc.loc}}</td>
                                                <td class="text-center">{{pc.piNo}}</td>
                                                <td class="text-center">{{pc.postDate}}</td>
                                                <td class="text-center">{{pc.total_amount | currency :''}}</td> 
                                                <td class="text-center">{{pc.po}}</td> 
                                                         
                                            </tr>    
                                        </tbody> 
                                    </table>
                                </div>
                                <!-- PI TABLE   -->
                            </div>                                                 
                        <!-- </div>  -->
                        <div>
                            <div class="form-check-inline  float-right">
                                <label class="form-check-label">
                                    <input type="checkbox" style="width: 20px;height: 20px;" class="form-check-input" ng-model="saveVariance" ng-disabled="proceedMatchPi == 0 || proceedMatchProf == 0 || !selectVendorsDeal" value="">Save Variance
                                </label>
                            </div>
                        </div>
                        <div class="row col-md-12">
                            <div class="col-md-6">
                                <button                                
                                    type="button"   
                                    id="btnMatch"                             
                                    class="btn btn-success btn-flat btn-block"                                
                                    ng-disabled="proceedMatchPi == 0 || proceedMatchProf == 0 || !selectVendorsDeal "
                                    ng-click="matchProformaVsPi(searchedCrfId,'1',$event)">
                                    <i class="fas fa-link"></i> Matching 1 
                                </button>                                
                            </div>
                            <div class="col-md-6">
                                <button                                
                                    type="button"   
                                    id="btnMatch2"                             
                                    class="btn btn-success btn-flat btn-block"                                
                                    ng-disabled="proceedMatchPi == 0 || proceedMatchProf == 0 || !selectVendorsDeal"
                                    ng-click="matchProformaVsPi(searchedCrfId,'2',$event)">
                                    <i class="fas fa-link"></i> Matching 2
                                </button>                                 
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit"  id="btnTag" title="Apply PI under this CRF" class="btn btn-danger btn-flat" ><i class="fas fa-tag"></i> TAG PI</button>
                            <button type="button"  id="btnUntag" title="Apply PI under this CRF" class="btn btn-danger btn-flat" ng-click="untagPi($event)"><i class="fas fa-unlink"></i> UNTAG PI</button>
                            <button type="button" id="btnClose" class="btn btn-dark btn-flat" data-dismiss="modal" > Close</button>
                          
                        </div>
                    </form>                   
                </div>                
            </div>
        </div>
    </div>
    <!-- TAG PI -->

    <!-- MANAGER'S KEY -->
    <div class="modal fade" id="managersKey" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-key"></i> Manager's Key</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data" ng-submit="updateItem($event)">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">  
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"> <i class="fa fa-user"></i></span>
                                    </div>
                                    <input type="text" id="user" ng-model="user" name="user"  placeholder="Username" class="form-control rounded-0" autocomplete="off" autofocus>  
                                </div>

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-unlock-alt"></i></span>
                                    </div>
                                    <input type="password" id="pass" ng-model="pass" name="pass" placeholder="Password" class="form-control rounded-0" autocomplete="off">
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-flat"><i class="fas fa-key"></i> Authorize</button>
                        <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- MANAGER'S KEY -->


    <!-- MODAL UPLOAD CM -->
    <div class="modal fade" id="uploadCMForm"  data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-md" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-upload"></i> Upload Credit Memo </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" name="uploadCM" id="uploadCM" ng-submit="uploadCm($event)" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="piNo">Purchase Invoice(PI) ID: </label>                                    
                                    <input type="text" style="border:none;text-align:left;font-weight:bold" ng-model="piId" name="piId" class="form-control rounded-0" readonly>                                    
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="piNo">Purchase Invoice(PI) No: </label>                                    
                                    <input type="text" style="border:none;text-align:left;font-weight:bold" ng-model="piNo" name="piNo" class="form-control rounded-0" readonly>                                    
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="cmFile">Credit Memo : </label>
                                    <input type="file" class="form-control rounded-0" style="height:45px" id="cmFile" ng-model="cmFile" name="cmFile" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn bg-gradient-primary btn-flat"><i class="fas fa-upload"></i> Upload</button>
                            <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>           
            </div>
        </div>
    </div>
    <!-- MODAL UPLOAD CM -->

     <!-- MODAL CM DETAILS  -->
     <div class="modal fade" id="viewCM" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-info-circle"></i> Credit Memo Details </h5>
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
                                        <th scope="col" class="text-center">CM No</th>
                                        <th scope="col" class="text-center">Posting Date</th>
                                        <th scope="col" class="text-center">PI Applied</th>
                                        <th scope="col" class="text-center">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">{{ cmNo  }}</td>
                                        <td class="text-center">{{ cmPostingDate }}</td>
                                        <td class="text-center">{{ cmPI }}</td>
                                        <td class="text-center">{{ cmAmount | currency:'₱ '}}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <table id="viewCmLine" class="table table-bordered table-sm table-hover">
                                <thead class="bg-dark">
                                    <tr>
                                        <th scope="col" style="width: 100px" class="text-center">#</th>
                                        <th scope="col" style="width: 100px" class="text-center">Item Code</th>
                                        <th scope="col" class="text-center">Description</th>
                                        <th scope="col" class="text-center">Quantity</th>
                                        <th scope="col" class="text-center">UOM</th>
                                        <th scope="col" class="text-center">Unit Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="cm in cmDetails">
                                        <td class="text-center">{{$index + 1}}</td>
                                        <td class="text-center">{{cm.item_code}}</td>
                                        <td class="text-center">{{cm.description}}</td>
                                        <td class="text-center">{{cm.qty}}</td>
                                        <td class="text-center">{{cm.uom}}</td>
                                        <td class="text-center">{{cm.price | currency:'₱ '}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal" ng-click="closeViewPi()"> Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- MODAL CM DETAILS -->

    <!-- MODAL NEW TAGGING -->
    <!-- <div class="modal fade" id="viewDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-link"></i> PURCHASE INVOICE TAGGING & MATCHING</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body modal-xl">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group" >
                                    <label for="purchaseinvoiceno" class="col-sm-12 col-form-label">Purchase Invoice No. :</label>
                                    <input type="text" style="border:none" ng-model="purchaseinvoiceno" value="" class="form-control rounded-0" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pipostingdate" class="col-sm-12 col-form-label">Posting Date :</label>
                                    <input type="text" style="border:none" ng-model="pipostingdate" value="" class="form-control rounded-0" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="piPONO" class="col-sm-12 col-form-label">PO No. :</label>
                                    <input type="text" style="border:none" ng-model="piPONO" class="form-control rounded-0" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" >
                                    <label for="ncrf" class="col-sm-12 col-form-label">CRF/CV : </label>
                                    <select id="ncrf" name="ncrf" ng-model="ncrf" class="form-control rounded-0" ng-change="loadProfs(ncrf,loadedCRFS)" required>
                                        <option value="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="crf in loadedCRFS" value="{{crf.crf_id}}">{{crf.crf_no}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ncrfDate" class="col-sm-12 col-form-label">Date : </label>
                                    <input type="text" style="border:none" id="ncrfDate" value="{{ncrfDate | date:'mediumDate'}}" value="" class="form-control rounded-0" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ncrfAmount" class="col-sm-12 col-form-label">CRF/CV Amount : </label>
                                    <input type="text" style="border:none" id="ncrfAmount" value="{{ncrfAmount | currency:'₱ '}}" value="" class="form-control rounded-0" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="nsopNo" class="col-sm-12 col-form-label">SOP No : </label>
                                    <input type="text" style="border:none" id="nsopNo" ng-model="nsopNo " value="" class="form-control rounded-0" readonly>
                                    <input type="hidden" name="" ng-model="sopId" readonly>
                                </div>
                            </div>                                
                            <div class="col-md-4">
                                <div class="form-group" >
                                    <label for="selectVendorsDealPi" class="col-sm-12 col-form-label">Vendor's Deal</label>
                                    <select id="selectVendorsDealPi" name="selectVendorsDealPi" ng-model="selectVendorsDealPi" class="form-control rounded-0" ng-change="showDealsToInput(selectVendorsDealPi,vendorsDeal)">
                                        <option ng-if="vendorsDeal ==''" value="" disabled="" selected="" style="display:none">No Data Found</option>
                                        <option value="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="deal in vendorsDeal" value="{{deal.vendor_deal_head_id}}">{{deal.vendor_deal_code}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dealPeriodFrom" class="col-sm-12 col-form-label">Period From</label>
                                    <input type="text" style="border:none" ng-model="dealPeriodFrom" value="" class="form-control rounded-0" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dealPeriodTo" class="col-sm-12 col-form-label">Period To</label>
                                    <input type="text" style="border:none" ng-model="dealPeriodTo" class="form-control rounded-0" readonly>
                                </div>
                            </div>
                        </div>
                    </div>                     
                    <div class="col-md-12" >
                        <div class="row">
                            <div class="col-md-12">                                  
                                <strong>PRO-FORMA SALES INVOICE</strong>
                                <table id="tagProfTable"  class="table table-bordered table-sm">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" style="width:15%" class="text-center">Location</th>
                                            <th scope="col" style="width:30%" class="text-center">Pro-forma</th>
                                            <th scope="col" style="width:10%" class="text-center">Date</th>
                                            <th scope="col" style="width:20%" class="text-center">PO No</th>
                                            <th scope="col" style="width:15%" class="text-center">PO Date</th>
                                            <th scope="col" style="width:10%" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>   
                                        <tr ng-repeat="a in appliedProfs" ng-cloak>
                                            <td class="text-center">{{a.l_acroname}}</td>
                                            <td class="text-center">{{a.proforma_code}}</td>
                                            <td class="text-center">{{a.delivery_date}}</td>
                                            <td class="text-center">{{a.po_no}}</td>
                                            <td class="text-center">{{a.posting_date}}</td>  
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
                    </div> 
                    <div class="col-md-12">
                        <div class="row">                             
                            <div class="col-md-10">
                                <label for="searchProforma">Search PRO-FORMA under Supplier : {{sName}} </label>
                                <input type="text" ng-model="searchedProf" id="searchProforma" ng-disabled="!ncrf" ng-keyup="searchProf($event)" placeholder="Search by Proforma Code or Invoice No or PO No or PO Reference" class="form-control rounded-0" autocomplete="off" >
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
                                    <label for="btnTagProf" style="color:white">Pro-forma</label>
                                    <div class="button-group">
                                        <button type="button" id="btnTagProf" title="Apply Proforma under this CRF" class="btn btn-danger btn-flat" ng-disabled="proceedApply == 0" ng-click="applyProf()">
                                            <i class="fas fa-tag"></i> Tag
                                        </button>
                                    </div>
                                    <div>
                                    </div>
                                </div>
                            </div>
                        </div>  
                    </div>
                    <div class="">
                        <button                                
                            type="button"   
                            id="btnProfvsPi"                             
                            class="btn btn-success btn-flat btn-block"                                
                            ng-disabled="proceedMatch == 0 || !selectVendorsDealPi"
                            ng-click="ProformaVsPi(ncrf,$event)">
                            <i class="fas fa-link"></i> Match PRO-FORMA VS PI 
                        </button>      
                    </div>
                    <div class="modal-footer">                            
                        <button type="button" id="" class="btn btn-dark btn-flat" data-dismiss="modal" ng-click="resetPITaggingForm($event)" > Close</button>                           
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <!-- MODAL NEW TAGGING -->

    <!-- UPLOAD PI TUBIGON -->
    <div class="modal fade" id="createPi" aria-hidden="true" role="dialog"  data-backdrop="static" data-keyboard="false" aria-labelledby="createPoLabel" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="createProformaModalLabel">Create Purchase Invoice - Tubigon</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" name="uploadPiTubigon" ng-submit="uploadPiTub($event)"  enctype="multipart/form-data">                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="pitub_loc"> Location Name : </label>
                                    <input type="text" class="form-control border-0 rounded-0" name="pitub_loc" value="LDI - TUBIGON WAREHOUSE" ng-readonly="true">
                                </div>
                            </div>   
                        </div>       
                        <div class="row">                       
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="pitub_sup"><i class="fab fa-slack required-icon"></i> Supplier Name : </label>
                                    <select ng-model="pitub_sup" name="pitub_sup" class="form-control rounded-0" ng-required="true">
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="supplier in suppliers" value="{{supplier.supplier_id}}">{{supplier.supplier_name}}</option>
                                    </select>
                                </div>
                            </div>                                                    
                        </div>   
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label for="pitub_po"><i class="fab fa-slack required-icon"></i> Purchase Order : </label>
                                <input type="text" ng-model="pitub_po" id="pitub_po" ng-disabled="!pitub_sup" ng-keyup="searchPo($event)" placeholder="Search PO No or PO Reference" class="form-control rounded-0" autocomplete="off" ng-required="true">                               
                                <div class="search-results" ng-repeat="s in searchedPo " ng-if="hasResults == 1">
                                    <a 
                                        href="#" 
                                        ng-repeat="s in searchedPo track by $index"                                        
                                        ng-click="displayPoDet(s)">
                                        {{s.po_header_id}} - {{s.po_no}} -  {{s.po_reference}} - {{s.posting_date}}<br>
                                    </a>                                  
                                </div>
                                <div class="search-results" ng-repeat="s in searchedPo" ng-if="hasResults == 0">
                                    <a 
                                        href="#" 
                                        ng-repeat="s in searchedPo">
                                        {{s.po_header_id}} <br>
                                    </a>                                  
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pitub_ref">Reference No.: </label>
                                    <input type="text" class="form-control border-0 rounded-0" ng-model="pitub_ref" name="pitub_ref" ng-readonly="true">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pitub_date">Date : </label>
                                    <input type="text" class="form-control border-0 rounded-0" ng-model="pitub_date" name="pitub_date" ng-readonly="true">
                                </div>
                            </div>             
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="pitub_File">Purchase Invoice : </label>
                                    <input type="file" class="form-control rounded-0" style="height:45px" id="pitub_File" ng-model="pitub_File" name="pitub_File" ng-required="true">
                                </div>
                            </div>
                        </div>
                                            
                        <div class="modal-footer">
                            <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled=""><i class="fas fa-upload"></i> Upload</button>
                            <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal"> Close</button>
                        </div> 
                    </form>                                               
                </div>      
            </div>            
        </div>
    </div>
    <!-- UPLOAD PI TUBIGON -->
    
</div>
<!-- /.content-wrapper -->




 