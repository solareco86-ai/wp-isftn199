<?php
// Script para agregar el shortcode a la página de contacto
define('WP_USE_THEMES', false);
require('./wp-load.php');

// Buscar la página de contacto
$pages = get_pages(array(
    'meta_key' => '_wp_page_template',
    'sort_column' => 'post_title',
));

// Encontrar la página con slug 'contacto'
$contact_page = null;
foreach (get_pages() as $page) {
    if ($page->post_name === 'contacto' || strpos($page->post_title, 'Contacto') !== false) {
        $contact_page = $page;
        break;
    }
}

if ($contact_page) {
    $page_id = $contact_page->ID;
    
    // Obtener contenido actual
    $current_content = get_post_field('post_content', $page_id);
    
    // Agregar el shortcode al final
    $shortcode = '[instituto_contact_form]';
    
    // Si el shortcode ya existe, no agregarlo de nuevo
    if (strpos($current_content, 'instituto_contact_form') === false) {
        $new_content = $current_content . "\n\n" . $shortcode;
        
        // Actualizar la página
        wp_update_post(array(
            'ID' => $page_id,
            'post_content' => $new_content
        ));
        
        echo "✅ Shortcode agregado a la página de contacto (ID: $page_id)";
    } else {
        echo "⚠️ El shortcode ya existe en la página";
    }
} else {
    echo "❌ No se encontró la página de contacto";
}

// Eliminar este archivo por seguridad
unlink(__FILE__);
?>
