<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="supplierledger-controller">
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
                                            <div class="panel-body"><i class="fas fa-file-alt"></i> <strong>SUPPLIERS LEDGER</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3" style="padding-left: 100px;">
                                <div class="col-md-6">
                                    <div class="form-inline mb-2" ng-init="getSuppliers()">
                                        <label for="#" class="col-md-4 float-right">Suppliers Name: </label>
                                        <div class="col-md-6">
                                            <select ng-change="getDetails()" class="form-control rounded-0" ng-model="supplierName" name="supplierName" style="width: 100%;">
                                                <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                                <option ng-repeat="s in suppliers" value="{{s.supplier_id}}">{{s.supplier_name}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-inline">
                                        <label for="#" class="col-md-4 float-right">Suppliers Code: </label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control rounded-0" name="supplierCode" autocomplete="off" style="width: 100%;" ng-model="supplierCode" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-inline mb-2">
                                        <label for="#" class="col-md-4 float-right">Suppliers Acroname: </label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control rounded-0" name="supplierAcroname" autocomplete="off" style="width: 100%;" ng-model="supplierAcroname" readonly>
                                        </div>
                                    </div>
                                    <div class="form-inline">
                                        <label for="#" class="col-md-4 float-right"></label>
                                        <div class="col-md-6">
                                            <button type="button" class="btn bg-gradient-primary btn-flat" style="width: 100%;" ng-click="generateLedger(supplierName)"><i class="fas fa-cogs"></i> Generate</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12" ng-show="tableShow">
                                <table id="supplierLedgerTable" class="table table-sm table-bordered font-small table-hover">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" class="text-center">Entry No</th>
                                            <th scope="col" class="text-center">Reference No</th>
                                            <th scope="col" class="text-center">Posting Date</th>
                                            <th scope="col" class="text-center">Doc Type</th>
                                            <th scope="col" class="text-center">Doc No</th>
                                            <th scope="col" class="text-center">PSI No</th>
                                            <th scope="col" class="text-center">PO Reference</th>
                                            <th scope="col" class="text-center">Debit</th>
                                            <th scope="col" class="text-center">Credit</th>
                                            <th scope="col" class="text-center">Running Balance</th>
                                            <th scope="col" class="text-center" style="width: 40px;">Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-cloak ng-repeat="l in ledger">
                                            <td class="text-left">{{ l.ledger_id }}</th>
                                            <td class="text-left">{{ l.reference_no}}</td>
                                            <td class="text-left">{{ l.posting_date }}</th>
                                            <td class="text-left">{{ l.doc_type }}</th>
                                            <td class="text-left">{{ l.doc_no }}</td>
                                            <td>
                                                <span class="text-left" ng-if="l.invoice_no != '' || l.invoice_no != null">{{ l.invoice_no }}</span>
                                                <span class="text-right" ng-if="l.invoice_no == '' || l.invoice_no == null">-</span>
                                            </td>
                                            <td class="text-left">
                                                <span ng-if="l.po_reference != '' || l.po_reference != null">{{ l.po_reference }}</span>
                                                <span ng-if="l.po_reference == '' || l.po_reference == null">-</span>
                                            </td>
                                            <td class="text-right">
                                                <span ng-if="l.debit != '' || l.debit != 0.00">{{ l.debit | currency : ''}}</span>
                                                <span ng-if="l.debit == '' || l.debit == 0.00">-</span>
                                            </td>
                                            <td class="text-right">
                                                <span ng-if="l.credit != '' || l.credit != 0.00">{{ l.credit | currency : ''}}</span>
                                                <span ng-if="l.credit == '' || l.credit == 0.00">-</span>
                                            </td>
                                            <td class="text-right" ng-dblclick="checkDetails(l)">{{ l.balance | currency : ''}}</td>
                                            <td class="text-center">
                                                <button type="button" class="btn bg-gradient-info btn-flat btn-xs" data-toggle="modal" data-target="#viewData" ng-click="details(l)">Details
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

    <!-- VIEW DETAILS -->
    <div class="modal fade" id="viewData" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <i class="fas fa-search"></i> <strong>View Details</strong>
                        </div>
                    </div>
                    <?php include './application/views/components/supplierLedgerView.php' ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-gradient-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.content-wrapper -->