window.myApp.controller('proformavspihistory-controller', function($scope, $http) {

    const warningTitle = "<i class='fas fa-exclamation-triangle fa-lg' style='color:#e65c00'></i>";
    const successTitle = "<i class='fas fa-check-circle fa-lg' style='color:#28a745'></i>";
    const infoTitle = "<i class='fas fa-info-circle fa-lg' style='color:#005ce6'></i>";
    
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
            url: $base_url + 'generateProfvPiHistory',
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
        window.open($base_url + 'files/Reports/ProformaVsPi/' + data.filename);
    }

    $scope.deleteDocument = function(data){
        $http({
            method: 'POST',
            url: $base_url + "deleteprofvspi",
            data: { id: data.tr_id, filename: data.filename }
        }).then(function successCallback(response) {

            var icon = "";
            if(response.data.info == "success"){
                icon = successTitle;
            } else if(response.data.info == "error"){
                icon = warningTitle;
            }
            Swal.fire({
                title: icon,
                html: '<b> ' + response.data.message +  ' </b>'
            }).then(function(){
                location.reload();
            })
        });
    }
});