<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="povspro-controller">
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
                                            <div class="panel-body"><i class="fas fa-file-alt"></i> <strong>PO VS PROFORMA</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($this->session->userdata('userType') == 'Admin' || $this->session->userdata('userType') == 'Buyer-Purchaser') : ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn bg-gradient-primary btn-flat" data-target="#addPOReport" data-toggle="modal"><i class="fas fa-file-upload"></i> Upload Proforma</button>
                                    <button class="btn bg-gradient-primary btn-flat" data-target="#addPOReportAdditionals" data-toggle="modal"><i class="fas fa-file-upload"></i> Upload Proforma (Additionals)</button>
                                    <button class="btn bg-gradient-primary btn-flat" data-target="#createProformaModal" data-toggle="modal"><i class="fas fa-plus-circle"></i> Create Pro-forma</button>
                                </div>
                            </div>
                            <?php endif; ?>

                            <hr>

                            <div class="container" style="padding-left: 220px; padding-right: 220px;">
                                <div class="row col-lg-12">
                                    <div class="col-lg-6">
                                        <div class="form-group" ng-init="getSuppliers()">
                                            <label for="supplierName">Supplier Name</label>
                                            <select class="form-control rounded-0" ng-model="supplierName" name="supplierName" ng-change="getPricing('view')" required>
                                                <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                                <option ng-repeat="s in suppliers" value="{{s.supplier_id}}">{{s.supplier_name}}</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group" ng-init="getCustomers()">
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
                                        <button type="button" class="btn bg-gradient-primary btn-block btn-flat" ng-click="getPendingMatches()" ng-disabled="!supplierName || !locationName ">GENERATE</button>
                                    </div>
                                </div>
                            </div>

                            <div ng-if="pendingMatchesTable">
                                <table id="proformaTable" class="table table-bordered table-sm table-hover">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" class="text-center">Business Unit Matched</th>
                                            <th scope="col" class="text-center">Purchase Order</th>
                                            <th scope="col" class="text-center">Purchase Order Date</th>
                                            <th scope="col" class="text-center">Proforma</th>
                                            <th scope="col" class="text-center">Proforma Date</th>
                                            <th scope="col" style="width: 50px;" class="text-center">Status</th>
                                            <th scope="col" style="width: 100px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="p in pendingMatches" ng-cloak>
                                            <td class="text-center"><i class="far fa-dot-circle"></i> {{p.supplier_name}} vs {{p.customer_name}}</th>
                                            <td class="text-center">{{p.po_no}}-{{p.po_reference}}</th>
                                            <td class="text-center">{{p.posting_date | date:'mediumDate'}}</th>
                                            <td class="text-center">{{p.proforma_code}}</td>
                                            <td class="text-center">{{p.profdate | date:'mediumDate'}}</td>
                                            <td class="text-center">
                                                <button ng-if="p.proforma_stat == '0' || p.proforma_stat == '3'" class="btn bg-gradient-danger btn-flat btn-sm">PENDING</button>
                                                <!-- <button ng-if="p.proforma_stat == '1'" class="btn bg-gradient-danger btn-flat btn-sm" disabled>PENDING</button> -->
                                            </td>
                                            <td class="text-center">
                                                <?php if($this->session->userdata('userType') != 'Receiving Clerk') : ?>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn bg-gradient-info btn-flat btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Action
                                                        </button>
                                                        
                                                            <div class="dropdown-menu rounded-0" style="margin-right: 80px;">
                                                                <a class="dropdown-item" href="#" data-target="#matchingModal" data-toggle="modal" ng-click="getItems(p)">
                                                                    <i class="fas fa-link" style="color: green;"></i> Match
                                                                </a>
                                                                <a class="dropdown-item" href="#" data-target="#viewProforma" data-toggle="modal" ng-click="view(p)">
                                                                    <i class="fas fa-pen-square" style="color: green;"></i> View/Edit
                                                                </a>

                                                                <?php if ($this->session->userdata('authorize_id') == '' || $this->session->userdata('authorize_id') == null) : ?>
                                                                    <a class="dropdown-item" href="#" ng-click="managersKey(p, 'managersKey', 'addDiscountsAddition')">
                                                                        <i class="fas fa-percentage" style="color: green;"></i> Discounts/VAT
                                                                    </a>
                                                                <?php else : ?>
                                                                    <a class="dropdown-item" href="#" data-target="#addDiscountsAddition" data-toggle="modal" ng-click="view(p)">
                                                                        <i class="fas fa-percentage" style="color: green;"></i> Discounts/VATs
                                                                    </a>
                                                                <?php endif; ?>

                                                                <a class="dropdown-item" href="#" data-target="#viewHistory" data-toggle="modal" ng-click="history(p)">
                                                                    <i class="fas fa-history" style="color: red;"></i> History
                                                                </a>
                                                            </div>
                                                        
                                                    </div>
                                                <?php else : ?>
                                                    <button type="button" class="btn bg-gradient-info btn-flat btn-sm" aria-expanded="false">Action
                                                    </button>
                                                <?php endif ?>
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

    <!-- MODAL UPLOAD PO VS PROFORMA -->
    <div class="modal fade" id="addPOReport" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-md" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-file-upload"></i> Upload Proforma</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data" ng-submit="uploadProforma($event)" name="addporeport" class="needs-validation">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" ng-init="getSuppliers()">
                                    <label for="#"><i class="fab fa-slack required-icon"></i> Supplier Name: </label>
                                    <select ng-change="getPurchaseOrder()" class="form-control rounded-0" ng-model="supplierSelect" name="supplierSelect" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="s in suppliers" ng-if="s.proforma == 'UPLOAD' && s.status =='1'" value="{{s.supplier_id}}">{{s.supplier_name}}</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please choose a supplier.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group" ng-init="getCustomers()">
                                    <label for="#"><i class="fab fa-slack required-icon"></i> Location Name: </label>
                                    <select ng-change="getPurchaseOrder()" class="form-control rounded-0" ng-model="customerSelect" name="customerSelect" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="c in customers" value="{{c.customer_code}}">{{c.customer_name}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="#"><i class="fab fa-slack required-icon"></i> Purchase Order: </label>
                                    <select class="form-control rounded-0" ng-model="poSelect" name="poSelect" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="p in po" value="{{p.po_header_id}}">{{p.po_no}} - {{p.po_reference}}</option>
                                        <option value="" disabled="" ng-if="po == ''">No PO Available</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="#"><i class="fab fa-slack required-icon"></i> Proforma: </label>
                                    <input type="file" name="proforma[]" id="proforma" class="form-control rounded-0" style="height: 45px" required multiple>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="addporeport.$invalid"><i class="fas fa-upload"></i> Upload</button>
                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL UPLOAD PO VS PROFORMA ADDITIONALS-->
    <div class="modal fade" id="addPOReportAdditionals" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-md" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-file-upload"></i> Upload Proforma (Additionals)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data" ng-submit="additionalPsi($event)" name="additionalPsiForm" class="needs-validation">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="#"><i class="fab fa-slack required-icon"></i> Purchase Order: </label>
                                    <input type="text" ng-model="poSelect2" name="poSelect2" id="poSelect2" ng-keyup="searchPO($event)" class="form-control rounded-0" placeholder="Search Purchase Order" autocomplete="off" required>
                                    <input type="hidden" ng-model="poSelectID2" name="poSelectID2" id="poSelectID2" class="form-control rounded-0">
                                    <div class="search-results" ng-repeat="p in purchaseorder" ng-if="hasResults == 1">
                                        <a 
                                            href="#" 
                                            ng-repeat="p in purchaseorder track by $index"                                        
                                            ng-click="getPSIdetails(p)">
                                            {{p.po_no}}<br>
                                        </a>    
                                    </div>
                                    <div class="search-results" ng-repeat="p in purchaseorder" ng-if="hasResults == 0">
                                        <a 
                                            href="#" 
                                            ng-repeat="p in purchaseorder">
                                            {{p.po_no}} <br>
                                        </a>                                  
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="#"><i class="fab fa-slack required-icon"></i> Supplier Name: </label>
                                    <input type="text" ng-model="supplier2" name="supplier2" id="supplier2" class="form-control rounded-0" placeholder="Supplier Name" autocomplete="off" required readonly>
                                    <input type="hidden" ng-model="supplierID2" name="supplierID2" id="supplierID2" class="form-control rounded-0" value="">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="#"><i class="fab fa-slack required-icon"></i> Location Name: </label>
                                    <input type="text" ng-model="location2" name="location2" id="location2" class="form-control rounded-0" placeholder="Location Name" autocomplete="off" required readonly>
                                    <input type="hidden" ng-model="locationID2" name="locationID2" id="locationID2" class="form-control rounded-0" value="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="#"><i class="fab fa-slack required-icon"></i> Proforma: </label>
                                    <input type="file" name="proforma2[]" id="proforma2" class="form-control rounded-0" style="height: 45px" required multiple>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="additionalPsiForm.$invalid"><i class="fas fa-upload"></i> Upload</button>
                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- VIEW PROFORMA -->
    <div class="modal fade" id="viewProforma" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-search-plus"></i> View Proforma</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data" ng-submit="editProforma($event)" name="viewForm" id="viewForm">
                    <!-- <form action="" method="post" enctype="multipart/form-data" name="viewForm" id="viewForm"> -->
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" class="form-control rounded-0" name="proforma_header_id" ng-value="proforma_header_id">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="acroname_edit">Acroname:</label>
                                    <input type="text" class="form-control rounded-0 read-only-color" name="acroname_edit" autocomplete="off" ng-model="acroname_edit" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="po_no">PO Number/PO Reference:</label>
                                    <input type="text" class="form-control rounded-0 read-only-color" name="po_no" autocomplete="off" ng-model="po_no" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="so_no">SO Number: </label>
                                    <input type="text" class="form-control rounded-0 read-only-color" name="so_no" autocomplete="off" ng-model="so_no" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="pro_code">Proforma Code: </label>
                                    <input type="text" class="form-control rounded-0 read-only-color" name="pro_code" autocomplete="off" ng-model="pro_code" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="totalAmountProf">Total Amount: </label>
                                    <input type="text" class="form-control rounded-0 read-only-color text-right currency" name="totalAmountProf" autocomplete="off" ng-model="totalAmountProf" ng-value="totalAmount() | currency:''" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="pro_code">Pricing Status: </label>
                                    <input type="text" class="form-control rounded-0 read-only-color" name="priceCheckStat" autocomplete="off" ng-model="priceCheckStat" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <fieldset>
                                    <legend style="float:right">PRICE LEGEND:</legend>
                                    <label class="alert alert-danger view-legend-text">NET of DISCOUNT with VAT</label> 
                                    <label class="alert alert-warning view-legend-text">NET of VAT and DISCOUNT</label> 
                                    <label class="alert alert-primary view-legend-text">NET of VAT with DISCOUNT</label>
                                    <label class="alert alert-info view-legend-text">GROSS of VAT w/o DISCOUNT</label>
                                    <label class="alert alert-success view-legend-text">GROSS of VAT and DISCOUNT</label>                                    
                                </fieldset>
                            </div>
                        </div>

                        <div class="container-fluid">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active rounded-0" id="proforma-line-tab" data-toggle="tab" href="#proforma-line" role="tab" aria-controls="home" aria-selected="true" style="color: black;" ng-click="tabs()">Proforma Line</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link rounded-0" id="discounts-tab" data-toggle="tab" href="#discounts" role="tab" aria-controls="profile" aria-selected="false" style="color: black;" ng-click="tabs()">Discounts and VAT</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="proforma-line" role="tabpanel" aria-labelledby="proforma-line-tab">
                                    <div class="row mt-2" ng-if="tableRow">
                                        <table class="table-sm table table-bordered table-hover">
                                            <thead class="bg-dark">
                                                <tr>
                                                    <th class="text-center">Item Code</th>
                                                    <th class="text-center">Description</th>
                                                    <th class="text-right" style="width: 10%;">QTY</th>
                                                    <th class="text-center">UOM</th>
                                                    <th class="text-center {{unitpriceClass}}" style="width: 15%;">Price</th>
                                                    <th class="text-right" style="width: 15%;">Amount</th>
                                                    <th class="text-center" style="width: 4%;;">Free</th>
                                                    <th class="text-center" style="width: 4%;;">Edit</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr ng-repeat="pl in proforma_line">
                                                    <td>{{ pl.item_code }}</td>
                                                    <td>{{ pl.description}}</td>
                                                    <td>
                                                        <div class="input-group input-group-sm rounded-0">
                                                            <input type="text" class="form-control rounded-0 text-right" ng-model="pl.qty" style="border: none;" ng-disabled="!pl.checkBoxEdit">
                                                        </div>
                                                    </td>
                                                    <td>{{ pl.uom }}</td>
                                                    <td>
                                                        <div class="input-group input-group-sm rounded-0">
                                                            <div class="input-group-prepend rounded-0">
                                                                <span class="input-group-text rounded-0" style="border: 0; background-color: white;"><strong>₱</strong></span>
                                                            </div>
                                                            <input type="text" class="form-control rounded-0 text-right currency" ng-model="pl.price" style="border: none;" ng-disabled="!pl.checkBoxEdit">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group input-group-sm rounded-0">
                                                            <div class="input-group-prepend rounded-0">
                                                                <span class="input-group-text rounded-0" style="border: 0; background-color: white;"><strong>₱</strong></span>
                                                            </div>
                                                            <input type="text" class="form-control rounded-0 text-right currency" name="amount" ng-model="pl.amountT" ng-value="pl.qty * pl.price | currency : ''" style="border: none;" readonly>
                                                        </div>
                                                    </td>
                                                    <td ng-if="pl.free == '1'">FREE</td>
                                                    <td ng-if="pl.free == '0' || !pl.free "></td>
                                                    <td class="text-center">
                                                        <div class="col-lg-12">
                                                            <div class="input-group">
                                                                <input type="checkbox" style="width: 20px; height: 20px;" ng-model="pl.checkBoxEdit" ng-changed="setButtonEnabled()" ng-disabled="priceCheckStat == 'PRICE CHECKED'" class="rounded-0">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="row mt-2" ng-if="uploadRow">
                                        <div class="col-md-12" style="padding-left: 200px; padding-right: 200px;">
                                            <div class="form-group">
                                                <label for="#">New Proforma: </label>
                                                <input type="file" name="new_proforma" id="new_proforma" class="form-control rounded-0" style="height: 45px">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="discounts" role="tabpanel" aria-labelledby="discounts-tab">
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <div class="container-fluid" style="padding-left: 200px; padding-right: 200px;">
                                                <table ng-init="getDiscount()" class="table table-bordered table-sm table-hover">
                                                    <thead class="bg-dark">
                                                        <tr>
                                                            <th class="text-center">Discount/VAT</th>
                                                            <th class="text-center">Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr ng-repeat="d in discount" ng-cloak>
                                                            <th>{{ d.discount }}</th>
                                                            <td class="text-right">{{ d.total_discount | currency : ''}}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                                                  
                        <?php if ( $this->session->userdata('userType') == 'Pricing'  || $this->session->userdata('userType') == 'Admin' ): ?>
                            <button type="button" id="btnPriceCheck" class="btn bg-gradient-success btn-flat" ng-disabled="priceCheckStat == 'PRICE CHECKED'" ng-click="priceCheck()">
                                <i class="fas fa-check-double"></i> Price Check
                            </button>
                            <button type="button" class="btn bg-gradient-primary btn-flat" ng-click="editProforma($event)" ng-disabled="setButtonEnabled() || tabIndex || priceCheckStat =='PRICE CHECKED'" ng-if="tableRow">
                                <i class="fas fa-pen-square"></i> Update & Price Check
                            </button>
                        <?php endif; ?>

                        <!-- <?php if ( $this->session->userdata('userType') != 'PI' ) : ?>
                            <button type="button" class="btn bg-gradient-primary btn-flat" ng-click="editProforma($event)" ng-disabled="(setButtonEnabled() || tabIndex) && priceCheckStat =='PRICE CHECKED'" ng-if="tableRow">
                                <i class="fas fa-pen-square"></i> Update & Price Check
                            </button>
                        <?php endif; ?> -->
                        
                        <?php if ( $this->session->userdata('userType') != 'PI'  || $this->session->userdata('userType') == 'Admin' ): ?>
                            <button type="submit" class="btn bg-gradient-success btn-flat" ng-if="uploadRow" ng-disabled="priceCheckStat == 'PRICE CHECKED'">
                                <i class="fas fa-upload"></i> Replace Proforma
                            </button>

                            <button type="button" class="btn bg-gradient-info btn-flat" ng-click="replaceProforma()" ng-disabled="priceCheckStat == 'PRICE CHECKED'">
                                <i class="fas fa-undo-alt"></i>
                                {{buttonNAme}}
                            </button>
                        <?php endif; ?>
                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ADD Discounts/VAT -->
    <div class="modal fade" id="addDiscountsAddition" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-percentage"></i> Discounts/VAT</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data" ng-submit="addDiscountVAT($event)" name="discountVATForm" id="discountVATForm">
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" class="form-control rounded-0" name="proforma_header_id" ng-value="proforma_header_id">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="acroname_edit">Acroname:</label>
                                    <input type="text" class="form-control rounded-0 read-only-color" name="acroname_edit" autocomplete="off" ng-model="acroname_edit" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="po_no">PO Number/PO Reference:</label>
                                    <input type="text" class="form-control rounded-0 read-only-color" name="po_no" autocomplete="off" ng-model="po_no" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="so_no">SO Number: </label>
                                    <input type="text" class="form-control rounded-0 read-only-color" name="so_no" autocomplete="off" ng-model="so_no" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="pro_code">Proforma Code: </label>
                                    <input type="text" class="form-control rounded-0 read-only-color" name="pro_code" autocomplete="off" ng-model="pro_code" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="container-fluid">
                                <div class="row mt-1">
                                    <label for="discount" style="margin-left: 25px;">Discount/VAT:</label>
                                    <label for="amount" style="margin-left: 240px;">Amount:</label>
                                    <div class="col-md-12" ng-init="discountData = [{}];">
                                        <div ng-repeat="data in discountData" class="row">
                                            <div class="col-md-6">
                                                <div class="form-group ml-3">
                                                    <input type="text" class="form-control rounded-0" ng-model="data.discount" id="discount" required autocomplete="off">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input step=".01" type="number" class="form-control rounded-0 text-right currency" id="amount" ng-model="data.amount" required autocomplete="off" placeholder="0.00">
                                                </div>
                                            </div>
                                            <div class="col-md-1 ml-3">
                                                <div class="row">
                                                    <div class="container">
                                                        <div class="row">
                                                            <button type="button" ng-if="$index == 0" class="btn btn-default btn-flat" ng-click="discountData.push({})">
                                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                                            </button>
                                                            <button class="btn btn-danger btn-flat" ng-if="$index > 0" ng-click="discountData.splice($index, 1)">
                                                                <i class="fa fa-minus" aria-hidden="true"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="discountVATForm.$invalid">
                            <i class="fas fa-pen-square"></i> Save
                        </button>

                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PROFORMA LINE HISTORY -->
    <div class="modal fade" id="viewHistory" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-history"></i> Proforma Line History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <table class="table table-bordered table-sm" id="historyTable" style="font-size: 12px;">
                            <thead class="bg-dark">
                                <tr>
                                    <th class="text-center">Item Code</th>
                                    <th class="text-center" style="width: 200px;">Description</th>
                                    <th class="text-center" style="width: 5%;">QTY</th>
                                    <th class="text-center">UOM</th>
                                    <th class="text-center" style="width: 15%;">Price</th>
                                    <th class="text-center" style="width: 15%;">Amount</th>
                                    <th class="text-center">Editted By</th>
                                    <th class="text-center">Approved By</th>
                                    <th>Date Edited</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="h in proforma_history">
                                    <td>{{ h.item_code }}</td>
                                    <td class="text-left">{{ h.description }}</td>
                                    <td>{{ h.qty }}</td>
                                    <td>{{ h.uom }}</td>
                                    <td class="text-right">₱ {{ h.price | currency: ''}}</td>
                                    <td class="text-right">₱ {{ h.amount | currency: ''}}</td>
                                    <td class="text-center">{{ h.editted_by }}</td>
                                    <td class="text-center">{{ h.approved }}</td>
                                    <td class="text-center">{{ h.date_edited }}</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal" ng-click="clearHistory()"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MATCH PROFORMA -->
    <div class="modal fade" id="matchingModal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-link"></i> PO vs Proforma Matching</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data" ng-submit="match($event, items)">
                    <div class="modal-body">
                        <table class="table table-bordered table-sm table-hover" id="historyTable">
                            <thead class="bg-dark">
                                <tr>
                                    <th class="text-center">PO Item Code</th>
                                    <th class="text-center">PO Description</th>
                                    <th class="text-center">Proforma Description</th>
                                    <th class="text-center">Proforma Item Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="i in items">
                                    <td class="text-center">
                                        <span class="no-item" ng-if="i.po_item == null">OVER SERVED</span>
                                        <span class="no-item" ng-if="i.po_item == 'NO SET UP'">NO SET UP</span>
                                        <span ng-if="i.po_item != null">{{ i.po_item }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="no-item" ng-if="i.po_desc == null || i.po_desc == ''">NO SET UP</span>
                                        <span class="no-item" ng-if="i.po_desc == 'NO SET UP'">NO SET UP</span>
                                        <span ng-if="i.po_desc != null || i.po_desc != ''">{{ i.po_desc }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="no-item" ng-if="i.prof_desc == null">ITEM UNAVALABLE</span>
                                        <span class="no-item" ng-if="i.prof_desc == 'NO SET UP'">NO SET UP</span>
                                        <span ng-if="i.prof_desc != null">{{ i.prof_desc }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="no-item" ng-if="i.pr_item == null">ITEM UNAVALABLE</span>
                                        <span class="no-item" ng-if="i.pr_item == 'NO SET UP'">NO SET UP</span>
                                        <span ng-if="i.pr_item != null">{{ i.pr_item }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-gradient-primary btn-flat"><i class="fas fa-link"></i> Match</button>
                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- CREATE PROFORMA -->
    <div class="modal fade" id="createProformaModal" aria-hidden="true" role="dialog"  data-backdrop="static" data-keyboard="false" aria-labelledby="createProformaModalLabel" tabindex="-1">
    <!-- <div class="modal fade" id="createProformaModal" data-backdrop="static" data-keyboard="false">     -->
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="createProformaModalLabel">Create Pro-froma</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- <h5>PURCHASE ORDER</h5> -->
                    <form action="" method="POST" id="createProformaForm" ng-submit="saveProforma($event)"  enctype="multipart/form-data">
                        <div class="row">                       
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="create_selectSupplier"><i class="fab fa-slack required-icon"></i> Supplier Name : </label>
                                    <select ng-model="create_selectSupplier" name="create_selectSupplier" ng-model="create_selectSupplier" class="form-control rounded-0" ng-change="detectChanges()" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="supplier in suppliers" ng-if="supplier.proforma == 'CREATE' && supplier.status =='1'" value="{{supplier.supplier_id}}">{{supplier.supplier_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="create_selectCustomer"><i class="fab fa-slack required-icon"></i> Location Name : </label>
                                    <select ng-model="create_selectCustomer" name="create_selectCustomer" class="form-control rounded-0" ng-change="detectChanges()" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="customer in customers" value="{{customer.customer_code}}">{{customer.customer_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="searchProforma"><i class="fab fa-slack required-icon"></i> Purchase Order : </label>
                                <input type="text" ng-model="create_searchPo" id="create_searchPo" ng-disabled="!create_selectSupplier || !create_selectCustomer" ng-keyup="searchPoCreate($event)" placeholder="Search PO No or PO Reference" class="form-control rounded-0" autocomplete="off" required>
                                <input type="hidden" name="" id="create_searchPo2" ng-model="create_searchPo2">
                                <div class="search-results" ng-repeat="s in searchedPO " ng-if="hasResultss == 1">
                                    <a 
                                        href="#" 
                                        ng-repeat="s in searchedPO track by $index"                                        
                                        ng-click="displayPoDet(s)">
                                        {{s.po_header_id}} - {{s.po_no}} -  {{s.po_reference}} - {{s.posting_date}}<br>
                                    </a>                                  
                                </div>
                                <div class="search-results" ng-repeat="s in searchedPO " ng-if="hasResultss == 0">
                                    <a 
                                        href="#" 
                                        ng-repeat="s in searchedPO">
                                        {{s.po_header_id}} <br>
                                    </a>                                  
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="create_poRef">Reference No.: </label>
                                    <input type="text" style="" class="form-control rounded-0" ng-model="create_poRef" name="create_poRef" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="create_poDate">Date : </label>
                                    <input type="text" style="" class="form-control rounded-0" ng-model="create_poDate" name="create_poDate" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="row">    
                            <div class="col-md-12">                   
                                <table id="poLine" class="table table-bordered table-sm table-hover">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" style="width:5%" class="text-center">#</th>
                                            <th scope="col" style="width:10%" class="text-center">Item Code</th>
                                            <th scope="col" style="width:38%" class="text-center">Description</th>
                                            <th scope="col" style="width:10%" class="text-center">Quantity</th>
                                            <th scope="col" style="width:10%" class="text-center">UOM</th>
                                            <th scope="col" style="width:20%" class="text-center">Direct Unit Cost</th>
                                            <th scope="col" style="width:7%" class="text-center">Select</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="d in poLine">
                                            <td class="text-center">{{$index + 1}}</td>
                                            <td class="text-center">{{d.item_code}}</td>
                                            <td class="text-center">{{d.description}}</td>
                                            <td class="text-center">{{d.qty}}</td>
                                            <td class="text-center">{{d.uom}}</td>
                                            <td class="text-center">{{d.direct_unit_cost | currency:'₱ '}}</td>
                                            <td class="text-center">
                                                <div class="">
                                                    <input  
                                                        type="checkbox" 
                                                        title=""   
                                                        style="width:38px; height:25px"
                                                        ng-change="countCheckedItems($event); optionToggled()"
                                                        ng-model="d.selected">                                     
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>          
                            </div>              
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-9"></div>
                            <div class="col-md-3" style="padding-left: 150px;">
                                <label for="">
                                    <input type="checkbox" 
                                        style="width:20px; height:18px" 
                                        ng-model="isAllSelected" 
                                        ng-disabled="!create_searchPo"
                                        ng-click="toggleAll(); countCheckedItems($event)"/> 
                                        <span ng-show="isAllSelected" >All Selected</span>
                                        <span ng-hide="isAllSelected" >Select All</span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <button type="button" id="btnAddProf" class="btn btn-danger btn-flat float-right" title="Add To Proforma" data-toggle="modal" href="#" ng-disabled="countSelected==0 || !create_searchPo" ng-click="addToProformaTable($event)">
                                    <i class="fas fa-plus-circle"></i>
                                    Add To Pro-forma
                                </button>
                            </div>                      
                        </div>
                        <hr>

                        <h4>PRO-FORMA SALES INVOICE</h4> 
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="salesinvoice"><i class="fab fa-slack required-icon"></i> Sales Invoice No. : </label>
                                    <input type="text" style="" name="salesinvoice" ng-model="salesinvoice" ng-disabled="totalInvoiceAmount == 0" class="form-control rounded-0" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="salesorder"><i class="fab fa-slack required-icon"></i> Sales Order No. : </label>
                                    <input type="text" style="" name="salesorder" ng-model="salesorder" ng-disabled="totalInvoiceAmount == 0" class="form-control rounded-0" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="deliverydate"><i class="fab fa-slack required-icon"></i> Delivery Date : </label>
                                    <input type="text" style="" id="deliverydate" ng-model="deliverydate" ng-disabled="totalInvoiceAmount == 0" class="form-control rounded-0" placeholder="YYYY-MM-DD" readonly required>
                                </div>
                            </div>
                        </div>  
                        <div class="row">
                            <div class="col-md-12">
                                <fieldset>
                                    <legend style="float:right">UNIT PRICE LEGEND :</legend>
                                    <label class="alert alert-danger create-legend-text">NET of DISCOUNT with VAT</label> 
                                    <label class="alert alert-warning create-legend-text">NET of VAT and DISCOUNT</label> 
                                    <label class="alert alert-primary create-legend-text">NET of VAT with DISCOUNT</label>
                                    <label class="alert alert-info create-legend-text">GROSS of DISCOUNT w/o VAT</label>
                                    <label class="alert alert-success create-legend-text">GROSS of VAT and DISCOUNT</label>                                    
                                </fieldset>
                            </div>
                        </div>
                        <br>
                        <div class="container-fluid">
                            <ul class="nav nav-tabs" id="myTab2" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active rounded-0" id="new-proforma-line-tab" data-toggle="tab" href="#new-proforma-line" role="tab" aria-controls="home" aria-selected="true" style="color: black;">Proforma Line</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link rounded-0" id="new-discounts-tab" data-toggle="tab" href="#new-discounts" role="tab" aria-controls="profile" aria-selected="false" style="color: black;" >Discounts and VAT</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="myTab2Content" ng-init="discounts = [{}]; profline = [{}];">
                                <div class="tab-pane fade show active" id="new-proforma-line" role="tabpanel" aria-labelledby="new-proforma-line-tab">
                                    <div class="row mt-2" > 
                                        <table id="proformalineTable" class="table table-bordered table-sm table-hover">
                                            <thead class="bg-dark">
                                                <tr>
                                                    <th scope="col" style="width:5%" class="text-center">#</th>
                                                    <th scope="col" style="width:12%" class="text-center">Material Code (Supplier Item Code)</th>
                                                    <th scope="col" style="width:12%" class="text-center">Customer Code (Navision Code)</th>
                                                    <th scope="col" style="width:25%" class="text-center">Description</th>
                                                    <th scope="col" style="width:8%" class="text-center">Quantity</th>
                                                    <th scope="col" style="width:8%" class="text-center">UOM</th>
                                                    <th scope="col" style="width:10%;" class="text-center {{unitpriceClass}}">Unit Price</th>
                                                    <th scope="col" style="width:10%" class="text-center">Amount</th>
                                                    <th scope="col" style="width:5%" class="text-center">Free</th>
                                                    <th scope="col" style="width:5%" class="text-center"><i class="fas fa-bars"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr ng-repeat="p in profline" ng-cloak>
                                                    <td ng-if="countProfLines > 0" class="text-center">{{$index + 1}}</td>
                                                    <td ng-if="countProfLines > 0" class="text-center">
                                                        <div class="input-group input-group-sm rounded-0 ">
                                                            <input type="text" class="form-control rounded-0 text-center text-bold" ng-value="p.materialcode" ng-model="p.materialcode" style="border: none; " required readonly>
                                                        </div>                                           
                                                    </td>
                                                    <td ng-if="countProfLines > 0" class="text-center">
                                                        <div class="input-group input-group-sm rounded-0 ">
                                                            <input type="text" class="form-control rounded-0 text-center text-bold" ng-value="p.customercode" ng-model="p.customercode" style="border: none; " required readonly>
                                                        </div>                                           
                                                    </td>
                                                    <td ng-if="countProfLines > 0" class="text-center">
                                                        <div class="input-group input-group-sm rounded-0 ">
                                                            <input type="text" class="form-control rounded-0 text-center text-bold" ng-value="p.description" ng-model="p.description" style="border: none; " required readonly>
                                                        </div>                                           
                                                    </td>
                                                    <td ng-if="countProfLines > 0" class="text-center">
                                                        <div class="input-group input-group-sm rounded-0 ">
                                                            <input type="number" string-to-number class="form-control rounded-0 text-center text-bold" ng-value="p.qty" ng-model="p.qty" style="border: none;" ng-keydown="validateNumber($event);" ng-change="calculateAmount('qty');" min="1" required>
                                                        </div>                                           
                                                    </td>
                                                    <td ng-if="countProfLines > 0" class="text-center">
                                                        <div class="input-group input-group-sm rounded-0 ">
                                                            <input type="text" class="form-control rounded-0 text-center text-bold" ng-value="p.uom" ng-model="p.uom" style="border: none; " maxlength="15" required >
                                                        </div>                                           
                                                    </td>
                                                    <td ng-if="countProfLines > 0" class="text-center">
                                                        <div class="input-group input-group-sm rounded-0 ">
                                                            <input type="number" string-to-number  class="form-control rounded-0 text-center text-bold" ng-value="p.unitcost" ng-model="p.unitcost" style="border: none; " ng-keydown="validateNumber($event);" ng-change="calculateAmount('unitcost')" required>
                                                        </div>                                           
                                                    </td>
                                                    <td ng-if="countProfLines > 0" class="text-center">
                                                        <div class="input-group input-group-sm rounded-0 ">                                                       
                                                            <input type="text" class="form-control rounded-0 text-center text-bold" ng-model="p.amount" ng-value="p.qty * p.unitcost "   style="border: none; " readonly>
                                                        </div>                                                                           
                                                    </td>  
                                                    <td ng-if="countProfLines > 0" class="text-center">
                                                        <div class="">
                                                            <input  
                                                                type="checkbox" 
                                                                title=""   
                                                                style="width:38px; height:25px"
                                                                ng-change="calculateAmount('checked');"
                                                                ng-model="p.selected">                                     
                                                        </div>
                                                    </td>                             
                                                    <td ng-if="countProfLines > 0" class="text-center">
                                                        <div class=""> 
                                                            <a href="#" style="color:red;" title="Remove" ng-click="profline.splice($index, 1);calculateAmount();">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </a>  
                                                        <div>                                   
                                                    </td>
                                                </tr>
                                                <tr style="font-weight:bold">
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td class="text-center">{{totalQty}}</td>                                                    
                                                    <td></td>
                                                    <td></td>
                                                    <td colspan="2" class="text-right">{{totalInvoiceAmount | currency :''}}</td>                                          
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>                                
                                </div>

                                <div class="tab-pane fade" id="new-discounts" role="tabpanel" aria-labelledby="new-discounts-tab">
                                    <div class="row mt-3">
                                        <div class="col-md-12">
                                            <div class="container-fluid" style="padding-left: 100px; padding-right: 100px;">
                                                <table class="table table-bordered table-sm table-hover">
                                                    <thead class="bg-dark">
                                                        <tr>
                                                            <th style="width:10%" class="text-center">#</th>
                                                            <th style="width:50%" class="text-center">Discount/VAT</th>
                                                            <th style="width:30%" class="text-center">Amount</th>
                                                            <th style="width:10%" class="text-center"><i class="fas fa-bars"></i></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr ng-if="countProfLines > 0" ng-repeat="d in discounts" ng-cloak>
                                                            <td class="text-center">{{$index + 1}}</td>
                                                            <td class="text-center">
                                                                <div class="input-group input-group-sm rounded-0 ">
                                                                    <input type="text" class="form-control rounded-0 text-center text-bold" value="{{d.discname}}" ng-model="d.discname" style="border: none; ">
                                                                </div>    
                                                            </td>
                                                            <td class="text-right">
                                                                <div class="input-group input-group-sm rounded-0 ">
                                                                <div class="input-group-prepend rounded-0">
                                                                    <span class="input-group-text rounded-0" style="border: 0; background-color: white;"><strong>₱</strong></span>
                                                                </div>
                                                                <input type="text" class="form-control rounded-0 text-right text-bold" style="border: none; " value="{{d.amount}}" ng-model="d.amount" ng-keydown="validateNumber($event)" ng-change="calculateAmount();">
                                                            </div> 
                                                            </td>
                                                            <td class="text-center" >
                                                                <div class=""> 
                                                                    <a href="#" style="color:red;" title="Remove" ng-click="discounts.splice($index, 1);">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </a>  
                                                                <div>                                   
                                                            </td>
                                                        </tr>
                                                        <tr style="font-weight:bold">
                                                            <td></td>
                                                            <td></td>    
                                                            <td class="text-right">{{totalDiscVat | currency :''}}</td>                                          
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="float-right" style="padding-right: 100px;">
                                                <button type="button" id="btnDiscount" class="btn btn-danger btn-flat float-right" title="Add Discount" data-toggle="modal" href="#" ng-disabled="countProfLines==0" ng-click="discounts.push({})">
                                                    <i class="fas fa-plus-circle"></i>
                                                    Add New Line
                                                </button>
                                            </div>                                        
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success btn-flat" ng-disabled="totalInvoiceAmount == 0">Save</button>
                            <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal"> Close</button>
                        </div> 
                    </form>                                               
                </div>    
                         
            </div>
           
        </div>
    </div>
    <!-- CREATE PROFORMA -->

    <?php include './application/views/components/managersKey.php'; ?>
</div>
<!-- /.content-wrapper -->