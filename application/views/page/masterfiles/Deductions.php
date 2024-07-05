<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="deduction-controller">
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card card-style-1">
                        <div class="card-header bg-dark rounded-0">
                            <div class="content-header bg-dark" style="padding: 0px">
                                <div class="panel panel-default">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="panel-body"> <strong> DEDUCTIONS </strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($this->session->userdata('userType') == 'Admin' ) : ?>
                            <div class="row mb-3">   
                                <button 
                                    class="btn bg-gradient-primary btn-flat mr-2" 
                                    data-target="#newType" 
                                    data-toggle="modal">
                                    <i class="fas fa-plus-circle"></i>
                                    Type
                                </button>
                                <button 
                                    class="btn bg-gradient-primary btn-flat mr-2" 
                                    data-target="#newDeduction" 
                                    data-toggle="modal">
                                    <i class="fas fa-plus-circle"></i>
                                    Deduction 
                                </button>                               
                            </div>  
                            <?php endif; ?>                         

                            <div>
                                <table id="deductionsTable" class="table table-bordered table-sm table-hover" ng-init="deductionsTable()">
                                    <thead class="bg-dark">
                                        <th scope="col" class="text-center">#</th>
                                        <th scope="col" class="text-center">SUPPLIER</th>
                                        <th scope="col" class="text-center">Deduction Type</th>
                                        <th scope="col" class="text-center">Deduction Name</th>
                                        <th scope="col" class="text-center">Name Used for Display</th>
                                        <th scope="col" class="text-center">Formula</th>
                                        <th scope="col" style="width: 160px;" class="text-center">Action</th>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="d in deductions" ng-cloak>
                                            <td class="text-center">{{ $index + 1}}</td>
                                            <td class="text-center">{{ d.supplier }}</td>
                                            <td class="text-center">{{ d.type }}</td>
                                            <td class="text-center">{{ d.name }}</td>
                                            <td class="text-center">{{ d.name_used_for_display }}</td>
                                            <td class="text-center">{{ d.formula }}</td>
                                            <td class="text-center">
                                                <button ng-disabled="d.statuss == 0" class="btn bg-gradient-info btn-flat btn-sm" data-toggle="modal" data-target="#updateDeduction" ng-click="fetchDeductionData(d)"><i class="fas fa-pen-square"></i> Edit
                                                </button>
                                                <button ng-disabled="d.statuss == 0" class="btn bg-gradient-danger btn-flat btn-sm" ng-click="deactivateDeduction(d)"><i class="fas fa-times"></i> Deactivate
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

    <!-- MODAL ADD TYPE -->
    <div class="modal fade" id="newType" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog" role="document">
            <div class="modal-content rounded-0 modal-md">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> New Deduction Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" name="addType" ng-submit="saveType($event)" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="type" class="col-sm-5 col-form-label"><i class="fab fa-slack required-icon"></i> Type : </label>
                                <input type="text" class="form-control rounded-0 text-uppercase" ng-model="type" name="type" required autocomplete="off">                                
                            </div>                            
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="addType.$invalid"><i class="fas fa-save"></i> Save</button>
                            <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ADD TYPE -->

    <!-- MODAL ADD DEDUCTION -->
    <div class="modal fade" id="newDeduction" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog" role="document">
            <div class="modal-content rounded-0 modal-md">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> New Deduction</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" name="addDeduction" ng-submit="saveDeduction($event)" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="form-group" ng-init="getSuppliers()">
                                <label for="selectSupplier">Supplier Name: </label>
                                <select name="selectSupplier" ng-model="selectSupplier" class="form-control rounded-0">
                                    <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                    <option ng-repeat="supplier in suppliers" value="{{supplier.supplier_id}}">{{supplier.supplier_id}}-{{supplier.supplier_name}}</option>
                                </select>
                            </div>
                            <div class="form-group" ng-init="getType();forDisplay='';formula='';checkInputted=false;checkRepeat=false ">
                                <label for="deductionType">Deduction Type: </label>
                                <select name="deductionType" ng-model="deductionType" class="form-control rounded-0" required>
                                    <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                    <option ng-repeat="ty in types" value="{{ty.deduction_type_id}}">{{ty.type}}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="deductionName" class="col-sm-5 col-form-label"><i class="fab fa-slack required-icon"></i> Deduction Name: </label>
                                <input type="text" class="form-control rounded-0 text-uppercase" ng-model="deductionName" name="deductionName" autocomplete="off" required>                               
                            </div>  
                            <div class="form-group">
                                <label for="forDisplay" class="col-sm-6 col-form-label"><i class="fab fa-slack required-icon"></i> Name Used For Display: </label>
                                <input type="text" class="form-control rounded-0 text-uppercase" ng-model="forDisplay" name="forDisplay" autocomplete="off" required> 
                            </div>  
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" ng-model="checkInputted" name="checkInputted" value="">Allow Inputted
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" ng-model="checkRepeat" name="checkRepeat" value="">Allow Repeat
                                </label>
                            </div> 
                            <div class="form-group" ng-show="!checkInputted">
                                <label for="formula" class="col-sm-5 col-form-label" ><i class="fab fa-slack required-icon"></i> Formula: </label>
                                <input type="text" class="form-control rounded-0" ng-model="formula" name="formula" autocomplete="off">                               
                            </div>                           
                                                   
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="!deductionType || !deductionName || !forDisplay"><i class="fas fa-save"></i> Save</button>
                            <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal" ng-click="resetAddDeduction()"><i class="fas fa-times"></i> Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ADD DEDUCTION -->

    <!-- EDIT DEDUCTION -->
    <div class="modal fade" id="updateDeduction" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog" role="document">
            <div class="modal-content rounded-0 modal-md">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> Edit Deduction</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" name="edit" ng-submit="updateDeduction($event)" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="editSupplier">Supplier Name: </label>
                                <select name="editSupplier" ng-model="editSupplier" ng-options="supplier.supplier_id as supplier.supplier_name for supplier in suppliers" class="form-control rounded-0" required>
                                    <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="editDeductionType">Deduction Type: </label>
                                <select name="editDeductionType" class="form-control rounded-0" ng-model="editDeductionType" ng-options="ty.deduction_type_id as ty.type for ty in types" required>
                                    <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="editDeductionName" class="col-sm-5 col-form-label"><i class="fab fa-slack required-icon"></i> Deduction Name: </label>
                                <input type="text" class="form-control rounded-0" ng-model="editDeductionName" name="editDeductionName" required autocomplete="off">    
                                <!-- FOR ERRORS -->
                                <div class="validation-Error">
                                    <span ng-show="edit.editDeductionName.$dirty && edit.editDeductionName.$error.required">
                                        <p class="error-display">This field is required.</p>
                                    </span>
                                </div>                           
                            </div>  
                            <div class="form-group">
                                <label for="editForDisplay" class="col-sm-6 col-form-label"><i class="fab fa-slack required-icon"></i> Name Used For Display: </label>
                                <input type="text" class="form-control rounded-0" ng-model="editForDisplay" name="editForDisplay" autocomplete="off" required>
                                <div class="validation-Error">
                                    <span ng-show="edit.editForDisplay.$dirty && edit.editForDisplay.$error.required">
                                        <p class="error-display">This field is required.</p>
                                    </span>
                                </div>  
                            </div>  
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" ng-model="editCheckInputted" name="editCheckInputted" value="">Allow Inputted
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" ng-model="editCheckRepeat" name="editCheckRepeat" value="">Allow Repeat
                                </label>
                            </div> 
                            <div class="form-group">
                                <label for="formula" class="col-sm-5 col-form-label" > Formula: </label>
                                <input type="text" class="form-control rounded-0" ng-model="editFormula" name="editFormula" autocomplete="off">  
                                <div class="validation-Error">
                                    <span ng-show="edit.editCheckInputted && edit.editFormula.$dirty">
                                        <p class="error-display">This field is required.</p>
                                    </span>
                                </div>                               
                            </div>                           
                                                   
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="edit.editDeductionType.$error.required || edit.editDeductionName.$error.required || edit.editAcronym.$error.required"><i class="fas fa-save"></i> Save</button>
                            <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- EDIT DEDUCTION -->


    

</div>
<!-- /.content-wrapper -->