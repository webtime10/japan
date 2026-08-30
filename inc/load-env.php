<?php
/**
 * Minimal .env loader for WordPress (japan.loc root).
 *
 * @param string $root_dir Absolute path to directory containing .env.
 */
function japan_loc_load_env_file( $root_dir ) {
	$env_file = rtrim( $root_dir, '/\\' ) . DIRECTORY_SEPARATOR . '.env';

	if ( ! is_readable( $env_file ) ) {
		return;
	}

	$lines = file( $env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
	if ( ! is_array( $lines ) ) {
		return;
	}

	foreach ( $lines as $line ) {
		$line = trim( (string) $line );

		if ( '' === $line || '#' === $line[0] ) {
			continue;
		}

		$eq_pos = strpos( $line, '=' );
		if ( false === $eq_pos ) {
			continue;
		}

		$name  = trim( substr( $line, 0, $eq_pos ) );
		$value = trim( substr( $line, $eq_pos + 1 ) );

		if ( '' === $name ) {
			continue;
		}

		if (
			( '"' === $value[0] && '"' === substr( $value, -1 ) )
			|| ( "'" === $value[0] && "'" === substr( $value, -1 ) )
		) {
			$value = substr( $value, 1, -1 );
		}

		if ( false !== getenv( $name ) ) {
			continue;
		}

		putenv( $name . '=' . $value );
		$_ENV[ $name ]    = $value;
		$_SERVER[ $name ] = $value;
	}
}
