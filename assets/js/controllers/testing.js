window.myApp.controller('testing-controller', function($scope, $http, $window) {
    var targetModal = '';
    var currentModal = '';

    $scope.label = 'Upload New';


    $scope.toggleSwitch = function() {
        // console.log($scope.switch);

        if ($scope.switch) {
            $scope.label = 'Update';
        } else {
            $scope.label = 'Upload New';
        }
    }
    $scope.getData = function() {
        $scope.item = "DECEMBER AVENUE";
    }

    $scope.getData2 = function(data) {
        console.log(data);
        $scope.input2 = data;
    }

    $scope.managersKey = (m, d) => {
        targetModal = d;
        currentModal = m;

        $('#' + m).modal('toggle')
    }

    $scope.authorizeKey = (e) => {
        e.preventDefault();

        console.log(currentModal);
        var formData = new FormData(e.target);

        $.ajax({
            type: 'POST',
            url: $base_url + 'authorize',
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

                if (response.info == 'Auth') {
                    $('#' + currentModal).modal('hide');
                    clearModal(currentModal);
                    $('#' + targetModal).modal('toggle')
                    toastAlert(response.message, 'success');
                } else if (response.info == 'Incorrect') {

                    toastAlert(response.message, 'error');

                } else if (response.info == 'Error') {
                    toastAlert(response.message, 'error');
                }
            }
        });
    }


    const customSweet = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-primary btn-flat mr-3',
            cancelButton: 'btn btn-danger btn-flat',
            toast: true
        },
        buttonsStyling: false
    });

    $scope.sweet = function() {
        $('#myAlert').modal('toggle')
    }

    $scope.loadSupplier = function() {
        $http({
            method: 'get',
            url: $base_url + 'getSuppliersForPI'
        }).then(function successCallback(response) {
            $scope.suppliers = response.data;
        });
    }

    $scope.loadCustomer = function() {
        $http({
            method: 'get',
            url: $base_url + 'getCustomersForPI'
        }).then(function successCallback(response) {
            $scope.customers = response.data;
        });
    }

    $scope.deduction = [];
    $scope.getSOP = function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: $base_url + 'getSOP',
            data: { supId: $scope.selectSupplier, cusId: $scope.selectCustomer },
            async: false,
            cache: false,
            beforeSend: function() {
                $("#genID").html(`<span class="spinner-border spinner-border-sm" role="status"></span>`);
            },
            success: function(data) {

                $scope.sopTrans = data.detailed;
                $scope.deduction = data.ded;
                $scope.sopList = true;

            },
            complete: function() {
                $("#genID").html(` Generate `);
            }

        });
    }

    $scope.viewDeduction = function(data) {
        $scope.deductions = [];
        angular.forEach($scope.deduction, function(value, key) {
            if (value.docno == data.docno) {
                $scope.deductions.push({ key, "docno": value.docno, "desc": value.desc, "amount": value.amount });

            }
        });
        $scope.total = 0;
        $scope.deductions.forEach(a => $scope.total += a.amount);
    }

    

   


});