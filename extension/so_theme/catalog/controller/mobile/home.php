<?php
namespace Opencart\Catalog\Controller\Extension\SoTheme\Mobile;
require_once (DIR_EXTENSION.'so_theme/admin/view/template/soconfig/class/soconfig.php');
class Home extends \Opencart\System\Engine\Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));

		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}
		$soconfig = new \ClassSoconfig($this->registry); 
		$this->registry->set('soconfig', $soconfig);		
		
		$data['soconfig'] = $this->soconfig;
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_home'] = $this->load->controller('extension/so_theme/soconfig/content_mobile');
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
	
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		
		$this->response->setOutput($this->load->view('extension/so_theme/somobile/template/mobile/home', $data));
	}
}
