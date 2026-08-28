<?php
/**
 * Bloque intt/biblioteca-list.
 *
 * Renderiza las 6 secciones de la biblioteca en orden fijo (definido en
 * INTT_CATEGORIAS_DOCUMENTO). Dentro de cada sección los documentos van A-Z.
 * Secciones sin documentos se omiten.
 */

if ( ! defined( 'INTT_CATEGORIAS_DOCUMENTO' ) ) return;
if ( ! function_exists( 'get_field' ) )         return;

$icono_url = get_template_directory_uri() . '/assets/images/pdf-icon.svg';

echo '<div class="intt-biblioteca">';

// La constante define el orden fijo y el slug de cada sección. El nombre
// mostrado en el <h2> se lee del término en BD para que los cambios hechos
// en WP Admin → Documentos → Categorías se reflejen sin tocar código.
foreach ( INTT_CATEGORIAS_DOCUMENTO as $slug => $_nombre_semilla ) :

    $term = get_term_by( 'slug', $slug, 'categoria_documento' );
    if ( ! $term || is_wp_error( $term ) ) continue;
    $nombre = $term->name;

    $documentos = get_posts( [
        'post_type'      => 'documento',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'tax_query'      => [
            [
                'taxonomy' => 'categoria_documento',
                'field'    => 'term_id',
                'terms'    => $term->term_id,
            ],
        ],
    ] );

    if ( empty( $documentos ) ) continue;
?>
    <section class="intt-biblioteca__seccion">

        <h2 class="wp-block-heading has-heading-3-font-size intt-biblioteca__titulo"><?php echo esc_html( $nombre ); ?></h2>

        <div class="intt-biblioteca__lista">

            <?php foreach ( $documentos as $documento ) :
                $archivo = get_field( 'archivo', $documento->ID );
                if ( empty( $archivo['url'] ) ) continue;

                $autor   = get_field( 'autor', $documento->ID );
                $tamano  = ! empty( $archivo['filesize'] ) ? size_format( (int) $archivo['filesize'] ) : '';
                $titulo  = get_the_title( $documento );
                $pdf_url = $archivo['url'];

                $meta = [];
                if ( ! empty( $autor ) )  $meta[] = 'Autor: ' . $autor;
                if ( ! empty( $tamano ) ) $meta[] = $tamano;
            ?>
            <article class="intt-doc-item">

                <span class="intt-doc-item__icono" aria-hidden="true">
                    <img src="<?php echo esc_url( $icono_url ); ?>" alt="" width="40" height="40">
                </span>

                <div class="intt-doc-item__body">
                    <h3 class="wp-block-heading has-heading-5-font-size intt-doc-item__titulo">
                        <a href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $titulo ); ?></a>
                    </h3>
                    <?php if ( ! empty( $meta ) ) : ?>
                    <p class="intt-doc-item__meta has-body-2-font-size"><?php echo esc_html( implode( ' · ', $meta ) ); ?></p>
                    <?php endif; ?>
                </div>

                <div class="wp-block-button intt-doc-item__accion">
                    <a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( $pdf_url ); ?>" target="_blank" rel="noopener" download>Descargar</a>
                </div>

            </article>
            <?php endforeach; ?>

        </div>
    </section>
<?php endforeach; ?>

<?php echo '</div>';
