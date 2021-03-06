<?php 
	include_once "controller_class.php";
	include_once "categoria_controller.php";
	include_once "model/categoria_model.php";
	include_once "model/producto_model.php";
	include_once "view/producto_view.php";
	include_once "paginador_controller.php";

	/**
	 * HomeController
	 * 
     */
	class ProductoController extends Controller
	{
		
		/**
		 * Constructor
		 * */
		function __construct()
		{
			parent::__construct();
			$this->model = new ProductoModel();
			$this->view = new ProductoView();
			
		}

		/**
		 * Atributos
		 * */

		private $view;
		private $model;

		// PUBLIC FUNCTIONS //

		/**
		 * visualizara un listado de productos dado por la categoria
		 * */

		public function listadoPorCategoria(){
			

			//Verificar que la id de categoria es correcta
			$categoriaController = new categoriaController();
			$idCategoria = $this->getDataRequest(ConfigApp::$ID_CATEGORIA);
			$dataMenu = $categoriaController->getByCategoria($idCategoria);
			$allCategorias = $categoriaController->getCategorias();
			if (!$categoriaController->verifCategoriaId($idCategoria)) {
				echo "categoria incorecta";
				exit();
			}
			

			//Recuperar los productos de la base
			//$data = $this->model->getAllByCategoria($idCategoria);
			
			$cant_elementos = 5; // cantidad de elementos por pagina
			$cant_paginas = 10; // cantidad de paginas en el paginador
			
			$arr = $this->getPage($cant_paginas,$cant_elementos,$idCategoria);
			$page = $arr['page'];
			$ini  = $arr['ini'];
			$fin  = $arr['fin'];
			$nPaginas = $arr['nPaginas'];

			$offset = $this->getOffset($page,$cant_elementos);

			$data = $this->model->getAllByCategoriaPaginado($idCategoria, $offset, $cant_elementos);
			
			if (count($data) > 0){
				//Mostrar la vista con los productos de forma paginada
				$params = array(
					"productos" => $data,
					"dataMenu" => $dataMenu,
					"allCategorias" => $allCategorias,
					"nPagina_ini" => $ini,
					"nPagina_fin" => $fin,
					"nPagina_max" => $nPaginas,
					"page" => $page,
					"id" => $idCategoria
					);
				$this->view->listadoPorCategoria($params);
			}else{
				//Mostrar la vista que no se han encontrado productos
				$params = array(
					"dataMenu" => $dataMenu,
					"allCategorias" => $allCategorias,
					"id" => $idCategoria
					);
				$this->view->listadoVacio($params);
			}
			
		}

		public function detalleProducto(){
			$id_producto = $this->getDataRequest(ConfigApp::$ID_PRODUCTO);
			if (!$this->model->verificarId($id_producto)){
				echo "producto inexistente";
				exit();
			}
			$data = $this->model->getById($id_producto);
			$caracteristicas = $this->model->getCaracteristicasByProducto($id_producto);
			$data = $data[0];
			if (count($caracteristicas) > 0){
				$caracteristicas[] = array('v_nombre' => "Precio",
										   'v_valor' => "$".$data['f_precio']);
			}
			$this->setVisitados($id_producto);
			$params = array(
				'data' => $data,
				'caracteristicas' => $caracteristicas
				);
			$this->view->detalleProducto($params);
		}

		public function publicarProducto(){
			$this->view->publicarProducto();
		}

		public function cargarPublicacion(){
			try{
				if (!$info = $this->getDataRequest('info')){
					throw new Exception("Error Processing Request", 1);
				}
				$id_usuario = $this->getDataSession('id');

				$caracteristicas = $this->getDataRequest('caracteristicas');
				
				$imagenes = $this->getDataRequest('imagenes');

				$nombreFile = uniqid('img_prod');
				
				$dataInfo = array(
					'v_nombre' => $info['nombre'], 
					'v_descripcion' => $info['descripcion'],
					'f_precio' => $info['precio'],
					'v_img_path' => 'img/'. $nombreFile,
					'id_categoria' => $info['sub_categoria'],
					'id_usuario' => $id_usuario);

				$idProducto = $this->model->add($dataInfo);

				if ($idProducto) {
					move_uploaded_file($_FILES['imagenes']['tmp_name']['file_1'], 'img/'.$nombreFile);
					if (is_array($caracteristicas)){
						foreach ($caracteristicas AS $id_caracteristica => $value){
							$dataCXP = array('id_producto' => $idProducto, 
										  	 'id_caracteristica' => $id_caracteristica, 
										     'v_valor' => $value);
							$this->model->addCaracteristicaXProducto($dataCXP);
						}
					}
			    }
		    }catch (Exception $ex)
		    {
		    	return $this->view->success(false);
		    }
		    return $this->view->success(true);
		}

		public function getAllProductosByAjax(){
			$buscar_txt = $this->getDataRequest(ConfigApp::$BUSCAR_TXT);
			$buscar_txt = trim($buscar_txt);
			$buscar_txt = $this->sacarEspaciosEnBlanco($buscar_txt);
			$buscar_txt = explode(" ",$buscar_txt);
			if (count($buscar_txt) == 1){
				$buscar_txt = $buscar_txt[0];
			}
			$cant_elementos = 10; // cantidad de elementos por pagina
			$cant_paginas = 5; // cantidad de paginas en el paginador
			
			$arr = $this->getPage($cant_paginas,$cant_elementos,false,$buscar_txt);
			$page = $arr['page'];
			$ini  = $arr['ini'];
			$fin  = $arr['fin'];
			$nPaginas = $arr['nPaginas'];

			$offset = $this->getOffset($page,$cant_elementos);
			$data = $this->model->getAll($buscar_txt,$offset, $cant_elementos);
			$params = array(
					"productos" => $data,
					"nPagina_ini" => $ini,
					"nPagina_fin" => $fin,
					"nPagina_max" => $nPaginas,
					"page" => $page
					);
			$this->view->json($params);
		}

		public function buscarProducto(){
			$buscar_txt = $this->getDataRequest(ConfigApp::$BUSCAR_TXT);
			$buscar_txt = trim($buscar_txt);
			$buscar_txt = $this->sacarEspaciosEnBlanco($buscar_txt);
			
			$params = array(
					"txt_buscar" => $buscar_txt
					);
			$this->view->buscarProducto($params);
		}

		public function getTmpListadoByAjax(){
			$this->view->getTmpListadoByAjax();
		}

		//PRIVATE FUNCTIONS //
		private function getPage($cant_paginas,$cant_elementos,$idCategoria = false,$buscar_txt = false){

			$nPaginas = $this->countPage($cant_elementos,$idCategoria,$buscar_txt);

			if ($nPaginas == 0){
				return array('page' => 1, 'ini'=>1, 'fin'=>1, 'nPaginas' => 1);
			}

			$page = $this->getDataRequest("page");
			
			$ini = 1;
			$fin = $cant_paginas;
			if (!$page || $page <= 0){
				$page = 1;
			}
			if ($page != 1){
				$ini = $this->getDataRequest("ini");
				$fin = $this->getDataRequest("fin");
				if ((!$ini || !$fin) && $page > $cant_paginas){
					$ini = $page - floor($cant_paginas/2);
					$fin = $ini + $cant_paginas -1;
				}
			}

			if ($page > $nPaginas){
				$page = $nPaginas;
			}
			
			if ($page == $fin || $page == $ini){
				$ini = $page - floor($cant_paginas/2);
				$fin = $ini + $cant_paginas -1;
			}
			if ($fin > $nPaginas){
				$fin = $nPaginas;
				$ini = ($nPaginas - $cant_paginas +1) < 1 ? 1 : $nPaginas - $cant_paginas +1;
			}
			if ($ini < 1){
				$ini = 1;
				$fin =  ($cant_paginas > $nPaginas) ? $nPaginas : $cant_paginas;
			}

			return array('page' => $page, 'ini'=>$ini, 'fin'=>$fin, 'nPaginas' => $nPaginas);
		}

		private function countPage($cant,$idCategoria=false,$buscar_txt=false){
			$count = 0;
			if ($idCategoria){
				$count = $this->model->count($idCategoria);
			}else if ($idCategoria == false && $buscar_txt){
				$count = $this->model->count(false,$buscar_txt);
			}
			$nPaginas = ceil($count / $cant);
			return $nPaginas;
		}

		private function getOffset($page, $cant){
			$offset = ($page - 1 ) * $cant;

			return $offset;

		}

		private function sacarEspaciosEnBlanco($texto){
			$i = 0;
			while ($i < strlen($texto)){
				if (substr($texto,$i,1) == " " &&
					substr($texto,$i+1,1) == " "){
					$a = substr($texto, 0,$i);
					$b = substr($texto, $i+1, strlen($texto) - strlen($a));
					$texto = $a . $b;
				}else{
					$i++;
				}
			}
			return $texto;
		}
		
		/*llama a el procedimiento de la base para incrementar el nVisitado del producto*/
		private function setVisitados($id_producto){
			$this->model->setVisitados($id_producto);
		}



		/**
		 * FUNCIONES ADMIN
		 * */

		public function listAllProductos(){
			//$data = $this->model->listAllUsuarios();
			$this->view->listAllProductos();
		}
		
		public function listAllProductosByAjax(){
			$paginador = new PaginadorController();
			$paginador->setTabla('view_producto');
			$paginador->setCols(array('id_producto','v_nombre','v_descripcion','f_precio','v_nombre_vendedor','n_visitado'));
			$paginador->setCant($this->getDataRequest('cant'));
			//$paginador->setWhere(array('id_usuario'=>2));
			$paginador->setPage($this->getDataRequest('page'));
			$paginador->setTxt($this->getDataRequest('txt'));
			
			$data = $paginador->getPage();

			$cantPages = $paginador->getCountPages();
			$page = $paginador->getNPage();

			$json = array(
				'success' => true,
				'rows' => $data,
				'cant_pages' => $cantPages,
				'page' => $page,
				);
			return $this->view->json($json);
		}
		
	}

 ?>