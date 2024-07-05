window.myApp.controller('transactionhistory-controller', function($scope, $http) {

    $scope.getSuppliers = function() {
        $http({
            method: 'GET',
            url: `${$base_url}getSuppliers`
        }).then(function successCallback(response) {
            $scope.suppliers = response.data;
        });
    }
    $scope.getSuppliers();

    $scope.getCustomers = function() {
        $http({
            method: 'GET',
            url: `${$base_url}getCustomers`
        }).then(function successCallback(response) {
            $scope.customers = response.data;
        });
    }
    $scope.getCustomers();

    $scope.generateTHistory = function() {

        var data = { transactionType: $scope.transactionType, supplierSelect: $scope.supplierSelect, locationSelect: $scope.locationSelect };
        $http({
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8' },
            method: 'POST',
            url: $base_url + 'getTransactionHistory',
            data: $.param(data),
            responseType: 'json'
        }).then(function successCallback(response) {
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
        window.open($base_url + 'files/Reports/POvsProforma/' + data.filename);
    }
});