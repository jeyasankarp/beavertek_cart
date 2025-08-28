<?php
namespace Opencart\Admin\Controller\Extension\SoTheme\Module;
class SoLookbookConfig extends \Opencart\System\Engine\Controller {	
	private $error = array();

	public function index() {
		$this->load->language('extension/so_theme/module/so_lookbook_config');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_lookbook', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if(isset($this->request->post['save_stay']) and $this->request->post['save_stay']=1) {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_lookbook_config', 'user_token=' . $this->session->data['user_token'], true));
			}
			else {
				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
			}
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

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
			'href' => $this->url->link('extension/so_theme/module/so_lookbook_config', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/so_theme/module/so_lookbook_config', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_lookbook_min_image_width'])) {
			$data['min_image_width'] = $this->request->post['module_lookbook_min_image_width'];
		} else {
			$data['min_image_width'] = $this->config->get('module_lookbook_min_image_width');
		}

		if (isset($this->request->post['module_lookbook_min_image_height'])) {
			$data['min_image_height'] = $this->request->post['module_lookbook_min_image_height'];
		} else {
			$data['min_image_height'] = $this->config->get('module_lookbook_min_image_height');
		}

		if (isset($this->request->post['module_lookbook_max_image_width'])) {
			$data['max_image_width'] = $this->request->post['module_lookbook_max_image_width'];
		} else {
			$data['max_image_width'] = $this->config->get('module_lookbook_max_image_width');
		}

		if (isset($this->request->post['module_lookbook_max_image_height'])) {
			$data['max_image_height'] = $this->request->post['module_lookbook_max_image_height'];
		} else {
			$data['max_image_height'] = $this->config->get('module_lookbook_max_image_height');
		}

		if (isset($this->request->post['module_lookbook_max_upload_filesize'])) {
			$data['max_upload_filesize'] = $this->request->post['module_lookbook_max_upload_filesize'];
		} else {
			$data['max_upload_filesize'] = $this->config->get('module_lookbook_max_upload_filesize');
		}

		if (isset($this->request->post['module_lookbook_allowed_extensions'])) {
			$data['allowed_extensions'] = $this->request->post['module_lookbook_allowed_extensions'];
		} else {
			$data['allowed_extensions'] = $this->config->get('module_lookbook_allowed_extensions');
		}

		if (isset($this->request->post['module_lookbook_pin_width'])) {
			$data['pin_width'] = $this->request->post['module_lookbook_pin_width'];
		} else {
			$data['pin_width'] = $this->config->get('module_lookbook_pin_width');
		}

		if (isset($this->request->post['module_lookbook_pin_height'])) {
			$data['pin_height'] = $this->request->post['module_lookbook_pin_height'];
		} else {
			$data['pin_height'] = $this->config->get('module_lookbook_pin_height');
		}

		if (isset($this->request->post['module_lookbook_pin_default'])) {
			$data['pin_default'] = $this->request->post['module_lookbook_pin_default'];
		} else {
			$data['pin_default'] = $this->config->get('module_lookbook_pin_default');
		}

		if (isset($this->request->post['module_lookbook_pin_price'])) {
			$data['pin_price'] = $this->request->post['module_lookbook_pin_price'];
		} else {
			$data['pin_price'] = $this->config->get('module_lookbook_pin_price');
		}

		if (isset($this->request->post['module_lookbook_pin_background'])) {
			$data['pin_background'] = $this->request->post['module_lookbook_pin_background'];
		} else {
			$data['pin_background'] = $this->config->get('module_lookbook_pin_background');
		}

		if (isset($this->request->post['module_lookbook_pin_text'])) {
			$data['pin_text'] = $this->request->post['module_lookbook_pin_text'];
		} else {
			$data['pin_text'] = $this->config->get('module_lookbook_pin_text');
		}

		if (isset($this->request->post['module_lookbook_popup_image_width'])) {
			$data['popup_image_width'] = $this->request->post['module_lookbook_popup_image_width'];
		} else {
			$data['popup_image_width'] = $this->config->get('module_lookbook_popup_image_width');
		}

		if (isset($this->request->post['module_lookbook_popup_image_height'])) {
			$data['popup_image_height'] = $this->request->post['module_lookbook_popup_image_height'];
		} else {
			$data['popup_image_height'] = $this->config->get('module_lookbook_popup_image_height');
		}

		if (isset($this->request->post['module_lookbook_navigation'])) {
			$data['navigation'] = $this->request->post['module_lookbook_navigation'];
		} else {
			$data['navigation'] = $this->config->get('module_lookbook_navigation');
		}

		if (isset($this->request->post['module_lookbook_pagination'])) {
			$data['pagination'] = $this->request->post['module_lookbook_pagination'];
		} else {
			$data['pagination'] = $this->config->get('module_lookbook_pagination');
		}

		if (isset($this->request->post['module_lookbook_auto_play'])) {
			$data['auto_play'] = $this->request->post['module_lookbook_auto_play'];
		} else {
			$data['auto_play'] = $this->config->get('module_lookbook_auto_play');
		}

		if (isset($this->request->post['module_lookbook_auto_play_timeout'])) {
			$data['auto_play_timeout'] = $this->request->post['module_lookbook_auto_play_timeout'];
		} else {
			$data['auto_play_timeout'] = $this->config->get('module_lookbook_auto_play_timeout');
		}

		if (isset($this->request->post['module_lookbook_stop_auto'])) {
			$data['stop_auto'] = $this->request->post['module_lookbook_stop_auto'];
		} else {
			$data['stop_auto'] = $this->config->get('module_lookbook_stop_auto');
		}

		if (isset($this->request->post['module_lookbook_loop'])) {
			$data['loop'] = $this->request->post['module_lookbook_loop'];
		} else {
			$data['loop'] = $this->config->get('module_lookbook_loop');
		}

		if (isset($this->request->post['module_lookbook_next_image'])) {
			$data['next_image'] = $this->request->post['module_lookbook_next_image'];
		} else {
			$data['next_image'] = $this->config->get('module_lookbook_next_image');
		}

		if (isset($this->request->post['module_lookbook_prev_image'])) {
			$data['prev_image'] = $this->request->post['module_lookbook_prev_image'];
		} else {
			$data['prev_image'] = $this->config->get('module_lookbook_prev_image');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_lookbook/lookbook_config', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/so_theme/module/so_lookbook_config')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function install() {
		$this->load->model('setting/setting');
		$default_config = array(
			'module_lookbook_min_image_width'	=> 300,
			'module_lookbook_min_image_height'	=> 400,
			'module_lookbook_max_image_width'	=> 1920,
			'module_lookbook_max_image_height'	=> 1360,
			'module_lookbook_max_upload_filesize'	=> 2097152,
			'module_lookbook_allowed_extensions'	=> 'jpg,jpeg,png,gif',
			'module_lookbook_pin_width'				=> 40,
			'module_lookbook_pin_height'			=> 40,
			'module_lookbook_pin_default'			=> '+',
			'module_lookbook_pin_price'				=> 0,
			'module_lookbook_pin_background'		=> '65affa',
			'module_lookbook_pin_text'				=> 'ffffff',
			'module_lookbook_popup_image_width'		=> 240,
			'module_lookbook_popup_image_height'	=> 320,
			'module_lookbook_navigation'			=> 1,
			'module_lookbook_pagination'			=> 1,
			'module_lookbook_auto_play'				=> 1,
			'module_lookbook_auto_play_timeout'		=> 5000,
			'module_lookbook_stop_auto'				=> 1,
			'module_lookbook_loop'					=> 1,
			'module_lookbook_next_image'			=> '',
			'module_lookbook_prev_image'			=> '',
		);
		$this->model_setting_setting->editSetting('module_lookbook', $default_config);
	}

	public function uninstall() {
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('module_lookbook');
	}
}