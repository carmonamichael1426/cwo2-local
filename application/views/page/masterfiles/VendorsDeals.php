<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="vendorsDeal-controller">
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
                                            <div class="panel-body"><i class="fas fa-percent"></i> <strong>VENDORS DEALS</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <button class="btn bg-gradient-primary btn-flat" data-target="#newVendorDeal" data-toggle="modal"><i class="fas fa-file-upload"></i> Upload Deals</button>
                                    <button class="btn bg-gradient-primary btn-flat" data-target="#setupNoDeal" data-toggle="modal"><i class="fas fa-file-upload"></i> Manual Setup</button>
                                </div>
                            </div>
                           
                            <hr>
                            <div class="container" style="padding-left: 220px; padding-right: 220px;">
                                <div class="row col-lg-12">
                                    <div class="col-lg-12">
                                        <div class="form-group" ng-init="getSuppliers()">
                                            <label for="supplierName">Supplier Name</label>
                                            <select class="form-control rounded-0" ng-model="supplierName" name="supplierName" required>
                                                <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                                <option ng-repeat="s in suppliers" value="{{s.supplier_id}}">{{s.supplier_name}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3 col-lg-8">
                                    <div class="col-lg-6"></div>
                                    <div class="col-lg-6">
                                        <button type="button" class="btn bg-gradient-primary btn-block btn-flat" ng-click="getVendorsDeal()" ng-disabled="!supplierName && !locationName">GET DEALS</button>
                                    </div>
                                </div>
                            </div>

                            <div ng-if="vendorsDealTables">
                                <table id="vendorsDealTable" class="table table-sm table-bordered font-small table-hover" ng-init="getVendorsDeal()" style="font-size: 14px;">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" class="text-center">Vendor Deal ID</th>
                                            <th scope="col" class="text-center">Vendor Deal Code</th>
                                            <th scope="col" class="text-center">Classification Code</th>
                                            <th scope="col" class="text-center">Description</th>
                                            <th scope="col" class="text-center">Period From</th>
                                            <th scope="col" class="text-center">Period To</th>
                                            <th scope="col" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="d in deals" ng-cloak>
                                            <td class="text-center">{{ d.vendor_deal_head_id}}</td>
                                            <td class="text-center">{{ d.vendor_deal_code}}</td>
                                            <td class="text-center">{{ d.classification_code}}</td>
                                            <td class="text-center">{{ d.description}}</td>
                                            <td class="text-center">{{ d.period_from}}</td>
                                            <td class="text-center">{{ d.period_to}}</td>
                                            <td class="text-center">
                                                <a href="#" title="Update" style="color:blue; padding-left:10px; padding-right:5px;" data-toggle="modal" data-target="#viewDetail" ng-click="fetchdetail(d)"><i class="fas fa-search"></i></a>
                                                <a href="#" title="Discounts" style="color:blue; padding-left:10px; padding-right:5px;" data-toggle="modal" data-target="#setDiscs" ng-click="fetchDiscounts(d)"><i class="fas fa-cogs"></i></a>                                                
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

    <div class="modal fade" id="newVendorDeal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-md" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-file-upload"></i> Upload Vendor's Deal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data" ng-submit="uploadDeal($event)" name="uploadVendorDealsForm" class="needs-validation">
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="customSwitch1" ng-model="switch" ng-change="toggleSwitch()">
                                <label class="custom-control-label" for="customSwitch1" ng-bind="label" style="user-select: none"></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" ng-init="getSuppliers()">
                                    <label for="supplierSelect"><i class="fab fa-slack required-icon"></i> Supplier Name: </label>
                                    <select ng-change="getPurchaseOrder()" class="form-control rounded-0" ng-model="supplierSelect" name="supplierSelect" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="s in suppliers" value="{{s.supplier_id}}">{{s.supplier_name}}</option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please choose a supplier.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="vendorsDeal"><i class="fab fa-slack required-icon"></i> Vendor's Deal: </label>
                                    <input type="file" name="vendorsDeal" id="vendorsDeal" class="form-control rounded-0" style="height: 45px" required multiple>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="uploadVendorDealsForm.$invalid"><i class="fas fa-upload"></i> Upload</button>
                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateVendorDeal" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-md" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-file-upload"></i> Update Vendor's Deal</h5>
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
                                        <option ng-repeat="s in suppliers" value="{{s.supplier_id}}">{{s.supplier_name}}</option>
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
                                    <label for="#"><i class="fab fa-slack required-icon"></i> Vendor's Deal: </label>
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

     <!-- MANUAL SETUP FOR NO DEALS SUPPLIER -->
    <div class="modal fade" id="setupNoDeal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl" role="document">
            <div class="modal-content rounded-0 modal-l">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i>Manual Setup</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="manual" name="manual" >
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="selectSupplier" ng-init="getSuppliers()"><i class="fab fa-slack required-icon"></i> Supplier Name: </label>
                                    <select name="selectSupplier" ng-model="selectSupplier" class="form-control rounded-0">
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="supplier in suppliers" value="{{supplier.supplier_id}}">{{supplier.supplier_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mFrom"><i class="fab fa-slack required-icon"></i> Period From: </label>
                                    <input type="text" class="form-control rounded-0" ng-model="mFrom" name="mFrom" required autocomplete="off">    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mTo"><i class="fab fa-slack required-icon"></i> Period To: </label>
                                    <input type="text" class="form-control rounded-0" ng-model="mTo" name="mTo" required autocomplete="off">    
                                </div>
                            </div>
                            <table class="table table-bordered table-sm" ng-init="deductions=[{}]">
                                <thead class="bg-dark">
                                    <tr>
                                        <th scope="col" style="width:15%" class="text-center">Item Dep't Code</th>
                                        <th scope="col" style="width:30%" class="text-center">Description</th>
                                        <th scope="col" style="width:10%" class="text-center">Discount 1</th>
                                        <th scope="col" style="width:10%" class="text-center">Discount 2</th>
                                        <th scope="col" style="width:10%" class="text-center">Discount 3</th>
                                        <th scope="col" style="width:10%" class="text-center">Discount 4</th>
                                        <th scope="col" style="width:10%" class="text-center">Discount 5</th>
                                        <th scope="col" style="width:5%" class="text-center"><i class="fas fa-bars"></i</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="data in deductions" ng-cloak>
                                        <td ng-if="$index > 0">
                                            <div class="input-group input-group-sm rounded-0 ">
                                                <input type="text" class="form-control rounded-0 text-center text-bold" value="{{data.itemcode}}" style="border: none;" readonly>
                                            </div>
                                        </td>
                                        <td ng-if="$index > 0">
                                            <div class="input-group input-group-sm rounded-0 ">
                                                <input type="text" class="form-control rounded-0 text-center text-bold" value="{{data.desc}}" style="border: none;" readonly>
                                            </div>
                                        </td>
                                        <td ng-if="$index > 0">
                                            <div class="input-group input-group-sm rounded-0 ">
                                                <input type="text" class="form-control rounded-0 text-center text-bold" value="{{data.disc1}}" style="border: none;" readonly>
                                            </div>
                                        </td>
                                        <td ng-if="$index > 0">
                                            <div class="input-group input-group-sm rounded-0 ">
                                                <input type="text" class="form-control rounded-0 text-center text-bold" value="{{data.disc2}}" style="border: none;" readonly>
                                            </div>
                                        </td>
                                        <td ng-if="$index > 0">
                                            <div class="input-group input-group-sm rounded-0 ">
                                                <input type="text" class="form-control rounded-0 text-center text-bold" value="{{data.disc3}}" style="border: none;" readonly>
                                            </div>
                                        </td>
                                        <td ng-if="$index > 0">
                                            <div class="input-group input-group-sm rounded-0 ">
                                                <input type="text" class="form-control rounded-0 text-center text-bold" value="{{data.disc4}}" style="border: none;" readonly>
                                            </div>
                                        </td>
                                        <td ng-if="$index > 0">
                                            <div class="input-group input-group-sm rounded-0 ">
                                                <input type="text" class="form-control rounded-0 text-center text-bold" value="{{data.disc5}}" style="border: none;" readonly>
                                            </div>
                                        </td>
                                        <td ng-if="$index > 0">                                           
                                            <div class="col-lg-12">
                                                <div class="input-group input-group-sm rounded-0">
                                                    <a href="#" style="color:red; padding-right: 10px; padding-left: 10px;" ng-click="deductions.splice($index, 1)">
                                                        <i class="fa fa-minus"></i>
                                                    </a>                                                   
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>  
                            <div class="col-md-12"> 
                                <div class="row">
                                    <div class="col-md-10"></div>
                                    <div class="col-md-2">
                                        <button 
                                            type="button" 
                                            class="btn btn-danger btn-flat float-right" 
                                            title="Add New Item Discount" 
                                            data-toggle="modal" 
                                            href="#itemDiscount" 
                                            ng-disabled="!selectSupplier"
                                            ng-click="loadSupplierItemDeptCode(selectSupplier)">
                                            <i class="fas fa-plus-circle"></i>
                                            Item Discount
                                        </button>
                                    </div>
                                </div> 
                            </div>                        
                        </div>
                        <div class="modal-footer">
                            <button 
                                type="button" 
                                class="btn bg-gradient-primary btn-flat" 
                                ng-click="submitManualSetup($event)"
                                ng-disabled="manual.mFrom.$invalid || manual.mTo.$invalid">
                                <i class="fas fa-save"></i> 
                                Save
                            </button>
                            <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- MANUAL SETUP FOR NO DEALS SUPPLIER -->

    <div class="modal fade" id="viewDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-info-circle"></i> Vendor Deals Detail </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body modal-xl">
                    <form action="">
                            <table id="vdlinetable" class="table table-bordered table-sm table-hover">
                                <thead class="bg-dark">
                                    <tr>
                                        <th scope="col" class="text-center">#</th>
                                        <th scope="col" class="text-center">Type</th>
                                        <th scope="col" class="text-center">Code</th>
                                        <th scope="col" class="text-center">Description</th>
                                        <th scope="col" class="text-center">Disc 1</th>
                                        <th scope="col" class="text-center">Disc 2</th>
                                        <th scope="col" class="text-center">Disc 3</th>
                                        <th scope="col" class="text-center">Disc 4</th>
                                        <th scope="col" class="text-center">Disc 5</th>
                                        <th scope="col" class="text-center">Disc 6</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="v in vdetails">
                                        <td class="text-center">{{$index + 1}}</td>
                                        <td class="text-center">{{v.type}}</td>   
                                        <td class="text-center">{{v.number}}</td>   
                                        <td class="text-center">{{v.description}}</td>   
                                        <td class="text-center">{{v.disc_1}}</td>   
                                        <td class="text-center">{{v.disc_2}}</td>                                        
                                        <td class="text-center">{{v.disc_3}}</td>
                                        <td class="text-center">{{v.disc_4}}</td>
                                        <td class="text-center">{{v.disc_5}}</td>
                                        <td class="text-center">{{v.disc_6}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary btn-flat" data-toggle="modal" data-target="#addLine"> Add Line</button>
                            <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal" ng-click="closeViewPi()"> Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addLine">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-file-invoice"></i> New Invoice </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <div class="container"></div>
                <div class="modal-body">
                    <form id="" name="addlineform" ng-submit="addNewLine($event)">  
                        <div class="col-md-12">
                            <div class="row">                          
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="addcode">Code :</label>
                                        <input type="text" name="addcode" ng-model="addcode" value="" class="form-control rounded-0" autocomplete="off" ng-required="true">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="addtype">Type :</label>
                                        <select name="addtype" ng-model="addtype"  class="form-control rounded-0" ng-required="true">
                                            <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                            <option value="Item">Item </option>
                                            <option value="Item Department">Item Department </option>
                                            <option value="Item Group">Item Group</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="adddesc">Description :</label>
                                <input type="text" name="adddesc" ng-model="adddesc" value="" class="form-control rounded-0" autocomplete="off" ng-required="true">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="adddisc1">Disc 1 :</label>
                                        <input type="number" id="adddisc1" name="adddisc1" ng-model="adddisc1" value="" class="form-control rounded-0" step="0.01" autocomplete="off" ng-required="true">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="adddisc2">Disc 2 :</label>
                                        <input type="number" id="adddisc2" name="adddisc2" ng-model="adddisc2" value="" class="form-control rounded-0" autocomplete="off" ng-required="true">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="adddisc3">Disc 3 :</label>
                                        <input type="number" id="adddisc3" name="adddisc3" ng-model="adddisc3" value="" step="any" class="form-control rounded-0" autocomplete="off" ng-required="true">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="adddisc4">Disc 4 :</label>
                                        <input type="number" id="adddisc4" name="adddisc4" ng-model="adddisc4" value="" class="form-control rounded-0" autocomplete="off" ng-required="true">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="adddisc5">Disc 5 :</label>
                                        <input type="number" id="adddisc5" name="adddisc5" ng-model="adddisc5" value="" class="form-control rounded-0" autocomplete="off" ng-required="true">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="adddisc6">Disc 6 :</label>
                                        <input type="number" id="adddisc6" name="adddisc6" ng-model="adddisc6" value="" class="form-control rounded-0" autocomplete="off" ng-required="true">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">                            
                            <button type="submit" class="btn btn-success btn-flat" ng-disabled="addlineform.$invalid">Add</button>
                            <button type="button" class="btn btn-dark btn-flat" ng-click="resetNewInvoice()" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="setDiscs">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-function"></i> Discounts to Use in Formula </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="container"></div>
                <div class="modal-body">
                    <form id="" name="discountsForm" ng-submit="setDiscounttoUse($event)">    
                        <fieldset class="border p-2">
                            <legend  class="float-none w-auto p-2">SOP</legend>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="sopgross" class="col-sm-4 col-form-label">Back To Gross</label>
                                    <div class="col-sm-8" >
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="sopdisc_1" name="sopdisc_1" ng-model="sopdisc_1" ng-value="1">
                                            <label class="form-check-label" for="sopdisc_1">Disc 1</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="sopdisc_2" name="sopdisc_2" ng-model="sopdisc_2" ng-value="1">
                                            <label class="form-check-label" for="sopdisc_2">Disc 2</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="sopdisc_3" name="sopdisc_3" ng-model="sopdisc_3" ng-value="1">
                                            <label class="form-check-label" for="sopdisc_3">Disc 3</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="sopdisc_4" name="sopdisc_4" ng-model="sopdisc_4" ng-value="1">
                                            <label class="form-check-label" for="sopdisc_4">Disc 4</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="sopdisc_5" name="sopdisc_5" ng-model="sopdisc_5" ng-value="1">
                                            <label class="form-check-label" for="sopdisc_5">Disc 5</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="sopdisc_6" name="sopdisc_6" ng-model="sopdisc_6" ng-value="1">
                                            <label class="form-check-label" for="sopdisc_6">Disc 6</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="border p-2">
                            <legend  class="float-none w-auto p-2">Proforma vs CRF</legend>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="pvcrfgross" class="col-sm-4 col-form-label">Back To Gross</label>
                                    <div class="col-sm-8" >
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfdisc_1" name="pvcrfdisc_1" ng-model="pvcrfdisc_1" ng-value="1">
                                            <label class="form-check-label" for="pvcrfdisc_1">Disc 1</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfdisc_2" name="pvcrfdisc_2" ng-model="pvcrfdisc_2" ng-value="1">
                                            <label class="form-check-label" for="pvcrfdisc_2">Disc 2</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfdisc_3" name="pvcrfdisc_3" ng-model="pvcrfdisc_3" ng-value="1">
                                            <label class="form-check-label" for="pvcrfdisc_3">Disc 3</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfdisc_4" name="pvcrfdisc_4" ng-model="pvcrfdisc_4" ng-value="1">
                                            <label class="form-check-label" for="pvcrfdisc_4">Disc 4</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfdisc_5" name="pvcrfdisc_5" ng-model="pvcrfdisc_5" ng-value="1">
                                            <label class="form-check-label" for="pvcrfdisc_5">Disc 5</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfdisc_6" name="pvcrfdisc_6" ng-model="pvcrfdisc_6" ng-value="1">
                                            <label class="form-check-label" for="pvcrfdisc_6">Disc 6</label>
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="" class="col-sm-4 col-form-label">Net Price</label>
                                    <div class="col-sm-8" >
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfnetdisc_1" name="pvcrfnetdisc_1" ng-model="pvcrfnetdisc_1" ng-value="1">
                                            <label class="form-check-label" for="pvcrfnetdisc_1">Disc 1</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfnetdisc_2" name="pvcrfnetdisc_2" ng-model="pvcrfnetdisc_2" ng-value="1">
                                            <label class="form-check-label" for="pvcrfnetdisc_2">Disc 2</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfnetdisc_3" name="pvcrfnetdisc_3" ng-model="pvcrfnetdisc_3" ng-value="1">
                                            <label class="form-check-label" for="pvcrfnetdisc_3">Disc 3</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfnetdisc_4" name="pvcrfnetdisc_4" ng-model="pvcrfnetdisc_4" ng-value="1">
                                            <label class="form-check-label" for="pvcrfnetdisc_4">Disc 4</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfnetdisc_5" name="pvcrfnetdisc_5" ng-model="pvcrfnetdisc_5" ng-value="1">
                                            <label class="form-check-label" for="pvcrfnetdisc_5">Disc 5</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfnetdisc_6" name="pvcrfnetdisc_6" ng-model="pvcrfnetdisc_6" ng-value="1">
                                            <label class="form-check-label" for="pvcrfnetdisc_6">Disc 6</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="" class="col-sm-4 col-form-label">Discounted Price</label>
                                    <div class="col-sm-8" >
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfdiscounteddisc_1" name="pvcrfdiscounteddisc_1" ng-model="pvcrfdiscounteddisc_1" ng-value="1">
                                            <label class="form-check-label" for="pvcrfdiscounteddisc_1">Disc 1</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfdiscounteddisc_2" name="pvcrfdiscounteddisc_2" ng-model="pvcrfdiscounteddisc_2" ng-value="1">
                                            <label class="form-check-label" for="pvcrfdiscounteddisc_2">Disc 2</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfdiscounteddisc_3" name="pvcrfdiscounteddisc_3" ng-model="pvcrfdiscounteddisc_3" ng-value="1">
                                            <label class="form-check-label" for="pvcrfdiscounteddisc_3">Disc 3</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfdiscounteddisc_4" name="pvcrfdiscounteddisc_4" ng-model="pvcrfdiscounteddisc_4" ng-value="1">
                                            <label class="form-check-label" for="pvcrfdiscounteddisc_4">Disc 4</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfdiscounteddisc_5" name="pvcrfdiscounteddisc_5" ng-model="pvcrfdiscounteddisc_5" ng-value="1">
                                            <label class="form-check-label" for="pvcrfdiscounteddisc_5">Disc 5</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvcrfdiscounteddisc_6" name="pvcrfdiscounteddisc_6" ng-model="pvcrfdiscounteddisc_6" ng-value="1">
                                            <label class="form-check-label" for="pvcrfdiscounteddisc_6">Disc 6</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="border p-2">
                            <legend  class="float-none w-auto p-2">Proforma vs PI</legend>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="pvcrfgross" class="col-sm-4 col-form-label">Back To Gross</label>
                                    <div class="col-sm-8" >
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpigrossdisc_1" name="pvpigrossdisc_1" ng-model="pvpigrossdisc_1" ng-value="1">
                                            <label class="form-check-label" for="pvpigrossdisc_1">Disc 1</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpigrossdisc_2" name="pvpigrossdisc_2" ng-model="pvpigrossdisc_2" ng-value="1">
                                            <label class="form-check-label" for="pvpigrossdisc_2">Disc 2</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpigrossdisc_3" name="pvpigrossdisc_3" ng-model="pvpigrossdisc_3" ng-value="1">
                                            <label class="form-check-label" for="pvpigrossdisc_3">Disc 3</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpigrossdisc_4" name="pvpigrossdisc_4" ng-model="pvpigrossdisc_4" ng-value="1">
                                            <label class="form-check-label" for="pvpigrossdisc_4">Disc 4</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpigrossdisc_5" name="pvpigrossdisc_5" ng-model="pvpigrossdisc_5" ng-value="1">
                                            <label class="form-check-label" for="pvpigrossdisc_5">Disc 5</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpigrossdisc_6" name="pvpigrossdisc_6" ng-model="pvpigrossdisc_6" ng-value="1">
                                            <label class="form-check-label" for="pvpigrossdisc_6">Disc 6</label>
                                        </div>
                                    </div>
                                </div>
                            </div>                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="pvcrfgross" class="col-sm-4 col-form-label">Net Price</label>
                                    <div class="col-sm-8" >
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpinetdisc_1" name="pvpinetdisc_1" ng-model="pvpinetdisc_1" ng-value="1">
                                            <label class="form-check-label" for="pvpinetdisc_1">Disc 1</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpinetdisc_2" name="pvpinetdisc_2" ng-model="pvpinetdisc_2" ng-value="1">
                                            <label class="form-check-label" for="pvpinetdisc_2">Disc 2</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpinetdisc_3" name="pvpinetdisc_3" ng-model="pvpinetdisc_3" ng-value="1">
                                            <label class="form-check-label" for="pvpinetdisc_3">Disc 3</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpinetdisc_4" name="pvpinetdisc_4" ng-model="pvpinetdisc_4" ng-value="1">
                                            <label class="form-check-label" for="pvpinetdisc_4">Disc 4</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpinetdisc_5" name="pvpinetdisc_5" ng-model="pvpinetdisc_5" ng-value="1">
                                            <label class="form-check-label" for="pvpinetdisc_5">Disc 5</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpinetdisc_6" name="pvpinetdisc_6" ng-model="pvpinetdisc_6" ng-value="1">
                                            <label class="form-check-label" for="pvpinetdisc_6">Disc 6</label>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="pvcrfgross" class="col-sm-4 col-form-label">Discounted Price</label>
                                    <div class="col-sm-8" >
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpidiscounteddisc_1" name="pvpidiscounteddisc_1" ng-model="pvpidiscounteddisc_1" ng-value="1">
                                            <label class="form-check-label" for="pvpidiscounteddisc_1">Disc 1</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpidiscounteddisc_2" name="pvpidiscounteddisc_2" ng-model="pvpidiscounteddisc_2" ng-value="1">
                                            <label class="form-check-label" for="pvpidiscounteddisc_2">Disc 2</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpidiscounteddisc_3" name="pvpidiscounteddisc_3" ng-model="pvpidiscounteddisc_3" ng-value="1">
                                            <label class="form-check-label" for="pvpidiscounteddisc_3">Disc 3</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpidiscounteddisc_4" name="pvpidiscounteddisc_4" ng-model="pvpidiscounteddisc_4" ng-value="1">
                                            <label class="form-check-label" for="pvpidiscounteddisc_4">Disc 4</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpidiscounteddisc_5" name="pvpidiscounteddisc_5" ng-model="pvpidiscounteddisc_5" ng-value="1">
                                            <label class="form-check-label" for="pvpidiscounteddisc_5">Disc 5</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="pvpidiscounteddisc_6" name="pvpidiscounteddisc_6" ng-model="pvpidiscounteddisc_6" ng-value="1">
                                            <label class="form-check-label" for="pvpidiscounteddisc_6">Disc 6</label>
                                        </div>
                                    </div>             
                                
                                </div>
                            </div>
                        </fieldset>
                        <div class="modal-footer">                            
                            <button type="submit" class="btn btn-success btn-flat">Set</button>
                            <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal">Close</button>
                        </div>
                    </form>  
                </div>
            </div>
        </div>
    </div>

</div>