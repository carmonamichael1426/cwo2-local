window.myApp.controller('uploadedtransaction-controller', function($scope, $http) {

    const warningTitle = "<i class='fas fa-exclamation-triangle fa-lg' style='color:#e65c00'></i>";
    const successTitle = "<i class='fas fa-check-circle fa-lg' style='color:#28a745'></i>";
    const infoTitle = "<i class='fas fa-info-circle fa-lg' style='color:#005ce6'></i>";

    $scope.table = {};

    $scope.getSuppliers = function() {
        $http({
            method: 'GET',
            url: $base_url + 'getSuppliersSop'
        }).then(function successCallback(response) {
            $scope.suppliers = response.data;
        });
    }
    $scope.getSuppliers();

    $scope.getCustomers = function() {
        $http({
            method: 'GET',
            url: $base_url + 'getCustomersSop'
        }).then(function successCallback(response) {
            $scope.customers = response.data;
        });
    }
    $scope.getCustomers();

   
    $scope.generateUploadedTransaction = function(){

        
        if ($.fn.DataTable.isDataTable('#uploadedtransactiontable')) {
            $scope.tableShow = false;
            $scope.table.destroy();
            $scope.documents = [];
        }

        var data = { type : $scope.transactionType, supplierSelect: $scope.supplierSelect, locationSelect: $scope.locationSelect  }
        $http({
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8' },
            method: 'POST',
            url: $base_url + 'generateUploadedTransaction',
            data: $.param(data)
        }).then(function successCallback(response) {
            if(response.data != ''){
                $scope.tableShow = true;
                $scope.documents = response.data;
                $(document).ready(function() { 
                    setTimeout(function() {
                        $scope.table = $('#uploadedtransactiontable').DataTable({
                            destroy: true
                        });
                    }, 100)
                });                
                
            } else {
                $scope.tableShow = false;
                swal.fire({
                    title: infoTitle,
                    html: "<b> No uploaded document for this supplier and/or location! </b>"
                })
            }
            
        });
    }

    $scope.downloadDocument = function(data){
        var url   = $base_url + data.document_path;
        var link  = document.createElement('a');
        link.href = url;
        link.download = data.document_path.substr(data.document_path.lastIndexOf('/') + 1);
        link.click();

    }

  

});