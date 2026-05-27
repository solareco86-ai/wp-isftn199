<?php
// Script para resetear contraseña de admin
// Cambiar la siguiente contraseña a la deseada

define('WP_USE_THEMES', false);
require('./wp-load.php');

$user_id = 1; // ID del usuario admin (generalmente 1)
$new_password = 'admin123'; // Nueva contraseña

wp_set_password($new_password, $user_id);

echo "Contraseña actualizada. Usuario: admin | Contraseña: " . $new_password;

// Eliminar este archivo por seguridad
unlink(__FILE__);
?>
