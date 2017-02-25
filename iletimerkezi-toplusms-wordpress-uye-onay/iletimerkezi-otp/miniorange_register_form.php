<?php

	function miniorange_site_register_form(){	
	?>
 		<input type="hidden" name="register_nonce" value="register_nonce"/>
 		<p>
		<label for="phone_number_mo">Telefon<br>
		<input type="text" name="phone_number_mo" id="phone_number_mo" class="input" value="" size="25"></label>
	</p>
	<?php
	}

	/* OTP LOGIC */
	function miniorange_site_challenge_otp($user_login, $user_email, $errors, $phone_number=null,$otp_type,$password="",$extra_data=null,$from_both=false){
		if (session_id() == '' || !isset($_SESSION)) { session_start(); }
		$_SESSION['current_url'] = MO_Validation_Utility::mo_curpageurl();
		$_SESSION['user_email'] = $user_email;
		$_SESSION['user_login'] = $user_login;
		$_SESSION['user_password'] = $password;
		$_SESSION['phone_number_mo'] = $phone_number;
		$_SESSION['extra_data'] = $extra_data;
		//die('<pre>'.var_export($_SESSION).'</pre>');
		if($otp_type=="phone"){
			$challenge_otp = new MO_Validation_Utility();
	
			$number = preg_replace('/\D/','',$phone_number);
			$number = substr($number, -10);
			if(!$number){
				if(isset($_SESSION['woocommerce_checkout_page']) || isset($_SESSION['cf7_contact_page'])){
					$result['message']='Lütfen sadece rakamlardan oluşan numaranızı yazın.';
					$result['result'] = 'error';
					wp_send_json( $result );
				}else{
					miniorange_site_otp_validation_form(null,null,null,"<strong>HATA:</strong> ".$phone_number." numarası hatalıdır, lütfen sadece rakamlardan oluşan numaranızı yazın.",$otp_type,$from_both);
					exit();
				}
			}else{
				$content = $challenge_otp->send_otp_token('SMS','',$number);
				
				if(strcasecmp($content, '<code>200</code>') == 0) {
					
					//$_SESSION['mo_customer_validation_site_txID'] = $content['txId'];
					update_option('mo_otp_verification_phone_otp_count',1);
					/*if(get_option('mo_otp_plugin_version')>1.4){
						update_option('mo_customer_phone_transactions_remaining',get_option('mo_customer_phone_transactions_remaining')-1);
					}*/
					if(isset($_SESSION['woocommerce_checkout_page']) || isset($_SESSION['cf7_contact_page'])){
						$result['message']=' <b>' . $phone_number . ' </b> Numaralı hatta onay kodu gönderilmiştir. <br/><br/>Lütfen size gönderilen onay konudunu giriniz.';
						$result['result'] = 'success';
						wp_send_json( $result );
					}else{
						$message = ' <b>' . $phone_number . ' </b> Numaralı hatta onay kodu gönderilmiştir. <br/><br/>Lütfen size gönderilen onay konudunu giriniz.';
						miniorange_site_otp_validation_form($user_login, $user_email,$phone_number,$message,$otp_type,$from_both);
						exit();
					}
				}else{
					if(isset($_SESSION['woocommerce_checkout_page'])){
						$result['message']='Sms gönderimi sırasında bir hata oldu, lütfen tekrar deneyin.';
						$result['result'] = 'error';
						wp_send_json( $result );
					}else{
						miniorange_site_otp_validation_form(null,null,null,"Sms gönderimi sırasında bir hata oldu, lütfen tekrar deneyin.",$otp_type,$from_both);
						exit();
					}
				}
				
			}
		}elseif($otp_type=="email"){
			
			$challenge_otp = new MO_Validation_Utility();
			$content = json_decode($challenge_otp->send_otp_token('EMAIL',$user_email), true);

			if(strcasecmp($content['status'], 'SUCCESS') == 0) {
				$_SESSION['mo_customer_validation_site_txID'] = $content['txId'];
				update_option('mo_otp_verification_email_otp_count',1);
				if(get_option('mo_otp_plugin_version')>1.4){
					update_option('mo_customer_email_transactions_remaining',get_option('mo_customer_email_transactions_remaining')-1);
				}
				if(isset($_SESSION['woocommerce_checkout_page']) || isset($_SESSION['cf7_contact_page'])){
					$result['message']='A verification code has been sent to <strong>'.$user_email.'</strong> . Please Enter the code in the Verification Field below to verify your email.';
					$result['result'] = 'success';
					wp_send_json( $result );
				}else{
					$message = 'A One Time Passcode has been sent to <b>' . $user_email . ' </b><br/><br/>Please enter the OTP below to verify your Email Address. If you cannot see the email in your inbox, make sure to check your SPAM folder.';
					miniorange_site_otp_validation_form($user_login, $user_email,$phone_number,$message,$otp_type,$from_both);
					exit();
				}
			}else{
				if(isset($_SESSION['woocommerce_checkout_page']) || isset($_SESSION['cf7_contact_page'])){
					$result['message']="There was an error in sending the verification code. Please enter a valid Email Id or contact site Admin.";
					$result['result'] = 'error';
					wp_send_json( $result );
				}else{
					miniorange_site_otp_validation_form(null,null,null,"There was an error in sending the OTP to the given Email Address. Please Try Again.",$otp_type,$from_both);
					exit();
				}
			}
		}elseif($otp_type=="both"){
			$message = "Please select one of the methods below to verify your account. A One time passcode will be sent to the selected method.";
			miniorange_verification_user_choice($user_login, $user_email,$phone_number,$message,$otp_type);
			exit();
		}else{
			miniorange_site_otp_validation_form($user_login, $user_email,$phone_number,$message,"phone",$from_both);
			exit();
		}
	}
	
	function miniorange_site_otp_validation_form($user_login,$user_email,$phone_number,$message,$otp_type,$from_both){
	?>
	<html>
		<head>
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<?php
				echo '<link rel="stylesheet" type="text/css" href="' . plugins_url('includes/css/mo_customer_validation_style.css', __FILE__) . '" />';
			?>
		</head>
		<body>
			<div class="mo-modal-backdrop">
				<div class="mo_customer_validation-modal" tabindex="-1" role="dialog" id="mo_site_otp_form">
					<div class="mo_customer_validation-modal-backdrop"></div>
					<div class="mo_customer_validation-modal-dialog mo_customer_validation-modal-md">
						<div class="login mo_customer_validation-modal-content">
							<div class="mo_customer_validation-modal-header">
								<b>Telefon Doğrulama</b>
								<a class="close" href="#" onclick="mo_validation_goback();" ><?php printf( __( '&larr; Geri Git' )); ?></a>
							</h3>
							</div>
							<div class="mo_customer_validation-modal-body center">
								<div><?php echo $message; ?></div><br /> 
								<?php if(!MO_Validation_Utility::mo_check_empty_or_null($user_email) || !MO_Validation_Utility::mo_check_empty_or_null($phone_number)){ ?>
								<div class="mo_customer_validation-login-container">
									<form name="f" method="post" action="">
										<input type='hidden' name="option" value='miniorange-validate-otp-form' />
										<input type="number" name="mo_customer_validation_otp_token"  autofocus="true" placeholder="" id="mo_customer_validation_otp_token" required="true" class="mo_customer_validation-textbox" autofocus="true" pattern="[0-9]{4,8}" title="Only digits within range 4-8 are allowed."/>
										<br /><input type="submit" name="miniorange_otp_token_submit" id="miniorange_otp_token_submit" class="miniorange_otp_token_submit"  value="Onayla" />
										<input type="hidden" name="otp_type" value="<?php echo $otp_type?>">
										<?php if(!$from_both){?>
											<input type="hidden" id="from_both" name="from_both" value="false">
											<a style="float:right"  onclick="mo_otp_verification_resend();"> Tekrar Gönder</a>
										<?php }else{?>
											<input type="hidden" id="from_both" name="from_both" value="true">
											<a style="float:right"  onclick="mo_select_goback();"> Tekrar Gönder</a>
										<?php }?>
										<?php extra_post_data(); ?>
									</form>
									<a href='http://miniorange.com/2-factor-authentication' hidden></a>
								</div>
								<?php }?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<form name="f" method="post" action="" id="validation_goBack_form">
				<input id="validation_goBack" name="option" value="validation_goBack" type="hidden"></input>
			</form>
			
			<form name="f" method="post" action="" id="verification_resend_otp_form">
				<input id="verification_resend_otp" name="option" value="<?php echo 'verification_resend_otp_'.$otp_type ?>" type="hidden"></input>
				<?php extra_post_data(); ?>
			</form>

			<form name="f" method="post" action="" id="goBack_choice_otp_form">
				<input id="verification_resend_otp" name="option" value="<?php echo 'verification_resend_otp_both' ?>" type="hidden"></input>
				<input type="hidden" id="from_both" name="from_both" value="true">
				<?php extra_post_data(); ?>
			</form>

			<style> .mo_customer_validation-modal{ display: block !important; } </style>
			<script>
				function mo_validation_goback(){
					document.getElementById('validation_goBack_form').submit();
				}
				
				function mo_otp_verification_resend(){
					document.getElementById('verification_resend_otp_form').submit();
				}

				function mo_select_goback(){
					document.getElementById('goBack_choice_otp_form').submit();
				}
			</script>
		</body>
    </html>
	
	<?php
	}
	
	/*function miniorange_verification_user_choice($user_login, $user_email,$phone_number,$message,$otp_type){
	?>
		<html>
		<head>
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<?php
				echo '<link rel="stylesheet" type="text/css" href="' . plugins_url('includes/css/mo_customer_validation_style.css', __FILE__) . '" />';
			?>
		</head>
		<body>
			<div class="mo-modal-backdrop">
				<div class="mo_customer_validation-modal" tabindex="-1" role="dialog" id="mo_site_otp_choice_form">
					<div class="mo_customer_validation-modal-backdrop"></div>
					<div class="mo_customer_validation-modal-dialog mo_customer_validation-modal-md">
						<div class="login mo_customer_validation-modal-content">
							<div class="mo_customer_validation-modal-header">
								<b>Select Verification Type</b>
								<a class="close" href="#" onclick="mo_validation_goback();" ><?php printf( __( '&larr; Go Back' )); ?></a>
							</h3>
							</div>
							<div class="mo_customer_validation-modal-body center">
								<div><?php echo $message; ?></div><br /> 
								<?php if(!MO_Validation_Utility::mo_check_empty_or_null($user_email) || !MO_Validation_Utility::mo_check_empty_or_null($phone_number)){ ?>
								<div class="mo_customer_validation-login-container">
									<form name="f" method="post" action="">
										<input id="miniorange-validate-otp-choice-form" type='hidden' name="option" value='miniorange-validate-otp-choice-form' />
										<input type="radio" checked name="mo_customer_validation_otp_choice" value="user_email_verification" />Email Verification<br>
										<input type="radio" name="mo_customer_validation_otp_choice" value="user_phone_verification" />Phone Verification<br>
										<br /><input type="submit" name="miniorange_otp_token_user_choice" id="miniorange_otp_token_user_choice" class="miniorange_otp_token_submit"  value="Send OTP" />	
										<?php if(isset($_SESSION['event_registration'])){
											echo '<input type="hidden" name="reg_form" value="'.$_POST['reg_form'].'" />';
										    echo '<input type="hidden" name="questions" value="'.$_POST['questions'].'" />';
										    echo '<input type="hidden" name="action" value="post"/>';
										    echo '<input type="hidden" name="token" value="'.$_POST['token'].'" />';
										    echo '<input type="hidden" name="event_id" value="'.$_POST['event_id'].'" />';	
										    $i = 0;
										    while($i<count($_POST['attendee'])){
										    	echo ' <input type="hidden" name="attendee['.$i.'][first_name]" value="'.$_POST["attendee"][$i]["first_name"].'">';
										    	echo ' <input type="hidden" name="attendee['.$i.'][last_name]" value="'.$_POST["attendee"][$i]["last_name"].'">';
										    	$i++;
											}
										}elseif (isset($_SESSION['crf_user_registration'])) {
											foreach ($_REQUEST as $key => $value)
												if($key!='option' && $key!='mo_customer_validation_otp_token' && $key!='miniorange_otp_token_submit' && $key!='miniorange-validate-otp-choice-form' )
													echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
										}elseif(isset($_SESSION['woocommerce_registration'])){
												foreach ($_POST as $key => $value)
													if($key!='option' && $key!='mo_customer_validation_otp_token' && $key!='miniorange_otp_token_submit' && $key!="miniorange-validate-otp-choice-form")
														echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
										}elseif(isset($_SESSION['uultra_user_registration'])){
											foreach ($_POST as $key => $value) {
												if($key!='option' && $key!='mo_customer_validation_otp_token' && $key!='miniorange_otp_token_submit' && $key!='miniorange-validate-otp-choice-form')
													echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
											}
										}elseif(isset($_SESSION['upme_user_registration'])){
											foreach ($_POST as $key => $value) {
												if($key!='option' && $key!='mo_customer_validation_otp_token' && $key!='miniorange_otp_token_submit' && $key!='miniorange-validate-otp-choice-form')
													echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
											}
										}elseif(isset($_SESSION['pie_user_registration'])){
											foreach ($_POST as $key => $value) {
												if($key!='option' && $key!='mo_customer_validation_otp_token' && $key!='miniorange_otp_token_submit' && $key!='miniorange-validate-otp-choice-form')
													echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
											}
										}?>
									</form>
									<a href="http://miniorange.com/cloud-identity-broker-service" style="display:none;"></a>
									<a href="http://miniorange.com/strong_auth" style="display:none;"></a>
									<a href="http://miniorange.com/single-sign-on-sso" style="display:none;"></a>
									<a href="http://miniorange.com/fraud" style="display:none;"></a>
								</div>
								<?php }?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<form name="f" method="post" action="" id="validation_goBack_form">
				<input id="validation_goBack" name="option" value="validation_goBack" type="hidden"></input>
			</form>
			<style> .mo_customer_validation-modal{ display: block !important; } </style>
			<script>	
				function mo_validation_goback(){
					document.getElementById('validation_goBack_form').submit();
				}
			</script>
		</body>
    </html>
	<?php }*/

	function _handle_verification_resend_otp_action($otp_type){
		if (session_id() == '' || !isset($_SESSION)){ session_start(); }
		$user_email = $_SESSION['user_email'];
		$user_login  = $_SESSION['user_login'];
		$password = $_SESSION['user_password'];
		$phone_number = $_SESSION['phone_number_mo'];
		$extra_data = $_SESSION['extra_data'];
		
		if($otp_type=="phone"){
			$challenge_otp = new MO_Validation_Utility();
			$content = $challenge_otp->send_otp_token('SMS','',$phone_number);
			if(strcasecmp($content, '<code>200</code>') == 0) {
/*				$_SESSION['mo_customer_validation_site_txID'] = $content['txId'];
				if(get_option('mo_otp_plugin_version')>1.4){
						update_option('mo_customer_phone_transactions_remaining',get_option('mo_customer_phone_transactions_remaining')-1);
					}
				update_option('mo_otp_verification_phone_otp_count',get_option('mo_otp_verification_phone_otp_count') + 1);*/
				$message =  ' <b>' . $phone_number . ' </b> Numaralı hatta onay kodu tekrar gönderilmiştir. <br/><br/>Lütfen size gönderilen onay konudunu giriniz.';
				
				miniorange_site_otp_validation_form($user_login, $user_email,$phone_number,$message,$otp_type,$from_both);
				exit();
			}else{
				miniorange_site_otp_validation_form(null,null,null,"Sms gönderimi sırasında bir hata oldu, lütfen tekrar deneyin.",$otp_type,$from_both);
				exit();
			}
		}elseif($otp_type=="email"){
			$challenge_otp = new MO_Validation_Utility();
			$content = json_decode($challenge_otp->send_otp_token('EMAIL',$user_email), true);
			if(strcasecmp($content['status'], 'SUCCESS') == 0) {
						$_SESSION['mo_customer_validation_site_txID'] = $content['txId'];
						if(get_option('mo_otp_plugin_version')>1.4){
							update_option('mo_customer_email_transactions_remaining',get_option('mo_customer_email_transactions_remaining')-1);
						}
						update_option('mo_otp_verification_email_otp_count',get_option('mo_otp_verification_email_otp_count') + 1);
						 $message =  'Another One Time Passcode has been sent ( ' . get_option('mo_otp_verification_email_otp_count') . ' )  to <b>' . $user_email. ' </b><br/><br/>Please enter the OTP below to verify your Email Address. If you cannot see the email in your inbox, make sure to check your SPAM folder.';;
				miniorange_site_otp_validation_form($user_login, $user_email,$phone_number,$message,$otp_type,$from_both);
				exit();
			}else{
				miniorange_site_otp_validation_form(null,null,null,"There was an error in sending the OTP to the given Email Address. Please Try Again.",$otp_type,$from_both);
				exit();	
			}
		}elseif($otp_type=='both'){
			$message = "Please select one of the methods below to resend the OTP to:";
			miniorange_verification_user_choice($user_login, $user_email,$phone_number,$message,$otp_type);
			exit();
		}
	}

	function extra_post_data(){
		//die(var_dump($_SESSION));
		if(isset($_SESSION['event_registration'])){
			if($key!='option' && $key!='mo_customer_validation_otp_token' && $key!='miniorange_otp_token_submit' && $key!='miniorange-validate-otp-choice-form' )
				echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
		    $i = 0;
		    while($i<count($_POST['attendee'])){
		    	echo ' <input type="hidden" name="attendee['.$i.'][first_name]" value="'.$_POST["attendee"][$i]["first_name"].'">';
		    	echo ' <input type="hidden" name="attendee['.$i.'][last_name]" value="'.$_POST["attendee"][$i]["last_name"].'">';
		    	$i++;
			}
		}elseif (isset($_SESSION['crf_user_registration'])) {
			foreach ($_REQUEST as $key => $value)
				if($key!='option' && $key!='mo_customer_validation_otp_token' && $key!='miniorange_otp_token_submit' && $key!='miniorange-validate-otp-choice-form' )
					echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
		}elseif(isset($_SESSION['woocommerce_registration'])){
			foreach ($_POST as $key => $value)
				if($key!='option' && $key!='mo_customer_validation_otp_token' && $key!='miniorange_otp_token_submit' && $key!='miniorange-validate-otp-choice-form')
					echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
				if (isset($_REQUEST['g-recaptcha-response']))
					 echo '<input type="hidden" name="g-recaptcha-response" value="'.$_POST['g-recaptcha-response'].'" />';
		}elseif(isset($_SESSION['uultra_user_registration'])){
			foreach ($_POST as $key => $value) {
				if($key!='option' && $key!='mo_customer_validation_otp_token' && $key!='miniorange_otp_token_submit' && $key!='miniorange-validate-otp-choice-form')
					echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
			}
		}elseif(isset($_SESSION['upme_user_registration'])){
			foreach ($_POST as $key => $value) {
				if($key!='option' && $key!='mo_customer_validation_otp_token' && $key!='miniorange_otp_token_submit' && $key!='miniorange-validate-otp-choice-form')
					echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
			}
		}elseif(isset($_SESSION['pie_user_registration'])){
			foreach ($_POST as $key => $value) {
				if($key!='option' && $key!='mo_customer_validation_otp_token' && $key!='miniorange_otp_token_submit' && $key!='miniorange-validate-otp-choice-form')
					echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
			}
		}elseif(isset($_SESSION['profileBuilder_registration'])){
			foreach ($_POST as $key => $value) {
				if($key!='option' && $key!='mo_customer_validation_otp_token' && $key!='miniorange_otp_token_submit' && $key!='miniorange-validate-otp-choice-form')
					echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
			}
		}elseif(isset($_SESSION['default_wp_registration'])){
			
			foreach ($_POST as $key => $value) {
				
				if($key!='user_login'&&$key!="user_email"&&$key!='register_nonce'&&$key!='option')
					echo '<input type="hidden" name="'.$key.'" value="'.$value.'" />';
			}
		}
	}

	function _handle_validation_goBack_action(){
		if (session_id() == '' || !isset($_SESSION)){ @session_start(); }
		$url = isset($_SESSION['current_url'])? $_SESSION['current_url'] : '';
		session_unset();
		wp_redirect($url);
		exit();
	}
	
	function _handle_validation_form_action($otp_type,$from_both=false){

		if(session_id() == '' || !isset($_SESSION)){ session_start(); }
		$user_login = !MO_Validation_Utility::mo_check_empty_or_null($_SESSION['user_login']) ? $_SESSION['user_login'] : null;
		$user_email = !MO_Validation_Utility::mo_check_empty_or_null($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
		$phone_number = !MO_Validation_Utility::mo_check_empty_or_null($_SESSION['phone_number_mo']) ? $_SESSION['phone_number_mo'] : null;
		$password = !MO_Validation_Utility::mo_check_empty_or_null($_SESSION['user_password']) ? $_SESSION['user_password'] : null;
		$extra_data = !MO_Validation_Utility::mo_check_empty_or_null($_SESSION['extra_data']) ? $_SESSION['extra_data'] : null;
		$validate_otp = new MO_Validation_Utility();
		$txID = '1';
		$otp_token = !MO_Validation_Utility::mo_check_empty_or_null($_POST['mo_customer_validation_otp_token']) ? $_POST['mo_customer_validation_otp_token'] : null;
		
		if(!is_null($txID)){
			
			$content = $validate_otp->validate_otp_token($phone_number, $otp_token);
			
			if(strcasecmp($content[1], 'SUCCESS') == 0) { //OTP validated
				$_SESSION['phone_number_mo'] = $phone_number;
				
				if(isset($_SESSION['woocommerce_registration']))
					register_woocommerce_user($user_login,$user_email,$password,$phone_number);
				elseif (isset($_SESSION['profileBuilder_registration']))
					return;
				elseif (isset($_SESSION['ultimate_members_registration']))
					register_ultimateMember_user($user_login,$user_email,$password,$phone_number,$extra_data);
				elseif (isset($_SESSION['event_registration']))
					session_unset();
				elseif (isset($_SESSION['crf_user_registration']))
					session_unset();
				elseif (isset($_SESSION['simplr_registration']))
					register_simplr_user($user_login,$user_email,$password,$phone_number,$extra_data);
				elseif (isset($_SESSION['buddyPress_user_registration']))
					signup_buddyPress_user($user_login,$user_email,$password,$phone_number,$extra_data);
				elseif(isset($_SESSION['uultra_user_registration']))
					session_unset();
				elseif(isset($_SESSION['woocommerce_checkout_page']))
					session_unset();
				elseif(isset($_SESSION['upme_user_registration']))
					session_unset();
				elseif(isset($_SESSION['pie_user_registration'])){
					$_SESSION['pie_user_registration_status']='validated';
					
				}elseif (isset($_SESSION['default_wp_registration'])){	
					session_unset('default_wp_registration');
					$errors = register_new_user($user_login, $user_email);
					if ( !is_wp_error($errors) ) {
						$redirect_to = !empty( $_POST['redirect_to'] ) ? $_POST['redirect_to'] :  wp_login_url()."?checkemail=registered";
						wp_redirect( $redirect_to );
						exit();
					}
				}
			}else{
				$message = $content[0];
				miniorange_site_otp_validation_form(null,null,null,$message,$otp_type,$from_both);
				exit();			
			}
		}
	}
	
	function _handle_validate_otp_choice_form($postdata){
		if (session_id() == '' || !isset($_SESSION)){ session_start(); }
			if($postdata['mo_customer_validation_otp_choice'] == 'user_email_verification')
				miniorange_site_challenge_otp($_SESSION['user_login'],$_SESSION['user_email'],null,$_SESSION['phone_number_mo'],"email",$_SESSION['user_password'],$_SESSION['extra_data'],true);
			else
				miniorange_site_challenge_otp($_SESSION['user_login'],$_SESSION['user_email'],null,$_SESSION['phone_number_mo'],"phone",$_SESSION['user_password'],$_SESSION['extra_data'],true);
	}

	/* DEFAULT WORDPRESS REGISTRATION PAGE FUNCTIONS*/
	function miniorange_registration_save($user_id){
		
		if ( isset( $_SESSION['phone_number_mo'] ) ){
			
			add_user_meta($user_id, 'telephone', $_SESSION['phone_number_mo']);
		}
		session_unset();
		
	}

	function miniorange_site_registration_errors($errors, $sanitized_user_login, $user_email ) {
		//die(var_dump($_POST));
		
		$phone_number =$_POST['phone_number_mo'];
				
			if ( ! isset( $phone_number ) || empty( $phone_number ) ) {
				
            	$errors->add( 'phone_number_error', __( '<strong>HATA</strong>: Lütfen telefon numaranızı girin.', 'mydomain' ) );
        	}

		if (session_id() == '' || !isset($_SESSION)) { session_start(); }
		MO_Validation_Utility::mo_check_empty_or_null(array_filter($errors->errors));
				
		if(get_option('mo_customer_validation_wp_default_enable') && MO_Validation_Utility::mo_check_empty_or_null(array_filter($errors->errors)) 
				&& isset($_POST['register_nonce'])){
				$_SESSION['default_wp_registration']=true;
				
				
				$errors = miniorange_site_challenge_otp($sanitized_user_login, $_POST['user_email'], $errors,$phone_number,"phone");
		}

		//die('<pre>'.var_export($errors).'</pre>');
		return $errors;
	}

	/* WOOCOMMERCE REGISTRATION PAGE FUNCTIONS*/
	function woocommerce_site_registration_errors($errors,$username,$password,$email){
		if(session_id() == '' || !isset($_SESSION)){ session_start(); }
		if(get_option('mo_customer_validation_wc_default_enable') && MO_Validation_Utility::mo_check_empty_or_null(array_filter($errors->errors))){
			$_SESSION['woocommerce_registration'] = true;
			$pattern = '/^[\+]\d{1,2}\d{8,11}$|^[\+]\d{1,2}[\s]\d{8,11}$/';

			if( get_option( 'woocommerce_registration_generate_username' )==='no' ){
				if (  MO_Validation_Utility::mo_check_empty_or_null( $username ) || ! validate_username( $username ) )
					return new WP_Error( 'registration-error-invalid-username', __( 'Please enter a valid account username.', 'woocommerce' ) );
				if ( username_exists( $username ) )
					return new WP_Error( 'registration-error-username-exists', __( 'An account is already registered with that username. Please choose another.', 'woocommerce' ) );
			}

			if( get_option( 'woocommerce_registration_generate_password' )==='no' ){
				if (  MO_Validation_Utility::mo_check_empty_or_null( $password ) )
					return new WP_Error( 'registration-error-invalid-password', __( 'Please enter a valid account password.', 'woocommerce' ) );
			}

			if ( MO_Validation_Utility::mo_check_empty_or_null( $email ) || ! is_email( $email ) )
				return new WP_Error( 'registration-error-invalid-email', __( 'Please enter a valid email address.', 'woocommerce' ) );
			if ( email_exists( $email ) )
				return new WP_Error( 'registration-error-email-exists', __( 'An account is already registered with your email address. Please login.', 'woocommerce' ) );

			do_action( 'woocommerce_register_post', $username, $email, $errors );
			if($errors->get_error_code())
				throw new Exception( $errors->get_error_message() );

			if(get_option('mo_customer_validation_wc_enable_type')=="mo_wc_phone_enable"){
				if ( MO_Validation_Utility::mo_check_empty_or_null( $_POST['billing_phone'] ) )
					return new WP_Error( 'billing_phone_error', __( 'Please enter a valid phone number.', 'woocommerce' ) );
				preg_match($pattern,$_POST['billing_phone'],$matches);
				if ( MO_Validation_Utility::mo_check_empty_or_null($matches))
					return new WP_Error( 'billing_phone_error', __( 'Please Enter a Valid Phone Number. E.g:+1XXXXXXXXXX', 'woocommerce' ) );
				$errors = miniorange_site_challenge_otp($username,$email,$errors,$_POST['billing_phone'],"phone",$password);
			}else if(get_option('mo_customer_validation_wc_enable_type')=="mo_wc_email_enable"){
				$errors = miniorange_site_challenge_otp($username,$email,$errors,$_POST['billing_phone'],"email",$password);
			}else if(get_option('mo_customer_validation_wc_enable_type')=="mo_wc_both_enable"){
				if ( MO_Validation_Utility::mo_check_empty_or_null( $_POST['billing_phone'] ) )
					return new WP_Error( 'billing_phone_error', __( '<strong>Error</strong>: Phone is required!.', 'woocommerce' ) );
				preg_match($pattern,$_POST['billing_phone'],$matches);
				if ( MO_Validation_Utility::mo_check_empty_or_null($matches))
					return new WP_Error( 'billing_phone_error', __( 'Please Enter a Valid Phone Number. E.g:+1XXXXXXXXXX', 'woocommerce' ) );
				$errors = miniorange_site_challenge_otp($username,$email,$errors,$_POST['billing_phone'],"both",$password);
			}
		}	
		return $errors; 
	}

	function register_woocommerce_user($username,$email,$password,$phone_number){
		$new_customer = wc_create_new_customer( sanitize_email( $email ), wc_clean( $username ), $password );
		
		if ( is_wp_error( $new_customer ) ) {
			wc_add_notice( $new_customer->get_error_message(), 'error' );
		}
		if ( apply_filters( 'woocommerce_registration_auth_new_customer', true, $new_customer ) ) 
			wc_set_customer_auth_cookie( $new_customer );

		if(isset($_POST['billing_phone']))
			update_user_meta( $new_customer, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
		
		//wp_safe_redirect( apply_filters( 'woocommerce_registration_redirect', wp_get_referer() ? wp_get_referer() : wc_get_page_permalink('myaccount')));
		session_unset();
		//wp_redirect( site_url()."/".get_page_uri( get_page_by_title( get_option('mo_customer_validation_wc_redirect') )->ID) ."/" );
		wp_redirect(get_permalink( get_page_by_title( get_option('mo_customer_validation_wc_redirect') )->ID));
		exit;
	} 

	function mo_add_phone_field(){
		if(get_option('mo_customer_validation_wc_enable_type')=="mo_wc_phone_enable" || get_option('mo_customer_validation_wc_enable_type')==='mo_wc_both_enable'){
	?>
		<p class="form-row form-row-wide">
		<label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php if ( ! empty( $_POST['billing_phone'] ) ) esc_attr_e( $_POST['billing_phone'] ); ?>" />
		</p>
	<?php
		}
	}

	/* WOOCOMMERCE CHECKOUT FORM FUNCTIONS */
	function _handle_woocommere_checkout_form($getdata){
		if(session_id() == '' || !isset($_SESSION)){ session_start(); }
		$result = array();
		$_SESSION['woocommerce_checkout_page'] = 'true';
		if(get_option('mo_customer_validation_wc_checkout_enable')){
			if(get_option('mo_customer_validation_wc_checkout_type')=="mo_wc_phone_enable")
				miniorange_site_challenge_otp('test',$getdata['user_email'],null,'+'.trim($getdata['user_phone']),"phone");
			else
				miniorange_site_challenge_otp('test',$getdata['user_email'],null,$getdata['user_email'],"email");
		}
	}

	function my_custom_checkout_field( $checkout ) {
		if((get_option('mo_customer_validation_wc_checkout_guest') && is_user_logged_in())){ return; }
			echo '<div id="woocommerce-shipping-fields"><h3>' . __('User Verification') . '</h3>';
			if(!get_option('mo_customer_validation_wc_checkout_button')){
				if(get_option('mo_customer_validation_wc_checkout_type')=="mo_wc_phone_enable"){
					echo '<div title="Please Enter a Phone Number to enable this link"><a href="#" ';
					echo 'style="text-align:center;color:grey;pointer-events:none;" ';
					echo 'id="miniorange_otp_token_submit" class="" >'.__("[ Click here to verify your Phone ]").'</a></div>';
				}else{
					echo '<div title="Please Enter an Email Address to enable this link"><a href="#" ';
					echo 'style="text-align:center;color:grey;pointer-events:none;" ';
					echo 'id="miniorange_otp_token_submit" class="" >'.__("[ Click here to verify your Email ]").'</a></div>';
				}
			}else{
				if(get_option('mo_customer_validation_wc_checkout_type')=="mo_wc_phone_enable"){
					echo '<input type="button" class="button alt" style="width: 100%;" id="miniorange_otp_token_submit" disabled title="Please Enter a Phone Number to enable this." value="Click here to verify your Phone"></input>';
				}
				else{
					echo '<input type="button" class="button alt" style="width: 100%;" id="miniorange_otp_token_submit" disabled title="Please Enter an Email Address to enable this." value="Click here to verify your Email"></input>';
				}
			}
			
			echo '<div id="mo_message" hidden></div>';

			woocommerce_form_field( 'order_verify', array(
	        'type'          => 'text',
	        'class'         => array('form-row-wide'),
	        'label'         => __('Verify Code'),
	        'required'  	=> true,
	        'placeholder'   => __('Enter Verification Code'),
	        ), $checkout->get_value( 'order_verify' ));

	        echo '<script> jQuery(document).ready(function() { $ = jQuery,';
	        echo '$(".woocommerce-message").length>0&&($("#order_verify").focus(),$("#mo_message").addClass("woocommerce-message"),$("#mo_message").show());';
			if(!get_option('mo_customer_validation_wc_checkout_button')){
			   	if(get_option('mo_customer_validation_wc_checkout_type')=="mo_wc_phone_enable"){
				    echo '""!=$("input[name=billing_phone]").val()&&$("#miniorange_otp_token_submit").removeAttr("style");';
				    echo '$("input[name=billing_phone]").change(function(){if($("input[name=billing_phone]").val()!=""){$("#miniorange_otp_token_submit").removeAttr("style");}else{$("#miniorange_otp_token_submit").css({"color":"grey","pointer-events":"none"}); }})';
				}else{
					echo '""!=$("input[name=billing_email]").val()&&$("#miniorange_otp_token_submit").removeAttr("style");';
					echo '$("input[name=billing_email]").change(function(){if($("input[name=billing_email]").val()!=""){$("#miniorange_otp_token_submit").removeAttr("style");}else{$("#miniorange_otp_token_submit").css({"color":"grey","pointer-events":"none"}); }})';
				}
			}else{
				if(get_option('mo_customer_validation_wc_checkout_type')=="mo_wc_phone_enable"){
				    echo '""!=$("input[name=billing_phone]").val()&&$("#miniorange_otp_token_submit").prop( "disabled", false );';
				    echo ' $("input[name=billing_phone]").change(function() {if ($("input[name=billing_phone]").val() != "") {$("#miniorange_otp_token_submit").prop( "disabled", false );} else { $("#miniorange_otp_token_submit").prop( "disabled", true ); }})';
				}else{
					echo '""!=$("input[name=billing_email]").val()&&$("#miniorange_otp_token_submit").prop( "disabled", false );';
					echo ' $("input[name=billing_email]").change(function() {if ($("input[name=billing_email]").val() != "") {$("#miniorange_otp_token_submit").prop( "disabled", false );} else { $("#miniorange_otp_token_submit").prop( "disabled", true ); }})';
				}
			}
			//echo ' ,$("#order_verify").focus() ';
			echo ',$(".woocommerce-error").length>0&&$("html, body").animate({scrollTop:$("div.woocommerce").offset().top-50},1e3),$("#miniorange_otp_token_submit").click(function(o){var e=$("input[name=billing_email]").val(),n=$("input[name=billing_phone]").val(),a=$("div.woocommerce");a.addClass("processing").block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),$.ajax({url:"'.site_url().'",type:"GET",data:"option=miniorange-woocommerce-checkout&user_email="+e+"&user_phone="+n,crossDomain:!0,dataType:"json",contentType:"application/json; charset=utf-8",success:function(o){ if(o.result=="success"){$(".blockUI").hide(),$("#mo_message").empty(),$("#mo_message").append(o.message),$("#mo_message").addClass("woocommerce-message"),$("#mo_message").show(),$("#order_verify").focus()}else{$(".blockUI").hide(),$("#mo_message").empty(),$("#mo_message").append(o.message),$("#mo_message").addClass("woocommerce-error"),$("#mo_message").show();} ;},error:function(o,e,n){}}),o.preventDefault()});});</script>';
			echo '</div>';
	}

	function my_custom_checkout_field_process() {
		if(session_id() == '' || !isset($_SESSION)){ session_start(); }
		if((get_option('mo_customer_validation_wc_checkout_guest') && is_user_logged_in())){ return; }
			if(isset($_SESSION['woocommerce_checkout_page'])){
				$validate_otp = new MO_Validation_Utility();
				if ( ! $_POST['order_verify'] )
						if(get_option('mo_customer_validation_wc_checkout_type')=="mo_wc_phone_enable")
							wc_add_notice( __( 'Please enter the verification sent to your phone' ), 'error' );
						else
							wc_add_notice( __( 'Please enter the verification sent to your email address' ), 'error' );
				else{
					$content = json_decode($validate_otp->validate_otp_token($_SESSION['mo_customer_validation_site_txID'], $_POST['order_verify']),true);
					if(strcasecmp($content['status'], 'SUCCESS') != 0) { 
						wc_add_notice( __( 'Invalid OTP Entered' ), 'error' );
					}else{
						session_unset($_SESSION['mo_customer_validation_site_txID']);
						session_unset($_SESSION['woocommerce_checkout_page']);
					}
				}
			}else{
				wc_add_notice( __( '<strong>Verify Code</strong> is a required field' ), 'error' );
			}
	}

	
	/* REGISTER PROFILE BUILDER PAGE FUNCTIONS*/
	function formbuilder_site_registration_errors($userdata,$global_request){
		
		if(session_id() == '' || !isset($_SESSION)){ session_start(); }
		$errors = new WP_Error();
		if($global_request['action']=='register' && get_option('mo_customer_validation_pb_default_enable') 
				&& !$_SESSION['profileBuilder_registration']){
			$_SESSION['profileBuilder_registration'] = true;
			$_SESSION['global_request'] = $global_request;
			//die(var_dump($userdata));
			foreach ($userdata as $key => $value) {
				if($key=="user_login"){
					$username = $value;	
				}elseif ($key=="nickname") {
					$phone = $value;
				}elseif ($key=="user_pass") {
					$password = $value;
				}else{
					$extra_data[$key]=$value;
				}
			}
			$errors = miniorange_site_challenge_otp($username,$email,$errors,$phone,"phone",$password,$extra_data);
		}else{
			session_unset();
			return $userdata;
		}
	}

    function miniorange_message_formbuilder_override($message){
    	
    	if(session_id() == '' || !isset($_SESSION)){ @session_start(); }
    	if(MO_Validation_Utility::mo_check_empty_or_null($message) && isset($_SESSION['profileBuilder_registration'])){
	    	session_unset();
	    	return;
    	}else{
    		return;
    	}
    }

    /*	SIMPLR REGISTRATION PAGE FUNCTIONS*/
    function simplr_site_registration_errors($errors){
		if(session_id() == '' || !isset($_SESSION)){ session_start(); }
		if(MO_Validation_Utility::mo_check_empty_or_null($errors) && !isset($_POST['fbuser_id']) 
				&& get_option('mo_customer_validation_simplr_default_enable')){
			$_SESSION['simplr_registration'] = true;
			$phone_number = null;
			foreach ($_POST as $key => $value) {
				if($key=="username"){
					$username = $value;
				}elseif ($key=="email") {
					$email = $value;
				}elseif ($key=="password") {
					$password = $value;
				}elseif ($key==get_option('mo_customer_validation_simplr_field_key')){
					$number = preg_replace('/\D/','',$value);
					$number = substr($number, -10);
					if(!MO_Validation_Utility::mo_check_empty_or_null($number)){
						$phone_number = $number;
					}else{
						$errors[].=__("Lütfen sadece rakamlardan oluşan numaranızı yazın.",'simplr-registration-form');
						add_filter($key.'_error_class','_sreg_return_error');
						return $errors;
					}
				}else{
					$extra_data[$key]=$value;
				}
			}
			if(get_option('mo_customer_validation_simplr_enable_type')=="mo_phone_enable")
				$errors = miniorange_site_challenge_otp($username,$email,$errors,$phone_number,"phone",$password,$extra_data);
			else if(get_option('mo_customer_validation_simplr_enable_type')=="mo_both_enable")
				$errors = miniorange_site_challenge_otp($username,$email,$errors,$phone_number,"both",$password,$extra_data);
			else
				$errors = miniorange_site_challenge_otp($username,$email,$errors,$phone_number,"email",$password,$extra_data);
		}	
		return $errors; 
	}

    function register_simplr_user($user_login,$user_email,$password,$phone_number,$extra_data){
    	$data = Array(); 
    	global $sreg,$simplr_options;
    	if( !$sreg ) { $sreg = new stdClass; }
    	$data['username'] = $user_login;
    	$data['email'] = $user_email;
    	$data['password'] = $password;
    	if(get_option('mo_customer_validation_simplr_field_key'))
    		$data[get_option('mo_customer_validation_simplr_field_key')] = $phone_number;
    	$data = array_merge($data,$extra_data);
    	$atts = $extra_data['atts'];
    	$sreg->output = simplr_setup_user($atts,$data);
    	if(MO_Validation_Utility::mo_check_empty_or_null($sreg->errors)) {
	    	if( isset($atts['thanks']) ) {
				$page = get_permalink($atts['thanks']);
				session_unset();
				wp_redirect($page);
				exit();
			}elseif( !MO_Validation_Utility::mo_check_empty_or_null($simplr_options->thank_you) ) {
				$page = get_permalink($simplr_options->thank_you);
				session_unset();
				wp_redirect($page);
				exit();
			}else {
				session_unset();
				$sreg->success = $sreg->output;
			}
		}
    }
	
	/* ULTIMATE MEMBER PAGE FUNCTIONS*/
	function miniorange_um_user_registration($args){
		if(session_id() == '' || !isset($_SESSION)){ session_start(); }
		$errors = new WP_Error();
		$_SESSION['ultimate_members_registration'] = true;
		$phone_number = null;
		
		if(get_option('mo_customer_validation_um_default_enable')){
			
			foreach ($args as $key => $value) {
					if($key=="user_login"){
						$username = $value;
					}elseif ($key=="user_email") {
						$email = $value;
					}elseif ($key=="user_password") {
						$password = $value;
					}elseif ($key == 'mobile_number'){
							$phone_number = $value;
					}elseif (!isset($phone_nuber)&&empty($phone_number)&&$key == 'phone_number') {
							$phone_number = $value;
					}else{
						$extra_data[$key]=$value;
					}
			}
			if(get_option('mo_customer_validation_um_enable_type')=="mo_um_phone_enable")	
				$errors = miniorange_site_challenge_otp($username,$email,$errors,$phone_number,"phone",$password,$extra_data);
			else if(get_option('mo_customer_validation_um_enable_type')=="mo_um_both_enable")
				$errors = miniorange_site_challenge_otp($username,$email,$errors,$phone_number,"both",$password,$extra_data);
			else
				$errors = miniorange_site_challenge_otp($username,$email,$errors,$phone_number,"email",$password,$extra_data);
		}else{
			return $args;
		}
	}

	function miniorange_um_phone_validation($args){
		global $ultimatemember;
		foreach ($args as $key => $value) {
			if ($key == 'mobile_number'){
				$number = preg_replace('/\D/','',$value);
				$number = substr($number, -10);
				if(MO_Validation_Utility::mo_check_empty_or_null($number)){
					$ultimatemember->form->add_error($key, __('Lütfen sadece rakamlardan oluşan numaranızı yazın.','ultimatemember') );
				}
			}
		}
	}

	function register_ultimateMember_user($user_login,$user_email,$password,$phone_number,$extra_data){
		$args = Array();
		$args['user_login'] = $user_login;
		$args['user_email'] = $user_email;
		$args['user_password'] = $user_password;
		$args = array_merge($args,$extra_data);
		$user_id = wp_create_user( $user_login,$password, $user_email );
		session_unset();
		do_action('um_after_new_user_register', $user_id, $args);
	}
	
	/* EVENT REGISTRATION USER FUNCTIONS */
	function miniorange_evr_user_registration($reg_form){
		$errors = new WP_Error();
		$event_form_data = Array();
		$phone_number = null;
		if(session_id() == '' || !isset($_SESSION)){ session_start(); }
		if($_POST['option']!="miniorange-validate-otp-form" && get_option('mo_customer_validation_event_default_enable')){
			$_SESSION['event_registration'] = true;
			if(get_option('mo_customer_validation_event_enable_type')=="mo_event_phone_enable")
				$errors = miniorange_site_challenge_otp($reg_form['fname'],$reg_form['email'],$errors,$reg_form['phone'],'phone');
			else if(get_option('mo_customer_validation_event_enable_type')=="mo_event_both_enable")
				$errors = miniorange_site_challenge_otp($reg_form['fname'],$reg_form['email'],$errors,$reg_form['phone'],'both');
			else
				$errors = miniorange_site_challenge_otp($reg_form['fname'],$reg_form['email'],$errors,$reg_form['phone'],'email');
		}
	}	

	/* BUDDYPRESS REGISTRATION USER FUNCTIONS */
	function miniorange_bp_user_registration($usermeta){
		if(session_id() == '' || !isset($_SESSION)){ session_start(); }
		$_SESSION['buddyPress_user_registration'] = true;
		if(get_option('mo_customer_validation_bbp_default_enable')){
			$errors = new WP_Error();
			$phone_number = null;
			foreach ($_POST as $key => $value) {
					if($key=="signup_username"){
						$username = $value;
					}elseif ($key=="signup_email") {
						$email = $value;
					}elseif ($key=="signup_password") {
						$password = $value;
					}else{
						$extra_data[$key]=$value;
					}
			}
			global $wpdb;
			$bp_xprofile_fields =$wpdb->prefix."bp_xprofile_fields";
			$extra_data['usermeta'] = $usermeta;
			$reg1 = $wpdb->get_results("SELECT id FROM $bp_xprofile_fields where name ='".get_option('mo_customer_validation_bbp_phone_key')."'");
			foreach($reg1 as $row1){
				$field_key = "field_".$row1->id;
				if(isset($_POST[$field_key])){
					$phone_number = $_POST[$field_key]; 
					break;
				}
			}
			if(get_option('mo_customer_validation_bbp_enable_type')=="mo_bbp_phone_enable")
				$errors = miniorange_site_challenge_otp($username,$email,$errors,$phone_number,'phone',$password,$extra_data);
			else if(get_option('mo_customer_validation_bbp_enable_type')=="mo_bbp_both_enable")
				$errors = miniorange_site_challenge_otp($username,$email,$errors,$phone_number,'both',$password,$extra_data);
			else
				$errors = miniorange_site_challenge_otp($username,$email,$errors,$phone_number,'email',$password,$extra_data);
		}else{
			return $usermeta;
		}
	}

	function signup_buddyPress_user($user_login,$user_email,$password,$phone_number,$extra_data){
		if ( isset( $extra_data['signup_with_blog'] ) && is_multisite() )
			$wp_user_id = bp_core_signup_blog( $extra_data['domain'], $extra_data['path'], $extra_data['blog_title'], $user_login, $user_email, $extra_data['usermeta'] );
		else
			$wp_user_id = bp_core_signup_user( $user_login, $password, $user_email, $extra_data['usermeta'] );
		if ( is_wp_error( $wp_user_id ) ) 
			$_SESSION['buddyPress_user_registration'] = 'error';
		else
			$_SESSION['buddyPress_user_registration'] = 'completed';
		do_action( 'bp_complete_signup');
		bp_core_load_template( apply_filters( 'bp_core_template_register', array( 'register', 'registration/register' ) ) );
	}

	function miniorange_check_registration_status(){
		if(session_id() == '' || !isset($_SESSION)){ session_start(); }
		if(isset($_SESSION['buddyPress_user_registration']) && $_SESSION['buddyPress_user_registration']=="completed"
			 && get_option('mo_customer_validation_bbp_default_enable')){
			buddypress()->signup->step = 'completed-confirmation';
			session_unset();
		}else if(isset($_SESSION['buddyPress_user_registration']) && $_SESSION['buddyPress_user_registration']=="error"){
			buddypress()->signup->step = 'request-details';
			bp_core_add_message( $wp_user_id->get_error_message(), 'error' );
			session_unset();
		}
	}

	/*REGISTRATION MAGIC USER FUNCTIONCS*/
	function _handle_crf_form_submit($requestdata){
		global $wpdb;
		$crf_fields =$wpdb->prefix."crf_fields";
		$reg1 = $wpdb->get_results("SELECT * FROM $crf_fields where Name ='".get_option('mo_customer_validation_crf_email_key')."'");
		die(var_dump($reg1));
		foreach($reg1 as $row1){
			$email = sanitize_key($row1->Name).'_'.$row1->Id;
			if(isset($requestdata[$email]))
				break;
		}

		$reg1 = $wpdb->get_results("SELECT * FROM $crf_fields where Name ='".get_option('mo_customer_validation_crf_phone_key')."'");
		foreach($reg1 as $row1){
			$phone = sanitize_key($row1->Name).'_'.$row1->Id;
			if(isset($requestdata[$phone]))
				break;
		}
		if(!MO_Validation_Utility::mo_check_empty_or_null($email) || !MO_Validation_Utility::mo_check_empty_or_null($phone) ){
			if(isset($requestdata['user_name']))
				miniorange_crf_user($requestdata[$email],$requestdata['user_name'],$requestdata[$phone]);
			else
				miniorange_crf_user($requestdata[$email],null,$requestdata[$phone]);
		}
	}

	function miniorange_crf_user($user_email,$user_name,$phone_number){
		die('asdadssda');
		if(session_id() == '' || !isset($_SESSION)){ session_start(); }
		$_SESSION['crf_user_registration'] = true;
		$errors = new WP_Error();
		if(get_option('mo_customer_validation_crf_enable_type')=="mo_crf_phone_enable")
			$errors = miniorange_site_challenge_otp($user_name,$user_email,$errors,$phone_number,"phone");
		else if(get_option('mo_customer_validation_crf_enable_type')=="mo_crf_both_enable")
			$errors = miniorange_site_challenge_otp($user_name,$user_email,$errors,$phone_number,"both");
		else
			$errors = miniorange_site_challenge_otp($user_name,$user_email,$errors,$phone_number,"email");
	}

	/*USER ULTRA FORM FUNCTIONS*/
	function _handle_uultra_form_submit($user_name,$user_email,$phone_number){
			
			$errors = new WP_Error();
			$test = new XooUserRegister;
			$test->uultra_prepare_request( $_POST );
			$test->uultra_handle_errors();
			if(!isset($test->errors)){
				if(session_id() == '' || !isset($_SESSION)){ session_start(); }
				$_SESSION['uultra_user_registration'] = true;
				 
			$fields = get_option('usersultra_profile_fields');
			$keys = array_keys($fields);
			foreach($keys as $key){
				 $newkeys =  array_keys($fields[$key]);
				 foreach($newkeys as $newkey){
									 
					 if($fields[$key][$newkey] == get_option('mo_customer_validation_uultra_phone_key')){
						 $phone = $fields[$key][$newkey];
						 break 2;
					 }
				 }
				  
                }
				if(get_option('mo_customer_validation_uultra_enable_type')=="mo_uultra_phone_enable")
					$errors = miniorange_site_challenge_otp($user_name,$user_email,$errors,$_POST[$phone],"phone");
				else if(get_option('mo_customer_validation_uultra_enable_type')=="mo_uultra_both_enable")
					$errors = miniorange_site_challenge_otp($user_name,$user_email,$errors,$_POST[$phone],"both");
				else
					$errors = miniorange_site_challenge_otp($user_name,$user_email,$errors,$_POST[$phone],"email");
			}
	}

	/*REGISTRATION USER PROFILE MADE EASY*/
	function _handle_upme_form_submit($POSTED){
		$mobile_number = null;
		foreach($POSTED as $key => $value){
			if($key == get_option('mo_customer_validation_upme_phone_key')){
				$mobile_number = $value;
				break;
			}
		}
		miniorange_upme_user($_POST['user_login'],$_POST['user_email'],$mobile_number);
	}

	function miniorange_upme_user($user_name,$user_email,$phone_number){
		$miniorange_upme = new UPME_Register();
		$miniorange_upme->prepare($_POST);
		$miniorange_upme->handle();
		if(!isset($miniorange_upme->errors)){
				if(session_id() == '' || !isset($_SESSION)){ session_start(); }
				$_SESSION['upme_user_registration'] = true;
				if(get_option('mo_customer_validation_upme_enable_type')=="mo_upme_phone_enable")
					$errors = miniorange_site_challenge_otp($user_name,$user_email,$errors,$phone_number,"phone");
				else if(get_option('mo_customer_validation_upme_enable_type')=="mo_upme_both_enable")
					$errors = miniorange_site_challenge_otp($user_name,$user_email,$errors,$phone_number,"both");
				else
					$errors = miniorange_site_challenge_otp($user_name,$user_email,$errors,$phone_number,"email");
			}
 	}
	
	/*REGISTRATION USING PIE REGISTRATION FORM*/
	function miniorange_pie_user_registration(){
		if(session_id() == '' || !isset($_SESSION)){ session_start(); }
		
		if (isset($_SESSION['pie_user_registration_status']) && $_SESSION['pie_user_registration_status']=='validated'){
				update_option('pie_user_registraion','completed');
				
		}else if(get_option('pie_user_registraion')!=='completed'){
			
			$fields = unserialize(get_option('pie_fields'));
			$keys = array_keys($fields);
			
			foreach($keys as $key){
				
				if($fields[$key]['label'] == get_option('mo_customer_validation_pie_phone_key')){
						$phone = str_replace("-","_",sanitize_title($fields[$key]['type']."_".(isset($fields[$key]['id'])?$fields[$key]['id']:"")));    
						
						break;
					 }
				 }
			$_SESSION['pie_user_registration'] = true;
			$errors = new WP_Error();
			$phone_number = null;
			
			if(get_option('mo_customer_validation_pie_enable_type')=="mo_pie_phone_enable")
				$errors = miniorange_site_challenge_otp( $_POST['username'],$_POST['e_mail'],$errors,$_POST[$phone],"phone");
			else if(get_option('mo_customer_validation_pie_enable_type')=="mo_pie_both_enable")
				$errors = miniorange_site_challenge_otp( $_POST['username'],$_POST['e_mail'],$errors,$_POST[$phone],"both");
			else
				$errors = miniorange_site_challenge_otp( $_POST['username'],$_POST['e_mail'],$errors,$_POST[$phone],"email");
		}else{
				delete_option('pie_user_registraion');
				return;
			}
	}

	/* CF7 CONTACT FORM FUNCTIONS */
	function _handle_cf7_contact_form($getdata){
		if(session_id() == '' || !isset($_SESSION)){ session_start(); }
			$_SESSION['cf7_contact_page'] = 'true';
			if(get_option('mo_customer_validation_cf7_contact_enable')){
				if(!MO_Validation_Utility::mo_check_empty_or_null($getdata['user_phone'])){
					$_SESSION['cf7_phone_verified'] = '+'.trim($getdata['user_phone']);
					die('asdadsdads');
					miniorange_site_challenge_otp('test','',null,'+'.trim($getdata['user_phone']),"phone");
				}else{
					if(get_option('mo_customer_validation_cf7_contact_type')=="mo_cf7_contact_phone_enable")
						$result['message']='You will have to provide a Phone Number before you can verify it.';
					else
						$result['message']='You will have to provide an Email Address before you can verify it.';
					$result['result'] = 'error';
					wp_send_json( $result );
				}
			}
	}

	function miniorange_cf7_text_validation($result, $tag){
		if(session_id() == '' || !isset($_SESSION)){ session_start(); }
		$tag = new WPCF7_Shortcode( $tag );

		$name = $tag->name;

		$value = isset( $_POST[$name] )
			? trim( wp_unslash( strtr( (string) $_POST[$name], "\n", " " ) ) )
			: '';

		if ( 'email' == $tag->basetype && $name==get_option('mo_customer_validation_cf7_email_key')) {
			$_SESSION['cf7_email_submitted'] = $value;
		}

		if ( 'tel' == $tag->basetype && $name=='mo_phone') {
			$_SESSION['cf7_phone_submitted'] = $value;
		}


		if ( 'text' == $tag->basetype && $name=='email_verify' || 'text' == $tag->basetype && $name=='phone_verify') {
			if ( $tag->is_required() && '' == $value ) {
				$error = true;
				$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
			}

			if($_SESSION['cf7_contact_page']=='true'){
				$validate_otp = new MO_Validation_Utility();
				if(strcasecmp($_SESSION['cf7_email_verified'], $_SESSION['cf7_email_submitted'])==0 || strcasecmp($_SESSION['cf7_phone_verified'], $_SESSION['cf7_phone_submitted'])==0 ){
					$content = json_decode($validate_otp->validate_otp_token($_SESSION['mo_customer_validation_site_txID'], $value),true);
					if(strcasecmp($content['status'], 'SUCCESS') != 0) { 
						$result->invalidate( $tag, 'Invalid OTP Entered' );
					}else{
						session_unset($_SESSION['mo_customer_validation_site_txID']);
						session_unset($_SESSION['cf7_contact_page']);
						session_unset($_SESSION['cf7_email_verified']);
						session_unset($_SESSION['cf7_email_submitted']);
					}
				}else{
					if(get_option('mo_customer_validation_cf7_contact_type')=="mo_cf7_contact_phone_enable")
						$result->invalidate( $tag, "The phone number OTP was sent to and the phone number in contact submission do not match." );
					else
						$result->invalidate( $tag, "The email OTP was sent to and the email in contact submission do not match." );
				}
			}else{
				session_unset($_SESSION['cf7_email_verified']);
				$result->invalidate( $tag, "You need to validate your Phone." );
			}
		}

		return $result;
	}
	
?>