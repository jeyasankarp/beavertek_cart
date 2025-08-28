<?php
namespace Opencart\Catalog\Controller\Extension\SoTheme\Module;
class SoLookbookSlider extends \Opencart\System\Engine\Controller {	
	public function index($setting) {
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_lookbook/css/styles.css');
		if (!defined ('LOOKBOOK_OWL_CAROUSEL')){
			$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_lookbook/css/animate.css');
			$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_lookbook/css/owl.carousel.css');
			$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_lookbook/css/owl.theme.min.css');
			$this->document->addScript('extension/so_theme/catalog/view/javascript/so_lookbook/js/owl.carousel.min.js');
			define( 'LOOKBOOK_OWL_CAROUSEL', 1 );
		}

		$this->load->model('extension/so_theme/module/so_lookbook');

		$sliderId = $setting['slide_id'];
		$data = array();
		if ($sliderId) {
			$slider = $this->model_extension_so_theme_module_so_lookbook->getSliderById($sliderId);
			if ($slider){
				if ($slider['status'] == 1) {
					$lookbooks = $this->model_extension_so_theme_module_so_lookbook->getLookBookBySlideId($sliderId);
					if (count($lookbooks)) {
						$data['lookbooks'] = $lookbooks;
					}
					$data['slider'] = $slider;

					$navigation = $slider['navigation'];
					if (!$navigation) {
						$navigation = $this->config->get('module_lookbook_navigation');
					}
					$data['navigation'] = $this->convertValue($navigation);

					$pagination = $slider['pagination'];
					if (!$pagination) {
						$pagination = $this->config->get('module_lookbook_pagination');
					}
					$data['pagination'] = $this->convertValue($pagination);

					$auto_play = $slider['auto_play'];
					if (!$auto_play) {
						$auto_play = $this->config->get('module_lookbook_auto_play');
					}
					$data['auto_play'] = $this->convertValue($auto_play);

					$data['auto_play_timeout'] = $slider['auto_play_timeout'];
					if ($data['auto_play_timeout'] == '') {
						$data['auto_play_timeout'] = $this->config->get('module_lookbook_auto_play_timeout');
					}

					$stop_auto = $slider['stop_auto'];
					if (!$stop_auto) {
						$stop_auto = $this->config->get('module_lookbook_stop_auto');
					}
					$data['stop_auto'] = $this->convertValue($stop_auto);

					$loop = $slider['loop'];
					if (!$loop) {
						$loop = $this->config->get('module_lookbook_loop');
					}
					$data['loop'] = $this->convertValue($loop);

					$next_image = $slider['next_image'];
					if ($next_image == '') {
						$next_image = $this->config->get('module_lookbook_next_image');
					}
					$data['next_image'] = htmlspecialchars_decode($next_image == '' ? '<i class="fa fa-angle-right"></i>' : $next_image);

					$prev_image = $slider['prev_image'];
					if ($prev_image == '') {
						$prev_image = $this->config->get('module_lookbook_prev_image');
					}
					$data['prev_image'] = htmlspecialchars_decode($prev_image == '' ? '<i class="fa fa-angle-left"></i>' : $prev_image);
				}
			}
		}
		return $this->load->view('extension/so_theme/module/so_lookbook/lookbook_slide', $data);
	}

	public function convertValue($value){
		if($value == 1){
			return 'true';
		}
		return 'false';
	}
}