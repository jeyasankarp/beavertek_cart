<?php
namespace Opencart\Catalog\Controller\Extension\SoTheme\Event;

require_once (DIR_EXTENSION.'so_theme/admin/view/template/soconfig/class/soconfig.php');

class SoSomobile extends \Opencart\System\Engine\Controller {
	
	public function so_theme_home_before(&$route, &$data){}
	public function so_theme_header_before(&$route, &$data){}
	public function so_theme_footer_before(&$route, &$data){}
	public function so_theme_search_before(&$route, &$data){}
	public function so_theme_language_before(&$route, &$data){}
	public function so_theme_currency_before(&$route, &$data){}
	public function so_theme_product_before(&$route, &$data){}	
	public function so_theme_category_before(&$route, &$data){}
	public function so_theme_information_before(&$route, &$data){}
	public function so_theme_contact_before(&$route, &$data){}
	public function so_theme_sitemap_before(&$route, &$data){}
	
}