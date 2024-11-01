<?php
/*
Plugin Name: U-Wordpress
Plugin URI: http://www.lachileparatodos.cl/proyectos/u-wordpress/
Description: Plugin para autentificar y crear ususarios en wordpress usando el sistema U-PASAPORTE de la Universidad de Chile.
Version: 1.0
Author: La Chile Para Todos
Author URI: http://www.lachileparatodos.cl/
License: GPL2
*/

/*  Copyright 2011 Ambrosio Yobánolo  (email : ayobanol@ing.uchile.cl)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Make sure we don't expose any info if called directly

if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}
function addpassportbanner()  {
	if ( !isset( $_GET['hello'] ) ) {
		if ( !is_user_logged_in() ) {	
			echo "<div id='upasaporte'><a href='https://www.u-cursos.cl/upasaporte/login?servicio=".get_option('UServicio')."&'><img alt='Entrar con U-Pasaporte' src='https://www.u-cursos.cl/upasaporte/d/images/boton.png' height='38' width='225'/></a></div>";
		}
	}
}
function ucursoscss()  {
	if ( !isset( $_GET['hello'] ) ) {
		if ( !is_user_logged_in() ) {	
			echo "<style type='text/css'>".get_option('UCSS')."</style>";
		} else {
			echo "<style type='text/css'>
				#wpadminbar img {
				  height: 16px;
				}
			</style>";
		}
	} else {
		echo "<style type='text/css'>
			#wpadminbar img {
			  height: 16px;
			}
		</style>";
	}
}


function usesion()  {
	if ( isset( $_GET['hello'] ) ) {
		if ( !is_user_logged_in() ) {
			session_id( $_GET['hello'] );
			session_start();
			if ( isset( $_SESSION['rut'] ) ) {
				$user_id = username_exists( $_SESSION['rut'] );			
				if ( !$user_id ) {
					$userpassu = wp_generate_password( 12, false );
					$mailenviado = isset($_SESSION['email']);
					if ( $mailenviado ) {
						if( email_exists($_SESSION['email']) ) {
							$mailyaexiste = '1';
						} else {
							$mailyaexiste = '2';
						}	
					} else {
						$mailyaexiste = '2';					
					}
					if($mailenviado and $mailyaexiste == '2') {
							$user_id = wp_create_user( $_SESSION['rut'], $userpassu, $_SESSION['email'] );
					} else {
						$user_id = wp_create_user( $_SESSION['rut'],  $userpassu, $_SESSION['rut'].'@'.$_SESSION['apellido1'].'.cl' );
					}
					if( is_wp_error( $user_id ) ) {
						echo 'Error prioritario, contacte inmediatamente a ayobanol@ing.uchile.cl detallando los pasos realizados y la respuesta del servidor';
						print_r($user_id);
						exit();
					}
					add_user_meta( $user_id, 'upassw', $userpassu, true );
				} 
				$userdata['ID']=$user_id;
				$userdata['user_nicename']=utf8_encode( $_SESSION['alias'] );
				$userdata['display_name']=utf8_encode( $_SESSION['nombre_completo'] );
				$userdata['first_name']=utf8_encode( $_SESSION['nombre1'] );
				$userdata['last_name']=utf8_encode( $_SESSION['apellido1'] );
				wp_update_user( $userdata );
				if (isset($_SESSION['foto'])) {
					add_user_meta( $user_id, 'ufoto', $_SESSION['foto'], true );
				}
				$creds = array();			
				$creds['user_password'] = get_user_meta( $user_id, 'upassw', true );
				$creds['user_login'] = $_SESSION['rut'];
				$creds['remember'] = true;	
				session_unset();
				session_destroy();
				$user = wp_signon( $creds );
				if ( is_wp_error( $user ) ) {
   				echo $user->get_error_message();
				}
			}
		}
	}
}

function upassfoto( $avatar, $id_or_email, $size, $default, $alt ) {		
	$fotopassport = get_user_meta( $id_or_email->user_id, 'ufoto', true );
	if ( strlen( $fotopassport ) > 0 ) {
		$avatar = "<img alt='' src='data:image/gif;base64,".$fotopassport."' alt='".$alt."' height='".$size."' width='".$size."' />";
	} else {
		unset( $fotopassport );		
		$fotopassport = get_user_meta( $id_or_email, 'ufoto', true );
		if( strlen( $fotopassport ) > 0 && !isset($id_or_email->user_id) ) {
			$avatar = "<img alt='' src='data:image/gif;base64,".$fotopassport."' alt='".$alt."' height='".$size."' width='".$size."' />";
		}
	}
	return $avatar;
}

function uwordpress_install() {
/* Creates new database field */
	add_option("UServicio", 'cei', '', 'yes');
	add_option("UCSS", '#upasaporte {
  position:absolute;
  top:15px;
  right:5%;
}
#upasaporte img {
  border-style:none;
}
#upasaporte #mensaje {
  border: 2px solid green;
}
#upasaporte a {
  text-decoration:none;
}', '', 'yes');


}

function register_uwsettings() { // whitelist options
// Add the section to reading settings so we can add our
 	// fields to it
 	add_settings_section('opciones_uwordpress', 'Configuración', 'seccion1_uwordpress', 'uwordpress-menu');
 	
 	// Add the field with the names and function to use for our new
 	// settings, put it in our new section
 	add_settings_field('uservicio','UServicio', 'uwservicio','uwordpress-menu','opciones_uwordpress');
	add_settings_field('ucss','UCSS', 'uwcss','uwordpress-menu','opciones_uwordpress');
 	
 	// Register our setting so that $_POST handling is done for us and
 	// our callback function just has to echo the <input>
 	register_setting( 'uwordpress-group', 'UServicio', 'sanitize_title' );
	register_setting( 'uwordpress-group', 'UCSS', 'wp_kses' );
}

function seccion1_uwordpress() {
 	echo '<p>Opciones de Configuración de U-Wordpress</p>';
}

function uwservicio() {
 	echo '<input name="UServicio" id="UServicio" type="text"  size="10" value="'.get_option('UServicio').'" class="code" /> Este es él código de servicio provisto por el área de infotecnologías de la Facultad de Ciencias Físicas y Matemáticas de la Universidad de Chile para autorizar el uso de éste plugin.';
}

function uwcss() {
 	echo '<textarea name="UCSS" id="UCSS" cols="50" rows="10" class="code">'.get_option('UCSS').'</textarea> Hoja de estilos para el botón de U-PASAPORTE que linkea a la página de autentificación de usuario.';
}

function uwordpress_remove() {
/* Deletes the database field */
	delete_option('Uservicio');
	delete_option('UCSS');
}
function uwordpress_admin_menu() {
	add_options_page('Opciones U-Wordpress', 'U-Wordpress', 'manage_options','uwordpress-menu', 'uwordpress_html_page');
}

function uwordpress_html_page() {
	?>
	<div class="wrap">
		<h2>Opciones U-Wordpress</h2>
		<form method="post" action="options.php">
			<?php settings_fields( 'uwordpress-group' ); 
			do_settings_sections('uwordpress-menu');?>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Guardar Cambios') ?>" />
			</p>
		</form>
	</div>
	<?php
}

/* Runs when plugin is activated */
register_activation_hook(__FILE__, 'uwordpress_install' );
/* Runs on plugin deactivation*/
register_deactivation_hook( __FILE__, 'uwordpress_remove' );
/* Call the html code */
add_action ( 'admin_menu', 'uwordpress_admin_menu' );
add_action ( 'admin_init', 'register_uwsettings' );
add_action ( 'wp_footer', 'addpassportbanner' );
add_action ( 'wp_head', 'ucursoscss' );
add_action ( 'init', 'usesion' );
add_filter ( 'get_avatar', 'upassfoto', 11, 5 );
?>
