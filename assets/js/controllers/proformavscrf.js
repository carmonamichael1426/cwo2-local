window.myApp.controller('proformavscrf-controller', function($scope, $http, $window) {
    const warningTitle = "<i class='fas fa-exclamation-triangle fa-lg' style='color:#e65c00'></i>";
    const successTitle = "<i class='fas fa-check-circle fa-lg' style='color:#28a745'></i>";
    const infoTitle    = "<i class='fas fa-info-circle fa-lg' style='color:#005ce6'></i>";
    const confirmButtonIcon = "<i class='fas fa-thumbs-up'></i>";
    const cancelButtonIcon  = "<i class='fas fa-thumbs-down'></i>";

    const ToastSuccess = Swal.mixin({
                            toast: true,
                            position: 'top-right',
                            iconColor: 'green',
                            customClass: { popup: 'toasts-top-right' },
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true })
    const ToastError = Swal.mixin({
                            toast: true,
                            position: 'top-right',
                            iconColor: 'red',
                            customClass: { popup: 'toasts-top-right'},
                            showConfirmButton: false,
                            timer: 1500,
                            timerProgressBar: true})

    $scope.hiddenSupplier = "";
    $scope.crfId = "";
    $scope.crfNo = "";
    $scope.crfAmount = "";
    $scope.proformaId = "";
    $scope.proceedMatch = 0; /* for matching    */
    $scope.proceedApply = 0; /* for tagging proforma    */
    $scope.hasResults = 0;   /* for searching untagged proforma  */

    $scope.getSuppliers = function() 
    {
        $http({
            method: 'get',
            url: $base_url + 'getSuppliersForCRF'
        }).then(function successCallback(response) {
            $scope.suppliers = response.data;
        });
    }
    $scope.getSuppliers();

    $scope.getSop = function(supplier, customer)
    {
        $http({
            method: 'post',
            url: '../transactionControllers/proformavscrfcontroller/getSop/' + supplier + '/' + customer
        }).then(function successCallback(response) {
            $scope.sops = response.data;
        });
    }

    $scope.getCustomers = function() 
    {
        $http({
            method: 'get',
            url: $base_url +  'getCustomersForCRF'
        }).then(function successCallback(response) {
            $scope.customers = response.data;
        });
    }
    $scope.getCustomers();

    $scope.getCrfs = function() 
    {
        $('#loading').show();
        if ($.fn.DataTable.isDataTable('#crfTable')) {
            $('#crfTable').DataTable().clear();
            $('#crfTable').DataTable().destroy();
            $scope.crf = [];
            $scope.pendingCrf = false;
        }

        $http({
            method: 'POST',
            url: $base_url + 'getCrfs',
            data   : { supId: $scope.supplierName, cusId: $scope.locationName, from: $scope.dateFrom, to: $scope.dateTo  }
        }).then(function successCallback(response) {
            $('#loading').hide();
            if(response.data != ''){                
                $(document).ready(function() { $('#crfTable').DataTable(); }); 
                $scope.crf = response.data;               
                $scope.pendingCrf = true;

            }else {
                swal.fire({
                    title: infoTitle,
                    html: "<b> No Pending Matches for this supplier and location! </b>"
                   })
               $scope.pendingCrf = false;
              
            }
           
        });
    }

    $scope.checkExt = function(element) {
        $scope.crfFile = element.files[0];
        var filename = $scope.crfFile.name;
        var index = filename.lastIndexOf(".");
        var strsubstring = filename.substring(index, filename.length);
        if (strsubstring == ".txt" || strsubstring == ".TXT") {
            console.log("allowed");

        } else {

            Swal.fire({
                title: warningTitle,
                html: "<b> Invalid file extension! </b>"
            }).then(function(){
                location.reload();
            })
        }
    }

    $scope.uploadCrf = function(ev) {

        
        ev.preventDefault();

        var form = $("#uploadProCrf")[0];
        var formData = new FormData(form);

        $.ajax({
            type: "POST",
            url: $base_url + 'uploadCrf',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function(response) {
            
                if (response == "success") {
                    Swal.fire({
                        title: successTitle,
                        html: '<b> CRF is uploaded successfully! </b>'               
                    }).then(function(){
                        location.reload();
                    })

                } else if (response == "exists") {
                    Swal.fire({
                        title: warningTitle,
                        html: "<b> CRF already exists!</b>"
                    }).then(function(){
                        location.reload();
                    })
                    
                } else if (response == "incomplete"){
                    Swal.fire({
                        title: warningTitle,
                        html: "<b> Uploading is incomplete!</b>"
                    }).then(function(){
                        location.reload();
                    })                    
                }
            }
        });
        
    }

    $scope.applyProforma = function(data) {
        
        $scope.hiddenSupplier = data.supplier_id;
        $scope.crfId     = data.crf_id;
        $scope.crfNo     = data.crf_no;
        $scope.crfDate   = data.crf_date;
        $scope.crfAmount = data.crf_amt;
        $scope.sopNo = data.sop_no
        $scope.sName = data.supplier_name;
        $scope.hasDeal = data.has_deal ;
        if( data.status == "MATCHED" && data.audited == "1" && data.crfvspi == "1"){
            $scope.crfStatus = 1
        } else {
            $scope.crfStatus = 0
        }
        $scope.loadApplied();
        $("#applyProformaToCrf").modal('show');
          
    }

    $scope.searchProf = function(ev)
    {
        ev.preventDefault();
        var string = $("#searchProforma").val();
        if(string == '') 
        {
            $(".search-results").hide();
            $scope.proceedApply = 0;
        }else
        {
            $http({
                method: 'post',
                url:'../transactionControllers/proformavscrfcontroller/getUnAppliedProforma/' + $scope.hiddenSupplier + '/' + $scope.crfId,
                data: { str: string }
            }).then(function successCallback(response) {
                $scope.searchResult = response.data;
                if($scope.searchResult.length == 0)
                {
                    $scope.hasResults = 0 ;
                    $scope.proceedApply = 0;
                    $scope.searchResult.push( { proforma_header_id: "No Results Found" } );
                }else
                {
                    $scope.hasResults = 1 ;
                    $scope.searchResult = response.data;                    
                }                
            });            
        }
        
    }

    $scope.getProf = function(prof) 
    {
        $("#searchProforma").val(prof.proforma_header_id + "-" + prof.so_no + "-" + prof.po_no + "-" + prof.posting_date);
        $(".search-results").hide();
        $scope.proformaId = prof.proforma_header_id;
        $scope.proceedApply = 1;
    }

 
    $scope.loadApplied = function() 
    {
        $http({
            method: 'post',
            url: '../transactionControllers/proformavscrfcontroller/getAppliedProforma/' + $scope.crfId + '/' + $scope.hiddenSupplier
        }).then(function successCallback(response) {
    
            if (response.data.profs.length > 0) {
                $scope.applied = response.data.profs;
                $scope.proceedMatch = 1;

            } else {
                $scope.applied = [];
                $scope.applied.push( { proforma_code: "No Data", delivery_date: "No Data", po_no: "No Data", item_total : '0.00', add_less : '0.00', total: '0.00' } );
                $scope.proceedMatch = 0;
            }
            $scope.vendorsDeal = response.data.deal;
            $scope.resetProfVsCrf();
        });
    }

    $scope.displayVendorsdDealToInput = function(dealId, deals)
    {
        var result        = deals.find(({ vendor_deal_head_id }) => vendor_deal_head_id === dealId);
        $scope.periodFrom = result.period_from ;
        $scope.periodTo   = result.period_to ;        
    }

    $scope.applyProf = function() 
    {      
        var id = $scope.proformaId; /* from getProf function */
        if (angular.isUndefined(id) || !id) {
            Swal.fire({
                title: warningTitle,
                html: '<b>' + "No Data to Tag!" + '</b>'
            })
        } else {
            Swal.fire({
                title: warningTitle,
                html: "<b>Are you sure to <strong>TAG </strong> this pro-forma ? </b>",
                buttonsStyling: false,            
                showCancelButton: true,
                confirmButtonText: confirmButtonIcon + " Yes",
                cancelButtonText: cancelButtonIcon + " No",
                customClass:{ confirmButton: "btn btn-outline-success", cancelButton: "btn btn-light" }  
            }).then((result) => {
                if (result.isConfirmed) 
                {
                    $http({
                        method: 'post',
                        url: '../transactionControllers/proformavscrfcontroller/applyProforma/' + $scope.crfId + '/' + $scope.hiddenSupplier,
                        data: { id: id }
                    }).then(function successCallback(response) {
                        if (response.data == "success") {                           
                            ToastSuccess.fire({
                                icon: 'success',
                                title: 'Pro-forma is tagged under this CRF!'
                            })
        
                        } else {                           
                            ToastError.fire({
                                icon: 'error',
                                title: 'Pro-forma is already tagged!'
                            })
                        }
                        $scope.loadApplied();
                        $("#searchProforma").val("");
                        $scope.proceedApply = 0;
                    });                
                }
            });
        }
    }

    $scope.untagProforma = function(data)
    {
        Swal.fire({
            title: warningTitle,
            html: "<b>Are you sure to <strong> UNTAG " + data.proforma_code +" </strong> ? </b>",
            buttonsStyling: false,            
            showCancelButton: true,
            confirmButtonText: confirmButtonIcon + " Proceed",
            cancelButtonText: cancelButtonIcon + " Cancel",
            customClass:{ confirmButton: "btn btn-outline-success", cancelButton: "btn btn-light" }  
        }).then((result) => {
            if (result.isConfirmed) 
            {
                $http({
                    method: 'post',
                    url: $base_url + 'untagProforma',
                    data: { profId: data.proforma_header_id, supId: data.supplier_id, crfId: $scope.crfId }
                }).then(function successCallback(response) 
                {
                    if (response.data == "success") {
                        ToastSuccess.fire({
                            icon: 'success',
                            title: 'Proforma is untagged under this CRF!'
                        })
    
                    } else if (data.response == "failed") {
                        ToastError.fire({
                            icon: 'error',
                            title: 'Failed to untag proforma!'
                        })
                    }
                    $scope.loadApplied();                          
                });    
            }
        })
    }

    $scope.matchProformaVsCrf = function(ev) 
    {
        
        ev.preventDefault();

        var count = 0;

        angular.forEach($scope.applied, function(value, key) {
            if( angular.isUndefined(value.dealId)){
                count ++; //count si with no vendor deal selected
            }
        });

        if ($scope.proceedMatch == 1 && count == 0) {
            Swal.fire({
                title: warningTitle,
                html: "<b>Match PRO-FORMA vs CRF ? </b>",
                buttonsStyling: false,            
                showCancelButton: true,
                confirmButtonText: confirmButtonIcon + " Yes",
                cancelButtonText: cancelButtonIcon + " No",
                customClass:{ confirmButton: "btn btn-outline-success", cancelButton: "btn btn-light"}  
            }).then((result) => {
                if (result.isConfirmed) 
                {

                    $('.btn').prop('disabled', true);
                    $('#selectProforma').attr("disabled", true);
                    $.ajax({
                        type: "POST",
                        url: $base_url + 'matchProformaVsCrf',
                        data: { crf: $scope.crfId, applied : $scope.applied },
                        cache: false,
                        beforeSend: function() {
                            $("#btnMatch").html(`Matching ... <span class="spinner-grow spinner-grow-sm" role="status"></span> `);
                        },
                        success: function(response) 
                        {                       
                            if (response.info == "incomplete") {
                                Swal.fire({
                                    title: warningTitle,
                                    html: '<b>' + response.message + '</b>'               
                                })    

                            } else if (response.info == "success") {
                                Swal.fire({
                                    title: successTitle,
                                    html: '<b>' + response.message + '</b>'
                                }).then(function() {
                                    window.open($base_url + 'files/Reports/ProformaVsCrf/' + response.file);
                                    $("#applyProformaToCrf").modal('hide');
                                })

                            } else if ( response.info == "no-setup") {

                                var result   = response.nosetup ;
                                var rows     = result.map(obj => `<tr><td class="text-center">${obj.itemcode}</td><td class="text-center">${obj.desc}</td></tr>`);
                                var table    = `<h5 style='color:#FF0000'> ` + response.message + ` </h5>
                                                <table class="table table-bordered table-hover table-sm">
                                                    <thead class="bg-dark">
                                                        <tr>
                                                            <th scope="col" class="text-center"> Item Code</th>
                                                            <th scope="col" class="text-center"> Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        ${rows.join('')}
                                                    </tbody>
                                                </table>`
                                Swal.fire({
                                    title: warningTitle,
                                    html: table     
                                })

                            }
                        },
                        complete: function() {
                            $("#btnMatch").html(` <i class="fas fa-link"></i> Match PRO-FORMA VS CRF  `);
                            // $("#btnTag").prop('disabled', false);
                            // $("#btnMatch").prop('disabled', false);
                            // $("#btnClose").prop('disabled', false);
                            $('.btn').prop('disabled', false);
                            $scope.proceedApply = 0 ;
                        }
                    });
                }
            })
        } else {
            Swal.fire({
                title: warningTitle,
                html: '<b>' + "No Data to Match or Please Select a Vendor Deal!" + '</b>'
            })
        } 
       
    }

    $scope.resetProfVsCrf = function()
    {
        $scope.vendorsdeal = null;
        $scope.periodFrom  = null;
        $scope.periodTo    = null;
        $scope.searchedProf= null;
        $scope.searchResult= null;
        $scope.proformaId  = null;
        $scope.proceedApply = 0;

    }

    $scope.tagAsAudited = function(crf)
    {   
        
        var crfId = crf.crf_id ;
        Swal.fire({
            title: warningTitle,
            html: "<b>Change Audit Status to <strong>AUDITED </strong> ? <br> You won't be able to revert this! </b>",
            buttonsStyling: false,            
            showCancelButton: true,
            confirmButtonText: confirmButtonIcon + " Yes",
            cancelButtonText: cancelButtonIcon + " Cancel",
            customClass:{ confirmButton: "btn btn-outline-success", cancelButton: "btn btn-light"}  
        }).then((result) => {
            if (result.isConfirmed) 
            {
                $.ajax({
                    type: "POST",
                    url: $base_url + 'auditCrf',
                    data: { crfId: crfId  },
                    cache: false,
                    beforeSend: function() {
                        $('#loading').show();
                    },
                    success: function(response) {
                        var titleHeader = "";
                        if(response.info == "success"){
                            titleHeader = successTitle
                        } else {
                            titleHeader = warningTitle
                        }
                        Swal.fire({
                            title: titleHeader,
                            html: '<b> ' + response.message + ' </b>'               
                        }).then(function() {
                            $scope.getCrfs(); 
                        })
                    },
                    complete: function() {
                        $('#loading').hide();
                    }
                });
            }
        })
        
    }   

    $scope.closeCrf = function() {
        // $("#uploadProCrf").trigger("reset");
        $scope.selectSupplier   = null;
        $scope.selectCustomer   = null;
        $scope.selectSop        = null;
        $scope.sops             = null;        
        $("#crfFile").val('');
      
    }

    $scope.closeApplyProforma = function() {
        $("#applyProforma").trigger("reset");
    }

    $scope.crfTagAsMatched = function(data){
        var crfId = data.crf_id;

        Swal.fire({
            title: warningTitle,
            html: "<b>Change Matching Status to <strong>MATCHED</strong> ? <br> You won't be able to revert this! </b>",
            buttonsStyling: false,            
            showCancelButton: true,
            confirmButtonText: confirmButtonIcon + " Yes",
            cancelButtonText: cancelButtonIcon + " Cancel",
            customClass:{ confirmButton: "btn btn-outline-success", cancelButton: "btn btn-light"}  
        }).then((result) => {
            if (result.isConfirmed) 
            {
                $.ajax({
                    type: "POST",
                    url: $base_url + 'tagAsMatchedCrf',
                    data: { crfId: crfId  },
                    cache: false,
                    beforeSend: function() {
                        $('#loading').show();
                    },
                    success: function(response) {
                        var titleHeader = "";
                        if(response.info == "success"){
                            titleHeader = successTitle
                        } else {
                            titleHeader = warningTitle
                        }
                        Swal.fire({
                            title: titleHeader,
                            html: '<b> ' + response.message + ' </b>'               
                        }).then(function() {
                            $scope.getCrfs(); 
                        })
                    },
                    complete: function() {
                        $('#loading').hide();
                    }
                });
            }
        })

    }

    $scope.replaceCrf = function(ev){

        ev.preventDefault();      
        var formData = new FormData(ev.target);
        formData.append('crfId', $scope.crfId); 
        formData.append('supId',$scope.hiddenSupplier);

        Swal.fire({
            title: warningTitle,
            html: "<b> Are you sure to replace this CRF? </b>",
            buttonsStyling: false,            
            showCancelButton: true,
            confirmButtonText: confirmButtonIcon + " Yes",
            cancelButtonText: cancelButtonIcon + " No",
            customClass:{ confirmButton: "btn btn-outline-success", cancelButton: "btn btn-light"}  
        }).then((result) => {
            if (result.isConfirmed) 
            {                               
        
                $.ajax({
                    type: "POST",
                    url: $base_url + 'replaceCRF',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        
                        var titleHeader = "";
                        if(response.info == "success"){
                            titleHeader = successTitle
                        } else {
                            titleHeader = warningTitle
                        }
                        Swal.fire({
                            title: titleHeader,
                            html: '<b>' + response.message + ' </b>'               
                        }).then(function(){
                            location.reload();
                        })
                        
                    }
                });
            }
        })
    }

    $scope.crfTagAsClosed = function(ev,data){
        ev.preventDefault();
        $scope.crfIdClose = data.crf_id;
        $scope.crfNoClose = data.crf_no;
        $("#closeCRFModal").modal("show");

    }

    $scope.closeCRF = function(ev){
        
        ev.preventDefault();        
        var formData = new FormData(ev.target);
        formData.append('crfId', $scope.crfIdClose); 

        Swal.fire({
            title: warningTitle,
            html: "<b> Are you sure? </b>",
            buttonsStyling: false,            
            showCancelButton: true,
            confirmButtonText: confirmButtonIcon + " Yes",
            cancelButtonText: cancelButtonIcon + " No",
            customClass:{ confirmButton: "btn btn-outline-success", cancelButton: "btn btn-light"}  
        }).then((result) => {
            if (result.isConfirmed) 
            {  
                $("#closeCRFModal").modal("hide");
                $.ajax({
                    type: "POST",
                    url: $base_url + 'tagAsClosedCRF',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#loading').show();
                    },
                    success: function(response) {
                        
                        var titleHeader = "";
                        if(response.info == "success"){
                            titleHeader = successTitle
                        } else {
                            titleHeader = warningTitle
                        }
                        Swal.fire({
                            title: titleHeader,
                            html: '<b>' + response.message + ' </b>'               
                        }).then(function(){
                            $scope.getCrfs(); 
                        })                        
                    },
                    complete: function() {
                        $('#loading').hide();
                    }
                });
            }

        })

    }
    
    $scope.trackCRF = function(data){
        
        $scope.tcrfId = data.crf_id
        $scope.tcrfNo = data.crf_no
        $scope.tdate  = data.crf_date
        $scope.tamount= data.crf_amt
        $scope.tsop   = data.sop_no

        $scope.tpi    = null
        $scope.tprof  = null

        $http({
            method: 'post',
            url: $base_url + 'trackCRF',
            data: { crfId: $scope.tcrfId }
        }).then(function successCallback(response) 
        {
            $scope.tpi = response.data.pi;
            $scope.tprof = response.data.prof;                    
        }); 

        
    }


    $(function() {
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
    });

});