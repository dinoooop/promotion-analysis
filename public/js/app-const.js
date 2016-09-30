
var appConst = {};

if (location.host == 'localhost' || location.host == '192.168.1.133') {
    appConst.base_url = location.port + '//' + location.host + '/post-video';
} else {
    appConst.base_url = location.port + '//' + location.host + '/nri-stories';
}

// update
appConst.url_db_data = appConst.base_url + "/ng-data";

// File upload
appConst.url_jsupload_files = appConst.base_url + "/ajax/js-upload";
appConst.url_delete_uploaded_files = appConst.base_url + "/ajax/du-file";
appConst.url_create_image = appConst.base_url + "/ajax/create-image-base-64";
appConst.url_move_uploaded_file = appConst.base_url + "/ajax/mu-file";
appConst.url_ajax = appConst.base_url + "/ajax/";

// Custom urls
appConst.url_preview_uploaded_file = appConst.base_url + "/ajax/preview-uploaded-file";
appConst.url_get_videos = appConst.base_url + "/ajax/videos";
appConst.token = "1Gz6YI0h0hIOhBPKTztw8CtuhousXiOEeSWPRFEf";
