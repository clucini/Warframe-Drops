function showHint(str) {
    if (str.length == 0) { 
        document.getElementById("suggestions").innerHTML = "";
        return;
    } else {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("suggestions").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "search_items.php?item=" + str, true);
        xmlhttp.send();
    }
}

function addItems(str){
    var selected_items = document.getElementById('selected_items');
    var a = false;
    for(i = 0; i < selected_items.childNodes.length; i++){
        var item = selected_items.childNodes[i];
        if(item.id == str){
            a = true;
        }
    }
    if(a)
        return;
    var button = document.createElement('button');
    button.id = str;
    button.innerHTML = str + ' <span aria-hidden="true">&times;</span>';
    button.className = "btn ";


    button.onclick = function(){this.parentNode.removeChild(this)};
    document.getElementById("selected_items").appendChild(button)
}

function createTable() {
    str = getSelectedItems();
    if(str == "")
        return;
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("table").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET", "get_best.php?item=" + str, true);
    xmlhttp.send();
    if(!document.getElementById("show more"))
        addShowMore();
}

function addShowMore(){
    var button = document.createElement('button');
    button.id = "show more";
    button.innerHTML = "Show More Info"
    button.className = "btn btn-link";
    button.onclick = function(){showMore()};
    document.getElementById("showmore").appendChild(button);
}

function getSelectedItems(){
    var items = []
    var selected_items = document.getElementById('selected_items');
    for(i = 0; i < selected_items.childNodes.length; i++){
        var item = selected_items.childNodes[i];
        items.push(item.id);
    }
    str = items.join();
    return str;
}

function showMore(){
    str = getSelectedItems();
    window.location.href = "/results.php?item=" + str;
}