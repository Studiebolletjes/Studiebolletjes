function Upload() {
    //private variables
    var chuckSize = 300000; //0.8mibi
    var maxFileSize = 52428800; //50mibi
    var rowUpload = {
        'file': false,
        'chunks': 0,
        'chunkID': 0,
        'blob': "",
        'request': null,
        'row': 1,
        'state': "unknown"
    };

    //variables from outside
    var lblProcess = null;
    var maxRows = null;
    var bntSave = null;
    var finishText = "Lijst is opgeslagen";
    this.setProcessLabel = function (label) { lblProcess = label; }
    this.setMaxRows = function (rows) { maxRows = rows; }
    this.setSaveButton = function (button) { bntSave = button; }
    this.setFinishText = function (text) { finishText = text; }

    //private functions
    var resetUpload = function () {
        rowUpload.file = false;
        rowUpload.chunks = 0;
        rowUpload.blob = "";
        rowUpload.request = null;
        rowUpload.chunkID = 0;
        rowUpload.row = 1;
        rowUpload.state = "unknown";
    }

    var createXmlHttpRequestObject = function () {
        var xmlHttp;

        if (window.ActiveXObject) {
            try {
                xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (err) {
                xmlHttp = false;
            }
        } else {
            try {
                xmlHttp = new XMLHttpRequest();
            }
            catch (err) {
                xmlHttp = false;
            }
        }

        if (!xmlHttp) {
            MessageBox.Error();
            console.error("E102");
            return;
        } else {
            return xmlHttp;
        }
    } //create xmlhttpRequest that all webbrowsers can use. 

    var feedbackRowSave = function () {
        //check state of request
        if (rowUpload.request.readyState == 4) { //when data have been sended
            //check request errors
            if (rowUpload.request.status != 200) {
                MessageBox.Error();
                console.error("E44");
                return;
            }
            var reply;

            //try to parse into an array
            try {
                reply = JSON.parse(rowUpload.request.response);
            }
            catch (ex) {
                //error handling
                MessageBox.Error();
                console.error("E43");
                console.log(rowUpload.request.response);
                return;
            }

            //Check chuck upload succceed
            if (reply.succeed != true) {
                MessageBox.Error();
                console.error(reply.error);
                return;
            }

            //go saving next row
            save(++rowUpload.row)

        }
    }
    var feedbackFileSave = function () {
        /*
        0: request not initialized
        1: server connection established
        2: request received
        3: processing request
        4: request finished and response is ready
        */

        //check state of request
        if (rowUpload.request.readyState == 4) {
            //check request error
            if (rowUpload.request.status != 200) {
                MessageBox.Error();
                console.error("E44");
                return;
            }
            var reply;

            //try to parse into an array
            try {
                reply = JSON.parse(rowUpload.request.response);
            }
            catch (ex) {
                //error handling
                MessageBox.Error();
                console.error("E43");
                console.log(rowUpload.request.response);
                return;
            }

            //Check chuck upload succceed
            if (reply.succeed != true) {
                MessageBox.Error();
                console.error(reply.error);

                return;
            }

            //next chunk
            rowUpload.chunkID++;

            //show process
            var rowStep = 1 / maxRows; //calculate percentage each row have
            var fileProcess = rowUpload.chunkID / rowUpload.chunks; //calculate process chunks
            var rowProcess = (rowUpload.row - 1) * rowStep;
           
            lblProcess.text(Math.round((fileProcess * rowStep + rowProcess)*100) + "%");
            //MessageBox.Show(Math.round( rowProcess) + "%")

            saveFile();
        }
    }

    var sendChuck = function () {
        //set-up url
        var url = "/php/set.php?action=upload_file";

        //check if last chuck
        if (rowUpload.chunkID + 1 >= rowUpload.chunks) {
            //escape special charecters
            var subject = $("#txtSubjectName").val();
            subject = ("" + subject).replace("*", "*0");
            subject = ("" + subject).replace("&", "*1");
            subject = ("" + subject).replace("=", "*2");

            //get extension
            var file = document.getElementById("image_" + rowUpload.row).files[0];
            var extension = file.name.split(".").pop();

			if (rowUpload.state == "new") {
	            //send extension and subject
	            url += "&ext=" + extension + "&subject=" + $("#txtSubjectName").val();
            } else{
				url += "&ext=" + extension + "&id=" + $("#id_" + rowUpload.row).val() + "&subject=" + $("#txtSubjectName").val();
			}
        }

        //set-up request
        rowUpload.request = createXmlHttpRequestObject();
        rowUpload.request.open("POST", url, true);
        rowUpload.request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        rowUpload.request.onreadystatechange = feedbackFileSave;

        //send chunk
        rowUpload.request.send(rowUpload.blob);
    }

    var saveRow = function () {
        //create formdata
        var formdata = new FormData();

        //depend on state, add action
        if (rowUpload.state == "new") {
            formdata.append("action", "upload_row");
            formdata.append("subject", $("#txtSubjectName").val());
        } else if (rowUpload.state == "remove"){
			formdata.append("action", "delete_row");
			formdata.append("id", $("#id_" + rowUpload.row).val());
		}
        else { //when state => changed
            formdata.append("action", "update_row");
            formdata.append("id", $("#id_" + rowUpload.row).val());
        }

        //add row data except file
        formdata.append("question", $("#word_" + rowUpload.row).val());
        formdata.append("m1", $("#answer_" + rowUpload.row).val());
        formdata.append("m2", $("#multi2_" + rowUpload.row).val());
        formdata.append("m3", $("#multi3_" + rowUpload.row).val());
        formdata.append("m4", $("#multi4_" + rowUpload.row).val());

        //prepaire request
        rowUpload.request = createXmlHttpRequestObject();
        rowUpload.request.open("POST", "/php/set.php", true);
        rowUpload.request.onreadystatechange = feedbackRowSave;
        rowUpload.request.send(formdata);
    }
    var saveFile = function () {
        //check all chunks uploaded
        if (rowUpload.chunkID >= rowUpload.chunks) {
            saveRow();
            return;
        }

        //calculate begin and end of chunk in file
        var begin = rowUpload.chunkID * chuckSize;
        var end = begin + chuckSize;

        //construct filereader
        var rdr = new FileReader();
        rdr.onload = sendChuck;

        //get chunk in non string format
        rowUpload.blob = "";
        rowUpload.blob = rowUpload.file.slice(begin, end);

        //determine browser and convert blob to string
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");

        if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
            //IE
            rdr.readAsText(rowUpload.blob);
        } else {
            rdr.readAsBinaryString(rowUpload.blob);
        }
    }
    var save = function (row) {
        //disable save button
        bntSave.prop("disabled", "disabled");
        bntSave.prop("value", "Bezig met opslaan...");


        //reset upload data from previous row
        resetUpload();

        //update process to label
        lblProcess.text(Math.round((row - 1) / id * 100, 2) + "%");

        //check if last row
        if (row >= maxRows) {
            //zet process label to 100%
            lblProcess.text("100%");
            //feedback to user
            MessageBox.Show(finishText);

            //redirect to list page
            window.location = "list.php";
            return;
        }

        //update values for row
        rowUpload.row = row;
        rowUpload.state = $("#state_" + row).val()

        //check state of row
        if (rowUpload.state == "new" || rowUpload.state == "changed") { //when new row or row has been changed
            //check if question of row is filled
            if ($("#word_" + row).val().trim() != "") {
                //get file
                rowUpload.file = document.getElementById("image_" + row).files[0];

                //check if file available
                if (rowUpload.file != undefined) {
                    //calculate count chunks
                    rowUpload.chunks = Math.ceil(rowUpload.file.size / chuckSize);

                    //upload file async. in chunks, starting by chunk 0
                    rowUpload.chunkID = 0;
                    saveFile();
                } else {
                    //save the row async.
                    saveRow();
                }
            } else {
                //move to next row
                save(++row);
            }
        } else if (rowUpload.state == "saved") { //else check if row already saved
            //move to next row
            save(++row); 
        }else if (rowUpload.state == "remove"){
			saveRow();
        } else {
            MessageBox.Error();
            console.error("E100");
        }
    }

    //public functions
    this.startSaving = function (row) {
        try {
            //check if all outside variables are set
            if (lblProcess == null || maxRows == null || bntSave == null) {
                MessageBox.Error();
                console.error("E40");
                return
            }

            //check unsupported browsers and informate the user when use unsupported browser
            if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
                MessageBox.Show("U browser wordt niet (meer) ondersteund door onze website. Update u webbrowser of gebruik een andere webbrowser.");
                return;
            }

            save(row);
        }
        catch (err) {
            MessageBox.Error();
            console.log("E101");
            console.error(err);
        }
    }
}