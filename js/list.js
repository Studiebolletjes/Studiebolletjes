function startGame(subject) {
    var formdata = new FormData();
    formdata.append("action", "new_game");
    formdata.append("subject", subject);

    $.ajax({
        url: "php/get.php",
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
                return false;
            }

            if (reply.succeed == true) {
                window.location.href = "/game_1.php";
                return true;
            } else {
                console.log(reply.error);
                MessageBox.Error();
                return false;
            }
        }
    });
}

function editSubject(subject) {
    window.location.href = "/edit.php?subject=" + subject;
}

function deleteSubject(subject) {

    var formdata = new FormData();
    formdata.append("action", "delete");
    formdata.append("subject_name", subject);

    $.ajax({
        url: "php/set.php",
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
                MessageBox.Show("Lijst is verwijderd")
                location.reload();
                return true;
            } else {
                console.log(reply.error);
                MessageBox.Error();
                return false;
            }
        }
    });
}