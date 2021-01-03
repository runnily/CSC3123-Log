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
        html += '<div class="custom-control p-2 custom-switch ml-2">';

        if (userJson.admin == true) {
            var stringfunc = '/adjax/admin/' + userJson.admin + '/project/' + PROJECTID + '/user/' + userJson.id + '/, admin';
            stringfunc = `'${stringfunc}'`
            html += '<input type="checkbox" checked="True" id="' + userJson.id + '" class="custom-control-input" onchange="hash="sha256-lfINeMOxdfy8I70IOV14S4oCJB1quxpmPKDUjb9qi+M=" loadDoc(' + stringfunc + ') ">';
        } else {
            html += '<input type="checkbox" id="' + userJson.id + '" class="custom-control-input checks">';
        }

        html += '<label class="custom-control-label" for="' + userJson.id + '"> Admin </label>';
        html += '</div>'
        html += '<a href="/project/' + PROJECTID + '/user/' + '/delete/' + userJson.id + '">';
        html += '<i class="fas fa-trash-alt"></i>';
        html += '<small> delete </small>';
        html += '</a>';
        html += '</div>';
        html += '</li>';
    }
    document.getElementById("user").innerHTML = html;
}

var bool = 0;

function admin(xxtp) {
    for (var checkbox in document.getElementsByClassName('checks')) {
        if (checkbox.checked == true) {
            bool = 1;
        } else {
            bool = 0;
        }
    }
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

loadDoc("/ajax/find/note/" + PROJECTID + "/", notes);
loadDoc("/ajax/find/user/" + PROJECTID + "/", users);

document.getElementById("updateT").onkeyup = function() { project('/title/' + document.getElementById("updateT").value) };
document.getElementById("updateS").onkeyup = function() { project('/summary/' + document.getElementById("updateS").value) };

var elements = document.getElementsByClassName('custom-control-input');
console.log(elements.length);