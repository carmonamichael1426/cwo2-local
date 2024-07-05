window.adminApp = null;
var adminApp = angular.module('admin_login', []);

adminApp.controller('adminlogin-controller', function($scope, $http, $window) {
    $scope.adminloginSubmit = function(e) {
        e.preventDefault();

        // alert(`${$scope.login_user} - ${$scope.login_pass}`);
        $http({
            method: 'POST',
            url: 'checkCredentials',
            data: $.param({ username: $scope.login_user, password: $scope.login_pass }),
            headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=utf-8' },
            responseType: 'json'
        }).then(function successCallback(response) {

            if (response.data['info'] == 'Error' || response.data['info'] == 'Denied') {
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
                    title: response.data['message']
                })
            } else {
                $window.location.href = 'admin_home';
            }
        });
    }
});