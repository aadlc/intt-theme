<?php
$terms = get_terms( [
    'taxonomy'   => 'estado',
    'orderby'    => 'name',
    'order'      => 'ASC',
    'hide_empty' => false,
] );

if ( is_wp_error( $terms ) || empty( $terms ) ) {
    echo '<p>No hay estados registrados.</p>';
    return;
}

$current_term = is_tax( 'estado' ) ? get_queried_object() : null;
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
            <span class="count">(<?php echo absint( $term->count ); ?>)</span>
        </a>
    </li>
<?php endforeach; ?>
</ul>
