var myApp = angular.module('angularApp', []);

myApp.controller("NameController", ["$scope", function($scope){
	$scope.name = "Scussel";
	$scope.changeName = function(newName){
		$scope.name = newName;
	}
}]);
