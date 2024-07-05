window.myApp.controller('proformavspi-controller', function($scope, $http, $window) {
    const warningTitle = "<i class='fas fa-exclamation-triangle fa-lg' style='color:#e65c00'></i>";
    const successTitle = "<i class='fas fa-check-circle fa-lg' style='color:#28a745'></i>";
    const infoTitle    = "<i class='fas fa-info-circle fa-lg' style='color:#005ce6'></i>";
    const confirmButtonIcon = "<i class='fas fa-thumbs-up'></i>";
    const cancelButtonIcon  = "<i class='fas fa-thumbs-down'></i>";
    const confirmButtonClass = "btn btn-outline-success mr-2";
    const cancelButtonClass = "btn btn-light";

    $scope.loadData = [];
    $scope.table = {};
    $scope.pricelogTable = {};
    $scope.proceedMatchProf = 0;
    $scope.proceedMatchPi = 0;
    $scope.crfId = 0;
    $scope.crfs = {};
    $scope.canEdit = false;

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

    $scope.toast = function(color)
    {
        const Toast = Swal.mixin({
                           toast: true,
                           position: 'top-right',
                           iconColor: color,
                           customClass: { popup: 'toasts-top-right' },
                           showConfirmButton: false,
                           timer: 1500,
                           timerProgressBar: true })
        return Toast;
    }

    $scope.loadSupplier = function() {
        $http({
            method: 'get',
            url: $base_url + 'getSuppliersForPI'
        }).then(function successCallback(response) {
            $scope.suppliers = response.data;
        });
    }
    $scope.loadSupplier();

    $scope.loadCustomer = function() {
        $http({
            method: 'get',
            url: $base_url + 'getCustomersForPI'
        }).then(function successCallback(response) {
            $scope.customers = response.data;
        });
    }
    $scope.loadCustomer();

    $scope.loadPi = function(supplier,location) {

        $('#loading').show();
        if ($.fn.DataTable.isDataTable('#proformaVspiTable')) {
            $('#proformaVspiTable').DataTable().clear();
            $('#proformaVspiTable').DataTable().destroy();
            $scope.pi = [];
            $scope.pendingPi = false;
        }

        $http({
            method: 'POST',
            url: $base_url + 'getPIs',
            data: { supId: supplier, cusId: location, from: $scope.dateFrom, to: $scope.dateTo }
        }).then(function successCallback(response) {
            $('#loading').hide();
            if (response.data != '') {                
                $(document).ready(function() { $('#proformaVspiTable').DataTable(); });
                $scope.pi = response.data;
                $scope.pendingPi = true;

            } else {
                swal.fire({
                    title: infoTitle,
                    html: "<b> No Pending Matches for this supplier and location! </b>"
                })
                $scope.pendingPi = false;
            }

        });
        $scope.loadCrf();        
    }

    $scope.uploadPi = function(ev) {

            ev.preventDefault();
            var formData = new FormData(ev.target);

            $.ajax({
                type: "POST",
                url: $base_url + 'uploadPi',
                data: formData,
                enctype: 'multipart/form-data',
                cache: false,
                processData: false,
                contentType: false,
                success: function(response) {
                    if(response.info == 'Error-ext'){
                        Swal.fire({
                            title: warningTitle,
                            html: "<b> " + response.message +"<br> [" + response.ext + "] </b>"
                        }).then(function() {
                            location.reload();
                        })
                    } else if(response.info == "Error-item"){
                        var items = Object.values(response.item);                 
                        var itemNotFound = "";
                        for (let i = 0; i < items.length; i++) {
                            itemNotFound = items;                        
                        }
                        Swal.fire({
                            title: warningTitle,
                            html: "<b> " + response.message +" <br> <i> [" + itemNotFound + "] </i> </b>"
                        }).then(function() {
                            location.reload();
                        })
                    } else if(response.info == "Error"){
                        Swal.fire({
                            title: warningTitle,
                            html: "<b> " + response.message + " </b>"
                        }).then(function() {
                            location.reload();
                        })

                    } else if(response.info == "Success"){
                        Swal.fire({
                            title: successTitle,
                            html: "<b> " + response.message + " </b>"
                        }).then(function() {
                            location.reload();
                        })
                    } else if(response.info == "PI-Invalid"){
                        Swal.fire({
                            title: warningTitle,
                            html: "<b> " + response.message + " </b>"
                        }).then(function() {
                            location.reload();
                        })
                    }
                }
            });       
      
    }


    $scope.viewPiDetails = function(data) {

        $scope.loadData = data;
        var total = 0;
        $scope.canUpdate = false;

        if ($.fn.DataTable.isDataTable('#viewPiLine')) {
            $scope.table.destroy();
            $scope.details = [];
            $scope.viewDetails = false ;
        }

        $http({
            method: 'POST',
            url: $base_url + 'getPiDetails',
            data: { pi: data.piId }
        }).then(function successCallback(response) {
            $scope.details = response.data;
            $(document).ready(function() {
                setTimeout(function() {
                    $scope.table = $('#viewPiLine').DataTable({
                        destroy: true,
                        stateSave: true
                    });
                }, 100);
            });
            angular.forEach($scope.details, function(value, key) {
                total += parseFloat(value.amt_including_vat) ;
            });
            $scope.totalAmountPi = total.toFixed(2);
            $scope.viewDetails = true ;
        });
    }

    $scope.managersKey = function(ev) {
        ev.preventDefault();
        $("#managersKey").modal("show");
    }

    $scope.updateItem = function(ev) {
        ev.preventDefault();

        $.ajax({
            type: "POST",
            url: $base_url + 'managersKey',
            data: { user: $scope.user, pass: $scope.pass },
            cache: false,
            success: function(response) {
                if (response != null) {
                    $scope.canEdit = true;
                    $("#managersKey").modal("hide");
                    $("#updateItemBtn").prop("disabled", true);  

                } else {                    
                    Swal.fire({
                        title: warningTitle,
                        html: "<b>Unauthorized Account!</b>"
                    }).then(function() {
                        location.reload();
                    })
                }
            }
        });
    }

    $scope.fetchItemPrice = function(data) {
        if ($scope.canEdit) {
            $scope.piLineId = data.pi_line_id;
            $scope.piHeadId = data.pi_head_id;
            $scope.itemCode = data.item_code;
            $scope.itemDesc = data.description;
            $scope.itemRemarks = data.remarks;
            $scope.itemQty = data.qty;
            $scope.newPrice = data.direct_unit_cost.replace(',', '');
            $scope.newAmount = data.amt_including_vat.replace(',', '');
            $scope.oldPrice = data.direct_unit_cost;
            $scope.oldAmount = data.amt_including_vat;
            $("#updatePrice").modal("show");
        }
    }

    $scope.updatePrice = function() {
        var formData = {
            piLineId: $scope.piLineId,
            piHeadId: $scope.piHeadId,
            itemCode: $scope.itemCode,
            itemQty: $scope.itemQty,
            newPrice: $scope.newPrice,
            newAmount: $scope.newAmount,
            oldPrice: $scope.oldPrice,
            oldAmount: $scope.oldAmount,
            remarks: $scope.itemRemarks
        }

        $.ajax({
            type: "POST",
            url: $base_url + 'updatePrice',
            data: formData,
            async: false,
            cache: false,
            success: function(response) {             
                Toast = $scope.toast(response.color);
                Toast.fire({
                      icon: response.info,
                      title: response.message
                })
                $('#updatePrice').modal('hide');
                $scope.viewPiDetails($scope.loadData);
            }
        });
    }

    $scope.calculate = function() {
        $scope.newAmount = ($scope.itemQty * 1) * ($scope.newPrice * 1);
    }

    $scope.closeViewPi = function() {
        var table = $("#viewPiLine").DataTable();
        table.state.clear();
        $("#updateItemBtn").prop("disabled", false);
        $scope.canEdit = false;
    }

    $scope.itemPricelog = function(data) {
        $scope.itemCode = data.item_code + ' - ' + data.description;
        $scope.quantity = data.qty;
        $scope.uom = data.uom;
        var formData = {
            piLineId: data.pi_line_id,
            piHeadId: data.pi_head_id,
            itemCode: data.item_code,
        }

        $http({
            method: 'POST',
            url: $base_url + 'getItemPriceLog',
            data: formData
        }).then(function successCallback(response) {
            if (response.data.length > 0) {
                $scope.pricelog = response.data;

            } else {
                $scope.pricelog = [];
                $scope.pricelog.push({ old_price: "0", old_amt: "0", changed_date: "No Data", username: "No Data" });
            }

        });
    }

    $scope.loadCrf = function() {
        $http({
            method: 'post',
            url: $base_url + 'getCrfInPI',
            data: { supId: $scope.supplierName, cusId: $scope.locationName }
        }).then(function successCallback(response) {

            $scope.crfs = response.data;
            $scope.profInCrf = [];
            $scope.profInCrf.push({ loc: "No Data", profCode: "No Data", delivery: "No Data", po: "No Data", total: "0.00" });
            $scope.proceedMatchProf = 0;
            $scope.piInCrf = [];
            $scope.piInCrf.push({ loc: "No Data", piNo: "No Data", postDate: "No Data", po: "No Data", total_amount: "0.00" });
            $scope.proceedMatchPi = 0;
            $scope.crfDate = "";
            $scope.crfAmount = "";
        });
    }

    $scope.loadProfPi = function() {

        $scope.profPiHasLoaded = false;
        $http({
            method: 'post',
            url: $base_url + 'getProfPiInCrf',
            data: { crfId: $scope.searchedCrfId, supId: $scope.supplierName }
        }).then(function successCallback(response) {
            $scope.profPiHasLoaded = true;

            // console.log($scope.searchedCrfId);
            // console.log($scope.profPiHasLoaded);
            if (response.data.prof.length > 0) {
                $scope.profInCrf = response.data.prof;
                $scope.proceedMatchProf = 1;
            } else {
                $scope.profInCrf = [];
                $scope.profInCrf.push({ loc: "No Data", profCode: "No Data", delivery: "No Data", po: "No Data", item_total: 0.00  });
                $scope.proceedMatchProf = 0;
            }
            if (response.data.pi.length > 0) {
                $scope.piInCrf = response.data.pi;
                $scope.proceedMatchPi = 1;
            } else {
                $scope.piInCrf = [];
                $scope.piInCrf.push({ loc: "No Data", piNo: "No Data", postDate: "No Data", po: "No Data", total_amount : 0.00});
                $scope.proceedMatchPi = 0;
            }
            
        });

        $scope.selectVendorsDeal = null;
        $scope.periodFrom        = null;
        $scope.periodTo          = null;
        $scope.loadDeals($scope.supplierName);
    }

    $scope.loadDeals = function(supplier)
    {   
        $http({
            method: 'post',
            url: `${$base_url}loadVendorsDeal` , 
            data: { supId: supplier }
        }).then(function successCallback(response) {
            $scope.vendorsDeal = response.data;
        });
    }

    $scope.displayVendorsdDealToInput = function(dealId, deals)
    {
        var result        = deals.find(({ vendor_deal_head_id }) => vendor_deal_head_id === dealId);
        $scope.periodFrom = result.period_from ;
        $scope.periodTo   = result.period_to ;        
    }

    $scope.tag = function(data) {
        
        $scope.piId   = data.piId;
        $scope.status = data.status ;
        $("#tagPi").modal('show');

    }

    $scope.applyPi = function(ev) {
        ev.preventDefault();

        var form = $("#applyPiForm")[0];
        var formData = new FormData(form);
        formData.append('pi', $scope.piId);
        formData.append('supId', $scope.supplierName);
        formData.append('crf', $scope.searchedCrfId);

        $.ajax({
            type: "POST",
            url: $base_url + 'applyPiToCrf',
            data: formData,
            async: false,
            cache: false,
            processData: false,
            contentType: false,
            success: function(response) {                
                Toast = $scope.toast(response.color);
                Toast.fire({
                      icon: response.info,
                      title: response.message
                })
                $scope.loadProfPi($scope.crfId, $scope.crfs);
            }
        });
    }

    $scope.untagPi = function(ev)
    {
        if($scope.piId != undefined && $scope.crf != undefined )
        {
            // console.log($scope.searchedCrfId);
            ev.preventDefault();
            $http({
                method: 'post',
                url: $base_url + 'untagPiFromCrf',
                data: { piId: $scope.piId, crfId: $scope.searchedCrfId }
            }).then(function successCallback(response) {
                Toast = $scope.toast(response.data.color);
                Toast.fire({
                      icon: response.data.info,
                      title: response.data.message
                })
                $scope.loadProfPi($scope.crfId, $scope.crfs);
            });

        } else {
            swal.fire({
                title: warningTitle,
                html: "<b>Pi No or CRF No is unknown!</b>"                                           
            })
        }
    }

    $('#managersKey').on('hidden.bs.modal', function() {
        $("#user").val('');
        $("#pass").val('');
    });

    $('#tagPi').on('hidden.bs.modal', function(e) {
        $scope.crf = null;
        $scope.loadPi($scope.supplierName, $scope.locationName) 
        // location.reload();
    });
    


    $scope.matchProformaVsPi = function(data, button, ev) {
        ev.preventDefault();

        // $scope.searchedCrfId
        var crfId = data;
        var saveVar = $scope.saveVariance === undefined ? 0 : 1

        Swal.fire({
            title: warningTitle,
            html: "<b>Match PRO-FORMA vs PI ? </b>",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "<i class='fas fa-thumbs-up'></i> Yes",
            cancelButtonText: "<i class='fas fa-thumbs-down'></i> No",
            customClass: { confirmButton: "btn btn-outline-success",cancelButton: "btn btn-light" }
        }).then((result) => {
            if (result.isConfirmed) {                

                (async () => {
                    const { value: type } = await Swal.fire({
                      title: 'Select File To Generate',
                      input: 'select',
                      inputOptions: {
                        pdf: 'PDF',
                        excel: 'Excel'
                      },
                      inputPlaceholder: 'Required',
                      showCancelButton: true,
                      inputValidator: (value) => {
                        return new Promise((resolve) => {
                          if (value !== '') {
                            resolve()
                          } else {
                            resolve('You need to select a file type!')
                          }
                        })
                      }
                    })
                    
                    if (type) {
                        $('button').prop('disabled', true); 

                        if( button == 1 ){ //matching 1
                            $.ajax({
                                type: "POST",
                                url: $base_url + 'matchProformaVsPi', //$base_url + 'matchPsivsPi', //
                                data: { crfId: crfId, supId: $scope.supplierName , cusId: $scope.locationName, dealId: $scope.selectVendorsDeal,type: type, saveVar: saveVar },
                                cache: false,
                                responseType: 'json',
                                beforeSend: function() {
                                    $("#btnMatch").html(`Matching ... <span class="spinner-grow spinner-grow-sm" role="status"></span> `);
                                },
                                success: function(response) {   
                                    if (response.info == "incomplete") {
                                        Swal.fire({
                                            title: warningTitle,
                                            html: '<b>' + response.message + '</b>'
                                        })    
                                    } else if (response.info == "success") {
                                        var unPairedPos = "";
                                        if( response.wayParesPoProf.length > 0 ){
                                            unPairedPos = "<strong>UNPAIRED PO in :<br> PRO-FORMA </strong> <br> <i>" + response.wayParesPoProf + "</i>" ;                                
                                        } else if ( response.wayParesPoPi.length > 0) {
                                            unPairedPos = "<strong>UNPAIRED PO in :<br> PI </strong> <br> <i>" + response.wayParesPoPi + "</i>" ;
                                        } else if (response.wayParesPoPi.length > 0 || response.wayParesPoProf.length > 0 ) {
                                            unPairedPos = "<strong>UNPAIRED PO in :<br> PRO-FORMA </strong> <br> <i>" + response.wayParesPoProf + "</i> <br> <strong>PI </strong> <br> <i>" + response.wayParesPoPi + "</i>" ;
                                        }    
                                        if(response.wayParesPoPi.length > 0 || response.wayParesPoProf.length > 0)  {
                                            
                                            Swal.fire({
                                                title: infoTitle,
                                                html: unPairedPos          
                                            }).then(function(){
                                                Swal.fire({
                                                    title: successTitle,
                                                    html: '<b>' + response.message + '</b>'
                                                }).then(function() {
                                                    window.open($base_url + 'files/Reports/ProformaVsPi/' + response.file);
                                                    location.reload();
                                                })
                                            })
                                        } else {
                                            Swal.fire({
                                                title: successTitle,
                                                html: '<b>' + response.message + '</b>'
                                            }).then(function() {
                                                window.open($base_url + 'files/Reports/ProformaVsPi/' + response.file)
                                                location.reload();
                                            })
                                        } 
                                    } else if(response == "no data") {
                                        swal.fire({
                                            title: warningTitle,
                                            html: "<b>No Data To Match!</b>"                                           
                                        })

                                    } else if(response.info == "no-setup") {
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
                                    $("#btnMatch").html(` <i class="fas fa-link"></i> Matching 1  `);
                                    $('button').prop('disabled', false); 
                                    $scope.proceedApply = 0 ;
                                }                            
                            });
                        } else if( button == 2 ){ //matching 2
                            $.ajax({
                                type: "POST",
                                url: $base_url + 'matching2',
                                data: { crfId: crfId, supId: $scope.supplierName , cusId: $scope.locationName, dealId: $scope.selectVendorsDeal,type: type, saveVar: saveVar },
                                cache: false,
                                responseType: 'json',
                                beforeSend: function() {
                                    $("#btnMatch2").html(`Matching ... <span class="spinner-grow spinner-grow-sm" role="status"></span> `);
                                },
                                success: function(response) {  
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
                                            window.open($base_url + 'files/Reports/ProformaVsPi/' + response.file);
                                            location.reload();
                                        })                                       
                                       
                                    } else if(response == "no data") {
                                        swal.fire({
                                            title: warningTitle,
                                            html: "<b>No Data To Match!</b>"                                           
                                        })

                                    } else if(response.info == "no-setup") {
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
                                    $("#btnMatch2").html(` <i class="fas fa-link"></i> Matching 2  `);
                                    $('button').prop('disabled', false); 
                                    $scope.proceedApply = 0 ;
                                }                            
                            });
                        }
                    }                    
                })()
                     
               
            }
        })
    }

    $scope.changeStatus = function(data)
    {
        var piId = data.piId;

        $http({
            method: 'post',
            url: $base_url + 'getPo',
            data: { pi: piId  }
        }).then(function successCallback(response) 
        {
                var result   = response.data;      
                var poId     = result.po_header_id;    
                var table    = `<h6 style='color:#FF0000'> Changing the status of this PI <mark>` + result.pi_no +  `</mark> will also change the status of the transaction below.  </h6>
                                <table class="table table-bordered table-hover table-sm">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th scope="col" class="text-center"> PO No</th>                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">${result.po_no}</td>
                                        </tr>
                                    </tbody>
                                </table>`                                
   
                 Swal.fire({
                    title: warningTitle,
                    html: table,
                    buttonsStyling: false,
                    showCancelButton: true,
                    confirmButtonText: "<i class='fas fa-thumbs-up'></i> Proceed",
                    cancelButtonText: " <i class='fas fa-thumbs-down'></i> Cancel", 
                    customClass: { confirmButton: "btn btn-outline-success",cancelButton: "btn btn-light" }
                }).then((result) => {
                    if (result.isConfirmed) 
                    {
                        $.ajax({
                            type: "POST",
                            url: $base_url + 'changeStatus',
                            data: { piId: piId, poId: poId },
                            cache: false,
                            beforeSend: function() {
                                $('#loading').show();
                            },
                            success: function(response) {
                                
                                if(response.info == "success"){
                                    Swal.fire({
                                        title: successTitle,
                                        html: '<b> ' + response.message + ' </b>'               
                                    }) 

                                } else {
                                    Swal.fire({
                                        title: warningTitle,
                                        html: '<b> ' + response.message + ' </b>'               
                                    }) 
                                }
                                $scope.loadPi($scope.supplierName, $scope.locationName) ; 
                            },
                            complete: function() {
                                $('#loading').hide();
                            }
                        });
                    }
                })
        });        
        
    }

    $scope.matchItems = function(crfId)
    {
        $http({
            method: 'post',
            url: $base_url + 'viewMatchedUnmatchedItems',
            data: { crfId: crfId  }
        }).then(function successCallback(response) 
        {
            console.log(response.data);  
            $scope.sameItemSamePo = response.data;
        });
    }

    $scope.applyCm = function(data)
    {
        $scope.piId = data.piId;
        $scope.piNo = data.pi_no;
    }

    $scope.uploadCm = function(ev)
    {
        ev.preventDefault();
        var form = $("#uploadCM")[0];
        var formData = new FormData(form);

        Swal.fire({
            title: warningTitle,
            html: "<b>Are you sure to apply CM in this PI ? <br> You won't be able to revert this! </b>",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "<i class='fas fa-thumbs-up'></i> Yes",
            cancelButtonText: "<i class='fas fa-thumbs-down'></i> No",
            customClass: { confirmButton: "btn btn-outline-success",cancelButton: "btn btn-light" }
        }).then((result) => {
            if (result.isConfirmed) 
            {
                $.ajax({
                    type: "POST",
                    url: $base_url + 'uploadCm',
                    data: formData,
                    async: false,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        var icon = ""
                        if(response.info == "Error"){
                            icon = warningTitle;
                        } else if(response.info == "Success"){
                            icon = successTitle ;
                        }
    
                        Swal.fire({
                            title: icon,
                            html: '<b> ' + response.message + ' </b>'               
                        }).then(function() {
                            location.reload();
                        })
                    }
                });
            }
        })

           
    }

    $scope.viewCmDetails = function(data)
    {
        $scope.cmNo = data.cm_no;
        $scope.cmPostingDate = data.posting_date;
        $scope.cmPI = data.pi_no ;

        $http({
            method: 'post',
            url: $base_url + 'viewCMDetails',
            data: { cmId: data.cm_head_id  }
        }).then(function successCallback(response) 
        {
            var total = 0;
            $scope.cmDetails = response.data;
            angular.forEach($scope.cmDetails, function(value, key) {
                total += parseInt(value.qty) * parseFloat(value.price);
            });
            $scope.cmAmount = total.toFixed(2);
        });
    }

    $scope.tagAsAudited = function(data){
        var pi = data.piId
        Swal.fire({
            title: warningTitle,
            html: "<b>Change Audit Status to <strong>AUDITED </strong> ? <br> You won't be able to revert this! </b>",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "<i class='fas fa-thumbs-up'></i> Yes",
            cancelButtonText: "<i class='fas fa-thumbs-down'></i> Cancel",
            customClass: { confirmButton: "btn btn-outline-success",cancelButton: "btn btn-light" }
        }).then((result) => {
            if (result.isConfirmed) 
            {
                $http({
                    method: 'post',
                    url: $base_url + 'changeAuditStatus',
                    data: { piId: pi  }
                }).then(function successCallback(response) 
                {
                    if(response.data == "success")
                    {
                        Swal.fire({
                            title: successTitle,
                            html: '<b> PI is AUDITED! </b>'               
                        })  
                    } else {
                        Swal.fire({
                            title: warningTitle,
                            html: "<b> Failed to change status!</b>"
                        })    
                    } 
                    $scope.loadPi($scope.supplierName, $scope.locationName) ;     
                });
            }
        })
    }

    // $scope.newTagging = function(data){
    //     $scope.loadCRFS();
    //     $("#viewDetails").modal("show")
    //     $scope.purchaseinvoiceId = data.piId
    //     $scope.purchaseinvoiceno = data.pi_no
    //     $scope.pipostingdate     = data.date
    //     $scope.piPONO            = data.po_no
    // }

    $scope.loadCRFS = function(){
        $http({
            method: 'post',
            url: $base_url + 'loadCRFS',
            data: { supId: $scope.supplierName, cusId: $scope.locationName }
        }).then(function successCallback(response) {
            $scope.loadedCRFS = response.data;
        })
    }
    
    $scope.loadProfs = function(id,loadedCRFS) {
        $scope.ncrf         = id;
        var result          = loadedCRFS.find(({ crf_id }) => crf_id === id);
        $scope.ncrfDate     = result.crf_date;
        $scope.ncrfAmount   = result.crf_amt;  
        $scope.sopIdTag     = result.sop_id;    
        $scope.nsopNo       = result.sop_no;  

        $scope.selectVendorsDealPi   = null;
        $scope.dealPeriodFrom        = null;
        $scope.dealPeriodTo          = null;
        $scope.loadDeals($scope.supplierName);
        $scope.loadApplied();
    }

    $scope.showDealsToInput = function(dealId, deals)
    {
        var result        = deals.find(({ vendor_deal_head_id }) => vendor_deal_head_id === dealId);
        $scope.dealPeriodFrom = result.period_from ;
        $scope.dealPeriodTo   = result.period_to ;        
    }

    $scope.searchProf = function(ev)
    {
        ev.preventDefault();
        var string = $("#searchProforma").val();
        if(string == '') {
            $(".search-results").hide();
            $scope.proceedApply = 0;

        }else{
            $http({
                method: 'post',
                url:'../transactionControllers/proformavspicontroller/searchProf/' + $scope.supplierName + '/' + $scope.ncrf,
                data: { str: string }
            }).then(function successCallback(response) {
                $scope.searchResult = response.data;
                if($scope.searchResult.length == 0)
                {
                    $scope.hasResults = 0 ;
                    $scope.proceedApply = 0;
                    $scope.searchResult.push( { proforma_header_id: "No Results Found" } );
                }else {
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

    $scope.applyProf = function()  {      
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
                        url: '../transactionControllers/proformavspicontroller/applyProforma/' + $scope.ncrf ,
                        data: { profId: id, piId: $scope.purchaseinvoiceId }
                    }).then(function successCallback(response) {
                        if (response.data == "success") {                           
                            ToastSuccess.fire({
                                icon: 'success',
                                title: 'Pro-forma is tagged under this CRF and PI!'
                            })
        
                        } else if (response.data == "exists") {                           
                            ToastError.fire({
                                icon: 'error',
                                title: 'Pro-forma is already tagged!'
                            })
                        } else {                           
                            ToastError.fire({
                                icon: 'error',
                                title: 'PO No. of PSI and PI is not the same!'
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

    $scope.loadApplied = function() 
    {
        $http({
            method: 'post',
            url: '../transactionControllers/proformavspicontroller/getAppliedProforma/' + $scope.purchaseinvoiceId + '/' + $scope.ncrf
        }).then(function successCallback(response) {
            $scope.appliedProfs = response.data
            if ($scope.appliedProfs.length > 0){
                $scope.proceedMatch = 1;
            } else {
                $scope.appliedProfs = [];
                $scope.appliedProfs.push( { l_acroname: "No Data", proforma_code: "No Data", delivery_date: "No Data", po_no : 'No Data', posting_date: 'No Data' } );
                $scope.proceedMatch = 0;
            }
            // $scope.vendorsDeal = response.data.deal;
        });
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
                    url: $base_url + 'untagProf',
                    data: { lineId: data.crf_psi_pi_id }
                }).then(function successCallback(response) 
                {
                    if (response.data == "success") {
                        ToastSuccess.fire({
                            icon: 'success',
                            title: 'Proforma is untagged under this CRF & PI!'
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

    $scope.resetPITaggingForm = function(ev){
        ev.preventDefault();

        $scope.purchaseinvoiceno    = null
        $scope.pipostingdate        = null
        $scope.piPONO               = null
        $scope.ncrf                 = null
        $scope.ncrfDate             = null
        $scope.ncrfAmount           = null
        $scope.nsopNo               = null
        $scope.selectVendorsDealPi  = null
        $scope.dealPeriodFrom       = null
        $scope.dealPeriodTo         = null
        $scope.appliedProfs         = null
        $scope.searchResult         = null
        $scope.searchedProf         = ""
        $scope.proceedMatch         = 0
        $scope.hasResults           = 0

    }

    $scope.ProformaVsPi = function(id,ev){
        ev.preventDefault();
        var crfId = id;

        Swal.fire({
            title: warningTitle,
            html: "<b>Match PRO-FORMA vs PI ? </b>",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: "<i class='fas fa-thumbs-up'></i> Yes",
            cancelButtonText: "<i class='fas fa-thumbs-down'></i> No",
            customClass: { confirmButton: "btn btn-outline-success",cancelButton: "btn btn-light" }
        }).then((result) => {
            if (result.isConfirmed) {                

                (async () => {
                    const { value: type } = await Swal.fire({
                        title: 'Select File To Generate',
                        input: 'select',
                        inputOptions: {
                        pdf: 'PDF',
                        excel: 'Excel'
                        },
                        inputPlaceholder: 'Required',
                        showCancelButton: true,
                        inputValidator: (value) => {
                        return new Promise((resolve) => {
                            if (value !== '') {
                            resolve()
                            } else {
                            resolve('You need to select a file type!')
                            }
                        })
                        }
                    })
                    
                    if (type) {
                        $('button').prop('disabled', true); 
                        $.ajax({
                            type: "POST",
                            url: $base_url + 'ProfvsPi',
                            data: { crfId: crfId, supId: $scope.supplierName , cusId: $scope.locationName, dealId: $scope.selectVendorsDealPi,type: type },
                            cache: false,
                            beforeSend: function() {
                                $("#btnProfvsPi").html(`Matching ... <span class="spinner-grow spinner-grow-sm" role="status"></span> `);
                            },
                            success: function(response) {  
                                if (response.info == "success") {                                   
                                      
                                    Swal.fire({
                                        title: successTitle,
                                        html: '<b>' + response.message + '</b>'
                                    }).then(function() {
                                        window.open($base_url + 'files/Reports/ProformaVsPi/' + response.file)
                                        location.reload();
                                    })
                                    
                                } else if(response.info == "no-data") {
                                    swal.fire({
                                        title: warningTitle,
                                        html: "<b>" + response.message + "</b>"                                           
                                    })

                                } 
                            },
                            complete: function() {
                                $("#btnProfvsPi").html(` <i class="fas fa-link"></i> Match PRO-FORMA VS PI  `);
                                $('button').prop('disabled', false); 
                                $scope.proceedMatch = 0 ;
                            }
                        });
                    }                    
                })()
                        
                
            }
        })
       
    }

    $scope.searchCrf = function(e){
        e.preventDefault();
        var string = $("#searchedCrfCv").val();
        if(string == '') 
        {
            $(".search-results").hide();
            $scope.proceedApply = 0;
        }else{
            $http({
                method: 'post',
                url:'../transactionControllers/proformavspicontroller/searchCrf/' + $scope.supplierName + '/' + string
            }).then(function successCallback(response) {
                $scope.searchResult = response.data;
                if($scope.searchResult.length == 0)
                {
                    $scope.hasResults = 0 ;
                    $scope.proceedApply = 0;
                    $scope.searchResult.push( { crf_id: "No Results Found" } );
                }else
                {
                    $scope.hasResults = 1 ;
                    $scope.searchResult = response.data;                    
                }                 
            });            
        }
    }

    $scope.getCrf = function(crf) 
    {
        $(".search-results").hide();
        $scope.crf = crf.crf_no;
        $scope.searchedCrfId = crf.crf_id;
        $scope.crfDate = crf.crf_date;
        $scope.crfAmount = crf.crf_amt;
        $scope.sopNo = crf.sop_no;
        $scope.proceedApply = 1;
    }

    $scope.searchPo = function() {

        if($scope.pitub_po == ""){
            $(".search-results").hide();
        }else{
            $http({
                method: 'post',
                url: $base_url + 'searchtub_po',
                data: { supId : $scope.pitub_sup, po: $scope.pitub_po }
            }).then(function successCallback(response) {
                $scope.searchedPo = response.data
                if($scope.searchedPo.length == 0){
                    $scope.hasResults = 0;
                    $scope.searchedPo.push( { po_no: "No Results Found" } );
                }else{
                    $scope.hasResults = 1 ;
                    $scope.searchedPo = response.data;                    
                }            
            });            
        }
    }

    $scope.displayPoDet = function(data) {
        $(".search-results").hide();
        $scope.poIdHolder = data.po_header_id ;
        $scope.pitub_po   = data.po_no ;
        $scope.pitub_ref  = data.po_reference ;
        $scope.pitub_date = data.posting_date ;
    }

    $scope.uploadPiTub = (e) => {
        e.preventDefault();

        Swal.fire({
            title: warningTitle,
            html: "<b>Are you sure to upload PI ? </b>",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: confirmButtonIcon,
            cancelButtonText: cancelButtonIcon,
            customClass: { confirmButton: confirmButtonClass, cancelButton: cancelButtonClass }
        }).then((result) => {
            if (result.isConfirmed) {

                var formData = new FormData(e.target);
                formData.append('poId', $scope.poIdHolder);

                $.ajax({
                    type: "POST",
                    url: $base_url + 'uploadPiTub',
                    data: formData,
                    enctype: 'multipart/form-data',
                    cache: false,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#loading').show();
                    },
                    success: function(response) {
                        if (response.info == "Success") {                                   
                                      
                            Swal.fire({
                                title: successTitle,
                                html: '<b>' + response.message + '</b>'
                            }).then(function() {
                                location.reload();
                            })
                            
                        } else if(response.info == "Error") {
                            swal.fire({
                                title: warningTitle,
                                html: "<b>" + response.message + "</b>"                                           
                            })
                        } 
                    }, 
                    complete: function() {
                        $('#loading').hide();
                    }
                });  
            }
        })
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
        $("#createpi_date").datepicker({
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