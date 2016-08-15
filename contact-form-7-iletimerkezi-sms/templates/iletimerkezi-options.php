<?php echo '<img src="' . plugins_url( 'images/im-icon.png', dirname( __FILE__ ) ) . '" > '; ?>
<div class="wrap">
  <div class="left-content">
      
    <div class="icon32"><img src="<?php echo plugins_url( 'images/logo_32px_32px.png', dirname( __FILE__ ) ); ?>" /></div>
    <h2>İletimerkezi SMS Ayarları</h2>
    
    <form method="post" action="options.php" id="clockwork_options_form">
    
    <?php
    
    
    settings_fields('iletimerkezi_options');
    do_settings_sections('iletimerkezi');
    submit_button();
    ?>
    
    </form>    
  </div>
</div>