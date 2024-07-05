window.myApp = null;
var myApp = angular.module('app', ['ngSanitize']);

myApp.directive('passwordConfirm', ['$parse', function($parse) {
    return {
        restrict: 'A',
        scope: {
            matchTarget: '=',
        },
        require: 'ngModel',
        link: function link(scope, elem, attrs, ctrl) {
            var validator = function(value) {
                ctrl.$setValidity('match', value === scope.matchTarget);
                return value;
            }

            ctrl.$parsers.unshift(validator);
            ctrl.$formatters.push(validator);

            // This is to force validator when the original password gets changed
            scope.$watch('matchTarget', function(newval, oldval) {
                validator(ctrl.$viewValue);
            });

        }
    };
}]);

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

clearModal = function(modalName) {
    $('#' + modalName).on('hidden.bs.modal', function(e) {
        $(this)
            .find("input,textarea,select")
            .val('')
            .end()
            .find("input[type=checkbox], input[type=radio]")
            .prop("checked", "")
            .end();
    })
}

toastAlert = function(message, icon) {
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
        icon: icon,
        title: message
    })
}

function showTime() {
    var date = new Date();
    var h = date.getHours(); // 0 - 23
    var m = date.getMinutes(); // 0 - 59
    var s = date.getSeconds(); // 0 - 59
    var session = "AM";

    if (h == 0) {
        h = 12;
    }

    if (h > 12) {
        h = h - 12;
        session = "PM";
    }

    h = (h < 10) ? "0" + h : h;
    m = (m < 10) ? "0" + m : m;
    s = (s < 10) ? "0" + s : s;

    var time = h + ":" + m + ":" + s + " " + session;
    document.getElementById("MyClockDisplay").innerText = time;
    document.getElementById("MyClockDisplay").textContent = time;

    setTimeout(showTime, 1000);

}

showTime();


// $(function() {
//     $("#dateFrom").datepicker({
//         showOtherMonths: true,
//         selectOtherMonths: true,
//         dateFormat: 'yy-mm-dd',
//         showAnim: 'slideDown',
//         changeMonth: true,
//         changeYear: true
//     });
//     $("#dateTo").datepicker({
//         showOtherMonths: true,
//         selectOtherMonths: true,
//         dateFormat: 'yy-mm-dd',
//         showAnim: 'slideDown',
//         changeMonth: true,
//         changeYear: true
//     });

// });