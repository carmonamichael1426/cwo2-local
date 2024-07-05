<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="charges-controller">
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
                                            <div class="panel-body"> <strong> CHARGES </strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($this->session->userdata('userType') == 'Admin'): ?>
                                <div class="row mb-3">
                                    <button class="btn bg-gradient-primary btn-flat mr-2" data-target="#newChargesType"
                                        data-toggle="modal">
                                        <i class="fas fa-plus-circle"></i>
                                        Charges Type
                                    </button>
                                </div>
                            <?php endif; ?>

                            <div>
                                <table id="chargesTypeTable" class="table table-bordered table-sm table-hover"
                                    ng-init="chargesTypeTable()">
                                    <thead class="bg-dark">
                                        <th scope="col" class="text-center">#</th>
                                        <th scope="col" class="text-center">Charges Type</th>
                                        <th scope="col" width="10%" class="text-center">Action</th>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="c in chargesTypeData" ng-cloak>
                                            <td class="text-center">{{ $index + 1}}</td>
                                            <td class="text-center">{{ c.charges_type }}</td>
                                            <td class="text-center">
                                                <button ng-disabled="c.statuss == 0"
                                                    class="btn bg-gradient-info btn-flat btn-sm" data-toggle="modal"
                                                    data-target="#editChargesType" ng-click="fetchChargesTypeData(c)"><i
                                                        class="fas fa-pen-square"></i> Edit
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

    <!-- ADD CHARGES TYPE -->
    <div class="modal fade" id="newChargesType" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog" role="document">
            <div class="modal-content rounded-0 modal-md">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> New Charges Type
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" name="addChargesTypeForm" ng-submit="saveChargesType($event)"
                    enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="type" class="col-sm-5 col-form-label"><i
                                        class="fab fa-slack required-icon"></i> Charges Type : </label>
                                <input type="text" class="form-control rounded-0" name="charges_type"
                                    ng-model="chargesType" required autocomplete="off">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn bg-gradient-primary btn-flat"><i class="fas fa-save"></i>
                                Save</button>
                            <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i
                                    class="fas fa-times"></i> Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ADD CHARGES TYPE -->

    <!-- EDIT CHARGES TYPE -->
    <div class="modal fade" id="editChargesType" data-backdrop="static" data-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog" role="document">
            <div class="modal-content rounded-0 modal-md">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> Edit Charges Type
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" name="editChargesTypeForm" ng-submit="updateChargesType($event)"
                    enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="type" class="col-sm-5 col-form-label"><i
                                        class="fab fa-slack required-icon"></i> Charges Type : </label>
                                <input type="text" class="form-control rounded-0" name="charges_type"
                                    ng-model="charges_type" required autocomplete="off">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn bg-gradient-primary btn-flat"
                                ng-disabled="editChargesTypeForm.$invalid"><i class="fas fa-pen-alt"></i>
                                Update</button>
                            <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i
                                    class="fas fa-times"></i> Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- EDIT CHARGES TYPE -->

</div>
<!-- /.content-wrapper -->