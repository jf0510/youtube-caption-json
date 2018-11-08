$('.scrapy-btn').click(function() {
	//check single url or not
	var input = $('.input-url').val();
	var v_id = "";
	if(input != ""){
	  if(input.length == 11){
			v_id = input;
		}else if(input.includes("youtube")){
			v_id = input.split("?v=")[1];
		}
		
		var v_id_JSON = JSON.stringify({
		  v: v_id
		});
		$.ajax({
		  type:"POST",
		  url:"youtubeAPI.php",
		  data: v_id_JSON,
		  dataType: "json",
		  contentType: "application/json; charset=utf-8",
		  processData: false,
		  success: function(data) {
				console.log(data);
				$('.input-url').val("");
				if(data.id != ""){				
					$('#text').get(0).href = "./json/" + data.file;
					$('#text').get(0).download = data.file;
					$('#text').get(0).click();
				}else{
					alert("請挑選有英文字幕之影片，謝謝!");
				}
			//window.location = data.file;
		  },
		  error: function(){
				console.log("error");
		  }
		});
	}
});