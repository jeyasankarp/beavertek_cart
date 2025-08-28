<?php
namespace Opencart\Catalog\Controller\Extension\SoTheme\Module;
require_once (DIR_EXTENSION.'so_theme/admin/view/template/soconfig/class/soconfig.php');
class SoLatestBlog extends \Opencart\System\Engine\Controller {	
	public function index($setting) {
		
	$this->load->language('extension/so_theme/module/so_latest_blog','',$this->config->get('config_language_catalog'));
	$data['heading_title'] = $this->language->get('text_latest');
	$data['text_view'] = $this->language->get('text_view');
	
	/*Config default*/
	if(!isset($setting['pre_text']))
	{
		$setting['pre_text'] = '';		
	}
	else {
		$setting['pre_text'] = html_entity_decode($setting['pre_text'], ENT_QUOTES, 'UTF-8');
	}
	if(!isset($setting['post_text']))
	{
		 $setting['post_text'] = '';
	}
	else {
		$setting['post_text'] = html_entity_decode($setting['post_text'], ENT_QUOTES, 'UTF-8');
	}
	$data['tag_id']	= 'so_latest_blog_'.$setting['moduleid'].'_'.rand().time();

	$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_latest_blog/css/style.css');
	$data['disp_title_module'] 		= (int)$setting['disp_title_module'];
	if (isset($setting['module_description'][$this->config->get('config_language_id')])) {
		$data['head_name'] 			= html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['head_name'], ENT_QUOTES, 'UTF-8');
	}else{
		$data['head_name']  		= html_entity_decode($setting['head_name'], ENT_QUOTES, 'UTF-8');
	}
	$data['direction'] = $this->language->get('direction');
	$data['error_no_database'] = '';
	$data['theme_config'] = $this->config->get('theme_default_directory');
	
		$this->load->model('catalog/category');
		$this->load->model('extension/so_theme/module/so_latest_blog');
		$this->load->model('tool/image');
		if (!defined ('OWL_CAROUSEL'))
		{
			$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_latest_blog/css/animate.css');
			$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_latest_blog/css/owl.carousel.css');
			$this->document->addScript('extension/so_theme/catalog/view/javascript/so_latest_blog/js/owl.carousel.js');
			define( 'OWL_CAROUSEL', 1 );
		}
	
		// General options
		$data['type_layout'] 			= $setting['type_layout'];
		$data['class_suffix'] 			= $setting['class_suffix'];
		$data['item_link_target'] 		= $setting['item_link_target'];
		$data['button_page'] 			= $setting['button_page'];
		$data['type_show']				= $setting['type_show'];
		$data['nb_column0'] 			= $setting['nb_column0'];
		$data['nb_column1'] 			= $setting['nb_column1'];
		$data['nb_column2'] 			= $setting['nb_column2'];
		$data['nb_column3'] 			= $setting['nb_column3'];
		$data['nb_column4'] 			= $setting['nb_column4'];
		$data['nb_rows'] 				= $setting['nb_row'];
		// Blogs options
		$data['display_title'] 			= $setting['display_title'];
		$data['display_description'] 	= $setting['display_description'];
		$data['display_author'] 		= $setting['display_author'];
		$data['display_comment'] 		= $setting['display_comment'];
		//$data['display_view'] 			= $setting['display_view'];
		$data['display_date_added'] 	= $setting['display_date_added'];
		$data['blog_image'] 			= $setting['blog_image'];
		$data['moduleid']  				= $setting['moduleid'];
		
		$data['width'] 					= $setting['width'];
		$data['height']	 				= $setting['height'];
		$data['class_suffix']  	 		= isset($setting['class_suffix'])?$setting['class_suffix']:'';
		$data['display_readmore'] 		= $setting['display_readmore'];
		$data['readmore_text']  	 	= isset($setting['readmore_text'])?$setting['readmore_text']:'Readmore';
		$data['NoItem'] 				= $this->language->get('text_noitem');
		// effect
		$data['autoplay'] = $setting['autoplay'] == 1 ? "true" : "false";
		if ($setting['autoplay'] == 1) {
			$data['autoplay_timeout'] = $setting['autoplay_timeout'];
		}else{
			$data['autoplay_timeout'] = 0;
		}
		$data['margin'] 		= (int)$setting['margin'];
		$data['slideBy'] 		= (int)$setting['slideBy'];
		$data['effect'] 		= $setting['effect'];
		$data['duration'] 		= (int)$setting['duration'];
		$data['delay'] 			= (int)$setting['delay'];
		$data['pausehover'] 	= (int)$setting['pausehover'] == 1 ? "true" : "false";
		$data['autoplaySpeed'] 	= (int)$setting['autoplaySpeed'];
		$data['loop'] 			= (int)$setting['loop'] == 1 ? "true" : "false";
		$data['startPosition'] 	= $setting['startPosition'];
		$data['mouseDrag'] 		= $setting['mouseDrag'] == 1 ? "true" : "false";
		$data['touchDrag'] 		= $setting['touchDrag'] == 1 ? "true" : "false";
		$data['dots'] 			= ($setting['dots'] == 1) ? "true" : "false";
		$data['dotsSpeed'] 		= $setting['dotsSpeed'];
		$data['nav'] 			= ($setting['navs'] == 1 ? "true" : "false");
		$data['navSpeed'] 		= $setting['navSpeed'];
		
		//Tab Advanced
	
		$data['pre_text'] 				= $setting['pre_text'];
		$data['post_text'] 				= $setting['post_text'];
		
		// Get blogs
		$data['blogs']= self::getListBlogs($setting);

		// caching
		$use_cache = (int)$setting['use_cache'];
		$cache_time = (int)$setting['cache_time'];
		$folder_cache = DIR_CACHE.'so/Latest_blog/';
		if(!file_exists($folder_cache))
			mkdir ($folder_cache, 0777, true);
		if (!class_exists('Cache_Lite'))
		    require_once (DIR_EXTENSION . 'so_theme/system/library/so/latest_blog/Cache_Lite/Lite.php');

		$options = array(
			'cacheDir' => $folder_cache,
			'lifeTime' => $cache_time
		);
		$Cache_Lite = new \Cache_Lite($options);
		if ($use_cache){
			$cacheid = (object)(md5( serialize(array($this->config->get('config_language'), $this->session->data['currency'], $setting))));
			$_data = $Cache_Lite->get($cacheid);
			if (!$_data) {
				$_data = $this->getLayoutMod($data,$data['type_layout'],'so_latest_blog');
				$Cache_Lite->save($_data);
				return  $_data;
			} else {
				return  $_data;
			}
		}else{
			if(file_exists($folder_cache))
				$Cache_Lite->_cleanDir($folder_cache);
			
			$_data = $this->getLayoutMod($data,$data['type_layout'],'so_latest_blog');
			return  $_data;
		}
	
	}
	
	//=== Theme Custom Code====
	public function getLayoutMod($data,$type_layout,$name=null){

        $soconfig = new \ClassSoconfig($this->registry); 
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');		
		$log_directory  = DIR_EXTENSION.'so_theme/catalog/view/template/module/'.$name;
		$fileNames = array();
		if (is_dir($log_directory)) {
			$files = scandir($log_directory);
			foreach ($files as  $value) {
				if (strpos($value, '.twig') !== false && strpos($value, '_') === false) {
					$fileNames[] = str_replace('.twig', '', $value);
				}
			}
		}
		$fileNames = isset($fileNames) ? $fileNames : '';
		
		$type_morelayout = '';
		foreach($fileNames as $option_id => $option_value){
			if($option_id == $type_layout){
				if($this->session->data['device']=='mobile' && $platforms_mobile) {
					$type_morelayout = $this->load->view('extension/so_theme/somobile/template/module/'.$name.'/'.$option_value, $data);
				} else {
					$type_morelayout = $this->load->view('extension/so_theme/module/'.$name.'/'.$option_value, $data);
				}		
			}
		}
		return $type_morelayout;
	}
	
	public function getListBlogs($setting){
		$this->load->model('cms/topic');
		$this->load->model('cms/article');
		if (!isset($setting['limit'])) {
			$setting['limit'] = 9;
		}
		if (!isset($setting['width'])) {
			$setting['width'] = 100;
		}
		if (!isset($setting['height'])) {
			$setting['height'] = 200;
		}
		
		// Get Category list
         $str_categorys = $setting['category'];
		$blogs =  array();
		if($str_categorys != "")
		{
			$filter_data = array(
				'filter_topic_id'	=> $str_categorys,
				'sort'  		=> $setting['sort'],
				'order' 		=> $setting['order'],
				'start' 		=> 0,
				'limit' 		=> $setting['limit']
			);

			$this->load->model('extension/so_theme/module/so_latest_blog');
			$blogs = $this->model_extension_so_theme_module_so_latest_blog->getArticles($filter_data);
			foreach( $blogs as $key => $blog ){
				if ($blogs[$key]['image'] && $setting['blog_get_featured_image']){
					$blogs[$key]['thumb'] = $this->model_tool_image->resize($blog['image'], (int)$setting['width'], (int)$setting['height'] );
				}else {
					$url = file_exists("image/catalog/so_latest_blog/images/".$setting['blog_placeholder_path']);
					
					if ($url) {
						$image_name = "catalog/so_latest_blog/images/".$setting['blog_placeholder_path'];
					} else {
						$image_name = "no_image.png";
					}
					$blogs[$key]['thumb'] = $this->model_tool_image->resize($image_name, (int)$setting['width'], (int)$setting['height']);
					
				}					
				// Title
				$title = $blog['name'];
				$title_maxlength = ((strlen($blog['name']) > $setting['title_maxlength'] && $setting['title_maxlength'] !=0)  ? substr(strip_tags(html_entity_decode($blog['name'], ENT_QUOTES, 'UTF-8')), 0, $setting['title_maxlength']) . '..' : $blog['name']);
				
				// Description
				$description 	= ((strlen($blog['description']) > $setting['description_maxlength'] && $setting['description_maxlength'] != 0) ? substr(strip_tags(html_entity_decode($blog['description'], ENT_QUOTES, 'UTF-8')), 0, $setting['description_maxlength']) . '..' : strip_tags(html_entity_decode($blog['description'], ENT_QUOTES, 'UTF-8')));

				
				$blogs[$key]['title'] 			= $title;
				$blogs[$key]['title_maxlength'] = $title_maxlength;
				$blogs[$key]['description'] 	= $description;
				$blogs[$key]['author'] 			= isset($blog['author'])? $blog['author']:$this->language->get('text_none_author');
				$blogs[$key]['date_added']      = strtotime($blog['date_added']); 
				$blogs[$key]['date_modified']   = strtotime($blog['date_modified']);
				$blogs[$key]['comment_count'] 	= $blog['rating'];
				$blogs[$key]['link'] 			= $this->url->link('cms/blog.info', 'language=' . $this->config->get('config_language') . '&article_id=' . $blog['article_id']);
				// text comment
				if($blog['rating'] > 1)
				{
					$blogs[$key]['text_comment']   = $this->language->get('text_comments');
				}else{
					$blogs[$key]['text_comment']   = $this->language->get('text_comment');
				}
			}
		}
		
		$data['blogs'] = $blogs;
		
		return $data['blogs'];
	}
}