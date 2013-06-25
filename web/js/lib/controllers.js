'use strict';

/* Controllers */

function GearmnaStatusCtrl($scope, $http) {
  $http.get('/monitor.php?auth_token=0000000000').success(function(data) {
    $scope.gearman_status = data.gearman_status;
    $scope.gearman_ok = data.gearman_ok;
  });
}
