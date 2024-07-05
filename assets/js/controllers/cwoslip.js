window.myApp.controller('cwoslip-controller', function($scope, $http, $window, $sce) {
    $scope.getSuppliers = function() {
        $http({
            method: 'get',
            url: `${$base_url}getSuppliers`
        }).then(function successCallback(response) {
            $scope.suppliers = response.data;
        });
    }

    $scope.getCustomers = function() {
        $http({
            method: 'get',
            url: `${$base_url}getCustomers`
        }).then(function successCallback(response) {
            $scope.customers = response.data;
        });
    }

    $scope.getPurchaseOrder = function() {

        $http({
            method: 'POST',
            url: `../transactionControllers/cwoslipcontroller/getPO/${$scope.supplierName}/${$scope.locationName}`
        }).then(function successCallback(response) {
            // console.log(response.data);
            $scope.podata = response.data;
        });
    }

    $scope.getInvoices = function() {

        console.log($scope.poNumber);

        $http({
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8' },
            method: 'POST',
            url: $base_url + 'getDataLedgerInvoice',
            data: $.param(data),
            responseType: 'json'
        }).then(function successCallback(response) {

            $scope.pi_no = response.data.PI_header.pi_no;;
            $scope.vendor_invoice_no = response.data.PI_header.vendor_invoice_no;
            $scope.posting_date = response.data.PI_header.posting_date;
            $scope.amt_including_vat = response.data.PI_header.amt_including_vat;

            $scope.PI_Lines = response.data.PI_line;
            $scope.PI_TotalAmount = response.data.PI_Total;
        });
    }
});