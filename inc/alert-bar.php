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
	<div class="notice notice-info inline" style="margin:12px 0;">
		<p>
			<strong>¿Cómo funciona?</strong><br>
			Esta alerta aparece en la parte superior de todas las páginas del sitio mientras esté publicada y dentro del rango de fechas configurado.
			El visitante puede cerrarla — la decisión se guarda en su navegador y no vuelve a aparecer a menos que la alerta sea editada.
			<br><br>
			<strong>Título:</strong> encabezado visible de la alerta.<br>
			<strong>Mensaje:</strong> texto opcional. Admite enlaces <code>&lt;a href="..."&gt;</code>.<br>
			<strong>Fecha de inicio:</strong> vacío = activa al publicar.<br>
			<strong>Fecha de expiración:</strong> vacío = sin vencimiento (despublicar el post para desactivarla).
		</p>
	</div>
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
	// Evita bucle infinito: wp_update_post dentro de save_post re-dispara este hook
	static $en_proceso = false;
	if ( $en_proceso ) return;

	if ( ! isset( $_POST['alerta_intt_nonce'] ) || ! wp_verify_nonce( $_POST['alerta_intt_nonce'], 'alerta_intt_meta_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	// Validaciones al publicar
	$publicando   = ( $_POST['post_status'] ?? '' ) === 'publish';
	$ahora_utc    = gmdate( 'Y-m-d H:i:s' );
	$inicio_local = sanitize_text_field( $_POST['fecha_inicio']     ?? '' );
	$expira_local = sanitize_text_field( $_POST['fecha_expiracion'] ?? '' );
	$inicio_utc   = intt_alerta_local_a_utc( $inicio_local );
	$expira_utc   = intt_alerta_local_a_utc( $expira_local );
	$errores      = [];

	if ( $publicando ) {
		// 0. Al menos título o mensaje deben estar presentes
		$titulo_vacio  = empty( trim( $_POST['post_title'] ?? '' ) );
		$mensaje_vacio = empty( trim( $_POST['mensaje']     ?? '' ) );
		if ( $titulo_vacio && $mensaje_vacio ) {
			$errores[] = 'La alerta debe tener al menos un título o un mensaje.';
		}

		// 1. Expiración no puede estar en el pasado
		if ( $expira_utc && $expira_utc < $ahora_utc ) {
			$errores[] = 'La fecha de expiración ya pasó. Corrígela o déjala vacía.';
		}

		// 2. Inicio debe ser anterior a expiración
		if ( $inicio_utc && $expira_utc && $inicio_utc >= $expira_utc ) {
			$errores[] = 'La fecha de inicio debe ser anterior a la fecha de expiración.';
		}

		// 3. Sin solapamiento con otras alertas publicadas
		if ( empty( $errores ) ) {
			$existentes = get_posts( [
				'post_type'      => 'alerta_intt',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'exclude'        => [ $post_id ],
				'fields'         => 'ids',
			] );

			foreach ( $existentes as $id_ex ) {
				$inicio_ex = get_post_meta( $id_ex, 'fecha_inicio',     true );
				$expira_ex = get_post_meta( $id_ex, 'fecha_expiracion', true );

				// No hay solapamiento si el nuevo termina antes de que el otro empiece,
				// o si el otro termina antes de que el nuevo empiece
				$nuevo_antes     = $expira_utc && $inicio_ex && $expira_utc   <= $inicio_ex;
				$existente_antes = $expira_ex  && $inicio_utc && $expira_ex   <= $inicio_utc;

				if ( ! $nuevo_antes && ! $existente_antes ) {
					$errores[] = sprintf(
						'El rango de fechas se solapa con la alerta publicada "%s".',
						get_the_title( $id_ex )
					);
					break;
				}
			}
		}

		if ( ! empty( $errores ) ) {
			$en_proceso = true;
			wp_update_post( [ 'ID' => $post_id, 'post_status' => 'draft' ] );
			$en_proceso = false;
			set_transient( 'intt_alerta_error_' . $post_id, implode( ' · ', $errores ), 60 );
			return;
		}
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

// Aviso de error de validación al publicar
add_action( 'admin_notices', function () {
	$screen = get_current_screen();
	if ( ! $screen || 'alerta_intt' !== $screen->post_type ) {
		return;
	}
	$post_id = get_the_ID() ?: ( $_GET['post'] ?? 0 );
	$error   = get_transient( 'intt_alerta_error_' . $post_id );
	if ( $error ) {
		delete_transient( 'intt_alerta_error_' . $post_id );
		echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $error ) . '</p></div>';
	}
} );

// Invalidar caché cuando el estado del post cambia (publicar, despublicar, trash, untrash)
add_action( 'transition_post_status', function ( $new, $old, $post ) {
	if ( 'alerta_intt' === $post->post_type ) {
		delete_transient( 'intt_alerta_activa' );
	}
}, 10, 3 );

// Invalidar caché al eliminar permanentemente
add_action( 'deleted_post', function ( $post_id ) {
	if ( get_post_type( $post_id ) === 'alerta_intt' ) {
		delete_transient( 'intt_alerta_activa' );
	}
} );

// Script en <head> para ocultar la alerta antes de que el cuerpo se pinte (evita flash al navegar)
add_action( 'wp_head', function () {
	$alerta = intt_get_active_alert();
	if ( ! $alerta ) return;
	$key = 'intt-alert-dismissed-' . $alerta['alert_key'];
	echo '<script>try{if(localStorage.getItem(' . wp_json_encode( $key ) . ')){document.documentElement.classList.add("intt-alert-dismissed");}}catch(e){}</script>' . "\n";
}, 1 );

// ---------- Consulta de alerta activa ----------

function intt_get_active_alert(): ?array {
	$cached = get_transient( 'intt_alerta_activa' );

	// Validar contenido del transient (puede estar corrupto o sobrescrito por otro plugin)
	if (
		false !== $cached &&
		'none' !== $cached &&
		( ! is_array( $cached ) || ! isset( $cached['tipo'], $cached['titulo'], $cached['mensaje'], $cached['alert_key'] ) )
	) {
		delete_transient( 'intt_alerta_activa' );
		$cached = false;
	}

	if ( false === $cached ) {
		$ahora   = gmdate( 'Y-m-d H:i:s' );
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
					[ 'key' => 'fecha_inicio', 'value' => '', 'compare' => '=' ],
					[ 'key' => 'fecha_inicio', 'value' => $ahora, 'compare' => '<=', 'type' => 'DATETIME' ],
				],
				[
					'relation' => 'OR',
					[ 'key' => 'fecha_expiracion', 'value' => '', 'compare' => '=' ],
					[ 'key' => 'fecha_expiracion', 'value' => $ahora, 'compare' => '>=', 'type' => 'DATETIME' ],
				],
			],
		] );

		if ( empty( $alertas ) || ! ( $alertas[0] instanceof WP_Post ) ) {
			set_transient( 'intt_alerta_activa', 'none', HOUR_IN_SECONDS );
			return null;
		}

		$alerta = $alertas[0];
		$cached = [
			'tipo'      => get_post_meta( $alerta->ID, 'tipo_alerta', true ) ?: 'info',
			'titulo'    => get_the_title( $alerta ),
			'mensaje'   => get_post_meta( $alerta->ID, 'mensaje', true ),
			'alert_key' => $alerta->ID . '-' . strtotime( $alerta->post_modified ),
		];
		set_transient( 'intt_alerta_activa', $cached, HOUR_IN_SECONDS );
	}

	if ( 'none' === $cached ) {
		return null;
	}

	return [
		'tipo'      => in_array( $cached['tipo'], [ 'info', 'warning', 'emergency' ], true ) ? $cached['tipo'] : 'info',
		'titulo'    => is_string( $cached['titulo'] )    ? $cached['titulo']    : '',
		'mensaje'   => is_string( $cached['mensaje'] )   ? $cached['mensaje']   : '',
		'alert_key' => is_string( $cached['alert_key'] ) ? $cached['alert_key'] : '',
	];
}

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
