<?php
$terms = get_terms( [
    'taxonomy'   => 'tipo_tramite',
    'orderby'    => 'name',
    'order'      => 'ASC',
    'hide_empty' => false,
] );

if ( is_wp_error( $terms ) || empty( $terms ) ) {
    echo '<p>No hay tipos de trámite registrados.</p>';
    return;
}

$current_term = is_tax( 'tipo_tramite' ) ? get_queried_object() : null;
?>
<ul class="intt-estado-list">
<?php foreach ( $terms as $term ) :
    $url = get_term_link( $term );
    if ( is_wp_error( $url ) ) continue;
    $current = $current_term && $current_term->term_id === $term->term_id;
?>
    <li<?php echo $current ? ' class="current-cat"' : ''; ?>>
        <a href="<?php echo esc_url( $url ); ?>"<?php echo $current ? ' aria-current="page"' : ''; ?>>
            <?php echo esc_html( $term->name ); ?>
        </a>
    </li>
<?php endforeach; ?>
</ul>
