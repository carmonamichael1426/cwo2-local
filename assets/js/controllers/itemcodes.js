window.myApp.controller('itemcode-controller', function($scope, $http) {
    const customSweet = Swal.mixin({
        customClass: {
            confirmButton: 'btn bg-gradient-primary btn-flat mr-3',
            cancelButton: 'btn bg-gradient-danger btn-flat'
        },
        buttonsStyling: false
    });
    var url = '';
    $scope.label = 'Upload New';


    $scope.toggleSwitch = function() {
        if ($scope.switch) {
            $scope.label = 'Update Items (Location Item Code Only)';
        } else {
            $scope.label = 'Upload New';
        }
    }

    $scope.generateItems = function() {
        var formData = {
            supplierID: $scope.supplierName,
            customerID: $scope.locationName
        };

        $scope.items = '';
        $scope.itemsTableToggle = false;

        $http({
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8' },
            method: 'POST',
            url: $base_url + 'generateItems',
            data: $.param(formData),
            responseType: 'json'
        }).then(function successCallback(response) {
            if (response.data != '') {
                $(document).ready(function() { $('#itemsTable').DataTable(); });

                $scope.itemsTableToggle = true;
                $scope.items = response.data;
            } else {
                $scope.items = '';
                $scope.itemsTableToggle = false;
                Swal.fire({
                    title: 'No Data Found'
                });
            }
        });
    }

    $scope.getSuppliers = function() {
        $http({
            method: 'get',
            url: $base_url + 'getSuppliers'
        }).then(function successCallback(response) {
            $scope.suppliers = response.data;
        });
    }

    $scope.getCustomers = function() {
        $http({
            method: 'get',
            url: $base_url + 'getCustomers'
        }).then(function successCallback(response) {
            $scope.customers = response.data;
        });
    }

    $scope.getMappingItems = function() {
        var formData = {
            supplierID: $scope.supplierSelectMapping,
            customerID: $scope.locationSelectMapping
        };

        $http({
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8' },
            method: 'POST',
            url: $base_url + 'getNoMapItems',
            data: $.param(formData),
            responseType: 'json'
        }).then(function successCallback(response) {

            if (response.data != '') {
                $scope.itemsMap = response.data;
            } else {
                $scope.itemsMap = '';
            }
        });
    }

    $scope.mapItemCodes = function(e) {
        e.preventDefault();

        var itemArray1 = [];
        var formData = new FormData(e.target);
        var itemCodeMappingData = JSON.parse(angular.toJson($scope.itemCodeMappingData));
        formData = convertModelToFormData(itemCodeMappingData, formData, 'itemCodeMappingData');

        customSweet.fire({
            title: `<i class="fas fa-question" style="color: #fcbe03;"></i>`,
            html: `<b> Are you sure to map items?</b>`,
            showCancelButton: true,
            showConfirmButton: true,
            confirmButtonText: 'Proceed',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false,
        }).then((result) => {

            if (result.dismiss != 'cancel') {
                $.ajax({
                    type: 'POST',
                    url: `${$base_url}saveMapItems`,
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

                        if (response.info == 'Items-Mapped') {
                            customSweet.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then((result) => {
                                location.reload();
                            });
                        } else if (response.info == 'Error-Mapping') {
                            customSweet.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        } else if (response.info == 'No-Data') {
                            customSweet.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        } else if (response.info == 'Duplicate-Item') {
                            for (let i = 0; i < response.items.length; i++) {
                                itemArray1.push(response.items[i]);
                            }

                            customSweet.fire({
                                title: `<i class="fas fa-exclamation-circle" style="color: orange;"></i>`,
                                html: `<strong>${response.message}</strong> <br><br> <label> Item Code: </label> <i>${itemArray1}</i>`
                            });
                        } else if (response.info == 'Mapped-Duplicate') {
                            for (let i = 0; i < response.items.length; i++) {
                                itemArray1.push(response.items[i]);
                            }

                            customSweet.fire({
                                title: `<i class="fas fa-exclamation-circle" style="color: orange;"></i>`,
                                html: `<strong>${response.message}</strong> <br><br> <label> Item Code: </label> <i>${itemArray1}</i>`
                            }).then((result) => {
                                location.reload();
                            });
                        }
                    }
                });
            } else {
                Swal.close();
            }
        })
    }

    $scope.uploadMapping = function(e) {
        e.preventDefault();

        customSweet.fire({
            html: `<strong>Are you sure to upload Item Codes?</strong>`,
            showCancelButton: true,
            showConfirmButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false,
        }).then((result) => {
            if (result.dismiss !== 'cancel') {
                var formData = new FormData(e.target);
                // var files = $('#itemCodes')[0].files;
                // formData.append('file', files[0]);

                $.ajax({
                    type: 'POST',
                    url: $base_url + 'uploadMapping',
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

                        if (response.info == 'Format') {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-right',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            })

                            Toast.fire({
                                icon: 'error',
                                title: response.message
                            })
                        } else if (response.info == 'Failed') {
                            customSweet.fire({
                                title: `<i class="fas fa-exclamation-triangle" style="color: red;"></i>`,
                                text: response.message
                            })
                        } else if (response.info == 'Error-Saving') {
                            customSweet.fire({
                                icon: 'error',
                                title: response.info,
                                text: response.message
                            })
                        } else if (response.info == 'Mapped') {
                            customSweet.fire({
                                icon: 'success',
                                title: response.info,
                                text: response.message
                            }).then((result) => {
                                location.reload();
                            });
                        } else if (response.info == 'Duplicate') {
                            customSweet.fire({
                                icon: 'info',
                                title: response.info,
                                text: response.message
                            })
                        } else if (response.info == 'Incorrect Supplier') {
                            customSweet.fire({
                                icon: 'info',
                                title: response.info,
                                text: response.message
                            })
                        } else if (response.info == 'Added-Duplicate') {
                            customSweet.fire({
                                icon: 'info',
                                title: response.info,
                                text: response.message
                            }).then((result) => {
                                location.reload();
                            });
                        }
                    }
                });
            } else {
                Swal.close();
            }
        })
    }

    $scope.uploadItems = function(e) {
        e.preventDefault();

        if ($scope.switch) {
            url = `${$base_url}updateItemCodes`;
        } else {
            url = `${$base_url}uploadItems`;
        }
        customSweet.fire({
            html: `<strong>Are you sure to upload Item Codes?</strong>`,
            showCancelButton: true,
            showConfirmButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false,
        }).then((result) => {
            if (result.dismiss !== 'cancel') {
                var formData = new FormData(e.target);
                // var files = $('#itemCodes')[0].files;
                // formData.append('file', files[0]);

                $.ajax({
                    type: 'POST',
                    url: url,
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

                        if (response.info == 'Format' || response.info == 'Failed' || response.info == 'Error-Saving' || response.info == 'Incorrect Supplier' || response.info == 'Duplicate') {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-right',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            })

                            Toast.fire({
                                icon: 'error',
                                title: response.message
                            })
                        } else if (response.info == 'Success') {
                            customSweet.fire({
                                icon: 'success',
                                title: response.info,
                                text: response.message
                            }).then((result) => {
                                location.reload();
                            });
                        } else if (response.info == 'Duplicate') {
                            customSweet.fire({
                                icon: 'info',
                                title: response.info,
                                text: response.message
                            })
                        } else if (response.info == 'Added-Duplicate') {
                            customSweet.fire({
                                icon: 'info',
                                title: response.info,
                                text: response.message
                            }).then((result) => {
                                location.reload();
                            });
                        }
                    }
                });
            } else {
                Swal.close();
            }
        })
    }

    $scope.uploadUpdate = function(e) {
        e.preventDefault();

        customSweet.fire({
            html: `<strong>Are you sure to update Item Codes?</strong>`,
            showCancelButton: true,
            showConfirmButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false,
        }).then((result) => {
            if (result.dismiss !== 'cancel') {
                var formData = new FormData(e.target);
                // var files = $('#itemCodesUpdated')[0].files;
                // formData.append('file', files[0]);

                $.ajax({
                    type: 'POST',
                    url: $base_url + 'updateItemCodes',
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

                        if (response.info == 'Format') {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-right',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            })

                            Toast.fire({
                                icon: 'error',
                                title: response.message
                            })
                        } else if (response.info == 'Failed') {
                            customSweet.fire({
                                title: `<i class="fas fa-exclamation-triangle" style="color: red;"></i>`,
                                text: response.message
                            })
                        } else if (response.info == 'Error-Updating') {
                            customSweet.fire({
                                icon: 'error',
                                title: response.info,
                                text: response.message
                            })
                        } else if (response.info == 'Updated') {
                            customSweet.fire({
                                icon: 'success',
                                title: response.info,
                                text: response.message
                            }).then((result) => {
                                location.reload();
                            });
                        } else if (response.info == 'No-Items') {
                            customSweet.fire({
                                icon: 'info',
                                title: response.info,
                                text: response.message
                            })
                        } else if (response.info == 'Incorrect-Supplier') {
                            customSweet.fire({
                                title: `<i class="fas fa-times-circle" style="color: red;"></i>`,
                                html: `<strong>${response.info} : ${response.message}</strong>`
                            })
                        } else if (response.info == 'Updated-No-Items') {
                            customSweet.fire({
                                icon: 'success',
                                title: 'Updated',
                                text: response.message
                            }).then((result) => {
                                location.reload();
                            });
                        }
                    }
                });
            } else {
                Swal.close();
            }
        })
    }

    $scope.editItem = (items) => {
        $scope.item_id = items.id;
        $scope.supplierEdit = items.supplier_id;
        $scope.locationEdit = items.customer_code;
        $scope.itemcode_supplier_edit = items.itemcode_sup;
        $scope.itemcode_location_edit = items.itemcode_loc;
        $scope.description_edit = items.description;
    }

    $scope.updateItem = (e) => {

        e.preventDefault();

        var formData = new FormData(e.target);
        formData.append('ID', $scope.item_id);
        formData.append('supplier_id', $scope.supplierEdit);
        formData.append('customer_code', $scope.locationEdit);

        customSweet.fire({
            title: 'Map Item?',
            html: `Supplier Item Code: <b>${$scope.itemcode_supplier_edit}</b><br> Location Item Code: <b>${$scope.itemcode_location_edit}</b><br> Description: <b>${$scope.description_edit}</b>`,
            showCancelButton: true,
            showConfirmButton: true,
            confirmButtonText: 'Proceed',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false,
        }).then((result) => {

            if (result.dismiss != 'cancel') {
                $.ajax({
                    type: 'POST',
                    url: $base_url + 'updateNewItems',
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

                        if (response.info == 'Updated') {
                            customSweet.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then((result) => {
                                 // location.reload();
                                $('#updateItemCode').modal('toggle');
                                $scope.generateItems();
                            });
                        } else if (response.info == 'Error Saving') {
                            customSweet.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        } else if (response.info == 'No Data') {
                            customSweet.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        } else if (response.info == 'Duplicate') {
                            customSweet.fire({
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

    $scope.deleteItem = (data) => {

        customSweet.fire({
            title: 'Would you like to delete this item?',
            showCancelButton: true,
            showConfirmButton: true,
            confirmButtonText: 'Proceed',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false,
        }).then((result) => {

            if (result.dismiss != 'cancel') {

                $http({
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8' },
                    method: 'POST',
                    url: $base_url + 'deleteItem',
                    data: $.param(data),
                    responseType: 'json'
                }).then(function successCallback(response) {
                    if (response.data.info == 'Deleted') {
                        customSweet.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.data.message
                        }).then((result) => {
                            location.reload();
                        });
                    } else if (response.data.info == 'Error') {
                        customSweet.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.data.message
                        });
                    }
                });
            } else {
                Swal.close();
            }
        })
    }
});