$(document).ready(document_loaded);

function document_loaded() {
    $("#bntLogin").click(bntLogin_OnClick);
}

function bntLogin_OnClick() {
    var txtUsername = $("#username");
    var txtPassword= $("#password");

    if (txtUsername.val() == ""){
        txtUsername.css("background-color", "red");
        return;
    }
    txtUsername.css("background-color", "white");
    
    if (txtPassword.val() == ""){
        txtPassword.css("background-color", "red");
        return;
    }
    txtPassword.css("background-color", "white");

    var formdata = new FormData();
    formdata.append("username", txtUsername.val());
    formdata.append("password", txtPassword.val());

    $.ajax({
        url: "php/login.php",
        type: "POST",
        data: formdata,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) { //when succeed
            if (data == "OK:1") {
                location.reload();
            }
            else if (data == "E5")
                MessageBox.Show("Wrong username/password");
            else if (data == "E6"){
				MessageBox.Show("Account niet geactiveerd");
			}
            else if (data == "E8") {
                MessageBox.Show("Already logged in");
            }
            else {
                console.log(data);
                MessageBox.Error();
            }
        }
    });
}