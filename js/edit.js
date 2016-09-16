$(document).ready(document_loaded);

//declare variables
var txtSubjectName, bntSave, container;
var id = 1;

function init() { //initialize elements
    //save elements to variables
	var bntCancel = $("#bntCancel");
    bntSave = $("#bntSave");
    container = $("#rowContainer");

    //bind events to elements
    bntSave.click(bntSave_click);
    bntCancel.click(bntCancel_click);
}
function document_loaded() { //when document loaded
    //initialize elements
    init();

    //check highest id
    while ($("#state_" + id).val() != undefined)
        id++;
}

function addRow(count) { //add rows to container
    //check parameter is set, else put 1 in it
    count = count || 1;

    //add 'count' times rows
    for (var i = 0; i < count; i++) {
        container.append(
            "<tr id=\"row_" + id + "\">" +
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
            "<td>" +
            "<input type=\"button\" value=\"Verwijder\" id=\"removeRow_" + id + "\" " +
            	"onclick=\"javascript:removeRow(" + id + ") />" +
            "</td>" + 
            "</tr>"
         );
        id++;
    }
}

function bntCancel_click(){
	 window.location = "list.php";
}

function bntSave_click() {
    var upload = new Upload();
    upload.setProcessLabel($("#process"));
    upload.setMaxRows(id);
    upload.setSaveButton(bntSave);
    upload.setFinishText("Wijzigen zijn opgeslagen");
    upload.startSaving(1);
}

function row_onchange(row) {
    $("#state_" + row).val("changed");
}

function removeRow(row){
	if ($("#state_" + row).val() == "remove"){
		$("#state_" + row).val("changed");	
		$("#row_" + row).css("background-color", "transparent");
		$("#row_" + row).css("opacity", "1");
		
		$("#removeRow_" + row).val("Verwijderen");
	}
	else if ($("#state_" + row).val() != "new"){
		$("#state_" + row).val("remove");	
		$("#row_" + row).css("background-color", "gray");
		$("#row_" + row).css("opacity", "0.6");
		
		$("#removeRow_" + row).val("Ongedaan maken");
	} 
	else{
		$("#word_" + row).val("");
	}
}