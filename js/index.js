$(document).ready(function(){

    $('.achieve_button').each(function(i,v){
        $(v).bind("click",function(){
            $(v).next().toggle();
        });
    });

$('.edit_cont textarea').each(function(i,v){
	$(v).bind("keyup",function(){
		var value=v.value;
		if(value.length<140){
			$(v).parent().next().children('.tip').hide();
		   }else{
		            	$(v).parent().next().children('.tip').show();
		            	return;
		 }	
	})	
});

$('.save_button').each(function(i,v){
	$(v).bind("click",function(){
		var value=$(v).parent('p').prev().children('textarea').attr('value');
	            	if(value.length<140){
	            		$(v).parent('.edit_cont').submit();
	            	}else{
	            		return false;
	            	}
	});
});

$('#wishId').bind("keyup",function(){
	var value=$(this).attr('value');
	if(value.length<140){
		$('#error').hide();
	}else{
		$('#error').show();
		return;
	 }
});

$("#wishBut").bind("click",function(){
	var value=$('#wishId').attr('value');
	if(value.length<140){
	            	$(v).parent('.edit_cont').submit();
	}else{
	             return false;
	}
});

$(".time").each(function(i,v){
	var heig=$(this).next().height()+10;
	$(this).css("height",heig);
})
    
});
