$(document).ready(document_loaded);
var slcUsers, slcMode, objExample, cssFormInput
//var exampleScreen = "<object type=\"type/html\" data=\"/list.php\"  class=\"fullscreen\">Uw webbrowser kan geen voorbeeld tonen. Update uw webbrowser of gebruik een andere.</object>";
var exampleScreen = "<iframe src=\"/list.php\"  class=\"fullscreen\"></iframe>";

function init(){
	slcUsers = $("#slcUsers");
	slcMode = $("#slcMode");
	objExample = $("#objExample");
	cssFormInput = $("#cssFormInput");
	
	//add a method to onchange handler
	slcUsers.change(activateCssMode);
	slcMode.change(loadCssForm);
	
	//load all students
	getUsers("Students", addUsers);
}

function addUsers(users){
	//all all users to dropdown box
	users.forEach(function(user){		
		slcUsers.append($('<option>', {
			value: user.username,
			text: user.username,
		}));
	});
	
	//enable css mode load example screen
	activateCssMode();
}

function loadCssForm(){
	var formdata = new FormData();
	formdata.append("user", slcUsers.val());
	formdata.append("mode", slcMode.val());
	
	//load all input fields
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
	
	//send request to activate CSS mode
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

			//load example Screen if request succeeded
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
	//mark as changed
	$("#status_" + id).val("changed");
}

function save(id){
	while (true){
		var status = $("#status_" + id).val();
		
		//check of at the end or not changed
		if (status == undefined){
			//reload example screen
			MessageBox.Show("Changed has been saved");
			activateCssMode();
			break;
		} else if (status == "unchanged"){
			id++;
			continue;
		}
		
		//save field
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
	
	//save a property
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
            	//save next item
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

	//request to exit Editor mode
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

			//if succeed, redirect to list page
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