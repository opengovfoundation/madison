/**
*	Document Viewer Controllers
*/

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

/**
*	Page Controllers
*/

function HomePageController($scope, $http, $filter){
	$scope.docs = [];
	$scope.categories = [];
	$scope.sponsors = [];
	$scope.select2;

	$scope.init = function(){
		$scope.getDocs();
		
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

			angular.forEach(doc.sponsor, function(sponsor){
				if(sponsor.id == $scope.select2 && cont){
					show = true;
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

				angular.forEach(doc.sponsor, function(sponsor){
					var found = $filter('filter')($scope.sponsors, sponsor, true);

					if(!found.length){
						$scope.sponsors.push(sponsor);
					}
				});
			});

		})
		.error(function(data){
			console.error("Unable to get documents: %o", data);
		});
	}
}

function UserPageController($scope, $http, $location){
	$scope.user;
	$scope.meta;
	$scope.docs = [];
	$scope.comments = [];
	$scope.verified = false;

	$scope.init = function(){
		$scope.getUser();
	}

	$scope.getUser = function(){
		var abs = $location.absUrl();
		var id = abs.match(/.*\/(\d+)$/);
		id = id[1];

		$http.get('/api/user/' + id)
		.success(function(data){
			$scope.user = angular.copy(data);
			$scope.meta = angular.copy(data.user_meta);

			angular.forEach(data.docs, function(doc){
				doc.created_at = Date.parse(doc.created_at);
				doc.updated_at = Date.parse(doc.updated_at);

				$scope.docs.push(doc);
			});

			angular.forEach(data.comments, function(comment){
				comment.created_at = Date.parse(comment.created_at);
				$scope.comments.push(comment);
			});

			angular.forEach($scope.user.user_meta, function(meta){
				var cont = true;

				if(meta.meta_key == 'verify' && meta.meta_value == 'verified' && cont){
					$scope.verified = true;
					cont = false
				}
			});

			$scope.user.created_at = Date.parse($scope.user.created_at);

		}).error(function(data){
			console.error("Unable to retrieve user: %o", data);
		});
	}

	$scope.showVerified = function(){
		if($scope.verified && $scope.docs.length > 0){
			return true;
		}

		return false;
	}

}

/** 
*	Dashboard Controllers
*/

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
	$scope.sponsor;
	$scope.status;
	$scope.verifiedUsers = [];
	$scope.categories = [];
	$scope.suggestedCategories = [];
	

	$scope.init = function(){
		$scope.doc = doc;
		$scope.getAllCategories();
		$scope.getDocCategories();
		$scope.getDocSponsor();
		$scope.getVerifiedUsers();
		$scope.setSelectOptions();

		var initCategories = true;
		var initSponsor = true;

		$scope.$watch('categories', function(values){
			if(initCategories){
				$timeout(function(){ initCategories = false; });
			}else{
				$scope.saveCategories();
			}
		});

		$scope.$watch('sponsor', function(value){
			if(initSponsor){
				$timeout(function(){ initSponsor = false; });
			}else{
				$scope.saveSponsor();
			}
		});
	}

	$scope.setSelectOptions = function(){
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

		$scope.sponsorOptions = {
			placeholder: "Select Document Sponsor",
			ajax: {
				url: "/api/user/verify",
				dataType: 'json',
				data: function(term, page){
					return;
				},
				results: function(data, page){
					var returned = [];
					angular.forEach(data, function(verified){
						var text = verified.user.fname + " " + verified.user.lname + " - " + verified.user.email;

						returned.push({ id: verified.user.id, text: text });
					});

					return {results: returned};
				}
			},
			initSelection: function(element, callback){
				callback($scope.sponsor);
			}
		};
	}

	$scope.getVerifiedUsers = function(){
		$http.get('/api/user/verify')
		.success(function(data){
			angular.forEach(data, function(verified){
				$scope.verifiedUsers.push(angular.copy(verified.user));
			});
		}).error(function(data){
			console.error("Unable to get verified users: %o", data);
		})
	}

	$scope.getDocCategories = function(){
		$http.get('/api/docs/' + $scope.doc.id + '/categories')
		.success(function(data){
			angular.forEach(data, function(category){
				$scope.categories.push(category.name);
			});
		}).error(function(data){
			console.error("Unable to get categories for document %o: %o", $scope.doc, data);
		});
	}

	$scope.getDocSponsor = function(){
		$http.get('/api/docs/' + $scope.doc.id + '/sponsor')
		.success(function(data){
			$scope.sponsor = angular.copy({id: data.id, text: data.fname + " " + data.lname + " - " + data.email});
		}).error(function(data){
			console.error("Error getting document sponsor: %o", data);
		});

	}

	$scope.getAllCategories = function(){
		$http.get('/api/docs/categories')
		.success(function(data){
			angular.forEach(data, function(category){
				$scope.suggestedCategories.push(category.name);
			})
		})
		.error(function(data){
			console.error("Unable to get document categories: %o", data);
		});
	}

	$scope.saveCategories = function(){
		$http.post('/api/docs/' + $scope.doc.id + '/categories', {'categories': $scope.categories})
		.success(function(data){
			console.log("Categories saved successfully: %o", data);
		}).error(function(data){
			console.error("Error saving categories for document %o: %o \n %o", $scope.doc, $scope.categories, data);
		});
	}

	$scope.saveSponsor = function(){
		$http.post('/api/docs/' + $scope.doc.id + '/sponsor', {'sponsor': $scope.sponsor})
		.success(function(data){
			console.log("Sponsor saved successfully: %o", data);
		}).error(function(data){
			console.error("Error saving sponsor: %o", data)
		});
	}

}

/**
*	Dependency Injections
*/

DashboardEditorController.$inject = ['$scope', '$http', '$timeout'];
DashboardSettingsController.$inject = ['$scope', '$http'];
DashboardVerifyController.$inject = ['$scope', '$http'];

HomePageController.$inject = ['$scope', '$http', '$filter'];
UserPageController.$inject = ['$scope', '$http', '$location'];

ReaderController.$inject = ['$scope', 'annotationService'];
ParticipateController.$inject = ['$scope', '$http', 'annotationService'];


