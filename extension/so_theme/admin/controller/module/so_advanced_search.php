<?php
namespace Opencart\Admin\Controller\Extension\SoTheme\Module;
class SoAdvancedSearch extends \Opencart\System\Engine\Controller {
	public $error = array();
	
	public function index() {
		$this->load->language('extension/so_theme/module/so_advanced_search','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));
		$data['objlang'] = $this->language;

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$module_id = $this->model_setting_module->addModule('so_theme.so_advanced_search', $this->request->post);
			} else {
				$module_id = $this->request->get['module_id'];
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->post['save_stay']) && $this->request->post['save_stay'] == 1) {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_advanced_search', 'user_token=' . $this->session->data['user_token'] . '&module_id='.$module_id, true));
			}
			else {
				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
			}
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
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
			'href' => $this->url->link('extension/so_theme/module/so_advanced_search', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/so_theme/module/so_advanced_search', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/so_theme/module/so_advanced_search', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '1';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_advanced_search', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/so_theme/module/so_advanced_search')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function install() {
		$this->setupEvent();
		$this->load->model('extension/so_theme/module/so_advanced_search');
		$this->model_extension_so_theme_module_so_advanced_search->install();
	}
	
    private function setupEvent() {
        $this->load->model('setting/event');

        $this->removeEvent();
		
		$dataFroBefore = array(
		   'code' => 'so_advanced_search',
		   'description' => 'so_advanced_search',
		   'trigger' => 'catalog/view/product/search/before',
		   'action' => 'extension/so_theme/event/so_advanced_search.search_before',
		   'status' => 1,
		   'sort_order' => 1,
		);
		
		$dataFroAfter = array(
		   'code' => 'so_advanced_search',
		   'description' => 'so_advanced_search',
		   'trigger' => 'catalog/view/product/search/after',
		   'action' => 'extension/so_theme/event/so_advanced_search.search_after',
		   'status' => 1,
		   'sort_order' => 1,
		);		
		
		$this->model_setting_event->addEvent($dataFroBefore);
		$this->model_setting_event->addEvent($dataFroAfter);

		
		$dataAdmin = array(
		   'code' => 'so_advanced_search',
		   'description' => 'so_advanced_search',
		   'trigger' => 'admin/view/common/column_left/before',
		   'action' => 'extension/so_theme/event/so_advanced_search.so_menu_before',
		   'status' => 1,
		   'sort_order' => 1,
		);

	    $this->model_setting_event->addEvent($dataAdmin);
    }		

	public function uninstall() {
		$this->load->model('extension/so_theme/module/so_advanced_search');
		$this->model_extension_so_theme_module_so_advanced_search->uninstall();
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('so_advanced_search');		
	}
	
    private function removeEvent() {
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode('so_advanced_search');
    }		
}