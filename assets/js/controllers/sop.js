window.myApp.controller('sop-controller', function($scope, $http, $window) {


    const warningTitle = "<i class='fas fa-exclamation-triangle fa-lg' style='color:#e65c00'></i>";
    const successTitle = "<i class='fas fa-check-circle fa-lg' style='color:#28a745'></i>";
    const infoTitle = "<i class='fas fa-info-circle fa-lg' style='color:#005ce6'></i>";
    const confirmButtonIcon = "<i class='fas fa-thumbs-up'></i> ";
    const cancelButtonIcon = "<i class='fas fa-thumbs-down'></i>";
    const confirmButtonClass = "btn btn-outline-success mr-2";
    const cancelButtonClass = "btn btn-light";

    $scope.crfId = "";
    $scope.sopNo = "";
    $scope.sopId = "";
    $scope.supplierName = "";
    $scope.userType = "";
    $scope.proceedApply = 0;
    $scope.isManagersKey = false;

    $scope.hasChanged = 0 ;
    $scope.variance = 0.00;
    $scope.originalTotalInvoiceAmt = 0 ;
    $scope.totalInvoiceAmount = 0;
    $scope.totalDeductionAmount = 0;
    $scope.totalChargesAmount = 0;
    $scope.totalNetPayableAmount = 0;
    $scope.amountToBeDeducted = 0;
    $scope.inputted = false;
    $scope.deductionAmountInputted = 0;
    $scope.inputQuantity = 0;

    $scope.sopTotalInvoiceAmount = 0;
    $scope.sopTotalDeductionAmount = 0;
    $scope.sopTotalChargesAmount = 0;
    $scope.sopTotalNetPayableAmount = 0;
    $scope.perInvoice = false;
    $scope.perDiminished = false;
    $scope.afterWht = false;
    $scope.sopType = "";

    $scope.invoicesloaded = false;
    $scope.mes = function()
    {
        swal.fire({
            title: infoTitle,
            html: "<b>This transaction is not available at the moment!</b>"
        });
    }

    $scope.setTransaction = function(type)
    {
        $scope.sopType = type;
    }

    $scope.getSuppliers = function() 
    {
        $http({
            method: 'get',
            url: $base_url + 'getSuppliersSop'
        }).then(function successCallback(response) {
            $scope.suppliers = response.data;
        });
    }
    $scope.getSuppliers();

    $scope.getSupplierName = function(selectedId, selection) 
    {
        var selected     = selection.find(({ supplier_id }) => supplier_id === selectedId);
        if( selected !== undefined){
            $scope.supplierName = selected.supplier_name ;           
        }
    }

    $scope.getCustomers = function() 
    {
        $http({
            method: 'get',
            url: $base_url + 'getCustomersSop'
        }).then(function successCallback(response) {

            $scope.customers = response.data;
        });
    }
    $scope.getCustomers();
  
    $scope.sopHead = [];
    $scope.sopDeduction = [];
    $scope.viewSopDeductions = [];


    $scope.getDetail = function(selectedSupplier, suppliers)
    {
        var result     = suppliers.find(({ supplier_id }) => supplier_id === selectedSupplier);
        $scope.hasDeal = result.has_deal;
        $scope.vendorsDeal= "";
        $scope.periodFrom = "";
        $scope.periodTo   = "";
        $scope.supplierWHT = result.inputted_wht;
        if($scope.hasDeal == "0"){ //no deals
            $scope.loadSONos(0);
        } else if($scope.hasDeal == "1"){
            $scope.loadDeals(selectedSupplier);
        }
    }

    $scope.loadDeals = function(supplier)
    {
        $http({
            method: 'post',
            url: $base_url + 'loadVendorsDeal',
            data: { supId: supplier }
        }).then(function successCallback(response) {
            $scope.deals = response.data;
            $scope.resetNewInvoice();
            $scope.selectCustomerNewSop = null;
            $scope.SOPInvoiceData = [{}];
            $scope.DeductionData = [{}];
            $scope.ChargesData = [{}];
            $scope.SONOs = [{}];
            $scope.deductionType = [{}];
            $scope.deductionNames = [{}];
            $scope.chargesType = [{}];
            $scope.totalInvoiceAmount = 0;
            $scope.totalDeductionAmount = 0;
            $scope.totalChargesAmount = 0;
            $scope.totalNetPayableAmount = 0;
            $scope.checkDeduction = false;
            $scope.checkCharges = false;
        });
    }

    $scope.displayToInputDeal = function(selectedDeal, deals)
    {
        // console.log(deals);
        var result = deals.find(({ vendor_deal_head_id }) => vendor_deal_head_id === selectedDeal);
        if(result !== undefined){
            $scope.periodFrom = result.period_from;
            $scope.periodTo   = result.period_to;            
            $scope.loadSONos(selectedDeal);
        }
       
    }

    $scope.loadSONos = function(selectedDeal) 
    {
        $http({
            method: 'post',
            url: $base_url + 'loadSONos',
            data: { supId: $scope.selectSupplierNewSop, dealId: selectedDeal } 
        }).then(function successCallback(response) {
            $scope.invoicesloaded = true;
            $scope.SONOs = response.data.SONOs;
            // $scope.itemMapping = response.data.items;
        });
    }

    $scope.closemyModal6 = function()
    {
        // $scope.SONOs =  null;
        // $scope.itemMapping = null;
    }

    $scope.displayToInput = function(profId, soData) 
    {
        var result = soData.find(({ proforma_header_id }) => proforma_header_id === profId);
        if(result !== undefined){
            $scope.invoiceDate = result.order_date;
            $scope.invoiceAmount = result.amount;
        }
    }

    $scope.addNewInvoiceToTable = function(ev, profIdd, SOData) 
    {
        ev.preventDefault();

        var findSOData = SOData.find(({ proforma_header_id }) => proforma_header_id === profIdd);
        var checkInvoiceDupes = $scope.SOPInvoiceData.find(({ profId }) => profId === profIdd);

        if (checkInvoiceDupes == undefined) {
            $scope.SOPInvoiceData.push({
                'dealId' : $scope.vendorsDeal,
                'profId': findSOData.proforma_header_id,
                'invoiceNo': findSOData.so_no,
                'invoiceDate': findSOData.order_date,
                'poNo': findSOData.po_no,
                'poDate': findSOData.poDate,
                'invoiceAmount': (findSOData.amount * 1).toFixed(2),
                'originalInvoice' : (findSOData.amount * 1).toFixed(2)
            });
            
            $scope.invoiceNo     = null;
            $scope.invoiceDate   = null;
            $scope.invoiceAmount = null;
            $('#myModal2').modal('hide')
            $scope.calculateTotals();      

            // console.log($scope.SOPInvoiceData);
        } else {
            swal.fire({
                title: warningTitle,
                html: "<b>Invoice is already added!</b>"
            });
        }
    }

    $scope.calculateTotals = function() 
    {
        var totalInvoice = 0;
        var totalDed = 0;
        var totalCharge = 0;
        angular.forEach($scope.SOPInvoiceData, function(value, key) {
            if (!isNaN(value.invoiceAmount)) {
                totalInvoice += value.invoiceAmount * 1;
            }
        });
        $scope.totalInvoiceAmount = totalInvoice;

        if ($scope.totalInvoiceAmount != 0.00){
            angular.forEach($scope.DeductionData, function(value, key) {
                if (!isNaN(value.dedAmount)) {
                    totalDed += Number(value.dedAmount);
                }
            });
            $scope.totalDeductionAmount = totalDed;

            angular.forEach($scope.ChargesData, function(value, key) {
                if (!isNaN(value.chargeAmount)) {
                    totalCharge += value.chargeAmount * 1;
                }

            });

            $scope.totalChargesAmount = totalCharge;
            $scope.totalNetPayableAmount = totalInvoice + totalCharge + totalDed; 
        } else {
            $scope.DeductionData = [{}];
            $scope.ChargesData   = [{}];
            $scope.totalDeductionAmount = 0;
            $scope.totalChargesAmount = 0;
            $scope.totalNetPayableAmount = 0;

        }
    
               

        // return $scope.totalInvoiceAmount, $scope.totalDeductionAmount, $scope.totalChargesAmount, $scope.totalNetPayableAmount;

    }

    $scope.calculateToBeDeductedAmount = function() 
    {

        $scope.amountToBeDeducted = $scope.totalInvoiceAmount + $scope.totalChargesAmount + $scope.totalDeductionAmount;
        return $scope.amountToBeDeducted;
    }

    $scope.loadDeductionType = function() 
    {
        $http({
            method: 'get',
            url: $base_url + 'loadDeductionType'
        }).then(function successCallback(response) {
            $scope.deductionType = response.data;
        });
    }

    $scope.loadDeductionNames = function(type) 
    {
        $scope.perInvoice         = false;
        $scope.perDiminished      = false;
        $scope.selectedInvoice    = null;
        $scope.deductionAmount    = 0;
        $scope.amountToBeDeducted = 0 ;
        $scope.deductionAmountInputted = 0;
        $scope.searchedCRF    = null;
        $scope.searchVariance = null;
        $scope.varianceAmt    = 0;

        $http({
            method: 'post',
            url: $base_url + 'loadDeduction',
            data: { typeId: type, supId: $scope.selectSupplierNewSop  }
        }).then(function successCallback(response) {
            $scope.deductionNames = response.data ;    
            $('input[name="customRadio"]').prop('checked', false)  ;         
           
        });
    }

    $scope.getDeductionOrder = function(type){
        $http({
            method: 'post',
            url: $base_url + 'getDeductionOrder',
            data: { typeId: type}
        }).then(function successCallback(response) {
           $scope.order = response.data;
        });

         // !selectedDeductionName || selectDeductionType==2 || selectDeductionType==3 || selectDeductionType==7 || selectDeductionType==14 || selectDeductionType==15 "
            
    }

    $scope.loadDeductionDetails = function(selected,selection)
    {
        $('input[name="customRadio"]').prop('checked', false);
        $scope.deductionAmount    = 0;
        $scope.amountToBeDeducted = 0;
        $scope.inputtedWHT        = "0";
        $scope.useForDisplay      = null ;       
        $scope.selectedInvoice    = null;
        $scope.perInvoice         = false;
        $scope.perDiminished        = false;
        
        var findDeductionData = selection.find(({ deduction_id }) => deduction_id === selected);
        if(findDeductionData !== undefined){
          
            $scope.inputted       = findDeductionData.inputted == 0 ? false : true ; 
            if(findDeductionData.deduction_type_id != 2){ // if dli tax ang deduction type
                $scope.useForDisplay  = findDeductionData.name_used_for_display;  
                $scope.inputtedWHT  = "0" ;             

            }  else { 
                if( $scope.supplierWHT == "0"){
                    $scope.inputtedWHT  = "0";
                    $scope.calculateDeduction(3); //if tax ang deduction type, automatic ang discounted amount ang kuhaan sa computation
                } else {
                    $scope.inputtedWHT  = "1" //inputted ang WHT ex. KSK
                    $scope.inputted = true; //set to true para ma inputan ang Amount(Inputted)
                    $scope.useForDisplay  = findDeductionData.name_used_for_display; 
                }                 
            }    
                     
        }     
    }   

    $scope.searchSOP = function(ev)
    {
        ev.preventDefault();
        var string = $("#sopInvoice").val();
        $scope.searchResult = {};
        if(string == '') 
        {
            $(".search-results").hide();
        }else
        {
            $http({
                method: 'post',
                url: $base_url + 'searchSOP',
                data: { str: string, supId: $scope.selectSupplierNewSop  }
            }).then(function successCallback(response) {
                $scope.searchResult = response.data;
                if($scope.searchResult.length == 0)
                {
                    $scope.hasResults = 0 ;
                    $scope.searchResult.push( { id: "No Results Found" } );
                }else
                {
                    $scope.hasResults = 1 ;
                    $scope.searchResult = response.data;                    
                }                
            });            
        }
    }

    $scope.sopInvLineId = 0;
    $scope.getSOPInv = function(data)
    {
        $("#sopInvoice").val(data.sop_no + "-" + data.so_no );
        $(".search-results").hide();
        $scope.sopInvLineId = data.id;
    }

    $scope.searchVar = function(ev)
    {
        ev.preventDefault();
        var string = $("#searchVariance").val();
        if(string == '') 
        {
            $(".search-results2").hide();
        } else {
            $http({
                method: 'post',
                url: $base_url + 'searchVar',
                data: { str: string, supId: $scope.selectSupplierNewSop  }
            }).then(function successCallback(response) {
                $scope.searchedCRF = response.data;
                if($scope.searchedCRF.length == 0)
                {
                    $scope.hasResults2 = 0 ;
                    $scope.searchedCRF.push( { id: "No Results Found" } );
                }else{
                    $scope.hasResults2 = 1 ;
                    $scope.searchedCRF = response.data;                    
                }                
            });            
        }
    }

    $scope.displayVarAmt = function(data){

        // $("#searchVariance").val(data.crf_no);
        $(".search-results2").hide();
        $scope.searchVarianceId = data.variance_id;
        $scope.varianceAmt      = data.balance;
        $scope.deductionAmountInputted = data.balance * 1;
        $scope.searchVariance   = data.crf_no;
        // console.log($scope.searchVariance);
    }

    $scope.calculateDeduction = function(selectedType) 
    {        
        $scope.perDiminished = false;
        $scope.perQuantity   = false;
        $scope.perInvoice    = false;
        if( !$scope.inputted){
            if(selectedType == 1){ //net
                $scope.perInvoice = false
                var invoiceData = JSON.parse(angular.toJson($scope.SOPInvoiceData));
                $http({
                    method: 'post',
                    url: $base_url + 'forRegDiscount',
                    data: { invoice: invoiceData, supId: $scope.selectSupplierNewSop, dedId: $scope.selectedDeductionName, dealId: $scope.vendorsDeal }
                }).then(function successCallback(response) {
                    $scope.amountToBeDeducted = response.data;
                    $scope.getDeductionAmount($scope.amountToBeDeducted, $scope.selectedDeductionName, invoiceData, $scope.selectSupplierNewSop);
                });

            } else if(selectedType == 2){ //gross not diminishing
                $scope.perInvoice = false;
                $scope.amountToBeDeducted = $scope.totalInvoiceAmount + $scope.totalChargesAmount ;
                $scope.getDeductionAmount($scope.amountToBeDeducted, $scope.selectedDeductionName, '',$scope.selectSupplierNewSop);

            } else if(selectedType == 3){ //gross diminishing
                $scope.perInvoice = false;
                if($scope.selectSupplierNewSop == 67 && $scope.selectDeductionType == 6){ //if ajinomoto and cwo discount
                    $scope.amountToBeDeducted = ($scope.totalInvoiceAmount + $scope.totalChargesAmount + $scope.totalDeductionAmount )/ 1.12;
                } else {
                    $scope.amountToBeDeducted = $scope.totalInvoiceAmount + $scope.totalChargesAmount + $scope.totalDeductionAmount;
                }
                $scope.getDeductionAmount($scope.amountToBeDeducted, $scope.selectedDeductionName,'',$scope.selectSupplierNewSop);
            
            } else if(selectedType == 4){ //select invoice
                $scope.perInvoice = true;
                $scope.amountToBeDeducted = 0;
                $scope.deductionAmount    = 0;
                $scope.selectedInvoice    = null;
            } else if(selectedType == 5){ //per case
                $scope.perQuantity = true;
                $scope.amountToBeDeducted = 0;
                $scope.deductionAmount    = 0;
                $scope.inputQuantity      = 0;
            } else if(selectedType == 6){ //Total Invoice(per Item Disc.) - Diminishing
                $scope.perDiminished = true;
                $scope.amountToBeDeducted = 0;
                $scope.deductionAmount    = 0;
                $scope.inputQuantity      = 0;
            } else if(selectedType == 7){ //after wht nga amount ang basehan sa amount to be deducted
                $scope.afterWht = true;
                $scope.amountToBeDeducted = 0;
                $scope.deductionAmount    = 0;
                $scope.inputQuantity      = 0;
            }
        }
    }

    $scope.QuantityxPrice = function(){
        var qty = $('#inputQuantity').val();
        $scope.getDeductionAmount(qty, $scope.selectedDeductionName,'',$scope.selectSupplierNewSop);
    }

    $scope.perInvoiceDisplayAmount = function(selectedId,selection){

        // console.log($scope.SOPInvoiceData)
        var selected     = selection.find(({ profId }) => profId === selectedId);

        if( selected !== undefined){
            $scope.amountToBeDeducted = selected.invoiceAmount * 1;
            $scope.getDeductionAmount($scope.amountToBeDeducted, $scope.selectedDeductionName,'',$scope.selectSupplierNewSop);
        }
    }

    $scope.perDiminishedAmt = function(selectedId,selection){
        // console.log('selected', selected);
        // console.log('selection', selection);

        var selected     = selection.find(({ dedId }) => dedId === selectedId);
        if( selected !== undefined){
            $scope.amountToBeDeducted = selected.toBeDeducted * 1 + selected.dedAmount;
            $scope.getDeductionAmount($scope.amountToBeDeducted, $scope.selectedDeductionName,'',$scope.selectSupplierNewSop);
        }
    }

    $scope.getDeductionAmount = function(toBeDeducted, dedId, invoiceData, supplier) {

        $http({
            method: 'post',
            url: $base_url + 'calculateDeduction',
            data: { amount: toBeDeducted, discountId: dedId, invoice: invoiceData, supId: supplier  }
        }).then(function successCallback(response) {
            $scope.deductionAmount = response.data;
        });
    }

    $scope.addNewDeductionToTable = function(ev) 
    {        
        ev.preventDefault();    
       
        var selected     = $scope.deductionNames.find(({ deduction_id }) => deduction_id === $scope.selectedDeductionName);
        var lastElement  = $scope.DeductionData.slice(-1);
        var inv          = $("#sopInvoice").val();
        var qty          = $('#inputQuantity').val();
        var newDeduction = {} ;
        var addToTable   = false;
        var amountFlag   = false;
        var msg          = "" ;
        var amtMsg       = "" ;
        var lastOrder    = "" ;

        angular.forEach(lastElement, function(value, key) {
            lastOrder = value.order ;
        });

        if ( selected.inputted == "1" && $scope.inputtedWHT  == "0" ) { //dli inputted ang WHT sa supplier
            if( $scope.deductionAmountInputted != 0 && $scope.deductionAmountInputted !== null  ){
                amountFlag = true;
                amtMsg     = "";

            } else if ( $scope.deductionAmountInputted === null || $scope.deductionAmountInputted == 0) {
                amountFlag = false;
                amtMsg     = "Amount is empty!";
            }

        } else if ( selected.inputted == "0" && $scope.deductionAmount !== null && $scope.inputtedWHT  == "0") {
            if( $scope.deductionAmount != 0 ){
                amountFlag = true;
                amtMsg        = "";

            } else if ( $scope.deductionAmount === null ||  $scope.deductionAmount == 0) {
                amountFlag = false;
                amtMsg        = "Amount is empty!";
            }

        } else if ( selected.inputted == "0" && ($scope.deductionAmount == 0 || $scope.deductionAmount === null)  && $scope.inputtedWHT == "1") { // inputted ang wht sa supplier ex. KSK
            if( $scope.deductionAmountInputted != 0 && $scope.deductionAmountInputted !== null  ){
                amountFlag = true;
                amtMsg     = "";

            } else if ( $scope.deductionAmountInputted === null || $scope.deductionAmountInputted == 0) {
                amountFlag = false;
                amtMsg     = "Amount is empty!";
            }
        }
          
        if( lastOrder === undefined ){

            if ( $scope.order == "after_discount" ||  $scope.order == "after_tax" ) {
                addToTable = false;
                msg        = "Discounts should be first before any other deductions.";                   

            } else {
                addToTable = true;
                msg        = "";
            }

        } else {
                if($scope.selectSupplierNewSop != "11"){ //if not gsmi

                    if( (lastOrder == "before_tax" && $scope.order == "before_tax") || //discount - discount
                        (lastOrder == "before_tax" && $scope.order == "after_discount") || //discount - tax
                        (lastOrder == "after_discount" && $scope.order == "after_discount") || //tax - tax
                        (lastOrder == "after_discount" && $scope.order == "after_tax") || // tax - other deds
                        (lastOrder == "after_tax" && $scope.order == "after_tax") || //other deds - other deds
                        (lastOrder == "before_tax" && $scope.order == "before_after") || //discount - before/after
                        (lastOrder == "after_tax" && $scope.order == "before_after") || //other deds - before/after   
                        (lastOrder == "after_discount" && $scope.order == "before_after") || //tax - before/after
                        (lastOrder == "before_after" && $scope.order == "before_after") || //before/after - before/after 
                        (lastOrder == "before_after" && $scope.order == "after_discount" ) || //before/after - tax
                        (lastOrder == "before_after" && $scope.order == "before_tax")){   //before/after - discount            
                            //#region 
                                //discounts - before_tax
                                //other deds - after_tax
                                //deductions that can be both before & after tax placement - before_after
                            //#endregion
                            addToTable = true;
                            msg        = "";

                    } else if( lastOrder == "before_tax" && $scope.order == "after_tax" ){ // discount - other deds
                            addToTable = false;
                            msg        = "Other deductions should follow after tax.";

                    } else if( lastOrder == "after_discount" && $scope.order == "before_tax" ) { // tax - discount
                            addToTable = false;
                            msg        = "Discounts should be first before any other deductions.";

                    } else if( lastOrder == "after_tax" && $scope.order == "before_tax" ){ //  other deds - discount
                            addToTable = false;
                            msg        = "Discounts should be first before any other deductions.";

                    } else if( lastOrder == "after_tax" && $scope.order == "after_discount" ){ // other deds - tax
                            addToTable = false;
                            msg        = "Tax should follow after discounts.";
                    }

                } else { //gsmi
                    addToTable = true;
                    msg        = "";
                }
                
        }

        // console.log('table', addToTable, 'msg', msg);

        if(addToTable && amountFlag){
            newDeduction.dedId     = selected.deduction_id;
            newDeduction.order     = $scope.order;
            // newDeduction.dedAmount = selected.inputted == 1 ? $scope.deductionAmountInputted.toFixed(2) * -1 : $scope.deductionAmount.toFixed(2) * -1;
            if( selected.inputted == 1 && ($scope.inputtedWHT == "0" || $scope.inputtedWHT == "1" )){
                newDeduction.dedAmount =    $scope.deductionAmountInputted.toFixed(2) * -1;
            } else  if( selected.inputted == 0 && $scope.inputtedWHT == "0"){
                newDeduction.dedAmount =    $scope.deductionAmount.toFixed(2) * -1;
            } else  if( selected.inputted == 0 && $scope.inputtedWHT == "1"){
                newDeduction.dedAmount =    $scope.deductionAmountInputted.toFixed(2) * -1;
            }

            if(!$scope.perQuantity){
                if( ($scope.sopInvoice === undefined || $scope.sopInvoice === null) && ($scope.remarksDed === undefined || $scope.remarksDed === null) && ($scope.searchVariance === undefined || $scope.searchVariance === null)  ){
                    newDeduction.varId     = 0;
                    newDeduction.sopInvId  =  0  ;
                    newDeduction.dedName   = selected.name_used_for_display + ' ' +  '(' + $scope.numberWithCommas($scope.amountToBeDeducted.toFixed(2)) + ')';
                    newDeduction.toBeDeducted = $scope.amountToBeDeducted.toFixed(2);
                } else if( ($scope.sopInvoice === undefined || $scope.sopInvoice === null) && ($scope.remarksDed !== undefined || $scope.remarksDed !== null) && ($scope.searchVariance === undefined || $scope.searchVariance === null) ) {
                    newDeduction.varId     = 0;
                    newDeduction.sopInvId  =  0  ;
                    newDeduction.toBeDeducted = 0;
                    newDeduction.dedName   = selected.name_used_for_display + ' ' +  $scope.remarksDed;
                } else if( ($scope.sopInvoice !== undefined || $scope.sopInvoice !== null) && ($scope.remarksDed === undefined || $scope.remarksDed === null) && ($scope.searchVariance === undefined || $scope.searchVariance === null) ){
                    newDeduction.varId     = 0;
                    newDeduction.toBeDeducted = 0;
                    newDeduction.sopInvId  = $scope.sopInvLineId   ;
                    newDeduction.dedName   = selected.name_used_for_display + ' ' + inv ; 
                } else if( ($scope.sopInvoice !== undefined || $scope.sopInvoice !== null) && ($scope.remarksDed !== undefined || $scope.remarksDed !== null) && ($scope.searchVariance === undefined || $scope.searchVariance === null) ){
                    newDeduction.varId     = 0;
                    newDeduction.toBeDeducted = 0;
                    newDeduction.sopInvId  = $scope.sopInvLineId   ;
                    newDeduction.dedName   = selected.name_used_for_display + ' ' + inv + ' '  +  $scope.remarksDed;
                } else if( ($scope.sopInvoice === undefined || $scope.sopInvoice === null ) && ($scope.searchVariance !== undefined || $scope.searchVariance !== null || $scope.searchVariance != '') ){
                    newDeduction.varId     = $scope.searchVarianceId ; // mention variance from previous matching
                    newDeduction.sopInvId  = 0;
                    newDeduction.toBeDeducted = 0;
                    newDeduction.dedName   = selected.name_used_for_display + ' ' + $scope.searchVariance + '(' + $scope.varianceAmt + ')' ;

                }
            } else {
                newDeduction.varId     =  0  ;
                newDeduction.sopInvId  =  0  ;
                newDeduction.dedName   = selected.name_used_for_display + ' X ' + qty;
            }

            if (selected.repeat == 1) {

                $scope.DeductionData.push(newDeduction);


            } else {
                var checkDupes = $scope.DeductionData.find(({ dedId }) => dedId === $scope.selectedDeductionName);

                if (checkDupes === undefined) {
                    $scope.DeductionData.push(newDeduction);
                } else {
                    swal.fire({
                        title: warningTitle,
                        html: "<b>Deduction is already added!</b>"
                    });
                }
            }

        } else if( addToTable && !amountFlag ){
            swal.fire({
                title: warningTitle,
                html: "<b>Unable to add deduction.</b> <br> " + amtMsg 
            });
      
        } else {
            swal.fire({
                title: warningTitle,
                html: "<b>Unable to add deduction.</b> <br> " + msg 
            });
        }

        // console.log($scope.DeductionData);
        
        $scope.resetNewDeduction();
        $('#myModal3').modal('hide'); 
        $scope.calculateTotals();
    }

    $scope.numberWithCommas = function(string) 
    {      
        return string.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    $scope.resetNewInvoice = function()
    {    
        $scope.invoiceNo     = null;
        $scope.invoiceDate   = null;
        $scope.invoiceAmount = null;
        $scope.vendorsDeal   = null;
        $scope.invoicesloaded = false;
    }

    $scope.resetNewDeduction = function()
     {
        $scope.selectDeductionType     = null
        $scope.selectedDeductionName   = null
        $scope.remarksDed              = null
        $scope.deductionAmount         = null
        $scope.deductionAmountInputted = null
        $scope.amountToBeDeducted      = null
        $scope.useForDisplay           = null
        $scope.sopInvoice              = null
        $scope.selectedInvoice         = null
        $scope.perInvoice              = false
        $scope.perQuantity             = false
        $scope.afterWht                = false;
        $scope.searchedCRF             = null;
        $scope.searchVariance          = null;
        $scope.varianceAmt             = 0;
        $("#sopInvoice").val("");
        $('input[name="customRadio"]').prop('checked', false);
    }

    $scope.loadChargesType = function() 
    {
        $http({
            method: 'get',
            url: $base_url + 'loadChargesType'
        }).then(function successCallback(response) {
            $scope.chargesType = response.data;
        });
    }

    $scope.displayToInputCharge = function()
    {
        $scope.chargeRemarks = null;
        $scope.chargeAmountInputted = null;
    }

    $scope.addNewChargeToTable = function(ev) 
    {
        ev.preventDefault();

        var findSelectedCharge = $scope.chargesType.find(({ charges_id }) => charges_id === $scope.selectChargeType);
        $scope.ChargesData.push({
            'chargeId': $scope.selectChargeType,
            'description': findSelectedCharge.charges_type + ' - ' + $scope.chargeRemarks,
            'chargeAmount': $scope.chargeAmountInputted
        });

        $scope.resetNewCharge();
        $("#myModal4").modal('hide');
        $scope.calculateTotals();
    }

    $scope.resetNewCharge = function() 
    {
        $scope.selectChargeType = null;
        $scope.chargeRemarks = null;
        $scope.chargeAmountInputted = null;
    }

    $scope.checkcharactercount = function()
    {
        if ($scope.useForDisplay.length + $scope.remarksDed.length > 70 ) {
            Swal.fire({
                title: warningTitle,
                html: '<b> Remarks(incl. Name used for Display) exceeded character limit! </b>'
            })
            $scope.remarksDed = "";
        }
    }

    $scope.submitNewSop = function(ev) 
    {
        ev.preventDefault(); 
        $scope.checkInvoiceAmountChanges();

        if( $scope.hasChanged > 0 ){
            var msg   = "";
            var level = "";
            if( $scope.variance > -1.00 && $scope.variance < 1.00 ){ 
                level = "1";
                msg = "<b> Invoice amount has been changed! </b> <br> Variance : " + $scope.variance;

            } else if($scope.variance <= 5.00 || $scope.variance <= -1.00 ){
                level = "2";
                msg = "<b> Invoice amount has been changed! </b> <br> Manager's key is needed to proceed. <br> Variance : " + $scope.variance;

            } else if($scope.variance == 0.00){
                level = "3";
            } else if($scope.variance > 5.00){
                level = "4";
            }

            if( level != "4"){
                Swal.fire({
                    title: level == "3" ? infoTitle : warningTitle,
                    html: msg,
                    buttonsStyling: false,
                    showCancelButton: true,
                    confirmButtonText: confirmButtonIcon + ' Proceed',
                    cancelButtonText: cancelButtonIcon,
                    customClass: { confirmButton: confirmButtonClass, cancelButton: cancelButtonClass } 
                }).then((result) => {
                    if(result.isConfirmed) {                        
                        // if(level == "2"){
                        //         $("#managersKey").modal("show");                       
                        // } else if ( level == "1" || level == "3") {
                            $scope.proceedSOP();
                        // }
                    } else if(result.dismiss){
                        Swal.fire({
                            title: infoTitle,
                            html: '<b> You opted not to proceed! </b>'
                        });
                    }
                })
            } else {
                Swal.fire({
                    title: warningTitle,
                    html: '<b> More than 5.00 variance is not allowed! </b> <br> Variance : ' + $scope.variance
                });             
            } 

        }  else {
            $scope.proceedSOP();
        }      
    }

    $scope.proceedSOP = function(){
        var InvoiceData    = JSON.parse(angular.toJson($scope.SOPInvoiceData));
        var ChargeData     = JSON.parse(angular.toJson($scope.ChargesData));
        var DeductionsData = JSON.parse(angular.toJson($scope.DeductionData));;

        // if ($scope.totalInvoiceAmount != 0.00 && $scope.totalDeductionAmount != 0.00 && $scope.totalNetPayableAmount != 0.00 && $scope.sopdate !== undefined) {
        if ($scope.totalInvoiceAmount != 0.00 && $scope.totalNetPayableAmount != 0.00 && $scope.sopdate !== undefined) {
            Swal.fire({
                title: warningTitle,
                html: "<b>Are you sure to submit SOP ? </b>",
                buttonsStyling: false,
                showCancelButton: true,
                confirmButtonText: confirmButtonIcon,
                cancelButtonText: cancelButtonIcon,
                customClass: { confirmButton: confirmButtonClass, cancelButton: cancelButtonClass }
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        type: "POST",
                        url: $base_url + 'submitSOP',
                        data: {
                            supId: $scope.selectSupplierNewSop,
                            cusId: $scope.selectCustomerNewSop,
                            sopdate : $scope.sopdate,
                            invoiceAmount: $scope.totalInvoiceAmount,
                            chargesAmount: $scope.totalChargesAmount,
                            dedAmount: $scope.totalDeductionAmount,
                            netAmount: $scope.totalNetPayableAmount,
                            invoice: InvoiceData,
                            deduction: DeductionsData,
                            charges: ChargeData
                        },
                        cache: false,
                        success: function(response) {
                            if (response.info == "success") {
                                Swal.fire({
                                    title: successTitle,
                                    html: '<b> ' + response.message + ' </b>'
                                }).then(function() {
                                    window.open($base_url + 'files/Reports/SOP/' + response.file);
                                    location.reload();
                                })
                            
                            } else if (response.info == "error") {
                                Swal.fire({
                                    title: warningTitle,
                                    html: '<b> ' + response.message + ' </b>'
                                })

                            } else if (response.data == "incomplete") {
                                Swal.fire({
                                    title: warningTitle,
                                    html: '<b> Failed to save SOP! </b>'
                                })
                            }
                        }
                    });
                }
            })

        } else {
            Swal.fire({
                title: warningTitle,
                html: '<b> No Data to Save! </b>'
            })
        }
    }

    $scope.resetNewSOP = function() 
    {
        $scope.selectSupplierNewSop = null;
        $scope.selectCustomerNewSop = null;
        $scope.vendorsDeal          = null;
        $scope.periodFrom           = null;
        $scope.periodTo             = null;
        $scope.SOPInvoiceData       = [{}];
        $scope.DeductionData        = [{}];
        $scope.ChargesData          = [{}];
        $scope.SONOs                = [{}];
        $scope.deductionType        = [{}];
        $scope.deductionNames       = [{}];
        $scope.chargesType          = [{}];
        $scope.totalInvoiceAmount   = 0;
        $scope.totalDeductionAmount = 0;
        $scope.totalChargesAmount   = 0;
        $scope.totalNetPayableAmount= 0;
        $scope.checkDeduction       = false;
        $scope.checkCharges         = false;
        $scope.deals                = null;
    }

    $scope.loadCwoSop = function() 
    {
        $('#loading').show();
        if ($.fn.DataTable.isDataTable('#cwoSopTable')) {
            $('#cwoSopTable').DataTable().clear();
            $('#cwoSopTable').DataTable().destroy();
            $scope.cwoSopHead = [];
            $scope.cwoSopList = false;
        }

        $http({
            method: 'post',
            url: $base_url + 'loadCwoSop',
            data: { supId: $scope.selectSupplier, cusId: $scope.selectCustomer, from: $scope.dateFrom, to: $scope.dateTo   }
        }).then(function successCallback(response) {
            $('#loading').hide();
            if (response.data != '') {
                $(document).ready(function() { $('#cwoSopTable').DataTable(); });
                $scope.cwoSopList = true;
                $scope.cwoSopHead = response.data;
            } else {
                swal.fire({
                    title: infoTitle,
                    html: "<b> No SOP Transactions for this supplier and location! </b>"
                })
                $scope.cwoSopList = false;
            }
        });
    }

    $scope.viewSopDetails = function(data) 
    {
        $scope.sopId       = data.sop_id;
        $scope.sopSupplier = data.supplier_name;
        $scope.sopCustomer = data.customer_name;
        $scope.sopDate     = data.sop_date;
        $scope.sopNumber   = data.sop_no;
        $scope.status      = data.statuss;

        $http({
            method: 'post',
            url: $base_url + 'loadSopDetails',
            data: { sopId: data.sop_id }
        }).then(function successCallback(response) {

            if (response.data.invoice.length != 0) {
                $scope.sopInvoice = response.data.invoice;
                var invoiceTotal = 0;
                angular.forEach($scope.sopInvoice, function(value, key) {
                    if (!isNaN(value.invoice_amount)) {

                        invoiceTotal += value.invoice_amount * 1;
                    }
                });
                $scope.sopTotalInvoiceAmount = invoiceTotal;
            }

            if (response.data.deduction.length != 0) {
                $scope.sopDeduction = response.data.deduction;
                var deductionTotal = 0;
                angular.forEach($scope.sopDeduction, function(value, key) {
                    if (!isNaN(value.deduction_amount)) {

                        deductionTotal += value.deduction_amount * 1;
                    }
                });
                $scope.sopTotalDeductionAmount = deductionTotal;
            }

            if (response.data.charges.length != 0) {
                $scope.sopCharges = response.data.charges;
                var chargesTotal = 0;
                angular.forEach($scope.sopCharges, function(value, key) {
                    if (!isNaN(value.charge_amount)) {

                        chargesTotal += value.charge_amount * 1;
                    }
                });
                $scope.sopTotalChargesAmount = chargesTotal;
            }

            $scope.sopTotalNetPayableAmount = $scope.sopTotalInvoiceAmount + $scope.sopTotalDeductionAmount + $scope.sopTotalChargesAmount;

        });
    }

    $scope.tagAsAudited = function(ev)
    {
        ev.preventDefault();

        Swal.fire({
            title: warningTitle,
            html: "<b>Are you sure you want to tag this as AUDITED ? </b>",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "<i class='fas fa-thumbs-up'></i> Yes",
            cancelButtonText: "<i class='fas fa-thumbs-down'></i> No",
            customClass: { confirmButton: "btn btn-outline-success",cancelButton: "btn btn-light" }
        }).then((result) => {
            if (result.isConfirmed) 
            {
                $http({
                    method: 'post',
                    url: $base_url + 'tagAsAudited',
                    data: { sopId: $scope.sopId }
                }).then(function successCallback(response) {
                   var icon = "";
                   if(response.data.info == "Success"){
                       icon = successTitle;
                   } else if(response.data.info == "Error"){
                       icon = warningTitle;
                   }
                   Swal.fire({
                        title: icon,
                        html: '<b> ' + response.data.message +  ' </b>'
                    }).then(function(){
                        location.reload();
                    })
                });
            }
        })
    }

    $scope.setEnabled = function(data,index){
      
        $scope.SOPInvoiceData[index].enabled = data.enabled;
       
    }  

    $scope.checkInvoiceAmountChanges = function(){
        $scope.hasChanged = 0;
        $scope.variance   = 0.00;
        angular.forEach($scope.SOPInvoiceData, function(value, key) {
            if (!isNaN(value.invoiceAmount)) {
                if(value.invoiceAmount != value.originalInvoice){
                    if($scope.hasChanged >= 0){
                        $scope.hasChanged += 1;
                        $scope.variance = (value.invoiceAmount * 1 - value.originalInvoice  * 1 ).toFixed(2) ;
                    } else {
                        $scope.hasChanged -= 1;
                        $scope.variance = (value.invoiceAmount * 1 - value.originalInvoice  * 1 ).toFixed(2) ;
                    }                                    
                } 
            }               
        });    
    }

    $scope.managersKey = function(ev){
        ev.preventDefault();

        $.ajax({
            type: "POST",
            url: $base_url + 'managersKey',
            data: { user: $scope.user, pass: $scope.pass },
            cache: false,
            success: function(response) {
                if (response != null) {
                    $("#managersKey").modal("hide");
                    $scope.proceedSOP();                  
                } else {    
                    $scope.isManagersKey = false;  
                    Swal.fire({
                        title: warningTitle,
                        html: "<b>Unauthorized Account!</b>"
                    }).then(function() {
                        // location.reload();
                        $("#managersKey").modal("hide");
                        
                    })                   
                }
            }
        });
    }

    $scope.validateNumber = function(ev){
        var e = ev || window.event;
        var key = e.keyCode || e.which;

        if (!e.shiftKey && !e.altKey && !e.ctrlKey &&
            // numbers   
            key >= 48 && key <= 57 ||
            // Numeric keypad
            key >= 96 && key <= 105 ||
            // Backspace and Tab and Enter
            key == 8 || key == 9 || key == 13 ||
            // Home and End
            key == 35 || key == 36 ||
            // left and right arrows
            key == 37 || key == 39 ||
            // Del     and      Ins     and decimal point
            key == 46 || key == 45 || key == 110) {
                // input is VALID

        } else {
            // input is INVALID
            e.returnValue = false;
            if (e.preventDefault) e.preventDefault();
        }
    }

    // # utility - region 
    $scope.generateSOPs = () => {
        
        if ($.fn.DataTable.isDataTable('#soptable')) {
            $scope.tableShow = false;
            $scope.table.destroy();
            $scope.documents = [];
        }

        var data = { type : $scope.transactionType, supplierSelect: $scope.supplierSelect, locationSelect: $scope.locationSelect  }
        $http({
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8' },
            method: 'POST',
            url: $base_url + 'getSOPs',
            data: $.param(data)
        }).then(function successCallback(response) {
            if(response.data != ''){
                $scope.tableShow = true;
                $scope.sopss = response.data;
                $(document).ready(function() { 
                    setTimeout(function() {
                        $scope.table = $('#soptable').DataTable({
                            destroy: true
                        });
                    }, 100)
                });                
                
            } else {
                $scope.tableShow = false;
                swal.fire({
                    title: infoTitle,
                    html: "<b> No SOP for this supplier and/or location! </b>"
                })
            }
            
        });
    }

    $scope.changeStatus = (data,status) => {
        var sopId = data.sop_id;      
        
        Swal.fire({
            title: warningTitle,
            html: "Are you sure to change status to <b>" + status + " ? </b>",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: confirmButtonIcon,
            cancelButtonText: cancelButtonIcon,
            customClass: { confirmButton: confirmButtonClass, cancelButton: cancelButtonClass }
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    type: "POST",
                    url: $base_url + 'changeSOPStatus',
                    data: { sopId: sopId, status: status },
                    cache: false,
                    success: function(response) {                       
                        var icon = "";
                        if(response.info == "Success"){
                            icon = successTitle;
                        } else if(response.info == "Error"){
                            icon = warningTitle;
                        }
                        Swal.fire({
                            title: icon,
                            html: '<b> ' + response.message +  ' </b>'
                        }).then(function(){
                            $scope.generateSOPs();
                        })
                    }
                });
            }
        })
    }
    //#utility - endregion

    $(document).ready(function() {
        $('#openBtn').click(() => $('#myModal').modal({
            show: true
        }));

        $(document).on('show.bs.modal', '.modal', function() {
            const zIndex = 1040 + 10 * $('.modal:visible').length;
            $(this).css('z-index', zIndex);
            setTimeout(() => $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack'));
        });

        $("#dateFrom").datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            maxDate: "0d",
            dateFormat: 'yy-mm-dd',
            showAnim: 'slideDown',
            changeMonth: true,
            changeYear: true
        });
        $("#dateTo").datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            maxDate: "0d",
            dateFormat: 'yy-mm-dd',
            showAnim: 'slideDown',
            changeMonth: true,
            changeYear: true
        });
        $("#sopdate").datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            maxDate: "0d",
            dateFormat: 'yy-mm-dd',
            showAnim: 'slideDown',
            changeMonth: true,
            changeYear: true
        });
        
    });   

    

});