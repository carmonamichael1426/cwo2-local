<div ng-if="paymentType">
    <div class="row">
        <label class="col-md-2 text-right">CRF NO: </label>
        <div class="col-md-2"><span ng-bind="crfNo"></span></div>
    </div>
    <div class="row">
        <label class="col-md-2 text-right">CRF Date: </label>
        <div class="col-md-2"><span ng-bind="crfDate"></span></div>
    </div>
    <div class="row">
        <label class="col-md-2 text-right">Collectors Name: </label>
        <div class="col-md-5"><span ng-bind="collectorsName"></span></div>
        <label class="col-md-2 text-right">CRF Amount: </label>
        <div class="col-md-2"><span>&#x20B1 {{ crfAmount | currency: ''}}</span></div>
    </div>
    <div class="row">
        <label class="col-md-2 text-right">Remarks: </label>
        <div class="col-md-5"><span ng-bind="remarks"></span></div>
        <label class="col-md-2 text-right">Paid Amount: </label>
        <div class="col-md-2"><span>&#x20B1 {{ paidAmount | currency: '' }}</span></div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="card collapsed-card rounded-0">
                <div class="card-header rounded-0 bg-dark">
                    <h3 class="card-title col-md-9">Proforma</h3>
                    <label>TOTAL : </label> <span>&#x20B1 {{ linesTotal + totalDiscount + totalVAT | currency : ''}}</span>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="card-body" style="display: none;">
                    <div class="row mb-3" ng-if="proformaHeader != 0">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <td>Proforma Code</td>
                                <td>SO No</td>
                                <td>Order No</td>
                                <td>Delivery Date</td>
                            </thead>
                            <tbody>
                                <tr ng-cloak ng-repeat="pr in proformaHeader">
                                    <td>{{ pr.proforma_code }}</td>
                                    <td>{{ pr.so_no }}</td>
                                    <td>{{ pr.order_no }}</td>
                                    <td>{{ pr.delivery_date }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row" ng-if="proformaLines != 0">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <td>Item Code</td>
                                <td>Description</td>
                                <td>Qty</td>
                                <td>UOM</td>
                                <td class="text-right">Price</td>
                                <td class="text-right">Amount</td>
                            </thead>
                            <tbody>
                                <tr ng-cloak ng-repeat="pl in proformaLines">
                                    <td>{{ pl.item_code }}</td>
                                    <td>{{ pl.description }}</td>
                                    <td>{{ pl.qty }}</td>
                                    <td>{{ pl.uom }}</td>
                                    <td class="text-right">{{ pl.price }}</td>
                                    <td class="text-right">{{ pl.amount | currency : ''}}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="1"><strong>TOTAL :</strong></td>
                                    <td colspan="5" class="text-right"><strong>{{ linesTotal | currency : '' }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="1"><strong>TOTAL DISCOUNT:</strong></td>
                                    <td colspan="5" class="text-right"><strong>{{ totalDiscount | currency : '' }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="1"><strong>TOTAL VAT:</strong></td>
                                    <td colspan="5" class="text-right"><strong>{{ totalVAT | currency : '' }}</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="1"><strong>AMOUNT DUE:</strong></td>
                                    <td colspan="5" class="text-right"><strong>{{ linesTotal + totalDiscount + totalVAT | currency : ''}}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row" ng-if="proformaHeader == 0 && proformaLines == 0">
                        <label class="col-md-12 text-center">NO DATA AVAILABLE</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card collapsed-card rounded-0">
                <div class="card-header rounded-0 bg-dark">
                    <h3 class="card-title col-md-9">Purchase Invoice</h3>
                    <label>TOTAL : </label> <span>&#x20B1 {{ PITotalAmount | currency : ''}}</span>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="card-body" style="display: none;">
                    <div class="row mb-3" ng-if="piheader != 0">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <td>PI No</td>
                                <td>Vendor Invoice No</td>
                                <td>Posting Date</td>
                                <td class="text-right">Amount</td>
                            </thead>
                            <tbody>
                                <tr ng-cloak ng-repeat="pi in piheader">
                                    <td>{{ pi.pi_no }}</td>
                                    <td>{{ pi.vendor_invoice_no }}</td>
                                    <td>{{ pi.posting_date }}</td>
                                    <td class="text-right">{{ pi.amt_including_vat | currency : ''}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row" ng-if="PILines != 0">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <td>Item Code</td>
                                <td>Description</td>
                                <td>Qty</td>
                                <td>UOM</td>
                                <td class="text-right">Price</td>
                                <td class="text-right">Amount</td>
                            </thead>
                            <tbody>
                                <tr ng-cloak ng-repeat="pil in PILines">
                                    <td>{{ pil.item_code }}</td>
                                    <td>{{ pil.description }}</td>
                                    <td>{{ pil.qty }}</td>
                                    <td>{{ pil.uom }}</td>
                                    <td class="text-right">{{ pil.direct_unit_cost | currency : ''}}</td>
                                    <td class="text-right">{{ pil.amt_including_vat | currency : ''}}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="1"><strong>TOTAL :</strong></td>
                                    <td colspan="5" class="text-right"><strong>{{ PITotalAmount | currency : '' }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="row" ng-if="piheader == 0 && PILines == 0">
                        <label class="col-md-12 text-center">NO DATA AVAILABLE</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div ng-if="invoiceType">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="row mb-3" ng-if="PI_Header != 0">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-dark">
                            <tr>
                                <th>PI No</th>
                                <th>Vendor Invoice No</th>
                                <th>Posting Date</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ pi_no }}</td>
                                <td>{{ vendor_invoice_no }}</td>
                                <td>{{ posting_date }}</td>
                                <td class="text-right">{{ amt_including_vat | currency : ''}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row" ng-if="PI_Lines != 0">
                    <table class="table table-bordered table-sm">
                        <thead class="bg-dark">
                            <td>Item Code</td>
                            <td>Description</td>
                            <td>Qty</td>
                            <td>UOM</td>
                            <td class="text-right">Price</td>
                            <td class="text-right">Amount</td>
                        </thead>
                        <tbody>
                            <tr ng-cloak ng-repeat="pil in PI_Lines">
                                <td>{{ pil.item_code }}</td>
                                <td>{{ pil.description }}</td>
                                <td>{{ pil.qty }}</td>
                                <td>{{ pil.uom }}</td>
                                <td class="text-right">{{ pil.direct_unit_cost | currency : ''}}</td>
                                <td class="text-right">{{ pil.amt_including_vat | currency : ''}}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="1"><strong>TOTAL :</strong></td>
                                <td colspan="5" class="text-right"><strong>{{ PI_TotalAmount | currency : '' }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row" ng-if="PI_Lines == 0 && PI_Header == 0">
                    <label class="col-md-12 text-center">NO DATA AVAILABLE</label>
                </div>
            </div>
        </div>
    </div>
</div>