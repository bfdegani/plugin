<?php
    global $current_user; 
    if($current_user->user_login) {
        echo 'Olá, ' . $current_user->user_login . '!';
    }
?>