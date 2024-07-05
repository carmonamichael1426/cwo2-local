window.myApp.controller('deduction-controller', function($scope, $http, $window) {
    // ============== DEDUCTIONS CONTROL ============== //

    const warningTitle        = "<i class='fas fa-exclamation-triangle fa-lg' style='color:#e65c00'></i>";
    const successTitle        = "<i class='fas fa-check-circle fa-lg' style='color:#28a745'></i>";
    const infoTitle           = "<i class='fas fa-info-circle fa-lg' style='color:#005ce6'></i>";
    const confirmButtonIcon   = "<i class='fas fa-thumbs-up'></i> ";
    const cancelButtonIcon    = "<i class='fas fa-thumbs-down'></i>";
    const confirmButtonClass  = "btn btn-outline-success";
    const cancelButtonClass   = "btn btn-light";

    $scope.selectedType ="";
    $scope.table = {};

    $scope.deductionsTable = function() {

        // if ($.fn.DataTable.isDataTable('#deductionsTable')) {
        //     $scope.table.destroy();
        // }

        $http({
            method: 'get',
            url: $base_url + 'loadDeductions'
        }).then(function successCallback(response) {  
            $scope.deductions = response.data;
            // $(document).ready(function() { $('#deductionsTable').DataTable(); }); 
            $(document).ready(function() {
                $scope.deductions = response.data;
                setTimeout(function() {
                    $scope.table = $('#deductionsTable').DataTable({
                        dom: 'Bfrtip',
                        "buttons": ["excel"],
                        stateSave: true
                    });
                }, 100);
            });            

        });
    }   

    $scope.saveType = function(ev)
    {
        ev.preventDefault();
        var msg = "<b> Are you sure to add type: " + $scope.type + " ? </b>";
        Swal.fire({
            title            : warningTitle,
            html             : msg,
            buttonsStyling   : false,            
            showCancelButton : true,
            confirmButtonText: confirmButtonIcon + " Yes",
            cancelButtonText : cancelButtonIcon + " No",
            customClass      :{ confirmButton: confirmButtonClass,cancelButton: cancelButtonClass }  
        }).then((result) => {
            if (result.isConfirmed) 
            {
                $http({
                    method: 'post',
                    url: $base_url + 'addType',
                    data : { type: $scope.type }
                }).then(function successCallback(response) {
                    var icon = "";
                    if( response.data.info == "Success"){
                        icon = successTitle;
                    } else if( response.data.info == "Error"){
                        icon = warningTitle;
                    } 
                    Swal.fire({
                        title: icon ,
                        html: "<b> " + response.data.message + " </b>"
                    }).then(function() {
                        location.reload();
                    });        
                });  
            }
        });  
    }

    $scope.getSuppliers = function()
    {
        $http({
            method: 'get',
            url: `${$base_url}getSuppliersForPO`
        }).then(function successCallback(response) {
            $scope.suppliers = response.data;
        });
    }

    $scope.getType = function(ev)
    {
        $http({
            method: 'get',
            url: $base_url + 'loadDeductionType'
        }).then(function successCallback(response) {
            $scope.types = response.data;
        });
    }

    $scope.saveDeduction = function(ev)
    {
        ev.preventDefault();
        if(!$scope.checkInputted && $scope.formula == ''){
            Swal.fire({
                title: warningTitle,
                html: "<b> Formula is required if not inputted! </b>"
            })
        } else {
            var supplier = $scope.selectSupplier == null? 'Applicable to other Suppliers' : $scope.selectSupplier  ;
            var type     = $scope.deductionType;
            var name     = $scope.deductionName;
            var acronym  = $scope.forDisplay == ''? '' : $scope.forDisplay ;
            var inputted = $scope.checkInputted == true ? 'Yes' : 'No' ;
            var repeat   = $scope.checkRepeat == true ? 'Yes' : 'No' ;
            var formula  = $scope.checkInputted == true ? 'No Formula' : $scope.formula;
            var msg      = "<b> Are you sure to save new deduction ? <br>" + 
                            name + "<br> Inputted: " + inputted + 
                            "<br>Repeat: " + repeat + 
                            "<br>Formula: " + formula + 
                            "<br>Supplier ID: " + supplier +" </b>";
            Swal.fire({
                title            : warningTitle,
                html             : msg,
                buttonsStyling   : false,            
                showCancelButton : true,
                confirmButtonText: confirmButtonIcon + " Yes",
                cancelButtonText : cancelButtonIcon + " No",
                customClass      :{ confirmButton: confirmButtonClass,cancelButton: cancelButtonClass }  
            }).then((result) => {
                if (result.isConfirmed) 
                {
                    $http({
                        method: 'post',
                        url: $base_url + 'saveDeduction',
                        data : { supId: supplier, type: type, name: name, acronym: acronym, inputted: inputted, repeat: repeat,formula :formula }
                    }).then(function successCallback(response) {
                        var icon = "";
                        if( response.data.info == "Info"){
                            icon = infoTitle;
                        } else if( response.data.info == "Success"){
                            icon = successTitle;
                        } else if( response.data.info == "Error"){
                            icon = warningTitle;
                        }
                        Swal.fire({
                            title: icon ,
                            html: "<b> " + response.data.message + " </b>"
                        }).then(function() {                            
                           location.reload();
                        });        
                    });  
                }
            });  
        }
    }

    $scope.resetAddDeduction = function()
    {
        $scope.selectSupplier   = null;
        $scope.deductionType    = null;
        $scope.deductionName    = null;
        $scope.forDisplay       = null;
        $scope.checkInputted    = null;
        $scope.checkRepeat      = null;
        $scope.formula          = null;
    }

    $scope.fetchDeductionData = function(data)
    {
        $scope.getType();   
        $scope.editSupplier         = data.supplier_id ;
        $scope.editDeductionId      = data.deduction_id ;
        $scope.editDeductionType    = data.deduction_type_id ;
        $scope.editDeductionName    = data.name ;
        $scope.editForDisplay       = data.name_used_for_display ;
        $scope.editCheckInputted    = data.inputted == "0" ? false : true;
        $scope.editCheckRepeat      = data.repeat == "0" ? false : true;
        $scope.editFormula          = data.formula == "No Formula" ? '' : data.formula;
    }

    $scope.updateDeduction = function(ev)
    {
        ev.preventDefault();

        if(!$scope.editCheckInputted && $scope.editFormula == ''){
            Swal.fire({
                title: warningTitle,
                html: "<b> Formula is required if not inputted! </b>"
            })
        } else if( $scope.editCheckInputted && $scope.editFormula !== ''){
            Swal.fire({
                title: warningTitle,
                html: "<b> Formula should be empty for inputted deductions! </b>"
            })
        } else {

            var supId    = $scope.editSupplier == null || $scope.editSupplier == ''? 'Applicable to other Suppliers' : $scope.editSupplier   ;
            var id       = $scope.editDeductionId ;
            var type     = $scope.editDeductionType;
            var name     = $scope.editDeductionName;
            var acronym  = $scope.editForDisplay;
            var inputted = $scope.editCheckInputted == true ? 'Yes' : 'No' ;
            var repeat   = $scope.editCheckRepeat == true ? 'Yes' : 'No' ;
            var formula  = $scope.editCheckInputted == true ? 'No Formula' : $scope.editFormula;
            var msg      = "<b> Are you sure to save this deduction ? <br>" + 
                            name + "<br>Inputted: " + inputted + 
                            "<br>Repeat: " + repeat + 
                            "<br>Formula: " + formula + 
                            "<br>Supplier ID: " + supId +" </b>";
            Swal.fire({
                title            : warningTitle,
                html             : msg,
                buttonsStyling   : false,            
                showCancelButton : true,
                confirmButtonText: confirmButtonIcon + " Yes",
                cancelButtonText : cancelButtonIcon + " No",
                customClass      :{ confirmButton: confirmButtonClass,cancelButton: cancelButtonClass }  
            }).then((result) => {
                if (result.isConfirmed) 
                {
                    $http({
                        method: 'post',
                        url: $base_url + 'editDeduction',
                        data : { supId: supId, id: id, type: type, name: name, acronym: acronym, inputted: inputted, repeat: repeat,formula :formula }
                    }).then(function successCallback(response) {
                        var icon = "";
                        if( response.data.info == "Info"){
                            icon = infoTitle;
                        } else if( response.data.info == "Success"){
                            icon = successTitle;
                        } else if( response.data.info == "Error"){
                            icon = warningTitle;
                        }
                        Swal.fire({
                            title: icon ,
                            html: "<b> " + response.data.message + " </b>"
                        }).then(function() {                            
                            $scope.deductionsTable();
                            $("#updateDeduction").modal('hide');
                        });                             
                    });  
                }
            });  
        }
    }


    $scope.deactivateDeduction = function(data)
    {
        // console.log(data);
        Swal.fire({
            title            : warningTitle,
            html             : '<b> Are you sure to deactivate ' + data. name_used_for_display +   ' ?</b>',
            buttonsStyling   : false,            
            showCancelButton : true,
            confirmButtonText: confirmButtonIcon + " Yes",
            cancelButtonText : cancelButtonIcon + " No",
            customClass      :{ confirmButton: confirmButtonClass,cancelButton: cancelButtonClass }  
        }).then((result) => {
            if (result.isConfirmed) 
            {
                $http({
                    method: 'post',
                    url: $base_url + 'deactivateDeduction',
                    data : { id: data.deduction_id }
                }).then(function successCallback(response) {
                    var icon = "";
                    if( response.data.info == "Info"){
                        icon = infoTitle;
                    } else if( response.data.info == "Success"){
                        icon = successTitle;
                    } else if( response.data.info == "Error"){
                        icon = warningTitle;
                    }
                    Swal.fire({
                        title: icon ,
                        html: "<b> " + response.data.message + " </b>"
                    }).then(function() { 
                        location.reload();                           
                        // $scope.deductionsTable();
                        // $("#updateDeduction").modal('hide');
                    });                             
                });  
            }
        }); 
    }
});