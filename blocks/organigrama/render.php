<?php
if ( ! function_exists( 'get_field' ) ) return;

$niveles = [
    [ 'slug' => 'estrategico',    'label' => 'Nivel Estratégico',         'layout' => 'una'  ],
    [ 'slug' => 'asesoria-apoyo', 'label' => 'Nivel de Asesoría y Apoyo', 'layout' => '2col' ],
    [ 'slug' => 'sustantivo',     'label' => 'Nivel Sustantivo',           'layout' => '2col' ],
];

foreach ( $niveles as $i => $nivel ) :
    $miembros = get_posts( [
        'post_type'      => 'organigrama',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'tax_query'      => [ [
            'taxonomy' => 'nivel_organigrama',
            'field'    => 'slug',
            'terms'    => $nivel['slug'],
        ] ],
    ] );

    $mt = $i === 0 ? '' : ' style="margin-top:var(--wp--preset--spacing--sp-48)"';
    $clase_titulo = $nivel['layout'] === '2col'
        ? ' intt-nivel-titulo--' . esc_attr( $nivel['slug'] )
        : '';
    ?>

    <h2 class="wp-block-heading<?php echo $clase_titulo; ?>"<?php echo $mt; ?>><?php echo esc_html( $nivel['label'] ); ?></h2>

    <?php if ( empty( $miembros ) ) : ?>

        <p>No hay miembros en este nivel.</p>

    <?php elseif ( $nivel['layout'] === 'una' ) : ?>

        <div class="intt-nivel--estrategico">
            <div class="intt-cadena-central">
                <?php foreach ( $miembros as $miembro ) :
                    $cargo = get_field( 'cargo', $miembro->ID );
                    ?>
                    <div class="intt-miembro-card">
                        <p class="intt-miembro-nombre"><?php echo esc_html( $miembro->post_title ); ?></p>
                        <?php if ( $cargo ) : ?>
                            <p class="intt-miembro-cargo"><?php echo esc_html( $cargo ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    <?php else : ?>

        <?php
        $izq = [];
        $der = [];
        foreach ( $miembros as $miembro ) {
            $columna = get_field( 'columna', $miembro->ID );
            if ( $columna === 'der' ) {
                $der[] = $miembro;
            } else {
                if ( ! $columna ) {
                    echo '<!-- ADVERTENCIA: ' . esc_html( $miembro->post_title ) . ' sin campo columna — colocado en izq por defecto -->';
                }
                $izq[] = $miembro;
            }
        }
        $count = max( count( $izq ), count( $der ) );
        ?>

        <div class="intt-nivel intt-nivel--2col intt-nivel--<?php echo esc_attr( $nivel['slug'] ); ?>">

            <?php for ( $j = 0; $j < $count; $j++ ) :
                $m_izq = $izq[ $j ] ?? null;
                $m_der = $der[ $j ] ?? null;
                if ( $m_izq === null || $m_der === null ) :
                    echo '<!-- ADVERTENCIA: fila ' . ( $j + 1 ) . ' dispare en nivel ' . esc_html( $nivel['slug'] ) . ' — celda vacía emitida -->';
                endif;
                ?>
                <div class="intt-fila">

                    <?php if ( $m_izq ) :
                        $cargo = get_field( 'cargo', $m_izq->ID ); ?>
                        <div class="intt-miembro-card intt-card--izq">
                            <p class="intt-miembro-nombre"><?php echo esc_html( $m_izq->post_title ); ?></p>
                            <?php if ( $cargo ) : ?>
                                <p class="intt-miembro-cargo"><?php echo esc_html( $cargo ); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <div class="intt-miembro-card intt-card--vacia"></div>
                    <?php endif; ?>

                    <div class="intt-fila-eje"></div>

                    <?php if ( $m_der ) :
                        $cargo = get_field( 'cargo', $m_der->ID ); ?>
                        <div class="intt-miembro-card intt-card--der">
                            <p class="intt-miembro-nombre"><?php echo esc_html( $m_der->post_title ); ?></p>
                            <?php if ( $cargo ) : ?>
                                <p class="intt-miembro-cargo"><?php echo esc_html( $cargo ); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <div class="intt-miembro-card intt-card--vacia"></div>
                    <?php endif; ?>

                </div>

            <?php endfor; ?>

        </div>

    <?php endif; ?>

<?php endforeach; ?>
