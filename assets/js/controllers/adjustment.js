window.myApp.controller('adjustment-controller', ($scope, $http) => {

    const warningTitle = "<i class='fas fa-exclamation-triangle fa-lg' style='color:#e65c00'></i>";
    const successTitle = "<i class='fas fa-check-circle fa-lg' style='color:#28a745'></i>";
    const infoTitle = "<i class='fas fa-info-circle fa-lg' style='color:#005ce6'></i>";
    const confirmButtonIcon = "<i class='fas fa-thumbs-up'></i> ";
    const cancelButtonIcon = "<i class='fas fa-thumbs-down'></i>";
    const confirmButtonClass = "btn btn-outline-success mr-2";
    const cancelButtonClass = "btn btn-light";

    $scope.positive = true;
    
    $scope.getSuppliers = () => {
        $http({
            method: 'get',
            url: $base_url + 'getSuppliers'
        }).then(function successCallback(response) {
            $scope.suppliers = response.data;
        });
    }
    $scope.getSuppliers();

    $scope.changeSupplier = () => 
    {
        $scope.searchedCRF = {};
        $scope.searchVariance = '';
        $(".search-results2").hide();
    }

    $scope.searchVar = (e) => 
    {
        var string = $("#searchVariance").val();
        if(string == '') 
        {
            $(".search-crf").hide();
        } else {
            $http({
                method: 'post',
                url: $base_url + 'searchCRFVar',
                data: { str: string, supId: $scope.selectSupplier  }
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

    $scope.displayresult = (data) => 
    {
        $(".search-crf").hide();
        $scope.crfId            = data.crf_id;
        $scope.varianceId       = data.variance_id;
        $scope.searchVariance   = data.crf_no;
        $scope.crfdate          = data.crf_date;
        $scope.varianceamt      = data.variance_amount;
        $scope.balanceamt       = data.balance;
    }

    $scope.closeAdjForm = () => 
    {
        $scope.selectSupplier = null;
        $scope.searchVariance = null;
        $scope.crfdate        = null;
        $scope.varianceamt    = null;
        $scope.balanceamt     = null;
        $scope.desc           = '';
        $scope.adjamt         = null;
        $scope.crfId          = 0;
        $scope.varianceId     = 0;

    }

    $scope.settype = (e) => 
    {
        e.preventDefault();

        if( $scope.buttonText == 'POSITIVE ADJUSTMENT'){
            $scope.buttonText = 'NEGATIVE ADJUSTMENT';
            $scope.positive = false;
        } else if( $scope.buttonText == 'NEGATIVE ADJUSTMENT' ) {
            $scope.buttonText = 'POSITIVE ADJUSTMENT';
            $scope.positive = true;
        }

    }

    $scope.createAdjustment = (e) =>
    {
        e.preventDefault();

        var formData = {'varianceId' : $scope.varianceId, 'crfId': $scope.crfId, 'supId': $scope.selectSupplier, 'desc' : $scope.desc, 'amount' : $scope.adjamt, 'positive' : $scope.positive };
        
        Swal.fire({
            title: warningTitle,
            html: "<b> Are you sure to submit adjustment ? </b>",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: confirmButtonIcon,
            cancelButtonText: cancelButtonIcon,
            customClass: { confirmButton: confirmButtonClass, cancelButton: cancelButtonClass }
        }).then((result) => {
            if (result.isConfirmed) {

                $('#loading').show();
                $http({
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8' },
                    method: 'POST',
                    url: $base_url + 'submitAdjustment',
                    data: $.param(formData),
                    responseType: 'json'
                }).then(function successCallback(response) {
                    $('#loading').hide();
                    var titleHeader = "";
                    if(response.data.info == "Success"){
                        titleHeader = successTitle
                    } else {
                        titleHeader = warningTitle
                    }
                    
                    Swal.fire({
                        title: titleHeader,
                        html: '<b> ' + response.data.message + ' </b>'               
                    }).then(function() {
                       location.reload();
                    })
                   
                });
            }       
        })
    }

    $scope.adjustmentTable = (e) => {
        $('#loading').show();
        if ($.fn.DataTable.isDataTable('#adjTable')) {
            $('#adjTable').DataTable().clear();
            $('#adjTable').DataTable().destroy();
            $scope.adjustments = [];
            $scope.adjlists = false;
        }

        $http({
            method: 'POST',
            url: $base_url + 'getAdjs',
            data   : { supId: $scope.supplierName, cusId: $scope.locationName, from: $scope.dateFrom, to: $scope.dateTo  }
        }).then(function successCallback(response) {
            $('#loading').hide();
            if(response.data != ''){                
                $(document).ready(function() { $('#adjTable').DataTable(); }); 
                $scope.adjustments = response.data;               
                $scope.adjlists = true;

            }else {
                swal.fire({
                    title: infoTitle,
                    html: "<b> No Adjustment(s) for this supplier between these dates! </b>"
                   })
               $scope.adjlists = false;              
            }           
        });
    }

    $scope.viewDetails = (data) => {
        $scope.adj_crfno   = data.crf_no ;
        $scope.adj_crfdate = data.crf_date ;
        $scope.adj_no      = data.adj_no ;
        $scope.adj_date    = data.adj_date ;
        $scope.adj_type    = data.type == '0' ? 'NEGATIVE ADJUSTMENT' : 'POSITIVE ADJUSTMENT'
        $scope.adj_part    = data.description ;
        $scope.adj_amount  = data.amount ;
    }

    $(function() {
        $("#dateTo").datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            maxDate: "0d",
            dateFormat: 'yy-mm-dd',
            showAnim: 'slideDown',
            changeMonth: true,
            changeYear: true
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
       
    });
   
});