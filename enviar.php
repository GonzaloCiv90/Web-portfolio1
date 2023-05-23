<?php
$nombre = $_POST['nombre'];
$telefono = $_POST['numero'];
$correo = $_POST['correo'];
$tema = $_POST['tema'];
$mensaje = $_POST['mensaje'];

//Validamos el reCaptcha de la API de google
$ip = $_SERVER['REMOTE_ADDR'];
$captcha = $_POST['g-recaptcha-response'];
$secretkey = "secretkey";
    
$respuesta = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretkey&response=$captcha&remoteip=$ip");
$atributos = json_decode($respuesta, TRUE);

if ($atributos['success']) {
    //Incluimos el archivo que contiene la conexión a la base de datos
    include('conexion.php');
    //Hacemos la petición a la base de datos 
    mysqli_query($conexion_bd,"INSERT INTO consulta (id, nombre, telefono, correo, tema, mensaje) VALUES (DEFAULT, '$nombre', '$telefono', '$correo', '$tema', '$mensaje'  )");

    //Formato HTML del correo electrónico 
    $destinatario = "gonzalocivita@gmail.com";
    $asunto = $tema;
    $cuerpo = "
        <html>
            <head>
                <title>".$tema."</title>
            </head>
            <body>
                <h1>Solicitud de contacto</h1>
                <p>
                    Contacto: ".$nombre."<br>
                    Telefon: ".$telefono."<br>
                    Mensaje: ".$mensaje." 
                </p>
            </body>
        </html>";

    //Para el envío en formato HTML
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 

    //Dirección del remitente
    $headers .= "FROM: $nombre <$correo>\r\n";
    //Dirección de la copia 
    $headers .= "Cc: $correo\r\n";
    mail($destinatario, $asunto, $cuerpo, $headers);

    header('Location: index.php?ok_bd');
} else {
    echo "Error en la validación del reCAPTCHA <a href='index.php'>Volver</a>";
}
?>
