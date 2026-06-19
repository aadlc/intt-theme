<?php
/**
 * Patrones sincronizados registrados programáticamente.
 *
 * Se crean en la BD la primera vez que carga el tema (si no existen).
 * El cliente los edita en Editor de Sitio → Patrones.
 * Agregar aquí cualquier patrón sincronizado que deba estar siempre disponible.
 */

add_action( 'init', function () {

    $patrones = [
        'intt-redes-sociales' => [
            'titulo'    => 'Redes Sociales',
            'locked'    => true,
            'contenido' => '<!-- wp:social-links {"iconColor":"gris-500","iconColorValue":"#9e9e9e","openInNewTab":true,"size":"has-normal-icon-size","align":"wide","className":"alignwide is-style-logos-only","layout":{"type":"flex","justifyContent":"left","orientation":"horizontal"}} --><ul class="wp-block-social-links has-normal-icon-size has-icon-color alignwide is-style-logos-only"><!-- wp:social-link {"url":"https://www.facebook.com/INTToficial","service":"facebook"} /--><!-- wp:social-link {"url":"https://www.instagram.com/inttoficial/","service":"instagram"} /--><!-- wp:social-link {"url":"https://www.threads.com/@inttoficial","service":"threads"} /--><!-- wp:social-link {"url":"https://www.youtube.com/@inttoficial","service":"youtube"} /--><!-- wp:social-link {"url":"https://www.tiktok.com/@inttoficial1","service":"tiktok"} /--><!-- wp:social-link {"url":"https://x.com/InttContigo","service":"x"} /--></ul><!-- /wp:social-links -->',
        ],
    ];

    $slugs_bloqueados = [];

    foreach ( $patrones as $slug => $datos ) {
        if ( ! get_page_by_path( $slug, OBJECT, 'wp_block' ) ) {
            wp_insert_post( [
                'post_type'    => 'wp_block',
                'post_status'  => 'publish',
                'post_title'   => $datos['titulo'],
                'post_name'    => $slug,
                'post_content' => $datos['contenido'],
                'meta_input'   => [ 'wp_pattern_sync_status' => '' ],
            ] );
        }
        if ( ! empty( $datos['locked'] ) ) {
            $slugs_bloqueados[] = $slug;
        }
    }

    if ( $slugs_bloqueados ) {
        add_filter( 'map_meta_cap', function ( $caps, $cap, $user_id, $args ) use ( $slugs_bloqueados ) {
            if ( $cap !== 'delete_post' || empty( $args[0] ) ) {
                return $caps;
            }
            $post = get_post( $args[0] );
            if ( $post && $post->post_type === 'wp_block' && in_array( $post->post_name, $slugs_bloqueados, true ) ) {
                $caps[] = 'do_not_allow';
            }
            return $caps;
        }, 10, 4 );
    }

}, 99 );
