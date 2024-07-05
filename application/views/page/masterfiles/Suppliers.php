<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="supplier-controller">
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
                                            <div class="panel-body"><i class="fas fa-people-carry"></i> <strong>SUPPLIERS</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <button class="btn bg-gradient-primary btn-flat" data-target="#newSupplier" data-toggle="modal"><i class="fas fa-plus-circle"></i> Add Supplier Manually</button>
                                    <button class="btn bg-gradient-primary btn-flat" data-target="#uploadSupplier" data-toggle="modal"><i class="fas fa-file-upload"></i> Upload Supplier Info</button>
                                </div>
                            </div>
                            <div>
                                <table id="suppliersTable" class="table table-bordered table-sm table-hover" ng-init="suppliersTable()" style="font-size: 14px;">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" class="text-center">ID</th>
                                            <th scope="col" class="text-center">Supplier Code</th>
                                            <th scope="col" class="text-center">Business Unit Name</th>
                                            <th scope="col" style="width: 160px;" class="text-center">Acro Name</th>
                                            <th scope="col" class="text-center">Address</th>
                                            <th scope="col" class="text-center">Contact No</th>
                                            <th scope="col" style="width: 160px;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="s in suppliers" ng-cloak>
                                            <td class="text-center">{{ s.supplier_id}}</td>
                                            <td class="text-center">{{ s.supplier_code}}</td>
                                            <td class="text-center">{{ s.supplier_name }}</td>
                                            <td class="text-center">{{ s.acroname }}</td>
                                            <td class="text-center">
                                                <span ng-if="s.address != '' || s.address != null">{{ s.address }}</span>
                                                <span ng-if="s.address == '' || s.address == null">-</span>
                                            </td>
                                            <td class="text-center">
                                                <span ng-if="s.contact_no != '' || s.contact_no != null">{{ s.contact_no }}</span>
                                                <span ng-if="s.contact_no == '' || s.contact_no == null">-</span>
                                            </td>
                                            <td class="text-center">
                                                <button title="Edit" class="btn bg-gradient-info btn-flat btn-sm" data-toggle="modal" data-target="#updateSupplier" ng-click="fetchSupplierData(s)"><i class="fas fa-pen-square"></i> Edit
                                                </button>

                                                <button class="btn bg-gradient-danger btn-flat btn-sm" ng-click="deactivateSupplier(s)"><i class="fas fa-ban"></i> Deactivate</button>
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

    <!-- MODAL ADD SUPPLIER -->
    <div class="modal fade" id="#" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0 pt-reduced pb-reduced">
                    <h6 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> New Supplier</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" name="addSupplierForm" ng-submit="saveSupplier($event)" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="vendorsCode"><i class="fab fa-slack required-icon"></i> Vendors Code:</label>
                                    <input type="text" class="form-control rounded-0" id="vendorsCode" ng-model="vendorsCode" name="vendorsCode" required autocomplete="off">
                                    <!-- FOR ERRORS -->
                                    <div class="validation-Error">
                                        <span ng-show="addSupplierForm.vendorsCode.$dirty && addSupplierForm.vendorsCode.$error.required">
                                            <p class="error-display">This field is required.</p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="supplierName"><i class="fab fa-slack required-icon"></i> Supplier Name:</label>
                                    <input type="text" class="form-control rounded-0" id="supplierName" ng-model="supplierName" name="supplierName" required autocomplete="off">
                                    <!-- FOR ERRORS -->
                                    <div class="validation-Error">
                                        <span ng-show="addSupplierForm.supplierName.$dirty && addSupplierForm.supplierName.$error.required">
                                            <p class="error-display">This field is required.</p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <label for="supplierAcroname"><i class="fab fa-slack required-icon"></i> Acro Name: </label>
                                    <input type="text" class="form-control rounded-0" ng-model="supplierAcroname" name="supplierAcroname" required autocomplete="off">
                                    <!-- FOR ERRORS -->
                                    <div class="validation-Error">
                                        <span ng-show="addSupplierForm.supplierAcroname.$dirty && addSupplierForm.supplierAcroname.$error.required">
                                            <p class="error-display">This field is required.</p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <fieldset class="form-group border">
                                    <legend class="col-form-label col-sm-4 pt-0" id="PH"><strong>Proforma Header:</strong></legend>
                                    <div class="row">
                                        <label for="PHcolumnName" style="margin-left: 25px;">Column:</label>
                                        <label for="PHdataType" style="margin-left: 197px;">Data Type:</label>
                                        <div class="col-md-12" ng-init="proformaHeader = [{}];">
                                            <div ng-repeat="data in proformaHeader" class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group ml-3">
                                                        <input type="text" class="form-control rounded-0" ng-model="data.PHcolumnName" required autocomplete="off">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select ng-model="data.PHdataType" class="form-control rounded-0" required>
                                                            <option selected value="">--</option>
                                                            <option>Varchar</option>
                                                            <option>Text</option>
                                                            <option>Int</option>
                                                            <option>Float</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 ml-3">
                                                    <div class="row">
                                                        <div class="container">
                                                            <div class="row">
                                                                <button type="button" ng-if="$index == 0" class="btn btn-success btn-flat" ng-click="proformaHeader.push({})">
                                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                                </button>
                                                                <button class="btn btn-danger btn-flat" ng-if="$index > 0" ng-click="proformaHeader.splice($index, 1)">
                                                                    <i class="fa fa-minus" aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="col-md-6">
                                <fieldset class="form-group border">
                                    <legend class="col-form-label col-sm-4 pt-0" id="PL"><strong>Proforma Line:</strong></legend>
                                    <div class="row">
                                        <label for="PLcolumnName" style="margin-left: 25px;">Column:</label>
                                        <label for="PLdataType" style="margin-left: 211px;">Data Type:</label>
                                        <div class="col-md-12" ng-init="proformaLine = [{}];">
                                            <div ng-repeat="data in proformaLine" class="row">
                                                <div class="col-md-6 ml-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control rounded-0" placeholder="" ng-model="data.PLcolumnName" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select ng-model="data.PLdataType" class="form-control rounded-0" required>
                                                            <option selected value="">--</option>
                                                            <option>Varchar</option>
                                                            <option>Text</option>
                                                            <option>Int</option>
                                                            <option>Float</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-1 ml-3">
                                                    <div class="row">
                                                        <div class="container">
                                                            <div class="row">
                                                                <button type="button" ng-if="$index == 0" class="btn btn-success btn-flat" ng-click="proformaLine.push({})">
                                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                                </button>
                                                                <button class="btn btn-danger btn-flat" ng-if="$index > 0" ng-click="proformaLine.splice($index, 1)">
                                                                    <i class="fa fa-minus" aria-hidden="true"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="addSupplierForm.$invalid"><i class="fas fa-save"></i> Save</button>
                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.content -->

    <div class="modal fade" id="newSupplier" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h6 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> New Supplier</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" name="addSupplierForm" ng-submit="saveSupplier($event)" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendorsCode"><i class="fab fa-slack required-icon"></i> Vendors Code:</label>
                                    <input type="text" class="form-control rounded-0" id="vendorsCode" ng-model="vendorsCode" name="vendorsCode" required autocomplete="off">
                                    <!-- FOR ERRORS -->
                                    <div class="validation-Error">
                                        <span ng-show="addSupplierForm.vendorsCode.$dirty && addSupplierForm.vendorsCode.$error.required">
                                            <p class="error-display">This field is required.</p>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="supplierName"><i class="fab fa-slack required-icon"></i> Supplier Name:</label>
                                    <input type="text" class="form-control rounded-0" id="supplierName" ng-model="supplierName" name="supplierName" required autocomplete="off">
                                    <!-- FOR ERRORS -->
                                    <div class="validation-Error">
                                        <span ng-show="addSupplierForm.supplierName.$dirty && addSupplierForm.supplierName.$error.required">
                                            <p class="error-display">This field is required.</p>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="supplierAcroname"><i class="fab fa-slack required-icon"></i> Acroname: </label>
                                    <input type="text" class="form-control rounded-0" ng-model="supplierAcroname" name="supplierAcroname" required autocomplete="off">
                                    <!-- FOR ERRORS -->
                                    <div class="validation-Error">
                                        <span ng-show="addSupplierForm.supplierAcroname.$dirty && addSupplierForm.supplierAcroname.$error.required">
                                            <p class="error-display">This field is required.</p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supplierAddress"><i class="fab fa-slack required-icon"></i> Address: </label>
                                    <input type="text" class="form-control rounded-0" ng-model="supplierAddress" name="supplierAddress" required autocomplete="off">
                                    <!-- FOR ERRORS -->
                                    <div class="validation-Error">
                                        <span ng-show="addSupplierForm.supplierAddress.$dirty && addSupplierForm.supplierAddress.$error.required">
                                            <p class="error-display">This field is required.</p>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="supplierContact"> Contact No.: </label>
                                    <input type="text" class="form-control rounded-0" ng-model="supplierContact" name="supplierContact" autocomplete="off" pattern="[0-9]{4}-[0-9]{3}-[0-9]{4}" placeholder="0932-413-3930">
                                    <!-- FOR ERRORS -->
                                    <div class="validation-Error">
                                        <span ng-show="addSupplierForm.supplierContact.$dirty && addSupplierForm.supplierContact.$error.required">
                                            <p class="error-display">This field is required.</p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="addSupplierForm.$invalid"><i class="fas fa-save"></i> Save</button>
                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.content -->

    <!-- MODAL UPLOAD SUPPLIER -->
    <div class="modal fade" id="uploadSupplier" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-md" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0 pt-reduced pb-reduced">
                    <h6 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> New Supplier</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" name="uploadSupplierForm" ng-submit="uploadSupplier($event)" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="supplierFile"><i class="fab fa-slack required-icon"></i> Supplier Data File: </label>
                                    <input type="file" name="supplierFile[]" id="supplierFile" class="form-control rounded-0" style="height: 45px" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="supplierPricing"><i class="fab fa-slack required-icon"></i> Pricing Type</label>
                                    <select class="form-control rounded-0" ng-model="supplierPricing" name="supplierPricing" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option>GROSS</option>
                                        <option>NET</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="uploadSupplierForm.$invalid"><i class="fas fa-save"></i> Upload</button>
                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.content -->

    <!-- MODAL EDIT SUPPLIER -->
    <div class="modal fade" id="updateSupplier" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h6 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> Edit Info</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" name="updateSupplierForm" ng-submit="updateSupplierData($event)" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="vendorsCodeU"><i class="fab fa-slack required-icon"></i> Vendors Code:</label>
                                    <input type="text" class="form-control rounded-0" id="vendorsCodeU" ng-model="vendorsCodeU" name="vendorsCodeU" required autocomplete="off">
                                    <!-- FOR ERRORS -->
                                    <div class="validation-Error">
                                        <span ng-show="updateSupplierForm.vendorsCodeU.$dirty && updateSupplierForm.vendorsCodeU.$error.required">
                                            <p class="error-display">This field is required.</p>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="supplierNameU"><i class="fab fa-slack required-icon"></i> Supplier Name:</label>
                                    <input type="text" class="form-control rounded-0" id="supplierNameU" ng-model="supplierNameU" name="supplierNameU" required autocomplete="off">
                                    <!-- FOR ERRORS -->
                                    <div class="validation-Error">
                                        <span ng-show="updateSupplierForm.supplierNameU.$dirty && updateSupplierForm.supplierNameU.$error.required">
                                            <p class="error-display">This field is required.</p>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="supplierAcronameU"><i class="fab fa-slack required-icon"></i> Acroname: </label>
                                    <input type="text" class="form-control rounded-0" ng-model="supplierAcronameU" name="supplierAcronameU" required autocomplete="off">
                                    <!-- FOR ERRORS -->
                                    <div class="validation-Error">
                                        <span ng-show="updateSupplierForm.supplierAcronameU.$dirty && updateSupplierForm.supplierAcronameU.$error.required">
                                            <p class="error-display">This field is required.</p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supplierAddressU"><i class="fab fa-slack required-icon"></i> Address: </label>
                                    <input type="text" class="form-control rounded-0" ng-model="supplierAddressU" name="supplierAddressU" required autocomplete="off">
                                    <!-- FOR ERRORS -->
                                    <div class="validation-Error">
                                        <span ng-show="updateSupplierForm.supplierAddressU.$dirty && updateSupplierForm.supplierAddressU.$error.required">
                                            <p class="error-display">This field is required.</p>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="supplierContactU"> Contact No.: </label>
                                    <input type="text" class="form-control rounded-0" ng-model="supplierContactU" name="supplierContactU" autocomplete="off" pattern="[0-9]{4}-[0-9]{3}-[0-9]{4}" placeholder="0912-413-3930">
                                    <!-- FOR ERRORS -->
                                    <div class="validation-Error">
                                        <span ng-show="updateSupplierForm.supplierContactU.$dirty && updateSupplierForm.supplierContactU.$error.required">
                                            <p class="error-display">This field is required.</p>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="updateSupplierForm.$invalid"><i class="fas fa-pen-alt"></i> Update</button>
                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>