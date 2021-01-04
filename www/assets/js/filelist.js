/**
 * filelist module
 * @module js/filelist
 * @description This module is used by twig note 
 * @author Adanna Obibuaku
 * @copyright 2020 Adanna
 */


var NOTEID = document.getElementById('noteid').getAttribute('data-note');
var PROJECTID = document.getElementById('noteid').getAttribute('data-project');


/**
 * This function uses adjax to allow users to view
 * the files they have synchronously
 * @returns {void}
 */
function uploads() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var json = JSON.parse(this.responseText);
            var html = '';
            for (var upl in json) {
                var uplJson = json[upl];
                html += '<li class="list-group-item">';
                var type = uplJson.filename.split(".");
                type = type[length - 1];
                if (type in ['png', 'gif', 'tiff', 'jpg']) {
                    html += '<i class="far fa-image"></i>';
                } else if (type in ['doc', 'docx']) {
                    html += '<i class="far fa-file-word"></i>';
                } else if (type == 'pdf') {
                    html += '<i class="far fa-file-pdf"></i>';
                } else {
                    html += '<i class="far fa-file"></i>';
                }
                html += '<a href="/note/' + NOTEID + '/project/' + PROJECTID + '/download/' + uplJson.id + '/">';
                html += ' ' + uplJson.filename;
                html += '</a>';
                html += '<a href="/note/' + NOTEID + '/project/' + PROJECTID + '/delete/' + uplJson.id + '/">';
                html += '<small> delete </small>';
                html += '</a>';
                html += '</li>';
            }
            document.getElementById("upload").innerHTML = html;
        }
    };
    xhttp.open("GET", "/ajax/find/upload/" + NOTEID + "/", true);
    console.log("/ajax/find/upload/" + NOTEID + "/")
    xhttp.send();
}

uploads();