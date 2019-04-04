<?php
    function cinco_alerta_enabled_field(){
        $checked = isset(get_option( 'cinco_alerta' )['exibir']) ? 'checked' : '';
        echo "<input type='checkbox' name='cinco_alerta[exibir]' $checked>";
    }

    function cinco_alerta_titulo_field(){
        $value = get_option( 'cinco_alerta' )['titulo'];
        echo "<input size='150' maxlength='150' type='text' id='titulo' name='cinco_alerta[titulo]' value='$value'>";
    }

    function cinco_alerta_msg1_field(){
        $value = get_option( 'cinco_alerta' )['msg1'];
        echo "<input size='150' maxlength='630' type='text' id='msg1' name='cinco_alerta[msg1]' value='$value'>";
    }

    function cinco_alerta_msg2_field(){
        $value = get_option( 'cinco_alerta' )['msg2'];
        echo "<input size='150' maxlength='630' type='text' id='msg2' name='cinco_alerta[msg2]' value='$value'>";
    }
?>