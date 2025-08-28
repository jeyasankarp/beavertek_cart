<?php
namespace Opencart\Catalog\Controller\Extension\SoTheme\Module;
require_once (DIR_EXTENSION.'so_theme/admin/view/template/soconfig/class/soconfig.php');
class SoExtraSlider extends \Opencart\System\Engine\Controller {	
	public function index($setting) {

		$soconfig = new \ClassSoconfig($this->registry); 
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');	
		$setting['cart'] = $this->url->link('common/cart|info', 'language=' . $this->config->get('config_language'));

		$setting['add_to_cart'] = $this->url->link('checkout/cart|add', 'language=' . $this->config->get('config_language'));
		$setting['add_to_wishlist'] = $this->url->link('account/wishlist|add', 'language=' . $this->config->get('config_language'));
		$setting['add_to_compare'] = $this->url->link('product/compare|add', 'language=' . $this->config->get('config_language'));
        $this->load->language('extension/so_theme/module/so_call_for_price','',$this->config->get('config_language'));
        $setting['cfp_setting'] = $this->model_setting_setting->getSetting('module_so_call_for_price');		

		// caching
		$use_cache = (int)$setting['use_cache'];
		$cache_time = (int)$setting['cache_time'];
		$folder_cache = DIR_CACHE.'so/ExtraSlider/';
		if(!file_exists($folder_cache))
			mkdir ($folder_cache, 0777, true);
		if (!class_exists('Cache_Lite'))
		   require_once (DIR_EXTENSION . 'so_theme/system/library/so/extra_slider/Cache_Lite/Lite.php');

		$options = array(
			'cacheDir' => $folder_cache,
			'lifeTime' => $cache_time
		);
		$Cache_Lite = new \Cache_Lite($options);
		if ($use_cache){
			$this->hash = (object)(md5( serialize(array($this->config->get('config_language'), $this->session->data['currency'], $setting))));
			$_data = $Cache_Lite->get($this->hash);
			if (!$_data) {
				$data = $this->readData($setting);
				if($this->session->data['device']=='mobile' && $platforms_mobile){
					$_data = $this->load->view('extension/so_theme/somobile/template/module/so_extra_slider/'.$data['store_layout'], $data);
				} else {
					$_data = $this->load->view('extension/so_theme/module/so_extra_slider/'.$data['store_layout'], $data);
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
				return $this->load->view('extension/so_theme/somobile/template/module/so_extra_slider/'.$data['store_layout'], $data);
			} else {
				return $this->load->view('extension/so_theme/module/so_extra_slider/'.$data['store_layout'], $data);
			}			
			
		}

	}
	public function readData($setting) {
		$this->load->language('extension/so_theme/module/so_extra_slider','',$this->config->get('config_language'));
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_tax'] = $this->language->get('text_tax');
		
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('extension/so_theme/module/so_extra_slider');
		$this->load->model('extension/so_theme/module/so_color_swatches_pro');
		$this->load->model('tool/image');		
		$setting['category'] = self::processCategory($setting['category']); // check category (disable)
		$default = array(
			'objlang'				=> $this->language,
			'name' 					=> '',
			'module_description'	=> array(),
			'disp_title_module'		=> '1',
			'status'				=> '1',
			'class_suffix'			=> '',
			'item_link_target'		=> '_blank',
			'products_style'		=> 'style1',	
			'nb_column0'			=> '4',
			'nb_column1'			=> '4',
			'nb_column2'			=> '3',
			'nb_column3'			=> '2',
			'nb_column4'			=> '1',
			'nb_row'				=> '1',
			'type_data'				=> 'category',
			'product_feature'		=> array(),
			'product_features'		=> array(),
			'categorys'				=> array(),
			'child_category'		=> '1',	
			'category_depth'		=> '1',
			'product_sort'			=> 'p.price',
			'product_ordering'		=> 'ASC',
			'limitation'			=> '6',
			
			'display_title'			=> '1',
			'title_maxlength'		=> '50',
			'display_description'	=> '1',
			'description_maxlength' => '100',
			'display_price'			=> '1',
			'display_readmore_link' => '1',
			'display_add_to_cart'	=> '1',
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
			'smartSpeed'			=> '1000',
			'startPosition'			=> '0',
			'mouseDrag'				=> '1',
			'touchDrag'				=> '1',
			'pullDrag'				=> '1',
			'button_page' 			=> 'top',
			'dots'					=> '1',
			'dotsSpeed'				=> '500',
			'loop'					=> '1',
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
			'direction_class'		=> ($this->language->get('direction') == 'rtl' ? 'so-extraslider-rtl' : 'so-extraslider-ltr')
		);

		$data = array_merge($default,$setting);
		$data['colorswatch_type'] = $this->config->get('module_so_color_swatches_pro_type');
		if (isset($setting['post_text'])) $data['post_text']  = html_entity_decode($setting['post_text'], ENT_QUOTES, 'UTF-8');
		if (isset($setting['pre_text'])) $data['pre_text']  = html_entity_decode($setting['pre_text'], ENT_QUOTES, 'UTF-8');
		$products_arr = array();
		if($setting['type_data'] == 'category' && $setting['category']){
			if($setting['child_category'] && $setting['category'])
			{
				for($i=1; $i<=$setting['category_depth'];$i++)
				{
					foreach ($setting['category'] as $categorys)
					{
						$filter_data = array(
							'category_id'  => $categorys,
						);
						$categoryss = $this->model_extension_so_theme_module_so_extra_slider->getCategories_son($filter_data);
						foreach ($categoryss as $category)
						{
							$setting['category'][]  = $category['category_id'];
						}
					}

				}
				$setting['category'] = array_unique($setting['category']);
			}
			$str_categorys = implode(",",$setting['category']);
			$filter_data = array(
				'filter_category_id'  => $str_categorys,
				'sort'         => $setting['product_sort'],
				'order'        => $setting['product_ordering'],
				'limit'        => $setting['limitation'] ,
				'start'        => '0'
			);

			$products_arr = $this->model_extension_so_theme_module_so_extra_slider->getProducts_extra_slider($filter_data);			
			if (!isset($setting['limit'])) {
				$setting['limit'] = 3;
			}
			if (!isset($setting['width'])) {
				$setting['width'] = 100;
			}
			if (!isset($setting['height'])) {
				$setting['height'] = 200;
			}
		}
		$data['products'] = array();
		$count_product = 1;
		if($setting['type_data'] == 'product_feature' && $setting['product_feature']){
			foreach($setting['product_feature'] as $item){
				if($count_product <= $setting['limitation'] || $setting['limitation'] == 0){
					$products_arr[] = $item;
				}	
				$count_product++;
			}	
		}
		
		if(!empty($products_arr)){
			foreach($products_arr as $product)
			{
				$product_info = $this->model_catalog_product->getProduct($product);
				
				// Dev Custom Show Category
				$categories = $this->model_catalog_product->getCategories($product_info['product_id']);
				if (!empty($categories)){
				   $categories_info = $this->model_catalog_category->getCategory($categories[0]['category_id']);
				}
				$categories_name = "";
				if (!empty($categories_info)){
					$categories_name = $categories_info['name'];
				}
				
				
                if(!empty($product_info)) {
					$product_image = $this->model_extension_so_theme_module_so_extra_slider->getImageExtra_slider($product_info['product_id']);
					$product_image_first = array_shift($product_image);
					$image2 = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					if($product_image_first != null)
					{
						$image2 = $this->model_tool_image->resize($product_image_first['image'], $setting['width'], $setting['height']);
					}
					if ($product_info['image'] && $setting['product_get_image_data'] && $setting['product_image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
					}elseif(isset($product_image_first['image']) && $product_image_first['image'] && $setting['product_get_image_image'] && $setting['product_image']){
						$image = $this->model_tool_image->resize($product_image_first['image'], $setting['width'], $setting['height']);
					} else {
						$url = file_exists("image/so_extra_slider/images/".$setting['placeholder_path']);
							if ($url) {
								$image_name = "so_extra_slider/images/".$setting['placeholder_path'];
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
						
						if ((float)$product_info['special'] && (float)$product_info['price'] != 0) {
							$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
							$discount = '-'.round((($product_info['price'] - $product_info['special'])/$product_info['price'])*100, 0).'%';
						} else {
							$special = false;
							$discount = false;
						}
						
						if ((float)$product_info['special']) {
							$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						} else {
							$special = false;
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
					
					$name = ((strlen($product_info['name']) > $setting['title_maxlength'] && $setting['title_maxlength'] !=0)  ? substr(strip_tags(html_entity_decode($product_info['name'], ENT_QUOTES, 'UTF-8')), 0, $setting['title_maxlength']) . '..' : $product_info['name']);
					$description = ((strlen($product_info['description']) > $setting['description_maxlength'] && $setting['description_maxlength'] != 0) ? substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $setting['description_maxlength']) . '..' : $product_info['description']);
					
					/*======Image Gallery=======*/
					
					$data['image_galleries'] = array();
					$image_galleries = $this->model_catalog_product->getImages($product_info['product_id']);
					foreach ($image_galleries as $image_gallery) {
						$data['image_galleries'][] = array(
							'cart' => $this->model_tool_image->resize($image_gallery['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height')),
							'thumb' => $this->model_tool_image->resize($image_gallery['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'))
						);
					}
					$data['first_gallery'] = array(
							'cart' => $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height')),
							'thumb' => $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'))
					);
					
					
					$data['suffix']= rand() . time();
					$datetimeNow = new \DateTime();
					$datetimeCreate = new \DateTime($product_info['date_available']);
					$interval = $datetimeNow->diff($datetimeCreate);
					$dateDay = $interval->format('%a');
					$productNew = ($dateDay <= $setting['date_day'] ? 1 : 0);
					if ((float)$product_info['price']) {
						$price_0 = $product_info['price'];
					} else {
						$price_0 = -1;
					}			
                     
					$option_data = array();
					$width_product_page = 0;
					$height_product_page = 0;
					if ($this->config->get('module_so_color_swatches_pro_status')) {
						$width_product_page = $this->config->get('module_so_color_swatches_pro_width_product_page',20);
						if ($width_product_page == 0) {
							$width_product_page = 20;
						}
						$height_product_page = $this->config->get('module_so_color_swatches_pro_height_product_page',20);
						if ($height_product_page == 0) {
							$height_product_page = 20;
						}
						
						
						$option_selected = $this->config->get('module_so_color_swatches_pro_option');
						$product_option = $this->model_extension_so_theme_module_so_color_swatches_pro->getProductOptionsByOptionId($product_info['product_id'], $option_selected);
						if ($product_option) {
							$product_option_id = $product_option['product_option_id'];
						}
						$option_selected = $option_selected;
		
						$options = $this->model_extension_so_theme_module_so_color_swatches_pro->getProductOptions($product_info['product_id']);		

						foreach ($options as $option) {
							$product_option_value_data = array();					
							foreach ($option['product_option_value'] as $option_value) {
								if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
									$p_image = $this->model_extension_so_theme_module_so_color_swatches_pro->getProductImages($product_info['product_id'], $option_value['option_value_id']);
									
									if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
										$priceO = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
									} else {
										$priceO = false;
									}								
									
									
									if (isset($p_image['image']) && $p_image['image']) {
										$pimage = $this->model_tool_image->resize($p_image['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
										$p_thumbimage = $this->model_tool_image->resize($p_image['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
									} else {
										$pimage = '';
										$p_thumbimage = '';
									}
									if (isset($p_image['product_image_id']) && $p_image['product_image_id']) {
										$product_image_id = $p_image['product_image_id'];
									}
									else {
										$product_image_id = '';
									}
									$product_option_value_data[] = array(
										'product_option_value_id' => $option_value['product_option_value_id'],
										'option_value_id'         => $option_value['option_value_id'],
										'name'                    => $option_value['name'],
										'image'                   => $this->model_tool_image->resize($option_value['image'], $width_product_page, $height_product_page),
										'price'                   => $priceO,
										'price_prefix'            => $option_value['price_prefix'],
										'color_image'             => $pimage,
										'color_thumb_image'       => $p_thumbimage,
										'product_image_id'        => $product_image_id
									);
								}
							}
							$option_data[] = array(
								'product_option_id'    => $option['product_option_id'],
								'product_option_value' => $product_option_value_data,
								'option_id'            => $option['option_id'],
								'name'                 => $option['name'],
								'type'                 => $option['type'],
								'value'                => $option['value'],
								'required'             => $option['required']
							);
						}						
					}
					
					$option_data = array_shift($option_data);			
					$data['products'][] = array(
						'product_id'  	=> $product_info['product_id'],
						'width_product_page' => $width_product_page,
						'height_product_page'=> $height_product_page,
						'thumb'       	=> $image,
						'thumb2'       	=> $image2,
						'option_data'   => $option_data,
						'image_galleries'     => $data['image_galleries'],
						'first_gallery'       => $data['first_gallery'],
						'name'        	=> $name,
						'nameFull'		=> $product_info['name'],
						'description' 	=> $description,
						'price'       	=> $price,
						'price_0'       	=> $price_0,
						'special'     	=> $special,
						'discount'      => $discount,
						'productNew'	=> $productNew,	
						'tax'         	=> $tax,
						'rating'      	=> $rating,
						'href'        	=> $this->url->link('product/product', 'language=' . $this->config->get('config_language') . '&product_id=' . $product_info['product_id']),
						'category_title' 	=> $categories_name
					);					
				}	
			}
		}
		
		$data['display_addtocart'] = $setting['display_add_to_cart'];
	
	
		if (isset($setting['module_description'][$this->config->get('config_language_id')])) {
			$data['head_name'] = html_entity_decode(isset($setting['module_description'][$this->config->get('config_language_id')]['head_name'])?$setting['module_description'][$this->config->get('config_language_id')]['head_name']:'', ENT_QUOTES, 'UTF-8');
		}else{
			$data['head_name']  = isset($setting['head_name'])?$setting['head_name']:'';
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
			$data['custom_url'] = $base.'/'.$data['custom_url'];
		}

		$data['moduleid']  = $setting['moduleid'];
		$data['disp_title_module'] = (int)$setting['disp_title_module'];
		$data['autoplay'] = $setting['autoplay'];
		if ($data['autoplay'] == 1) {
			$data['autoplayTimeout'] = $setting['autoplayTimeout'];
		}else{
			$data['autoplayTimeout'] = 0;
		}
		$data['dots'] 	= ($setting['dots'] == 1) ? "true" : "false";
		$data['loop'] 					= ($setting['loop'] == 1 ? "true" : "false");
		$data['nav'] 					= ($setting['navs'] == 1 ? "true" : "false");
		$data['nb_rows'] = $setting['nb_row'];
		$data['count'] = $setting['limitation'];		
		return $data;
	}
	private function processCategory($catids)
	{
		$catpubid = array();
		if (empty($catids)) return;
		foreach ($catids as $i => $cid) {
			$category = $this->model_catalog_category->getCategory($cid);
			$cats[$i] = $category;
			if (empty($category)) {
				unset($cats[$i]);
			} else {
				$catpubid[] = $category['category_id'];
			}
		}

		if (empty($catpubid)) {
			$filter_data = array(
				'category_id' => 0,
				'start' => 0,
				'limit' => 4
			);
			$catids = $this->model_extension_so_theme_module_so_extra_slider->getCategories_son($filter_data);

			$_catids = array();
			if (!empty($catids)) {
				foreach ($catids as $cat) {
					$_catids[] = $cat['category_id'];
				}
			}
			$catids = $_catids;

			foreach ($catids as $i => $cid) {
				$category = $this->model_catalog_category->getCategory($cid);
			
				$cats[$i] = $category;
				if (empty($category)) {
					unset($cats[$i]);
				} else {
					$catpubid[] = $category['category_id'];
				}
			}
		}
		return $catpubid;
	}

}