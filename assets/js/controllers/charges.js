window.myApp.controller('charges-controller', function ($scope, $http, $window) {
    // ============== CHARGES CONTROL ============== //

    const warningTitle = "<i class='fas fa-exclamation-triangle fa-lg' style='color:#e65c00'></i>";
    const successTitle = "<i class='fas fa-check-circle fa-lg' style='color:#28a745'></i>";
    const infoTitle = "<i class='fas fa-info-circle fa-lg' style='color:#005ce6'></i>";
    const confirmButtonIcon = "<i class='fas fa-thumbs-up'></i> ";
    const cancelButtonIcon = "<i class='fas fa-thumbs-down'></i>";
    const confirmButtonClass = "btn btn-outline-success";
    const cancelButtonClass = "btn btn-light";

    $scope.chargesTypeTable = function () {
        $http({
            method: 'get',
            url: '../masterfileController/Chargescontroller/fetchChargesType'
        }).then(function successCallback(response) {
            $(document).ready(function () { $('#chargesTypeTable').DataTable(); });
            $scope.chargesTypeData = response.data;
        });
    }

    $scope.saveChargesType = function (e) {
        e.preventDefault();
        var formData = new FormData(e.target);

        Swal.fire({
            title: warningTitle,
            html: '<b>Are you sure to add charges type: "' + $scope.chargesType + '"?</b>',
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: confirmButtonIcon + " Yes",
            cancelButtonText: cancelButtonIcon + " No",
            customClass: { confirmButton: confirmButtonClass, cancelButton: cancelButtonClass }
        }).then((result) => {

            if (result.dismiss != 'cancel') {
                $.ajax({
                    type: 'POST',
                    url: '../masterfileController/chargescontroller/addChargesType',
                    data: formData,
                    enctype: 'multipart/form-data',
                    async: true,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.info == 'Success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then((result) => {
                                location.reload();
                            });
                        } else if (response.info == 'Error Saving') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        } else if (response.info == 'No Data') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        } else if (response.info == 'Exist') {
                            Swal.fire({
                                icon: 'info',
                                title: 'Duplicate',
                                text: response.message
                            });
                        }
                    }
                });
            } else {
                Swal.close();
            }
        })
    }

    $scope.fetchChargesTypeData = function (data) {
        $scope.charges_id = data.charges_id;
        $scope.charges_type = data.charges_type;
    }

    $scope.updateChargesType = function (e) {
        e.preventDefault();

        var formData = new FormData(e.target);
        formData.append('ID', $scope.charges_id);

        Swal.fire({
            title: warningTitle,
            html: '<b>Are you sure to update this charges type?</b>',
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: confirmButtonIcon + " Yes",
            cancelButtonText: cancelButtonIcon + " No",
            customClass: { confirmButton: confirmButtonClass, cancelButton: cancelButtonClass }
        }).then((result) => {

            if (result.dismiss != 'cancel') {
                $.ajax({
                    type: 'POST',
                    url: '../masterfileController/chargescontroller/editChargesType',
                    data: formData,
                    enctype: 'multipart/form-data',
                    async: true,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.info == 'Success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then((result) => {
                                location.reload();
                            });
                        } else if (response.info == 'Error Saving') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        } else if (response.info == 'No Data') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        } else if (response.info == 'Exist') {
                            Swal.fire({
                                icon: 'info',
                                title: 'Duplicate',
                                text: response.message
                            });
                        }
                    }
                });
            } else {
                Swal.close();
            }
        })
    }


    $scope.deactivateChargesType = function (data) {
        // console.log(data);
        Swal.fire({
            title: warningTitle,
            html: '<b> Are you sure to deactivate ' + data.charges_type + ' ?</b>',
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: confirmButtonIcon + " Yes",
            cancelButtonText: cancelButtonIcon + " No",
            customClass: { confirmButton: confirmButtonClass, cancelButton: cancelButtonClass }
        }).then((result) => {
            if (result.isConfirmed) {
                $http({
                    method: 'post',
                    url: $base_url + 'deactivateChargesType',
                    data: { charges_id: data.charges_id }
                }).then(function successCallback(response) {
                    var icon = "";
                    if (response.data.info == "Info") {
                        icon = infoTitle;
                    } else if (response.data.info == "Success") {
                        icon = successTitle;
                    } else if (response.data.info == "Error") {
                        icon = warningTitle;
                    }
                    Swal.fire({
                        title: icon,
                        html: "<b> " + response.data.message + " </b>"
                    }).then(function () {
                        location.reload();
                    });
                });
            }
        });
    }


});