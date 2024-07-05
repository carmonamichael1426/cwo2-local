<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="uploadedtransaction-controller">
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
                                            <div class="panel-body"><i class="fas fa-file-alt"></i> <strong>UPLOADED TRANSACTION DOCUMENTS</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" ng-init="supplierSelect=0;locationSelect=0">
                            <div class="row padding-style mb-2">
                                <label for="transactionType" class="col-md-3 col-form-label text-right">Filter: </label>
                                <div class="col-md-4">
                                    <select class="form-control rounded-0" ng-model="transactionType" name="transactionType" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option>All Transactions</option>
                                        <option>By Supplier</option>
                                        <option>By Location</option>
                                        <option>By Supplier and Location</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row padding-style mb-2" ng-show="transactionType == 'By Supplier' || transactionType == 'By Supplier and Location'" ng-cloak>
                                <label for="supplierSelect" class="col-md-3 col-form-label text-right">Supplier: </label>
                                <div class="col-md-4">
                                    <select class="form-control rounded-0" ng-model="supplierSelect" name="supplierSelect" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="s in suppliers" value="{{s.supplier_id}}">{{s.supplier_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row padding-style mb-2" ng-show="transactionType == 'By Location' || transactionType == 'By Supplier and Location'" ng-cloak>
                                <label for="locationSelect" class="col-md-3 col-form-label text-right">Location: </label>
                                <div class="col-md-4">
                                    <select class="form-control rounded-0" ng-model="locationSelect" name="locationSelect" required>
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="c in customers" value="{{c.customer_code}}">{{c.customer_name}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row padding-style">
                                <label for="staticEmail" class="col-md-3 col-form-label text-right"></label>
                                <div class="col-md-4">
                                    <button type="button" ng-disabled="!transactionType" ng-click="generateUploadedTransaction()" class="btn bg-gradient-primary btn-flat btn-block">Generate</button>
                                </div>
                            </div>

                            <div class="col-md-12 mt-1" ng-if="tableShow">
                                <table id="uploadedtransactiontable" class="table table-sm font-small table-bordered table-hover">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" class="text-center">Document Type</th>
                                            <th scope="col" class="text-center">File Name</th>
                                            <th scope="col" class="text-center">File Path</th>
                                            <th scope="col" class="text-center">Supplier</th>
                                            <th scope="col" class="text-center">Location</th>  
                                            <th scope="col" class="text-center">Date Time Uploaded</th>                                          
                                            <th scope="col" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-cloak ng-repeat="d in documents">
                                            <td class="text-center">{{ d.document_type }}</th>
                                            <td class="text-center">{{ d.document_name }}</th>
                                            <td class="text-center">{{ d.document_path }}</td>
                                            <td class="text-center">{{ d.supplier_name }}</td>
                                            <td class="text-center">{{ d.customer_name }}</td>
                                            <td class="text-center">{{ d.uploaded_on }}</td>
                                            <td class="text-center">
                                                <button type="button" class="btn bg-gradient-info btn-flat btn-xs" title="Download Document" ng-click="downloadDocument(d)">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                            </td>
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
</div>
<!-- /.content-wrapper -->