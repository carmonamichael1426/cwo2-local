<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="users-controller">
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
                                            <div class="panel-body"><i class="fas fa-users"></i> <strong>USERS</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mt-4 mb-4">
                                    <label for="searchemployee">Search Employee To Add : </label>
                                    <input type="text" class="form-control rounded-0" id="searchemployee"
                                        name="searchemployee" ng-model="searchemployee"
                                        ng-keyup="searchEmployee($event)" placeholder="Lastname, Firstname Middlename"
                                        autocomplete="off">
                                    <div class="search-emp-results" ng-repeat="s in searchResult " ng-cloak
                                        ng-if="hasResults == 1">
                                        <a href="#" ng-repeat="s in searchResult track by $index" ng-cloak
                                            ng-click="addEmployee($event,s)">
                                            {{s.emp_id}} * {{s.name}} - {{s.company}} - {{s.position}} -
                                            {{s.company_code}} - {{s.bunit_code}} - {{s.dept_code}}<br>
                                        </a>
                                    </div>
                                    <div class="search-emp-results" ng-repeat="s in searchResult" ng-cloak
                                        ng-if="hasResults == 0">
                                        <a href="#" ng-cloak ng-repeat="s in searchResult">
                                            {{s.emp_id}} <br>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <table id="usersTable" class="table table-bordered table-sm table-hover"
                                        ng-init="getUsers()">
                                        <thead class="bg-dark">
                                            <tr>
                                                <th scope="col" class="text-center" style="display:none;">ID</th>
                                                <th scope="col" class="text-center">Name</th>
                                                <th scope="col" class="text-center">Username</th>
                                                <th scope="col" class="text-center">Position</th>
                                                <th scope="col" class="text-center">User Type</th>
                                                <th scope="col" class="text-center">Status</th>
                                                <th scope="col" class="text-center" style="width: 100px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr ng-repeat="u in users" ng-cloak>
                                                <td class="text-center" style="display:none;">{{ u.user_id }}</td>
                                                <td class="text-center">{{ u.name }}</td>
                                                <td class="text-center">{{ u.username }}</th>
                                                <td class="text-center">{{ u.position }}</th>
                                                <td class="text-center">{{ u.userType }}</th>
                                                <td class="text-center">{{ u.status }}</th>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <button type="button"
                                                            class="btn bg-gradient-info btn-flat btn-sm dropdown-toggle"
                                                            data-toggle="dropdown" aria-expanded="false">Action
                                                        </button>
                                                        <div class="dropdown-menu rounded-0"
                                                            style="margin-right: 50px;">
                                                            <a href="#" class="dropdown-item" data-toggle="modal"
                                                                data-target="#updateUserModal"
                                                                ng-click="getUserDetails(u)"><i
                                                                    class="fas fa-pen-square"></i> Update</a>
                                                            <a href="#" class="dropdown-item"
                                                                ng-click="deactivate(u)"><i class="fas fa-ban"></i>
                                                                Deactivate</a>
                                                            <a href="#" class="dropdown-item"
                                                                ng-click="resetpassword(u)"><i
                                                                    class="fas fa-undo-alt"></i> Reset Password</a>
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
                </div>
                <!-- /.col-md-6 -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <!-- MODAL ADD USER -->
    <div class="modal fade" id="newUser" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-pen-square"></i> New User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" name="newUserForm" ng-submit="saveUser($event)"
                    enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="firstname" class="col-sm-4 col-form-label text-right"><i
                                            class="fab fa-slack required-icon"></i> First Name:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control rounded-0" id="firstname"
                                            name="firstname" ng-model="firstname" required autocomplete="off">
                                        <div class="validation-Error">
                                            <span
                                                ng-show="newUserForm.firstname.$dirty && newUserForm.firstname.$error.required">
                                                <p class="error-display">This field is required.</p>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="middlename" class="col-sm-4 col-form-label text-right">Middle
                                        Name:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control rounded-0" id="middlename"
                                            name="middlename" ng-model="middlename">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="lastname" class="col-sm-4 col-form-label text-right"><i
                                            class="fab fa-slack required-icon"></i> Last Name:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control rounded-0" id="lastname" name="lastname"
                                            ng-model="lastname" required autocomplete="off">
                                        <div class="validation-Error">
                                            <span
                                                ng-show="newUserForm.lastname.$dirty && newUserForm.lastname.$error.required">
                                                <p class="error-display">This field is required.</p>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="position" class="col-sm-4 col-form-label text-right"><i
                                            class="fab fa-slack required-icon"></i> Position:</label>
                                    <div class="col-sm-8">
                                        <select class="form-control rounded-0" id="position" name="position"
                                            ng-model="position" required>
                                            <option value="" disabled="" selected="" style="display:none">Please Select
                                                One</option>
                                            <option>Accounting Clerk I</option>
                                            <option>Accounting Clerk II</option>
                                            <option>Accounting Clerk III</option>
                                            <option>Accounts Payable Clerk</option>
                                            <option>Buyer-Purchaser</option>
                                            <option>CDC Accounting Section Head</option>
                                            <option>Jr. Auditor</option>
                                            <option>LDI</option>
                                            <option>Manager</option>
                                            <option>MIS - Encoder</option>
                                            <option>Pricing In Charge</option>
                                            <option>Programmer</option>
                                            <option>Receiving Clerk</option>
                                            <option>Section Head</option>
                                            <option>Sr. Auditor</option>
                                            <option>Supervisor</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group row">
                                    <label for="department"
                                        class="col-sm-4 col-form-label text-right">Department:</label>
                                    <div class="col-sm-8">
                                        <select class="form-control rounded-0" name="department" ng-model="department">
                                            <option value="" disabled="" selected="" style="display:none">Please Select
                                                One</option>
                                            <option>CDC MIS</option>
                                            <option>Central Accounting</option>
                                            <option>Colonnade Colon Accounting</option>
                                            <option>Colonnade Mandaue Accounting</option>
                                            <option>Colonnade Colon IAD</option>
                                            <option>Colonnade Mandaue IAD</option>
                                            <option>Corporate Accounting</option>
                                            <option>Corporate IAD</option>
                                            <option>Corporate IT</option>
                                            <option>LDI</option>
                                            <option>MIS</option>
                                            <option>Purchasing</option>
                                            <option>Purchasing Colonnade Colon</option>
                                            <option>Purchasing Colonnade Mandaue</option>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="subsidiary"
                                        class="col-sm-4 col-form-label text-right">Subsidiary:</label>
                                    <div class="col-sm-8">
                                        <select class="form-control rounded-0" name="subsidiary" ng-model="subsidiary">
                                            <option value="" disabled="" selected="" style="display:none">Please Select
                                                One</option>
                                            <option>Alturas Mall - Main</option>
                                            <option>CDC</option>
                                            <option>Corporate</option>
                                            <option>Colonnade Colon</option>
                                            <option>Colonnade Mandaue</option>
                                            <option>Island City Mall</option>
                                            <option>Plaza Marcela</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="usertype" class="col-sm-4 col-form-label text-right"><i
                                            class="fab fa-slack required-icon"></i> User Type:</label>
                                    <div class="col-sm-8">
                                        <select class="form-control rounded-0" name="usertype" ng-model="usertype"
                                            required>
                                            <option value="" disabled="" selected="" style="display:none">Please Select
                                                One</option>
                                            <option>Admin</option>
                                            <option>Accounting</option>
                                            <option>Buyer-Purchaser</option>
                                            <option>GGM Encoder</option>
                                            <option>IAD</option>
                                            <option>LDI</option>
                                            <option>Manager</option>
                                            <option>PI</option>
                                            <option>Pricing</option>
                                            <option>Receiving Clerk</option>
                                            <option>Section Head</option>
                                            <option>SOP</option>
                                            <option>SOPAccttg</option>
                                            <option>Supervisor</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="username" class="col-sm-4 col-form-label text-right"><i
                                            class="fab fa-slack required-icon"></i> User Name:</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control rounded-0" name="username"
                                            ng-model="username" required autocomplete="off">
                                        <div class="validation-Error">
                                            <span
                                                ng-show="newUserForm.username.$dirty && newUserForm.username.$error.required">
                                                <p class="error-display">This field is required.</p>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-gradient-primary btn-flat"
                            ng-disabled="newUserForm.$invalid || newUserForm.confirmpassword.$error.match"><i
                                class="fas fa-save"></i> Save</button>
                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i
                                class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.content -->

    <!-- MODAL EDIT USER -->
    <div class="modal fade" id="updateUserModal" data-backdrop="static" data-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-lg" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark rounded-0">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-pen-square"></i> Update User
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" name="updateUserForm" ng-submit="updateUser($event)"
                    enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label for="name_u" class="col-sm-2 col-form-label">Full Name :</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control rounded-0" id="name_u" name="name"
                                            ng-model="name_u" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="position_u" class="col-sm-2 col-form-label">Position :</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control rounded-0" id="position_u" name="name"
                                            ng-model="position_u" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="company_u" class="col-sm-2 col-form-label">Company :</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control rounded-0" id="company_u"
                                            ng-model="company_u" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="bu_u" class="col-sm-2 col-form-label">Business Unit :</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control rounded-0" id="bu_u" ng-model="bu_u"
                                            readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="department_u" class="col-sm-2 col-form-label">Department :</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control rounded-0" id="department_u"
                                            ng-model="department_u" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="usertype_u" class="col-sm-2 col-form-label"><i
                                            class="fab fa-slack required-icon"></i> User Type:</label>
                                    <div class="col-sm-10">
                                        <select class="form-control rounded-0" id="usertype_u" name="usertype"
                                            ng-model="usertype_u" required>
                                            <option value="" disabled="" selected="" style="display:none">Please Select
                                                One</option>
                                            <option>Admin</option>
                                            <option>Accounting</option>
                                            <option>Buyer-Purchaser</option>
                                            <option>GGM Encoder</option>
                                            <option>IAD</option>
                                            <option>LDI</option>
                                            <option>Manager</option>
                                            <option>PI</option>
                                            <option>Pricing</option>
                                            <option>Receiving Clerk</option>
                                            <option>Section Head</option>
                                            <option>SOP</option>
                                            <option>SOPAccttg</option>
                                            <option>Supervisor</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn bg-gradient-success btn-flat"
                            ng-disabled="updateUserForm.$invalid"><i class="fas fa-save"></i> Save</button>
                        <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i
                                class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- /.content -->
</div>