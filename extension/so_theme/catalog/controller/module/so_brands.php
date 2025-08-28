<?php
namespace Opencart\Catalog\Controller\Extension\SoTheme\Module;
class SoBrands extends \Opencart\System\Engine\Controller {	
	public function index($setting) {
		
		if (!defined ('OWL_CAROUSEL'))
		{
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_brands/css/owl.carousel.css');
		$this->document->addScript('extension/so_theme/catalog/view/javascript/so_brands/js/owl.carousel.js');
		}
		
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_brands/css/css3.css');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_brands/css/animate.css');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_brands/css/style.css');
		
		// caching
		$use_cache = (int)$setting['use_cache'];
		$cache_time = (int)$setting['cache_time'];
		$folder_cache = DIR_CACHE.'so/Brands/';
		if(!file_exists($folder_cache))
			mkdir ($folder_cache, 0777, true);
		if (!class_exists('Cache_Lite'))
	    	require_once (DIR_EXTENSION . 'so_theme/system/library/so/brands/Cache_Lite/Lite.php');

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
				$_data = $this->load->view('extension/so_theme/module/so_brands/'.$data['store_layout'], $data);
				$Cache_Lite->save($_data);
				return  $_data;
			} else {
				return  $_data;
			}
		}else{
			if(file_exists($folder_cache))
				$Cache_Lite->_cleanDir($folder_cache);
			$data = $this->readData($setting);
			return $this->load->view('extension/so_theme/module/so_brands/'.$data['store_layout'], $data);
		}	
	}
	
	
	public function readData($setting) {
		
		static $module = 1;
		$this->load->language('extension/so_theme/module/so_brands/','',$this->config->get('config_language_catalog'));
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_view_all'] = $this->language->get('text_view_all');
		$data['text_end_in'] = $this->language->get('text_end_in');
		
		$this->load->model('design/banner');
		$this->load->model('tool/image');
		$this->load->model('catalog/manufacturer');
		$this->load->model('extension/so_theme/module/so_brands');
		
		$default = array(
			'name' 					=> '',
			'module_description'	=> array(),
			'disp_title_module'		=> '1',
			'status'				=> '1',
			'class_suffix'			=> '',
			'item_link_target'		=> '_blank',

			'position_thumbnail'	=> 'vertical',
			
			'nb_column0'			=> '4',
			'nb_column1'			=> '4',
			'nb_column2'			=> '3',
			'nb_column3'			=> '2',
			'nb_column4'			=> '1',
			'nb_row'				=> '1',
			
			'manufacturers'				=> array(),
			'source_limit'			=> '8',

			
			'manufacturer_image'	=> '1',
			'manufacturer_name'	    => '1',
			'manufacturer_readmore'	=> '0',
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
			'use_cache'				=> '0',
			'cache_time'			=> '3600',
			'direction'				=> ($this->language->get('direction') == 'rtl' ? 'true' : 'false'),
			'direction_class'		=> ($this->language->get('direction') == 'rtl' ? 'so-brands-rtl' : 'so-brands-ltr')			
		);
		
		$data =  array_merge($default,$setting);//check data empty setting 

		// Leader :Check folter Module
		$folder_so_brand = DIR_TEMPLATE.$this->config->get('theme_default_directory').'/template/extension/so_theme/module/so_brands/';
		if(file_exists($folder_so_brand)) $data['config_theme'] = $this->config->get('theme_default_directory');
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
		$data['nav'] 					= ($setting['navs'] == 1 ? "true" : "false");
        $data['suffix']= rand() . time();
		$data['our_url'] = $this->registry->get('url');

		if (isset($setting['module_description'][$this->config->get('config_language_id')])) {
			$data['head_name'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['head_name'], ENT_QUOTES, 'UTF-8');
		}else{
			$data['head_name']              = reset($setting['module_description'])['head_name'];
		}	
		
		if (isset($setting['module_description'][$this->config->get('config_language_id')])) {
			$data['readmore'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['readmore'], ENT_QUOTES, 'UTF-8');
		}else{
			$data['readmore']              = reset($setting['module_description'])['readmore'];
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
		$manufacturerids = $setting['manufacturer'];
		$list = array();

		$filter_data = array(
			'filter_manufacturer_id'  => implode(',',$setting['manufacturer']),
			'limit'        => $setting['source_limit'],
			'start' 	   => $setting['start']
		);
		$manufacturer_brands = $this->model_extension_so_theme_module_so_brands->getManufacturers($filter_data);

		foreach($manufacturer_brands as $item)
		{
			if ($item['image'] && $setting['manufacturer_image']) {					
				$image = $this->model_tool_image->resize($item['image'], $setting['width'], $setting['height']);			
			}else {
				$url = file_exists("image/".$setting['placeholder_path']);
				if ($url) {
					$image_name = $setting['placeholder_path'];
				} else {
					$image_name = "no_image.png";
				}				
				$image = $this->model_tool_image->resize($image_name, $setting['width'], $setting['height']);			
			}
			
			$data['manufacturer_brands'][] = array(
			    'thumb'       		=> $image,
				'name'              => $item['name'],
				'href'              => $this->url->link('product/manufacturer|info', 'manufacturer_id=' . $item['manufacturer_id'])
			);
		}	
		return $data;
	}	
}