<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") { // Esto hace que el código SOLO se ejecute SI se presiona el botón de registro en login.html
    include "connection.php"; //Esto sirve para incluir el archivo de conexión a la base de datos, lo que permite que el script se conecte a la base de datos y realice operaciones como consultas e inserciones.

    $nombre = $_POST["Nombre"] ?? "";
    $apellido = $_POST["Apellido"] ?? "";
    $ci = $_POST["CI"] ?? "";
    $fechaNacimiento = $_POST["FechaNacimiento"] ?? "";
    $email = $_POST["Gmail"] ?? "";
    $pass = $_POST["Contraseña"] ?? ""; //Estas son las variables que almacenan los datos enviados desde el formulario de registro en login.html. Se utilizan para capturar la información ingresada por el usuario, como nombre, apellido, cédula de identidad, fecha de nacimiento, correo electrónico y contraseña. El operador de fusión de null (??) se utiliza para asignar un valor predeterminado vacío en caso de que alguna de las variables no esté presente en la solicitud POST.

    if (
        empty($nombre) || empty($apellido) || empty($ci) || empty($fechaNacimiento) || empty($email) || empty($pass) // Esto hace que el código SOLO se ejecute SI todos los campos están completos. Usando los caracteres ||(OR) y empty() para verificar si alguna de las variables está vacía. Si alguna de ellas está vacía, se ejecuta el bloque de código dentro del if, que muestra un mensaje de error y detiene la ejecución del script con exit().
    ) {
        exit("Completá todos los campos.");
    }

    $emailSeguro = mysqli_real_escape_string($con, $email); // Esto asegura que el correo electrónico sea seguro para la consulta SQL

    $buscar = mysqli_query( $con, "SELECT * FROM usuario WHERE Gmail = '$emailSeguro'"); // Esto busca en la base de datos si el correo electrónico ya existe

    if (mysqli_num_rows($buscar) > 0) { // Esto verifica si el correo electrónico ya existe en la base de datos, De la manera que lo hace es la siguiente: Si la cantidad de filas devueltas por la consulta es mayor a 0, significa que el correo electrónico ya existe en la base de datos. En ese caso, se muestra un mensaje de error y se detiene la ejecución del script con exit(). Esto evita que se intente insertar un nuevo registro con un correo electrónico duplicado.
        exit("Ese correo ya existe."); 
    }

    $passHash = password_hash($pass, PASSWORD_DEFAULT); // Esto encripta la contraseña antes de guardarla en la base de datos, para mayor seguridad. De la manera que lo hace es la siguiente: La función password_hash() toma la contraseña proporcionada por el usuario y la encripta utilizando un algoritmo de hash seguro (en este caso, PASSWORD_DEFAULT). Esto significa que la contraseña no se almacena en texto plano en la base de datos, sino que se guarda como un hash irreversible. Esto mejora la seguridad de las contraseñas almacenadas, ya que incluso si alguien accede a la base de datos, no podrá obtener las contraseñas originales.

    $nombre = mysqli_real_escape_string($con, $nombre);
    $apellido = mysqli_real_escape_string($con, $apellido);
    $ci = mysqli_real_escape_string($con, $ci);
    $fechaNacimiento = mysqli_real_escape_string($con, $fechaNacimiento); // Esto asegura que los demás campos sean seguros para la consulta SQL

    $insertar = mysqli_query($con, "INSERT INTO usuario (Nombre, Apellido, CI, FechaNacimiento, Gmail, Contraseña) VALUES ('$nombre', '$apellido', '$ci', '$fechaNacimiento', '$emailSeguro', '$passHash')" ); // Esto inserta los datos en la base de datos

    if ($insertar) { // Esto verifica si la inserción fue exitosa
        header("Location: menu.html"); // Esto redirige al usuario a la página de menú después de un registro exitoso
        exit();
    }

    exit("Error al registrar: " . mysqli_error($con)); // Esto muestra un mensaje de error si la inserción falla, y proporciona información sobre el error específico devuelto por la base de datos
}
?>
