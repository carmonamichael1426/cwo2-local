<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="po-controller">
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
                                            <div class="panel-body"><i class="fas fa-file-alt"></i> <strong>PURCHASE ORDER</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php if ($this->session->userdata('userType') == 'Buyer-Purchaser' || $this->session->userdata('userType') == 'Admin') : ?>
                                    <div class="col-md-12 mb-4">
                                        <button class="btn bg-gradient-primary btn-flat" data-target="#addPOReport" data-toggle="modal"><i class="fas fa-file-upload"></i> Upload PO </button>
                                        <button class="btn bg-gradient-primary btn-flat" data-target="#createPo" data-toggle="modal"><i class="fas fa-plus-circle"></i> Create PO - Alta Citta </button>
                                    </div>                                    
                                <?php endif; ?>
                            </div>

                            <hr>

                            <div class="container" style="padding-left: 220px; padding-right: 220px;">
                                <!-- <form action="" method="post" name="generatePoForm" enctype="multipart/form-data" ng-submit="poTable($event)"> -->
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
                                            <button type="button" class="btn bg-gradient-primary btn-block btn-flat" ng-click="poTable($event)" ng-disabled="!supplierName || !locationName || !dateFrom || !dateTo" >GENERATE</button>
                                        </div>
                                    </div>
                                <!-- </form> -->
                            </div>

                            <div ng-if="poList">
                                <table id="poTable" class="table table-bordered table-sm table-hover">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" class="text-center">PO No - Reference</th>
                                            <th scope="col" class="text-center">PO Date</th>
                                            <th scope="col" class="text-center">Business Unit Matched</th>
                                            <th scope="col" class="text-center">Status</th>
                                            <th scope="col" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="p in po" ng-cloak>
                                            <td class="text-center">{{p.poNo}} - {{p.ref}}</td>
                                            <td class="text-center">{{p.orderDate | date:'mediumDate'}}</td>
                                            <td class="text-center">{{p.cusName}} vs. {{p.supName}}</td>
                                            <td class="text-center" ng-if="p.status=='PENDING' ">
                                                <span class="badge badge-warning">PENDING</span>
                                            </td> 
                                            <td class="text-center" ng-if="p.status=='MATCHED' ">
                                                <span class="badge badge-success">MATCHED</span>
                                            </td> 
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn bg-gradient-primary btn-flat btn-sm dropdown-toggle" data-toggle="dropdown">Action
                                                    </button>
                                                    <div class="dropdown-menu" style="margin-right: 30px;">
                                                        <a class="dropdown-item" title="View Item" href="#" data-toggle="modal" data-target="#viewDetails" ng-click="viewPoDetails(p)">
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

    <!-- MODAL UPLOAD PO -->
    <div class="modal fade" id="addPOReport" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-upload"></i> Upload New PO </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="uploadPoForm" ng-submit="uploadPo($event)" enctype="multipart/form-data">
                        <div class="row">
                            <div class="alert alert-warning" role="alert">
                                <h4 class="alert-heading">Reminder!</h4>
                                <p>Multiple PO uploading must be of the same supplier and customer/location.</p>                                
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="selectSupplier">Supplier Name: </label>
                                    <select ng-model="selectSupplier" name="selectSupplier" class="form-control rounded-0" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="supplier in suppliers" value="{{supplier.supplier_id}}">{{supplier.supplier_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="selectCustomer">Location Name: </label>
                                    <select ng-model="selectCustomer" name="selectCustomer" class="form-control rounded-0" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="customer in customers" value="{{customer.customer_code}}">{{customer.customer_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="pofile">Purchase Order: </label>
                                    <input type="file" class="form-control rounded-0" style="height:45px" id="pofile" ng-model="pofile" name="pofile[]" required multiple>
                                </div>
                            </div>                            
                        </div>
                        
                        <div class="modal-footer">
                            <button type="submit" class="btn bg-gradient-primary btn-flat"><i class="fas fa-upload"></i> Upload</button>
                            <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal" ng-click="closePoForm()">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL VIEW PO DETAILS  -->
    <div class="modal fade" id="viewDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-info-circle"></i> Purchase Order Details </h5>
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
                                        <th scope="col" class="text-center">PO No</th>
                                        <th scope="col" class="text-center">Reference</th>
                                        <th scope="col" class="text-center">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center">{{ poNo  }}</td>
                                        <td class="text-center">{{ poRef }}</td>
                                        <td class="text-center">{{poAmt | currency:'₱ '}}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <table id="viewPiLine" class="table table-bordered table-sm table-hover">
                                <thead class="bg-dark">
                                    <tr>
                                        <th scope="col" style="width: 100px" class="text-center">#</th>
                                        <th scope="col" style="width: 100px" class="text-center">Item Code</th>
                                        <th scope="col" class="text-center">Description</th>
                                        <th scope="col" class="text-center">Quantity</th>
                                        <th scope="col" class="text-center">UOM</th>
                                        <th scope="col" class="text-center">Direct Unit Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="d in poDetails">
                                        <td class="text-center">{{$index + 1}}</td>
                                        <td class="text-center">{{d.item_code}}</td>
                                        <td class="text-center">
                                            <span class="no-item" ng-if="!d.description">
                                                UNKNOWN ITEM
                                            </span>
                                            <span ng-if="d.description">
                                                {{d.description}}
                                            </span>
                                        </td>
                                        <td class="text-center">{{d.qty}}</td>
                                        <td class="text-center">{{d.uom}}</td>
                                        <td class="text-center">{{d.direct_unit_cost | currency:'₱ '}}</td>
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
    <!-- MODAL VIEW PI DETAILS -->
                            
    <!-- CREATE PO ALTA CITTA -->
    <div class="modal fade" id="createPo" aria-hidden="true" role="dialog"  data-backdrop="static" data-keyboard="false" aria-labelledby="createPoLabel" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="createProformaModalLabel">Create Purchase Order for Alta Citta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- <h5>PURCHASE ORDER</h5> -->
                    <form action="" method="POST" id="createPoForm" ng-submit="createPo($event)"  enctype="multipart/form-data">
                        <div class="row">                       
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="createpo_sup"><i class="fab fa-slack required-icon"></i> Supplier Name : </label>
                                    <select ng-model="createpo_sup" name="createpo_sup" ng-model="createpo_sup" class="form-control rounded-0" ng-change="loadItems()" ng-required="true">
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="supplier in suppliers" value="{{supplier.supplier_id}}">{{supplier.supplier_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="create_selectCustomer"> Location Name : </label>
                                    <input type="text" class="form-control border-0 rounded-0" name="loc_altacitta" value="ALTA CITTA" readonly>
                                </div>
                            </div>                            
                        </div>
                        <div class="row" ng-show="itemshasloaded">
                            <div class="col-md-12">                   
                                <table id="tblsupitems" class="table table-bordered table-sm table-hover">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" style="width:10%" class="text-center">#</th>
                                            <th scope="col" style="width:20%" class="text-center">Item Code</th>
                                            <th scope="col" style="width:60%" class="text-center">Description</th>
                                            <th scope="col" style="width:10%" class="text-center">Select</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="i in supItems">
                                            <td class="text-center">{{$index + 1}}</td>
                                            <td class="text-center">{{i.itemcode_loc}}</td>
                                            <td class="text-center">{{i.description}}</td>
                                            <td class="text-center">
                                                <div class="">
                                                    <input  
                                                        type="checkbox" 
                                                        title=""   
                                                        style="width:38px; height:25px"
                                                        ng-change="countCheckedItems($event);"
                                                        ng-model="i.selected">                                     
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>          
                            </div>
                        </div>
                        <div class="row mb-3" ng-show="itemshasloaded">
                            <div class="col-md-8 mt-3"></div>
                            <div class="col-md-4 mt-3">
                                <button type="button" id="btnadditems" class="btn btn-danger btn-flat float-right" title="Add Items"href="#" ng-disabled="countSelected ==0" ng-click="addToPotable($event)">
                                    <i class="fas fa-plus-circle"></i>
                                    Add Items
                                </button>
                            </div>                      
                        </div>
                        <div class="row">   
                            <div class="col-md-5">
                                <label for="createpo_po"><i class="fab fa-slack required-icon"></i> Purchase Order : </label>
                                <input type="text" class="form-control rounded-0" ng-model="createpo_po" name="createpo_po" ng-disabled="!createpo_sup" placeholder="PO Number" autocomplete="off" ng-required="true"> 
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="createpo_ref"><i class="fab fa-slack required-icon"></i> Reference No.: </label>
                                    <input type="text" class="form-control rounded-0" ng-model="createpo_ref"  ng-disabled="!createpo_po" placeholder="PO Reference" name="createpo_ref" autocomplete="off" ng-required="true">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="createpo_date"><i class="fab fa-slack required-icon"></i> Date : </label>
                                    <input type="text" class="form-control rounded-0" id="createpo_date" ng-model="createpo_date"  ng-disabled="!createpo_po" placeholder="PO Date" name="createpo_date" autocomplete="off" ng-required="true">
                                </div>
                            </div>
                           
                            <div class="col-md-12"  ng-init="poItems = [{}];">   
                                <div class="alert alert-danger" role="alert">
                                    Items with zero quantity or unit cost will not be saved.
                                </div>                
                                <table id="tblpoitems" class="table table-bordered table-sm table-hover">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" style="width:5%"  class="text-center">#</th>
                                            <th scope="col" style="width:10%" class="text-center">Item Code</th>
                                            <th scope="col" style="width:38%" class="text-center">Description</th>
                                            <th scope="col" style="width:10%" class="text-center">UOM</th>
                                            <th scope="col" style="width:10%" class="text-center">Quantity</th>                                            
                                            <th scope="col" style="width:10%" class="text-center">Unit Cost</th>
                                            <th scope="col" style="width:12%" class="text-center">Amount</th>
                                            <th scope="col" style="width:5%"  class="text-center"><i class="fas fa-bars"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="p in poItems">
                                            <td ng-if="countPoLines > 0" class="text-center">{{$index + 1}}</td>
                                            <td ng-if="countPoLines > 0" class="text-center">{{p.item_code}}</td>
                                            <td ng-if="countPoLines > 0" class="text-center">{{p.description}}</td>
                                            <td ng-if="countPoLines > 0" class="text-center">
                                                <div class="input-group input-group-sm rounded-0 ">
                                                    <input type="text" class="form-control rounded-0 text-center text-bold" ng-model="p.uom" style="border: none; "  ng-required="true">
                                                </div> 
                                            </td>
                                            <td ng-if="countPoLines > 0" class="text-center">
                                                <div class="input-group input-group-sm rounded-0 ">
                                                    <input type="text" class="form-control rounded-0 text-center text-bold" ng-model="p.qty" style="border: none; " ng-change="calculatePo()" ng-required="true">
                                                </div> 
                                            </td>                                            
                                            <td ng-if="countPoLines > 0" class="text-center">
                                                <div class="input-group input-group-sm rounded-0 ">
                                                    <input type="text" class="form-control rounded-0 text-center text-bold" ng-model="p.cost" style="border: none; " ng-change="calculatePo()"  ng-required="true">
                                                </div>
                                            </td>
                                            <td ng-if="countPoLines > 0" class="text-center">
                                                <div class="input-group input-group-sm rounded-0 ">
                                                    <input type="text" class="form-control rounded-0 text-center text-bold" ng-model="p.amount" ng-value="p.qty * p.cost" style="border: none; " readonly>
                                                </div> 
                                            </td>
                                            <td ng-if="countPoLines > 0" class="text-center">
                                                <div class=""> 
                                                    <a href="#" style="color:red;" title="Remove" ng-click="poItems.splice($index, 1);calculatePo();">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>  
                                                <div>    
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot ng-show="totalPoAmount_c > 0 && totalPoQty_c> 0">
                                        <td colspan="4"></td>
                                        <td class="text-center text-bold">{{totalPoQty_c}}</td>
                                        <td></td>
                                        <td colspan="2" class="text-right text-bold">{{totalPoAmount_c | currency : '₱ '}}</td>
                                    </tfoot>
                                </table>          
                            </div>              
                        </div>                      
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success btn-flat" ng-disabled="totalPoAmount_c == 0 || totalPoQty_c == 0">Save</button>
                            <button type="button" class="btn btn-dark btn-flat" data-dismiss="modal"> Close</button>
                        </div> 
                    </form>                                               
                </div>      
            </div>            
        </div>
    </div>
    <!-- CREATE PO ALTA CITTA -->

</div>
<!-- /.content-wrapper -->