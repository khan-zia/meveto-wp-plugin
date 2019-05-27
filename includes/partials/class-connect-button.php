<?php
class Connect_To_Meveto_Button {
    public static function connect_button() {
        ?>

        <link rel='stylesheet' id='meveto-login-css'  href='<?=plugins_url() . '/meveto-login/public/css/widget.css'?>' type='text/css' media='all' />

        <div class="widget meveto-login-widget">
            <a href="#" class="meveto-button" onclick="window.open(
                        'https://meveto.com/#/login',
                        'newwindow', 
                        'width=700,height=600'
                        );
                        return false;"
                >
                  Connect to Meveto
              </a>
        </div>
        <?php
    }
}