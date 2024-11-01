<?php
// La funcion debe imprimir una URL donde ADI dirigira al usuario en caso de exito o '-1' en caso de error

ini_set( "session.use_cookies",  0 );

$firma = base64_decode( $_POST['firma'] );
unset( $_POST['firma'] );

$public_key = openssl_pkey_get_public( file_get_contents( 'https://www.u-cursos.cl/upasaporte/certificado' ) );
$result     = openssl_verify( array_reduce( $_POST, create_function( '$a,$b', 'return $a.$b;' ) ), $firma, $public_key );

openssl_free_key( $public_key );

// Si el resultado es negativo significa que el mensaje no está siendo enviado por U-Pasaporte y por lo tanto debemos retornar un error.
if( ! $result ) exit( '-1' );

// Si el script llega a este punto, significa que
// ADI valido al usuario con emito y envío la información de este a través del arreglo $_POST.
// Este script por su parte debe validar al usuario (por ejemplo, que cumpla un determinado perfil)
// e imprimir una URL donde ADI dirigirá al usuario.
// Se recomienda crear una sesión en este punto y entregar el id en la URL que se imprime

session_start();
$_SESSION = $_POST;

exit( 'http'.($_SERVER['HTTPS']?'s':'').'://'.$_SERVER['SERVER_NAME'].dirname( $_SERVER['REQUEST_URI'] ).'?hello='.session_id() );

