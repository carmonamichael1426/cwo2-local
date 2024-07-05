window.myApp.controller('deductionreport-controller', function($scope, $http) {

    const warningTitle = "<i class='fas fa-exclamation-triangle fa-lg' style='color:#e65c00'></i>";
    const successTitle = "<i class='fas fa-check-circle fa-lg' style='color:#28a745'></i>";
    const infoTitle = "<i class='fas fa-info-circle fa-lg' style='color:#005ce6'></i>";

    $scope.search = function() {

        if ($scope.searchBy == 'All Supplier') {
            $scope.allSupplier = 'true';
            $(function() {
                $("#dateFrom").datepicker({
                    showOtherMonths: true,
                    selectOtherMonths: true,
                    dateFormat: 'yy-mm-dd',
                    showAnim: 'slideDown',
                    changeMonth: true,
                    changeYear: true
                });
                $("#dateTo").datepicker({
                    showOtherMonths: true,
                    selectOtherMonths: true,
                    dateFormat: 'yy-mm-dd',
                    showAnim: 'slideDown',
                    changeMonth: true,
                    changeYear: true
                });
            });
        } else {
            $scope.allSupplier = 'true';
            $(function() {
                $("#dateFrom").datepicker({
                    showOtherMonths: true,
                    selectOtherMonths: true,
                    dateFormat: 'yy-mm-dd',
                    showAnim: 'slideDown',
                    changeMonth: true,
                    changeYear: true
                });
                $("#dateTo").datepicker({
                    showOtherMonths: true,
                    selectOtherMonths: true,
                    dateFormat: 'yy-mm-dd',
                    showAnim: 'slideDown',
                    changeMonth: true,
                    changeYear: true
                });
            });
        }
    }

    $scope.getSuppliers = function() {
        $http({
            method: 'get',
            url: $base_url + 'getSuppliersSop'
        }).then(function successCallback(response) {
            $scope.suppliers = response.data;
        });
    }

    $scope.loadDeductionType = function()
    {
        $http({
            method: 'post',
            url: $base_url + 'loadDeductionType'
        }).then(function successCallback(response) {
            $scope.deductionTypes = response.data
            $scope.deductionTypes.push({deduction_type_id : 'All',type : 'ALL'});
        });
    }
    
    $scope.generateDeductionRep = function(ev){
        ev.preventDefault();
       
        var formData = new FormData(ev.target);

            $.ajax({
                type: "POST",
                url: $base_url + 'generateDeductionReport',
                data: formData,
                enctype: 'multipart/form-data',
                cache: false,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(response) {
                    
                    if(response.info == "success" ){                       
                        window.open($base_url + 'files/Reports/DeductionReports/' + response.file);

                    } else if(response.info == "no-data"){
                        swal.fire({
                            title: infoTitle,
                            html: response.message
                        })
                    }   
                },
                complete: function() {
                    $('#loading').hide();
                }     
            });
    }

    $scope.getSuppliers();


});