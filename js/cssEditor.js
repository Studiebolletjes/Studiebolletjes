$(document).ready(document_loaded);
var slcUsers, objExample, cssFormInput
//var exampleScreen = "<object type=\"type/html\" data=\"/list.php\"  class=\"fullscreen\">Uw webbrowser kan geen voorbeeld tonen. Update uw webbrowser of gebruik een andere.</object>";
var exampleScreen = "<iframe src=\"/list.php\"  class=\"fullscreen\"></iframe>";

function init(){
	slcUsers = $("#slcUsers");
	objExample = $("#objExample");
	cssFormInput = $("#cssFormInput");
	
	slcUsers.change(activateCssMode);
	getUsers("Students", addUsers);
}

function addUsers(users){
	users.forEach(function(user){		
		slcUsers.append($('<option>', {
			value: user.username,
			text: user.username,
		}));
	});
	
	activateCssMode();
}

function loadCssForm(){
	var formdata = new FormData();
	formdata.append("user", slcUsers.val());
	
    $.ajax({
        url: "php/cssFormInput.php",
        type: "POST",
        data: formdata,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) { //when succeed
        	cssFormInput.html(data);
        }
	});
}

function document_loaded(){
	init();
}

function activateCssMode(){
	var formdata = new FormData();
	formdata.append("action", "CHECK");
	formdata.append("user", slcUsers.val());
	
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
            	objExample.html(exampleScreen);
            	loadCssForm();
                return;
            } else {
                console.log(reply.error);
                MessageBox.Error();
                return;
            }
        }
	});
}

function setChanged(id){
	$("#status_" + id).val("changed");
}

function save(id){
	while (true){
		var status = $("#status_" + id).val();
		
		if (status == undefined){
			MessageBox.Show("Changed has been saved");
			activateCssMode();
			break;
		} else if (status == "unchanged"){
			id++;
			continue;
		}
		
		var selector = $("#selector_" + id).val();
		var property = $("#property_" + id).html();
		var value = $("#value_" + id).val();
		
		sendItem(selector,property, value, ++id)
		break;
	}
}
function sendItem(selector, property, value, nextId){
	var formdata = new FormData();
	formdata.append("action", "SET");
	formdata.append("user", slcUsers.val());
	formdata.append("selector", selector);
	formdata.append("property", property);
	formdata.append("value", value);
	
	
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
            	save(nextId);
                return;
            } else {
                console.log(reply.error);
                MessageBox.Error();
                return;
            }
        }
	});
}

function closeEditor(){
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
            	window.location.replace("/list.php");
                return;
            } else {
                console.log(reply.error);
                MessageBox.Error();
                return;
            }
        }
    });
}