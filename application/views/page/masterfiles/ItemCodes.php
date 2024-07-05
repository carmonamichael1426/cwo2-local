<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="itemcode-controller">
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
                                            <div class="panel-body"><i class="fas fa-box"></i> <strong>ITEM CODES</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <!-- <button class="btn btn-success btn-flat mr-2 float-right" data-target="#addItemCode" data-toggle="modal"><i class="fas fa-plus-circle"></i> Add Item Manually</button> -->
                                <button class="btn bg-gradient-primary btn-flat mr-2" data-target="#supplierOnly" data-toggle="modal"><i class="fas fa-upload"></i> Upload Location Item Codes</button>
                                <!-- <button class="btn bg-gradient-primary btn-flat mr-2" data-target="#updateItems" data-toggle="modal"><i class="fas fa-pen-square"></i> Update Item Codes</button> -->
                                <button class="btn bg-gradient-primary btn-flat mr-2" data-target="#multipleMapping" data-toggle="modal"><i class="fas fa-map-signs"></i> Multiple Mapping (Manual)</button>
                                <button class="btn bg-gradient-primary btn-flat mr-2" data-target="#multipleMappingUpload" data-toggle="modal"><i class="fas fa-map-signs"></i> Multiple Mapping (Upload)</button>
                            </div>

                            <hr>

                            <div class="container" style="padding-left: 220px; padding-right: 220px;">
                                <div class="row col-lg-12">
                                    <div class="col-lg-6">
                                        <div class="form-group" ng-init="getSuppliers()">
                                            <label for="supplierName">Supplier Name</label>
                                            <select class="form-control rounded-0" ng-model="supplierName" name="supplierName" required>
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

                                <div class="row mb-3 col-lg-8">
                                    <div class="col-lg-6"></div>
                                    <div class="col-lg-6">
                                        <button type="button" class="btn bg-gradient-primary btn-block btn-flat" ng-click="generateItems()" ng-disabled="!supplierName && !locationName">GET ITEMS</button>
                                    </div>
                                </div>
                            </div>

                            <div ng-if="itemsTableToggle">
                                <table id="itemsTable" class="table table-bordered table-sm table-hover">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" class="text-center">ID</th>
                                            <th scope="col" class="text-center">Supplier Item Code</th>
                                            <th scope="col" class="text-center">Location Item Code</th>
                                            <th scope="col" class="text-center">Location Item Description</th>
                                            <th scope="col" style="width: 160px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="i in items" ng-cloak>
                                            <td class="text-center">{{ i.id }}</td>
                                            <td class="text-center">
                                                <span ng-if="i.itemcode_sup != 'NO SET UP' && i.itemcode_sup != null">{{ i.itemcode_sup }}</span>
                                                <span ng-if="i.itemcode_sup == null || i.itemcode_sup == 'NO SET UP'" style="color: red;">{{ i.itemcode_sup }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span ng-if="i.itemcode_loc != null || i.itemcode_loc != 'NO SET UP'">{{ i.itemcode_loc }}</span>
                                                <span ng-if="i.itemcode_loc == null || i.itemcode_loc == 'NO SET UP'">-</span>
                                            </td>
                                            <td class="text-center">{{ i.description }}</td>
                                            <td class="text-center">
                                                <button class="btn bg-gradient-info btn-flat btn-sm" data-toggle="modal" data-target="#updateItemCode" ng-click="editItem(i)">
                                                    <i class="fas fa-pen-square"></i> Map Item
                                                </button>
                                                <button class="btn bg-gradient-danger btn-flat btn-sm" ng-click="deleteItem(i)">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
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

    <!-- MODAL MULTIPLE MAPPING MANUAL ITEM CODE -->
    <div class="modal fade" id="multipleMapping" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-map-signs"></i> Multiple Items Mapping</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form name="mapItemForm" method="post" enctype="multipart/form-data" ng-submit="mapItemCodes($event)">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" ng-init="getSuppliers()">
                                    <label for="supplierSelectMapping"><i class="fab fa-slack required-icon"></i> Supplier Name</label>
                                    <select class="form-control rounded-0" ng-change="getMappingItems()" ng-model="supplierSelectMapping" name="supplierSelectMapping" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="s in suppliers" value="{{s.supplier_id}}">{{s.supplier_name}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group" ng-init="getCustomers()">
                                    <label for="locationSelectMapping"><i class="fab fa-slack required-icon"></i> Location Name </label>
                                    <select class="form-control rounded-0" ng-change="getMappingItems()" ng-model="locationSelectMapping" name="locationSelectMapping" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="c in customers" value="{{c.customer_code}}">{{c.customer_name}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row scroll-able">
                            <div class="col-md-12" ng-init="itemCodeMappingData = [{}];">
                                <label for="description" style="margin-left: 10px;"><i class="fab fa-slack required-icon"></i> Location Item Description:</label>
                                <label for="locationItemCode" style="margin-left: 250px;">Location Item Code:</label>
                                <label for="supplierItemCode" style="margin-left: 130px;"><i class="fab fa-slack required-icon"></i> Supplier Item Code:</label>
                                <div ng-repeat="data in itemCodeMappingData" class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <!-- <select class="form-control rounded-0" ng-model="data.locationItemCode" name="itemDescription" required>
                                                <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                                <option ng-if="itemsMap == ''" value="" disabled="" selected="" style="display:none">NO DATA FOUND</option>
                                                <option ng-repeat="i in itemsMap" value="{{i.itemcode_loc}}">{{i.description}}</option>
                                            </select> -->
                                            <input type="text" class="form-control rounded-0 currency" ng-model="data.locationItemCode" required autocomplete="off" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <!-- <input type="text" class="form-control rounded-0 currency" ng-model="data.locationItemCode" required autocomplete="off"> -->
                                            <select class="form-control rounded-0 currency" ng-model="data.locationItemCode" name="itemDescription" required>
                                                <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                                <option ng-if="itemsMap == ''" value="" disabled="" selected="" style="display:none">NO DATA FOUND</option>
                                                <option ng-repeat="i in itemsMap" value="{{i.description}}">{{i.itemcode_loc}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="text" class="form-control rounded-0 currency" ng-model="data.supplierItemCode" required autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="row">
                                            <div class="container">
                                                <div class="row">
                                                    <button type="button" ng-if="$index == 0" class="btn btn-default btn-flat" ng-click="itemCodeMappingData.push({})" ng-disabled="itemsMap == ''">
                                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-flat" ng-if="$index > 0" ng-click="itemCodeMappingData.splice($index, 1)">
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
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="mapItemForm.$invalid"><i class="fas fa-save"></i> Map Items</button>
                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL MULTIPLE MAPPING UPLOAD ITEM CODE -->
    <div class="modal fade" id="multipleMappingUpload" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-upload"></i> Multiple Items Mapping</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form name="mapItemsUpload" method="post" enctype="multipart/form-data" ng-submit="uploadMapping($event)">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" ng-init="getSuppliers()">
                                    <label for="supplierUploadMapping"><i class="fab fa-slack required-icon"></i> Supplier Name</label>
                                    <select class="form-control rounded-0" ng-model="supplierUploadMapping" name="supplierUploadMapping" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="s in suppliers" value="{{s.supplier_id}}">{{s.supplier_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group" ng-init="getCustomers()">
                                    <label for="locationUploadMapping"><i class="fab fa-slack required-icon"></i> Location Name </label>
                                    <select class="form-control rounded-0" ng-model="locationUploadMapping" name="locationUploadMapping" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="c in customers" value="{{c.customer_code}}">{{c.customer_name}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="itemCodesMapping"><i class="fab fa-slack required-icon"></i> Item Code/s</label>
                            <input type="file" class="form-control rounded-0" name="itemCodesMapping" ng-model="itemCodesMapping" style="height: 50px">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="mapItemsUpload.$invalid"><i class="fas fa-upload"></i> Upload</button>
                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL UPLOAD ITEM CODE -->
    <div class="modal fade" id="supplierOnly" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-upload"></i> Upload Item Code</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form name="uploadItemsForm" method="post" enctype="multipart/form-data" ng-submit="uploadItems($event)">
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
                                    <label for="supplierNameUpload"><i class="fab fa-slack required-icon"></i> Supplier Name</label>
                                    <select class="form-control rounded-0" ng-model="supplierNameUpload" name="supplierNameUpload" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="s in suppliers" value="{{s.supplier_id}}">{{s.supplier_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group" ng-init="getCustomers()">
                                    <label for="locationNameUpload"><i class="fab fa-slack required-icon"></i> Location Name </label>
                                    <select class="form-control rounded-0" ng-model="locationNameUpload" name="locationNameUpload" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="c in customers" value="{{c.customer_code}}">{{c.customer_name}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="itemCodes"><i class="fab fa-slack required-icon"></i> Item Code/s</label>
                            <input type="file" class="form-control rounded-0" name="itemCodes" ng-model="itemCodes" style="height: 50px">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="uploadItemsForm.$invalid"><i class="fas fa-upload"></i> Upload</button>
                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL UPDATE ITEM CODE -->
    <div class="modal fade" id="updateItems" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-pen-square"></i> Update Item Codes</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form name="updateItemsForm" method="post" enctype="multipart/form-data" ng-submit="uploadUpdate($event)">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" ng-init="getSuppliers()">
                                    <label for="supplierNameUpdate"><i class="fab fa-slack required-icon"></i> Supplier Name</label>
                                    <select class="form-control rounded-0" ng-model="supplierNameUpdate" name="supplierNameUpdate" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="s in suppliers" value="{{s.supplier_id}}">{{s.supplier_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group" ng-init="getCustomers()">
                                    <label for="locationSelectUpdate"><i class="fab fa-slack required-icon"></i> Location Name </label>
                                    <select class="form-control rounded-0" ng-model="locationSelectUpdate" name="locationSelectUpdate" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="c in customers" value="{{c.customer_code}}">{{c.customer_name}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="itemCodesUpdated"><i class="fab fa-slack required-icon"></i> Item Code/s</label>
                            <input type="file" class="form-control rounded-0" id="itemCodesUpdated" name="itemCodesUpdated" ng-model="itemCodesUpdated" style="height: 50px">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="updateItemsForm.$invalid"><i class="fas fa-pen-square"></i> Update</button>
                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL MAP ITEM CODE -->
    <div class="modal fade" id="updateItemCode" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> Item Code Mapping to Supplier Item Code</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form name="editItemForm" method="post" enctype="multipart/form-data" ng-submit="updateItem($event)">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="itemcode_location_edit"><i class="fab fa-slack required-icon"></i> Location Item Code</label>
                                    <input type="text" class="form-control rounded-0 currency" ng-model="itemcode_location_edit" name="itemcode_location_edit" required readonly>
                                    <!-- FOR ERRORS -->
                                    <div class="validation-Error">
                                        <span ng-show="editItemForm.itemcode_location_edit.$dirty && editItemForm.itemcode_location_edit.$error.required">
                                            <p class="error-display">This field is required.</p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="itemcode_supplier_edit"><i class="fab fa-slack required-icon"></i> Suppliers Item Code</label>
                                    <input type="text" class="form-control rounded-0 currency" ng-model="itemcode_supplier_edit" name="itemcode_supplier_edit" required>
                                    <!-- FOR ERRORS -->
                                    <div class="validation-Error">
                                        <span ng-show="editItemForm.itemcode_supplier_edit.$dirty && editItemForm.itemcode_supplier_edit.$error.required">
                                            <p class="error-display">This field is required.</p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="editItemForm.$invalid"><i class="fas fa-pen-square"></i> Map Item</button>
                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /.content-wrapper -->