
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Módulo de Noticias</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../resourse/css/noti.css" />
</head>
<body>
    <header>
        <h1>Módulo de Noticias:</h1>
        <div class="breadcrumbs">
            You are here: <a href="../company_info.php">Home</a> > <a href="#">Blog</a> > Medium Images
        </div>
    </header>

    <div class="container">
        <main class="main-content" id="noticiasContainer">
            <!-- Aquí se cargarán las noticias dinámicamente -->
        </main>
        
        <aside class="sidebar">
            <div class="sidebar-section">
                <div class="search-box">
                    <input type="text" placeholder="Título..." />
                    <button><span style="font-size: 0.9em;">&#x1F50D;</span></button>
                </div>
                <div class="sidebar-form-group">
                    <input type="text" placeholder="" />
                </div>
                <div class="sidebar-buttons">
                    <button class="btn-search">Buscar</button>
                    <button class="btn-clear">Limpiar</button>
                </div>
            </div>

            <div class="sidebar-section tag-cloud">
                <h3>TAG CLOUD</h3>
                <ul>
                    <li><a href="../page/page2/registrar.php">Agregar una Noticia</a></li>
                    <li><a href="#">Agregar un Evento</a></li>
                    <li><a href="#">Agregar una Noticia Deportiva</a></li>
                </ul>
            </div>

            <div class="sidebar-section categories">
                <h3>CATEGORIES</h3>
                <ul>
                    <li><a href="#">Noticias <span class="count">(555)</span></a></li>
                </ul>
            </div>
        </aside>
    </div>
    <div style="text-align: center; margin: 20px;">
            <button id="btnAnterior">Anterior</button>
            <span id="paginaActual">1</span>
            <button id="btnSiguiente">Siguiente</button>
        </div>

    <script src="../JS/noticiasjs.js"></script>

</body>
</html>
