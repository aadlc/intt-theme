<?php
$ahora  = gmdate( 'Y-m-d H:i:s' ); // UTC — independiente de la zona horaria de WordPress
$cached = get_transient( 'intt_alerta_activa' );

if ( false === $cached ) {
	// fecha_inicio vacía = activa inmediatamente; con valor = comparar contra UTC
	// fecha_expiracion vacía = nunca expira; con valor = comparar contra UTC
	$alertas = get_posts( [
		'post_type'      => 'alerta_intt',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'meta_query'     => [
			'relation' => 'AND',
			[
				'relation' => 'OR',
				[ 'key' => 'fecha_inicio', 'compare' => 'NOT EXISTS' ],
				[ 'key' => 'fecha_inicio', 'value' => '', 'compare' => '=' ],
				[ 'key' => 'fecha_inicio', 'value' => $ahora, 'compare' => '<=', 'type' => 'DATETIME' ],
			],
			[
				'relation' => 'OR',
				[ 'key' => 'fecha_expiracion', 'compare' => 'NOT EXISTS' ],
				[ 'key' => 'fecha_expiracion', 'value' => '', 'compare' => '=' ],
				[ 'key' => 'fecha_expiracion', 'value' => $ahora, 'compare' => '>=', 'type' => 'DATETIME' ],
			],
		],
	] );

	if ( empty( $alertas ) ) {
		set_transient( 'intt_alerta_activa', 'none', 60 );
		return;
	}

	$alerta = $alertas[0];
	$cached = [
		'tipo'      => get_post_meta( $alerta->ID, 'tipo_alerta', true ) ?: 'info',
		'titulo'    => get_the_title( $alerta ),
		'mensaje'   => get_post_meta( $alerta->ID, 'mensaje', true ),
		'alert_key' => $alerta->ID . '-' . strtotime( $alerta->post_modified ),
	];
	set_transient( 'intt_alerta_activa', $cached, 60 );
}

if ( 'none' === $cached ) {
	return;
}

$tipo      = in_array( $cached['tipo'], [ 'info', 'warning', 'emergency' ], true ) ? $cached['tipo'] : 'info';
$titulo    = $cached['titulo'];
$mensaje   = $cached['mensaje'];
$alert_key = $cached['alert_key'];

$iconos = [
	'info'      => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
	'warning'   => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
	'emergency' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>',
];
?>
<aside
	class="intt-alert intt-alert--<?php echo esc_attr( $tipo ); ?>"
	role="alert"
	aria-label="<?php echo esc_attr( $titulo ?: $tipo ); ?>"
	data-alert-key="<?php echo esc_attr( $alert_key ); ?>"
>
	<div class="intt-alert__inner">
		<div class="intt-alert__main">
			<div class="intt-alert__header">
				<span class="intt-alert__icon"><?php echo $iconos[ $tipo ]; // phpcs:ignore WordPress.Security.EscapingOutput ?></span>
				<?php if ( $titulo ) : ?>
					<strong class="intt-alert__title"><?php echo esc_html( $titulo ); ?></strong>
				<?php endif; ?>
			</div>
			<?php if ( $mensaje ) : ?>
				<p class="intt-alert__body"><?php echo wp_kses( $mensaje, [ 'a' => [ 'href' => true, 'target' => true, 'rel' => true ] ] ); ?></p>
			<?php endif; ?>
		</div>
		<button class="intt-alert__close" aria-label="Cerrar alerta">&times;</button>
	</div>
</aside>
