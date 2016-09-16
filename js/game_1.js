$(document).ready(document_loaded);
var lblQuestion, lblAnswer, lblScore, lblWrong, txtAnswer, bntNext;
var id;
var score = 0;
var countWrong = 0;

//temp dev
function getScore() {
    var formdata = new FormData();
    formdata.append("action", "score");

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
                MessageBox.Error();
                console.log("E43");
                return;
            }

            if (reply.succeed == true) {
                score = reply.data.score;
                countWrong = reply.data.wrongWords;
                
                //update score
                lblScore.text(score);
                lblWrong.text(countWrong);
                return;
            } else {
                console.log(reply.error);
                MessageBox.Error();
                return;
            }
        }
    });
}

//initialize components
function init() {
    //save components in variables
    bntNext = $("#bntNext");
    txtAnswer = $("#answer");
    lblQuestion = $("#lblQuestion");
    lblAnswer = $("#lblAnswer");
    lblScore = $("#lblScore");
    lblWrong = $("#lblWrong");

    //adding events
    bntNext.click(bntNext_OnClick);
    txtAnswer.keydown(txtAnswer_OnKeyDown);
}

//when pages is loaded
function document_loaded() {
    init();
    getQuestion();
}

function txtAnswer_OnKeyDown(eventArgs) {
    //when hit enter (key 13)
    if (eventArgs.which == 13) {
        //run same function as clicking on next button
        bntNext_OnClick();
    }
}
function bntNext_OnClick() {
    //check its need to go to next question or check question
    if (bntNext.val() == "next") {
        //wait few seconds and go to next question
        getQuestion();
    } else if (bntNext.val() == "check") {
        //check the answer
        check();
    }
}

function getQuestion() {
    //check if already ask for new question
    if (bntNext.prop("disabled") == false || bntNext.val() == "wait") {
        //disable button settings
        bntNext.prop("disabled", "disabled");
        bntNext.text("Laden...");

        //prepaire data that need to send to the server
        var formdata = new FormData();
        formdata.append("action", "question");

        //send asynchroon data to server and wait for response
        $.ajax({
            //settings for sending
            url: "php/get.php",
            type: "POST",
            data: formdata,
            mimeType: "multipart/form-data",
            contentType: false,
            cache: false,
            processData: false,

            //if sending and receiving is successfull
            success: function (data) { //when succeed
                var reply;

                //reset components
                txtAnswer.css("background-color", "transparent");
                txtAnswer.val("");
                lblAnswer.text("");
                lblQuestion.text("");

                //try to parse into an array
                try {
                    reply = JSON.parse(data);
                }
                catch (ex) {
                    //error handling
                    console.log("E43");
                    MessageBox.Error();

                    //enable button
                    bntNext.text("Opnieuw laden");
                    bntNext.prop("disabled", "");
                    return;
                }

                //look in action was accomplished or not
                if (reply.succeed == true) {
                    //remember id of question
                    id = reply.data.id;

                    //show text to screen
                    lblQuestion.text(reply.data.question);
                    
                    //var mediaControl = new Media();
                    if (reply.data.type_media == "i"){
                    	new Media().showImage(reply.data.path_media);
                    } else if (reply.data.type_media == "m1"){
						new Media().playMP3(reply.data.path_media);
					}else if (reply.data.type_media == "m2"){
						new Media().playOgg(reply.data.path_media);
					}else if (reply.data.type_media == "m3"){
						new Media().playWav(reply.data.path_media);
					}
                     else{
						new Media().clear()
					}

                    //enable button
                    bntNext.prop("disabled", "");

                    //set button settings
                    bntNext.val("check");
                    bntNext.text("Controleer");
                    return;
                } else {
                    if (reply.error == "E42") {
                        window.location.href = "/review.php";
                    } else {
                        //action not accomplished
                        console.log(reply.error);
                        MessageBox.Error();

                        //enable button
                        bntNext.text("Opnieuw laden");
                        bntNext.prop("disabled", "");
                        return;
                    }
                }
            }
        });
    }
}
function check() {
    //check if already checking
    if (bntNext.prop("disabled") == false) {
        //disable button settings
        bntNext.prop("disabled", "disabled");
        bntNext.text("Nakijken...");

        //prepaire data that need to send to the server
        var formdata = new FormData();
        formdata.append("action", "answer");
        formdata.append("id", id);
        formdata.append("answer", txtAnswer.val());

        //send asynchroon data to server and wait for response
        $.ajax({
            //settings for sending
            url: "php/get.php",
            type: "POST",
            data: formdata,
            mimeType: "multipart/form-data",
            contentType: false,
            cache: false,
            processData: false,

            //if sending and receiving is successfull
            success: function (data) {
                var reply;

                //try to parse into an array
                try {
                    reply = JSON.parse(data);
                }
                catch (ex) {
                    //error handling
                    console.log("E43");
                    MessageBox.Error();;

                    //enable button
                    bntNext.text("Opnieuw nakijken");
                    bntNext.prop("disabled", "");
                    return;
                }

                //look in action was accomplished or not
                if (reply.succeed == true) {
                    if (reply.data.isGood) {
                        //update score
                        score++;

                        //set style to components
                        txtAnswer.css("background-color", "green");
                        bntNext.val("wait");
                        bntNext.text("Wachten");

                        //wait few seconds and go to next game
                        setTimeout(getQuestion, 1000);
                    } else {

                        //update wrong words
                        countWrong++;

                        //set background color of textbox to red
                        txtAnswer.css("background-color", "red");

                        //show right answer
                        lblAnswer.text(reply.data.answer);

                        //enable button
                        bntNext.prop("disabled", "");

                        //set button settings
                        bntNext.val("next");
                        bntNext.text("Volgende");
                    }
                    //update score
                    lblScore.text(score);
                    lblWrong.text(countWrong);
                    return;
                } else {
                    //action not accomplished
                    console.log(reply.error);
                    MessageBox.Error();

                    //enable button
                    bntNext.text("Opnieuw nakijken");
                    bntNext.prop("disabled", "");
                    return;
                }
            }
        });
    }
}