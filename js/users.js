function createUser(){
	var formdata = new FormData();
    formdata.append("action", "create");
    formdata.append("username", $("#txtUsername").val());
	formdata.append("password", $("#txtPassword").val());
	formdata.append("pass_repeat", $("#txtPassRepeat").val());
    formdata.append("email", $("#txtEmail").val());
	formdata.append("rights", $("#slcRights").val());

	
	$.ajax({
        url: "php/userManager.php",
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
                console.log("E43");
                MessageBox.Error();
                console.log(data);
                return false;
            }

            if (reply.succeed == true) {
                gotoUrl("/users.php");
            } else {
                MessageBox.Show(reply.error);
            }
        }
    });
}

function getUsers(type, onReceive){
	var formdata = new FormData();
	formdata.append("action", "get" + type);
	
	$.ajax({
        url: "php/userManager.php",
        type: "POST",
        data: formdata,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
        	var reply;

            try {
                reply = JSON.parse(data);
            }
            catch (ex) {
                console.log("E43");
                MessageBox.Error();
                console.log(data);
                return false;
            }

            if (reply.succeed == true) {
            	
            	if (onReceive != undefined){
        			onReceive(reply.error);
        		}
        	} else{
				MessageBox.Show(reply.error);
			}
        }
	});
}

function drawTeachers(teachers){
	$("#divTeachers").html("");
	teachers.forEach(function(teacher){
		var html = 
			"<div class='sort'>" + 
			"<label>" + teacher.username;
		if (!teacher.activate){
			html += " (inactief)";
		}
		html += "</label>";
		
		//html += "<input type='button' value='Bewerken' />";
		html += "<input type='button' value='Verwijderen' onclick=\"javascript:removeUser('" + teacher.username + "')\" />";
		
		$("#divTeachers").append(html + "</div>");
	});
}

function drawStudents(students){
	$("#divStudents").html("");
	students.forEach(function(student){
		var html = 
			"<div class='sort'>" + 
			"<label>" + student.username;
		if (!student.activate){
			html = html + " (inactief)";
		}
		html += "</label>";
		
		//html += "<input type='button' value='Bewerken' />";
		html += "<input type='button' value='Verwijderen' onclick=\"javascript:removeUser('" + student.username +  "')\" />";
		
		
		$("#divStudents").append(html + "</div>");
	});
}

function removeUser(username){
	var formdata = new FormData();
	formdata.append("action", "remove");
	formdata.append("username", username);
	
	$.ajax({
        url: "php/userManager.php",
        type: "POST",
        data: formdata,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
        	var reply;

            try {
                reply = JSON.parse(data);
            }
            catch (ex) {
                console.log("E43");
                MessageBox.Error();
                console.log(data);
                return false;
            }

            if (reply.succeed == true) {
            	MessageBox.Show("Gebruiker is verwijderd");
            	window.location.reload();
        	} else{
				MessageBox.Show(reply.error);
			}
        }
	});
}

function gotoUrl(url){
	window.location.replace(url);
}