var myApp = angular.module('login', []);

myApp.controller('login-controller', function($scope, $http, $window) {
    $scope.showPassword = false;

    $scope.loginSubmit = function(e) {
        e.preventDefault();

        var request = $http({
            method: 'post',
            url: 'login',
            data: { username: $scope.login_user, password: $scope.login_pass },
            headers: { 'Content-Type': 'application/json' }
        }).then(function successCallback(response) {

            if (response.data['info'] == 'Denied') {
                Swal.fire({
                    icon: 'error',
                    title: 'Access Denied',
                    text: response.data['message']
                })
            } else if (response.data['info'] == 'Error') {
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
                    icon: 'warning',
                    title: response.data['message']
                })
            } else {
                $window.location.href = 'home';
            }
        });
    }

    $scope.toggleShowPassword = () => {
        $scope.showPassword = !$scope.showPassword;
    }
});

// ======================== JQUERY PLUGIN ======================== //
(function($) {
    $.fn.serializeObject = function() {
        var data = {};
        $(this).serializeArray().forEach(function(fd, key) {
            data[fd.name] = fd.value;
        })
        return data;
    };
}(jQuery));