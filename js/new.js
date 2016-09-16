$(document).ready(document_loaded);

//declare variables
var txtSubjectName, bntSave, container;
var id = 1; //start with 1 for visual reasons

function init() { //initialize elements
    //save elements to variables
	var bntCancel = $("#bntCancel");
    bntSave = $("#bntSave");
    txtSubjectName = $("#txtSubjectName");
    container = $("#rowContainer");

    //bind events to elements
    bntSave.click(bntSave_click);
    bntCancel.click(bntCancel_click);

    //add 5 rows to container
    addRow(5);
}
function document_loaded() { //when document loaded
    //initialize elements
    init();
}

function addRow(count) { //add rows to container
    //check parameter is set, else put 1 in it
    count = count || 1;

    //add 'count' times rows
    for (var i = 0; i < count; i++) {
        container.append(
            "<tr>" +
            "<td>" +
            "<input type=\"hidden\" value=\"new\" name=\"state_" + id + "\" id=\"state_" + id + "\"/>" +
            "<label>" + id + "</label>" +
            "</td>" +

            "<td>" +
            "<input maxlength=\"40\" type=\"text\" name=\"word_" + id + "\"  id=\"word_" + id + "\" />" +
            "</td>" +

            "<td>" +
            "<input type=\"file\" name=\"image_" + id + "\" id=\"image_" + id + "\" />" +
            "</td>" +

            "<td>" +
            "<input maxlength=\"30\" type=\"text\" name=\"answer_" + id + "\" id=\"answer_" + id + "\" />" +
            "</td>" +

            "<td>" +
            "<div>" +
            "<input maxlength=\"30\" type=\"text\" name=\"multi2_" + id + "\" id=\"multi2_" + id + "\" />" +
            "<input maxlength=\"30\" type=\"text\" name=\"multi3_" + id + "\" id=\"multi3_" + id + "\" />" +
            "<input maxlength=\"30\" type=\"text\" name=\"multi4_" + id + "\" id=\"multi4_" + id + "\" />" +
            "</div>" +
            "</td>" +
            "</tr>"
         );
        id++;
    }
}

function bntSave_click() { //save basic information and all rows
    var upload = new Upload();
    upload.setProcessLabel($("#process"));
    upload.setMaxRows(id);
    upload.setSaveButton(bntSave);
    upload.setFinishText("Lijst is opgeslagen");

    upload.startSaving(1);
}
function bntCancel_click(){
	 window.location = "list.php";
}