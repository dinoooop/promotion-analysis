app.controller('CreatePosts', function ($scope) {

    $scope.form_data = {};

});
app.controller('EditPosts', function ($scope, DataService) {

    $scope.form_data = {};

    var modal_id = angular.element(document.querySelector('#pv_edit_post_modal_id')).val();

    DataService.get_db_data('post_edit/' + modal_id, function (response) {
        $scope.form_data = response;
    });


});


app.controller('IndexPosts', function ($scope, DataService) {

    $scope.delete_row = function ($event) {

        swal({
            title: "Are you sure?",
            text: "You will not able to undo this action!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        }, function () {
            var modal_id = angular.element($event.target).attr("data-modal_id");

            DataService.delete_modal('posts', modal_id, function (response) {
                angular.element($event.target).parent().parent().remove();
                swal("Deleted!", "That record has been deleted successfully!!!", "success");
            });

        });
    }

});



app.controller('CreateVideos', function ($scope) {

    $scope.form_data = {
    };

});
app.controller('EditVideos', function ($scope, DataService) {

    $scope.form_data = {
    };
    var modal_id = angular.element(document.querySelector('#pv_edit_video_modal_id')).val();

    DataService.get_db_data('video_edit/' + modal_id, function (response) {
        $scope.form_data = response;
    });
});


app.controller('IndexVideos', function ($scope, DataService) {

    $scope.delete_row = function ($event) {

        swal({
            title: "Are you sure?",
            text: "You will not able to undo this action!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        }, function () {
            var modal_id = angular.element($event.target).attr("data-modal_id");

            DataService.delete_modal('videos', modal_id, function (response) {
                angular.element($event.target).parent().parent().remove();
                swal("Deleted!", "That record has been deleted successfully!!!", "success");
            });

        });
    }

});




app.controller('CreateUsers', function ($scope, DataService) {

    $scope.form_data = {
    };



    DataService.get_db_data('select-option/roles', function (response) {
        if (response.status) {
            $scope.role_options = angular.fromJson(response.result);
        }
    });


});
app.controller('EditUsers', function ($scope, DataService) {

    $scope.form_data = {
    };
    var modal_id = angular.element(document.querySelector('#pv_edit_user_modal_id')).val();

    DataService.get_db_data('user_edit/' + modal_id, function (response) {
        $scope.form_data = response.modal;
        $scope.role_options = angular.fromJson(response.roles);
    });


});


app.controller('IndexUsers', function ($scope, DataService) {

    $scope.delete_row = function ($event) {

        swal({
            title: "Are you sure?",
            text: "You will not able to undo this action!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        }, function () {
            var modal_id = angular.element($event.target).attr("data-modal_id");

            DataService.delete_modal('users', modal_id, function (response) {
                angular.element($event.target).parent().parent().remove();
                swal("Deleted!", "That record has been deleted successfully!!!", "success");
            });

        });
    }

});




//------------------------------------------------------------------------------
// Ads
//------------------------------------------------------------------------------

app.controller('CreateAds', function ($scope, DataService) {

    $scope.form_data = {};

    DataService.get_db_data('create_ad/add', function (response) {
        
            $scope.ad_location_options = angular.fromJson(response.ad_location_options);
            $scope.form_data.ad_location = $scope.ad_location_options[0];
            $scope.crope_sizes = response.crope_sizes;
        
    });
    
    $scope.change_crop_size = function(){
        
        console.log('hi');
    }

});
app.controller('EditAds', function ($scope, DataService) {

    $scope.form_data = {};
    var modal_id = angular.element(document.querySelector('#pv_edit_ad_modal_id')).val();

    DataService.get_db_data('ad_edit/' + modal_id, function (response) {
        $scope.form_data = response.modal;
        $scope.ad_location_options = angular.fromJson(response.ad_location);
    });

});


app.controller('IndexAds', function ($scope, DataService) {

    $scope.delete_row = function ($event) {

        swal({
            title: "Are you sure?",
            text: "You will not able to undo this action!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        }, function () {
            var modal_id = angular.element($event.target).attr("data-modal_id");

            DataService.delete_modal('ads', modal_id, function (response) {
                angular.element($event.target).parent().parent().remove();
                swal("Deleted!", "That record has been deleted successfully!!!", "success");
            });

        });
    }

});