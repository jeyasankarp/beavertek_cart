<?php
namespace Opencart\Admin\Controller\Extension\SoTheme\Theme;
class Basic extends \Opencart\System\Engine\Controller {
	public function index(): void {

		$this->load->language('extension/so_theme/theme/basic');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/so_theme/theme/basic', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id'])
		];

		$data['save'] = $this->url->link('extension/so_theme/theme/basic|save', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $this->request->get['store_id']);
		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=theme');

		if (isset($this->request->get['store_id'])) {
			$this->load->model('setting/setting');

			$setting_info = $this->model_setting_setting->getSetting('theme_so_theme', $this->request->get['store_id']);
		}

		if (isset($setting_info['theme_so_theme_status'])) {
			$data['theme_so_theme_status'] = $setting_info['theme_so_theme_status'];
		} else {
			$data['theme_so_theme_status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/theme/basic', $data));
	}

	public function save(): void {
		$this->load->language('extension/so_theme/theme/basic');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/so_theme/theme/basic')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('theme_so_theme', $this->request->post, $this->request->get['store_id']);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
