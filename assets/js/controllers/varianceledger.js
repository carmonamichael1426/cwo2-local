window.myApp.controller('varianceledger-controller', ($scope, $http) => {

    $scope.getSuppliers = () => {
        $http({
            method: 'get',
            url: $base_url + 'getSuppliers'
        }).then(function successCallback(response) {
            $scope.suppliers = response.data;
        });
    }

    

    $scope.getDetails = () => {
        $http({
            method: 'POST',
            url: $base_url + 'getSuppliers'
        }).then(function successCallback(response) {
            angular.forEach(response.data, function(item) {
                if ($scope.supplierName == item.supplier_id) {
                    $scope.supplierCode = item.supplier_code;
                    $scope.supplierAcroname = item.acroname;
                }
            });
        });
    }

    $scope.generateLedger = (supplier_id) => {

        $('#loading').show();
        if ($.fn.DataTable.isDataTable('#varianceledgerTable')) {
            $('#varianceledgerTable').DataTable().clear();
            $('#varianceledgerTable').DataTable().destroy();
            $scope.ledger = [];
            $scope.tableShow = false;
        }

        $http({
            method: 'POST',
            url: $base_url + 'generateVarianceLedger',
            data : {supId: supplier_id}
        }).then(function successCallback(response) {
            $('#loading').hide();
            if (response.data != '') {
                $scope.ledger = response.data;
                $scope.tableShow = true;
                $(document).ready(function() {
                    $('#varianceledgerTable').DataTable();
                });
            } else {
                $scope.tableShow = false;
                Swal.fire({
                    title: 'No Data Found.'
                })
            }
        });
    }

    $scope.getCrfDetails = function(data) {
        
        $scope.varianceAmt = data.debit ;
        $scope.origDebit   = data.debit_orig ;
        $scope.paymentType = true;
        $http({
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8' },
            method: 'POST',
            url: $base_url + 'getCrfDetails',
            data: $.param(data),
            responseType: 'json'
        }).then(function successCallback(response) {
            $scope.crfNo          = response.data.crf.crf_no;
            $scope.crfDate        = response.data.crf.crf_date;
            $scope.collectorsName = response.data.crf.collector_name;
            $scope.crfAmount  = response.data.crf.crf_amt;
            $scope.remarks    = response.data.crf.remarks;
            $scope.paidAmount = response.data.crf.paid_amt;

            $scope.mentions        = response.data.mentions;
            $scope.mentionsTotal   = response.data.mentionsTotal;
            $scope.adjustments     = response.data.adjustments;
            $scope.adjustmentTotal = response.data.adjustmentTotal;

            $scope.balance   = parseFloat($scope.varianceAmt)  + parseFloat(response.data.mentionsTotal) ;
            
           
        });
    }
});