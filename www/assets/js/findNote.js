var PROJECTID = document.getElementById("note").getAttribute('data-project');
document.getElementById("state").style.visibility = "hidden";

function loadDoc(url, cFunction) {
    var xhttp;
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            cFunction(this);
        }
    };
    console.log(url);
    xhttp.open("GET", url, true);
    xhttp.send();
}

function notes(xttp) {
    var json = JSON.parse(xttp.responseText);
    var html = '';
    for (var note in json) {
        var noteJson = json[note];
        html += "<li class='list-group-item list-group-item-action'>";
        html += "<div class='d-flex w-100 justify-content-between'>";
        html += "<a href='/note/" + noteJson.id + "/project/" + PROJECTID + "/'>";
        html += "<i class='far fa-sticky-note'></i>";
        html += " " + noteJson.title;
        html += "</a>"
        html += "<a href='/project/" + PROJECTID + "/note/delete/" + noteJson.id + "/'>";
        html += "<i class='fas fa-trash-alt'></i>";
        html += "<small> delete </small>";
        html += "</a>";
        html += "</div>";
        html += "<p class='mb-1'>" + noteJson.summary + "</p>";
        html += "</li>";
    }
    document.getElementById("note").innerHTML = html;
}

function users(xttp) {
    var json = JSON.parse(xttp.responseText);
    var html = '';
    for (var user in json) {
        var userJson = json[user];
        html += '<li class="list-group-item list-group-item-action">';
        html += '<div class="d-flex w-100 justify-content-between">';
        html += '<a href="/profile/' + userJson.id + '">';
        html += '<i class="far fa-user"></i> ';
        html += userJson.login;
        html += '</a>';
        html += '<span class="custom-control p-2 custom-switch ml-2">';
        if (userJson.admin) {
            html += '<input type="checkbox" checked="True" id="{{user.id}}" class="custom-control-input">';
        } else {
            html += '<input type="checkbox" id="{{user.id}}" class="custom-control-input">';
        }
        html += '<label class="custom-control-label" for="{{user.id}}"> Admin </label>';
        html += '</span>'
        html += '<a href="/project/' + PROJECTID + '/user/' + '/delete/' + userJson.id + '">';
        html += '<i class="fas fa-trash-alt"></i>';
        html += '<small> delete </small>';
        html += '</a>';
        html += '</div>';
        html += '</li>';
    }
    document.getElementById("user").innerHTML = html;
}

function project(url) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("state").style.visibility = "hidden"
        }

    };
    xhttp.open("POST", "/ajax/insertproject/" + PROJECTID + url, true);
    document.getElementById("state").style.visibility = "visible"
    xhttp.send();
}

loadDoc("/ajax/findnote/" + PROJECTID + "/", notes);
loadDoc("/ajax/finduser/" + PROJECTID + "/", users);

document.getElementById("updateT").onkeyup = function() { project('/title/' + document.getElementById("updateT").value) };
document.getElementById("updateS").onkeyup = function() { project('/summary/' + document.getElementById("updateS").value) };