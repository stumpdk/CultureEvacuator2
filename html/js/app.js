//Instantiating app
var app = angular.module("CultureEvacuator",['ngRoute', 'services']);

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
app.controller('MainCtrl', ['$scope', '$location', 'Post','Comment','Keyword', function($scope, $location, Post, Comment, Keyword){
	$scope.list = 'partials/partials-list.html';
    $scope.currentPost = {};
/*	$scope.posts = [];
	$scope.posts.push({
		imageUrl : 'test.jpg',
		headline : 'Test headline',
		id : 1,
        text: 'test af postens tekst',
        comments : [
            {id:"1",rate:undefined,text:"Test af kommentar"},
            {id:"2",rate:true,text:"Test af valgt kommentar"},
        ],
        keywords : [
            {id:"1", rate:undefined, text:"Ogs"},
            {id:"2", rate:true, text:"Ogs"},
            {id:"3", rate:false, text:"Ogs"},
        ]
	});*/

    $scope.Comment = Comment;
    $scope.Keyword = Keyword;
    $scope.Post = Post;

	$scope.goToItem = function(id){
        $scope.currentPost = $scope.Post.getPostById($scope.posts, id);
        $scope.Comment.getComments($scope.currentPost);
        $scope.Keyword.getKeywords($scope.currentPost);
	    console.log($scope.currentPost);
        $location.url('/show/' + id);
        $scope.list = 'partials/partials-show.html';
	};

    $scope.goToList = function(){
        $location.url('/list');
        $scope.list = 'partials/partials-list.html';
    };

    $scope.init = function(){
        $scope.Post.getPosts().then(function(data){
            $scope.posts = data;
        });
    };

    $scope.init();
}]);
/*
angular.module('services').factory('Comment', function($resource) {
    return $resource('/api?type=comments&post=:userId'); // Note the full endpoint address
});*/

//Service that handle posts
var services = angular.module('services',[]);
services.service('test', function($resource){
	var pubs = {};
	pubs.Comments = $resource('index.php?type=comments&post=:userId',
	 {userId:123, cardId:'@id'}, {
	  get: {method:'GET', params:{postid:true}}
	 });
});

services.service('Post', function ($http, $q) {
    var exp = {};

    exp.getPostById = function(posts,id){
        for(var i = 0; i < posts.length; i++){
            if(posts[i].post_id == id)
                return posts[i];
        }

        return false;
    };

    exp.approvePost = function(post, approved){
        post.approved = approved;
    };
    exp.getPosts = function () {
        var def= $q.defer();
        $http.jsonp("http://www.jacoblarsen.net/hack4dk/2014/api/public/1/index.php?type=posts&callback=JSON_CALLBACK").success( function(data) {
            def.resolve(data);
        })
        .error(function(data, status, headers, config){
            def.resolve(false);
        });
        return def.promise;
    };


    return exp;
});

services.service('Keyword', function($http){
    var exp = {};

    exp.rate = function(keyword, rate){
        if(rate === undefined)
            rate = true;
        keyword.rate = rate;
        console.log('rated');
    };
    exp.getKeywords = function (post) {
        $http.jsonp("http://www.jacoblarsen.net/hack4dk/2014/api/public/1/index.php?type=comment_keywords&post_id=" + post.post_id + "callback=JSON_CALLBACK").success( function(data) {
            post.keywords = data;
        })
        .error(function(data, status, headers, config){
            post.keywords = [
{
post_id: "109602915873899_374313669402821",
keyword: "Vesterbrogade",
comment_id: "Post level keyword",
type: "addresses"
},
{
post_id: "109602915873899_374313669402821",
keyword: "Saxogade 48",
comment_id: "374334512734070",
type: "addresses"
},
{
post_id: "109602915873899_374313669402821",
keyword: "Ditte",
comment_id: "374338089400379",
type: "names"
},
{
post_id: "109602915873899_374313669402821",
keyword: "Regitze",
comment_id: "374350369399151",
type: "names"
}];
        });
    };

    return exp;
});

services.service('Comment', function ($q, $http){
    var exp = {};

    exp.rate = function(comment, rate){
        if(rate === undefined)
            rate = true;
        comment.rate = rate;
        console.log('rated');
    };

    exp.getComments = function (post) {
        $http.jsonp("http://www.jacoblarsen.net/hack4dk/2014/api/public/1/index.php?type=comments&post_id=" + post.post_id + "&callback=JSON_CALLBACK").success( function(data) {
            post.comments = data;
        })
        .error(function(data, status, headers, config){
            alert("oh noez, cant load comments!");
        });
    };


    return exp;
});

/*
//Service to handle geo-tags

//Service to handle posts

//Service to handle likes
*/
