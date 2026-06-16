<?php
/**
 * CPT alerta_intt: registro, meta box y columnas de administración.
 */

add_action( 'init', function () {
	register_post_type( 'alerta_intt', [
		'labels'       => [
			'name'               => 'Alertas',
			'singular_name'      => 'Alerta',
			'add_new_item'       => 'Nueva alerta',
			'edit_item'          => 'Editar alerta',
			'view_item'          => 'Ver alerta',
			'search_items'       => 'Buscar alertas',
			'not_found'          => 'No se encontraron alertas.',
			'not_found_in_trash' => 'No hay alertas en la papelera.',
		],
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => true,
		'menu_icon'    => 'dashicons-warning',
		'supports'     => [ 'title', 'excerpt' ],
		'show_in_rest' => true,
	] );
} );

// ---------- Meta box ----------

add_action( 'add_meta_boxes', function () {
	add_meta_box(
		'alerta_intt_meta',
		'Configuración de la alerta',
		'intt_alerta_meta_box_html',
		'alerta_intt',
		'normal',
		'high'
	);
} );

function intt_alerta_meta_box_html( $post ) {
	wp_nonce_field( 'alerta_intt_meta_save', 'alerta_intt_nonce' );

	$tipo       = get_post_meta( $post->ID, 'tipo_alerta',      true ) ?: 'info';
	$inicio     = get_post_meta( $post->ID, 'fecha_inicio',     true );
	$expiracion = get_post_meta( $post->ID, 'fecha_expiracion', true );

	// Convertir de 'YYYY-MM-DD HH:MM:SS' a 'YYYY-MM-DDTHH:MM' para datetime-local
	$inicio_input     = $inicio     ? str_replace( ' ', 'T', substr( $inicio,     0, 16 ) ) : '';
	$expiracion_input = $expiracion ? str_replace( ' ', 'T', substr( $expiracion, 0, 16 ) ) : '';

	$tipos = [
		'info'      => 'Información',
		'warning'   => 'Advertencia',
		'emergency' => 'Emergencia',
	];
	?>
	<table class="form-table">
		<tr>
			<th><label for="tipo_alerta">Tipo</label></th>
			<td>
				<select name="tipo_alerta" id="tipo_alerta">
					<?php foreach ( $tipos as $valor => $etiqueta ) : ?>
						<option value="<?php echo esc_attr( $valor ); ?>" <?php selected( $tipo, $valor ); ?>>
							<?php echo esc_html( $etiqueta ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th><label for="fecha_inicio">Fecha de inicio</label></th>
			<td><input type="datetime-local" name="fecha_inicio" id="fecha_inicio" value="<?php echo esc_attr( $inicio_input ); ?>" /></td>
		</tr>
		<tr>
			<th><label for="fecha_expiracion">Fecha de expiración</label></th>
			<td><input type="datetime-local" name="fecha_expiracion" id="fecha_expiracion" value="<?php echo esc_attr( $expiracion_input ); ?>" /></td>
		</tr>
	</table>
	<p class="description">El <strong>Título</strong> del post es el encabezado de la alerta. El <strong>Extracto</strong> es el cuerpo del mensaje.</p>
	<?php
}

add_action( 'save_post_alerta_intt', function ( $post_id ) {
	if ( ! isset( $_POST['alerta_intt_nonce'] ) || ! wp_verify_nonce( $_POST['alerta_intt_nonce'], 'alerta_intt_meta_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$tipo = sanitize_text_field( $_POST['tipo_alerta'] ?? 'info' );
	if ( ! in_array( $tipo, [ 'info', 'warning', 'emergency' ], true ) ) {
		$tipo = 'info';
	}
	update_post_meta( $post_id, 'tipo_alerta', $tipo );

	// Convertir 'YYYY-MM-DDTHH:MM' a 'YYYY-MM-DD HH:MM:SS' para comparación DATETIME
	foreach ( [ 'fecha_inicio', 'fecha_expiracion' ] as $campo ) {
		$valor = sanitize_text_field( $_POST[ $campo ] ?? '' );
		if ( $valor ) {
			$valor = str_replace( 'T', ' ', $valor ) . ':00';
		}
		update_post_meta( $post_id, $campo, $valor );
	}
} );

// ---------- Columna de estado en la lista de posts ----------

add_filter( 'manage_alerta_intt_posts_columns', function ( $cols ) {
	$cols['tipo_alerta']      = 'Tipo';
	$cols['fecha_inicio']     = 'Inicio';
	$cols['fecha_expiracion'] = 'Expira';
	return $cols;
} );

add_action( 'manage_alerta_intt_posts_custom_column', function ( $col, $post_id ) {
	$etiquetas = [ 'info' => 'Información', 'warning' => 'Advertencia', 'emergency' => 'Emergencia' ];
	switch ( $col ) {
		case 'tipo_alerta':
			$tipo = get_post_meta( $post_id, 'tipo_alerta', true ) ?: 'info';
			echo esc_html( $etiquetas[ $tipo ] ?? $tipo );
			break;
		case 'fecha_inicio':
			echo esc_html( get_post_meta( $post_id, 'fecha_inicio', true ) ?: '—' );
			break;
		case 'fecha_expiracion':
			echo esc_html( get_post_meta( $post_id, 'fecha_expiracion', true ) ?: '—' );
			break;
	}
}, 10, 2 );
