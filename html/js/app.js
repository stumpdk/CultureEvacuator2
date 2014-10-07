//Instantiating app
var app = angular.module("CultureEvacuator",['ngRoute']);

app.config(function($routeProvider, $locationProvider) {
  $routeProvider
  .when('/show/:bookId', {
    templateUrl: 'partials/partials-show.html',
    controller: 'MainCtrl'
  })
  .otherwise({
  	templateUrl: 'partials/partials-list.html',
  	controller: 'MainCtrl'
  });

  $locationProvider.html5Mode(false);
});

//The main controller handles the primary window
app.controller('MainCtrl', ['$scope', '$location',function($scope, $location){
	$scope.list = 'partials/status-list.html';
	$scope.posts = [];
	$scope.posts.push({
		imageUrl : 'test.jpg',
		headline : 'Test headline',
		id : 1
	});

	$scope.goToItem = function(id){
		$location.url('/show/' + id);
	};
}]);

/*
//CommentsCtrl handles the views concerning comments
app.controller('CommentsCtrl', function($scope){
	$scope.comments = [];
});

//KeywordsCtrl handles the views concerning comments
app.controller('KeywordsCtrl', function($scope){
	$scope.comments = [];
});

angular.module('CultureEvacuator.services').factory('Comment', function($resource) {
  return $resource('/api?type=comments&post=:userId'); // Note the full endpoint address
});

//Service to load comments
app.module('service', function($resource){
	var pubs = {};
	pubs.Comments = $resource('index.php?type=comments&post=:userId',
	 {userId:123, cardId:'@id'}, {
	  get: {method:'GET', params:{postid:true}}
	 });
});

//Service to handle geo-tags

//Service to handle posts

//Service to handle likes
*/
