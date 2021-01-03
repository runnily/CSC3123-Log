function notes() {
    var project = document.getElementById("note").getAttribute('data-project');
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var json = JSON.parse(this.responseText);

            var html = '';
            for (var note in json) {
                var noteJson = json[note];
                html += "<li class='list-group-item list-group-item-action'>";
                html += "<div class='d-flex w-100 justify-content-between'>";
                html += "<a href='/note/" + noteJson.id + "/project/" + project + "/'>";
                html += "<i class='far fa-sticky-note'></i>";
                html += " " + noteJson.title;
                html += "</a>"
                html += "<a href='/project/" + project + "/note/delete/" + noteJson.id + "/'>";
                html += "<i class='fas fa-trash-alt'></i>";
                html += "<small> delete </small>";
                html += "</a>";
                html += "</div>";
                html += "<p class='mb-1'>" + noteJson.summary + "</p>";
                html += "</li>";
            }
            document.getElementById("note").innerHTML = html;
        }
    };
    console.log("/ajax/findNote/" + project + "/")
    xhttp.open("GET", "/ajax/findNote/" + project + "/", true);
    xhttp.send();
}


notes();