<?php
namespace Opencart\Admin\Controller\Extension\SoTheme\Module;
class SoColorSwatchesPro extends \Opencart\System\Engine\Controller {	
	private $error = array();

	public function index() {
		$this->load->language('extension/so_theme/module/so_color_swatches_pro','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');
		$this->load->model('extension/so_theme/module/so_color_swatches_pro');
		$data['objlang']	= $this->language;

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
			$this->setupEvent();
			
			$action = isset($this->request->post["action"]) ? $this->request->post["action"] : "";
			unset($this->request->post['action']);

			$this->model_setting_setting->editSetting('module_so_color_swatches_pro', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if($action == "save_edit") {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_color_swatches_pro', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			}else {
				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
			}
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/so_theme/module/so_color_swatches_pro', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/so_theme/module/so_color_swatches_pro', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		$data['option_selected']	= $this->model_extension_so_theme_module_so_color_swatches_pro->getSelectOption();

		if (isset($this->request->post['module_so_color_swatches_pro_status'])) {
			$data['status'] = $this->request->post['module_so_color_swatches_pro_status'];
		} else {
			$data['status'] = $this->config->get('module_so_color_swatches_pro_status');
		}

		if (isset($this->request->post['module_so_color_swatches_pro_option'])) {
			$data['color_swatch_option'] = $this->request->post['module_so_color_swatches_pro_option'];
		} else {
			$data['color_swatch_option'] = $this->config->get('module_so_color_swatches_pro_option');
		}

		if (isset($this->request->post['module_so_color_swatches_pro_type'])) {
			$data['colorswatch_type'] = $this->request->post['module_so_color_swatches_pro_type'];
		} else {
			$data['colorswatch_type'] = $this->config->get('module_so_color_swatches_pro_type');
		}

		if (isset($this->request->post['module_so_color_swatches_pro_enable_product_list'])) {
			$data['enable_product_list'] = $this->request->post['module_so_color_swatches_pro_enable_product_list'];
		} else {
			$data['enable_product_list'] = $this->config->get('module_so_color_swatches_pro_enable_product_list');
		}

		if (isset($this->request->post['module_so_color_swatches_pro_width_product_list'])) {
			$data['width_product_list'] = $this->request->post['module_so_color_swatches_pro_width_product_list'];
		} else {
			$data['width_product_list'] = $this->config->get('module_so_color_swatches_pro_width_product_list');
		}

		if (isset($this->request->post['module_so_color_swatches_pro_height_product_list'])) {
			$data['height_product_list'] = $this->request->post['module_so_color_swatches_pro_height_product_list'];
		} else {
			$data['height_product_list'] = $this->config->get('module_so_color_swatches_pro_height_product_list');
		}

		if (isset($this->request->post['module_so_color_swatches_pro_enable_product_page'])) {
			$data['enable_product_page'] = $this->request->post['module_so_color_swatches_pro_enable_product_page'];
		} else {
			$data['enable_product_page'] = $this->config->get('module_so_color_swatches_pro_enable_product_page');
		}

		if (isset($this->request->post['module_so_color_swatches_pro_width_product_page'])) {
			$data['width_product_page'] = $this->request->post['module_so_color_swatches_pro_width_product_page'];
		} else {
			$data['width_product_page'] = $this->config->get('module_so_color_swatches_pro_width_product_page');
		}

		if (isset($this->request->post['module_so_color_swatches_pro_height_product_page'])) {
			$data['height_product_page'] = $this->request->post['module_so_color_swatches_pro_height_product_page'];
		} else {
			$data['height_product_page'] = $this->config->get('module_so_color_swatches_pro_height_product_page');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_color_swatches_pro', $data));
	}
	
	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/so_theme/module/so_color_swatches_pro')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function install() {
		$this->load->model('extension/so_theme/module/so_color_swatches_pro');
		$this->load->model('setting/setting');
		$this->model_extension_so_theme_module_so_color_swatches_pro->addColumnProductImage();
		$setting_array = array(
			'module_so_color_swatches_pro_status'			=> 1,
			'module_so_color_swatches_pro_type'					=> 'click',
			'module_so_color_swatches_pro_enable_product_list'	=> 1,
			'module_so_color_swatches_pro_width_product_list'		=> 15,
			'module_so_color_swatches_pro_height_product_list'	=> 15,
			'module_so_color_swatches_pro_enable_product_page'	=> 1,
			'module_so_color_swatches_pro_width_product_page'		=> 20,
			'module_so_color_swatches_pro_height_product_page'	=> 20
		);
		$this->model_setting_setting->editSetting('module_so_color_swatches_pro', $setting_array);
	}
	
    private function setupEvent() {
        $this->load->model('setting/event');
        $this->load->model('extension/so_theme/module/soconfig/setting');
        $this->removeEvent();

        $this->model_extension_so_theme_module_soconfig_setting->addEvent('so_color_swatches','', "admin/controller/catalog/product|form/before", "extension/so_theme/event/so_color_swatches_pro.controller_before" , 1 , 1);
		
		$this->model_extension_so_theme_module_soconfig_setting->addEvent('so_color_swatches','', "admin/controller/catalog/product|form/after", "	extension/so_theme/event/so_color_swatches_pro.controller_after" , 1 , 1);
		
		$this->model_extension_so_theme_module_soconfig_setting->addEvent('so_color_swatches','', "admin/view/catalog/product_form/before", "extension/so_theme/event/so_color_swatches_pro.text_before" , 1 , 1);
		
		$this->model_extension_so_theme_module_soconfig_setting->addEvent('so_color_swatches','', "admin/view/catalog/product_form/after", "extension/so_theme/event/so_color_swatches_pro.text_after" , 1 , 1);
		
		$this->model_extension_so_theme_module_soconfig_setting->addEvent('so_color_swatches','', "admin/model/catalog/product.editProduct/before", "	extension/so_theme/event/so_color_swatches_pro.model_before" , 1 , 1);
		
		$this->model_extension_so_theme_module_soconfig_setting->addEvent('so_color_swatches','', "admin/model/catalog/product.editProduct/after", "	extension/so_theme/event/so_color_swatches_pro.model_after" , 1 , 1);
		
        $this->model_extension_so_theme_module_soconfig_setting->addEvent('so_color_swatches','', "admin/model/catalog/product.addProduct/before", "	extension/so_theme/event/so_color_swatches_pro.model_before" , 1 , 1);
		
		$this->model_extension_so_theme_module_soconfig_setting->addEvent('so_color_swatches','', "admin/model/catalog/product.addProduct/after", "	extension/so_theme/event/so_color_swatches_pro.model_after" , 1 , 1);	

        $this->model_extension_so_theme_module_soconfig_setting->addEvent('so_color_swatches','', "catalog/controller/product/product/before", "	extension/so_theme/event/so_color_swatches_pro.controller_before" , 1 , 1);		
		
		$this->model_extension_so_theme_module_soconfig_setting->addEvent('so_color_swatches','', "catalog/controller/product/product/after", "	extension/so_theme/event/so_color_swatches_pro.controller_after" , 1 , 1);

        $this->model_extension_so_theme_module_soconfig_setting->addEvent('so_color_swatches','', "catalog/view/product/product/before", "	extension/so_theme/event/so_color_swatches_pro.text_before" , 1 , 1);		
		
		$this->model_extension_so_theme_module_soconfig_setting->addEvent('so_color_swatches','', "catalog/view/product/product/after", "	extension/so_theme/event/so_color_swatches_pro.text_after" , 1 , 1);	
		
		$this->model_extension_so_theme_module_soconfig_setting->addEvent('so_color_swatches','', "catalog/view/product/thumb/before", "	extension/so_theme/event/so_color_swatches_pro.text_before" , 1 , 1);	
		
        $this->model_extension_so_theme_module_soconfig_setting->addEvent('so_color_swatches','', "catalog/controller/product/thumb/before", "	extension/so_theme/event/so_color_swatches_pro.controller_before" , 1 , 1);		
		
		$this->model_extension_so_theme_module_soconfig_setting->addEvent('so_color_swatches','', "catalog/view/product/thumb/after", "	extension/so_theme/event/so_color_swatches_pro.text_thumb_after" , 1 , 1);			
    }
	
    private function removeEvent() {
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode('so_color_swatches');
    }		

	public function uninstall() {
		$this->load->model('setting/setting');
        $this->model_setting_setting->deleteSetting('module_so_color_swatches_pro');
		
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('so_color_swatches');		
	}
}