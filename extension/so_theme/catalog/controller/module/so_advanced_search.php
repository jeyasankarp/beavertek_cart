<?php
namespace Opencart\Catalog\Controller\Extension\SoTheme\Module;
class SoAdvancedSearch extends \Opencart\System\Engine\Controller {	
	public function index($setting) {
		static $module = 0;

		$this->load->language('extension/so_theme/module/so_advanced_search','',$this->config->get('config_language_catalog'));
		$data['objlang'] = $this->language;

		$this->load->model('extension/so_theme/module/so_advanced_search');

		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_advanced_search/css/style.css');


		if (isset($this->request->get['make_id'])) {
			$data['make_id'] = $this->request->get['make_id'];
		}
		else {
			$data['make_id'] = '';
		}

		if (isset($this->request->get['model_id'])) {
			$data['model_id'] = $this->request->get['model_id'];
		}
		else {
			$data['model_id'] = '';
		}

		if (isset($this->request->get['engine_id'])) {
			$data['engine_id'] = $this->request->get['engine_id'];
		}
		else {
			$data['engine_id'] = '';
		}

		if (isset($this->request->get['year_id'])) {
			$data['year_id'] = $this->request->get['year_id'];
		}
		else {
			$data['year_id'] = '';
		}

		$data['makes'] = $this->model_extension_so_theme_module_so_advanced_search->getMakes();
		$data['models'] = $this->model_extension_so_theme_module_so_advanced_search->getModels();
		$data['engines'] = $this->model_extension_so_theme_module_so_advanced_search->getEngines();
		$data['years'] = $this->model_extension_so_theme_module_so_advanced_search->getYears();

		$data['module'] = $module++;

		return $this->load->view('extension/so_theme/module/so_advanced_search/default', $data);
	}

	public function getModel() {
		$this->load->language('extension/so_theme/module/so_advanced_search');

		$html = '';
		$html .= '<option value="">'.$this->language->get('text_select_make').'</option>';
		if (isset($this->request->post['make_id']) && !empty($this->request->post['make_id'])) {
			$this->load->model('extension/so_theme/module/so_advanced_search');
			$models = $this->model_extension_so_theme_module_so_advanced_search->getModels(array('make_id'=>$this->request->post['make_id']));
			if (!empty($models)) {
				foreach ($models as $model) {
					$html .= '<option value="'.$model['model_id'].'">'.$model['model_name'].'</option>';
				}
			}
		}

		echo $html;
		die();
	}

	public function getEngine() {
		$this->load->language('extension/so_theme/module/so_advanced_search','',$this->config->get('config_language_catalog'));

		$html = '';
		$html .= '<option value="">'.$this->language->get('text_select_engine').'</option>';
		if (isset($this->request->post['model_id']) && !empty($this->request->post['model_id'])) {
			$this->load->model('extension/so_theme/module/so_advanced_search');
			$engines = $this->model_extension_so_theme_module_so_advanced_search->getEngines(array('model_id'=>$this->request->post['model_id']));
			if (!empty($engines)) {
				foreach ($engines as $engine) {
					$html .= '<option value="'.$engine['engine_id'].'">'.$engine['engine_name'].'</option>';
				}
			}
		}

		echo $html;
		die();
	}

	public function getYear() {
		$this->load->language('extension/so_theme/module/so_advanced_search','',$this->config->get('config_language_catalog'));

		$html = '';
		$html .= '<option value="">'.$this->language->get('text_select_year').'</option>';
		if (isset($this->request->post['engine_id']) && !empty($this->request->post['engine_id'])) {
			$this->load->model('extension/so_theme/module/so_advanced_search');
			$years = $this->model_extension_so_theme_module_so_advanced_search->getYears(array('engine_id'=>$this->request->post['engine_id']));
			if (!empty($years)) {
				foreach ($years as $year) {
					$html .= '<option value="'.$year['year_id'].'">'.$year['year_name'].'</option>';
				}
			}
		}

		echo $html;
		die();
	}
}