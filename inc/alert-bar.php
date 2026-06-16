<?php
/**
 * CPT alerta_intt: registro, meta box y columnas de administración.
 * Las fechas se almacenan en UTC y se muestran en la zona horaria de WordPress.
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
		'supports'     => [ 'title' ],
		'show_in_rest' => true,
	] );
} );

// ---------- Meta: mensaje ----------

add_action( 'init', function () {
	register_post_meta( 'alerta_intt', 'mensaje', [
		'single'            => true,
		'type'              => 'string',
		'sanitize_callback' => static function ( $value ) {
			return wp_kses( $value, [
				'a' => [ 'href' => true, 'target' => true, 'rel' => true ],
			] );
		},
		'show_in_rest'      => true,
	] );
} );

// ---------- Helpers de conversión de zona horaria ----------

function intt_alerta_utc_a_local( string $utc ): string {
	if ( ! $utc ) return '';
	try {
		$dt = new DateTime( $utc, new DateTimeZone( 'UTC' ) );
		$dt->setTimezone( new DateTimeZone( wp_timezone_string() ) );
		return $dt->format( 'Y-m-d\TH:i' );
	} catch ( Exception $e ) {
		return '';
	}
}

function intt_alerta_local_a_utc( string $local ): string {
	if ( ! $local ) return '';
	try {
		$dt = new DateTime( str_replace( 'T', ' ', $local ), new DateTimeZone( wp_timezone_string() ) );
		$dt->setTimezone( new DateTimeZone( 'UTC' ) );
		return $dt->format( 'Y-m-d H:i:s' );
	} catch ( Exception $e ) {
		return '';
	}
}

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
	$mensaje    = get_post_meta( $post->ID, 'mensaje',          true );
	$inicio_utc = get_post_meta( $post->ID, 'fecha_inicio',     true );
	$expira_utc = get_post_meta( $post->ID, 'fecha_expiracion', true );

	// Convertir UTC almacenado a hora local para el input
	$inicio_input = intt_alerta_utc_a_local( $inicio_utc );
	$expira_input = intt_alerta_utc_a_local( $expira_utc );

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
			<td>
				<input type="datetime-local" name="fecha_inicio" id="fecha_inicio" value="<?php echo esc_attr( $inicio_input ); ?>" />
				<p class="description">Dejar vacío para que sea activa inmediatamente al publicar.</p>
			</td>
		</tr>
		<tr>
			<th><label for="fecha_expiracion">Fecha de expiración</label></th>
			<td>
				<input type="datetime-local" name="fecha_expiracion" id="fecha_expiracion" value="<?php echo esc_attr( $expira_input ); ?>" />
				<p class="description">Dejar vacío para que nunca expire (se controla despublicando el post).</p>
			</td>
		</tr>
		<tr>
			<th><label for="mensaje">Mensaje</label></th>
			<td><textarea name="mensaje" id="mensaje" rows="3" style="width:100%;resize:vertical"><?php echo esc_textarea( $mensaje ); ?></textarea></td>
		</tr>
	</table>
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

	update_post_meta( $post_id, 'mensaje', wp_unslash( $_POST['mensaje'] ?? '' ) );

	// Convertir hora local ingresada a UTC antes de guardar
	foreach ( [ 'fecha_inicio', 'fecha_expiracion' ] as $campo ) {
		$local = sanitize_text_field( $_POST[ $campo ] ?? '' );
		update_post_meta( $post_id, $campo, intt_alerta_local_a_utc( $local ) );
	}

	delete_transient( 'intt_alerta_activa' );
} );

// Invalidar caché cuando el estado del post cambia (publicar, despublicar, eliminar)
add_action( 'transition_post_status', function ( $new, $old, $post ) {
	if ( 'alerta_intt' === $post->post_type ) {
		delete_transient( 'intt_alerta_activa' );
	}
}, 10, 3 );

// ---------- Columnas de administración ----------

add_filter( 'manage_alerta_intt_posts_columns', function ( $cols ) {
	$cols['tipo_alerta']      = 'Tipo';
	$cols['fecha_inicio']     = 'Inicio (hora local)';
	$cols['fecha_expiracion'] = 'Expira (hora local)';
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
			$utc = get_post_meta( $post_id, 'fecha_inicio', true );
			echo esc_html( $utc ? str_replace( 'T', ' ', intt_alerta_utc_a_local( $utc ) ) : '—' );
			break;
		case 'fecha_expiracion':
			$utc = get_post_meta( $post_id, 'fecha_expiracion', true );
			echo esc_html( $utc ? str_replace( 'T', ' ', intt_alerta_utc_a_local( $utc ) ) : 'Sin vencimiento' );
			break;
	}
}, 10, 2 );
