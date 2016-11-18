
var appConst = {};

if (location.host == 'localhost') {
    appConst.base_url = location.port + '//' + location.host + '/promotion-analysis';
    
} else {
    appConst.base_url = location.port + '//' + location.host;
    
}

// update
appConst.url_db_data = appConst.base_url + "/ng-data";

// File upload
appConst.url_jsupload_files = appConst.base_url + "/ajax/js-upload";
appConst.url_delete_uploaded_files = appConst.base_url + "/ajax/du-file";
appConst.url_create_image = appConst.base_url + "/ajax/create-image-base-64";
appConst.url_move_uploaded_file = appConst.base_url + "/ajax/mu-file";
appConst.url_ajax = appConst.base_url + "/ajax/";