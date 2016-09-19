$(document).ready(document_loaded);
var dropdownMenu;
var lblMenu;
var ulMenu;

function init(){
	
	dropdownMenu = $(".dropdownMenu");
	lblMenu = $("#menu_label");
	ulMenu = $("#menu_dropdownActive");
	
	lblMenu.mouseenter(lblMenu_OnMouseEnter);
	dropdownMenu.mouseleave(lblMenu_OnMouseoLeave);
}

function document_loaded(){
	init();
}

function lblMenu_OnMouseEnter(){
	ulMenu.show();
}

function lblMenu_OnMouseoLeave(){
	ulMenu.hide();
}

function bntLogout_OnClick() {
    var formdata = new FormData();
    formdata.append("logout", true);

    $.ajax({
        url: "php/login.php",
        type: "POST",
        data: formdata,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) { //when succeed
            if (data == "OK:2") {
                location.reload();
            }
            else if (data == "E7"){
                MessageBox.Show("Not logged in");
            }
            else {
                console.log(data);
                MessageBox.Show("There is an error");
            }
        }
    });
}

function escapeCssEditorMode(){
	var formdata = new FormData();
    formdata.append("action", "STOP");
    formdata.append("user", "");

    $.ajax({
        url: "php/cssManager.php",
        type: "POST",
        data: formdata,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) { //when succeed
            var reply;

            try {
                reply = JSON.parse(data);
            }
            catch (ex) {
                MessageBox.Error();
                console.log("E43");
                return;
            }

            if (reply.succeed == true) {
            	location.reload();
                return;
            } else {
                console.log(reply.error);
                MessageBox.Error();
                return;
            }
        }
    });
}

function gotopage(url){
	window.location.replace(url);
}