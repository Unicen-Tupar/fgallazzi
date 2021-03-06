<?php 
	//sleep(1);
	//usleep(100000);
	/**
	 * Enrutador de peticiones
	 * Segun el pedido, instanciara su correspondiente Controlador.
	 * Por defecto sera HomeController.
	 * Si el pedido no se encuentra disponible, se mostrara una pagina 
	 *  informando que no existe tal pagina.
	 *  **/
	
	include_once "controller/config_app.php";
	include_once "controller/acl_controller.php";
	include_once "controller/registry.php";
	include_once "controller/home_controller.php";
	include_once "controller/producto_controller.php";
	include_once "controller/categoria_controller.php";
	include_once "controller/caracteristica_controller.php";
	include_once "controller/carrito_controller.php";
	include_once "controller/usuario_controller.php";
	include_once "view/view_class.php";

	session_start();

	$aclController = AclController::getInstance();
	// de no tener acceso, 
	// identificar el request:
	// si es por ajax devolver un success false
	// caso contrario un redirect al home
	if ($aclController->validarAcceso() === false){
		if ($aclController->isAjax()){
			$view = new View();
			return $view->success(false);
		}else{
			header("Location: index.php?".ConfigApp::$ACTION."=".ConfigApp::$ACTION_HOME);
			exit();
		}
	}
	$aclController->setUser();

	if (!array_key_exists(ConfigApp::$ACTION,$_REQUEST ) || $_REQUEST[ConfigApp::$ACTION] == ConfigApp::$ACTION_HOME){
		// Home del sitio
		$homeController = new HomeController();
		$homeController->home();
		
	}else if (array_key_exists(ConfigApp::$ACTION,$_REQUEST )){
		switch ($_REQUEST[ConfigApp::$ACTION]) {
			case ConfigApp::$ACTION_PRODUCTOS:
				$productoController = new ProductoController();
				$productoController->listadoPorCategoria();
				break;
			case ConfigApp::$ACTION_DETALLE:
				$productoController = new ProductoController();
				$productoController->detalleProducto();
				break;
			case ConfigApp::$ACTION_PUBLICAR:
				$productoController = new ProductoController();
				$productoController->publicarProducto();
				break;
			case ConfigApp::$ACTION_GET_CATEGORIAS:	
				$categoriaController = new CategoriaController();
				$categoriaController->getCategoriasEnComboByAjax();
				break;
			case ConfigApp::$ACTION_CARGAR_PUBLICACION:
				$productoController = new ProductoController();
				$productoController->cargarPublicacion();
				break;
			case ConfigApp::$ACTION_GET_CARACTERISTICAS:
				$caracteristicaController = new CaracteristicaController();
				$caracteristicaController->getAllCaracteristicasPorCategoriaByAjax();
				break;
			case ConfigApp::$ACTION_BUSCADOR:
				$productoController = new ProductoController();
				$productoController->buscarProducto();
				break;
			case ConfigApp::$ACTION_GET_ALL_PRODUCTOS_BY_AJAX:
				$productoController = new ProductoController();
				$productoController->getAllProductosByAjax();
				break;
			case ConfigApp::$ACTION_GET_CARRITO_BY_AJAX:
				$carritoController = new CarritoController();
				$carritoController->carritoCompraByAjax();
				break;
			case ConfigApp::$ACTION_FORM_LOGIN_BY_AJAX:
				$usuarioController = new UsuarioController();
				$usuarioController->formLoginByAjax();
				break;
			case ConfigApp::$ACTION_LOGIN_BY_AJAX:
				$usuarioController = new UsuarioController();
				$usuarioController->loginByAjax();
				break;	
			case ConfigApp::$ACTION_GET_TMP_LISTADO_BY_AJAX:
				$productoController = new ProductoController();
				$productoController->getTmpListadoByAjax();	
				break;
			case ConfigApp::$ACTION_FORM_NUEVO_USUARIO_BY_AJAX:
				$usuarioController = new UsuarioController();
				$usuarioController->formNuevoUsuarioByAjax();	
				break;
			case ConfigApp::$ACTION_ALTA_NUEVO_USUARIO_BY_AJAX:
				$usuarioController = new UsuarioController();
				$usuarioController->altaNuevoUsuarioByAjax();	
				break;
			case ConfigApp::$ACTION_LOGOUT_BY_AJAX:
				$usuarioController = new UsuarioController();
				$usuarioController->logOutByAjax();	
				break;
			case ConfigApp::$ACTION_FORM_LOGIN:
				$usuarioController = new UsuarioController();
				$usuarioController->formLogin();	
				break;
			case ConfigApp::$ACTION_INSERT_PRODUCTO_CARRITO_BY_AJAX:
				$carritoController = new CarritoController();
				$carritoController->insertarProductoAlCarritoByAjax();
				break;
			case ConfigApp::$ACTION_GET_CONTENT_BY_AJAX:
				$view = new View();
				$view->getContents();
				break;
			case ConfigApp::$ACTION_UPDATE_CARRITO_BY_AJAX:
				$carritoController = new CarritoController();
				$carritoController->updateCarritoByAjax();
				break;
			case ConfigApp::$ACTION_CONFIRMAR_COMPRA_BY_AJAX:
				$carritoController = new CarritoController();
				$carritoController->confirmarCompra();
				break;
			case ConfigApp::$ACTION_LIST_ALL_USUARIOS:
				$usuarioController = new UsuarioController();
				$usuarioController->listAllUsuarios();
				break;
			case ConfigApp::$ACTION_LIST_ALL_USUARIOS_BY_AJAX:
				$usuarioController = new UsuarioController();
				$usuarioController->listAllUsuariosByAjax();
				break;
			case ConfigApp::$ACTION_LIST_ALL_PRODUCTOS:
				$productoController = new ProductoController();
				$productoController->listAllProductos();
				break;
			case ConfigApp::$ACTION_LIST_ALL_PRODUCTOS_BY_AJAX:
				$productoController = new ProductoController();
				$productoController->listAllProductosByAjax();
				break;
			case ConfigApp::$ACTION_LIST_ALL_CATEGORIAS:
				$categoriaController = new CategoriaController();
				$categoriaController->listAllCategorias();
				break;
			case ConfigApp::$ACTION_LIST_ALL_CATEGORIAS_BY_AJAX:
				$categoriaController = new CategoriaController();
				$categoriaController->listAllCategoriasByAjax();
				break;
			case ConfigApp::$ACTION_LIST_ALL_SUBCATEGORIAS:
				$categoriaController = new CategoriaController();
				$categoriaController->listAllSubCategorias();
				break;
			case ConfigApp::$ACTION_LIST_ALL_SUBCATEGORIAS_BY_AJAX:
				$categoriaController = new CategoriaController();
				$categoriaController->listAllSubCategoriasByAjax();
				break;
			case ConfigApp::$ACTION_NEW_CATEGORIA:
				$categoriaController = new CategoriaController();
				$categoriaController->newCategoria();
				break;
			case ConfigApp::$ACTION_EDIT_CATEGORIA:
				$categoriaController = new CategoriaController();
				$categoriaController->editCategoria();
				break;
			case ConfigApp::$ACTION_BAJA_CATEGORIA_BY_AJAX:
				$categoriaController = new CategoriaController();
				$categoriaController->bajaCategoriaByAjax();
				break;
			case ConfigApp::$ACTION_LIST_ALL_COMPRAS:
				$carritoController = new CarritoController();
				$carritoController->listAllCompras();
				break;
			case ConfigApp::$ACTION_LIST_ALL_COMPRAS_BY_AJAX:
				$carritoController = new CarritoController();
				$carritoController->listAllComprasByAjax();
				break;
			case ConfigApp::$ACTION_FORM_CONTACTO:
				$usuarioController = new UsuarioController();
				$usuarioController->formContacto();
				break;
			default:
				echo "Pagina no encontrada";
				break;
		}

	}




 ?>