=== u-wordpress ===
Contributors: ayobanol@ing.uchile.cl, adi@ing.uchile.cl 
Tags: Universidad de Chile, comunidad, usuarios, login, u-pasaporte
Requires at least: 3.1
Tested up to: 3.1
Stable tag: 1.0

Creación de usuarios en plataforma wordpress con el sistema de verificación institucional de la Universidad de Chile.

== Description ==

Este plugin se ha creado para que las diferentes organizaciones existentes en la Universidad de Chile puedan crear comunidades y generar sistemas de participación en sus sistemas de publicación basandose en los datos y verificación de identidad mediante el sistema institucional de acceso digital de la Universidad de Chile.

El uso de este plugin requiere contactar al Area de Infotecnologias (ADI) de la Facultad de Ciencias Físicas y Matemáticas 'adi@ing.uchile.cl'  para la autorización de uso del servicio pasaporte en su servidor.

El contribuir a este plugin no implica de ninguna manera un apoyo a las políticas del Grupo La Chile Para Todos. Respetando el espiritu del software Libre toda contribución sera revisada y, luego de chequeos, agregada al código. Así mismo, conforme a la Licencia Pública Global, cualquier persona tiene libertad de usar este código y modificarlo como estime conveniente.

== Installation ==

1. Sube la carpeta `u-wordpress` al directorio `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Indica en opciones el servicio correspondiente, ejemplo: "https://www.u-cursos.cl/upasaporte/login?servicio=cei&". En este caso el servicio es 'cei'. El nombre del servicio es entregado por el ADI (adi@ing.uchile.cl).

== Frequently Asked Questions ==

== Screenshots ==

1. None yet.

== Changelog ==

= 1.0 =
1.Existe una página de configuración del plugin, se puede cambiar el estilo del boton de acceso a U-PASAPORTE y se puede ingresar el servicio asociado.
2.Corregido un problema al intentar crear usuarios que no autorizan el envío del email desde el passport o cuando el email ya lo tiene asignado otro usuario de wordpress.
3.Se agrega un mensaje de error y se detienen los procesos si hay problemas en la creación de nuevo ususario, esto impide que se asignen datos erroneos al superusuario de wordpress.

= 0.7 =
1.U-Passport se comunica con wordpress, nuevos usuarios son creados y se les asigna el rol por defecto para nuevos usuarios definido en el panel de administración general de wordpress.
2.El plugin usa las fotos institucionales de la Universidad de Chile en vez de las fotos por defecto de wordpress.
3.Plugin detecta si el usuario ya esta logueado y elimina el boton de acceso para loguearse en este caso.
4.Plugin detecta si no existe foto de ucursos, en ese caso usa la foto por defecto de wordpress.

= 0.01 =
1.Aparece el boton para loguearse en la página.

== Upgrade Notice ==
= 1.0 =
Primera Versión estable del Plugin.

= 0.7 =
Primera versión funcional del Plugin.

= 0.01 =
Primera versión del plugin.

== Known Bugs and Issues ==
Al usar Buddypress las fotos de u-cursos vuelven a ser reemplazadas por gravatar. Hasta el momento la única forma de cambiar este comportamiento ha sido modificando el archivo bp-core-avatars.php en la carpeta /bp-core/ del plugin Buddypress. Para recuperar el comportamiento antiguo es necesario agregar las siguientes líneas en la funcion 'bp_core_fetch_avatar' cerca de la línea 210, despues de 'if ( !$no_grav ) {':

// Skips gravatar check if $no_grav is passed
if ( !$no_grav ) {
	//Compatibilidad con U-Wordpress, para mostrar imagenes de U-PASSPORT
	if ( 'user' == $object ) {
		$fotopassport = get_user_meta( $item_id, 'ufoto', true );
		if( strlen( $fotopassport ) > 0 ) {
			$avatar = "<img alt='' src='data:image/gif;base64,".$fotopassport."' alt='" . $alt . $html_width . $html_height . "'/>'";
			return $avatar;
		}
	}

Favor reportar bugs del plugin a Ambrosio Yobánolo del Real (ayobanol@ing.uchile.cl)
