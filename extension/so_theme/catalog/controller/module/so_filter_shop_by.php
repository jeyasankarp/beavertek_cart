<?php
namespace Opencart\Catalog\Controller\Extension\SoTheme\Module;
require_once (DIR_EXTENSION.'so_theme/admin/view/template/soconfig/class/soconfig.php');
class SoFilterShopBy extends \Opencart\System\Engine\Controller {	
	public function index($setting) {
		
		$this->load->language('extension/so_theme/module/so_filter_shop_by','',$this->config->get('config_language_catalog'));
		$data['heading_title'] = $this->language->get('heading_title');
		$obj_lang = $this->language;
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('extension/so_theme/module/so_filter_shop_by');
		$this->load->model('tool/image');

		if($setting['disp_pro_price'])
		{
			$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_filter_shop_by/css/nouislider.css');
			$this->document->addScript('extension/so_theme/catalog/view/javascript/so_filter_shop_by/js/nouislider.js');
		}
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_filter_shop_by/css/style.css');
	
	// Get data
		$default = array(
			'disp_title_module'		=> '1',
			'class_suffix'			=> '',
			'disp_pro_price'		=> '1',
			'disp_search_text'		=> '1',
			'character_search'		=> '3',
			'disp_rating'			=> '1',
			'disp_reset_all'		=> '1',
			'disp_manu_all'			=> '1',
			'disp_subcategory'		=> '1'
		);
		// Get all attribute
		$disp_attributes = array();
		$disp_attributes_group = array();
		$attributes =  $this->model_extension_so_theme_module_so_filter_shop_by->getAttributes(array('sort'=>'a.sort_order'));
		if(!empty($attributes)){
			foreach($attributes as $item)
			{
				$disp_attributes["disp_att_id_".$item['attribute_id']] = 1;
				$disp_attributes_group["disp_att_group_id_".$item['attribute_group_id']] = 1;
			}
			$default = array_merge($default,$disp_attributes); // Array config display attribute
			$default = array_merge($default,$disp_attributes_group); // Array config display attribute group
		}
		// Get all options
		$disp_options = array();
		$options_arr = $this->model_extension_so_theme_module_so_filter_shop_by->getOptions();
		if(!empty($options_arr)){
			foreach($options_arr as $item)
			{
				$disp_options["disp_opt_id_".$item['option_id']] = 1;
			}
			$default = array_merge($default,$disp_options); // Array config display option
		}
		// Get all manufacturer
		$disp_manu = array();
		$manufacturers =  $this->model_extension_so_theme_module_so_filter_shop_by->getManufacturers(array('sort'=>'sort_order'));
		if(!empty($manufacturers)){
			foreach($manufacturers as $item)
			{
				$disp_manu["disp_manu_id_".$item['manufacturer_id']] = 1;
			}
			$default = array_merge($default,$disp_manu); // Array config display manufacturer
		}
		
		// Set data in database => $data
		$data = array_merge($default,$setting);
		$data['config_language'] = $this->config->get('config_language');
	
		if (isset($setting['module_description'][$this->config->get('config_language_id')])) {
			$data['head_name'] 			= html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['head_name'], ENT_QUOTES, 'UTF-8');
		}else{
			$data['head_name']  		= $setting['head_name'];
		}
		$data['disp_attributes_group']	= array_merge($disp_attributes_group,$setting);
		$data['disp_attributes']		= array_merge($disp_attributes,$setting);
		$data['disp_options']			= array_merge($disp_options,$setting);
		$data['disp_manu']				= array_merge($disp_manu,$setting);
		
		// Get Category list
		$products_arr_id		= array();
		$option_all 			= "";
		$attribute_all 			= "";
		$manufacturer_all 		= "";
		$subcategory_all 		= "";
		$category_id			= "";
		if (isset($this->request->get['route']) && isset($this->request->get['path']) && $this->request->get['route'] == 'product/category') {
            $cate_path = $this->request->get['path'];   
			$cate_id_arr = explode("_",$cate_path);
			$category_id = $cate_id_arr[count($cate_id_arr)-1];
			if(isset($this->request->get['subcate_id'])){
				$category_id = $this->request->get['subcate_id'];
			}
			$filter_data = array(
				'filter_category_id'  => $category_id
			);
			$products_arr_info = $this->model_catalog_product->getProducts($filter_data);
			if(count($products_arr_info) > 0 )
			{
				foreach($products_arr_info as $item)
				{
					$products_arr_id[] = $item['product_id'];
				}
				$option_all 		= $this->model_extension_so_theme_module_so_filter_shop_by->getAllOptions($products_arr_id);
				$attribute_all 		= $this->model_extension_so_theme_module_so_filter_shop_by->getAllAttributes($products_arr_id);
				$manufacturer_all 	= $this->model_extension_so_theme_module_so_filter_shop_by->getAllManufacturerId($products_arr_id);
			}
			$subcategory_all = $this->model_extension_so_theme_module_so_filter_shop_by->getAllSubCategory($category_id);
		}
		$data['opt_id'] = $data['att_id'] = $data['manu_id']  = $data['subcate_id'] =  array();
		$data['text_search'] 		= "";
		if(isset($this->request->get['opt_id'])){
			$data['opt_id'] = $this->request->get['opt_id'];
		}
		if(isset($this->request->get['att_id'])){
			$data['att_id'] = $this->request->get['att_id'];
		}
		if(isset($this->request->get['manu_id'])){
			$data['manu_id'] = $this->request->get['manu_id'];
		}
		if(isset($this->request->get['subcate_id'])){
			$data['subcate_id'] = $this->request->get['subcate_id'];
		}
		if(isset($this->request->get['search'])){
			$data['text_search'] = $this->request->get['search'];
		}
		
		$data['options_all'] 		= $option_all;
		$data['attribute_all'] 		= $attribute_all;
		$data['manufacturer_all'] 	= $manufacturer_all;
		$data['subcategory_all'] 	= $subcategory_all;
		if (isset($this->request->get['path']))
			$data['category_id_path'] 	= $this->request->get['path'];
		else
			$data['category_id_path'] 	= '';
		
		$data['condition_search'] = $setting['condition_search_option'];
		// Get Price Product
		$minPrice = $maxPrice = 0;
		
		if(count($products_arr_id) > 0)
		{
			$product_data = $this->model_extension_so_theme_module_so_filter_shop_by->getAllProducts($category_id);
			$minPrice = $product_data[0]['price'];
			foreach($product_data as $item)
			{		
                if($item) {
					if($item['price'] < $minPrice)
					{
						$minPrice = $item['price'];
					}
					if($item['price'] > $maxPrice)
					{
						$maxPrice = $item['price'];
					}					
				}			
			}
		}
		
		$data['products_arr_id'] = implode(',',$products_arr_id);
		$data['minPrice'] = $data['minPrice_new'] = $minPrice;
		$data['maxPrice'] = $data['maxPrice_new'] = $maxPrice;
		
		if(isset($this->request->get['minPrice'])){
			if (!filter_var($this->request->get['minPrice'],FILTER_VALIDATE_FLOAT) || $this->request->get['minPrice'] < 0){
				$data['minPrice_new'] = $data['minPrice'];
			}else{
				$data['minPrice_new'] = $this->request->get['minPrice'];
			}
		}
		if(isset($this->request->get['maxPrice'])){
			if (!filter_var($this->request->get['maxPrice'],FILTER_VALIDATE_FLOAT) || $this->request->get['maxPrice'] < 0){
				$data['maxPrice_new'] = $data['maxPrice'];
			}else{
				$data['maxPrice_new'] = $this->request->get['maxPrice'];
			}
		}
		
		$data['obj_lang'] =  $this->language;
		$http = $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';
		$data['url'] = $http . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$data['theme_config']	= $this->config->get('theme_default_directory');

		$data['opt_id']	= '';
		if (isset($_GET['opt_id']))
			$data['opt_id']	= $_GET['opt_id'];

		$data['att_id']	= '';
		if (isset($_GET['att_id']))
			$data['att_id']	= $_GET['att_id'];

		$data['manu_id']	= '';
		if (isset($_GET['manu_id']))
			$data['manu_id']	= $_GET['manu_id'];

		$data['subcate_id']	= '';
		if (isset($_GET['subcate_id']))
			$data['subcate_id']	= $_GET['subcate_id'];

		if (isset($_GET['sort'])) {
			$data['sort']	= $_GET['sort'];
		}

		if (isset($_GET['order'])) {
			$data['order']	= $_GET['order'];
		}

		if (isset($_GET['limit'])) {
			$data['limit']	= $_GET['limit'];
		}

		if (isset($_GET['page'])) {
			$data['page']	= $_GET['page'];
		}
		
		// Get Currency 
		$this->load->model('localisation/currency');
		$data['currencies'] = "$";
		$results_currencies = $this->model_localisation_currency->getCurrencies();
		if(!empty($results_currencies)){
			foreach ($results_currencies as $result) {
				if(isset($this->session->data['currency']) && ($this->session->data['currency'] == $result['code']))
				{
					if($result['symbol_left'] != "")
					{
						$data['currencies'] = $result['symbol_left'];
					}else{
						$data['currencies'] = $result['symbol_right'];
					}
					
				}
			}
		}

		// caching
		$use_cache = (int)$setting['use_cache'];
		$cache_time = (int)$setting['cache_time'];
		$folder_cache = DIR_CACHE.'so/Filter_shop_by/';
		if(!file_exists($folder_cache))
			mkdir ($folder_cache, 0777, true);
		if (!class_exists('Cache_Lite'))
		    require_once (DIR_EXTENSION . 'so_theme/system/library/so/filter_shop_by/Cache_Lite/Lite.php');

		$options = array(
			'cacheDir' => $folder_cache,
			'lifeTime' => $cache_time
		);
		$Cache_Lite = new \Cache_Lite($options);
		if ($use_cache){
			$this->hash = md5( serialize(array($this->config->get('config_language'), $this->session->data['currency'], $setting)));
			$_data = $Cache_Lite->get($this->hash);
			if (!$_data) {
				$_data = $this->load->view('extension/so_theme/module/so_filter_shop_by/default', $data);
				$Cache_Lite->save($_data);
				return  $_data;
			} else {
				return  $_data;
			}
		}else{
			if(file_exists($folder_cache))
				$Cache_Lite->_cleanDir($folder_cache);
			
			return $this->load->view('extension/so_theme/module/so_filter_shop_by/default', $data);
		}
	}
	
	public function filter_data(){
		$this->load->model('setting/setting');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('catalog/review');
		$this->load->model('extension/so_theme/module/so_filter_shop_by');
		$opt_value_id = $att_value_id = $manu_value_id = $minPrice = $maxPrice = $text_search = $subcate_value_id = "";
		$url = '';
		if(isset($this->request->post['opt_value_id']) && $this->request->post['opt_value_id'] != ''){
			$opt_value_id = $this->request->post['opt_value_id'];
			$url .= '&opt_id='.$opt_value_id;
		}
		if(isset($this->request->post['att_value_id']) && $this->request->post['att_value_id'] != ''){
			$att_value_id = $this->request->post['att_value_id'];
			$url .= '&att_id='.$att_value_id;
		}
	
		if(isset($this->request->post['product_arr_all'])){
			$product_arr_all = $this->request->post['product_arr_all'];
		}
		if(isset($this->request->post['manu_value_id']) && $this->request->post['manu_value_id'] != ''){
			$manu_value_id = $this->request->post['manu_value_id'];
			$url .= '&manu_id='.$manu_value_id;
		}
		
		if(isset($this->request->post['minPrice'])){
			$minPrice = round($this->request->post['minPrice']);
			$url .= '&minPrice='.$minPrice;
		}
		
		if(isset($this->request->post['maxPrice'])){
			$maxPrice = round($this->request->post['maxPrice']);
			$url .= '&maxPrice='.$maxPrice;
		}
		
		if(isset($this->request->post['text_search']) && $this->request->post['text_search'] != ''){
			$text_search = $this->request->post['text_search'];
		}
		
		if(isset($this->request->post['subcate_value_id']) && $this->request->post['subcate_value_id'] != ''){
			$subcate_value_id = $this->request->post['subcate_value_id'];
		}
		$grid_view ='';
		if(isset($this->request->post['grid_view']) && $this->request->post['grid_view'] != ''){
			$grid_view = $this->request->post['grid_view'];
		}
		
		
		if (isset($this->request->post['category_id_path'])) {
			$path = '';
			$parts = explode('_', (string)$this->request->post['category_id_path']);
			$category_id = (int)array_pop($parts);
		} else {
			$category_id = 0;
		}
		
		$product_arr 		= isset($_POST['product_arr_all']) ? $_POST['product_arr_all'] : array();
		$product_data		= array();
		$data['products'] 	= array();
		$products_arr_id 	= array();
		$minPrice_new = $maxPrice_new = 0;
		$condition_search = isset($_POST['condition_search']) ? $_POST['condition_search'] : "";

		if (isset($this->request->post['category_id_path'])) {
			$path = $this->request->post['category_id_path'];
		}
		else {
			$path = 0;
		}

		if (isset($this->request->post['sort_by'])) {
			$sort_by = $this->request->post['sort_by'];
		}
		else {
			$sort_by = 'p.sort_order';
		}

		if (isset($this->request->post['order_by'])) {
			$order_by = $this->request->post['order_by'];
		}
		else {
			$order_by = 'ASC';
		}

		if (isset($this->request->post['p_limit'])) {
			$limit = $this->request->post['p_limit'];
		}
		else {
			$limit = $this->config->get('config_pagination');
		}

		if (isset($this->request->post['ajax_page'])) {
			$page = $this->request->post['ajax_page'];
		} else {
			$page = 1;
		}

		$start = ((int)$page - 1) * (int)$limit;
		$product_data = $this->model_extension_so_theme_module_so_filter_shop_by->getProducts($opt_value_id,$att_value_id,$manu_value_id,$text_search,$minPrice,$maxPrice,$subcate_value_id,$category_id,$condition_search, $sort_by, $order_by, $start, $limit);
		if(empty($product_data) && count($product_data) <= 0){
			
			$start = 0;
			$product_data = $this->model_extension_so_theme_module_so_filter_shop_by->getProducts($opt_value_id,$att_value_id,$manu_value_id,$text_search,$minPrice,$maxPrice,$subcate_value_id,$category_id,$condition_search, $sort_by, $order_by, $start, $limit);
			
		}		
		
		if($product_data != "" && count($product_data) > 0){
			
			$minPrice_new = $maxPrice_new = $product_data[0]['price'];

			
			foreach ($product_data as $result) {
				
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				}
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				
				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}

				$this->load->model('catalog/category');
				
				$description = '';
						
				/*======Image Galleries=======*/
        		$data['image_galleries'] = array();
				$image_galleries = $this->model_catalog_product->getImages($result['product_id']);
				foreach ($image_galleries as $image_gallery) {
					$data['image_galleries'][] = array(
						'cart' => $this->model_tool_image->resize($image_gallery['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height')),
						'thumb' => $this->model_tool_image->resize($image_gallery['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'))
					);
				}
				$data['first_gallery'] = array(
						'cart' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height')),
						'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'))
				);
        		/*======Check New Label=======*/
				if ((float)$result['special']) $discount = '-'.round((($result['price'] - $result['special'])/$result['price'])*100, 0).'%';
        		else  $discount = false;
        		
				
    			$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$result['reviews']);

				if ($result['quantity'] <= 0) {
					$this->load->model('localisation/stock_status');

					$stock_status_info = $this->model_localisation_stock_status->getStockStatus($result['stock_status_id']);

					if ($stock_status_info) {
						$stock_status = $stock_status_info['name'];
					} else {
						$stock_status = '';
					}
				} elseif ($this->config->get('config_stock_display')) {
					$stock_status = $result['quantity'];
				} else {
					$stock_status = $this->language->get('text_instock');
				}

				$product_data = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'),
					'description' => $description,
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],				
					'href'        => str_replace('&amp;', '&', $this->url->link('product/product', 'language=' . $this->config->get('config_language') . '&product_id=' . $result['product_id'])),
					'href_quickview'        => str_replace('&amp;', '&', $this->url->link('extension/soconfig/quickview', 'language=' . $this->config->get('config_language') . '&product_id='.$result['product_id'] )),
					'image_galleries'       => $data['image_galleries'],
					'first_gallery'       => $data['first_gallery'],
	        		'discount'  => $discount,
					'stock_status'  => $stock_status,
					'reviews'  => $data['reviews'],	                	               
					'quantity'  => $result['quantity'],
				);
				
				$data['products'][] = $this->load->controller('product/thumb', $product_data);
				
				if($result['price'] < $minPrice_new)
				{
					$minPrice_new = $result['price'];
				}
				if($result['price'] > $maxPrice_new)
				{
					$maxPrice_new = $result['price'];
				}
			}
			$products_arr_info = $this->model_catalog_product->getProducts();
			foreach($products_arr_info as $item) {
				array_push($products_arr_id, $item['product_id']);
			}			
		}
		
		$filter_data = array(
			'opt_value_id'	=> $opt_value_id,
			'att_value_id'	=> $att_value_id,
			'manu_value_id'	=> $manu_value_id,
			'text_search'	=> $text_search,
			'minPrice'	=> $minPrice,
			'maxPrice'	=> $maxPrice,
			'subcate_value_id'	=> $subcate_value_id,
			'category_id'	=> $category_id,
			'condition_search'	=> $condition_search,
			'sort_by'	=> $sort_by,
			'order_by'	=> $order_by,
			'start'	=> $start,
			'limit'	=> $limit
		);
		$product_total = $this->model_extension_so_theme_module_so_filter_shop_by->getTotalProducts($filter_data);
		

		$results = sprintf($this->language->get('text_pagination'), ($product_total) ? ((int)($page - 1) * (int)$limit) + 1 : 0, (((int)($page - 1) * (int)$limit) > ((int)$product_total - (int)$limit)) ? $product_total : (((int)($page - 1) * (int)$limit) + (int)$limit), $product_total, ceil((int)$product_total / (int)$limit));
		
		$this->load->language('extension/so_theme/module/so_filter_shop_by','',$this->config->get('config_language_catalog'));
		$header 			= '';
		$breadcrumbs 		= array();
		$column_left 		= '';
		$column_right 		= '';
		$content_top 		= '';
		$heading_title 		= '';
		$thumb 				= '';
		$description 		= '';
		$categories 		= '';
		$compare 			= '';
		$text_compare 		= '';
		$button_list 		= '';
		$button_grid 		= '';
		$text_sort 			= '';
		$text_limit 		= '';
		$text_tax 			= $this->language->get('text_tax');;
		$text_empty 		= $this->language->get('text_empty');
		$button_wishlist 	= '';
		$button_cart 		= $this->language->get('button_cart');
		$button_compare 	= '';
		$content_bottom 	= '';
		$footer 			= '';
		$sorts 				= array();
		$limits 			= array();
		$products 			= $data['products'];
		$continue 			= $this->url->link('common/home', 'language=' . $this->config->get('config_language'));
		$button_continue 	= $this->language->get('button_continue');
		$result 				= new \stdClass();
		$result->product_arr 	= implode(",",$products_arr_id);
		$result->minPrice_new 	= round($minPrice_new);
		$result->maxPrice_new 	= round($maxPrice_new);
        $result->grid_view 	= $grid_view;
		$data['objlang'] = $this->language;

		$theme_directory = $this->config->get('config_theme');
		$our_url = $this->registry->get('url');
		
		$pagination = $this->load->controller('common/pagination', [
				'total' => $product_total,
				'page'  => $page,
				'limit' => $limit,
				'url'   => $this->url->link('product/category', 'language=' . $this->config->get('config_language') . '&path=' . $path . $url . '&page={page}')
		]);
		$result->pagination 	= $pagination;
	
		$data_template = array(
			'header'  => $header,
			'footer'  => $footer,
			'continue'  => $continue,
			'text_tax'  => $text_tax,
			'text_empty'  => $text_empty,
			'button_wishlist'  => $button_wishlist,
			'button_cart'  => $button_cart,
			'button_compare'  => $button_compare,
			'content_bottom'  => $content_bottom,
			'sorts'  => $sorts,
			'limits'  => $limits,
			'breadcrumbs' => $breadcrumbs,
			'button_list' => $button_list,
			'pagination'	=> $pagination,
			'results'		=> $results,
			'products'  => $products,
			'theme_directory'  => $theme_directory,	
			'our_url'	=> $our_url,						
		);

		$soconfig = new \ClassSoconfig($this->registry); 		
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');			
		if($this->session->data['device']=='mobile' && $platforms_mobile){	
            $html_data = $this->load->view('extension/so_theme/somobile/template/product/category', $data_template);
		} else {
            $html_data = $this->load->view('extension/so_theme/product/category', $data_template);			
		}
		
		ob_start();
		echo $html_data;
		
		$buffer = ob_get_contents();
		$result->html = preg_replace(
			array(
					'/ {2,}/',
					'/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'
			),
			array(
					' ',
					' '
			),
			$buffer
		);
		ob_end_clean();
		die (json_encode($result));
	}
	
	public function convertNameToParam($string) {
		//Lower case everything
		$string = strtolower($string);
		//Make alphanumeric (removes all other characters)
		$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
		//Clean up multiple dashes or whitespaces
		$string = preg_replace("/[\s-]+/", " ", $string);
		//Convert whitespaces and underscore to dash
		$string = preg_replace("/[\s_]/", "-", $string);
		return $string;
	}
}
?>