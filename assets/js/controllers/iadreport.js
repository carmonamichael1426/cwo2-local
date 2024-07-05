window.myApp.controller('iadreport-controller', function($scope, $http) {

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
            url: $base_url + 'getSuppliers'
        }).then(function successCallback(response) {
            $scope.suppliers = response.data;
        });
    }

    $scope.generateIADReports = function(e) {
        e.preventDefault();

        var formData = new FormData(e.target);

        $.ajax({
            type: 'POST',
            url: $base_url + 'getIadReports',
            data: formData,
            enctype: 'multipart/form-data',
            async: true,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('#modal_loading').modal({
                    backdrop: 'static',
                    keyboard: false
                });
            },
            success: function(response) {
                $('#modal_loading').modal('toggle');

                if (response.info == 'Success') {

                    window.open($base_url + 'files/Reports/IADReports/' + response.file);
                    location.reload();
                } else if (response.info == 'No Data') {
                    Swal.fire({
                        title: response.message
                    })

                }
            }
        });
    }
});