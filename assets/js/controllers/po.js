window.myApp.controller('po-controller', function($scope, $http, $window) {
    const warningTitle = "<i class='fas fa-exclamation-triangle fa-lg' style='color:#e65c00'></i>";
    const infoTitle = "<i class='fas fa-info-circle fa-lg' style='color:#005ce6'></i>";
    const successTitle = "<i class='fas fa-check-circle fa-lg' style='color:#28a745'></i>";
    const confirmButtonIcon = "<i class='fas fa-thumbs-up'></i> ";
    const cancelButtonIcon = "<i class='fas fa-thumbs-down'></i>";
    const confirmButtonClass = "btn btn-outline-success mr-2";
    const cancelButtonClass = "btn btn-light";

    $scope.countSelected = 0 ;
    $scope.totalPoAmount_c = 0;
    $scope.totalPoQty_c = 0;

    $scope.loadSupplier = () =>
    {

        $http({
            method: 'get',
            url: $base_url + 'getSuppliersForPO'
        }).then(function successCallback(response) {
            $scope.suppliers = response.data;
        });
    }
    $scope.loadSupplier();

    $scope.loadCustomer = () =>
    {
        $http({
            method: 'get',
            url: $base_url + 'getCustomersForPO'
        }).then(function successCallback(response) {
            $scope.customers = response.data;
        });
    }
    $scope.loadCustomer();

    $scope.uploadPo = (ev) =>
    {
        ev.preventDefault();

        var formData = new FormData(ev.target);
        var msg = "";
        $.ajax({
            type: "POST",
            url: $base_url + 'uploadPo',
            data: formData,
            async: false,
            cache: false,
            processData: false,
            contentType: false,
            success: function(response) {
                
                if( response.info == "Error-ext" ) {
                    msg = "<b> " + response.message + " <br> File extension : " + response.ext + " </b>"
                } else if ( response.info == "Error-item" ) {
                    var items = Object.values(response.item);
                    var itemNotFound = "";
                    for (let i = 0; i < items.length; i++) {
                        itemNotFound = items;
                    }
                    msg = "<b> " +  response.message +" </b> <br> " + "<i>" + itemNotFound + "</i>"

                } else if( response.info == "ExtNotFound" ){

                    msg = "<b> " + response.message + " </b>"
                } else if( response.info == "Error"){
                    msg = "<b> " + response.message + " </b>"
                }

                if( response.info == "Success" ){
                    Swal.fire({
                        title: successTitle,
                        html: "<b> " + response.message + " </b>"
                    }).then(function() {
                        location.reload();
                    })
                } else if( response == "duplicate" ){
                    Swal.fire({
                        title: warningTitle,
                        html: "<b> PO already exists! </b>"
                    }).then(function() {
                        location.reload();
                    })

                } else {
                    Swal.fire({
                        title: warningTitle,
                        html: msg
                    }).then(function() {
                        location.reload();
                    })
                }
            }
        });

    }

    $scope.closePoForm = () => {
        $("#uploadPoForm").trigger("reset");
    }

    $scope.poTable = (e) => {

        $('#loading').show();
        if ($.fn.DataTable.isDataTable('#poTable')) {
            $('#poTable').DataTable().clear();
            $('#poTable').DataTable().destroy();
            $scope.po = [];
            $scope.poList = false;
        }

        $http({
            method: 'POST',
            url: $base_url + 'getPOs',
            data: { supId: $scope.supplierName, cusId: $scope.locationName, from: $scope.dateFrom, to: $scope.dateTo }
        }).then(function successCallback(response) {
            $('#loading').hide();
            if (response.data != '') {                
                $(document).ready(function() { $('#poTable').DataTable(); });
                $scope.po = response.data;
                $scope.poList = true;
            } else {
                swal.fire({
                    title: infoTitle,
                    html: "<b> No Pending Matches for this supplier and location! </b>"
                })
                $scope.poList = false;
            } 
        });
     
    }

    $scope.viewPoDetails = (data) => {
        var poId = data.po_header_id;
        var supId = data.supplier_id;
        var total = 0;
        $scope.poNo = data.poNo;
        $scope.poRef = data.ref;

        $http({
            method: 'post',
            url: '../transactionControllers/pocontroller/getPoDetails/' + poId + '/' + supId
        }).then(function successCallback(response) {
            $scope.poDetails = response.data;
            angular.forEach($scope.poDetails, function(value, key) {
                total += parseInt(value.qty) * parseFloat(value.direct_unit_cost);
            });
            $scope.poAmt = total.toFixed(2);

        });
    }

    $scope.loadItems = () =>
    {
        $scope.itemshasloaded = false;
        if ($.fn.DataTable.isDataTable('#tblsupitems')) {
            $('#tblsupitems').DataTable().clear();
            $('#tblsupitems').DataTable().destroy();
            $scope.supItems = [];
            $scope.itemshasloaded = false;
        }

        $http({
            method: 'POST',
            url: $base_url + 'loadItems',
            data: { supId: $scope.createpo_sup }
        }).then(function successCallback(response) {
            $(document).ready(function() { $('#tblsupitems').DataTable(); });
            $scope.itemshasloaded = true;
            $scope.supItems = response.data;
        });
    }

    $scope.countCheckedItems = (e) =>
    {
        $scope.countSelected = 0 ;

        angular.forEach($scope.supItems, function(value, key) {
            if(value.selected){
                $scope.countSelected ++ ;
            }            
        });
    }

    $scope.addToPotable = (e) =>
    {
        e.preventDefault();
        $scope.poItems = [{}];
        $scope.poItems.splice(0,1);      
        $scope.totalPoAmount = 0;
        $scope.countPoLines = 0;
    
        angular.forEach($scope.supItems, function(line, key) {
            if(line.selected){       
                $scope.poItems.push({'item_code': line.itemcode_loc,
                                     'description' : line.description,
                                     'qty'         : 0,
                                     'uom'         : "",
                                     'cost'        : 0,
                                     'amount'      : 0
                                     })
                $scope.totalInvoiceAmount += round(line.direct_unit_cost * line.qty,2);
                $scope.countPoLines ++ ;
            }                
        });          
          
    }

    $scope.calculatePo = () =>{
        $scope.totalPoAmount_c = 0;
        $scope.totalPoQty_c = 0;
        
        angular.forEach($scope.poItems, function(value, key) {
            $scope.totalPoAmount_c += round(value.qty * value.cost,2); 
            $scope.totalPoQty_c +=  parseInt(value.qty) ;
        });

    }

    $scope.createPo = (e) =>
    {
        e.preventDefault();

        Swal.fire({
            title: warningTitle,
            html: "<b>Are you sure to save PO ? </b>",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: confirmButtonIcon,
            cancelButtonText: cancelButtonIcon,
            customClass: { confirmButton: confirmButtonClass, cancelButton: cancelButtonClass }
        }).then((result) => {
            if (result.isConfirmed) {
                $('#loading').show();
                $http({
                    method: 'POST',
                    url: $base_url + 'createPo',
                    data: {  'pono'   : $scope.createpo_po,
                             'poref'  : $scope.createpo_ref,
                             'podate' : $scope.createpo_date,
                             'supid'  : $scope.createpo_sup,
                             'items'  : JSON.parse(angular.toJson($scope.poItems))
                          }
                }).then(function successCallback(response) {
                    $('#loading').hide();
                    if(response.data.info == 'Success'){
                        swal.fire({
                            title: successTitle,
                            html: "<b>" +  response.data.message +" </b>"
                        }).then(function() {
                            location.reload();
                        })
                    } else {
                        swal.fire({
                            title: warningTitle,
                            html: "<b>" +  response.data.message +" </b>"
                        })
                    }
                });
            }
        })
   
    }

    $(function() {
        $("#dateFrom").datepicker({
            maxDate: "0d",
            dateFormat: 'yy-mm-dd',
            showAnim: 'slideDown',
            changeMonth: true,
            changeYear: true
        });
        $("#dateTo").datepicker({
            maxDate: "0d",
            dateFormat: 'yy-mm-dd',
            showAnim: 'slideDown',
            changeMonth: true,
            changeYear: true
        });
        $("#createpo_date").datepicker({
            maxDate: "0d",
            dateFormat: 'yy-mm-dd',
            showAnim: 'slideDown',
            changeMonth: true,
            changeYear: true
        });
    });
    
   
});