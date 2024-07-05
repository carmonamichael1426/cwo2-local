<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="customer-controller">
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
                                            <div class="panel-body"><i class="fas fa-location-arrow"></i> <strong>LOCATIONS</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <button class="btn bg-gradient-primary btn-flat float" data-target="#newCustomer" data-toggle="modal"><i class="fas fa-plus-circle"></i> Add Location</button>
                                </div>
                            </div>

                            <div>
                                <table id="customersTable" class="table table-bordered table-sm table-hover" ng-init="customersTable()">
                                    <thead class="bg-dark">
                                        <th scope="col" class="text-center">ID</th>
                                        <th scope="col" class="text-center">Location Name</th>
                                        <th scope="col" class="text-center">Acroname</th>
                                        <th scope="col" style="width: 160px;" class="text-center">Action</th>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="c in customers" ng-cloak>
                                            <td class="text-center">{{ c.customer_code }}</td>
                                            <td class="text-center">{{ c.customer_name }}</td>
                                            <td class="text-center">{{ c.l_acroname }}</td>
                                            <td class="text-center">
                                                <button ng-disabled="c.status == 0" class="btn bg-gradient-info btn-flat btn-sm" data-toggle="modal" data-target="#updateCustomer" ng-click="fetchCustomerData(c)"><i class="fas fa-pen-square"></i> Edit
                                                </button>
                                                <button ng-disabled="c.status == 0" class="btn bg-gradient-danger btn-flat btn-sm" ng-click="deactivateCustomer(c)"><i class="fas fa-times"></i> Deactivate
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

    <!-- MODAL ADD CUSTOMER -->
    <div class="modal fade" id="newCustomer" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog" role="document">
            <div class="modal-content rounded-0 modal-md">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> New Location</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" name="addLocation" ng-submit="saveCustomer($event)" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="customer" class="col-sm-5 col-form-label"><i class="fab fa-slack required-icon"></i> Location Name: </label>
                                <input type="text" class="form-control rounded-0" ng-model="customer" name="customer" required autocomplete="off">
                                <!-- FOR ERRORS -->
                                <div class="validation-Error">
                                    <span ng-show="addLocation.customer.$dirty && addLocation.customer.$error.required">
                                        <p class="error-display">This field is required.</p>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="acroname" class="col-sm-5 col-form-label"><i class="fab fa-slack required-icon"></i> Acroname: </label>
                                <input type="text" class="form-control rounded-0" ng-model="acroname" name="acroname" required autocomplete="off">
                                <!-- FOR ERRORS -->
                                <div class="validation-Error">
                                    <span ng-show="addLocation.acroname.$dirty && addLocation.acroname.$error.required">
                                        <p class="error-display">This field is required.</p>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="addLocation.$invalid"><i class="fas fa-save"></i> Save</button>
                            <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT SUPPLIER -->
    <div class="modal fade" id="updateCustomer" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog" role="document">
            <div class="modal-content rounded-0 modal-md">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> Edit Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" name="editInfo" ng-submit="editCustomer($event)" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="customer" class="col-sm-5 col-form-label"><i class="fab fa-slack required-icon"></i> Location Name: </label>
                                <input type="text" name="updateCustomerName" ng-model="updateCustomerName" value="" class="form-control rounded-0" required autocomplete="off">
                                <!-- FOR ERRORS -->
                                <div class="validation-Error">
                                    <span ng-show="editInfo.updateCustomerName.$dirty && editInfo.updateCustomerName.$error.required">
                                        <p class="error-display">This field is required.</p>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="updateAcroname" class="col-sm-5 col-form-label"><i class="fab fa-slack required-icon"></i> Acroname: </label>
                                <input type="text" name="updateAcroname" ng-model="updateAcroname" value="" class="form-control rounded-0" required autocomplete="off">
                                <!-- FOR ERRORS -->
                                <div class="validation-Error">
                                    <span ng-show="editInfo.updateAcroname.$dirty && editInfo.updateAcroname.$error.required">
                                        <p class="error-display">This field is required.</p>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="editInfo.$invalid"><i class="fas fa-pen-alt"></i> Update</button>
                            <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /.content-wrapper -->