<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="vat-controller">
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
                                            <div class="panel-body"> <strong> Value Added Tax </strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">                                
                                <button 
                                    class="btn bg-gradient-primary btn-flat mr-2" 
                                    data-target="#newType" 
                                    data-toggle="modal">
                                    <i class="fas fa-plus-circle"></i>
                                    VAT
                                </button>                         
                            </div>                           

                            <div>
                                <table id="vatTable" class="table table-bordered table-sm table-hover" ng-init="vatTable()">
                                    <thead class="bg-dark">
                                        <th scope="col" class="text-center">#</th>
                                        <th scope="col" class="text-center">Description</th>
                                        <th scope="col" class="text-center">Value</th>
                                        <th scope="col" style="width: 160px;" class="text-center">Action</th>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="v in vatData" ng-cloak>
                                            <td class="text-center">{{ $index + 1}}</td>
                                            <td class="text-center">{{ v.description }}</td>
                                            <td class="text-center">{{ v.value }}</td>                                            
                                            <td class="text-center">
                                                <button ng-disabled="v.status == 0" class="btn bg-gradient-info btn-flat btn-sm" data-toggle="modal" data-target="#updateVat" ng-click="fetchData(v)"><i class="fas fa-pen-square"></i> Edit
                                                </button>
                                                <button ng-disabled="v.status == 0" class="btn bg-gradient-danger btn-flat btn-sm" ng-click="deactivateVAT(v)"><i class="fas fa-times"></i> Deactivate
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

    <!-- MODAL ADD VAT -->
    <div class="modal fade" id="newType" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog" role="document">
            <div class="modal-content rounded-0 modal-md">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> New VAT</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" name="addvat" ng-submit="saveVAT($event)" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="desc" class="col-sm-12 col-form-label"><i class="fab fa-slack required-icon"></i> Description : </label>
                                <input type="text" class="form-control rounded-0" ng-model="desc" name="desc" required autocomplete="off">                                
                            </div>                            
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="valcom" class="col-sm-12 col-form-label"><i class="fab fa-slack required-icon"></i> Value(Computation) : </label>
                                <input type="text" class="form-control rounded-0" ng-model="valcom" name="valcom" ng-pattern="/^[0-9]+(\.[0-9]{1,2})?$/" required autocomplete="off">                                
                            </div>                            
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="addvat.$invalid"><i class="fas fa-save"></i> Save</button>
                            <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal" ng-click="resetAddVat()"><i class="fas fa-times"></i> Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- ADD VAT -->


    <!-- EDIT VAT -->
    <div class="modal fade" id="updateVat" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog" role="document">
            <div class="modal-content rounded-0 modal-md">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> Edit VAT</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" name="edit" ng-submit="updateVat($event)" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="desc" class="col-sm-12 col-form-label"><i class="fab fa-slack required-icon"></i> Description : </label>
                                <input type="text" class="form-control rounded-0" ng-model="desc_e" name="desc" required autocomplete="off">                                
                            </div>                            
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="valcom" class="col-sm-12 col-form-label"><i class="fab fa-slack required-icon"></i> Value(Computation) : </label>
                                <input type="text" class="form-control rounded-0" ng-model="val_e" name="valcom" ng-pattern="/^[0-9]+(\.[0-9]{1,2})?$/" required autocomplete="off">                                
                            </div>                            
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn bg-gradient-primary btn-flat" ng-disabled="edit.$invalid"><i class="fas fa-save"></i> Save</button>
                            <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- EDIT VAT -->


    

</div>
<!-- /.content-wrapper -->