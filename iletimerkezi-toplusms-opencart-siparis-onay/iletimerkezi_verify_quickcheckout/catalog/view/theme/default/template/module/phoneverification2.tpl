<div id="repl">
	<h4><div class="text-danger" style="display:none; font-size:17px;"  id="ovwarn"></div></h4>
	<h3>SMS ile doğrulama </h3>
<div class="buttons" id="step1">
<p style="color:black;">Aşağıdaki alana erişebileceğiniz telefon numarası yazınız, telefon numaranıza gelen şifreyi girmeniz istenecektir.</p>
<p style="color:black;">Telefon numaranız:</p>
<input type="text" name="phone" id="phone" class="form-control" value=""> <br><br>

<div class="right">
<input type="button" value="SMS Gönder" id="button-startver" data-loading-text="Loading..." class="btn btn-primary">
<!--<a id="button-startver" class="button"><span>SMS Gönder</span></a>-->
</div>
</div>
<div id="step2" style="display:none" class="buttons">
<h4>Doğrulama başladı</h4><br>
<p style="color:black;">Aşağıdaki alana cep telefonunuza gelen doğrulama şifrenizi giriniz.</p>
<p style="color:black;">Pin:</p> <input class="form-control" type="text" name="pin" id="pin">
  <div class="right"> <br><br>
  <input type="button" value="Doğrula" id="button-confirm" data-loading-text="Loading..." class="btn btn-primary">&nbsp;&nbsp;&nbsp;&nbsp;
  <input type="button" value="Tekrar Gönder" id="button-startver2" data-loading-text="Loading..." class="btn btn-primary">
  <!--<a id="button-confirm" class="button"><span>Doğrula</span></a> 
  <a id="button-startver2" class="button"><span>Tekrar Gönder</span></a></div>-->
</div>
<script type="text/javascript"><!--
var pinsent = '<?php echo $pinsent?>';
var vtype2 = '<?php echo $vtype2?>';
if (pinsent=='1') {
		
		$("#step1").hide();
		$("#step2").show();
		$("#type1").hide();
		$("#type2").hide();
		if (vtype2) $("#type"+vtype2).show();
}
$('#step2>.right>#button-confirm').bind('click', function() {
	$.ajax({ 
		type: 'POST',
		data: 'pin=' + $('#step2>#pin').val(),
		url: 'index.php?route=module/phoneverification/confirm',
		success: function(data) {
				if (data==1) {
					$("#ovwarn").hide();
					$("#repl").load("index.php?route=module/phoneverification/getrepl");
				}
				else {
				$("#ovwarn").html("<?php echo $text_invalid_pin?>");
				$("#ovwarn").show();
				}
		}		
	});
});
$('#step2>.right>#button-startver2').bind('click', function() {
	$("#step2").hide();
		$("#step1").show();
});
var wait = 0;
$("#step1>.right>#button-startver").bind('click', function() {

if (!wait) {
wait = 1;
	$.ajax({ 
		type: 'POST',
		data: 'phone='+$('#step1>#phone').val() + "&svtype=" + $("#step1>input[name='svtype']:checked").val(),
		url: 'index.php?route=module/phoneverification/start',
		success: function(data) {
		wait = 0;
			switch (data) {
			case "5":
				$("#ovwarn").html("<?php echo $text_provide_valid_mobile_number;?>");
				$("#ovwarn").show();
			break;
			case "2":
			case "1":
				
					$("#ovwarn").hide();
					$("#step1").hide();
					$("#step2").show();	
					$("#type1").hide();
					$("#type2").hide();
					$("#type"+data).show();
			break;
			case "17":
			$("#ovwarn").html("<?php echo $text_explain_unique_number;?>");
				$("#ovwarn").show();
			break;
			case "15":
			$("#ovwarn").html("<?php echo $text_explain_same_number;?>");
				$("#ovwarn").show();
			break;
			case "14":
			$("#ovwarn").html("<?php echo $text_please_wait_next;?>");
				$("#ovwarn").show();
			break;
			case "12":
			$("#ovwarn").html("<?php echo $text_max_retries_exceeded;?>");
				$("#ovwarn").show();
			break;
			case "16":
			$("#ovwarn").html("<?php echo $text_connection_problem;?>");
				$("#ovwarn").show();
			break;
			default:
			$("#ovwarn").html("<?php echo $text_provide_valid_number;?>");
				$("#ovwarn").show();
				
				}
		}		
	});
	}
	else alert('<?php echo $text_please_wait;?>');
});
//--></script> 

</div>