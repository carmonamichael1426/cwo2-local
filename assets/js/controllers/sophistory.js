window.myApp.controller('sophistory-controller', function($scope, $http) {

    $scope.getSuppliers = function() {
        $http({
            method: 'GET',
            url: `${$base_url}getSuppliersSop`
        }).then(function successCallback(response) {
            $scope.suppliers = response.data;
        });
    }
    $scope.getSuppliers();

    $scope.getCustomers = function() {
        $http({
            method: 'GET',
            url: `${$base_url}getCustomersSop`
        }).then(function successCallback(response) {
            $scope.customers = response.data;
        });
    }
    $scope.getCustomers();

    $scope.generateTHistory = function() {

        $('#loading').show();
        if ($.fn.DataTable.isDataTable('#transactionHistoryTable')) {
            $('#transactionHistoryTable').DataTable().clear();
            $('#transactionHistoryTable').DataTable().destroy();
            $scope.tHistory = [];
            $scope.tableShow = false;
        }

        var data = { transactionType: $scope.transactionType, supplierSelect: $scope.supplierSelect, locationSelect: $scope.locationSelect };
        $http({
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8' },
            method: 'POST',
            url: $base_url + 'generateSopHistory',
            data: $.param(data),
            responseType: 'json'
        }).then(function successCallback(response) {
            $('#loading').hide();
            if (response.data != '') {
                $scope.tHistory = response.data;
                $scope.tableShow = true;
                $(document).ready(function() { $('#transactionHistoryTable').DataTable(); });
            } else {
                $scope.tableShow = false;
                Swal.fire({
                    title: 'No Data Found.'
                })

            }
        });
    }

    $scope.viewDocument = function(data) {
        window.open($base_url + 'files/Reports/SOP/' + data.filename);
    }
});