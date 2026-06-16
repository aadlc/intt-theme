<?php
$alerta = intt_get_active_alert();
if ( ! $alerta ) return;

$tipo      = $alerta['tipo'];
$titulo    = $alerta['titulo'];
$mensaje   = $alerta['mensaje'];
$alert_key = $alerta['alert_key'];

// No renderizar si no hay contenido visible
if ( '' === trim( $titulo ) && '' === trim( wp_strip_all_tags( $mensaje ) ) ) {
	return;
}

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
				<span class="intt-alert__icon"><?php echo $iconos[ $tipo ] ?? ''; // phpcs:ignore WordPress.Security.EscapingOutput ?></span>
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
