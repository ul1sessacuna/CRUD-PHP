<?php
// Conexión a la base de datos
$host = 'mysql-ulisesacuna.alwaysdata.net'; 
$user = '383098_ulisesa'; 
$password = '28638933'; 
$database = 'ulisesacuna_tp3';

$conn = new mysqli($host,$user,$password,$database);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$nombre = "";
$descripcion = "";
$imagen = "";
$update = false;
$id = 0;

// Creaamos el nuevo registro
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $imagen = '';

    // Subi¿e la imagen y la almacena en la carpeta descargas
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen = 'descargas/' . uniqid() . '_' . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen);
    }

    // Insertamos el registro en la base de datos
    $sql = "INSERT INTO registros (nombre, descripcion, imagen) VALUES ('$nombre', '$descripcion', '$imagen')";
    $conn->query($sql);
    header("Location: index.php"); // Redirige a la página para limpiar el formulario
    exit();
}

// Carga los datos para editar
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM registros WHERE id=$id");
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $nombre = $row['nombre'];
        $descripcion = $row['descripcion'];
        $imagen = $row['imagen'];
        $update = true;
    }
}

// Actualiza el registro
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $imagen = 'descargas/' . uniqid() . '_' . basename($_FILES['imagen']['name']);
        move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen);
    }

    $sql = "UPDATE registros SET nombre='$nombre', descripcion='$descripcion', imagen='$imagen' WHERE id=$id";
    $conn->query($sql);
    header("Location: index.php"); // Redirige a la página para limpiar el formulario
    exit();
}

// Elimina el registro
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM registros WHERE id=$id";
    $conn->query($sql);
    header("Location: index.php"); // Redirige a la página después de eliminar
    exit();
}

// Consultamos todos los registros seleccionados
$result = $conn->query("SELECT * FROM registros");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Productos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Formulario de Creación/Edición -->
    
    <h2 ><?php echo $update ? 'Editar registro' : 'Crear nuevo registro';?></h2>
    
    <form class="form" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?php echo $nombre; ?>" required>
        <br>
        <label>Descripción:</label>
        <textarea name="descripcion" required><?php echo $descripcion; ?></textarea>
        <br>
        <label>Imagen:</label>
        <input type="file" name="imagen">
        <?php if ($imagen): ?>
            <br><img src="<?php echo $imagen; ?>" alt="Imagen" width="50">
        <?php endif; ?>
        <br><br>
        <button type="submit" name="<?php echo $update ? 'update' : 'create'; ?>">
            <?php echo $update ? 'Actualizar' : 'Crear'; ?>
        </button>
    </form>


    <!-- Listado de Registros -->
    <h2>Listado de Registros</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Imagen</th>
            <th>Acciones</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['nombre']; ?></td>
            <td><?php echo $row['descripcion']; ?></td>
            <td>
                <?php if ($row['imagen']): ?>
                    <img src="<?php echo $row['imagen']; ?>" alt="Imagen" width="50">
                <?php endif; ?>
            </td>
            <td>
                <a class="editar" href="index.php?edit=<?php echo $row['id']; ?>">Editar</a>
                <a class="eliminar" href="index.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este registro?');">Eliminar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</body>
</html>

<?php $conn->close(); ?>
