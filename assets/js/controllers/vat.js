window.myApp.controller('vat-controller', function($scope, $http, $window) {
    // ============== DEDUCTIONS CONTROL ============== //

    const warningTitle        = "<i class='fas fa-exclamation-triangle fa-lg' style='color:#e65c00'></i>";
    const successTitle        = "<i class='fas fa-check-circle fa-lg' style='color:#28a745'></i>";
    const infoTitle           = "<i class='fas fa-info-circle fa-lg' style='color:#005ce6'></i>";
    const confirmButtonIcon   = "<i class='fas fa-thumbs-up'></i> ";
    const cancelButtonIcon    = "<i class='fas fa-thumbs-down'></i>";
    const confirmButtonClass  = "btn btn-outline-success";
    const cancelButtonClass   = "btn btn-light";

    $scope.vatTable = function() {
        $http({
            method: 'get',
            url: $base_url + 'loadVAT'
        }).then(function successCallback(response) {
            $(document).ready(function() { $('#vatTable').DataTable(); });
            $scope.vatData = response.data;
        });
    }   

    $scope.saveVAT = function(ev)
    {
        ev.preventDefault();

        var msg = "<b> Are you sure to add VAT: " + $scope.desc + " and set as default ? </b>";
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
                    url: $base_url + 'addVAT',
                    data : { desc: $scope.desc, value: $scope.valcom }
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
                        $scope.vatTable();
                    });        
                });  
            }
        });  
    }

    $scope.resetAddVat = function()
    {
        $scope.desc      = null;
        $scope.valcom    = null;
    }

    $scope.fetchData = function(data)
    {
        $scope.vatId    = data.vat_id;
        $scope.desc_e   = data.description;
        $scope.val_e    = data.value;
    }

    $scope.updateVat = function(ev)
    {
        ev.preventDefault();

        
        var msg      = "<b> Are you sure to save this VAT ? <br>" + 
                        "Description: " + $scope.desc_e + "<br>Value: " + $scope.val_e + " </b>";
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
                    url: $base_url + 'updateVAT',
                    data : { id: $scope.vatId, desc: $scope.desc_e, value: $scope.val_e }
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
                        $scope.vatTable();
                        $("#updateVat").modal('hide');
                    });                             
                });  
            }
        });  
        
    }


    $scope.deactivateVAT = function(data)
    {
        console.log(data);
        Swal.fire({
            title            : warningTitle,
            html             : '<b> Are you sure to deactivate ' + data.description  +   ' ?</b>',
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
                    url: $base_url + 'deactivateVAT',
                    data : { id: data.vat_id }
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
                        $scope.vatTable();
                    });                             
                });  
            }
        }); 
    }
});