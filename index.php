<?php
/*
 * VERIFICACION DEL WEBHOOK
*/
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(-1);


//TOQUEN QUE QUERRAMOS PONER 
$token = 'TokenValidacion';
//RETO QUE RECIBIREMOS DE FACEBOOK
$palabraReto = $_GET['hub_challenge'];
//TOQUEN DE VERIFICACION QUE RECIBIREMOS DE FACEBOOK
$tokenVerificacion = $_GET['hub_verify_token'];
//SI EL TOKEN QUE GENERAMOS ES EL MISMO QUE NOS ENVIA FACEBOOK RETORNAMOS EL RETO PARA VALIDAR QUE SOMOS NOSOTROS
if ($token === $tokenVerificacion) {
    echo $palabraReto;
    exit;
}

/*
 * RECEPCION DE MENSAJES
 */
//LEEMOS LOS DATOS ENVIADOS POR WHATSAPP
$respuesta = file_get_contents("php://input");
//CONVERTIMOS EL JSON EN ARRAY DE PHP
$respuesta = json_decode($respuesta, true);
//EXTRAEMOS EL MENSAJE DEL ARRAY
$mensaje = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];
//EXTRAEMOS EL TELEFONO DEL ARRAY
$telefonoCliente = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['from'];
//EXTRAEMOS EL ID DE WHATSAPP DEL ARRAY
$id = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['id'];
//EXTRAEMOS EL TIEMPO DE WHATSAPP DEL ARRAY
$timestamp = $respuesta['entry'][0]['changes'][0]['value']['messages'][0]['timestamp'];
file_put_contents("text4.txt", $id);


//SI HAY UN MENSAJE
if ($mensaje != null) {
    //file_put_contents("text.txt", $mensaje);
    //Aqui se implementan las respuestas con IA. 
    require_once "envia.php";
    $respuesta = conversacion($mensaje);
    enviar($mensaje, $respuesta, $id, $timestamp, $telefonoCliente);
}
function convertirMinNoTilde($mensaje){
    $mensaje = mb_strtolower($mensaje, 'UTF-8');
    $mensaje = str_replace(array('á','é','í','ó','ú','ü'), array('a','e','i','o','u','u') , $mensaje);
    return $mensaje; 
}
function conversacion($mensaje)
{
    $mensaje = convertirMinNoTilde($mensaje); 

    if ($mensaje == convertirMinNoTilde('buenos dias, me puede ayudar con informacion de los paquetes?')) {
        $respuesta = '¡Saludos! Claro, con gusto le ayudaré. ¿Podría proporcionarme su número de identificación, por favor?';
    }

    else if ($mensaje == convertirMinNoTilde('claro, mi cedula es 1234567890') ){
        $respuesta = 'En qué le puedo ayudar señor Jaime Gonzales: \n';
        $respuesta .= '- Información de los paquetes\n'; 
        $respuesta .= '- Paquetes personalizados\n';
        $respuesta .= '- Gestión de visado '
        ;
    }else if($mensaje == convertirMinNoTilde('1234567899')) {
        $respuesta = 'Lo sentimos no fue posible encontrar su identificación, verifíquela e inténtelo nuevamente    '; 
    }

    else if ($mensaje == convertirMinNoTilde('Quiero consultar sobre Medellin en los próximos dos meses')) {
        $respuesta = 'Por supuesto, tenemos varias fechas disponibles. Las salidas son los lunes y jueves a las 9:00 a.m., y los regresos son los miércoles y sábados a las 6:00 p.m. \n';
        $respuesta .= 'En los próximos dos meses, algunas fechas disponibles son: \n';
        $respuesta .= '- Viernes 16 de febrero\n';
        $respuesta .= '- Miércoles 21 de febrero\n';
        $respuesta .= '- Sábado 24 de febrero\n';
        $respuesta .= '- Viernes 1 de marzo\n';
        $respuesta .= '- Miércoles 6 de marzo\n';
        $respuesta .= '- Miércoles 15 de marzo\n';
        $respuesta .= '- Viernes 17 de marzo\n';
        $respuesta .= '- Sábado 25 de marzo\n';
        $respuesta .= '- Lunes 3 de abril\n';
        $respuesta .= '- Martes 11 de abril\n';
        $respuesta .= '- Sábado 15 de abril\n';
    }
    else if ($mensaje == convertirMinNoTilde('cuál es el precio?')) {
        $respuesta = 'Los precios para este paquete son los siguientes: (USD 450 por habitación doble, USD 250 en habitación sencilla)';
    }
    else if ($mensaje == convertirMinNoTilde('me gustaría reservar una habitacion doble para el 15 de marzo')) {
        $respuesta = 'Perfecto, ¿cómo prefiere realizar el pago? ¿Tarjeta o efectivo?';
    }
    else if ($mensaje == convertirMinNoTilde('en efectivo') ){
        $respuesta = 'Excelente. Por favor, envíeme una captura de pantalla del depósito con el valor de (USD 450) al siguiente número: 2201700111.';
    }
    else if ($mensaje == convertirMinNoTilde('listo, ya realicé el depósito')) {
        $respuesta = 'En este momento estamos revisando la solicitud. Le confirmaremos por este medio cuando haya sido validada.';
    }
    else if ($mensaje == convertirMinNoTilde('hay algo más que necesite hacer?')) {
        $respuesta = 'No, en este momento solo estamos esperando la validación. Le confirmaremos tan pronto como sea posible. ¡Que tenga un buen día!';
    }
    else if ($mensaje == convertirMinNoTilde('gracias, hasta luego')) {
        $respuesta = '¡Hasta luego!';
    } else{
        $respuesta = $mensaje; 
    }
    return $respuesta;
}
