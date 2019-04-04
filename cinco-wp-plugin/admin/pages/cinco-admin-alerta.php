<div class="wrap">
    <h1>Cinco Atendimento - Alertas</h1>
    <p><small>Essa configuração depende do plugin <strong>wpfront-notification-bar</strong></small></p>
    <form action='options.php' method='post'>
        <?php
            settings_fields( CINCO_ALERTA_WP_OPTION );
            do_settings_sections( CINCO_ALERTA_WP_OPTION );
            submit_button();
        ?>
    </form>
</div>