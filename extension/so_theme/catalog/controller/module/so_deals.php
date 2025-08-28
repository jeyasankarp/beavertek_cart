<?php
namespace Opencart\Catalog\Controller\Extension\SoTheme\Module;
require_once (DIR_EXTENSION.'so_theme/admin/view/template/soconfig/class/soconfig.php');
class SoDeals extends \Opencart\System\Engine\Controller {	
	public function index($setting) {

		$soconfig = new \ClassSoconfig($this->registry); 
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');			


		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_deals/css/style.css');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_deals/css/css3.css');
		
		$setting['cart'] = $this->url->link('common/cart|info', 'language=' . $this->config->get('config_language'));

		$setting['add_to_cart'] = $this->url->link('checkout/cart|add', 'language=' . $this->config->get('config_language'));
		$setting['add_to_wishlist'] = $this->url->link('account/wishlist|add', 'language=' . $this->config->get('config_language'));
		$setting['add_to_compare'] = $this->url->link('product/compare|add', 'language=' . $this->config->get('config_language'));	
		
		// caching
		$use_cache = (int)$setting['use_cache'];
		$cache_time = (int)$setting['cache_time'];
		$folder_cache = DIR_CACHE.'so/Deals/';
		if(!file_exists($folder_cache))
			mkdir ($folder_cache, 0777, true);
		if (!class_exists('Cache_Lite'))
		    require_once (DIR_EXTENSION . 'so_theme/system/library/so/deals/Cache_Lite/Lite.php');

		$options = array(
			'cacheDir' => $folder_cache,
			'lifeTime' => $cache_time
		);
		$Cache_Lite = new \Cache_Lite($options);
		if ($use_cache){
			$cacheid = (object)(md5( serialize(array($this->config->get('config_language'), $this->session->data['currency'], $setting))));
			$_data = $Cache_Lite->get($cacheid);
			if (!$_data) {
				$data = $this->readData($setting);
				
				if($this->session->data['device']=='mobile' && $platforms_mobile){
					$_data = $this->load->view('extension/so_theme/somobile/template/module/so_deals/'.$data['store_layout'], $data);
				} else {
					$_data = $this->load->view('extension/so_theme/module/so_deals/'.$data['store_layout'], $data);
				}				
				
				$Cache_Lite->save($_data);
				return  $_data;
			} else {
				return  $_data;
			}
		}else{
			if(file_exists($folder_cache))
				$Cache_Lite->_cleanDir($folder_cache);
			$data = $this->readData($setting);		
			
			if($this->session->data['device']=='mobile' && $platforms_mobile){
				return $this->load->view('extension/so_theme/somobile/template/module/so_deals/'.$data['store_layout'], $data);
			} else {
				return $this->load->view('extension/so_theme/module/so_deals/'.$data['store_layout'], $data);
			}				
		}
	}
	
	public function readData($setting) {
		
		static $module = 1;
		$this->load->language('extension/so_theme/module/so_deals','',$this->config->get('config_language_catalog'));
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_view_all'] = $this->language->get('text_view_all');
		$data['text_end_in'] = $this->language->get('text_end_in');
		
		$this->load->model('design/banner');
		$this->load->model('tool/image');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('extension/so_theme/module/so_deals');
		
		$default = array(
			'objlang'				=> $this->language,
			'name' 					=> '',
			'module_description'	=> array(),
			'disp_title_module'		=> '1',
			'status'				=> '1',
			'class_suffix'			=> '',
			'item_link_target'		=> '_blank',
			
			'include_js'			=> 'owlCarousel',
			'position_thumbnail'	=> 'vertical',
			
			'nb_column0'			=> '4',
			'nb_column1'			=> '4',
			'nb_column2'			=> '3',
			'nb_column3'			=> '2',
			'nb_column4'			=> '1',
			'nb_row'				=> '1',
			'categorys'				=> array(),
			'child_category'		=> '1',
			'type_data'		        => '1',	
			'category_depth'		=> '1',
			'product_sort'			=> 'p.price',
			'product_ordering'		=> 'ASC',
			'source_limit'			=> '6',
			'display_title'			=> '1',
			'title_maxlength'		=> '50',
			'display_description'	=> '1',
			'description_maxlength' => '100',
			'display_price'			=> '1',
			'display_addtocart'		=> '1',
			'display_wishlist' 		=> '1',
			'display_compare'		=> '1',
			'display_rating'		=> '1',
			'display_sale'			=> '1',
			'display_new'			=> '1',
			'date_day'				=> '7',
			'product_image_num' 	=> '1',
			
			'product_image'			=> '1',
			'product_get_image_data'=> '1',
			'product_get_image_image'=> '1',
			'width'					=> '200',
			'height'				=> '200',
			'placeholder_path'		=> 'nophoto.png',
			'margin'				=> '5',
			'slideBy'				=> '1',
			'autoplay'				=> '0',
			'autoplayTimeout'		=> '5000',
			'autoplayHoverPause'	=> '0',
			'autoplaySpeed'			=> '1000',
			'startPosition'			=> '0',
			'mouseDrag'				=> '1',
			'touchDrag'				=> '1',
			'loop'					=> '1',
			'button_page' 			=> 'top',
			'dots'					=> '1',
			'dotsSpeed'				=> '500',
			'navs'					=> '1',
			'navSpeed'				=> '500',
			'effect'				=> 'starwars',
			'duration'				=> '800',
			'delay'					=> '500',
			
			'store_layout'			=> 'default',
			'post_text'				=> '',
			'pre_text'				=> '',
			'use_cache'				=> '1',
			'cache_time'			=> '3600',
			'direction'				=> ($this->language->get('direction') == 'rtl' ? 'true' : 'false'),
			'direction_class'		=> ($this->language->get('direction') == 'rtl' ? 'so-deals-rtl' : 'so-deals-ltr')
		);
		$data =  array_merge($default,$setting);//check data empty setting 

		// Leader :Check folter Module
		$folder_so_deal = DIR_TEMPLATE.$this->config->get('theme_default_directory').'/template/extension/so_theme/module/so_deals/';
		if(file_exists($folder_so_deal)) $data['config_theme'] = $this->config->get('theme_default_directory');
		else $data['config_theme'] = 'default';
		
		
		if (!isset($setting['limit'])) {
			$setting['limit'] = 3;
		}
		if (!isset($setting['start'])) {
			$setting['start'] = 0;
		}
		if (!isset($setting['width'])) {
			$setting['width'] = 100;
		}
		if (!isset($setting['height'])) {
			$setting['height'] = 200;
		}
		$data['nb_rows'] 			= $setting['nb_row'];
		$data['start'] 				= $setting['start'];
		$data['autoplay'] 				= ($setting['autoplay'] ==1 ? "true" : "false");
		$data['autoplay_hover_pause'] 	= ($setting['autoplayHoverPause'] ==1 ? "true" : "false");
		$data['mouseDrag'] 				= ($setting['mouseDrag'] == 1 ? "true" : "false" );
		$data['touchDrag'] 				= ($setting['touchDrag'] == 1 ? "true" : "false" );
		$data['loop'] 					= ($setting['loop'] == 1 ? "true" : "false" );
		$data['dots'] 					= ($setting['dots'] == 1 ? "true" : "false");
		$data['navs'] 					= ($setting['navs'] == 1 ? "true" : "false");

		// Dev Custom Show Category
		$data['our_url'] = $this->registry->get('url');

		if (isset($setting['module_description'][$this->config->get('config_language_id')])) {
			$data['head_name'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['head_name'], ENT_QUOTES, 'UTF-8');
		}else{
			$data['head_name']              = reset($setting['module_description'])['head_name'];
		}	

		if (isset($setting['module_description'][$this->config->get('config_language_id')])) {
			$data['custom_url'] = isset($setting['module_description'][$this->config->get('config_language_id')]['custom_url']) ? $setting['module_description'][$this->config->get('config_language_id')]['custom_url'] : '';
		}else{
			$data['custom_url']  = $setting['custom_url'];
		}

		if ($data['custom_url'] != '' && substr($data['custom_url'], 0, 7) != 'http://' && substr($data['custom_url'], 0, 8) != 'https://') {
			if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
				$base = $this->config->get('config_ssl');
			} else {
				$base = $this->config->get('config_url');
			}
			$data['custom_url'] = $base.$data['custom_url'];
		}	

		if (isset($setting['post_text']))
			$data['post_text']  = html_entity_decode($setting['post_text'], ENT_QUOTES, 'UTF-8');
		else
			$data['post_text']  = '';
		if (isset($setting['pre_text']))
			$data['pre_text']  = html_entity_decode($setting['pre_text'], ENT_QUOTES, 'UTF-8');
		else
			$data['pre_text']  = '';

		//Default	
		$catids = $setting['category'];
		$list = array();
		$product_arr = array();
		$cats = array();
		$_catids = (array)self::processCategory($catids);
		$type_data = (int)$setting['type_data'];
		if(count($_catids) != 0 && $type_data === 1)
		{
			$category_id_list = self::getCategoryson($_catids,$setting);
			$product_arr = self::getProducts($category_id_list,$setting);
		}
		$data['list'] = $product_arr;
		
		$data['product_features'] = array();
		$data['product_feature_ids'] = array();
		
		if($type_data === 0){
			$data['product_feature_ids'] = $setting['product_feature'];
		
			if(count($setting['product_feature']) > 0){
				$filter_data = array(
					'filter_product_id'  => implode(',',$setting['product_feature']),
					'sort'         => $setting['product_sort'],
					'order'        => $setting['product_ordering'],
					'limit'        => $setting['source_limit'],
					'start' 	   => $setting['start']
				);
				$products_deals = $this->model_extension_so_theme_module_so_deals->getDeals($filter_data);
				foreach($products_deals as $product)
				{
					
					$specialPriceToDate = '';
					if (strtotime ($product['date_start']) != false && strtotime ($product['date_end']) != false)
					{
						$current = date ('Y/m/d H:i:s');
						$start_date = date ('Y/m/d H:i:s', strtotime ($product['date_start']));
						$date_end = date ('Y/m/d H:i:s', strtotime ($product['date_end']));
						if (strtotime ($date_end) >= strtotime ($current) && strtotime ($start_date) <= strtotime ($date_end))
							$specialPriceToDate = $date_end;
					}
					
					// Dev Custom Show Category
					$category_info = array();
					$product_info = $this->model_catalog_product->getProduct($product['product_id']);
					$categories = $this->model_catalog_product->getCategories($product['product_id']);
					foreach ($categories as $categorie){
						$category_info[] = $this->model_catalog_category->getCategory($categorie["category_id"]);
					}

					$product_image = $this->model_catalog_product->getImages($product['product_id']);
					
					$product_info = $this->model_catalog_product->getProduct($product['product_id']);
					
					$product_image_first = array_shift($product_image);
					$image2 = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					if($product_image_first != null)
					{
						$image2 = $this->model_tool_image->resize($product_image_first['image'], $setting['width'], $setting['height']);
					}
					if ($product_info['image'] && $setting['product_get_image_data']) {
						$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
					}elseif(isset($product_image[0]['image']) && $setting['product_get_image_image']){
						$image = $this->model_tool_image->resize($product_image[0]['image'], $setting['width'], $setting['height']);
					} else {
						$url = file_exists("image/".$setting['placeholder_path']);

						if ($url) {
							$image_name = $setting['placeholder_path'];
						} else {
							$image_name = "no_image.png";
						}

						$image = $this->model_tool_image->resize($image_name, $setting['width'], $setting['height']);
					}
					
					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}

					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						$discount = '-'.round((($product_info['price'] - $product_info['special'])/$product_info['price'])*100, 0).'%';
					} else {
						$special = false;
						$discount = false;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = $product_info['rating'];
					} else {
						$rating = false;
					}
					
					$name = (($setting['title_maxlength'] != 0 && strlen($product_info['name']) > $setting['title_maxlength'] ) ? (substr(strip_tags(html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8')), 0, $setting['title_maxlength']) .'..') : $product_info['name']);
					$description = (($setting['description_maxlength'] != 0 && strlen($product_info['description']) > $setting['description_maxlength'] ) ? substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $setting['description_maxlength']) . '..' : $product_info['description']);
					
					$datetimeNow = new \DateTime();
					$datetimeCreate = new \DateTime($product_info['date_available']);
					$interval = $datetimeNow->diff($datetimeCreate);
					$dateDay = $interval->format('%a');
					$productNew = ($dateDay <= $setting['date_day'] ? 1 : 0);
					
					/*====== Leader: Check sold product=======*/
					//$this->load->model('extension/soconfig/general');
					$sold = 0;
					$avail = 0;
	        		//if($this->model_extension_soconfig_general->getUnitsSold($product_info['product_id'])){
	        		//	$sold = $this->model_extension_soconfig_general->getUnitsSold($product_info['product_id']);
	        		//}
	        		$total_quantity = $product_info['quantity'] + $sold;
	        		$avail = $total_quantity - $sold;
	        		if($sold > 0){	        			
	    				$sold_width = number_format(($avail/$total_quantity) * 100,2);    				
	        		}else{
	        			$sold_width = 0;
	        		}

					$data['product_features'][] = array(
						'product_id'  		=> $product_info['product_id'],
						'location'  		=> $product_info['location'],
						'thumb'       		=> $image,
						'thumb2'       		=> $image2,
						'name'        		=> $product_info['name'],
						'name_maxlength'    => $name,
						'description' 		=> $product_info['description'],
						'description_maxlength'	=> $description,
						'price'       		=> $price,
						'special'     		=> $special,
						'discount'      => $discount,
						'productNew'		=> $productNew,
						'tax'         		=> $tax,
						'rating'      		=> $rating,
						'date_added'  		=> $product_info['date_added'],
						'model'  	  		=> $product_info['model'],
						'quantity'    		=> $product_info['quantity'],
						'href'        		=> $this->url->link('product/product', 'language=' . $this->config->get('config_language') . '&product_id=' . $product_info['product_id']),
						'specialPriceToDate' => $specialPriceToDate,
						'category_info' 	=> $category_info,
					);
				}
			}
		} // display feature
		$data['module'] = $module++;
		$ok = array();						
		if($setting['type_limit_random'] == 1 && (int)$setting['count_limit_random'] <= count($data['product_features']))
		{
			$rand_keys = array_rand($data['product_features'], (int)$setting['count_limit_random']);					
			if((int)$setting['count_limit_random'] == 1) {
				array_push($ok, $data['product_features'][$rand_keys]);
				$data['product_features'] = $ok;
			}
			else {					
				foreach ($rand_keys as $item) {
					array_push($ok, $data['product_features'][$item]);
				}
				$data['product_features'] = $ok;
			}			
		}		
		return $data;
	}
	
	public function getCategoryson($category_list, $setting)
	{
		if($setting['child_category'] ==1)
		{
			for($i=1; $i<=$setting['category_depth'];$i++)
			{
				$filter_data = array(
					'category_id'  => implode(',',$category_list),
				);
				
				$categoryss = $this->model_extension_so_theme_module_so_deals->getCategories_son_deals($filter_data);

				foreach ($categoryss as $category)
				{
					if(!in_array($category['category_id'],$category_list))
					{
						$category_list[] = $category['category_id'];
					}
				}
			}
		}
		return $category_list;
	}
	public function getProducts($category_id_list,$setting)
	{
		$list = array();
		if ($category_id_list != '') {
			$filter_data = array(
				'filter_category_id'  => implode(',',$category_id_list),
				'sort'         => $setting['product_sort'],
				'order'        => $setting['product_ordering'],
				'limit'        => $setting['source_limit'],
				'start' 	   => $setting['start']
			);
		}
		else {
			$filter_data = array(
				'sort'         => $setting['product_sort'],
				'order'        => $setting['product_ordering'],
				'limit'        => $setting['source_limit'],
				'start' 	   => $setting['start']
			);
		}
		$products_arr = $this->model_extension_so_theme_module_so_deals->getProducts_deals($filter_data);
		if (count($products_arr) > 0) 
		{
			$cat['child'] = array();
			foreach($products_arr as $product)
			{
				$specialPriceToDate = '';
				
				if (isset($product['date_start']) && strtotime ($product['date_start']) != false && strtotime ($product['date_end']) != false)
				{
					$current = date ('Y/m/d H:i:s');
					$start_date = date ('Y/m/d H:i:s', strtotime ($product['date_start']));
					$date_end = date ('Y/m/d H:i:s', strtotime ($product['date_end']));
					if (strtotime ($date_end) >= strtotime ($current) && strtotime ($start_date) <= strtotime ($date_end))
						$specialPriceToDate = $date_end;
				}
				
				$product_image = $this->model_catalog_product->getImages($product['product_id']);
				
				//$product_info = $this->model_catalog_product->getProduct($product['product_id']);
				
				// Dev Custom Show Category
				$category_info = array();
				$product_info = $this->model_catalog_product->getProduct($product['product_id']);
				$categories = $this->model_catalog_product->getCategories($product['product_id']);
				foreach ($categories as $categorie){
					$category_info[] = $this->model_catalog_category->getCategory($categorie["category_id"]);
				}
				
				$product_image_first = array_shift($product_image);
				$image2 = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				if($product_image_first != null)
				{
					$image2 = $this->model_tool_image->resize($product_image_first['image'], $setting['width'], $setting['height']);
				}
				if ($product_info['image'] && $setting['product_get_image_data']) {
					$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
				}elseif(isset($product_image[0]['image']) && $setting['product_get_image_image']){
					$image = $this->model_tool_image->resize($product_image[0]['image'], $setting['width'], $setting['height']);
				} else {
					$url = file_exists("image/".$setting['placeholder_path']);

					if ($url) {
						$image_name = $setting['placeholder_path'];
					} else {
						$image_name = "no_image.png";
					}

					$image = $this->model_tool_image->resize($image_name, $setting['width'], $setting['height']);
				}
				
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$product_info['special'] && (float)$product_info['price'] != 0 ) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$discount = '-'.round((($product_info['price'] - $product_info['special'])/$product_info['price'])*100, 0).'%';
				} else {
					$special = false;
					$discount = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $product_info['rating'];
				} else {
					$rating = false;
				}
				
				$name = (($setting['title_maxlength'] != 0 && strlen($product_info['name']) > $setting['title_maxlength'] ) ? (substr(strip_tags(html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8')), 0, $setting['title_maxlength']) .'..') : $product_info['name']);
				$description = (($setting['description_maxlength'] != 0 && strlen($product_info['description']) > $setting['description_maxlength'] ) ? substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $setting['description_maxlength']) . '..' : $product_info['description']);
				
				$datetimeNow = new \DateTime();
				$datetimeCreate = new \DateTime($product_info['date_available']);
				$interval = $datetimeNow->diff($datetimeCreate);
				$dateDay = $interval->format('%a');
				$productNew = ($dateDay <= $setting['date_day'] ? 1 : 0);
				
				/*====== Leader: Check sold product=======*/
				$this->load->model('extension/so_theme/soconfig/general');
				$sold = 0;
				$avail = 0;
        		
				if($this->model_extension_so_theme_soconfig_general->getUnitsSold($product_info['product_id'])){
        			$sold = $this->model_extension_so_theme_soconfig_general->getUnitsSold($product_info['product_id']);
        		}
				
        		$total_quantity = $product_info['quantity'] + $sold;
        		$avail = $total_quantity - $sold;
        		if($sold > 0){
        			
    				$sold_width = number_format(($sold/$total_quantity) * 100,0);    				
        		}else{
        			$sold_width = 0;
        		}

				$cat['child'][] = array(
					'product_id'  		=> $product_info['product_id'],
					'location'  		=> $product_info['location'],
					'thumb'       		=> $image,
					'thumb2'       		=> $image2,
					'name'        		=> $product_info['name'],
					'name_maxlength'    => $name,
					'description' 		=> $product_info['description'],
					'description_maxlength'	=> $description,
					'price'       		=> $price,
					'special'     		=> $special,
					'discount'      => $discount,
					'productNew'		=> $productNew,
					'tax'         		=> $tax,
					'rating'      		=> $rating,
					'date_added'  		=> $product_info['date_added'],
					'model'  	  		=> $product_info['model'],
					'quantity'    		=> $product_info['quantity'],
					'href'        		=> $this->url->link('product/product', 'language=' . $this->config->get('config_language') . '&product_id=' . $product_info['product_id']),
					'specialPriceToDate' => $specialPriceToDate,
					'category_info' 	=> $category_info,
					'sold_width'  => $sold_width,
					'sold_number'  => $sold,
					'avail_number'  => $avail,
				);
			}
			$list = $cat['child'];
		}
		return $list;
	}
		
	private function processCategory($catids)
	{
		$category_list = array();
		if($catids == null) {
			$catids = array();
		}
		foreach($catids as $category_item)
		{
			$checkCategory = $this->model_extension_so_theme_module_so_deals->checkCategory($category_item);
			if(isset($checkCategory) && $checkCategory != null)
			{
				$category_list[] =  $category_item;
			}
		}
		return $category_list;
	}	
}