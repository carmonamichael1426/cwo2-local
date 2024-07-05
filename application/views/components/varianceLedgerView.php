<div ng-if="paymentType">
    <div class="row">
        <label class="col-md-2 text-right">CRF NO: </label>
        <div class="col-md-5"><span ng-bind="crfNo"></span></div>
        <label class="col-md-2 text-right">CRF Amount: </label>
        <div class="col-md-2"><span>&#x20B1 {{ crfAmount | currency: ''}}</span></div>
    </div>
    <div class="row">
        <label class="col-md-2 text-right">CRF Date: </label>
        <div class="col-md-5"><span ng-bind="crfDate"></span></div>
        <label class="col-md-2 text-right">Paid Amount: </label>
        <div class="col-md-2"><span>&#x20B1 {{ paidAmount | currency: '' }}</span></div>
        
    </div>
    <div class="row">
        <label class="col-md-2 text-right">Collectors Name: </label>
        <div class="col-md-5"><span ng-bind="collectorsName"></span></div>
        <label class="col-md-2 text-right">Original Variance Amount: </label>
        <div class="col-md-2"><span>&#x20B1 {{ origDebit | currency: '' }}</span></div>
        
    </div>
    <div class="row">
        <label class="col-md-2 text-right">Remarks: </label>
        <div class="col-md-5"><span ng-bind="remarks"></span></div>
        <label class="col-md-2 text-right">Current Variance Amount: </label>
        <div class="col-md-2"><span>&#x20B1 {{ varianceAmt | currency: '' }}</span></div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="card collapsed-card rounded-0">
                <div class="card-header rounded-0 bg-dark">
                    <h3 class="card-title col-md-9">ADJUSTMENT <span class="text-muted">(Debit)</span></h3>
                    <label>TOTAL : </label> <span>&#x20B1 {{ adjustmentTotal | currency : ''}}</span>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="card-body" style="display: none;">
                    <div class="row mb-2" ng-if="adjustments != 0">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <td class="text-center">Adjustment No</td>
                                <td class="text-center">Date</td>
                                <td class="text-center">Deduction</td>
                                <td class="text-center">Amount</td>
                            </thead>
                            <tbody>
                                <tr ng-cloak ng-repeat="adj in adjustments">
                                    <td class="text-center">{{ adj.adj_no }}</td>
                                    <td class="text-center">{{ adj.adj_date }}</td>
                                    <td class="text-center">{{ adj.description }}</td>
                                    <td class="text-right">{{ adj.amount | number:2 }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="1"><strong>TOTAL :</strong></td>
                                    <td colspan="3" class="text-right"><strong>{{ adjustmentTotal | currency : '' }}</strong></td>
                                </tr>                               
                            </tfoot>
                        </table>
                    </div>
                    <div class="row" ng-if="adjustmentTotal == 0">
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
                    <h3 class="card-title col-md-9">MENTIONS FROM OTHER SOP <span class="text-muted">(Credit)</span></h3>
                    <label>TOTAL : </label> <span>&#x20B1 {{ mentionsTotal | currency : ''}}</span>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
                <div class="card-body" style="display: none;">
                    <div class="row mb-2" ng-if="mentions != 0">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <td class="text-center">SO No</td>
                                <td class="text-center">Date</td>
                                <td class="text-center">Deduction</td>
                                <td class="text-center">Amount</td>
                            </thead>
                            <tbody>
                                <tr ng-cloak ng-repeat="mn in mentions">
                                    <td class="text-center">{{ mn.sop_no }}</td>
                                    <td class="text-center">{{ mn.date_created }}</td>
                                    <td class="text-center">{{ mn.description }}</td>
                                    <td class="text-right">{{ mn.deduction_amount | number:2 }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="1"><strong>TOTAL :</strong></td>
                                    <td colspan="3" class="text-right"><strong>{{ mentionsTotal | currency : '' }}</strong></td>
                                </tr>                               
                            </tfoot>
                        </table>
                    </div>
                    <div class="row" ng-if="mentionsTotal == 0">
                        <label class="col-md-12 text-center">NO DATA AVAILABLE</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card rounded-0">
                <div class="card-header rounded-0 bg-dark">
                    <h3 class="card-title col-md-9">BALANCE</span></h3>
                    <label>TOTAL : </label> <span>&#x20B1 {{ balance | currency : ''}}</span>
                </div>  
            </div>            
        </div>
    </div>
   
   
    
</div>
