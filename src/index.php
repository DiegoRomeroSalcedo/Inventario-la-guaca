
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();






// Catgamos las dependencias de composer

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../lib/View.php';

// Actualizmos la última actividad del usuario

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > MAX_INACTIVITY_TIME) {
        // Si la inactividad supera el tiempo máximo permitido
        session_unset();
        session_destroy();
        header('Location: /login');
        exit();
    }

    $_SESSION['last_activity'] = time(); // Actualizar la hora de la última actividad
}

use Dotenv\Dotenv;
use Config\ConfigConnect;
use Views\View\View;
use Proyecto\Controller\AuthController;
use Proyecto\Controller\MarcaController;
use Proyecto\Controller\ProductoController;
use Proyecto\Models\Productos;
use Proyecto\Models\Marcas;
use Proyecto\Models\Inventario;
use Proyecto\Controller\InventarioController;
use Proyecto\Controller\VentasController;
use Proyecto\Controller\DevolucionController;
use Proyecto\Controller\FacturasController;
use Proyecto\Controller\ClientesController;
use Proyecto\Controller\DescuentosController;
use Proyecto\Models\Facturas;

$dotenv = Dotenv::createImmutable(__DIR__ . './../');
$dotenv->load();

$host = $_SERVER['HTTP_HOST'];

if (strpos($host, 'dev.') !== false) {
    $env = 'development';
} else {
    $env = 'production';
}

if ($env === 'development') {
    if (isset($_SESSION['sede']) && $_SESSION['sede'] === 'sincelejo') {
        $config = [
            'dbHost' => $_ENV['DB_HOST_DEV_SIN'],
            'dbName' => $_ENV['DB_NAME_DEV_SIN'],
            'dbUser' => $_ENV['DB_USER_DEV_SIN'],
            'dbPass' => $_ENV['DB_PASS_DEV_SIN']
        ];
    } else {
        $config = [
            'dbHost' => $_ENV['DB_HOST_DEV_SAH'],
            'dbName' => $_ENV['DB_NAME_DEV_SAH'],
            'dbUser' => $_ENV['DB_USER_DEV_SAH'],
            'dbPass' => $_ENV['DB_PASS_DEV_SAH']
        ];
    }
} else {
    if (isset($_SESSION['sede']) && $_SESSION['sede'] === 'sincelejo') {
        $config = [
            'dbHost' => $_ENV['DB_HOST_PROD_SIN'],
            'dbName' => $_ENV['DB_NAME_PROD_SIN'],
            'dbUser' => $_ENV['DB_USER_PROD_SIN'],
            'dbPass' => $_ENV['DB_PASS_PROD_SIN'],
        ];
    } else {
        $config = [
            'dbHost' => $_ENV['DB_HOST_PROD_SAH'],
            'dbName' => $_ENV['DB_NAME_PROD_SAH'],
            'dbUser' => $_ENV['DB_USER_PROD_SAH'],
            'dbPass' => $_ENV['DB_PASS_PROD_SAH']
        ];
    }
}

$dbConnection = ConfigConnect::getInstance($config);
$pdo = $dbConnection->getConnection();

// Instancias e inyecciones
$AuthController = new AuthController($pdo);
$view = new View();
$productos = new Productos($pdo);
$marcas = new Marcas($pdo);
$inventario = new Inventario($pdo);
$facturas = new Facturas($pdo);
$inventarioController = new InventarioController($view, $inventario, $marcas, $productos);
$marcaController = new MarcaController($pdo, $view);
$productoController = new ProductoController($pdo, $view, $productos, $marcas);
$ventaController = new VentasController($pdo, $view, $productos, $marcas);
$facturasController = new FacturasController($pdo, $view);
$devolucionController = new DevolucionController($pdo, $view, $facturas);
$clientesController = new ClientesController($pdo, $view);
$descuentosController = new DescuentosController($pdo, $view);



// Obtener la URI y el metodo de solicitud
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// $requestUri = str_replace(BASE_URL, '', $path);


// Definimos las rutas y los metodos correspondientes

$routes = [
    'GET' => [
        '/login' => [$AuthController, 'login'],
        '/logout' => [$AuthController, 'logout'],
        '/inventario' => [$inventarioController, 'list'],
        '/marcas' => [$marcaController, 'list'],
        '/add-marcas' => [$marcaController, 'addMarca'],
        '/productos' => [$productoController, 'list'],
        '/add-productos' => [$productoController, 'addProducto'],
        '/get-add-cantidades' => [$productoController, 'search'],
        '/add-cantidad-product' => [$productoController, 'addCantidadProducto'],
        '/search-update-marcas' => [$marcaController, 'searchUpdate'],
        '/update-form-marca' => [$marcaController, 'updateMarca'],
        '/search-update-productos' => [$productoController, 'searchUpdateProducto'],
        '/update-form-product' => [$productoController, 'updateProduct'],
        '/search-add-venta' => [$ventaController, 'searchAddVenta'],
        '/ventas' => [$ventaController, 'listVentas'],
        '/get-factura' => [$facturasController, 'renderFactura'],
        '/get-data-factura' => [$facturasController, 'getFacturaData'],
        '/facturas' => [$facturasController, 'listFacturData'],
        '/detalles-facturas' => [$facturasController, 'listDetalleFactura'],
        '/clientes' => [$clientesController, 'getDataCliente'],
        '/search-factura-devolucion' => [$devolucionController, 'searchFacturaDevolucion'],
        '/add-devolucion' => [$devolucionController, 'addDevolucion'],
        '/list-devoluciones' => [$devolucionController, 'listDevoluciones'],
        '/validate-descuentos'     => [$descuentosController, 'listDescuentosVencidos'],
        '/update-user' => [$AuthController, 'updateUser'],
        '/form-update-user' => [$AuthController, 'formUpdate'],
        '/dashboard' => [$ventaController, 'renderDasboard'],
        '/dasboard-ventas' => [$ventaController, 'getdataDashboard']
    ],
    'POST' => [
        '/login' => [$AuthController, 'login'],
        '/inventario' => [$inventarioController, 'searchData'],
        '/add-marcas' => [$marcaController, 'addMarca'],
        '/add-productos' => [$productoController, 'addProducto'],
        '/get-add-cantidades' => [$productoController, 'search'],
        '/add-cantidad-product' => [$productoController, 'addCantidadProducto'],
        '/search-update-marcas' => [$marcaController, 'searchUpdate'],
        '/update-form-marca' => [$marcaController, 'updateMarca'],
        '/search-update-productos' => [$productoController, 'searchUpdateProducto'],
        '/update-form-product' => [$productoController, 'updateProduct'],
        '/search-add-venta' => [$ventaController, 'searchAddVenta'],
        '/ventas' => [$ventaController, 'listVentas'],
        '/finalizar-venta' => [$ventaController, 'finalizarVenta'],
        '/get-factura' => [$ventaController, 'renderFactura'],
        '/get-data-factura' => [$ventaController, 'getFacturaData'],
        '/facturas' => [$facturasController, 'listFacturData'],
        '/detalles-facturas' => [$facturasController, 'listDetalleFactura'],
        '/clientes' => [$clientesController, 'getDataCliente'],
        '/search-factura-devolucion' => [$devolucionController, 'searchFacturaDevolucion'],
        '/add-devolucion' => [$devolucionController, 'addDevolucion'],
        '/list-devoluciones' => [$devolucionController, 'listDevoluciones'],
        '/validate-descuentos'     => [$descuentosController, 'listDescuentosVencidos'],
        '/quitar-descuentos'        => [$descuentosController, 'quitarDescuento'],
        '/update-user' => [$AuthController, 'updateUser'],
        '/form-update-user' => [$AuthController, 'formUpdate'],
        '/dashboard' => [$ventaController, 'renderDasboard'],
        '/dasboard-ventas' => [$ventaController, 'getdataDashboard'],
        '/check-client' => [$clientesController, 'checkExistClient']
    ]
];

// Definimos las rutas protegidas, esto con el fin de evaluar si estan en el requestUri

$routesProtected = [
    '/logout',
    '/inventario',
    '/marcas',
    '/add-marcas',
    '/productos',
    '/add-productos',
    '/get-add-cantidades',
    '/add-cantidad-product',
    '/search-update-marcas',
    '/update-form-marca',
    '/search-update-productos',
    '/update-form-product',
    '/search-add-venta',
    '/search-factura-devolucion',
    '/get-factura',
    '/get-data-factura',
    '/ventas',
    '/facturas',
    '/detalles-facturas',
    '/clientes',
    '/search-factura-devolucion',
    '/add-devolucion',
    '/list-devoluciones',
    '/validate-descuentos',
    '/validate-descuentos',
    '/update-user',
    '/form-update-user',
    '/dashboard',
    '/dasboard-ventas',
    '/check-client'
];

// Verificamos si la ruta actual es una ruta Protegida y si esta logeado el usuario

if (in_array($path, $routesProtected) && (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true)) {
    header('Location: /login');
    exit();
}

// Buscamos la ruta y ejecutamos el método corresponiente

if (isset($routes[$requestMethod][$path])) {
    call_user_func($routes[$requestMethod][$path]);
}
