function Media() {
	var mediaBox = $("#mediaBox");
	playMusic = function(path, type){
		public.clear()
		mediaBox.append(
			"<audio id=\"musicPlayer\" controls=\"controls\" autoplay=\"autoplay\">" + 
			"<source src=\"" + path +/* "?" + new Date().getTime() +*/ "\" type=\"audio/" + type + "\" />" +
			"</audio>"
		);
		
		var audio = document.getElementById("musicPlayer");
		if (audio.canPlayType("audio/" + type)){
			audio.load();
			audio.play();
		} else {
			MessageBox.Show(type + " is not supported by your browser");
		}
	}
	
	var public = {
		showImage: function(path){
			public.clear();
			mediaBox.append("<img src=\"" + path + "\" id=\"imageBox\" />");
		},
		
		playMP3: function(path){
			playMusic(path, "mpeg");
		},
		
		playOgg: function(path){
			playMusic(path, "ogg")
		},
		playWav: function(path){
			playMusic(path, "wav")
		},
		
		clear: function(){
			mediaBox.html("");
		},
	
	}
	
	return public;
}