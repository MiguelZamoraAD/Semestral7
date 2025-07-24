<?php session_start();
require_once '../../class/conexion.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../resourse/css/princ.css">
    <title>Document</title>
</head>
<body>
    <header class="header">
        <div class="container">
            <h1 class="logo">Categorias de Libros</h1>
            <nav class="nav-menu">
                    <button class="user-menu-toggle" aria-label="Abrir menú de navegación">&#9776;</button>
                    <ul class="nav-list">
                        <li ><a href="../company_info.php" >Inicio</a></li>
                        <?php if (isset($_SESSION['tipo']) && strtolower($_SESSION['tipo']) === 'adm'): ?>
                        <li ><a href="../user.php" >Usuario</a></li>
                        <?php endif; ?> 
                        <li ><a href="../studen.php" >Estudiante</a></li>
                        <li ><a href="Books.php" >Libros</a></li>
                        <li ><a href="../CategoryBook.php" >Categorias</a></li>
                    </ul>
            </nav>
        </div>
    </header>
    <h2>Listado de Usuario</h2>
    <div class="buscador-usuarios">
        <input type="text" id="inputBuscar" placeholder="Buscar usuario...">
        <button id="btnLimpiar">Limpiar</button>
    </div>
    <table border="1" id="tablaUsuarios" class="table table-bordered">
        <thead>
            <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Usuario</th>
            <th>Tipo</th>
            <th>Sexo</th>
            <th>HashMagic</th>
            <th>Acciones</th>
            </tr>
        </thead>

            <tbody id="usuariosBody">
                <!-- Aquí se insertarán las filas dinámicamente -->
            </tbody>
    </table>
            <div class="paginacion-usuarios">
                <button class="btnAnterior" id="btnAnterior">&laquo;</button>
                <span class="btn-paginacion activo" id="paginaActual"></span>
                <button class="btnSiguiente" id="btnSiguiente">&raquo;</button>
            </div>
 
            

<div class="formulario-grid">
    <h2>Registro para Usuarios</h2>
        <form id="registroForm">
            <div class="form-group"><input type="text" id="cedula" name="cedula" placeholder="Cédula" required></div>

            <div class="form-group"><input type="text" id="nombre" name="nombre" placeholder="Primer nombre" required></div>
            <div class="form-group"><input type="text" id="segundoN" name="segundoN" placeholder="Segundo nombre"></div>

            <div class="form-group"><input type="text" id="apellido" name="apellido" placeholder="Primer apellido" required></div>
            <div class="form-group"><input type="text" id="segundoA" name="segundoA" placeholder="Segundo apellido"></div>

            <div class="form-group"><input type="date" id="fechaNacimiento" name="fechaNacimiento" required></div>
            <div class="form-group">
                <select id="carrera" name="carrera" required>
                    <option value="">Seleccione carrera</option>
                    <option value="Sistemas Computacionales">Sistemas Computacionales</option>
                    <option value="Ingeniería Industrial">Ingeniería Industrial</option>
                    <option value="Ingeniería Electrónica">Ingeniería Electrónica</option>
                    <option value="Ingeniería Civil">Ingeniería Civil</option>
                    <option value="Ingeniería Mecánica">Ingeniería Mecánica</option>
                    <option value="Ingeniería en Sistemas">Ingeniería en Sistemas</option>
                    <option value="Ingeniería en Telecomunicaciones">Ingeniería en Telecomunicaciones</option>
                    <option value="Ciencias de la Computación">Ciencias de la Computación</option>
                    <option value="Administración de Empresas">Administración de Empresas</option>
                    <option value="Contabilidad">Contabilidad</option>
                    <option value="Psicología">Psicología</option>
                    <option value="Educación">Educación</option>
                    <option value="Derecho">Derecho</option>
                    <option value="Medicina">Medicina</option>
                    <option value="Enfermería">Enfermería</option>
                    <option value="Arquitectura">Arquitectura</option>
                    <option value="Diseño Gráfico">Diseño Gráfico</option>
                </select>
            </div>
            <div class="form-group"><input type="email" id="correo" name="correo" placeholder="Correo (Usuario)" required></div>
            <div class="form-group">
            <select id="tipo" name="tipo" required>
                <option value="">Seleccione tipo</option>
                <option value="adm">Administrador</option>
                <option value="user">Usuario</option>
                <option value="prof">Profesor</option>
            </select>
            </div>

            <div class="form-group">
            <select id="sexo" name="sexo" required>
                <option value="">Seleccione sexo</option>
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
            </select>
            </div>

            <div class="form-group"><input type="password" id="pass1" name="hashMagic" placeholder="Contraseña" required></div>
            <div class="form-group"><input type="password" id="pass2" name="confirmPassword" placeholder="Confirmar contraseña" required></div>

            <!-- Botón que ocupa toda la fila -->
            <div class="form-group full-width">
            <button type="button" id="btnRegistrar">Registrar</button>
        </div>
        </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../JS/user.js"></script>
</body>
</html>

<?php include("../../resourse/include/footer.php");?>