window.myApp.controller('customer-controller', function($scope, $http, $window) {
    // ============== CUSTOMERS CONTROL ============== //

    let warningTitle = "<i class='fas fa-exclamation-triangle fa-lg' style='color:#e65c00'></i>";
    let successTitle = "<i class='fas fa-check-circle fa-lg' style='color:#28a745'></i>";

    $scope.customersTable = function() {
        var request = $http({
            method: 'get',
            url: '../masterfileController/customercontroller/fetchCustomers'
        }).then(function successCallback(response) {
            $(document).ready(function() { $('#customersTable').DataTable(); });
            $scope.customers = response.data;

        });
    }

    $scope.saveCustomer = function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: $base_url + "addCustomer",
            data: { customer: $scope.customer },
            async: false,
            cache: false,
            success: function(response) {
                if (response == "success") {
                    Swal.fire({
                        title: successTitle,
                        html: "<b> Customer is added! </b>"
                    }).then(function() {
                        location.reload();
                    });

                } else {
                    Swal.fire({
                        title: warningTitle,
                        html: "<b> Error saving, please check the detail. </b>"
                    }).then(function() {
                        location.reload();
                    });
                }

            }

        });

    }

    $scope.editCustomer = function(e) {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: $base_url + "updateCustomer",
            data: { ccode: $scope.customerCode, cname: $scope.updateCustomerName, lacroname: $scope.updateAcroname },
            async: false,
            cache: false,
            success: function(response) {
                if (response == "success") {
                    Swal.fire({
                        title: successTitle,
                        html: "<b> Customer updated successfully! </b>"
                    }).then(function() {
                        location.reload();
                    });

                } else {
                    Swal.fire({
                        title: warningTitle,
                        html: "<b> Error update, please check the detail. </b>"
                    }).then(function() {
                        location.reload();
                    });
                }

            }
        });
    }


    $scope.deactivateCustomer = function(data) {

        var ccode = data.customer_code;
        var cname = data.customer_name;

        Swal.fire({
            title: warningTitle,
            html: "<b> DEACTIVATE THIS CUSTOMER ? </b>",
            buttonsStyling: false,
            showConfirmButton: true,
            showCancelButton: true,
            confirmButtonText: "<i class='fas fa-thumbs-up'></i> Proceed",
            cancelButtonText: "<i class='fas fa-thumbs-down'></i> Cancel",
            customClass: {
                confirmButton: "btn btn-outline-success",
                cancelButton: "btn btn-light"
            },
            allowOutsideClick: false,
        }).then((result) => {

            if (result.dismiss != "cancel") {
                $.ajax({
                    type: "POST",
                    url: $base_url + "deactivateCustomer",
                    data: { ccode: ccode },
                    async: false,
                    cache: false,
                    success: function(response) {
                        if (response == "success") {
                            Swal.fire({
                                title: successTitle,
                                html: "<b> Customer is deactivated! </b>"
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            alert(response);
                        }
                    }
                });

            } else {
                Swal.close();
            }
        });

    }

    $scope.fetchCustomerData = function(data) {
        $scope.customerCode = data.customer_code;
        $scope.updateCustomerName = data.customer_name;
        $scope.updateAcroname = data.l_acroname;
    }
});