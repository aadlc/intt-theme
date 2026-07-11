<?php
$texto = get_field( 'tramites_archive_intro', 'option' );

if ( empty( $texto ) ) {
    $texto = 'Encuentra el trámite que necesitas y consulta los requisitos para realizarlo.';
}
?>
<p class="has-gris-800-color has-text-color has-heading-4-font-size" style="margin-top:0;margin-bottom:var(--wp--preset--spacing--sp-16)"><?php echo esc_html( $texto ); ?></p>
