<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="/inventario/public/css/layout_styles.css">
    <link rel="stylesheet" href="/inventario/public/css/layout_form.css">
    <?= isset($stylesCss) ? $stylesCss : '' ?>
    <?= isset($stylesLibraries) ? $stylesLibraries :  '' ?>
    <style>
        div.dt-container {
            width: 1050px !important;
            margin: 0 auto !important;
        }
    </style>
</head>

<body>
    <section class="page__section">
        <aside class="sidebar">
            <div class="sidebar-contente">
                <section class="perfil__section">
                    <div class="img__container">
                        <img class="image__user" src="/inventario/public/images/logo_empresa.png" alt="Por definir">
                    </div>
                    <div>
                        <p style="color: black; font-size: 1.1rem;">
                            <?= isset($_SESSION['sede']) ? strtoupper($_SESSION['sede']) : '' ?>
                        </p>
                    </div>
                </section>
                <section class="perfil_section">
                    <?php
                    if (isset($_SESSION['role_user']) && $_SESSION['role_user'] == 1) {
                        echo '<p class="perfil__user-name"><a class="sidebar-sections-links" href="/update-user">' . $_SESSION['username'] . '</a></p>';
                    } else {
                        echo '<p class="perfil__user-name">' . $_SESSION['username'] . '</p>';
                    }
                    ?>
                </section>
                <ul class="categories__list">
                    <li>
                        <a href="#" class="dropdown-toggle link-toggle-menu">Listar</a>
                        <ul class="dropdown-menu">
                            <?php
                            if (isset($_SESSION['role_user']) && $_SESSION['role_user'] == 1) {
                                echo '<li><a class="sidebar-sections-links" href="/inventario"> Inventario</a></li>';
                                echo '<li><a class="sidebar-sections-links" href="/facturas"> Facturas</a></li>';
                                echo '<li><a class="sidebar-sections-links" href="/ventas"> Ventas</a></li>';
                                echo '<li><a class="sidebar-sections-links" href="/validate-descuentos"> Descuentos Vencidos</a></li>';
                            }
                            ?>
                            <li><a class="sidebar-sections-links" href="<?= '/clientes' ?>"> Clientes</a></li>
                            <li><a class="sidebar-sections-links" href="<?= '/marcas' ?>"> Marcas</a></li>
                            <li><a class="sidebar-sections-links" href="<?= '/productos' ?>"> Productos</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="dropdown-toggle link-toggle-menu">Insertar</a>
                        <ul class="dropdown-menu">
                            <li><a class="sidebar-sections-links" href="<?= '/add-marcas' ?>"> Marcas</a></li>
                            <li><a class="sidebar-sections-links" href="<?= '/add-productos' ?>"> Productos</a></li>
                            <li><a class="sidebar-sections-links" href="<?= '/get-add-cantidades' ?>"> Cantidades</a></li>
                        </ul>
                    </li>
                    <?php
                    if (isset($_SESSION['role_user']) && $_SESSION['role_user'] == 1) {

                        echo '<li>
                                    <a href="#" class="dropdown-toggle link-toggle-menu">Actualizar</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="sidebar-sections-links" href="/search-update-marcas"> Marcas</a></li>
                                        <li><a class="sidebar-sections-links" href="/search-update-productos"> Productos</a></li>
                                    </ul>
                                </li>';
                    }
                    ?>
                    <li>
                        <a href="#" class="dropdown-toggle link-toggle-menu">Ventas</a>
                        <ul class="dropdown-menu">
                            <li><a class="sidebar-sections-links" href="<?= '/search-add-venta' ?>"> Ingresar Venta</a></li>
                            <?php
                            if (isset($_SESSION['role_user']) && $_SESSION['role_user'] == 1) {
                                echo '<li><a class="sidebar-sections-links" href="/dashboard"> Registro de Ventas</a></li>';
                            }
                            ?>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="dropdown-toggle link-toggle-menu">Devoluciones</a>
                        <ul class="dropdown-menu">
                            <li><a class="sidebar-sections-links" href="<?= '/search-factura-devolucion' ?>">Insertar Devolucion</a></li>
                            <li><a class="sidebar-sections-links" href="<?= '/list-devoluciones' ?>">Listar Devoluciones</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <section class="logout">
                <a href="<?= '/logout' ?>"><img src="/inventario/public/images/logout.svg" alt="Logout_image"></a>
            </section>
        </aside>
        <main class="main-content">
            <section class="content">
                <?= $content ?>
            </section>
            <footer class="footer">LaGuaca 2025 | By Diego Salcedo Romero
            </footer>
        </main>
    </section>
    <?= isset($librariesHtml) ? $librariesHtml : '' ?>
    <?= isset($scriptsHtml) ? $scriptsHtml : '' ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownToggle = document.querySelectorAll('.dropdown-toggle');

            dropdownToggle.forEach(function(toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const dropdownMenu = this.nextElementSibling;
                    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
                });
            });
        });
    </script>
</body>

</html>