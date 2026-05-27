<?php
/**
 * Astra Child Theme functions and definitions
 */

function astra_child_enqueue_styles() {
    wp_enqueue_style( 'astra-parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'astra-child-style',
        get_stylesheet_uri(),
        array( 'astra-parent-style' ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'astra_child_enqueue_styles', 20 );

/**
 * Shortcode para formulario de contacto personalizado
 * Uso: [instituto_contact_form]
 */
function instituto_contact_form_shortcode() {
    ob_start();
    ?>
    <form method="POST" class="instituto-contact-form" id="contactForm">
        <div class="form-group">
            <label for="nombre">Nombre *</label>
            <input type="text" id="nombre" name="nombre" required placeholder="Tu nombre">
        </div>

        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" required placeholder="tu@email.com">
        </div>

        <div class="form-group">
            <label for="telefono">Teléfono</label>
            <input type="tel" id="telefono" name="telefono" placeholder="(011) XXXX-XXXX">
        </div>

        <div class="form-group">
            <label for="asunto">Asunto *</label>
            <input type="text" id="asunto" name="asunto" required placeholder="Motivo de contacto">
        </div>

        <div class="form-group">
            <label for="mensaje">Mensaje *</label>
            <textarea id="mensaje" name="mensaje" rows="6" required placeholder="Tu mensaje..."></textarea>
        </div>

        <div class="form-group">
            <label for="carrera">Carrera de interés</label>
            <select id="carrera" name="carrera">
                <option value="">-- Selecciona una carrera --</option>
                <option value="Turismo">Tecnicatura Superior en Turismo</option>
                <option value="Hotelería">Tecnicatura Superior en Hotelería</option>
                <option value="Logística">Tecnicatura Superior en Logística</option>
                <option value="Higiene y Seguridad">Tecnicatura Superior en Higiene y Seguridad en el Trabajo</option>
                <option value="Ciencia de Datos">Ciencia de Datos e Inteligencia Artificial</option>
                <option value="Recursos Humanos">Administración de los Recursos Humanos</option>
                <option value="Energía Eléctrica">Energía Eléctrica con orientación en Digitalización</option>
            </select>
        </div>

        <div class="form-group" style="background-color: transparent; border: none; padding: 0;">
            <label style="display: flex; align-items: center; font-weight: normal;">
                <input type="checkbox" id="privacidad" name="privacidad" required style="width: auto; margin-right: 8px;">
                Acepto la política de privacidad
            </label>
        </div>

        <button type="submit" class="form-submit">Enviar Mensaje</button>
    </form>

    <style>
    .instituto-contact-form {
        max-width: 600px;
        margin: 30px auto;
    }

    .instituto-contact-form .form-submit {
        width: 100%;
        padding: 14px;
        font-size: 16px;
        font-weight: 600;
    }
    </style>

    <script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const nombre = document.getElementById('nombre').value;
        const email = document.getElementById('email').value;
        const telefono = document.getElementById('telefono').value;
        const asunto = document.getElementById('asunto').value;
        const mensaje = document.getElementById('mensaje').value;
        
        const mailtoLink = 'mailto:isft199@gmail.com?subject=' + encodeURIComponent(asunto) + 
                          '&body=' + encodeURIComponent('Nombre: ' + nombre + '\nEmail: ' + email + '\nTeléfono: ' + telefono + '\n\nMensaje:\n' + mensaje);
        
        window.location.href = mailtoLink;
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode( 'instituto_contact_form', 'instituto_contact_form_shortcode' );
