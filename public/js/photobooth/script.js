$(document).ready(function(){
	
});

var count = 0;

function photobooth(){

	count=1;
    
	if(count == 1){
		$('#wrapper_booth').show();
		$('#example').photobooth().on( "image", function( event, dataUrl ){
			$("#imagem").val(dataUrl);
			$("#foto").attr("src",dataUrl);
			$("#example").data( "photobooth" ).destroy();
			$("#wrapper_booth").hide();
			count=0;
		});
	}
}