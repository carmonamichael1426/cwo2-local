<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper body-bg" ng-controller="iadreport-controller">
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
                                            <div class="panel-body"><i class="fas fa-file-alt"></i> <strong>IAD Report</strong></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body card-body-style">
                            <!-- <div class="col-md-12">
                                <table id="iadreportTable" class="table table-sm table-bordered font-xs" ng-init="getIadReports()">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" class="text-center">Supplier's Name</th>
                                            <th scope="col" class="text-center">CRF/CV No</th>
                                            <th scope="col" class="text-center">CRF/CV Date</th>
                                            <th scope="col" class="text-center">CRF/CV Amount</th>
                                            <th scope="col" class="text-center">PI No</th>
                                            <th scope="col" class="text-center">Posting Date</th>
                                            <th scope="col" class="text-center">Amount W/ VAT</th>
                                            <th scope="col" class="text-center">Unclosed Balance</th>
                                            <th scope="col" class="text-center">Remarks</th>
                                            <th scope="col" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-cloak ng-repeat="iad in iadReports">
                                            <td class="text-left">{{ iad.supplier_name }}</th>
                                            <td class="text-center">{{ iad.crf_no }}</th>
                                            <td class="text-center">{{ iad.crf_date }}</th>
                                            <td class="text-right">{{ iad.total_crf_amount | currency : '' }}</th>
                                            <td class="text-center">{{ iad.pi_no }}</th>
                                            <td class="text-center">{{ iad.posting_date }}</th>
                                            <td class="text-right">{{ iad.amt_including_vat | currency : '' }}</th>
                                            <td class="text-right">{{ iad.unclosed_balance | currency : '' }}</th>
                                            <td class="text-center">{{ iad.remarks }}</th>
                                            <td class="text-center"> 
                                                <button title="Print" class="btn btn-info btn-flat btn-xs"><i class="fas fa-file-alt"></i></button>
                                            </th>
                                        </tr>
                                    </tbody>
                                </table>
                            </div> -->
                            <form action="" method="post" name="iadReporForm" enctype="multipart/form-data" ng-submit="generateIADReports($event)">
                                <!-- <div class="padding-style-1"></div> -->
                                <div class="row padding-style mb-2">
                                    <label for="staticEmail" class="col-md-3 col-form-label text-right">Search By: </label>
                                    <div class="col-md-4">
                                        <select class="form-control rounded-0" ng-change="search()" ng-model="searchBy" name="searchBy" required>
                                            <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                            <option>All Supplier</option>
                                            <option>Supplier</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row padding-style mb-2" ng-if="searchBy == 'Supplier'" ng-cloak>
                                    <label for="staticEmail" class="col-md-3 col-form-label text-right">Supplier Name: </label>
                                    <div class="col-md-4">
                                        <select multiple class="form-control rounded-0" ng-model="supplierSelect" name="supplierSelect[]" required>
                                            <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                            <option ng-repeat="s in suppliers" value="{{s.supplier_id}}">{{s.supplier_name}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row padding-style mb-2" ng-if="allSupplier">
                                    <label for=" staticEmail" class="col-md-3 col-form-label text-right">Date From: </label>
                                    <div class="col-md-4">
                                        <input type="text" id="dateFrom" class="form-control rounded-0" name="dateFrom1" readonly placeholder="YYYY-MM-DD">
                                    </div>
                                </div>
                                <div class="row padding-style mb-2" ng-if="allSupplier">
                                    <label for=" staticEmail" class="col-md-3 col-form-label text-right">Date To: </label>
                                    <div class="col-md-4">
                                        <input type="text" id="dateTo" class="form-control rounded-0" name="dateTo1" readonly placeholder="YYYY-MM-DD">
                                    </div>
                                </div>

                                <div class="row padding-style">
                                    <label for="staticEmail" class="col-md-3 col-form-label text-right"></label>
                                    <div class="col-md-4">
                                        <button type="submit" ng-disabled="!searchBy" class="btn bg-gradient-primary btn-flat btn-block">Generate</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /.col-md-6 -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->

    <!-- MODAL UPLOAD PO VS PROFORMA -->
    <div class="modal fade" id="addPOReport" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-md" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> Upload Proforma</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data" ng-submit="uploadProforma($event)">
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group" ng-init="getSuppliers()">
                                    <label for="#">Supplier Name: </label>
                                    <select ng-change="getPurchaseOrder()" class="form-control rounded-0" ng-model="supplierSelect" name="supplierSelect">
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="s in suppliers" value="{{s.supplier_code}}">{{s.supplier_name}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group" ng-init="getCustomers()">
                                    <label for="#">Customer Name: </label>
                                    <select ng-change="getPurchaseOrder()" class="form-control rounded-0" ng-model="customerSelect" name="customerSelect">
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="c in customers" value="{{c.customer_code}}">{{c.customer_name}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="#">Purchase Order: </label>
                                    <select class="form-control rounded-0" ng-model="poSelect" name="poSelect">
                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                        <option ng-repeat="p in po" value="{{p.po_header_id}}">{{p.po_no}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="#">Proforma: </label>
                                    <input type="file" name="proforma[]" id="proforma" class="form-control rounded-0" style="height: 45px">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-success btn-flat"><i class="fas fa-upload"></i> Upload</button>
                        <button type="button" class="btn btn-outline-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- VIEW PROFORMA -->
    <div class="modal fade" id="viewProforma" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> View Proforma</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data" ng-submit="editProforma($event)">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="acroname_edit">Acroname:</label>
                                    <input type="text" class="form-control rounded-0" name="acroname_edit" autocomplete="off" ng-model="acroname_edit" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="po_no">PO Number/PO Reference:</label>
                                    <input type="text" class="form-control rounded-0" name="po_no" autocomplete="off" ng-model="po_no" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="so_no">SO Number: </label>
                                    <input type="text" class="form-control rounded-0" name="so_no" autocomplete="off" ng-model="so_no" readonly>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="pro_code">Proforma Code: </label>
                                    <input type="text" class="form-control rounded-0" name="pro_code" autocomplete="off" ng-model="pro_code" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="container-fluid">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="proforma-line-tab" data-toggle="tab" href="#proforma-line" role="tab" aria-controls="home" aria-selected="true">Proforma Line</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="discounts-tab" data-toggle="tab" href="#discounts" role="tab" aria-controls="profile" aria-selected="false">Discounts</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="replace-proforma-tab" data-toggle="tab" href="#replace-proforma" role="tab" aria-controls="contact" aria-selected="false">Replace Proforma</a>
                                </li>
                            </ul>

                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="proforma-line" role="tabpanel" aria-labelledby="proforma-line-tab">
                                    <div class="row mt-2" ng-if="tableRow">
                                        <table class="table-sm">
                                            <thead class="bg-dark">
                                                <tr>
                                                    <th>Item Code</th>
                                                    <th>Description</th>
                                                    <th class="text-right" style="width: 5%;">QTY</th>
                                                    <th>UOM</th>
                                                    <th class="text-right" style="width: 15%;">Price</th>
                                                    <th class="text-right" style="width: 15%;">Amount</th>
                                                    <th class="text-center" style="width: 5%;;">Edit</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr ng-repeat="pl in proforma_line">
                                                    <td>{{ pl.item_code }}</td>
                                                    <td>{{ pl.description}}</td>
                                                    <td>
                                                        <div class="input-group input-group-sm rounded-0">
                                                            <input type="text" class="form-control rounded-0 text-right" ng-model="pl.qty" style="border: none;" ng-disabled="!pl.checkBoxEdit">
                                                        </div>
                                                    </td>
                                                    <td>{{ pl.uom }}</td>
                                                    <td>
                                                        <div class="input-group input-group-sm rounded-0">
                                                            <div class="input-group-prepend rounded-0">
                                                                <span class="input-group-text rounded-0" style="border: 0; background-color: white;"><strong>₱</strong></span>
                                                            </div>
                                                            <input type="text" class="form-control rounded-0 text-right" ng-model="pl.price" style="border: none;" ng-disabled="!pl.checkBoxEdit">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="input-group input-group-sm rounded-0">
                                                            <div class="input-group-prepend rounded-0">
                                                                <span class="input-group-text rounded-0" style="border: 0; background-color: white;"><strong>₱</strong></span>
                                                            </div>
                                                            <input type="text" class="form-control rounded-0 text-right" name="amount" id="amount" ng-value="pl.qty * pl.price | currency : ''" style="border: none;" readonly>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="col-lg-12">
                                                            <div class="input-group">
                                                                <input type="checkbox" style="width: 30px; height: 30px;" ng-model="pl.checkBoxEdit" ng-changed="setButtonEnabled()" class="rounded-0">
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="row" ng-if="uploadRow">
                                        <div class="col-md-12" style="padding-left: 200px; padding-right: 200px;">
                                            <div class="form-group">
                                                <label for="#">New Proforma: </label>
                                                <input type="file" name="new_proforma[]" id="new_proforma" class="form-control rounded-0" style="height: 45px">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="discounts" role="tabpanel" aria-labelledby="discounts-tab">
                                    <div class="container" style="padding-left: 200px; padding-right: 200px;">
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="#">Discount: </label>
                                                    <select class="form-control rounded-0" ng-model="poSelect" name="poSelect">
                                                        <option value="" disabled="" selected="" style="display:none">Please Select One</option>
                                                        <option>BO Allowance</option>
                                                        <option>Payment Terms</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="po_no">Amount:</label>
                                                    <input type="text" class="form-control rounded-0 text-right" name="po_no" autocomplete="off" value="0.00">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="replace-proforma" role="tabpanel" aria-labelledby="replace-proforma-tab">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-success btn-flat" ng-disabled="setButtonEnabled()" ng-if="tableRow">
                            <i class="fas fa-edit"></i> Update
                        </button>

                        <button type="submit" class="btn btn-outline-success btn-flat" ng-if="uploadRow">
                            <i class="fas fa-upload"></i> Replace Proforma
                        </button>

                        <button type="button" class="btn btn-outline-info btn-flat" ng-click="replaceProforma()">
                            <i class="fas fa-undo-alt"></i>
                            {{buttonNAme}}
                        </button>

                        <button type="button" class="btn btn-outline-danger btn-flat" data-dismiss="modal">
                            <i class="fas fa-times"></i> Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PROFORMA LINE HISTORY -->
    <div class="modal fade" id="viewHistory" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> Proforma Line History</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data" ng-submit="editProforma($event)">
                    <div class="modal-body">
                        <table class="table-sm" id="historyTable">
                            <thead class="bg-dark">
                                <tr>
                                    <th>Item Code</th>
                                    <th style="width: 200px;">Description</th>
                                    <th class="text-right" style="width: 5%;">QTY</th>
                                    <th>UOM</th>
                                    <th class="text-right" style="width: 15%;">Price</th>
                                    <th class="text-right" style="width: 15%;">Amount</th>
                                    <th>Date Edited</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="h in proforma_history">
                                    <td>{{ h.item_code }}</td>
                                    <td class="text-left">{{ h.description }}</td>
                                    <td>{{ h.qty }}</td>
                                    <td>{{ h.uom }}</td>
                                    <td>{{ h.price }}</td>
                                    <td>{{ h.amount }}</td>
                                    <td>{{ h.date_edited }}</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MATCH PROFORMA -->
    <div class="modal fade" id="matchingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog modal-xl" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header bg-dark">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fas fa-edit"></i> PO vs Proforma Matching</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="post" enctype="multipart/form-data" ng-submit="match($event, items)">
                    <div class="modal-body">
                        <table class="table-sm" id="historyTable">
                            <thead class="bg-dark">
                                <tr>
                                    <th class="text-center">PO Item Code</th>
                                    <th class="text-center">Customer Item Code</th>
                                    <th class="text-center">Proforma Description</th>
                                    <th class="text-center">Supplier Item Code</th>
                                    <th class="text-center">Proforma Item Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr ng-repeat="i in items">
                                    <td class="text-center">
                                        <span class="no-item" ng-if="i.po_item == null">NO ITEM</span>
                                        <span ng-if="i.po_item != null">{{ i.po_item }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="no-item" ng-if="i.itemcode_cus == null">NO ITEM</span>
                                        <span ng-if="i.itemcode_cus != null">{{ i.itemcode_cus }}</span>
                                    </td class="text-center">
                                    <td class="text-center">
                                        <span class="no-item" ng-if="i.description == null">NO ITEM</span>
                                        <span ng-if="i.description != null">{{ i.description }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="no-item" ng-if="i.itemcode_sup == null">NO ITEM</span>
                                        <span ng-if="i.itemcode_sup != null">{{ i.itemcode_sup }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="no-item" ng-if="i.pr_item == null">NO ITEM</span>
                                        <span ng-if="i.pr_item != null">{{ i.pr_item }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-success btn-flat"><i class="fas fa-link"></i> Match</button>
                        <button type="button" class="btn btn-outline-danger btn-flat" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /.content-wrapper -->