function ReaderController($scope, annotationService){
	$scope.annotations = [];

	$scope.$on('annotationsUpdated', function(){
		$scope.annotations = annotationService.annotations;
		$scope.$apply();
	});
}

function ParticipateController($scope, $http, annotationService){
	$scope.annotations = [];
	$scope.comments = [];

	$scope.init = function(docId){
		$scope.getDocComments(docId);
		$scope.user = user;
		$scope.doc = doc;
	}

	$scope.$on('annotationsUpdated', function(){
		$scope.annotations = annotationService.annotations;
		$scope.$apply();
	});

	$scope.showCommentThread = function(annotationId, $event){
		thread = $('#' + annotationId + '-comments');
		thread.collapse('toggle');
		$($event.target).children('.caret').toggleClass('caret-right');
	};

	$scope.getDocComments = function(docId){
		$http({method: 'GET', url: '/api/docs/' + docId + '/comments'})
		.success(function(data, status, headers, config){
			$scope.comments = data;
		})
		.error(function(data, status, headers, config){
			console.error("Error loading comments: %o", data);
		});
	};
	$scope.commentSubmit = function(form){
		comment = angular.copy($scope.comment);
		comment.user = $scope.user;
		comment.doc = $scope.doc;

		$http.post('/api/docs/' + comment.doc.id + '/comments', {'comment': comment})
		.success(function(data, status, headers, config){
			$scope.comments.push(comment);
			$scope.comment.content = '';
		})
		.error(function(data, status, headers, config){
			console.error("Error posting comment: %o", data);
		});
	}
}

function HomePageController($scope, $http, $filter){
	$scope.docs = [];
	$scope.categories = [];
	$scope.select2;

	

	$scope.init = function(){
		$scope.getDocs();
		//$scope.getCategories();	

		$scope.select2Config = {
			multiple: true,
			allowClear: true
		}	
	}

	$scope.docFilter = function(doc){
		var show = false;

		if(typeof $scope.select2 != 'undefined' && $scope.select2 != ''){
			var cont = true;

			angular.forEach(doc.categories, function(category){
				if(category.name == $scope.select2 && cont){
					show =  true;
					cont = false;
				}
			});
		}else{
			show = true;
		}

		return show;
	}

	$scope.getDocs = function(){
		$http.get('/api/docs/')
		.success(function(data, status, headers, config){

			angular.forEach(data, function(doc, key){
				doc.updated_at = Date.parse(doc.updated_at);
				doc.created_at = Date.parse(doc.created_at);

				$scope.docs.push(doc);

				angular.forEach(doc.categories, function(category){
					var found = $filter('filter')($scope.categories, category, true);

					if(!found.length){
						$scope.categories.push(category.name);
					}
				});
			});

		})
		.error(function(data){
			console.error("Unable to get documents: %o", data);
		});
	}
}

function DashboardVerifyController($scope, $http){
	$scope.requests = [];

	$scope.init = function(){
		$scope.getRequests();
	}

	$scope.getRequests = function(){
		$http.get('/api/user/verify')
		.success(function(data, status, headers, config){
			$scope.requests = data;
		})
		.error(function(data, status, headers, config){
			console.error(data);
		});
	}

	$scope.update = function(request, status, event){
		$http.post('/api/user/verify', {'request': request, 'status': status})
		.success(function(data){
			request.meta_value = status;
		})
		.error(function(data, status, headers, config){
			console.error(data);
		});
	}
}

function DashboardSettingsController($scope, $http){
	$scope.admins = [];

	$scope.getAdmins = function(){
		$http.get('/api/user/admin')
		.success(function(data){
			$scope.admins = data;
		})
		.error(function(data){
			console.error(data);
		});
	}

	$scope.saveAdmin = function(admin){
		admin.saved = false;

		$http.post('/api/user/admin', {'admin': admin})
		.success(function(data){
			admin.saved = true;
		})
		.error(function(data){
			console.error(data);
		});
	}

	$scope.init = function(){
		$scope.getAdmins();
	}
}

function DashboardEditorController($scope, $http, $timeout)
{
	$scope.doc;
	$scope.categories = [];
	$scope.suggestedCategories = [];
	

	$scope.init = function(){
		$scope.doc = doc;

		$http.get('/api/docs/categories')
		.success(function(data){
			angular.forEach(data, function(category){
				$scope.suggestedCategories.push(category.name);
			})
		})
		.error(function(data){
			console.error("Unable to get document categories: %o", data);
		});

		$http.get('/api/docs/' + $scope.doc.id + '/categories')
		.success(function(data){
			angular.forEach(data, function(category){
				$scope.categories.push(category.name);
			});
		}).error(function(data){
			console.error("Unable to get categories for document %o: %o", $scope.doc, data);
		});

		$scope.categoryOptions = {
			multiple: true,
			simple_tags: true,
			tags: function(){
				return $scope.suggestedCategories;
			},
			results: function(){
				return $scope.categories;
			}
		};

		var initializing = true;

		$scope.$watch('categories', function(values){
			if(initializing){
				$timeout(function(){ initializing = false; });
			}else{
				$scope.saveCategories();
			}
		});
	}

	$scope.saveCategories = function(){
		console.log("Document %o has categories %o", $scope.doc, $scope.categories);

		$http.post('/api/docs/' + $scope.doc.id + '/categories', {'categories': $scope.categories})
		.success(function(data){
			console.log(data);
		}).error(function(data){
			console.error("Error saving categories for document %o: %o \n %o", $scope.doc, $scope.categories, data);
		});
	}

}

DashboardEditorController.$inject = ['$scope', '$http', '$timeout'];
DashboardSettingsController.$inject = ['$scope', '$http'];
DashboardVerifyController.$inject = ['$scope', '$http'];
HomePageController.$inject = ['$scope', '$http', '$filter'];
ReaderController.$inject = ['$scope', 'annotationService'];
ParticipateController.$inject = ['$scope', '$http', 'annotationService'];


