window.myApp.controller('povspro-controller', function($scope, $http, $window, $sce) {
    var index = 0;
    var targetModal = '';
    var getData = '';
    var currentModal = '';
    $scope.dataContainer = {};
    $scope.reportData = {};

    //======== CREATE PROFORMA ========//
    const warningTitle = "<i class='fas fa-exclamation-triangle fa-lg' style='color:#e65c00'></i>";
    const successTitle = "<i class='fas fa-check-circle fa-lg' style='color:#28a745'></i>";
    const infoTitle = "<i class='fas fa-info-circle fa-lg' style='color:#005ce6'></i>";
    const confirmButtonIcon = "<i class='fas fa-thumbs-up'></i> ";
    const cancelButtonIcon = "<i class='fas fa-thumbs-down'></i>";
    const confirmButtonClass = "btn btn-outline-success mr-2";
    const cancelButtonClass = "btn btn-light";

    $scope.countSelected = 0 ;
    $scope.countProfLines= 0 ;
    $scope.totalInvoiceAmount = 0;
    $scope.totalDiscVat = 0;
    $scope.pricing = "**";
    $scope.unitpriceClass = "";
    //======== CREATE PROFORMA ========//

    $('.nav-tabs a').click(function(e) {
        e.preventDefault();
        index = $($(this).attr('href')).index();
    });

    const customSweet = Swal.mixin({
        customClass: {
            confirmButton: 'btn bg-gradient-primary btn-flat mr-3',
            cancelButton: 'btn bg-gradient-danger btn-flat'
        },
        buttonsStyling: false
    });

    $scope.getPendingMatches = function() {

        var matchesData = {
            supplier_id: $scope.supplierName,
            customer_code: $scope.locationName,
            from : $scope.dateFrom,
            to: $scope.dateTo
           
        }

        if ($.fn.DataTable.isDataTable('#proformaTable')) {
            $('#proformaTable').DataTable().clear();
            $('#proformaTable').DataTable().destroy();
            $scope.pendingMatches = [];
            $scope.pendingMatchesTable = false;
        }

        $http({
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8' },
            method: 'POST',
            url: `${$base_url}getPendingMatchesPRF`,
            data: $.param(matchesData),
            responseType: 'json'
        }).then(function successCallback(response) {
            if (response.data.info == 'Does Not Exist') {
                customSweet.fire({
                    title: `<i class='fas fa-info-circle fa-lg' style='color:#005ce6'></i>`,
                    html: `<strong>${response.data.Message}</strong>`,
                    allowOutsideClick: false,
                    confirmButtonText: 'OK'
                })
                $scope.pendingMatchesTable = false;
            } else if (response.data != '') {
                $scope.pendingMatchesTable = true;
                $scope.pendingMatches = response.data;
                $(document).ready(function() { $('#proformaTable').DataTable(); });
            } else {
                customSweet.fire({
                    title: `<i class='fas fa-info-circle fa-lg' style='color:#005ce6'></i>`,
                    html: `<strong>No Pending Matches Found.</strong>`,
                    allowOutsideClick: false,
                    confirmButtonText: 'OK'
                })
                $scope.pendingMatchesTable = false;

            }
        });
    }

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
            method: 'post',
            url: '../transactionControllers/povsproformacontroller/getPurchaseOrder/' + $scope.supplierSelect + '/' + $scope.customerSelect
        }).then(function successCallback(response) {

            // console.log(response.data);
            $scope.po = response.data;
        });
    }

    $scope.uploadProforma = function(e) {
        e.preventDefault();

        var formData = new FormData(e.target);

        $.ajax({
            type: 'POST',
            url: `${$base_url}uploadProforma`,
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

                if (response.info == 'Invalid Format') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top',
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
                } else if (response.info == 'No Data') {
                    customSweet.fire({
                        icon: 'error',
                        title: response.info,
                        text: response.message
                    })
                } else if (response.info == 'Error') {
                    customSweet.fire({
                        icon: 'error',
                        title: response.info,
                        text: response.message
                    })
                } else if (response.info == 'Uploaded') {
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
                } else if (response.info == 'Item') {
                    var items = Object.values(response.item);
                    var itemNotFound = "";
                    for (let i = 0; i < items.length; i++) {
                        itemNotFound = items;
                    }
                    msg = "<b> " +  response.message +" </b> <br> " + "<i>" + itemNotFound + "</i>"
                    customSweet.fire({
                        icon: 'error',
                        title: response.info,
                        html: msg
                    })
                } 
            }
        });
    }

    $scope.getItems = function(data) {

        console.log(data);
        $scope.reportData = {
            po: data.po_no,
            rep_stat_id: data.rep_stat_id,
            po_header_id: data.po_header_id,
            proforma_header_id: data.proforma_header_id,
            proforma_code: data.proforma_code,
            supplier_code: data.supplier_code,
            customer_code: data.customer_code,
            acroname: data.acroname
        };

        $http({
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8' },
            method: 'POST',
            url: $base_url + 'getMatchItems',
            data: $.param(data),
            responseType: 'json'
        }).then(function successCallback(response) {
            $scope.items = response.data;
        });
    }

    $scope.match = function(e, data) {
        e.preventDefault();

        var items = data;

        for (let i = 0; i < items.length; i++) {

            if (items[i].po_desc == undefined || items[i].po_desc == 'NO SET UP' || items[i].po_desc == '') {
                customSweet.fire({
                    title: `<i class="fas fa-exclamation-triangle" style="color: orange;"></i> ` + 'WARNING!',
                    html: `<strong>There are missing Item codes/Description detected please setup those missing item codes to proceed.</strong>`,
                    allowOutsideClick: false
                })

                return;
            }
        }

        $scope.items = data;
        var formData = new FormData(e.target);
        var container1 = JSON.parse(angular.toJson($scope.items));
        var container2 = JSON.parse(angular.toJson($scope.reportData));
        formData = convertModelToFormData(container1, formData, 'container1');
        formData = convertModelToFormData(container2, formData, 'container2');


        customSweet.fire({
            html: `<strong>Are you sure to match this<br> PO : <i>${$scope.reportData.po}</i><br>Proforma : <i>${$scope.reportData.proforma_code}</i>?</strong>`,
            showCancelButton: true,
            showConfirmButton: true,
            confirmButtonText: 'Match',
            cancelButtonText: 'Cancel',
            allowOutsideClick: false,
        }).then((result) => {

            if (result.dismiss !== 'cancel') {

                $.ajax({
                    type: 'POST',
                    url: $base_url + 'matchPOandProforma',
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

                        response = JSON.parse(response);

                        var itemArray1 = [];
                        var itemArray2 = [];

                        if (response.info == 'Matching Failed') {
                            customSweet.fire({
                                icon: 'error',
                                title: response.info,
                                text: response.message
                            })
                        } else if (response.info == 'Served and Not served') {
                            for (let i = 0; i < response.item_codes.length; i++) {
                                itemArray1.push(response.item_codes[i]);
                            }

                            customSweet.fire({
                                icon: 'success',
                                html: `<label>Matched Succesfully!</label> <br>${response.message}<br><br> <strong>PO ITEMS : <i>${itemArray1}</i></strong>`,
                                width: 600,
                            }).then((result) => {
                                window.open($base_url + 'files/Reports/POvsProforma/' + response.file);
                                location.reload();
                            });
                        } else if (response.info == 'Served and Overserved') {
                            for (let i = 0; i < response.item_codes.length; i++) {
                                itemArray1.push(response.item_codes[i]);
                            }

                            customSweet.fire({
                                icon: 'success',
                                title: 'Matched Succesfully',
                                html: `${response.message}<br><br> <strong><i>${itemArray1}</i></strong>`,
                            }).then((result) => {
                                window.open($base_url + 'files/Reports/POvsProforma/' + response.file);
                                location.reload();
                            });
                        } else if (response.info == 'Served, Not served , and Overserved') {
                            if (response.po_items != null) {
                                for (let i = 0; i < response.po_items.length; i++) {
                                    itemArray1.push(response.po_items[i]);
                                }
                            }

                            if (response.pr_items != null) {
                                for (let i = 0; i < response.pr_items.length; i++) {
                                    itemArray2.push(response.pr_items[i]);
                                }
                            }

                            customSweet.fire({
                                icon: 'success',
                                title: 'Matched Succesfully',
                                html: `${response.message}<br><br> <strong>PO Items: <i>${itemArray1}</i></strong> <br> <strong>Proforma Items: <i>${itemArray2}</i></strong>`,
                            }).then((result) => {
                                window.open($base_url + 'files/Reports/POvsProforma/' + response.file);
                                location.reload();
                            });
                        } else if (response.info == 'Matched') {
                            customSweet.fire({
                                icon: 'success',
                                title: 'Matched Succesfully',
                                html: `${response.message}`,
                            }).then((result) => {
                                window.open($base_url + 'files/Reports/POvsProforma/' + response.file);
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

    $scope.view = function(data) {
        const newLocal = '../transactionControllers/povsproformacontroller/getProforma/';
        $http({
            method: 'POST',
            url: `${newLocal + data.acroname}/${data.po_header_id}/${data.proforma_header_id}`,
        }).then(function successCallback(response) {
            $scope.po_no = response.data[0].po_no + "/" + response.data[0].po_reference;
            $scope.so_no = response.data[0].so_no;
            $scope.pro_code = response.data[0].proforma_code;
            $scope.proforma_line = response.data;
            $scope.acroname_edit = data.acroname;
            $scope.proforma_header_id = data.proforma_header_id;
            $scope.priceCheckStat = data.pricing_status;

            $scope.tableRow = true;
            $scope.uploadRow = false;
            $scope.buttonNAme = 'Replace Proforma';
        });

        $scope.dataContainer = {
            poNo: data.po_no,
            supplierCode: data.supplier_code,
            customerCode: data.customer_code,
            po_reference: data.po_reference
        }
    }

    $scope.editProforma = function(e) {

        // if (e.target != '') {
            e.preventDefault();
        // }

        if ($scope.tableRow == true) {
            var formData1 = [];

            $scope.proforma_line.forEach(function(data) {
                if (data.checkBoxEdit == true) {
                    formArray = {
                        id: data.proforma_line_id,
                        item_code: data.item_code,
                        description: data.description,
                        qty: data.qty,
                        uom: data.uom,
                        price: data.price
                    };

                    formData1.push(formArray);
                }
            });

            // if (e.target != '') {
                // var formData = new FormData(e.target);
            // } else {
                var formData = new FormData();
            // }
            var proforma_line = JSON.parse(angular.toJson(formData1));
            formData = convertModelToFormData(proforma_line, formData, 'proforma_line');

            customSweet.fire({
                title: 'Update Proforma',
                text: 'Are you sure to update Proforma Line?',
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {

                if (result.dismiss !== 'cancel') {
                    $.ajax({
                        type: 'POST',
                        url: `${$base_url}updateProformaLine`,
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
                                    title: response.info,
                                    text: response.message
                                }).then((result) => {
                                    location.reload();
                                });
                            } else if (response.info == 'Error') {
                                customSweet.fire({
                                    icon: 'success',
                                    title: response.info,
                                    text: response.message
                                })
                            } else if (response.info == 'Empty') {
                                customSweet.fire({
                                    icon: 'success',
                                    title: response.info,
                                    text: response.message
                                })
                            }
                        }
                    });
                } else {
                    Swal.close();
                }
            })
        } else {
            var formData = new FormData(e.target);
            // var files = $('#new_proforma')[0].files;
            var container = JSON.parse(angular.toJson($scope.dataContainer));
            formData = convertModelToFormData(container, formData, 'container');
            // formData.append('file', files[0]);

            customSweet.fire({
                title: 'Replace Proforma',
                text: 'Are you sure to replace Proforma Line?',
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {

                if (result.dismiss !== 'cancel') {
                    $.ajax({
                        type: 'POST',
                        url: $base_url + 'replaceProforma',
                        data: formData,
                        enctype: 'multipart/form-data',
                        async: true,
                        cache: false,
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            $('#loading_modal').modal({
                                backdrop: 'static',
                                keyboard: false
                            });
                        },
                        success: function(response) {
                            $('#loading_modal').modal('toggle');

                            if (response.info == 'No File') {
                                const Toast = Swal.mixin({
                                    toast: true,
                                    position: 'top-end',
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    didOpen: (toast) => {
                                        toast.addEventListener('mouseenter', Swal.stopTimer)
                                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                                    }
                                })

                                Toast.fire({
                                    icon: 'warning',
                                    title: response.message
                                })
                            } else if (response.info == 'Invalid Format') {
                                customSweet.fire({
                                    icon: 'error',
                                    title: response.info,
                                    text: response.message
                                });
                            } else if (response.info == 'Error') {
                                customSweet.fire({
                                    icon: 'error',
                                    title: response.info,
                                    text: response.message
                                });
                            } else if (response.info == 'Replaced') {
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
                            }
                        }
                    });
                } else {
                    Swal.close();
                }
            })
        }

    }

    $scope.addDiscountVAT = function(e) {
        e.preventDefault();

        var formData = new FormData(e.target);
        var discountData = JSON.parse(angular.toJson($scope.discountData));
        formData = convertModelToFormData(discountData, formData, 'discountData');

        customSweet.fire({
            text: `Are you sure to Additionals/Deductions to ${$scope.pro_code}?`,
            showCancelButton: true,
            showConfirmButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {

            if (result.dismiss !== 'cancel') {
                $.ajax({
                    type: 'POST',
                    url: $base_url + 'addDiscount',
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

                        if (response.info == 'Added') {
                            customSweet.fire({
                                icon: 'success',
                                title: response.info,
                                text: response.message
                            }).then((result) => {
                                location.reload();
                            });
                        } else if (response.info == 'Error') {
                            customSweet.fire({
                                icon: 'error',
                                title: response.info,
                                text: response.message
                            })
                        } else if (response.info == 'No Data') {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            })

                            Toast.fire({
                                icon: 'warning',
                                title: response.message
                            })
                        }
                    }
                });
            } else {
                Swal.close();
            }
        })
    }

    $scope.authenticate = () => {
        $('#managersKey').modal('toggle')
    }

    $scope.managersKey = (data, mkey, targerModal) => {
        targetModal = targerModal;
        getData = data;
        currentModal = mkey;

        $('#' + currentModal).modal('toggle')
    }

    $scope.authorizeKey = (e) => {
        e.preventDefault();
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

                    if (targetModal == '') {
                        $('#managersKey').modal('hide');
                        clearModal('managersKey');
                        $scope.editProforma(document.getElementById("viewForm"));
                    } else {
                        $('#' + currentModal).modal('hide');
                        clearModal(currentModal);
                        $scope.view(getData);
                        $('#' + targetModal).modal('toggle')
                        toastAlert(response.message, 'success');
                    }

                } else if (response.info == 'Denied' || response.info == 'Error' || response.info == 'Not Found') {

                    toastAlert(response.message, 'error');

                }
            }
        });

    }

    $scope.setButtonEnabled = function() {
        var isButtonEnabled = true;
        if (index == 0) {
            angular.forEach($scope.proforma_line, function(item) {
                if (item && item.checkBoxEdit)
                    isButtonEnabled = false;
            });
        } else {
            isButtonEnabled = false;
        }
               
        return isButtonEnabled;
    }

    $scope.history = function(data) {
        $http({
            method: 'post',
            url: '../transactionControllers/povsproformacontroller/getHistory/' + data.proforma_header_id,
        }).then(function successCallback(response) {
            if (response.data != '') {
                $scope.proforma_history = response.data;
                $(document).ready(function() { $('#historyTable').DataTable(); });
            } else {
                $scope.proforma_history = '';
            }
        });
    }

    $scope.clearHistory = function() {
        $scope.proforma_history = '';
    }

    $scope.replaceProforma = function() {
        if ($scope.buttonNAme == 'Replace Proforma') {
            $scope.tableRow = false;
            $scope.uploadRow = true;
            $scope.buttonNAme = 'Return';
        } else {
            $scope.tableRow = true;
            $scope.uploadRow = false;
            $scope.buttonNAme = 'Replace Proforma';
        }
    }

    $scope.tabs = () => {
        if (index == 1) {
            $scope.tabIndex = true;
            $scope.discount_tab = true;
            $scope.tableRow = true;
            $scope.uploadRow = false;
            $scope.buttonNAme = 'Replace Proforma';
            $scope.getDiscount($scope.proforma_header_id);
        } else {
            $scope.discount_tab = false;
            $scope.tabIndex = false;
        }
    }

    $scope.getDiscount = function(id) {
        if (id !== undefined) {
            $http({
                method: 'post',
                url: '../transactionControllers/povsproformacontroller/getDiscount/' + id,
            }).then(function successCallback(response) {
                $scope.discount = response.data;
            });
        }
    }

    $scope.proformaTotal = function() {
        let total = 0;
        if($scope.proforma_line){
            $scope.proforma_line.forEach(function(data) {
                total += parseNumber(data.amount);
            });
        }

        return total;
    }

    $scope.totalAmount = function() {
        let totalamount = $scope.proformaTotal();
        return parseNumber(totalamount);
    }

    $scope.searchPO = function(e){
        e.preventDefault();

        var string = $scope.poSelect2;

        console.log(string);
        if(string == '' || string == undefined){
            $(".search-results").hide();
        }else{
            $http({
                method: 'post',
                url:'../getPos',
                data: { po: string }
            }).then(function successCallback(response) {
                $scope.purchaseorder = response.data;

                if($scope.purchaseorder.length == 0){
                    $scope.hasResults = 0;
                    $scope.purchaseorder.push( { po_no: "No Results Found" } );
                }else{
                    $scope.hasResults = 1 ;
                    $scope.purchaseorder = response.data;                    
                }            
            });            
        }
    }

    $scope.getPSIdetails = function(po) {
        $scope.poSelect2   = po.po_no;
        $scope.poSelectID2 = po.po_header_id;
        $(".search-results").hide();

        $http({
            method: 'post',
            url:'../getPisDetails',
            data: { po_id: po.po_header_id }
        }).then(function successCallback(response) {
            $scope.supplier2   = response.data.sup.supplier_name;
            $scope.location2   = response.data.loc.customer_name;
            $scope.supplierID2 = response.data.sup.supplierID;
            $scope.locationID2 = response.data.loc.locationID;
        }); 
    }

    $scope.additionalPsi = function(e){
        e.preventDefault();

        var formData = new FormData(e.target);
        formData.append('supplierID', $scope.supplierID2);
        formData.append('locationID', $scope.locationID2);
        formData.append('po_header_id', $scope.poSelectID2);

        $.ajax({
            type: 'POST',
            url: `${$base_url}additionals`,
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

                if (response.info == 'Invalid Format') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top',
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
                } else if (response.info == 'Uploaded') {
                    customSweet.fire({
                        icon: 'success',
                        title: response.info,
                        text: response.message
                    }).then((result) => {
                        location.reload();
                    });
                } else{
                    customSweet.fire({
                        icon: 'error',
                        title: response.info,
                        text: response.message
                    })
                }
            }
        });
    }

    $scope.priceCheck = function() {

        Swal.fire({
            title: warningTitle,
            html: "<b> Are you sure to PRICE CHECKED this transaction? </b>",
            buttonsStyling: false,
            showCancelButton: true,
            confirmButtonText: confirmButtonIcon,
            cancelButtonText: cancelButtonIcon,
            customClass: { confirmButton: confirmButtonClass, cancelButton: cancelButtonClass }
        }).then((result) => {
            if (result.isConfirmed) {

                $('#loading').show();
                var Toast = Swal.mixin({
                                        toast: true,
                                        position: 'top-right',
                                        iconColor: 'green',
                                        customClass: { popup: 'toasts-top-right' },
                                        showConfirmButton: false,
                                        timer: 1500,
                                        timerProgressBar: true })                    
                $http({
                    method: 'POST',
                    url: $base_url + 'priceCheck',
                    data: { profId:$scope.proforma_header_id  }
                }).then(function successCallback(response) {
                    $('#loading').hide();
                    var icon ;
                    if (response.data.info == "success") {   
                        icon = 'success'
                    } else if (response.data.info == "error") {    
                        icon = 'error'
                    }
                    Toast.fire({
                        icon: icon,
                        title: response.data.message
                    })       
                    
                    $("#viewProforma").modal('hide');
                    $scope.getPendingMatches();
                });
                        
            }
        })

        
    }
    //========= CREATE PROFORMA =========//

    $scope.detectChanges = function(){
        $scope.countSelected  = 0;
        $scope.countProfLines = 0;
        $scope.create_searchPo = null;
        $scope.create_poRef    = null;
        $scope.create_poDate   = null;
        $scope.profline = [{}];
        $scope.profline.splice(0,1); 
        $scope.poLine   = [{}];
        $scope.poLine.splice(0,1);
        $scope.discounts = [{}];
        $scope.discounts.splice(0,1);
        $scope.isAllSelected = false;

        $scope.getPricing('create');
    }

    $scope.getPricing = function(type){
        var supId ;
        if(type == 'create'){
            supId = $scope.create_selectSupplier
        } else {
            supId = $scope.supplierName
        }
        $http({
            method: 'post',
            url: $base_url + 'getPricing',
            data: { supId:supId   }
        }).then(function successCallback(response) {
            var pricing = "";
                pricing = response.data;
            
            if(pricing == "GROSSofVAT&Disc"){
                $scope.unitpriceClass = "alert alert-success";
            } else if(pricing == "NETofDiscwVAT"){
                $scope.unitpriceClass = "alert alert-danger";
            } else if(pricing == "NETofVAT&Disc"){
                $scope.unitpriceClass = "alert alert-warning";
            } else if(pricing == "NETofVATwDisc"){
                $scope.unitpriceClass = "alert alert-primary";
            } else if(pricing == "GROSSofDiscwoVAT"){
                $scope.unitpriceClass = "alert alert-info";
            }
        });  
    }

    $scope.searchPoCreate = function(e){
        e.preventDefault();

        $scope.poLine = [];
        $scope.isAllSelected = false;
        var string = $scope.create_searchPo;

        if(string == '' || string == undefined){
            $(".search-results").hide();
        }else{
            $http({
                method: 'post',
                url:'../searchPos',
                data: { po: string, supplier: $scope.create_selectSupplier, customer: $scope.create_selectCustomer }
            }).then(function successCallback(response) {
                $scope.searchedPO = response.data;
                
                if($scope.searchedPO.length == 0){
                    $scope.hasResultss = 0;
                    $scope.searchedPO.push( { po_header_id: "No Results Found" } );
                }else{
                    $scope.hasResultss = 1 ;
                    $scope.searchedPO = response.data;                    
                }            
            });            
        }
    }  

    $scope.displayPoDet = function(selected){
        $scope.create_searchPo2  = selected.po_header_id ;
        $scope.create_searchPo   = selected.po_no ;
        $scope.create_poRef      = selected.po_reference ;
        $scope.create_poDate     = selected.posting_date ;
        $(".search-results").hide();
        $scope.getPoDetails();
    }

    $scope.getPoDetails = function(){
        $http({
            method: 'POST',
            url: $base_url + 'PoDetails',
            data: { poId:$scope.create_searchPo2, supId: $scope.create_selectSupplier }
        }).then(function successCallback(response) {
            $(document).ready(function() { $('#poLine').DataTable(); });
            $scope.poLine = response.data;
        });
    }

    $scope.countCheckedItems = function(e){
        $scope.countSelected = 0 ;

        angular.forEach($scope.poLine, function(value, key) {
            if(value.selected){
                $scope.countSelected ++ ;
            }            
        });
    }

    $scope.addToProformaTable = function(e)
    {
        e.preventDefault();
        $scope.profline = [{}];
        $scope.profline.splice(0,1);    
        $scope.discounts = [{}];
        $scope.discounts.splice(0,1);   
        $scope.totalInvoiceAmount = 0;
        $scope.totalQty           = 0;
        $scope.totalDiscVat = 0; 
        
    
        angular.forEach($scope.poLine, function(line, key) {
            if(line.selected){       
                $scope.profline.push({'materialcode': line.item_code, 
                                      'customercode': line.item_code, 
                                      'description' : line.description,
                                      'qty'         : parseFloat(line.qty),
                                      'orig_qty'    : parseFloat(line.qty),
                                      'uom'         : line.uom,
                                      'unitcost'    : line.direct_unit_cost * 1,
                                      'orig_unitcst': line.direct_unit_cost * 1,
                                      'amount'      : round(line.direct_unit_cost * line.qty,2)
                                    })
                $scope.totalInvoiceAmount += round(line.direct_unit_cost * line.qty,2);
                $scope.totalQty += parseInt(line.qty);
                $scope.countProfLines ++ ;
            }                
        });           
          
    }

    $scope.validateNumber = function(ev){
        var e = ev || window.event;
        var key = e.keyCode || e.which;

        if (!e.shiftKey && !e.altKey && !e.ctrlKey &&
            // numbers   
            key >= 48 && key <= 57 ||
            // Numeric keypad
            key >= 96 && key <= 105 ||
            // Backspace and Tab and Enter
            key == 8 || key == 9 || key == 13 ||
            // Home and End
            key == 35 || key == 36 ||
            // left and right arrows
            key == 37 || key == 39 ||
            // Del     and      Ins     and decimal point
            key == 46 || key == 45 || key == 110) {
                // input is VALID

        } else {
            // input is INVALID
            e.returnValue = false;
            if (e.preventDefault) e.preventDefault();
        }
    }

    $scope.calculateAmount = function(type){
        $scope.totalInvoiceAmount = 0;
        $scope.totalDiscVat       = 0;
        $scope.totalQty           = 0;
        angular.forEach($scope.profline, function(prof, key) {
            if(type == 'unitcost'){                
                $scope.totalInvoiceAmount += round(prof.qty * prof.unitcost,2); 
                $scope.totalQty           += parseInt(prof.qty);
            } else if( type == 'qty') {
                $scope.totalInvoiceAmount += round(prof.qty * prof.unitcost,2); 
                $scope.totalQty           += parseInt(prof.qty); 
            } else if( type == 'checked'){
                if(prof.selected){
                    prof.unitcost =  0 ;
                } else {
                    prof.unitcost = prof.orig_unitcst ;
                }
                $scope.totalInvoiceAmount += round(prof.qty * prof.unitcost,2); 
                $scope.totalQty           += parseInt(prof.qty);
                
            }
        });

        angular.forEach($scope.discounts, function(disc, key){
            $scope.totalDiscVat += parseFloat(disc.amount) ;
        });

    }

    $scope.saveProforma = function(e){
        e.preventDefault();

        if($scope.totalInvoiceAmount != 0.00 && ($scope.deliverydate != "" && $scope.deliverydate != undefined)){

            var InvoiceData    = JSON.parse(angular.toJson($scope.profline));
            var DiscVatData    = JSON.parse(angular.toJson($scope.discounts));

            Swal.fire({
                title: warningTitle,
                html: "<b>Are you sure to save Pro-forma ? </b>",
                buttonsStyling: false,
                showCancelButton: true,
                confirmButtonText: confirmButtonIcon,
                cancelButtonText: cancelButtonIcon,
                customClass: { confirmButton: confirmButtonClass, cancelButton: cancelButtonClass }
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        type: "POST",
                        url: $base_url + 'saveProforma',
                        data: { supId: $scope.create_selectSupplier,
                                cusid: $scope.create_selectCustomer,
                                poId : $scope.create_searchPo2,
                                si   : $scope.salesinvoice,
                                so   : $scope.salesorder,
                                date : $scope.deliverydate,
                                inv  : InvoiceData,
                                disc : DiscVatData },
                        cache: false,
                        beforeSend: function() {
                            $('#loading').show();
                        },
                        success: function(response) {
                            var titleHeader = "";
                            if(response.info == "Success"){
                                titleHeader = successTitle
                            } else {
                                titleHeader = warningTitle
                            }
                            Swal.fire({
                                title: titleHeader,
                                html: '<b> ' + response.message + ' </b>'               
                            }).then(function() {
                               location.reload();
                            })
                        },
                        complete: function() {
                            $('#loading').hide();
                        }
                    });
                }

            })

        } else {
            alert('No data to save!')
        }
    }

    $scope.toggleAll = function() {

        var toggleStatus = !$scope.isAllSelected;
        angular.forEach($scope.poLine, function(line){ 
            line.selected = toggleStatus; 
        });   
    }

    $scope.optionToggled = function(){
        $scope.isAllSelected = $scope.poLine.every(function(line){ 
            return line.selected; 
        });
    }
    
    //========= CREATE PROFORMA =========//
    
    $(function() {
        $("#deliverydate").datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            maxDate: "0d",
            dateFormat: 'yy-mm-dd',
            showAnim: 'slideDown',
            changeMonth: true,
            changeYear: true
        });
        $("#dateFrom").datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            maxDate: "0d",
            dateFormat: 'yy-mm-dd',
            showAnim: 'slideDown',
            changeMonth: true,
            changeYear: true
        });
        $("#dateTo").datepicker({
            showOtherMonths: true,
            selectOtherMonths: true,
            maxDate: "+1m",
            dateFormat: 'yy-mm-dd',
            showAnim: 'slideDown',
            changeMonth: true,
            changeYear: true
        });
       
    });
   

    

});