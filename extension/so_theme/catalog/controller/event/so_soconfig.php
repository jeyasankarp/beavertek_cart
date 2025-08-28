<?php
namespace Opencart\Catalog\Controller\Extension\SoTheme\Event;

require_once (DIR_EXTENSION.'so_theme/admin/view/template/soconfig/class/soconfig.php');

class SoSoconfig extends \Opencart\System\Engine\Controller {
	
	public function so_theme_cart_before(&$route, &$data){
		$this->load->language('extension/so_theme/soconfig/cart','',$this->config->get('config_language'));
		$totals = [];
		$taxes = $this->cart->getTaxes();
		$total = 0;

		$this->load->model('checkout/cart');

		if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			($this->model_checkout_cart->getTotals)($totals, $taxes, $total);
		}

		$data['text_items'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));
		
		$route = 'extension/so_theme/common/cart';
	}
	public function so_theme_cart_after(&$route, &$data){}	
	
	
	public function so_controller_header_before(&$route, &$data){
		 if (!defined ('OWL_CAROUSEL')){ 
		 $this->document->addStyle('extension/so_theme/catalog/view/javascript/soconfig/css/owl.carousel.css'); 
		 $this->document->addScript('extension/so_theme/catalog/view/javascript/soconfig/js/owl.carousel.js'); 
		 define( 'OWL_CAROUSEL', 1 ); 	
         }
		if (!defined ('SLICK_SLIDER'))		{
			$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_deals/css/slick.css');
			$this->document->addScript('extension/so_theme/catalog/view/javascript/so_deals/js/slick.js');
			define( 'SLICK_SLIDER', 1 );
		}		 
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_searchpro/css/chosen.css');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_searchpro/css/so_searchpro.css');
		$this->document->addScript('extension/so_theme/catalog/view/javascript/so_searchpro/js/chosen.jquery.js');	
        $this->document->addStyle('extension/so_theme/catalog/view/javascript/so_listing_tabs/css/so-listing-tabs.css');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_sociallogin/css/so_sociallogin.css');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_latest_blog/css/style.css');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_megamenu/so_megamenu.css');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_megamenu/wide-grid.css');
		$this->document->addScript('extension/so_theme/catalog/view/javascript/so_megamenu/so_megamenu.js');	
        $this->document->addStyle('extension/so_theme/catalog/view/javascript/so_home_slider/css/style.css');
        $this->document->addStyle('extension/so_theme/catalog/view/javascript/so_newletter_custom_popup/css/style.css');		 
        $this->document->addStyle('extension/so_theme/catalog/view/javascript/so_extra_slider/css/style.css');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_extra_slider/css/css3.css');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_categories/css/so-categories.css');
		$this->document->addScript('extension/so_theme/catalog/view/javascript/so_categories/js/jquery.imagesloaded.js');
		$this->document->addScript('extension/so_theme/catalog/view/javascript/so_categories/js/jquery.so_accordion.js');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_instagram_gallery/css/style.css');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_basic_products/css/style.css');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_deals/css/style.css');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_deals/css/css3.css');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_super_category/css/style.css');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_super_category/css/animate.css');
		$this->document->addScript('extension/so_theme/catalog/view/javascript/so_html_content/js/script.js');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_advanced_search/css/style.css');
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_lookbook/css/styles.css');  
        $this->document->addScript('extension/so_theme/catalog/view/javascript/soconfig/js/jquery.elevateZoom-3.0.8.min.js');		 
        if (!defined ('so_call_for_price')){
            $this->document->addStyle('extension/so_theme/catalog/view/javascript/so_call_for_price/css/jquery.fancybox.css');
			$this->document->addScript('extension/so_theme/catalog/view/javascript/so_call_for_price/js/jquery.fancybox.js');
            $this->document->addStyle('extension/so_theme/catalog/view/javascript/so_call_for_price/css/style.css');
            $this->document->addScript('extension/so_theme/catalog/view/javascript/so_call_for_price/js/script.js');
            define( 'so_call_for_price', 1 );
        }
		if (!defined ('LOOKBOOK_OWL_CAROUSEL')){
			$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_lookbook/css/animate.css');
			//$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_lookbook/css/owl.carousel.css');
			$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_lookbook/css/owl.theme.min.css');
			//$this->document->addScript('extension/so_theme/catalog/view/javascript/so_lookbook/js/owl.carousel.min.js');
			define( 'LOOKBOOK_OWL_CAROUSEL', 1 );
		} 
	}
	public function so_theme_header_before(&$route, &$data){
		 $soconfig = new \ClassSoconfig($this->registry); 
		 $this->registry->set('soconfig', $soconfig);
	
		 $data['soconfig'] = $this->soconfig; 
		 $this->load->language('extension/so_theme/soconfig/soconfig','',$this->config->get('config_language'));
		 $data['objlang'] = $this->language; 
		 $data['lang_id'] = $this->config->get('config_language'); 
		 $data['theme_directory'] = 'extension/so_theme/catalog'; 
		 $data['config_language'] = $this->config->get('config_language');
	 
		 $data['url_layoutbox'] = isset($this->request->get['layoutbox']) ? $this->request->get['layoutbox'] : '' ; 
		 $data['url_pattern'] = isset($this->request->get['pattern']) ? $this->request->get['pattern'] : '' ; 
		 $data['account_fb'] = isset($this->request->get['account_fb']) ? $this->request->get['account_fb'] : '' ; 
		 $data['compare'] = $this->url->link('product/compare', 'language=' . $this->config->get('config_language')); 

		// add position
		$data['content_menu1'] = $this->load->controller('extension/so_theme/soconfig/content_menu_one');		
		$data['content_menu2'] = $this->load->controller('extension/so_theme/soconfig/content_menu_two');
		$data['content_menu3'] = $this->load->controller('extension/so_theme/soconfig/content_menu_three');
		$data['header_block'] = $this->load->controller('extension/so_theme/soconfig/header_block');
        $data['header_block2'] = $this->load->controller('extension/so_theme/soconfig/header_block_two');
		$data['header_block3'] = $this->load->controller('extension/so_theme/soconfig/header_block_three');
		$data['header_block4'] = $this->load->controller('extension/so_theme/soconfig/header_block_four');		
		$data['search_block'] = $this->load->controller('extension/so_theme/soconfig/search_block');
		$data['header_block5'] = $this->load->controller('extension/so_theme/soconfig/header_block_five');

		$search = array(
			'/\>[^\S ]+/s',     // strip whitespaces after tags, except space
			'/[^\S ]+\</s',     // strip whitespaces before tags, except space
			'/(\s)+/s',         // shorten multiple whitespace sequences
			'/<!--(.|\s)*?-->/', // Remove HTML comments
			'/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\')\/\/.*))/' // Remove JQUERY comments
		);
		$replace = array(
			'>',
			'<',
			'\\1',
			'',
			'',
		);		
		
		$data['imgfavicon'] = !empty($data['soconfig']->get_settings('imgfavicon'))?$data['soconfig']->get_settings('imgfavicon'):'';
         
		if(!empty($data['soconfig']->get_settings('htmlminify')) && $data['soconfig']->get_settings('htmlminify') == 1) {
			$data['content_menu1'] = preg_replace($search, $replace, $data['content_menu1']);	
			$data['content_menu2'] = preg_replace($search, $replace, $data['content_menu2']);	
			$data['header_block'] = preg_replace($search, $replace, $data['header_block']);	
			$data['header_block2'] = preg_replace($search, $replace, $data['header_block2']);	
			$data['header_block3'] = preg_replace($search, $replace, $data['header_block3']);	
			$data['search_block'] = preg_replace($search, $replace, $data['search_block']);
			$data['header_block5'] = preg_replace($search, $replace, $data['header_block5']);			
		} 

		// For page specific css
		if (isset($this->request->get['route'])) $data['class'] = str_replace('/', '-', $this->request->get['route']);
		else $data['class'] = 'common-home';
		//Decodes HTML Entities
         $data['selector_body'] = !empty($data['soconfig']->get_settings('selector_body')) ? html_entity_decode($data['soconfig']->get_settings('selector_body'), ENT_QUOTES, 'UTF-8') : '';
         $data['selector_menu'] = !empty($data['soconfig']->get_settings('selector_menu')) ? html_entity_decode($data['soconfig']->get_settings('selector_menu'), ENT_QUOTES, 'UTF-8') : '';
         $data['selector_heading'] = !empty($data['soconfig']->get_settings('selector_heading')) ? html_entity_decode($data['soconfig']->get_settings('selector_heading'), ENT_QUOTES, 'UTF-8') : '';
		 $data['mselector_body'] = !empty($data['soconfig']->get_settings('mselector_body')) ? html_entity_decode($data['soconfig']->get_settings('mselector_body'), ENT_QUOTES, 'UTF-8') : '';
         $data['mselector_menu'] = !empty($data['soconfig']->get_settings('mselector_menu')) ? html_entity_decode($data['soconfig']->get_settings('mselector_menu'), ENT_QUOTES, 'UTF-8') : '';
         $data['mselector_heading'] = !empty($data['soconfig']->get_settings('mselector_heading')) ? html_entity_decode($data['soconfig']->get_settings('mselector_heading'), ENT_QUOTES, 'UTF-8') : '';
				
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');	

		if($this->session->data['device']=='mobile' && $platforms_mobile != 0){
			$data['home'] = $this->url->link('extension/so_theme/mobile/home');
			$this->load->language('extension/so_theme/soconfig/somobile','',$this->config->get('config_language'));
			$data['menu_search'] = $this->url->link('product/search', '', true);
			$data['mobile'] = new \ClassSoconfig($this->registry); 
			$http = $_SERVER["HTTPS"]  ? 'https://' : 'http://';
			$data['actual_link'] = $http."$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$data['text_items'] = sprintf($this->language->get('text_itemcount'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0));			
			$route = 'extension/so_theme/somobile/template/common/header';
		}	else {
			$route = 'extension/so_theme/common/header';
		}				
				
		 
	}	
	public function so_theme_header_after(string &$route, array &$data, mixed &$output): void {}
    
	public function so_theme_footer_before(&$route, &$data){
		/*======Show Themeconfig=======*/
		$soconfig = new \ClassSoconfig($this->registry); 
		$this->registry->set('soconfig', $soconfig);		
		
		$data['soconfig'] = $this->soconfig;
		$this->load->language('extension/so_theme/soconfig/soconfig','',$this->config->get('config_language'));
		$data['objlang'] = $this->language;
		$data['lang_id'] = $this->config->get('config_language');
		$data['theme_directory'] = 'extension/so_theme/catalog'; 
		$data['account_fb'] = isset($this->request->get['account_fb']) ? $this->request->get['account_fb'] : '' ;
		$data['compare'] = $this->url->link('product/compare', 'language=' . $this->config->get('config_language'));
		
		// add position
		if( $this->soconfig->get_settings('typefooter') == 1) $data['footer_block1'] = $this->load->controller('extension/so_theme/soconfig/footer_block_one');
		else if( $this->soconfig->get_settings('typefooter') == 2) $data['footer_block2'] = $this->load->controller('extension/so_theme/soconfig/footer_block_two');
	    else if( $this->soconfig->get_settings('typefooter') == 3)  $data['footer_block3'] = $this->load->controller('extension/so_theme/soconfig/footer_block_three');
	    else if( $this->soconfig->get_settings('typefooter') == 4)  $data['footer_block4'] = $this->load->controller('extension/so_theme/soconfig/footer_block_four');
	    else if( $this->soconfig->get_settings('typefooter') == 5)  $data['footer_block5'] = $this->load->controller('extension/so_theme/soconfig/footer_block_five');
	    else if( $this->soconfig->get_settings('typefooter') == 6)  $data['footer_block6'] = $this->load->controller('extension/so_theme/soconfig/footer_block_six');
	    else if( $this->soconfig->get_settings('typefooter') == 7)  $data['footer_block7'] = $this->load->controller('extension/so_theme/soconfig/footer_block_seven');
	    else if( $this->soconfig->get_settings('typefooter') == 8)  $data['footer_block8'] = $this->load->controller('extension/so_theme/soconfig/footer_block_eight');
	    else if( $this->soconfig->get_settings('typefooter') == 9)  $data['footer_block9'] = $this->load->controller('extension/so_theme/soconfig/footer_block_nine');
	    else if( $this->soconfig->get_settings('typefooter') == 10)  $data['footer_block10'] = $this->load->controller('extension/so_theme/soconfig/footer_block_ten');
		else if( $this->soconfig->get_settings('typefooter') == 11)  $data['footer_block11'] = $this->load->controller('extension/so_theme/soconfig/footer_block_eleven');
		else if( $this->soconfig->get_settings('typefooter') == 12)  $data['footer_block12'] = $this->load->controller('extension/so_theme/soconfig/footer_block_twelve');
		else if( $this->soconfig->get_settings('typefooter') == 13)  $data['footer_block13'] = $this->load->controller('extension/so_theme/soconfig/footer_block_thirteen');
		else if( $this->soconfig->get_settings('typefooter') == 14)  $data['footer_block14'] = $this->load->controller('extension/so_theme/soconfig/footer_block_fourteen');
		else if( $this->soconfig->get_settings('typefooter') == 15)  $data['footer_block15'] = $this->load->controller('extension/so_theme/soconfig/footer_block_fifteen');
		else if( $this->soconfig->get_settings('typefooter') == 16)  $data['footer_block16'] = $this->load->controller('extension/so_theme/soconfig/footer_block_sixteen');
		else if( $this->soconfig->get_settings('typefooter') == 17)  $data['footer_block17'] = $this->load->controller('extension/so_theme/soconfig/footer_block_seventeen');
		else if( $this->soconfig->get_settings('typefooter') == 18)  $data['footer_block18'] = $this->load->controller('extension/so_theme/soconfig/footer_block_eighteen');
		else if( $this->soconfig->get_settings('typefooter') == 19)  $data['footer_block19'] = $this->load->controller('extension/so_theme/soconfig/footer_block_nineteen');
		else if( $this->soconfig->get_settings('typefooter') == 20)  $data['footer_block20'] = $this->load->controller('extension/so_theme/soconfig/footer_block_twenty');

		else if( $this->soconfig->get_settings('typefooter') == 21)  $data['footer_block21'] = $this->load->controller('extension/so_theme/soconfig/footer_block_twenty_one');
		else if( $this->soconfig->get_settings('typefooter') == 22)  $data['footer_block22'] = $this->load->controller('extension/so_theme/soconfig/footer_block_twenty_two');
		else if( $this->soconfig->get_settings('typefooter') == 23)  $data['footer_block23'] = $this->load->controller('extension/so_theme/soconfig/footer_block_twenty_three');
		else if( $this->soconfig->get_settings('typefooter') == 24)  $data['footer_block24'] = $this->load->controller('extension/so_theme/soconfig/footer_block_twenty_four');
		else if( $this->soconfig->get_settings('typefooter') == 25)  $data['footer_block25'] = $this->load->controller('extension/so_theme/soconfig/footer_block_twenty_five');
		else if( $this->soconfig->get_settings('typefooter') == 26)  $data['footer_block26'] = $this->load->controller('extension/so_theme/soconfig/footer_block_twenty_six');
		else if( $this->soconfig->get_settings('typefooter') == 27)  $data['footer_block27'] = $this->load->controller('extension/so_theme/soconfig/footer_block_twenty_seven');
		else if( $this->soconfig->get_settings('typefooter') == 28)  $data['footer_block28'] = $this->load->controller('extension/so_theme/soconfig/footer_block_twenty_eight');
		else if( $this->soconfig->get_settings('typefooter') == 29)  $data['footer_block29'] = $this->load->controller('extension/so_theme/soconfig/footer_block_twenty_nine');
		else if( $this->soconfig->get_settings('typefooter') == 30)  $data['footer_block30'] = $this->load->controller('extension/so_theme/soconfig/footer_block_thirty');
		else if( $this->soconfig->get_settings('typefooter') == 31)  $data['footer_block31'] = $this->load->controller('extension/so_theme/soconfig/footer_block_thirty_one');
		else if( $this->soconfig->get_settings('typefooter') == 32)  $data['footer_block32'] = $this->load->controller('extension/so_theme/soconfig/footer_block_thirty_two');
		else if( $this->soconfig->get_settings('typefooter') == 33)  $data['footer_block33'] = $this->load->controller('extension/so_theme/soconfig/footer_block_thirty_three');
		else if( $this->soconfig->get_settings('typefooter') == 34)  $data['footer_block34'] = $this->load->controller('extension/so_theme/soconfig/footer_block_thirty_four');
		else if( $this->soconfig->get_settings('typefooter') == 35)  $data['footer_block35'] = $this->load->controller('extension/so_theme/soconfig/footer_block_thirty_five');
		else if( $this->soconfig->get_settings('typefooter') == 36)  $data['footer_block36'] = $this->load->controller('extension/so_theme/soconfig/footer_block_thirty_six');
		else if( $this->soconfig->get_settings('typefooter') == 37)  $data['footer_block37'] = $this->load->controller('extension/so_theme/soconfig/footer_block_thirty_seven');
		else if( $this->soconfig->get_settings('typefooter') == 38)  $data['footer_block38'] = $this->load->controller('extension/so_theme/soconfig/footer_block_thirty_eight');
		else if( $this->soconfig->get_settings('typefooter') == 39)  $data['footer_block39'] = $this->load->controller('extension/so_theme/soconfig/footer_block_thirty_nine');

		$search = array(
			'/\>[^\S ]+/s',     // strip whitespaces after tags, except space
			'/[^\S ]+\</s',     // strip whitespaces before tags, except space
			'/(\s)+/s',         // shorten multiple whitespace sequences
			'/<!--(.|\s)*?-->/' // Remove HTML comments
		);

		$replace = array(
			'>',
			'<',
			'\\1',
			''
		);	

		if(!empty($data['soconfig']->get_settings('htmlminify')) && $data['soconfig']->get_settings('htmlminify') == 1) {
			if( $this->soconfig->get_settings('typefooter') == 1) $data['footer_block1'] = preg_replace($search, $replace, $data['footer_block1']);
			else if( $this->soconfig->get_settings('typefooter') == 2) $data['footer_block2'] = preg_replace($search, $replace, $data['footer_block2']);
			else if( $this->soconfig->get_settings('typefooter') == 3)  $data['footer_block3'] = preg_replace($search, $replace, $data['footer_block3']);
			else if( $this->soconfig->get_settings('typefooter') == 4)  $data['footer_block4'] = preg_replace($search, $replace, $data['footer_block4']);
			else if( $this->soconfig->get_settings('typefooter') == 5)  $data['footer_block5'] = preg_replace($search, $replace, $data['footer_block5']);
			else if( $this->soconfig->get_settings('typefooter') == 6)  $data['footer_block6'] = preg_replace($search, $replace, $data['footer_block6']);
			else if( $this->soconfig->get_settings('typefooter') == 7)  $data['footer_block7'] = preg_replace($search, $replace, $data['footer_block7']);
			else if( $this->soconfig->get_settings('typefooter') == 8)  $data['footer_block8'] = preg_replace($search, $replace, $data['footer_block8']);
			else if( $this->soconfig->get_settings('typefooter') == 9)  $data['footer_block9'] = preg_replace($search, $replace, $data['footer_block9']);
			else if( $this->soconfig->get_settings('typefooter') == 10)  $data['footer_block10'] = preg_replace($search, $replace, $data['footer_block10']);
			else if( $this->soconfig->get_settings('typefooter') == 11)  $data['footer_block11'] = preg_replace($search, $replace, $data['footer_block11']);
			else if( $this->soconfig->get_settings('typefooter') == 12)  $data['footer_block12'] = preg_replace($search, $replace, $data['footer_block12']);
			else if( $this->soconfig->get_settings('typefooter') == 13)  $data['footer_block13'] = preg_replace($search, $replace, $data['footer_block13']);
			else if( $this->soconfig->get_settings('typefooter') == 14)  $data['footer_block14'] = preg_replace($search, $replace, $data['footer_block14']);
			else if( $this->soconfig->get_settings('typefooter') == 15)  $data['footer_block15'] = preg_replace($search, $replace, $data['footer_block15']);
			else if( $this->soconfig->get_settings('typefooter') == 16)  $data['footer_block16'] = preg_replace($search, $replace, $data['footer_block16']);
			else if( $this->soconfig->get_settings('typefooter') == 17)  $data['footer_block17'] = preg_replace($search, $replace, $data['footer_block17']);
			else if( $this->soconfig->get_settings('typefooter') == 18)  $data['footer_block18'] = preg_replace($search, $replace, $data['footer_block18']);
			else if( $this->soconfig->get_settings('typefooter') == 19)  $data['footer_block19'] = preg_replace($search, $replace, $data['footer_block19']);
			else if( $this->soconfig->get_settings('typefooter') == 20)  $data['footer_block20'] = preg_replace($search, $replace, $data['footer_block20']);
			else if( $this->soconfig->get_settings('typefooter') == 21)  $data['footer_block21'] = preg_replace($search, $replace, $data['footer_block21']);
			else if( $this->soconfig->get_settings('typefooter') == 22)  $data['footer_block22'] = preg_replace($search, $replace, $data['footer_block22']);
			else if( $this->soconfig->get_settings('typefooter') == 23)  $data['footer_block23'] = preg_replace($search, $replace, $data['footer_block23']);
			else if( $this->soconfig->get_settings('typefooter') == 24)  $data['footer_block24'] = preg_replace($search, $replace, $data['footer_block24']);
			else if( $this->soconfig->get_settings('typefooter') == 25)  $data['footer_block25'] = preg_replace($search, $replace, $data['footer_block25']);
			else if( $this->soconfig->get_settings('typefooter') == 26)  $data['footer_block26'] = preg_replace($search, $replace, $data['footer_block26']);
			else if( $this->soconfig->get_settings('typefooter') == 27)  $data['footer_block27'] = preg_replace($search, $replace, $data['footer_block27']);
			else if( $this->soconfig->get_settings('typefooter') == 28)  $data['footer_block28'] = preg_replace($search, $replace, $data['footer_block28']);
			else if( $this->soconfig->get_settings('typefooter') == 29)  $data['footer_block29'] = preg_replace($search, $replace, $data['footer_block29']);
			else if( $this->soconfig->get_settings('typefooter') == 30)  $data['footer_block30'] = preg_replace($search, $replace, $data['footer_block30']);
			else if( $this->soconfig->get_settings('typefooter') == 31)  $data['footer_block31'] = preg_replace($search, $replace, $data['footer_block31']);
			else if( $this->soconfig->get_settings('typefooter') == 32)  $data['footer_block32'] = preg_replace($search, $replace, $data['footer_block32']);
			else if( $this->soconfig->get_settings('typefooter') == 33)  $data['footer_block33'] = preg_replace($search, $replace, $data['footer_block33']);
			else if( $this->soconfig->get_settings('typefooter') == 34)  $data['footer_block34'] = preg_replace($search, $replace, $data['footer_block34']);
			else if( $this->soconfig->get_settings('typefooter') == 35)  $data['footer_block35'] = preg_replace($search, $replace, $data['footer_block35']);
			else if( $this->soconfig->get_settings('typefooter') == 36)  $data['footer_block36'] = preg_replace($search, $replace, $data['footer_block36']);
			else if( $this->soconfig->get_settings('typefooter') == 37)  $data['footer_block37'] = preg_replace($search, $replace, $data['footer_block37']);
			else if( $this->soconfig->get_settings('typefooter') == 38)  $data['footer_block38'] = preg_replace($search, $replace, $data['footer_block38']);
			else if( $this->soconfig->get_settings('typefooter') == 39)  $data['footer_block39'] = preg_replace($search, $replace, $data['footer_block39']);
		}
	
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');		
		if($this->session->data['device']=='mobile' && $platforms_mobile != 0){
			$this->load->language('extension/soconfig/somobile','',$this->config->get('config_language'));
			//Decodes HTML Entities
			$data['customfooter_text'] = html_entity_decode($data['soconfig']->get_settings('customfooter_text') ?? '', ENT_QUOTES, 'UTF-8');			
			$route = 'extension/so_theme/somobile/template/common/footer';
		} else {
			$route = 'extension/so_theme/common/footer';		
		}
	}

	public function so_theme_home_before(&$route, &$data){
		$soconfig = new \ClassSoconfig($this->registry); 
		$this->registry->set('soconfig', $soconfig);		
		
		$data['soconfig'] = $this->soconfig;		
		$this->load->language('extension/so_theme/soconfig/soconfig','',$this->config->get('config_language'));
		$data['objlang'] = $this->language;	
		$data['content_home'] = $this->load->controller('extension/so_theme/soconfig/content_home');

		$search = array(
			'/\>[^\S ]+/s',     // strip whitespaces after tags, except space
			'/[^\S ]+\</s',     // strip whitespaces before tags, except space
			'/(\s)+/s',         // shorten multiple whitespace sequences
			'/<!--(.|\s)*?-->/' // Remove HTML comments
		);

		$replace = array(
			'>',
			'<',
			'\\1',
			''
		);
        if(!empty($data['soconfig']->get_settings('htmlminify')) && $data['soconfig']->get_settings('htmlminify') == 1) {
		    $data['content_home'] = preg_replace($search, $replace, $data['content_home']);
		}
		
		$soconfig = new \ClassSoconfig($this->registry); 
	    $this->registry->set('soconfig', $soconfig);
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');	

		    if($this->session->data['device']=='mobile' && $platforms_mobile != 0)
			{
				$this->response->redirect($this->url->link('extension/so_theme/mobile/home'));
			}
		    else {
			    $route = 'extension/so_theme/common/home';
			}
	}	
	
	
	public function so_theme_language_before(&$route, &$data){
		$soconfig = new \ClassSoconfig($this->registry); 
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');
		    if($this->session->data['device']=='mobile' && $platforms_mobile != 0) {
				$route = 'extension/so_theme/somobile/template/common/language';	
            } else {
				$route = 'extension/so_theme/common/language';
			}				
	}
	public function so_theme_currency_before(&$route, &$data){
		$soconfig = new \ClassSoconfig($this->registry); 
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');
		    if($this->session->data['device']=='mobile' && $platforms_mobile != 0)  {			
				$route = 'extension/so_theme/somobile/template/common/currency';
			}
            else {
				$route = 'extension/so_theme/common/currency';
			}			
	}	
	
	public function so_theme_product_before(&$route, &$data){	
        $this->load->language('extension/so_theme/module/so_call_for_price','',$this->config->get('config_language'));
		$this->load->language('extension/so_theme/soconfig/soconfig','',$this->config->get('config_language'));
		$this->load->model('extension/so_theme/soconfig/general');
		$soconfig = new \ClassSoconfig($this->registry); 
		$this->registry->set('soconfig', $soconfig);		
		$data['soconfig'] = $this->soconfig;		
		$data['products'] = [];
		$data['config_review_guest'] = $this->config->get('config_review_guest');
		$productoption_info = $this->model_extension_so_theme_soconfig_general->getProduct($data['product_id']);
		$data['product_video'] = array();
		$data['product_tabtitle'] = array();
		$data['product_tabcontent'] = array();
		if(isset($productoption_info['video'])) $data['product_video'] = $productoption_info['video'];
		if(isset($productoption_info['tab_title'])) $data['product_tabtitle'] = $productoption_info['tab_title'];
		if(isset($productoption_info['html_product_tab'])) $data['product_tabcontent'] = html_entity_decode($productoption_info['html_product_tab'], ENT_QUOTES, 'UTF-8');	
		
		/*=======url query parameters=======*/ 
		$data['url_sidebarsticky'] = isset($this->request->get['sidebarsticky']) ? $this->request->get['sidebarsticky'] : '' ; 
		$data['url_productGallery'] = isset($this->request->get['productGallery']) ? $this->request->get['productGallery'] : '' ; 
		$data['url_asidePosition'] = isset($this->request->get['asidePosition']) ? $this->request->get['asidePosition'] : '' ; 
		$data['url_asideType'] = isset($this->request->get['asideType']) ? $this->request->get['asideType'] : '' ; 		

        $data['special_end_date'] = $this->model_extension_so_theme_soconfig_general->getDateEnd($data['product_id']);		

        $product_info = $this->model_catalog_product->getProduct($data['product_id']);
		
		$data['cfp_setting'] = $this->model_setting_setting->getSetting('module_so_call_for_price');
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $data['base'] = $this->config->get('config_ssl');
        } else {
            $data['base'] = $this->config->get('config_url');
        }
				
        if ((float)$product_info['price']) {
            $data['price_0'] = $product_info['price'];
        } else {
            $data['price_0'] = false;
        }		
		if ((float)$product_info['special']) $data['discount'] = '-'.round((($product_info['price'] - $product_info['special'])/$product_info['price'])*100, 0).'%';
        	else  $data['discount'] = false;
			
		$sold = 0; 
			 $totalQuantity = $product_info['quantity'];
			 if($this->model_extension_so_theme_soconfig_general->getUnitsSold($product_info['product_id'])){ 
			 $sold = $this->model_extension_so_theme_soconfig_general->getUnitsSold($product_info['product_id']); 
			 } 

			 $data['orders'] = sprintf($this->language->get('text_product_orders'),$sold,$totalQuantity); 	
		
		$results = $this->model_catalog_product->getRelated($data['product_id']);

			foreach ($results as $result) {
				if (is_file(DIR_IMAGE . html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'))) {
					$image = $this->model_tool_image->resize(html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'), $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}
				
				if ((float)$result['price']) {
					$price_0 = $result['price'];
				} else {
					$price_0 = false;
				}					

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}
				
				if ((float)$result['special']) 
					$discount = '-'.round((($result['price'] - $result['special'])/$result['price'])*100, 0).'%';
				else  $discount = false;

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				$product_data = [
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('config_product_description_length')) . '..',
					'price'       => $price,
					'price_0'     => $price_0,
					'special'     => $special,
					'discount'    => $discount,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'href'        => $this->url->link('product/product', 'language=' . $this->config->get('config_language') . '&product_id=' . $result['product_id'])
				];

				$data['products'][] = $product_data;
				$data['our_url'] = $this->registry->get('url');
			}		
			

		$this->load->model('extension/so_theme/module/so_color_swatches_pro');
			
        $data['option_data'] = array();
        if ($this->config->get('module_so_color_swatches_pro_status') && $this->config->get('module_so_color_swatches_pro_enable_product_page')) {
                    $data['width_product_page'] = $this->config->get('module_so_color_swatches_pro_width_product_page');
                    if ($data['width_product_page'] == 0) {
                        $data['width_product_page'] = 20;
                    }
                    $data['height_product_page'] = $this->config->get('module_so_color_swatches_pro_height_product_page');
                    if ($data['height_product_page'] == 0) {
                        $data['height_product_page'] = 20;
                    }
                    $data['colorswatch_type'] = $this->config->get('module_so_color_swatches_pro_type');
 
                    $option_selected = $this->config->get('module_so_color_swatches_pro_option');
                    $product_option = $this->model_extension_so_theme_module_so_color_swatches_pro->getProductOptionsByOptionId($data['product_id'], $option_selected);
                    if ($product_option) {
                        $data['product_option_id'] = $product_option['product_option_id'];
                    }
                    $data['option_selected'] = $option_selected;
    
                    $options = $this->model_extension_so_theme_module_so_color_swatches_pro->getProductOptions($data['product_id']);
						
                    foreach ($options as $option) {
                        $product_option_value_data = array();					
                        foreach ($option['product_option_value'] as $option_value) {
                            if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                                $p_image = $this->model_extension_so_theme_module_so_color_swatches_pro->getProductImages($data['product_id'], $option_value['option_value_id']);
								
								if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
									$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
								} else {
									$price = false;
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
                                    'image'                   => $this->model_tool_image->resize($option_value['image'], $data['width_product_page'], $data['height_product_page']),
                                    'price'                   => $price,
                                    'price_prefix'            => $option_value['price_prefix'],
                                    'color_image'             => $pimage,
                                    'color_thumb_image'       => $p_thumbimage,
                                    'product_image_id'        => $product_image_id
                                );
                            }
                        }
                        $data['option_data'][] = array(
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
        $data['option_data'] = array_shift($data['option_data']);			
				
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');
		if($this->session->data['device']=='mobile' && $platforms_mobile != 0){
			$this->load->language('extension/so_theme/soconfig/somobile','',$this->config->get('config_language'));
		    $route = 'extension/so_theme/somobile/template/product/product';
		} else {
			$route = 'extension/so_theme/product/product';
		}		
		
	}	

	public function so_theme_product_after(&$route, &$args, &$output){	
	    if (isset($args['cfp_setting']['module_so_call_for_price_status']) && ($args['price_0'] <= 0)):
		$yt= '';	
                if ($args['cfp_setting']['module_so_call_for_price_status'] && $args['price_0'] <= 0):
		
                    if (isset($args['cfp_setting']['module_so_call_for_price_replace_cart']) && $args['cfp_setting']['module_so_call_for_price_replace_cart'] == '0'):						
                     $yt .= '<a data-fancybox data-type="ajax" data-src="'.$args['base'].'index.php?route=extension/so_theme/module/so_call_for_price&product_id='.$args['product_id'].'" href="javascript:;" class="callforprice" style="color: #ff0000; font-weight: bold;"><i class="fa fa-phone" ></i> '.$args['text_price_0'].'</a>';
                    endif;        
                else:
                    $yt .= '<span class="price-new">'.$args['price'].'</span>';
                endif;
				
		$output = str_replace('<span class="price-new">'.$args['price'].'</span>',$yt,$output);	
		
		
		
		$yt1='';
		if ($args['tax'] && $args['cfp_setting']['module_so_call_for_price_status'] && ($args['price_0'] > 0)) {
		$yt1 .= ''.$args['text_tax'].' '.$args['tax'].'';
		}
		$output = str_replace(''.$args['text_tax'].' '.$args['tax'].'',$yt1,$output);
		
        $yt2='';
                    if (isset($args['cfp_setting']['module_so_call_for_price_hide_cart']) && ($args['cfp_setting']['module_so_call_for_price_hide_cart'] == '0')):
                        if (isset($args['cfp_setting']['module_so_call_for_price_replace_cart']) && ($args['cfp_setting']['module_so_call_for_price_replace_cart'] == '1')):
                         $yt2 .='<input type="button" value="'.$args['text_price_0'].'" data-fancybox data-type="ajax" data-src="'.$args['base'].'index.php?route=extension/so_theme/module/so_call_for_price&product_id='.$args['product_id'].'" data-loading-text="'.$args['text_loading'].'" class="btn btn-mega btn-lg callforprice">';
                        else:
                         $yt2 .='<input type="button" value="'.$args['button_cart'].'" data-loading-text="'.$args['text_loading'].'" class="btn btn-mega btn-lg" style="cursor: default; background: #eee; color: #ccc; border: 1px solid #eee; text-shadow: none; box-shadow: none;">';
                        endif;
                    else:
                        if (isset($args['cfp_setting']['module_so_call_for_price_replace_cart']) && ($args['cfp_setting']['module_so_call_for_price_replace_cart'] == '1')):
                        $yt2 .='<input type="button" value="'.$args['text_price_0'].'" data-fancybox data-type="ajax" data-src="'.$args['base'].'index.php?route=extension/so_theme/module/so_call_for_price&product_id='.$args['product_id'].'" data-loading-text="'.$args['text_loading'].'" class="btn btn-mega btn-lg ">';
                        endif;
                    endif;
		
		$output = str_replace('<input type="button" value="'.$args['button_cart'].'" data-loading-text="'.$args['text_loading'].'" id="button-cart" class="btn btn-mega">',$yt2,$output);
		
		
		$output = str_replace('<input type="button" value="'.$args['text_buynow'].'" data-loading-text="'.$args['text_loading'].'" class="btn btn-checkout " />','',$output);
		endif;
	
	}	
	
	public function so_theme_search_before(&$route, &$data){
		$soconfig = new \ClassSoconfig($this->registry); 		
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');			
		if($this->session->data['device']=='mobile' && $platforms_mobile != 0){		
			$this->load->language('extension/so_theme/soconfig/somobile','',$this->config->get('config_language'));
			$route = 'extension/so_theme/somobile/template/product/search';			
        }	else {
			$this->load->language('extension/so_theme/soconfig/soconfig','',$this->config->get('config_language'));
			$route = 'extension/so_theme/product/search';
		}	
	}	
	
	public function so_theme_category_before(&$route, &$data){	
		$this->load->language('extension/so_theme/soconfig/soconfig','',$this->config->get('config_language'));
		$soconfig = new \ClassSoconfig($this->registry); 
		$this->registry->set('soconfig', $soconfig);
        $data['our_url'] = $this->registry->get('url');
        /*=======url query parameters=======*/ 		
		$data['url_sidebarsticky'] = isset($this->request->get['sidebarsticky']) ? $this->request->get['sidebarsticky'] : '' ; 
		$data['url_cartinfo'] = isset($this->request->get['cartinfo']) ? $this->request->get['cartinfo'] : '' ; 
		$data['url_thumbgallery'] = isset($this->request->get['thumbgallery']) ? $this->request->get['thumbgallery'] : '' ; 
		$data['url_listview'] = isset($this->request->get['listview']) ? $this->request->get['listview'] : '' ; 
		$data['url_asidePosition'] = isset($this->request->get['asidePosition']) ? $this->request->get['asidePosition'] : '' ; 
		$data['url_asideType'] = isset($this->request->get['asideType']) ? $this->request->get['asideType'] : '' ; 
		$data['url_layoutbox'] = isset($this->request->get['layoutbox']) ? $this->request->get['layoutbox'] : '' ; 		
		
		$data['so_config'] = $this->soconfig;
 
		if (isset($this->request->get['path'])) {
			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);
            $category_id = (int)array_pop($parts);

		} else {
			$category_id = 0;
		}
        $results = $this->model_catalog_category->getCategories($category_id);	
        foreach ($data['categories'] as $key => $item) {		
		    foreach ($results as $number => $result) {
				if (is_file(DIR_IMAGE . html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'))) {
					$image[$number] = $this->model_tool_image->resize(html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'), $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
				} else {
					$image[$number] = $this->model_tool_image->resize('placeholder.png', $this->config->get('config_image_related_width'), $this->config->get('config_image_related_height'));
				}
				if ($number == $key) {
				    $data['categories'][$key]['thumb'] = $image[$number];
			    }
			}		
		}
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');			
		if($this->session->data['device']=='mobile' && $platforms_mobile != 0){		
			$this->load->language('extension/so_theme/soconfig/somobile','',$this->config->get('config_language'));
			$route = 'extension/so_theme/somobile/template/product/category';			
        } else {	
		    $route = 'extension/so_theme/product/category';
		}
	}
	public function so_theme_manufacturer_info_before(&$route, &$data){
		$route = 'extension/so_theme/product/manufacturer_info';
	}	
	public function so_theme_manufacturer_list_before(&$route, &$data){
		$route = 'extension/so_theme/product/manufacturer_list';
	}	
	public function so_theme_special_before(&$route, &$data){
		$route = 'extension/so_theme/product/special';
	}	
	
	public function so_theme_thumb_before(&$route, &$data){	
        $this->load->language('extension/so_theme/module/so_call_for_price','',$this->config->get('config_language'));
		$this->load->language('extension/so_theme/soconfig/soconfig','',$this->config->get('config_language'));
		$soconfig = new \ClassSoconfig($this->registry); 
		$this->load->model('extension/so_theme/soconfig/general');
		$this->registry->set('soconfig', $soconfig);		
		$data['soconfig'] = $this->soconfig;		
        $data['our_url'] = $this->registry->get('url');
		
		$data['url_sidebarsticky'] = isset($this->request->get['sidebarsticky']) ? $this->request->get['sidebarsticky'] : '' ; 
		$data['url_cartinfo'] = isset($this->request->get['cartinfo']) ? $this->request->get['cartinfo'] : '' ; 
		$data['url_thumbgallery'] = isset($this->request->get['thumbgallery']) ? $this->request->get['thumbgallery'] : '' ; 
		$data['url_listview'] = isset($this->request->get['listview']) ? $this->request->get['listview'] : '' ; 
		$data['url_asidePosition'] = isset($this->request->get['asidePosition']) ? $this->request->get['asidePosition'] : '' ; 
		$data['url_asideType'] = isset($this->request->get['asideType']) ? $this->request->get['asideType'] : '' ; 
		$data['url_layoutbox'] = isset($this->request->get['layoutbox']) ? $this->request->get['layoutbox'] : '' ; 
		
		$data['href_quickview'] = htmlspecialchars_decode($this->url->link('extension/so_theme/soconfig/quickview' , 'language=' . $this->config->get('config_language') . '&product_id='.$data['product_id'] ));
		
		$data['special_end_date'] = $this->model_extension_so_theme_soconfig_general->getDateEnd($data['product_id']);

		$data['image_galleries'] = array(); 

		$this->load->model('localisation/currency');
		$results_currencies = $this->model_localisation_currency->getCurrencyByCode($this->session->data['currency']);
		
		$special = str_replace($results_currencies['symbol_left'],'',$data['special']);
	    $special = str_replace($results_currencies['symbol_right'],'',$special);
		$price = str_replace($results_currencies['symbol_left'],'',$data['price']);
	    $price = str_replace($results_currencies['symbol_right'],'',$price);

		if ((float)$special) $data['discount'] = '-'.round((((float)$price - (float)$special)/(float)$price)*100, 0).'%'; 
        else  $data['discount'] = false; 

        //$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$data['reviews']); 	
	
        $cartfix = str_replace($this->config->get('config_image_product_width').'x'.$this->config->get('config_image_product_height'),$this->config->get('config_image_cart_width').'x'.$this->config->get('config_image_cart_height'),$data['thumb']);

	    $data['first_gallery'] = array(
	    'cart' => $cartfix, 
	    'thumb' => $data['thumb'] 
	    ); 	
		
		$image_galleries = $this->model_catalog_product->getImages($data['product_id']); 
		
        $product_info = $this->model_catalog_product->getProduct($data['product_id']);

		$data['cfp_setting'] = $this->model_setting_setting->getSetting('module_so_call_for_price');
        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $data['base'] = $this->config->get('config_ssl');
        } else {
            $data['base'] = $this->config->get('config_url');
        }
				
        if (isset($product_info['price']) && (float)$product_info['price']) {
            $data['price_0'] = $product_info['price'];
        } else {
            $data['price_0'] = false;
        }	
		
		 foreach ($image_galleries as $image_gallery) { 
			 $data['image_galleries'][] = array( 
			 'cart' => $this->model_tool_image->resize($image_gallery['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height')), 
			 'thumb' => $this->model_tool_image->resize($image_gallery['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height')) 
			 ); 
		 } 
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');			
		if($this->session->data['device']=='mobile' && $platforms_mobile){
		    $route = 'extension/so_theme/somobile/template/product/thumb';
		} else {
			$route = 'extension/so_theme/product/thumb';
		}
	}
	
	public function so_column_left_before(&$route, &$data){
		$route = 'extension/so_theme/common/column_left';
	}		
    public function so_column_left_after(string &$route, array &$data, mixed &$output): void {}

	public function so_column_right_before(&$route, &$data){
		$route = 'extension/so_theme/common/column_left';
	}		
    public function so_column_right_after(string &$route, array &$data, mixed &$output): void {}
	
	public function so_theme_information_before(&$route, &$data){
		$soconfig = new \ClassSoconfig($this->registry); 		
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');	
	
		if($this->session->data['device']=='mobile' && $platforms_mobile != 0){		
			$route = 'extension/so_theme/somobile/template/information/information';	
        }	else {
			$route = 'extension/so_theme/information/information';				
		}				
	}		
    public function so_theme_contact_before(&$route, &$data){
		$soconfig = new \ClassSoconfig($this->registry); 
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');
		$this->registry->set('soconfig', $soconfig);
		$data['soconfig'] = $this->soconfig;		
		if($this->session->data['device']=='mobile' && $platforms_mobile != 0){
			$this->load->language('extension/so_theme/soconfig/somobile','',$this->config->get('config_language'));
			$route = 'extension/so_theme/somobile/template/information/contact';
		} else {
			$this->load->language('extension/so_theme/soconfig/soconfig','',$this->config->get('config_language'));
			$route = 'extension/so_theme/information/contact';
		}	
	}
	public function so_theme_sitemap_before(&$route, &$data){
		$soconfig = new \ClassSoconfig($this->registry); 
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');
		$this->registry->set('soconfig', $soconfig);
		$data['soconfig'] = $this->soconfig;		
		if($this->session->data['device']=='mobile' && $platforms_mobile != 0){
			$route = 'extension/so_theme/somobile/template/information/sitemap';
		} else {
			$route = 'extension/so_theme/information/sitemap';
		}
	}
	public function so_theme_not_found_before(&$route, &$data){
		$route = 'extension/so_theme/error/not_found';
	}
	public function so_theme_blog_before(&$route, &$data){
		$soconfig = new \ClassSoconfig($this->registry); 
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');
		$this->registry->set('soconfig', $soconfig);
		$data['soconfig'] = $this->soconfig;		
		if($this->session->data['device']=='mobile' && $platforms_mobile != 0){
			$route = 'extension/so_theme/somobile/template/cms/blog';
		} else {
			$route = 'extension/so_theme/cms/blog';
		}
	}
	public function so_theme_blog_info_before(&$route, &$data){
		$soconfig = new \ClassSoconfig($this->registry); 
		$platforms_mobile 		= $soconfig->get_settings('platforms_mobile');
		$this->registry->set('soconfig', $soconfig);
		$data['soconfig'] = $this->soconfig;		
		if($this->session->data['device']=='mobile' && $platforms_mobile != 0){
			$route = 'extension/so_theme/somobile/template/cms/blog_info';
		} else {
			$route = 'extension/so_theme/cms/blog_info';
		}
	}
}