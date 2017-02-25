<?php
function mo_register_plugin() {
	if( isset( $_GET[ 'tab' ]) && $_GET[ 'tab' ] !== 'register' ) {
		$active_tab = $_GET[ 'tab' ];
	} else if(MO_Validation_Utility::mo_customer_validation_is_customer_registered()) {
		$active_tab = 'settings';
	} else {
		$active_tab = 'register';
	}

	if(MO_Validation_Utility::mo_is_curl_installed()==0){ ?>
		<p style="color:red;">(Dikkat: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP CURL eklentisi</a> yüklü ya da aktif değil.) Kurulumu için linkten yardım alabilirsiniz.</p>
	<?php
	}?>
<div id="tab">
	<h2 class="nav-tab-wrapper">
		<?php if(!MO_Validation_Utility::mo_customer_validation_is_customer_registered()) { ?>
		<a class="nav-tab <?php echo $active_tab == 'register' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'register'), $_SERVER['REQUEST_URI'] ); ?>">İletimerkezi Ayarları</a>
		<?php }else{ ?>
		<a class="nav-tab <?php echo $active_tab == 'profile' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'profile'), $_SERVER['REQUEST_URI'] ); ?>">İletimerkezi Ayarları</a>
		<?php } ?>
		<a class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'settings'), $_SERVER['REQUEST_URI'] ); ?>">SMS Ayarları</a>
		<!--<a class="nav-tab <?php echo $active_tab == 'config' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'config'), $_SERVER['REQUEST_URI'] ); ?>">Raporlar</a>
		<a class="nav-tab <?php echo $active_tab == 'pricing' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'pricing'), $_SERVER['REQUEST_URI'] ); ?>">Licensing Plans</a>
		<a class="nav-tab <?php echo $active_tab == 'help' ? 'nav-tab-active' : ''; ?>" href="<?php echo add_query_arg( array('tab' => 'help'), $_SERVER['REQUEST_URI'] ); ?>">Help & Troubleshooting</a>-->	
	</h2>
</div>

<div id="mo_registration_settings">

	<div class="mo_container">
			<table style="width:100%;">
				<tr>
					<td style="vertical-align:top;width:65%;">

						<?php
							if ( $active_tab == 'register') {
								mo_profile_info();
							}else if($active_tab == 'settings') {
								mo_validation_show_settings_page();
							}else if($active_tab == 'help') {
								mo_validation_troubleshoot_info();
							}else if($active_tab == 'profile'){
								mo_profile_info();
							}else if($active_tab == 'pricing'){
								mo_customer_validation_pricing_info();
							}else if($active_tab == 'config'){
								mo_validation_extra_settings();
							}
							
						?>
					</td>
					<?php If($active_tab != 'pricing' || MO_Validation_Utility::mo_is_customer_validated()){ ?>
					<td style="vertical-align:top;padding-left:1%;">
						<?php echo miniorange_plugin_support(); ?>
					</td>
					<?php }?>
				</tr>
			</table>
		<?php

}


function mo_profile_info(){
	global $current_user;
	$current_user = wp_get_current_user();
	$get_balance = MO_Validation_Utility::get_balance();
	//die(var_dump($get_balance[1]));
?>
	<div class="mo_registration_table_layout">
		<h3>İleti Merkezi Ayarları</h3>
		<p><?php if(!empty($get_balance) && ($get_balance[1] == true )){ ?>
			<strong>Mevcut bakiyeniz : <sms><?php echo $get_balance[0]; ?></sms> <a target="_blank" href="https://www.iletimerkezi.com/index.php?function=default&obj1=signinViaGet&gsm=<?php echo get_option('iletimerkezi_username');?>&password=<?php echo get_option('iletimerkezi_password');?>">SMS Satın Al!</a></strong>
		<?php }else if(!empty($get_balance) && ($get_balance[1] == false )){ ?>
			<strong style = "color:red"><?php echo $get_balance[0]; ?></strong>
		<?php }else{ ?>
		<strong>Mesaj gönderebilmek için giriş bilgileriniz doldurun. Eğer bilmiyorsanız <a href="https://www.iletimerkezi.com" target="_blank">İleti Merkezi'nden</a> bilgi alabilirsiniz.</strong>
		<?php 
		}
		?>
        </p>
	<form method="post" action="">
		<input type="hidden" name="option" value="iletimerkezi_options" />
		<table>
			<tr>
				<td style="width:45%; padding: 10px;"><b>İletimerkezi Kullanıcı Adınız :</b></td>
				<td><input type="text" style="width:270px; padding: 10px;"  id="iletimerkezi_username" placeholder="İletimerkezi Kullanıcı Adınız"  name="iletimerkezi_username" value="<?php echo get_option('iletimerkezi_username');?>"></td> 

			</tr>
			<tr>
				<td style="width:45%; padding: 10px;"><b>İletimerkezi Şifreniz :</b></td>
				<td><input type="password" style="width:270px; padding: 10px;"  id="iletimerkezi_password" placeholder="İletimerkezi Şifreniz" name="iletimerkezi_password" value="<?php echo get_option('iletimerkezi_password');?>"></td> 

			</tr>
			<tr>
				<td style="width:45%; padding: 10px;"><b>Başlık Bilginiz :</b></td>
				<td><input type="text" style="width:270px; padding: 10px;"  id="iletimerkezi_sender" placeholder="Başlık Bilginiz"  name="iletimerkezi_sender" value="<?php echo get_option('iletimerkezi_sender');?>"></td> 

			</tr>
		</table><br/>
		<input type="submit" name="submit" value="Kaydet" style="width:110px;" class="button button-primary button-large" />

	</form>
	
<?php 
}

function miniorange_plugin_support(){
	global $current_user;
	$current_user = wp_get_current_user();
?>
	<div class="mo_registration_support_layout">

		<h3>Destek</h3>
		<p>Destek için <a href ="http://www.iletimerkezi.com"> iletimerkezi.com </a>'a başvurabilirsiniz.</p>
		<p>Şikayet ve önerileriniz için destek@emarka.com.tr adresine mail gönderebilirsiniz.</p>
		<p style = "text-align: right; margin-right: 10px; ">Versiyon 1.0</p>
	</div>
	
<?php
}

function mo_registration_show_verify_password_page() {
	?>
			<!--Verify password with miniOrange-->
		<form name="f" method="post" action="">
			<input type="hidden" name="option" value="mo_registration_connect_verify_customer" />
			<div class="mo_registration_table_layout">
				<?php if(!MO_Validation_Utility::mo_customer_validation_is_customer_registered()) { ?>
					<div style="display:block;margin-top:10px;color:red;background-color:rgba(251, 232, 0, 0.15);padding:5px;border:solid 1px rgba(255, 0, 9, 0.36);">
					Please <a href="<?php echo add_query_arg( array('tab' => 'register'), $_SERVER['REQUEST_URI'] ); ?>">Register or Login with miniOrange</a> to enable OTP Verification.
					</div>
				<?php } ?>
			
				<h3>Login with miniOrange</h3>
				<p><b>It seems you already have an account with miniOrange. Please enter your miniOrange email and password. <a href="#forgot_password">Click here if you forgot your password?</a></b></p>
				<table class="mo_registration_settings_table">
					<tr>
						<td><b><font color="#FF0000">*</font>Email:</b></td>
						<td><input class="mo_registration_table_textbox" type="email" name="email"
							required placeholder="person@example.com"
							value="<?php echo get_option('mo_customer_validation_admin_email');?>" /></td>
					</tr>
					<td><b><font color="#FF0000">*</font>Password:</b></td>
					<td><input class="mo_registration_table_textbox" required type="password"
						name="password" placeholder="Choose your password" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="button" id="goBackButton" value="Go Back"
							class="button button-primary button-large" />
							<input type="submit" name="submit"
							class="button button-primary button-large" />
						</td>
					</tr>
				</table>
			</div>
		</form>
		<form name="goBack" method="post" action="" id="goBacktoRegistrationPage">
			<input type="hidden" name="option" value="mo_registration_go_back"/>
		</form>
		<form name="forgotpassword" method="post" action="" id="forgotpasswordform">
			<input type="hidden" name="option" value="mo_registration_forgot_password"/>
		</form>
		<script>
			jQuery('a[href="#forgot_password"]').click(function(){
				jQuery('#forgotpasswordform').submit();
			});
			jQuery('#goBackButton').click(function(){
				jQuery('#goBacktoRegistrationPage').submit();
			});
		</script>
		<?php
}

function mo_registration_show_otp_verification(){
	?>
		<!-- Enter otp -->
		<form name="f" method="post" id="otp_form" action="">
			<input type="hidden" name="option" value="mo_registration_validate_otp" />
				<div class="mo_registration_table_layout">
					<table class="mo_registration_settings_table">
						<h3>Verify Your Email</h3>
						<tr>
							<td><b><font color="#FF0000">*</font>Enter OTP:</b></td>
							<td colspan="3"><input class="mo_registration_table_textbox" autofocus="true" type="text" name="otp_token" required placeholder="Enter OTP" style="width:40%;" pattern="[0-9]{6,8}" title="Only 6 digit numbers are allowed"/>
							 &nbsp;&nbsp;<a style="cursor:pointer;" onclick="document.getElementById('resend_otp_form').submit();">Resend OTP ?</a></td>
						</tr>
						<tr><td colspan="3"></td></tr>
						<tr>
							<td>&nbsp;</td>
							<td style="width:17%">
								<input type="submit" name="submit" value="Validate OTP" class="button button-primary button-large" />
							</td>
		</form>
						<form name="f" method="post">
						<td style="width:18%">
										<input type="hidden" name="option" value="mo_registration_go_back"/>
										<input type="submit" name="submit"  value="Back" class="button button-primary button-large" /></td>
						</form>
							<form name="f" id="resend_otp_form" method="post" action="">
						<td>

							<input type="hidden" name="option" value="mo_registration_resend_otp"/>
						</td>
						</tr>
											
										
							</form>
					</table>
		<br>
		<hr>

		<h3>I did not recieve any email with OTP . What should I do ?</h3>
		<form id="phone_verification" method="post" action="">
			<input type="hidden" name="option" value="mo_registration_phone_verification" />
			 If you cannot see an email from miniOrange in your mails, please check your <b>SPAM Folder</b>. If you don't see an email even in SPAM folder, verify your identity with our alternate method.
			 <br><br>
				<b>Enter your valid phone number here and verify your identity using one time passcode sent to your phone.</b>
				<br><br>
				<table class="mo_registration_settings_table">
				<tr>
				<td colspan="3">
				<input class="mo_registration_table_textbox" required  pattern="[0-9\+]{12,18}" autofocus="true" style="width:100%;" type="tel" name="phone_number" id="phone" placeholder="Enter Phone Number" value="<?php echo get_option('mo_customer_validation_admin_phone'); ?>" title="Enter phone number(at least 10 digits) without any space or dashes."/>
				</td>
				<td>&nbsp;&nbsp;
			<a style="cursor:pointer;" onclick="document.getElementById('phone_verification').submit();">Resend OTP ?</a>
				</td>
				</tr>
				</table>
				<br><input type="submit" value="Send OTP" class="button button-primary button-large" />
		
		</form>
		<br>
		<h3>What is an OTP ?</h3>
		<p>OTP is a one time passcode ( a series of numbers) that is sent to your email or phone number to verify that you have access to your email account or phone. </p>
		</div>
		<script>
		jQuery("#phone").intlTelInput();
					
						
		</script>


<?php
}

function mo_registration_show_new_registration_page() {
	update_option ( 'mo_customer_validation_new_registration', 'true' );
	global $current_user;
	$current_user = wp_get_current_user();
	?>

		<!--Register with miniOrange-->
		<form name="f" method="post" action="" id="register-form">
			<input type="hidden" name="option" value="mo_registration_register_customer" />
			<div class="mo_registration_table_layout">
				<?php if(!MO_Validation_Utility::mo_customer_validation_is_customer_registered()) { ?>
					<div style="display:block;margin-top:10px;color:red;background-color:rgba(251, 232, 0, 0.15);padding:5px;border:solid 1px rgba(255, 0, 9, 0.36);">
					Please <a href="<?php echo add_query_arg( array('tab' => 'register'), $_SERVER['REQUEST_URI'] ); ?>">Register or Login with miniOrange</a> to enable OTP Verification.
					</div>
				<?php } ?>

				<h3>Register with miniOrange</h3>

				<p>Please enter a valid email that you have access to. You will be able to move forward after verifying an OTP that we will be sending to this email. <b>OR</b> Login using your miniOrange credentials.
				</p>
				<table class="mo_registration_settings_table">
					<tr>
						<td><b><font color="#FF0000">*</font>Email:</b></td>
						<td><input class="mo_registration_table_textbox" type="email" name="email"
							required placeholder="person@example.com"
							value="<?php echo $current_user->user_email;?>" /></td>
					</tr>
					<tr>
						<td><b><font color="#FF0000">*</font>Website/Company Name:</b></td>
						<td><input class="mo_registration_table_textbox" type="text" name="company"
							required placeholder="Enter website or company name" 
							value="<?php echo $_SERVER['SERVER_NAME']; ?>"/></td>
					</tr>
					<tr>
						<td><b>&nbsp;&nbsp;First Name:</b></td>
						<td><input class="mo_registration_table_textbox" type="text" name="fname"
							placeholder="Enter first name"
							value="<?php echo $current_user->user_firstname;?>" /></td>
					</tr>
					<tr>
						<td><b>&nbsp;&nbsp;Last Name:</b></td>
						<td><input class="mo_registration_table_textbox" type="text" name="lname"
							placeholder="Enter last name"
							value="<?php echo $current_user->user_lastname;?>" /></td>
					</tr>
					<tr>
						<td><b>&nbsp;&nbsp;Phone number:</b></td>
						<td><input class="mo_registration_table_textbox" type="tel" id="phone"
							pattern="[\+]\d{11,14}|[\+]\d{1,4}[\s]\d{9,10}" name="phone"
							title="Phone with country code eg. +1xxxxxxxxxx"
							placeholder="Phone with country code eg. +1xxxxxxxxxx"
							value="<?php echo get_option('mo_customer_validation_admin_phone');?>" /><br/>We will call only if you need support.</td>
						<td></td>
					</tr>
					<tr>
						<td><b><font color="#FF0000">*</font>Password:</b></td>
						<td><input class="mo_registration_table_textbox" required type="password"
							name="password" placeholder="Choose your password (Min. length 6)" /></td>
					</tr>
					<tr>
						<td><b><font color="#FF0000">*</font>Confirm Password:</b></td>
						<td><input class="mo_registration_table_textbox" required type="password"
							name="confirmPassword" placeholder="Confirm your password" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><br /><input type="submit" name="submit" value="Next" style="width:100px;"
							class="button button-primary button-large" /></td>
					</tr>
				</table>
				<p></p>
				By clicking Next, you agree to our <a href="http://miniorange.com/usecases/miniOrange_Privacy_Policy.pdf" target="_blank">Privacy Policy</a> and <a href="http://miniorange.com/usecases/miniOrange_User_Agreement.pdf" target="_blank">User Agreement</a>.<p></p>				
			</div>
		</form>
		<script>
				//jQuery("#phone").intlTelInput();
				var text = "&nbsp;&nbsp;We will call only if you need support."
				jQuery('.intl-number-input').append(text);

		</script>
		<?php
}

function mo_validation_show_settings_page(){
?>	
	<div class="mo_registration_table_layout">
		<?php $get_balance = MO_Validation_Utility::get_balance(); 
		if(!empty($get_balance) && $get_balance[1] == false) { ?>
			<div style="display:block;margin-top:10px;color:red;background-color:rgba(251, 232, 0, 0.15);padding:5px;border:solid 1px rgba(255, 0, 9, 0.36);">
			Lütfen iletimerkezi bilgileriniz ile <a href="<?php echo add_query_arg( array('tab' => 'register'), $_SERVER['REQUEST_URI'] ); ?>">giriş yapınız.</a> 
			</div>
		<?php } ?>
		<form name="f" method="post" action="" id="mo_otp_verification_settings">
			<input type="hidden" name="option" value="mo_customer_validation_settings" />
				<table style="width: 100%;">
					<tr>
						<td colspan="3">
							<h3>OTP AYARLARI</h3>
							<hr>
						</td>
					</tr>
					<tr>
						<td><b>Aşağıdaki adımları takip ederek kolayca ayarları yapabilirsiniz:
							<ol><li>Formunuzu seçin <a class="registration_question">[ Formum listede yok ]</a>
								<div style="font-weight:normal" hidden class="mo_registration_help_desc" > Kullandığınız formu destek@emarka.com.tr adresine gönderirseniz eklentimize eklemeye çalışırız. </div>
							</li>
							<li>Ayarlarınızı kayıt edin.</li>
							<li>Çıkış yapıp kayıt formunuzu test edin.</li>
							<!--<li>To customize your SMS/Email messages/gateway check under <a href="<?php echo admin_url().'?page=mo_customer_validation_settings&tab=config'?>">Configuration Tab</a></li>
							<li>For any query related to custom SMS/Email messages/gateway check <a href="<?php echo admin_url().'?page=mo_customer_validation_settings&tab=help'?>">Help & Troubleshooting Tab</a></li>-->
						</b></td>
					</tr>
					<tr>
						<td>
							<br/><input type="checkbox" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="wp_default" class="app_enable" name="mo_customer_validation_wp_default_enable" value="1"
							<?php checked( get_option('mo_customer_validation_wp_default_enable') == 1 );?> /><strong>WordPress Default Registration Form</strong>
						</td>
					</tr>
					<!--<tr>
						<td>
							<br/><input type="checkbox" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="wc_default" data-toggle="wc_default_options" class="app_enable" name="mo_customer_validation_wc_default_enable" value="1"
							<?php checked( get_option('mo_customer_validation_wc_default_enable') == 1 );?> /><strong>Woocommerce Registration Form</strong>
							<img class="form_preview" title="Click here to see Woocommerce Form" style="margin-bottom:-4px" data-formlink="https://wordpress.org/plugins/woocommerce/" src="<?php echo plugins_url( 'includes/images/i.png', __FILE__ )?>" />
							<div class="mo_registration_help_desc" <?php if(!get_option('mo_customer_validation_wc_default_enable') == 1) echo 'hidden'; ?> id="wc_default_options">
								<b></b>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="wc_phone" class="app_enable" name="mo_customer_validation_wc_enable_type" value="mo_wc_phone_enable"
									<?php checked( get_option('mo_customer_validation_wc_enable_type') == "mo_wc_phone_enable" );?> /><strong>Telefon ile doğrulamayı onayla.</strong>
								</p>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="wc_email" class="app_enable" name="mo_customer_validation_wc_enable_type" value="mo_wc_email_enable"
									<?php checked( get_option('mo_customer_validation_wc_enable_type') == "mo_wc_email_enable" );?> /><strong>Enable Email verification</strong>
								</p>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="wc_both" class="app_enable" name="mo_customer_validation_wc_enable_type" value="mo_wc_both_enable"
									<?php checked( get_option('mo_customer_validation_wc_enable_type') == "mo_wc_both_enable" );?> /><strong>Let the user choose</strong>
									<?php mo_form_additional_info(1,null,false); ?>
								</p>

							</div>

						</td>
					</tr>
					<tr>
						<td>
							<br/><input type="checkbox" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="wc_checkout" data-toggle="wc_checkout_options" class="app_enable" name="mo_customer_validation_wc_checkout_enable" value="1"
							<?php checked( get_option('mo_customer_validation_wc_checkout_enable') == 1 );?> /><strong>Woocommerce Checkout Form</strong>
							<img class="form_preview" title="Click here to see Woocommerce Checkout Form" style="margin-bottom:-4px" data-formlink="https://wordpress.org/plugins/woocommerce/" src="<?php echo plugins_url( 'includes/images/i.png', __FILE__ )?>" />
							<div class="mo_registration_help_desc" <?php if(!get_option('mo_customer_validation_wc_checkout_enable') == 1) echo 'hidden'; ?> id="wc_checkout_options">
								<b></b>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="wc_checkout_phone" class="app_enable" name="mo_customer_validation_wc_checkout_type" value="mo_wc_phone_enable"
									<?php checked( get_option('mo_customer_validation_wc_checkout_type') == "mo_wc_phone_enable" );?> /><strong>Telefon ile doğrulamayı onayla.</strong>
								</p>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="wc_checkout_email" class="app_enable" name="mo_customer_validation_wc_checkout_type" value="mo_wc_email_enable"
									<?php checked( get_option('mo_customer_validation_wc_checkout_type') == "mo_wc_email_enable" );?> /><strong>Enable Email verification</strong>
								</p>
								<p style="margin-left:2%;">
									<input type="checkbox" <?php if(get_option('get_balance') != 1) echo 'disabled'?> <?php checked(get_option('mo_customer_validation_wc_checkout_guest')); ?> class="app_enable" name="mo_customer_validation_wc_checkout_guest" value="1" ><b>Enable Verification only for Guest Checkout.</b><br/>
									<div style="margin-left:4%;"><i>Verify customer's phone number or email address only when he is not logged in during checkout ( is a guest user ).</i></div>
								<p>
								<p style="margin-left:2%;">
									<input type="checkbox" <?php if(get_option('get_balance') != 1) echo 'disabled'?> <?php checked(get_option('mo_customer_validation_wc_checkout_button')); ?> class="app_enable" name="mo_customer_validation_wc_checkout_button" value="1" type="checkbox"><b>Show verification button instead of link on WooCommerce Checkout Page.</b><br/>
								</p>
							</div>

						</td>
					</tr>-->
					<tr>
						<td>
							<br/><input type="checkbox" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="pb_default" class="app_enable" name="mo_customer_validation_pb_default_enable" value="1"
							<?php checked( get_option('mo_customer_validation_pb_default_enable') == 1 );?> /><strong>Profile Builder Kayıt Formu</strong>
							<img class="form_preview" title="Profile Builder görmek için tıklayınız." style="margin-bottom:-4px"  id="pbform_info" data-formlink="https://wordpress.org/plugins/profile-builder/" src="<?php echo plugins_url( 'includes/images/i.png', __FILE__ )?>" />
							<p><a  class="form_query" data-desc="1"><b>[ Kayıt formuna nick name alanını manuel olarak eklemelisiniz. ]</b></a></p>
							<div id="form_query_desc_1"hidden class="mo_registration_help_desc">
								<ol>
									<li>Alanları düzenlemek için <a href="<?php echo admin_url().'admin.php?page=manage-fields'?>"  target="_blank">tıklayın.</a> </li>
									<li>Default - Nickname seçip Add Field'a tıklayın.</li>
									<li>Alan adını Telefon Numarası olarak kayıt edin.</li>
								</ol>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<br/><input type="checkbox" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="simplr_default" data-toggle="simplr_default_options" class="app_enable" name="mo_customer_validation_simplr_default_enable" value="1"
							<?php checked( get_option('mo_customer_validation_simplr_default_enable') == 1 );?> /><strong>Simplr User Registration Form Plus</strong>
							<img class="form_preview" title="Simplr User Registration Form'u görmek için tıklayınız." style="margin-bottom:-4px" data-formlink="https://wordpress.org/plugins/simplr-registration-form/" src="<?php echo plugins_url( 'includes/images/i.png', __FILE__ )?>" />
							<div class="mo_registration_help_desc" <?php if(!get_option('mo_customer_validation_simplr_default_enable') == 1) echo 'hidden'; ?> id="simplr_default_options">
								
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> data-form="simplr_phone" id="simplr_phone" class="form_options app_enable" name="mo_customer_validation_simplr_enable_type" value="mo_phone_enable"
									<?php checked( get_option('mo_customer_validation_simplr_enable_type') == "mo_phone_enable" );?> /><strong>Telefon ile doğrulamayı onayla.</strong>
									<a class="form_query" data-desc="2"> <b>[ Telefon numarası alanını manuel olarak eklemelisiniz. ]</b></a>
									<div hidden id="form_query_desc_2" class="mo_registration_help_desc">
									<ol>
										<li>Form alanlarını düzenlemek için <a href="<?php echo admin_url().'options-general.php?page=simplr_reg_set&regview=fields&orderby=name&order=desc'?>" target="_blank">tıklayınız.</a></li>
										<li> <b>Add Field</b> butonuna tıklayarak Telefon numarası alanı ekleyin.</li>
										<li> <b>Field Name</b> ve <b>Field Key</b> alanlarını doldurun. Field Key alanını not alabilirsiniz daha sonra eklenti için gerekecek.</li>
										<li>Sayfanın sol alt kısmındaki <b>Add Field</b> butonuna tıklayarak yeni alanınızı kayıt edin. </li>
										<li>Sayflarınızı görmek için <a href="<?php echo admin_url().'edit.php?post_type=page'?>" target="_blank	">tıklayınız</a>.</li>
										<li>Eğer varsa kayıt formu için sayfanız <b>Düzenle</b>ye tıklayın yoksa yeni eklemek için <b>Yeni Ekle</b>ye tıklayın. </li>
										<li>Eğer formunuz varsa <b>fields="{2. adımda oluşturlan Field Key}"</b> eklemelisiniz. Eğer formunuz yoksa <b>Add Registration Form</b>'a tıklayarak yeni form eklemeli ve telefon numarası alanını seçmelisiniz.</li>
										<li><b>Güncelle</b>'ye tıklayrak sayfayı kayıt etmelisiniz.</li>
									</ol>
									</div>
								</p>
									<div <?php if(get_option('mo_customer_validation_simplr_enable_type') != "mo_phone_enable") echo 'hidden'; ?> class="simplr_form" id="simplr_phone_field" >
										Telefon numarasına ait Field Key'i yazın:<input class="mo_registration_table_textbox" id="simplr_phone_field_key1" name="simplr_phone_field_key" type="text" value="<?php echo get_option('mo_customer_validation_simplr_field_key'); ?>">
									</div>
								<!--<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> data-form="simplr_email" id="simplr_email" class="form_options app_enable" name="mo_customer_validation_simplr_enable_type" value="mo_email_enable"
									<?php checked( get_option('mo_customer_validation_simplr_enable_type') == "mo_email_enable" );?> /><strong>Enable Email verification</strong>
								</p>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> data-form="simplr_both" id="simplr_both" class="form_options app_enable" name="mo_customer_validation_simplr_enable_type" value="mo_both_enable"
									<?php checked( get_option('mo_customer_validation_simplr_enable_type') == "mo_both_enable" );?> /><strong>Let the user choose</strong>
									<?php mo_form_additional_info(3,8,true); ?>
									<div hidden id="form_query_desc_8" class="mo_registration_help_desc">
									<ol>
										<li><a href="<?php echo admin_url().'options-general.php?page=simplr_reg_set&regview=fields&orderby=name&order=desc'?>" target="_blank">Click here</a> to see your list of fields. 
										<li>Add a new Phone Field by clicking the <b>Add Field</b> button.</li>
										<li>Give the <b>Field Name</b> and <b>Field Key</b> for the new field. Remember the Field Key as you will need it later.</li>
										<li>Click on <b>Add Field</b> button at the bottom of the page to save your new field.</li>
										<li><a href="<?php echo admin_url().'edit.php?post_type=page'?>" target="_blank	">Click here</a> to see your list of pages.</li>
										<li>Click on the <b>Edit</b> link of your page to modify it.</li>
										<li>In the ShortCode add the following attribute : <b>fields="{Field Key you provided in Step 2}"</b>. If you already have the fields attribute defined then just add the new field key to the list.</li>
										<li>Click on <b>update</b> to save your page.</li>
									</ol>
									</div>
								</p>
									<div <?php if(get_option('mo_customer_validation_simplr_enable_type') != "mo_both_enable") echo 'hidden'; ?> class="simplr_form" id="simplr_both_field" >
										Enter the Field Key of the phone field:<input class="mo_registration_table_textbox" id="simplr_phone_field_key2" name="simplr_phone_field_key" type="text" value="<?php echo get_option('mo_customer_validation_simplr_field_key'); ?>">
									</div>-->
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<br/><input type="checkbox" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="um_default" data-toggle="um_default_options" class="app_enable" name="mo_customer_validation_um_default_enable" value="1"
							<?php checked( get_option('mo_customer_validation_um_default_enable') == 1 );?> /><strong>Ultimate Member Registration Form</strong>
							<img class="form_preview" title="Ultimate Member Registration Form'u görmek için tıklanıyınız." style="margin-bottom:-4px" data-formlink="https://wordpress.org/plugins/ultimate-member/" src="<?php echo plugins_url( 'includes/images/i.png', __FILE__ )?>" />
							<div class="mo_registration_help_desc" <?php if(!get_option('mo_customer_validation_um_default_enable') == 1) echo 'hidden'; ?> id="um_default_options">
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="um_phone" class="app_enable" name="mo_customer_validation_um_enable_type" value="mo_um_phone_enable"
									<?php checked( get_option('mo_customer_validation_um_enable_type') == "mo_um_phone_enable" );?> /><strong>Telefon ile doğrulamayı onayla.</strong>
									<a  class="form_query" data-desc="4"><b>[ Telefon Numarası alanını manuel olarak eklemelisiniz. ]</b></a>
									<div id="form_query_desc_4"hidden class="mo_registration_help_desc">
										<ol>
											<li>Formlarınızı görmek için<a href="<?php echo admin_url().'edit.php?post_type=um_form'?>"  target="_blank"> tıklayınız.</a></li>
											<li>Kayıt formunuzu <b>Düzenle</b>'ye tıklayınız.</li>
											<li>Kayıt formunuza form eklede açılan listeden <b>Cep Telefonu Numarası</b>'ı ekleyin. </li>
											<li><b>Güncelle</b> butonuna tıklayarak kayıt edin.</li>
										</ol>
									</div>
								</p>
								<!--<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="um_email" class="app_enable" name="mo_customer_validation_um_enable_type" value="mo_um_email_enable"
									<?php checked( get_option('mo_customer_validation_um_enable_type') == "mo_um_email_enable" );?> /><strong>Enable Email verification</strong>
								</p>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="um_both" class="app_enable" name="mo_customer_validation_um_enable_type" value="mo_um_both_enable"
									<?php checked( get_option('mo_customer_validation_um_enable_type') == "mo_um_both_enable" );?> /><strong>Let the user choose</strong>
									<?php mo_form_additional_info(5,7,true); ?>
									<div id="form_query_desc_7"hidden class="mo_registration_help_desc">
										<ol>
											<li><a href="<?php echo admin_url().'edit.php?post_type=um_form'?>"  target="_blank">Click here</a> to see your list of forms.</li>
											<li>Click on the <b>Edit link</b> of your form.</li>
											<li>Add a new <b>Mobile Number</b> Field from the list of predefined fields.</li>
											<li>Click on <b>update</b> to save your form.</li>
										</ol>
									</div>
								</p>-->
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<br/><input type="checkbox" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="event_default" class="app_enable" data-toggle="event_default_options" name="mo_customer_validation_event_default_enable" value="1"
							<?php checked( get_option('mo_customer_validation_event_default_enable') == 1 );?> /><strong>Event Registration Form</strong>
							<img class="form_preview" title="Event Registration Form'u görmek için tıklayınız." style="margin-bottom:-4px" data-formlink="https://wordpress.org/plugins/event-registration/" src="<?php echo plugins_url( 'includes/images/i.png', __FILE__ )?>" />
							<div class="mo_registration_help_desc" <?php if(!get_option('mo_customer_validation_event_default_enable') == 1) echo 'hidden'; ?> id="event_default_options">
								<!--<b></b>-->
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="event_phone" class="app_enable" name="mo_customer_validation_event_enable_type" value="mo_event_phone_enable"
									<?php checked( get_option('mo_customer_validation_event_enable_type') == "mo_event_phone_enable" );?> /><strong>Telefon ile doğrulamayı onayla.</strong>
								</p>
								<!--<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="event_email" class="app_enable" name="mo_customer_validation_event_enable_type" value="mo_event_email_enable"
									<?php checked( get_option('mo_customer_validation_event_enable_type') == "mo_event_email_enable" );?> /><strong>Enable Email verification</strong>
								</p>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="event_both" class="app_enable" name="mo_customer_validation_event_enable_type" value="mo_event_both_enable"
									<?php checked( get_option('mo_customer_validation_event_enable_type') == "mo_event_both_enable" );?> /><strong>Let the user choose</strong>
									<?php mo_form_additional_info(6,null,false); ?>
								</p>-->
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<br/><input type="checkbox" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="bbp_default" class="app_enable" data-toggle="bbp_default_options" name="mo_customer_validation_bbp_default_enable" value="1"
							<?php checked( get_option('mo_customer_validation_bbp_default_enable') == 1 );?> /><strong>BuddyPress Registration Form</strong>
							<img class="form_preview" title="BuddyPress Form'u görmek için tıklayınız." style="margin-bottom:-4px" data-formlink="https://wordpress.org/plugins/buddypress/" src="<?php echo plugins_url( 'includes/images/i.png', __FILE__ )?>" />
							<div class="mo_registration_help_desc" <?php if(!get_option('mo_customer_validation_bbp_default_enable') == 1) echo 'hidden'; ?> id="bbp_default_options">
								<!--<b></b>-->
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> data-form='bbp_phone' id="bbp_phone" class="form_options app_enable" name="mo_customer_validation_bbp_enable_type" value="mo_bbp_phone_enable"
									<?php checked( get_option('mo_customer_validation_bbp_enable_type') == "mo_bbp_phone_enable" );?> /><strong>Telefon ile doğrulamayı onayla.</strong>
									<a class="form_query" data-desc="13"> <b>[ Telefon numarası alanını manuel olarak eklemelisiniz. ]</b></a>
									<div hidden id="form_query_desc_13" class="mo_registration_help_desc">
										<ol>
											<li>Form alanlarını görmek için <a href="<?php echo admin_url().'users.php?page=bp-profile-setup'?>" target="_blank"> tıklayınız.</a></li>
											<li><b>Yeni Alan Ekle</b> butonuna tıklayarak yeni bir telefon numarası alanı ekleyiniz.</li>
											<li><b>İsim</b> ve <b>Açıklama</b> yazın yeni alan için. İsim alanını not alın ekleti için gerekecek.</li>
											<li>Alt kısımda <b>Tip</b> olarak <b>Sayı</b> ya da <b>Yazı Alanı</b> seçiniz.</li>
											<!--<li>Select the field <b>requirement</b> from the select box to the right.</li>-->
											<li><b>Kaydet</b>'e tıklayarak kayıt edin.</li>
										</ol>
									</div>
								</p>
								<div <?php if(get_option('mo_customer_validation_bbp_enable_type') != "mo_bbp_phone_enable") echo 'hidden'; ?> class="bbp_form" id="bbp_phone_field">
										Telefon numarası alanının adını yazınız :<input class="mo_registration_table_textbox" id="bbp_phone_field_key" name="bbp_phone_field_key" type="text" value="<?php echo get_option('mo_customer_validation_bbp_phone_key'); ?>">
								</div>
								<!--<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> data-form="bbp_email" id="bbp_email" class="form_options app_enable" name="mo_customer_validation_bbp_enable_type" value="mo_bbp_email_enable"
									<?php checked( get_option('mo_customer_validation_bbp_enable_type') == "mo_bbp_email_enable" );?> /><strong>Enable Email verification</strong>
								</p>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?>  data-form='bbp_both' id="bbp_both" class="form_options app_enable" name="mo_customer_validation_bbp_enable_type" value="mo_bbp_both_enable"
									<?php checked( get_option('mo_customer_validation_bbp_enable_type') == "mo_bbp_both_enable" );?> /><strong>Let the user choose</strong>
									<?php mo_form_additional_info(9,14,true); ?>
									<div hidden id="form_query_desc_14" class="mo_registration_help_desc">
										<ol>
											<li><a href="<?php echo admin_url().'users.php?page=bp-profile-setup'?>" target="_blank">Click here</a> to see your list of fields.</li>
											<li>Add a new Phone Field by clicking the <b>Add New Field</b> button.</li>
											<li>Give the <b>Field Name</b> and <b>Description</b> for the new field. Remember the Field Name as you will need it later.></li>
											<li>Select the field <b>type</b> from the select box. Choose <b>Number</b>.</li>
											<li>Select the field <b>requirement</b> from the select box to the right.</li>
											<li>Click on <b>Save</b> button to save your new field.</li>
										</ol>
									</div>
								</p>
									<div <?php if(get_option('mo_customer_validation_bbp_enable_type') != "mo_bbp_both_enable") echo 'hidden'; ?> class="bbp_form" id="bbp_both_field" >
										Enter the Name of the phone field:<input class="mo_registration_table_textbox" id="bbp_phone_field_key1" name="bbp_phone_field_key" type="text" value="<?php echo get_option('mo_customer_validation_bbp_phone_key'); ?>">
									</div>-->
							</div>
						</td>
					</tr>
					<!--<tr>
						<td>
							<br/><input type="checkbox" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="crf_default" class="app_enable" data-toggle="crf_default_options" name="mo_customer_validation_crf_default_enable" value="1"
							<?php checked( get_option('mo_customer_validation_crf_default_enable') == 1 );?> /><strong>Custom User Registration Form Builder [ RegistrationMagic ]</strong>
							<img class="form_preview" title="Click here to see RegistrationMagic Form" style="margin-bottom:-4px" data-formlink="https://wordpress.org/plugins/custom-registration-form-builder-with-submission-manager/" src="<?php echo plugins_url( 'includes/images/i.png', __FILE__ )?>" />
							<div class="mo_registration_help_desc" <?php if(!get_option('mo_customer_validation_crf_default_enable') == 1) echo 'hidden'; ?> id="crf_default_options">
								<b></b>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="crf_phone" data-form='crf_phone' class="form_options app_enable" name="mo_customer_validation_crf_enable_type" value="mo_crf_phone_enable"
									<?php checked( get_option('mo_customer_validation_crf_enable_type') == "mo_crf_phone_enable" );?> /><strong>Telefon ile doğrulamayı onayla.</strong>
									<a class="form_query" data-desc="15"> <b>[ You will need to add a Phone Number field manually ]</b></a>
									<div hidden id="form_query_desc_15" class="mo_registration_help_desc">
										<ol>
											<li><a href="<?php echo admin_url().'admin.php?page=crf_manage_forms'?>" target="_blank">Click here</a> to see your list of forms.</li>
											<li>Click on <b>field</b> link of your form to see list of fields.</li>
											<li>Choose <b>number</b> field from the list.</li>
											<li>Enter the <b>Label</b> of your new field. Keep this handy as you will need it later.</li>
											<li>Under Advanced Options check the box which says <b>Is Required</b>.</li>
											<li>Click on <b>Save</b> button to save your new field.</li>
										</ol>
									</div>
								</p>
								<div <?php if(get_option('mo_customer_validation_crf_enable_type') != "mo_crf_phone_enable") echo 'hidden'; ?> class="crf_form" id="crf_phone_field" >
										Enter the Label of the phone field:<input class="mo_registration_table_textbox" id="crf_phone_field_key" name="crf_phone_field_key" type="text" value="<?php echo get_option('mo_customer_validation_crf_phone_key'); ?>">
									</div>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="crf_email" data-form='crf_email' class="form_options app_enable" name="mo_customer_validation_crf_enable_type" value="mo_crf_email_enable"
									<?php checked( get_option('mo_customer_validation_crf_enable_type') == "mo_crf_email_enable" );?> /><strong>Enable Email verification</strong>
								</p>
								<div <?php if(get_option('mo_customer_validation_crf_enable_type') != "mo_crf_email_enable") echo 'hidden'; ?> class="crf_form" id="crf_email_field" >
										Enter the Label of the email field:<input class="mo_registration_table_textbox" id="crf_email_field_key" name="crf_email_field_key" type="text" value="<?php echo get_option('mo_customer_validation_crf_email_key'); ?>">
									</div>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="crf_both" data-form="crf_both" class="form_options app_enable" name="mo_customer_validation_crf_enable_type" value="mo_crf_both_enable"
									<?php checked( get_option('mo_customer_validation_crf_enable_type') == "mo_crf_both_enable" );?> /><strong>Let the user choose</strong>
									<?php mo_form_additional_info(10,16,true); ?>
									<div hidden id="form_query_desc_16" class="mo_registration_help_desc">
										<ol>
											<li><a href="<?php echo admin_url().'admin.php?page=crf_manage_forms'?>" target="_blank">Click here</a> to see your list of forms.</li>
											<li>Click on <b>field</b> link of your form to see list of fields.</li>
											<li>Choose <b>number</b> field from the list.</li>
											<li>Enter the <b>Label</b> of your new field. Keep this handy as you will need it later.</li>
											<li>Under Advanced Options check the box which says <b>Is Required</b>.</li>
											<li>Click on <b>Save</b> button to save your new field.</li>
										</ol>
									</div>
									<div <?php if(get_option('mo_customer_validation_crf_enable_type') != "mo_crf_both_enable") echo 'hidden'; ?> class="crf_form" id="crf_both_field" >
										Enter the Label of the phone field:<input class="mo_registration_table_textbox" id="crf_phone_field_key1" name="crf_phone_field_key" type="text" value="<?php echo get_option('mo_customer_validation_crf_phone_key'); ?>"><br/>
										Enter the Label of the email field:<input class="mo_registration_table_textbox" id="crf_email_field_key1" name="crf_email_field_key" type="text" value="<?php echo get_option('mo_customer_validation_crf_email_key'); ?>">
									</div>
								</p>
							</div>
						</td>
					</tr>-->
					<tr>
						<td>
							<br/><input type="checkbox" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="uultra_default" class="app_enable" data-toggle="uultra_default_options" name="mo_customer_validation_uultra_default_enable" value="1"
									<?php checked( get_option('mo_customer_validation_uultra_default_enable') == 1 );?> /><strong>User Ultra Registration Form</strong>
								<img class="form_preview" title="Users Ultra Registration formu görmek için tıklayınız." style="margin-bottom:-4px" data-formlink="https://wordpress.org/plugins/users-ultra/" src="<?php echo plugins_url( 'includes/images/i.png', __FILE__ )?>"/> 
								<div class="mo_registration_help_desc" <?php if(!get_option('mo_customer_validation_uultra_default_enable') == 1) echo 'hidden'; ?> id="uultra_default_options">
								<b></b>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> data-form="uultra_phone" id="uultra_phone" class="form_options app_enable" name="mo_customer_validation_uultra_enable_type" value="mo_uultra_phone_enable"
									<?php checked( get_option('mo_customer_validation_uultra_enable_type') == "mo_uultra_phone_enable" );?> /><strong>Telefon ile doğrulamayı onayla.</strong>
									<a class="form_query" data-desc="17"> <b>[ Telefon numarası alanını manuel olarak eklemelisiniz. ]</b></a>
									<div hidden id="form_query_desc_17" class="mo_registration_help_desc">
										<ol>
											<li>Form alanlarını görmek için<a href="<?php echo admin_url().'admin.php?page=userultra&tab=fields'?>" target="_blank"> tıklayınız</a> </li>
											<li><b>Click here to add new field</b> butonuna tıklayarak yeni alan ekleyiniz.</li>
											<li>Gerekli alanları doldurduktan sonra <b>Submit New Field</b> tıklayın.</li>
											<li><b>Meta Key</b>'i not alın eklentide gerekecektir.</li>
										</ol>
									</div>
								</p>
								<div <?php if(get_option('mo_customer_validation_uultra_enable_type') != "mo_uultra_phone_enable") echo 'hidden'; ?> class="uultra_form" id="uultra_phone_field" >
										 Numara alanına ait Meta Key'i yazın :<input class="mo_registration_table_textbox" id="uultra_phone_field_key" name="uultra_phone_field_key" type="text" value="<?php echo get_option('mo_customer_validation_uultra_phone_key'); ?>">
									</div>
								<!--<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> data-form="uultra_email" id="uultra_email" class="form_options app_enable" name="mo_customer_validation_uultra_enable_type" value="mo_uultra_email_enable"
									<?php checked( get_option('mo_customer_validation_uultra_enable_type') == "mo_uultra_email_enable" );?> /><strong>Enable Email verification</strong>
								</p>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> data-form="uultra_both" id="uultra_both" class="form_options app_enable" name="mo_customer_validation_uultra_enable_type" value="mo_uultra_both_enable"
									<?php checked( get_option('mo_customer_validation_uultra_enable_type') == "mo_uultra_both_enable" );?> /><strong>Let the user choose</strong>
									<?php mo_form_additional_info(11,18,true); ?>
									<div hidden id="form_query_desc_18" class="mo_registration_help_desc">
										<ol>
											<li><a href="<?php echo admin_url().'admin.php?page=userultra&tab=fields'?>" target="_blank">Click here</a> to see your list of fields.</li>
											<li>Click on <b>Click here to add new field</b> button to add a new field.</li>
											<li>Fill up the details of your new field and click on <b>Submit New Field</b>.</li>
											<li>Keep the <b>Meta Key</b> handy as you will need it later on.</li>
										</ol>
									</div>
									<div <?php if(get_option('mo_customer_validation_uultra_enable_type') != "mo_uultra_both_enable") echo 'hidden'; ?> class="uultra_form" id="uultra_both_field" >
										Enter the Meta Key of the phone field:<input class="mo_registration_table_textbox" id="uultra_phone_field_key1" name="uultra_phone_field_key" type="text" value="<?php echo get_option('mo_customer_validation_uultra_phone_key'); ?>">
									</div>
								</p>-->
							</div>
						</td>
					</tr>
					<tr>
 						<td>
							<br/><input type="checkbox" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="upme_default" class="app_enable" data-toggle="upme_default_options" name="mo_customer_validation_upme_default_enable" value="1"
							<?php checked( get_option('mo_customer_validation_upme_default_enable') == 1 );?> /><strong>UserProfile Made Easy Registration Form</strong>
									
							<img class="form_preview" title="User Profile Made Easy Registration Form'u görmek için tıklayın." style="margin-bottom:-4px" data-formlink="http://codecanyon.net/item/user-profiles-made-easy-wordpress-plugin/4109874" src="<?php echo plugins_url( 'includes/images/i.png', __FILE__ )?>" /> 
															
							<div class="mo_registration_help_desc" <?php if(!get_option('mo_customer_validation_upme_default_enable') == 1) echo 'hidden'; ?> id="upme_default_options">
								<b></b>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> data-form="upme_phone" id="upme_phone" class="form_options app_enable" name="mo_customer_validation_upme_enable_type" value="mo_upme_phone_enable"
									<?php checked( get_option('mo_customer_validation_upme_enable_type') == "mo_upme_phone_enable" );?> /><strong>Telefon ile doğrulamayı onayla.</strong>
								
									<a class="form_query" data-desc="19"> <b>[ Telefon numarası alanını manuel olarak eklemelisiniz. ]</b></a>
									<div hidden id="form_query_desc_19" class="mo_registration_help_desc">
										<ol>
											<li>Form alanlarını görmek için <a href="<?php echo admin_url().'admin.php?page=upme-field-customizer'?>" target="_blank">tıklayınız.</a> </li>
											<li><b>Click here to add new field</b> butonuna tıklayarak yeni alan ekleyin.</li>
											<li>Gerekli alanları doldurarak <b>Submit New Field</b> butonuna tıklayın.</li>
											<li><b>Meta Key</b>'i not edin, ekletide kullanılacaktır.</li>
										</ol>
									</div>
									<div <?php if(get_option('mo_customer_validation_upme_enable_type') != "mo_upme_phone_enable") echo 'hidden'; ?> class="upme_form" id="upme_phone_field" >
										Numara alanına ait Meta Key'i yazın.<input class="mo_registration_table_textbox" id="upme_phone_field_key" name="upme_phone_field_key" type="text" value="<?php echo get_option('mo_customer_validation_upme_phone_key'); ?>">
									</div>
								</p>

							<!--	<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> data-form="upme_email" id="upme_email" class="form_options app_enable" name="mo_customer_validation_upme_enable_type" value="mo_upme_email_enable"
									<?php checked( get_option('mo_customer_validation_upme_enable_type') == "mo_upme_email_enable" );?> /><strong>Enable Email verification</strong>
								</p>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> data-form="upme_both" id="upme_both" class="form_options app_enable" name="mo_customer_validation_upme_enable_type" value="mo_upme_both_enable"
									<?php checked( get_option('mo_customer_validation_upme_enable_type') == "mo_upme_both_enable" );?> /><strong>Let the user choose</strong>
									<?php mo_form_additional_info(12,19,true); ?>
									<div hidden id="form_query_desc_19" class="mo_registration_help_desc">
										<ol>
											<li><a href="<?php echo admin_url().'admin.php?page=upme-field-customizer'?>" target="_blank">Click here</a> to see your list of fields.</li>
											<li>Click on <b>Click here to add new field</b> button to add a new field.</li>
											<li>Fill up the details of your new field and click on <b>Submit New Field</b>.</li>
											<li>Keep the <b>Meta Key</b> handy as you will need it later on.</li>
										</ol>
									</div>
									<div <?php if(get_option('mo_customer_validation_upme_enable_type') != "mo_upme_both_enable") echo 'hidden'; ?> class="upme_form" id="upme_both_field" >
										Enter the Meta Key of the phone field:<input class="mo_registration_table_textbox" id="upme_phone_field_key1" name="upme_phone_field_key" type="text" value="<?php echo get_option('mo_customer_validation_upme_phone_key'); ?>">
									</div>
									
								</p>-->
							</div>
						</td>
					</tr>
					<tr>
 						<td>
							<br/><input type="checkbox" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="pie_default" class="app_enable" data-toggle="pie_default_options" name="mo_customer_validation_pie_default_enable" value="1"
									<?php checked( get_option('mo_customer_validation_pie_default_enable') == 1 );?> /><strong>PIE Registration Form</strong>
									
								<img class="form_preview" title="PIE Registration Form'u görmek için tıklayın." style="margin-bottom:-4px" data-formlink="http://pieregister.com/" src="<?php echo plugins_url( 'includes/images/i.png', __FILE__ )?>" /> 
																
							<div class="mo_registration_help_desc" <?php if(!get_option('mo_customer_validation_pie_default_enable') == 1) echo 'hidden'; ?> id="pie_default_options">
								<b></b>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="pie_phone" class="app_enable" name="mo_customer_validation_pie_enable_type" value="mo_pie_phone_enable"
									<?php checked( get_option('mo_customer_validation_pie_enable_type') == "mo_pie_phone_enable" );?> /><strong>Telefon ile doğrulamayı onayla.</strong>
								
								<div <?php if(get_option('mo_customer_validation_pie_enable_type') != "mo_pie_phone_enable") echo 'hidden'; ?> id="pie_phone_field" >
										Numara alanına ait Label'ı yazın :<input class="mo_registration_table_textbox" id="pie_phone_field_key" name="pie_phone_field_key" type="text" value="<?php echo get_option('mo_customer_validation_pie_phone_key'); ?>">
									</div>
								</p>
								<!--<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="pie_email" class="app_enable" name="mo_customer_validation_pie_enable_type" value="mo_pie_email_enable"
									<?php checked( get_option('mo_customer_validation_pie_enable_type') == "mo_pie_email_enable" );?> /><strong>Enable Email verification</strong>
								</p>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="pie_both" class="app_enable" name="mo_customer_validation_pie_enable_type" value="mo_pie_both_enable"
									<?php checked( get_option('mo_customer_validation_pie_enable_type') == "mo_pie_both_enable" );?> /><strong>Let the user choose</strong>
									<?php mo_form_additional_info(20,null,false); ?>
									<div <?php if(get_option('mo_customer_validation_pie_enable_type') != "mo_pie_both_enable") echo 'hidden'; ?> id="pie_both_field" >
										Enter the Meta Key of the phone field:<input class="mo_registration_table_textbox" id="pie_phone_field_key1" name="pie_phone_field_key" type="text" value="<?php echo get_option('mo_customer_validation_pie_phone_key'); ?>">
									</div>
									
								</p>-->
							</div>
						</td>
					</tr>
					<!--<tr>
 						<td>
							<br/><input type="checkbox" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="cf7_contact" class="app_enable" data-toggle="cf7_contact_options" name="mo_customer_validation_cf7_contact_enable" value="1"
									<?php checked( get_option('mo_customer_validation_cf7_contact_enable') == 1 );?> /><strong>Contact Form 7 - Contact Form</strong>
									
								<img class="form_preview" title="Click here to see Contact Form 7" style="margin-bottom:-4px" data-formlink="https://wordpress.org/plugins/contact-form-7/" src="<?php echo plugins_url( 'includes/images/i.png', __FILE__ )?>" /> 
																
							<div class="mo_registration_help_desc" <?php if(!get_option('mo_customer_validation_cf7_contact_enable') == 1) echo 'hidden'; ?> id="cf7_contact_options">
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="cf7_contact_email" class="app_enable" data-toggle="cf7_contact_email_instructions" name="mo_customer_validation_cf7_contact_type" value="mo_cf7_contact_email_enable"
									<?php checked( get_option('mo_customer_validation_cf7_contact_type') == "mo_cf7_contact_email_enable" );?> /><strong>Enable Email verification</strong>
								</p>
								<div <?php if(get_option('mo_customer_validation_cf7_contact_type') != "mo_cf7_contact_email_enable") echo 'hidden'; ?> class="mo_registration_help_desc" id="cf7_contact_email_instructions" >
										Follow the following steps to enable Email Verification for Contact form 7: 
										<ol>
											<li><a href="<?php echo admin_url().'edit.php?post_type=page'?>" target="_blank">Click Here</a> to see your list of pages.</li>
											<li>Click on the <b>Edit</b> option of the page which has your contact form.</li>
											<li>Add the following short code just below your Contact Form 7 shortcode : <code>[mo_verify_email]</code> </li>
											<li><a href="<?php echo admin_url().'admin.php?page=wpcf7'?>" target="_blank">Click Here</a> to see your list of Contact Forms.</li>
											<li>Click on the <b>Edit</b> option of your form.</li>
											<li>
												Now place the following code in your form where you wish to show the Verify Email button and field : <br>
												<pre>&lt;div style="margin-bottom:3%"&gt;<br/>&lt;input type="button" class="button alt" style="width:100%" id="miniorange_otp_token_submit" title="Please Enter an Email Address to enable this." value="Click here to verify your Email"&gt;&lt;div id="mo_message" hidden="" style="background-color: #f7f6f7;padding: 1em 2em 1em 3.5em;"&gt;&lt;/div&gt;<br/>&lt;/div&gt;<br/><br/>&lt;p&gt;Verify Code (required)&lt;br /&gt;<br/>	[text* email_verify]&lt;/p&gt;</pre>
											</li>
											<li>Enter the name of the email field below:<br>
												<input class="mo_registration_table_textbox" id="cf7_email_field_key" name="cf7_email_field_key" type="text" value="<?php echo get_option('mo_customer_validation_cf7_email_key'); ?>">
												<br/><i> For Reference : [email* &lt;name of your email field&gt;]</i> 
											</li>
											<li>Click on the Save Button below to save your settings</li>
										</ol>
								</div>
								<p><input type="radio" <?php if(get_option('get_balance') != 1) echo 'disabled'?> id="cf7_contact_phone" class="app_enable" data-toggle="cf7_contact_phone_instructions" name="mo_customer_validation_cf7_contact_type" value="mo_cf7_contact_phone_enable"
									<?php checked( get_option('mo_customer_validation_cf7_contact_type') == "mo_cf7_contact_phone_enable" );?> /><strong>Telefon ile doğrulamayı onayla.</strong>
								</p>
								<div <?php if(get_option('mo_customer_validation_cf7_contact_type') != "mo_cf7_contact_phone_enable") echo 'hidden'; ?> class="mo_registration_help_desc" id="cf7_contact_phone_instructions" >
										Follow the following steps to Telefon ile doğrulamayı onayla. for Contact form 7: 
										<ol>
											<li><a href="<?php echo admin_url().'edit.php?post_type=page'?>" target="_blank">Click Here</a> to see your list of pages.</li>
											<li>Click on the <b>Edit</b> option of the page which has your contact form.</li>
											<li>Add the following short code just below your Contact Form 7 shortcode : <code>[mo_verify_phone]</code> </li>
											<li><a href="<?php echo admin_url().'admin.php?page=wpcf7'?>" target="_blank">Click Here</a> to see your list of Contact Forms.</li>
											<li>Click on the <b>Edit</b> option of your form.</li>
											<li>
												Now place the following code in your form where you wish to show the Verify Email button and field : <br>
												<pre>&lt;p&gt;Phone Number (required)&lt;br /&gt;<br/>	[tel* mo_phone]&lt;/p&gt;<br /><br/>&lt;div style="margin-bottom:3%"&gt;<br/>&lt;input type="button" class="button alt" style="width:100%" id="miniorange_otp_token_submit" title="Please Enter a phone number to enable this." value="Click here to verify your Phone"&gt;&lt;div id="mo_message" hidden="" style="background-color: #f7f6f7;padding: 1em 2em 1em 3.5em;"&gt;&lt;/div&gt;<br/>&lt;/div&gt;<br/><br/>&lt;p&gt;Verify Code (required)&lt;br /&gt;<br/>	[text* phone_verify]&lt;/p&gt;</pre>
											</li>
											<li>Click on the Save Button below to save your settings</li>
										</ol>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<br/>
							<div class="registration_question"><a><b>[ Cannot see your registration form in the list above? Have your own custom registration form? ]</b></a></div>
							<div hidden class="mo_registration_help_desc" >We are actively adding support for more forms. Please contact us using the support form on your right or email us at <b>info@miniorange.com</b>. <br/>While contacting us please include enough information about your registration form and how you intend to use this plugin. We will respond promptly.  </div>
						</td>
					</tr>
					<tr>
						<td colspan="3"><br>
							<h3>Redirect after registration(For Woo-Commerce only)</h3>
							<?php if(get_option('mo_customer_validation_wc_redirect')){ ?>
								<p><strong>Select Page</strong>: <?php wp_dropdown_pages(array('selected' => get_page_by_title( get_option('mo_customer_validation_wc_redirect') )->ID));?>
							<?php }else{ ?>
								<p><strong>Select Page</strong>: <?php wp_dropdown_pages();?>
							<?php }?>
							</p>
						</td>
					</tr>-->
					<tr>
						<td><br>
							<input type="hidden" id="error_message" name="error_message" value="">
							<input type="button" id="ov_settings_button"  title="Lütfen listeden bir form seçin" value="Kaydet" style="float:left; width:100px;margin-bottom:2%;" <?php if(get_option('get_balance') != 1) echo 'disabled'?>
								class="button button-primary button-large" />
							
						</td>
					</tr>
				</table>

		</form>
			
			
	<!--	<form name="f" method="post" action="" id="mo_otp_verification_test">
			<input type="hidden" name="option" value="mo_otp_verification_test" />	
			<input type="submit" id="test_button"  value="Test" style="float:right; width:100px;margin-top:-6%;" <?php //if(!MO_Validation_Utility::mo_customer_validation_is_customer_registered()) echo 'disabled'?>
								class="button button-primary button-large" />
		</form>-->
	</div>
<?php
}

function mo_customer_validation_pricing_info(){
	$plan = MO_Validation_Utility::mo_is_customer_validated();
?>
	<div class="mo_registration_table_layout">
		<?php if(!MO_Validation_Utility::mo_customer_validation_is_customer_registered()) { ?>
			<div style="display:block;margin-top:10px;color:red;background-color:rgba(251, 232, 0, 0.15);padding:5px;border:solid 1px rgba(255, 0, 9, 0.36);">
			Please <a href="<?php echo add_query_arg( array('tab' => 'register'), $_SERVER['REQUEST_URI'] ); ?>">Register or Login with miniOrange</a> to enable OTP Verification.
			</div>
		<?php } ?>
		
		<table class="mo_registration_pricing_table">
		<h2>LICENSING PLANS
			<span style="float:right">
				<input type="button" <?php if(get_option('get_balance') != 1) echo 'disabled'?> name="check_btn" id="check_btn" class="button button-primary button-large" value="Check License" />
				<input type="button" name="ok_btn" id="ok_btn" class="button button-primary button-large" value="OK, Got It" onclick="window.location.href='admin.php?page=mo_customer_validation_settings&tab=settings'" />
			</span>
		<h2>
		<hr>
		<tr style="vertical-align:top;">
			<?php if(!$plan){?>
			<td>
				<div class="mo_registration_thumbnail mo_registration_pricing_free_tab" >
					<h3 class="mo_registration_pricing_header">Free</h3>
					<h4 class="mo_registration_pricing_sub_header">( You are automatically on this plan )<br/><br/></h4>
					<hr>
					<!--<p class="mo_registration_pricing_text">For 1 site - Forever</p><hr>-->
					<p  style="margin-bottom: 31.6%;" class="mo_registration_pricing_text">$0 - One Time Payment<br/><br/>( 10 SMS and 10 Email Transactions )<br/><br/><br/></p>
					<hr>
					<p class="mo_registration_pricing_text">Features:</p>
					<p class="mo_registration_pricing_text" style="margin-bottom:11%;">Email Address Verification<br/>Phone Number Verification<br/><br/><br/><br/></p>
					<hr>
					<p class="mo_registration_pricing_text">Basic Support by Email</p>
				</div>
			</td>
			<?php }?>
			<td>
				<div class="mo_registration_thumbnail mo_registration_pricing_paid_tab">
					<h3 class="mo_registration_pricing_header">Do it yourself</h3>
						<h4 class="mo_registration_pricing_sub_header">
						<?php if(strcmp($plan,MO_Validation_Utility::$bCode)!=0){?>
							<input type="button" style="margin-bottom:3.8%;" <?php if(get_option('get_balance') != 1) echo 'disabled'?> class="button button-primary button-large" onclick="mo2f_upgradeform('wp_otp_verification_basic_plan')" value="Upgrade Now"></input>
						</h4>
						<?php }else{?>
							<input type="button" style="margin-bottom:3.8%;"  <?php if(get_option('get_balance') != 1) echo 'disabled'?> class="button button-primary button-large" onclick="mo2f_upgradeform('otp_recharge_plan')" value="Recharge"></input>
						<?php }?>
					<hr>
					<!--<p class="mo_registration_pricing_text">For 1+ site</p><hr>-->
					<p class="mo_registration_pricing_text"><b>$0 - One Time Payment</b><br/>+
						  <select class="mo-form-control">
						    <option>&nbsp;&nbsp;&nbsp;&nbsp;$10 per 100 transaction*</option>
						    <option>&nbsp;&nbsp;&nbsp;$35 per 500 transaction*</option>
						    <option>&nbsp;&nbsp;&nbsp;$50 per 1k transaction*</option>
						    <option>&nbsp;&nbsp;&nbsp;$100 per 5k transaction*</option>
						    <option>&nbsp;&nbsp;&nbsp;$150 per 10k transaction*</option>
							 <option>&nbsp;&nbsp;&nbsp;$750 per 50k transaction*</option>
						  </select>
						  [ This is for your own SMS/SMTP gateway ]<br/>[ You can refill at anytime ]<br/>[ To use miniOrange SMS/SMTP gateway, click Upgrade Now button** ]<br><br><br>
					</p>
					<hr>
					<p class="mo_registration_pricing_text">Features:</p>
					<p class="mo_registration_pricing_text">
						Email Address Verification
						<br/>Phone Number Verification
						<br/>Custom Email Template
						<br/>Custom SMS Template
						<br/>Custom SMS/SMTP Gateway<br/><br/>
					</p>
					<hr>
					<p class="mo_registration_pricing_text">Basic Support By Email</p>
				</div>
			</td>
		</td>
		<td><div class="mo_registration_thumbnail mo_registration_pricing_free_tab">
				<h3 class="mo_registration_pricing_header">Premium</h3>
				<h4 class="mo_registration_pricing_sub_header">
					<?php if(strcmp($plan,MO_Validation_Utility::$pCode)!=0){?>
						<input type="button" style="margin-bottom:3.8%;"  <?php if(get_option('get_balance') != 1) echo 'disabled'?> class="button button-primary button-large" onclick="mo2f_upgradeform('wp_otp_verification_premium_plan')" value="Upgrade Now"></input>
					<?php }else{?>
						<input type="button" style="margin-bottom:3.8%;"  <?php if(get_option('get_balance') != 1) echo 'disabled'?> class="button button-primary button-large" onclick="mo2f_upgradeform('otp_recharge_plan')" value="Recharge"></input>
					<?php }?>
				</h4>
				<hr>
				<!--<p class="mo_registration_pricing_text">For 1+ site, Setup and Custom Work</p><hr>-->
				<p  class="mo_registration_pricing_text"><b>$0 - One Time Payment</b><br/>+
					  <select class="mo-form-control">
					    <option>&nbsp;&nbsp;&nbsp;&nbsp;$10 per 100 transaction*</option>
						<option>&nbsp;&nbsp;&nbsp;$35 per 500 transaction*</option>
						<option>&nbsp;&nbsp;&nbsp;$50 per 1k transaction*</option>
						<option>&nbsp;&nbsp;&nbsp;$100 per 5k transaction*</option>
						<option>&nbsp;&nbsp;&nbsp;$150 per 10k transaction*</option>
						 <option>&nbsp;&nbsp;&nbsp;$750 per 50k transaction*</option>
					  </select>
					  [ This is for your own SMS/SMTP gateway ]<br/>[ You can refill at anytime ]<br/>[ To use miniOrange SMS/SMTP gateway, click Upgrade Now button** ]
					  
					  <br>+ <br>Custom Integration Charges***<br>
				</p>
				</p>
				<hr>
				<p class="mo_registration_pricing_text">Features:</p>
				<p class="mo_registration_pricing_text">
					Email Address Verification
					<br/>Phone Number Verification
					<br/>Custom Email Template
					<br/>Custom SMS Template
					<br/>Custom SMS/SMTP Gateway
					<br/>Custom Integration/Work
				</p><hr>
				<p class="mo_registration_pricing_text">Premium Support Plans</p>
			</div></td>
		</td>
		</tr>
		
		</table>
		<br>
		<div id="disclaimer" style="margin-bottom:15px;">
			<span style="font-size:15px;">
				*<b>This is for your own SMS/SMTP gateway.</b> If you want to use more than 50k transactions, mail us at <a href="mailto:info@miniorange.com"><b>info@miniorange.com</b></a> or submit a support request using the support form under User <a href="<?php echo admin_url().'admin.php?page=mo_customer_validation_settings&tab=profile'?>">Profile tab</a>.<br/><br/>
				**If you want to <b>use miniorange SMS/SMTP gateway</b>, and your country is not in list, mail us at <a href="mailto:info@miniorange.com"><b>info@miniorange.com</b></a> or submit a support request using the support form under User <a href="<?php echo admin_url().'admin.php?page=mo_customer_validation_settings&tab=profile'?>">Profile tab</a>. We will get back to you promptly.<br><br>
				***<b>Custom integration charges</b> will be applied for supporting a registration form which is not already supported by our plugin. Each request will be handled on a per case basis.				
			</span>
		</div>
		
		<h3>10 Days Return Policy -</h3>
		<p>At miniOrange, we want to ensure you are 100% happy with your purchase.  If the premium plugin you purchased is not working as advertised and you’ve attempted to resolve any feature issues with our support team, which couldn't get resolved. We will refund the whole amount within 10 days of the purchase. Please email us at <a href="mailto:info@miniorange.com">info@miniorange.com</a> for any queries regarding the return policy.<br>
If you have any doubts regarding the licensing plans, you can mail us at <a href="mailto:info@miniorange.com">info@miniorange.com</a> or submit a query using the support form.</p>
		<br>
		
		</div>
		
		 <form style="display:none;" id="mocf_loginform" action="<?php echo get_option( 'mo_customer_validation_host_name').'/moas/login'; ?>" 
		target="_blank" method="post">
			<input type="email" name="username" value="<?php echo get_option('mo_customer_validation_admin_email'); ?>" />
			<input type="text" name="redirectUrl" value="<?php echo get_option( 'mo_customer_validation_host_name').'/moas/initializepayment'; ?>" />
			<input type="text" name="requestOrigin" id="requestOrigin"  />
		</form>
		<form id="mo_ln_form" style="display:none;" action="" method="post">
			<input type="hidden" name="option" value="check_mo_ln" />
		</form>  
		<script>
			function mo2f_upgradeform(planType){
				jQuery('#requestOrigin').val(planType);
				jQuery('#mocf_loginform').submit();
			}
			
		</script>
	</div>
<?php 
}
	
function mo_validation_troubleshoot_info(){
?>
		<div class="mo_registration_table_layout">
	
		<?php if(!MO_Validation_Utility::mo_customer_validation_is_customer_registered()) { ?>
			<div style="display:block;margin-top:10px;color:red;background-color:rgba(251, 232, 0, 0.15);padding:5px;border:solid 1px rgba(255, 0, 9, 0.36);">
			Please <a href="<?php echo add_query_arg( array('tab' => 'register'), $_SERVER['REQUEST_URI'] ); ?>">Register or Login with miniOrange</a> to enable OTP Verification.
			</div>
		<?php } ?>
		<table width="100%">
		<tbody>
		 	<tr><td>
		 		<p>If any section is not opening, press CTRL + F5 to clear cache.<p>
		 		<div class="registration_question"><h3><a>How does this plugin work?</a></h3></div>
		 		<div hidden  class="mo_registration_help_desc">
					<ol>
						<li>On submitting the registration form an Email/SMS with OTP is sent to the email address/mobile number provided by the user.</li>
						<li>Once the OTP is entered, it is verified by our servers.</li>
					</ol>
				</div>
				<hr>
			</td></tr>
			
			<tr><td>	
				<div class="registration_question"><h3><a>My Registration form is missing from the list</a></h3></div>
				<div  hidden  class="mo_registration_help_desc">
					We are actively adding support for more forms. Please contact us using the support form on your right or email us at <b>info@miniorange.com</b>. <br/>While contacting us please include enough information about your registration form and how you intend to use this plugin. We will respond promptly.  
				</div>
				<hr>
			</td></tr>
			<tr><td>
				<div class="registration_question"><h3><a>Is OTP SMS delivered to DND(Do Not Disturb) mobile numbers?</a></h3></div>
				<div hidden class="mo_registration_help_desc">
					 Yes, OTP SMS is even delivered to DND mobile numbers. 
				</div>
				<hr>
			</td></tr>
			<tr><td>
				<div class="registration_question"><h3><a>Is SMS OTP delivered in any part of the world?</a></h3></div>
				<div hidden class="mo_registration_help_desc">
					 Yes, miniOrange SMS gateway delivers SMS all over the world but the pricing on SMS delivery defers from country to country.
				</div>
				<hr>
			</td></tr>
			<tr><td>
				<div class="registration_question"><h3><a>Can I integrate custom SMS/SMTP gateway with the plugin?</a></h3></div>
				<div hidden class="mo_registration_help_desc">
					Yes, you can integrate your custom SMS/SMTP gateway with us. This feature is available after upgrading the plugin. Steps (which can be seen after upgrading) to configure the custom SMS/SMTP gateway are under <a href="<?php echo admin_url().'?page=mo_customer_validation_settings&tab=config'?>">Configuration</a> tab. 
				</div>
				<hr>
			</td></tr>
			
			<tr><td>
				<div class="registration_question"><h3><a>Can I customize Sender ID of SMS?</a></h3></div>
				<div hidden class="mo_registration_help_desc">
					You can customtize Sender ID, if you use your own SMS gateway. Using miniOrange SMS gateway, you can't customize Sender ID of SMS. 
				</div>
				<hr>
			</td></tr>
			<tr><td>
				<div class="registration_question"><h3><a>Can I customize Sender Email of OTP emails?</a></h3></div>
				<div hidden class="mo_registration_help_desc">
					Yes, you can customize Sender Email of OTP emails. Steps (which can be seen after upgrading) to configure the custom Sender Email are under <a href="<?php echo admin_url().'?page=mo_customer_validation_settings&tab=config'?>">Configuration</a> tab. 
				</div>
				<hr>
			</td></tr>
			
			<tr><td>
				<div class="registration_question"><h3><a>Can I customize SMS/Email template?</a></h3></div>
				<div hidden class="mo_registration_help_desc">
					Yes, you can customtize SMS/Email template. This feature is available after upgrading the plugin. Steps (which can be seen after upgrading) to configure the custom SMS/SMTP gateway are under  <a href="<?php echo admin_url().'?page=mo_customer_validation_settings&tab=config'?>">Configuration</a> tab. 
				</div>
				<hr>
			</td></tr>
		 	<tr><td>	
				<div class="registration_question"><h3><a>How do i integrate the plugin with my own custom Registration Form?</a></h3></div>
				<div  hidden  class="mo_registration_help_desc">
					Please contact us using the support form on your right or email us at <b>info@miniorange.com</b>. <br/>While contacting us please include enough information about your registration form and how you intend to use this plugin. We will respond promptly.  
				</div>	
				<hr>
		 	</td></tr>
		 	<tr><td>
				<div class="registration_question"><h3><a>How to enable PHP cURL extension? (Pre-requisite)</a></h3></div>
				<div hidden  class="mo_registration_help_desc">
					cURL is enabled by default but in case you have disabled it, follow the steps to enable it
					<ol>
						<li>Open php.ini(it's usually in /etc/ or in php folder on the server).</li>
						<li>Search for extension=php_curl.dll. Uncomment it by removing the semi-colon( ; ) in front of it.</li>
						<li>Restart the Apache Server.</li>
					</ol>
					For any further queries, please submit a query on right hand side in our <b>Support Section</b>.
				</div>
				<hr>
			</td></tr>
			<tr><td>
				<div class="registration_question"><h3><a>I am getting this error. What do i do?
				<span style="font-size:12px;"><br/>[ curl_setopt(): CURLOPT_FOLLOWLOCATION cannot be activated when an open_basedir is set. ]</a></h3></div>
				<div hidden class="mo_registration_help_desc">
					Just set safe_mode = Off in your php.ini file (it's usually in /etc/ on the server). If that's already off, then look around for the open_basedir in the php.ini file, and change it to open_basedir = .
				</div>
				<hr>
			</td></tr>
		
		 	<tr><td>			
				<div class="registration_question"><h3><a>I did not recieve OTP. What should I do?</a></h3></div>
				<div hidden class="mo_registration_help_desc">
					The OTP is sent as an email to your email address with which you have registered. If you cannot see the email from miniOrange in your mails, please make sure to check your SPAM folder. <br/><br/>If you don't see an email even in SPAM folder, please verify your account using your mobile number. You will get an OTP on your mobile number which you need to enter on the page. If none of the above works, please contact us using the Support form on the right.
				</div>
				<hr>
			</td></tr>
			<tr><td>
				<div class="registration_question"><h3><a>After entering OTP, I get Invalid OTP. What should I do?</a></h3></div>
				<div hidden class="mo_registration_help_desc">
					Use the <b>Resend OTP</b> option to get an additional OTP. Plese make sure you did not enter the first OTP you recieved if you selected <b>Resend OTP</b> option to get an additional OTP. Enter the latest OTP since the previous ones expire once you click on Resend OTP. <br/><br/>If OTP sent on your email address are not working, please verify your account using your mobile number. You will get an OTP on your mobile number which you need to enter on the page. If none of the above works, please contact us using the Support form on the right.
				</div>
				<hr>
			</td></tr>
			<tr><td>
				<div class="registration_question"><h3><a>I forgot the password of my miniOrange account. How can I reset it?</a></h3></div>
				<div hidden class="mo_registration_help_desc">
					There are two cases according to the page you see -<br><br/>
					1. <b>Login with miniOrange</b> screen: You should click on <b>forgot password</b> link. You will get your new password on your email address which you have registered with miniOrange . Now you can login with the new password.<br><br/>
					2. <b>Register with miniOrange</b> screen: Enter your email ID and any random password in <b>password</b> and <b>confirm password</b> input box. This will redirect you to <b>Login with miniOrange</b> screen. Now follow first step.
				</div>
				<hr>
			</td></tr>
			<tr><td>
				<div class="registration_question"><h3><a>How is this plugin better than other plugins available?</a></h3></div>
				<div hidden class="mo_registration_help_desc">
					<ol>
						<li>Verfication of user's Email Address/Mobile Number during registration is a must these days. But what if you do not have your own SMTP/SMS gateway? With our plugin it's not necessary to have your own SMTP/SMS gateway. You can use our own gateways to send OTP over Email/SMS. </li>
						<li>You can even use your own SMS/SMTP Gateway if you choose to do so.</li>
						<li>Each Email is verified for it's authenticity by miniOrange servers.</li>
						<li>Easy and hassle free setup. No SMTP/SMS gateway configuration required. You just need to choose your registration form and you are good to go.</li>
						<li>Customizable Email/SMS Templates.</li>
					</ol>
				</div>
				<hr>
			</td></tr>
		</tbody>
		</table>
		</div>
<?php
}

function mo_form_additional_info($desc_no1,$desc_no2,$additional_field){
?>
	<a  class="form_query" data-desc="<?php echo $desc_no1; ?>"><b>[ What does this mean? ]</b></a>
	<?php if($additional_field){ ?>
		<a class="form_query" data-desc="<?php echo $desc_no2; ?>"> <b>[ You will need to add a Phone Number field manually ]</b></a>
	<?php } ?>
	<div id="<?php echo 'form_query_desc_'.$desc_no1; ?>" hidden class="mo_registration_help_desc" >New users can validate their Email or Phone Number using either Email or Phone Verification. They will be prompted during registration to choose one of the two verification methods.</div>
<?php
}

function mo_validation_extra_settings(){
?>
	<div class="mo_registration_table_layout">
		<?php if(!MO_Validation_Utility::mo_customer_validation_is_customer_registered()) { ?>
			<div style="display:block;margin-top:10px;color:red;background-color:rgba(251, 232, 0, 0.15);padding:5px;border:solid 1px rgba(255, 0, 9, 0.36);">
			Please <a href="<?php echo add_query_arg( array('tab' => 'register'), $_SERVER['REQUEST_URI'] ); ?>">Register or Login with miniOrange</a> to enable OTP Verification.
			</div>
		<?php } ?>
		<table style="width: 100%;">
			<tr>
				<td colspan="3">
					<h3>SMS & EMAIL CONFIGURATION</h3><hr>
				</td>
			</tr>
			<tr>
				<td>
					<b>Look at the sections below to customize the Email and SMS that you receive:</b>
					<ol>
						<li><b><a href="#sms">Custom SMS Template</a> :</b> Change the text of the message that you receive on your phones.</li>
						<li><b><a href="#sms">Custom SMS Gateway</a> :</b> You can configure settings to use your own SMS gateway.</li>
						<li><b><a href="#email">Custom Email Template</a> :</b> Change the text of the email that you receive.</li>
						<li><b><a href="#email">Custom Email Gateway</a> :</b> You can configure settings to use your own Email gateway.</li>
					</ol>
			</tr>
			<tr>
				<td>
					<a class="form_query" data-desc="21"><b>[ How can I change the SenderID/Number of the SMS I receive? ]</b></a>
					<div id="form_query_desc_21" hidden class="mo_registration_help_desc" >
						SenderID/Number is gateway specific. You will need to use your own SMS gateway for this.
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<a class="form_query" data-desc="22"><b>[ How can I change the Sender Email of the Email I receive? ]</b></a>
					<div id="form_query_desc_22" hidden class="mo_registration_help_desc" >
						Sender Email is gateway specific. You will need to use your own Email gateway for this.
					</div>
				</td>
			</tr>
			<tr id="sms">
				<td>
					<h2>SMS Configuration</h2><hr/>
				</td>
			</tr>
			<tr>
				<td>
					<b>Custom SMS Template:</b>
					<div style="padding:2%;background-color: rgba(111, 111, 111, 0.09);">
						<img src="<?php echo MO_Validation_Utility::mo_is_customer_validated() ? plugins_url( 'includes/images/smsTemplate.jpg', __FILE__ ) : plugins_url( 'includes/images/smsTemplateOb.jpg', __FILE__ );?>" />
						<div <?php echo MO_Validation_Utility::mo_is_customer_validated() ? '' : 'hidden' ?> style="text-align:center">
							<input type="button" title="Need to be registered for this option to be available"  value="Change SMS Template" onclick="extraSettings('<?php echo  get_option('mo_customer_validation_host_name') ?>','/moas/showsmstemplate');" class="button button-primary button-large" style="margin-right: 3%;">
						</div>
					</div>
					<b>Custom SMS Gateway:</b>
					<div style="padding:2%;background-color: rgba(111, 111, 111, 0.09);">
						<img src="<?php echo MO_Validation_Utility::mo_is_customer_validated() ? plugins_url( 'includes/images/smsGateway.jpg', __FILE__ ) : plugins_url( 'includes/images/smsGatewayOb.jpg', __FILE__ )?>" />
						<div <?php echo MO_Validation_Utility::mo_is_customer_validated() ? '' : 'hidden' ?> style="text-align:center">
							<input type="button" title="Need to be registered for this option to be available"  value="Change SMS Gateway" onclick="extraSettings('<?php echo  get_option('mo_customer_validation_host_name') ?>','/moas/smsconfig');" class="button button-primary button-large" style="margin-right: 3%;">
						</div>	
					</div>
				</td>
			</tr>
			<tr id="email">
				<td>
					<h2>Email Configuration</h2><hr/>
				</td>
			</tr>
			<tr>
				<td>
					<b>Custom Email Template:</b>
					<div style="padding:2%;background-color: rgba(111, 111, 111, 0.09);">
						<img src="<?php echo MO_Validation_Utility::mo_is_customer_validated() ? plugins_url( 'includes/images/emailTemplate.jpg', __FILE__ ) : plugins_url( 'includes/images/emailTemplateOb.jpg', __FILE__ )?>" />
						<div <?php echo MO_Validation_Utility::mo_is_customer_validated() ? '' : 'hidden' ?> style="text-align:center">
							<input type="button" title="Need to be registered for this option to be available"  value="Change Email Template" onclick="extraSettings('<?php echo  get_option('mo_customer_validation_host_name') ?>','/moas/showemailtemplate');"'' class="button button-primary button-large" style="margin-right: 3%;">
						</div>
					</div>
					<b>Custom Email Gateway:</b>
					<div style="padding:2%;background-color: rgba(111, 111, 111, 0.09);">
						<img src="<?php echo MO_Validation_Utility::mo_is_customer_validated() ? plugins_url( 'includes/images/emailGateway.jpg', __FILE__ ) : plugins_url( 'includes/images/emailGatewayOb.jpg', __FILE__ )?>" />
						<div <?php echo MO_Validation_Utility::mo_is_customer_validated() ? '' : 'hidden' ?> style="text-align:center">
							<input type="button" title="Need to be registered for this option to be available"  value="Change Email Gateway" onclick="extraSettings('<?php echo  get_option('mo_customer_validation_host_name') ?>','/moas/configureSMTP');" class="button button-primary button-large" style="margin-right: 3%;">
						</div>
					</div>
				</td>
			</tr>
		</table>
		<form id="showExtraSettings" action="<?php echo get_option('mo_customer_validation_host_name').'/moas/login'?>" target="_blank" method="post">
	       <input type="hidden" id="extraSettingsUsername" name="username" value="<?php echo get_option('mo_customer_validation_admin_email')?>" />
	       <input type="hidden" id="extraSettingsRedirectURL" name="redirectUrl" value="" />
		</form>
	</div>
<?php
}

