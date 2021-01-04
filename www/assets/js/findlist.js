/**
 * findlist module
 * @module js/findlist
 * @description This module is used by twig project, to invoke adjax operations in order to see
 *              usefull information about the project
 * @author Adanna Obibuaku
 * @copyright 2020 Adanna
 */

var PROJECTID = document.getElementById("projectid").getAttribute('data-project');
document.getElementById("state").style.visibility = "hidden";

/**
 * This is a call back function which uses adjax to allow operations to happen
 * @param {string}          url         The url to send the adjax request to
 * @param {function}        cFunction   the function to do the operation once ajax request is successful
 * @returns {void}
 */
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

/**
 * This is one function notes which is used by the ajax function this is used
 * to display the notes within the database assiocated to a certain project
 * @param {XMLHttpRequest}      xttp    used to send operations to a defined ajax operation
 * @returns {void}
 */
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
        if (noteJson.summary) {
            html += "<p class='mb-1'>" + noteJson.summary + "</p>";
        }
        html += "</li>";
    }
    document.getElementById("note").innerHTML = html;
}

/**
 * This is one function user which is used by the ajax function this is used
 * to display the users within the database assiocated to a certain project
 * @param {XMLHttpRequest}      xttp    used to send request to a defined ajax operation
 * @returns {void}
 */
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
        html += '<a href="/project/' + PROJECTID + '/user/' + '/delete/' + userJson.id + '">';
        html += '<i class="fas fa-trash-alt"></i>';
        html += '<small> delete </small>';
        html += '</a>';
        html += '</div>';
        html += '</li>';
    }
    document.getElementById("user").innerHTML = html;
}


/**
 * A seperate ajax function which is used to send POST request
 * to allow the user to update their title sychronisaly.
 * @param {string}      url   the url to send
 * @returns {void}
 */
function project(url) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("state").style.visibility = "hidden"
        }
    };
    xhttp.open("POST", "/ajax/insertproject/" + PROJECTID + url, true);
    console.log("/ajax/insertproject/" + PROJECTID + url);
    document.getElementById("state").style.visibility = "visible"
    xhttp.send();
}

// calls operations defined

loadDoc("/ajax/find/note/" + PROJECTID + "/", notes);
loadDoc("/ajax/find/user/" + PROJECTID + "/", users);

document.getElementById("updateT").onkeyup = function() { project('/title/' + document.getElementById("updateT").value) };
document.getElementById("updateS").onkeyup = function() { project('/summary/' + document.getElementById("updateS").value) };