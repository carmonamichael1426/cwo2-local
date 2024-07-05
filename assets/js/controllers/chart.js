window.myApp.controller('chart-controller', ($scope, $http) => {


    $scope.proftotal     = 0;
    $scope.profunchecked = 0;
    $scope.profpricechecked   = 0;

    $scope.sopcancelled = 0;
    $scope.soppending   = 0;
    $scope.sopaudited   = 0;
    $scope.soptotal     = 0;

    $scope.crftotal     = 0;
    $scope.crfpending   = 0;
    $scope.crfmatched   = 0;

    $scope.pitotal     = 0;
    $scope.pipending   = 0;
    $scope.pimatched   = 0;

    $scope.profChart = () => {
        $http({
            method: 'get',
            url: $base_url + 'getProfPriceCheckStats'
        }).then(function successCallback(response) {
            $scope.proftotal          = parseInt(response.data.All);
            $scope.profunchecked      = parseInt(response.data.Pending);   
            $scope.profpricechecked   = parseInt(response.data.Checked); 

            $scope.prof = {
                labels: ["Unchecked", "Price Checked"],
                data: [$scope.profunchecked, $scope.profpricechecked],
                color: ["#f39c12", "#00a65a"]
            };

            var ctx = document.getElementById("profcanvas").getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: $scope.prof.data,
                        backgroundColor: $scope.prof.color
                    }],
                    labels: $scope.prof.labels
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    showScale: false
                }
            });

        });
    }

    $scope.sopChart = () => {
        
        $http({
            method: 'get',
            url: $base_url + 'getSopStats'
        }).then(function successCallback(response) {
            $scope.soptotal     = parseInt(response.data.All);
            $scope.sopcancelled = parseInt(response.data.Cancelled);   
            $scope.soppending   = parseInt(response.data.Pending);   
            $scope.sopaudited   = parseInt(response.data.Audited); 

            $scope.sop = {
                labels: ["Cancelled", "Pending", "Audited"],
                data: [$scope.sopcancelled, $scope.soppending, $scope.sopaudited],
                color: ["#f56954", "#f39c12", "#00a65a"]
            };
            var ctx2 = document.getElementById("sopcanvas").getContext('2d');
            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: $scope.sop.data,
                        backgroundColor: $scope.sop.color
                    }],
                    labels: $scope.sop.labels
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    showScale: false
                }
            });           
        }); 
    }

    $scope.crfChart = () => {
        $http({
            method: 'get',
            url: $base_url + 'getCrfStats'
        }).then(function successCallback(response) {
            $scope.crftotal     = parseInt(response.data.All);
            $scope.crfpending   = parseInt(response.data.Pending);   
            $scope.crfmatched   = parseInt(response.data.Matched); 

            $scope.crf = {
                labels: ["Pending", "Matched"],
                data: [$scope.crfpending, $scope.crfmatched],
                color: ["#f39c12", "#00a65a"]
            };

            var ctx3 = document.getElementById("crfcanvas").getContext('2d');
            new Chart(ctx3, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: $scope.crf.data,
                        backgroundColor: $scope.crf.color
                    }],
                    labels: $scope.crf.labels
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    showScale: false
                }
            });

        });
    }

    $scope.piChart = () => {
        $http({
            method: 'get',
            url: $base_url + 'getPiStats'
        }).then(function successCallback(response) {
            $scope.pitotal     = parseInt(response.data.All);
            $scope.pipending   = parseInt(response.data.Pending);   
            $scope.pimatched   = parseInt(response.data.Matched); 

            $scope.pi = {
                labels: ["Pending", "Matched"],
                data: [$scope.pipending, $scope.pimatched],
                color: ["#f39c12", "#00a65a"]
            };

            var ctx4 = document.getElementById("picanvas").getContext('2d');
            new Chart(ctx4, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: $scope.pi.data,
                        backgroundColor: $scope.pi.color
                    }],
                    labels: $scope.pi.labels
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    showScale: false
                }
            });

        });
    }

    $scope.profChart();
    $scope.sopChart();
    $scope.crfChart();
    $scope.piChart();
});