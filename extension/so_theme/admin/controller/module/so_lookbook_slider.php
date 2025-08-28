<?php
namespace Opencart\Admin\Controller\Extension\SoTheme\Module;
class SoLookbookSlider extends \Opencart\System\Engine\Controller {	
	private $error = array();
	
	public function index() {
		$this->load->language('extension/so_theme/module/so_lookbook_slider');
		$this->load->model('setting/module');
		$this->load->model('extension/so_theme/module/so_lookbook');
		
		$this->document->setTitle($this->language->get('heading_title'));

		$this->getList();
	}

	public function add() {
		$this->load->language('extension/so_theme/module/so_lookbook_slider');
		$this->load->model('extension/so_theme/module/so_lookbook');
		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$slide_id = $this->model_extension_so_theme_module_so_lookbook->addLookBookSlider($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			$this->session->data['text_layout'] = $this->language->get('text_layout');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if(isset($this->request->post['save_stay']) and $this->request->post['save_stay']=1) {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_lookbook_slider|edit', 'user_token=' . $this->session->data['user_token'] . '&slide_id='.$slide_id, true));
			}
			else {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_lookbook_slider', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/so_theme/module/so_lookbook_slider');
		$this->load->model('extension/so_theme/module/so_lookbook');
		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_so_theme_module_so_lookbook->editLookBookSlider($this->request->get['slide_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			$this->session->data['text_layout'] = $this->language->get('text_layout');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if(isset($this->request->post['save_stay']) and $this->request->post['save_stay']=1) {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_lookbook_slider|edit', 'user_token=' . $this->session->data['user_token'] . '&slide_id='.$this->request->get['slide_id'], true));
			}
			else {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_lookbook_slider', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
		}

		$this->getForm();
	}

	protected function getList() {
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/so_theme/module/so_lookbook_slider', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('extension/so_theme/module/so_lookbook_slider|add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/so_theme/module/so_lookbook_slider|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['lookbook_slides'] = array();
		$filter_data = array(
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
		);

		$this->load->model('tool/image');

		$lookbook_slide_total = $this->model_extension_so_theme_module_so_lookbook->getTotalLookBookSlider($filter_data);

		$results = $this->model_extension_so_theme_module_so_lookbook->getLookBookSlider($filter_data);

		foreach ($results as $result) {
			$data['lookbook_slides'][] = array(
				'slide_id' => $result['slide_id'],
				'title' => $result['title'],
				'status' => $result['status'],
				'edit'	=> $this->url->link('extension/so_theme/module/so_lookbook_slider|edit', 'user_token=' . $this->session->data['user_token'] . '&slide_id=' . $result['slide_id'] . $url, true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->session->data['text_layout'])) {
			$data['text_layout'] = $this->session->data['text_layout'];

			unset($this->session->data['text_layout']);
		} else {
			$data['text_layout'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';
		
		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $lookbook_slide_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('extension/so_theme/module/so_lookbook_slider', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true)
		]);			

		$data['results'] = sprintf($this->language->get('text_pagination'), ($lookbook_slide_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($lookbook_slide_total - $this->config->get('config_pagination_admin'))) ? $lookbook_slide_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $lookbook_slide_total, ceil($lookbook_slide_total / $this->config->get('config_pagination_admin')));

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_lookbook/lookbook_slider_list', $data));
	}

	public function delete() {
		$this->load->language('extension/so_theme/module/so_lookbook_slider');
		$this->load->model('extension/so_theme/module/so_lookbook');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $slide_id) {
				$this->model_extension_so_theme_module_so_lookbook->deleteLookBookSlider($slide_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/so_theme/module/so_lookbook_slider', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['slide_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->session->data['text_layout'])) {
			$data['text_layout'] = $this->session->data['text_layout'];
			unset($this->session->data['text_layout']);
		} else {
			$data['text_layout'] = '';
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = '';
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/so_theme/module/so_lookbook_slider', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['slide_id'])) {
			$data['action'] = $this->url->link('extension/so_theme/module/so_lookbook_slider|add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/so_theme/module/so_lookbook_slider|edit', 'user_token=' . $this->session->data['user_token'] . '&slide_id=' . $this->request->get['slide_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/so_theme/module/so_lookbook_slider', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['slide_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$lookbook_slide_info = $this->model_extension_so_theme_module_so_lookbook->getLookBookSliderInfo($this->request->get['slide_id']);

		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['title'])) {
			$data['title'] = $this->request->post['title'];
		} elseif (!empty($lookbook_slide_info)) {
			$data['title'] = $lookbook_slide_info['title'];
		} else {
			$data['title'] = '';
		}

		if (isset($this->request->post['custom_class'])) {
			$data['custom_class'] = $this->request->post['custom_class'];
		} elseif (!empty($lookbook_slide_info)) {
			$data['custom_class'] = $lookbook_slide_info['custom_class'];
		} else {
			$data['custom_class'] = '';
		}

		if (isset($this->request->post['navigation'])) {
			$data['navigation'] = $this->request->post['navigation'];
		} elseif (!empty($lookbook_slide_info)) {
			$data['navigation'] = $lookbook_slide_info['navigation'];
		} else {
			$data['navigation'] = '0';
		}

		if (isset($this->request->post['pagination'])) {
			$data['pagination'] = $this->request->post['pagination'];
		} elseif (!empty($lookbook_slide_info)) {
			$data['pagination'] = $lookbook_slide_info['pagination'];
		} else {
			$data['pagination'] = '0';
		}

		if (isset($this->request->post['auto_play'])) {
			$data['auto_play'] = $this->request->post['auto_play'];
		} elseif (!empty($lookbook_slide_info)) {
			$data['auto_play'] = $lookbook_slide_info['auto_play'];
		} else {
			$data['auto_play'] = '0';
		}

		if (isset($this->request->post['auto_play_timeout'])) {
			$data['auto_play_timeout'] = $this->request->post['auto_play_timeout'];
		} elseif (!empty($lookbook_slide_info)) {
			$data['auto_play_timeout'] = $lookbook_slide_info['auto_play_timeout'];
		} else {
			$data['auto_play_timeout'] = '';
		}

		if (isset($this->request->post['stop_auto'])) {
			$data['stop_auto'] = $this->request->post['stop_auto'];
		} elseif (!empty($lookbook_slide_info)) {
			$data['stop_auto'] = $lookbook_slide_info['stop_auto'];
		} else {
			$data['stop_auto'] = '0';
		}

		if (isset($this->request->post['loop'])) {
			$data['loop'] = $this->request->post['loop'];
		} elseif (!empty($lookbook_slide_info)) {
			$data['loop'] = $lookbook_slide_info['loop'];
		} else {
			$data['loop'] = '0';
		}

		if (isset($this->request->post['next_image'])) {
			$data['next_image'] = $this->request->post['next_image'];
		} elseif (!empty($lookbook_slide_info)) {
			$data['next_image'] = $lookbook_slide_info['next_image'];
		} else {
			$data['next_image'] = '';
		}

		if (isset($this->request->post['prev_image'])) {
			$data['prev_image'] = $this->request->post['prev_image'];
		} elseif (!empty($lookbook_slide_info)) {
			$data['prev_image'] = $lookbook_slide_info['prev_image'];
		} else {
			$data['prev_image'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($lookbook_slide_info)) {
			$data['status'] = $lookbook_slide_info['status'];
		} else {
			$data['status'] = true;
		}

		$data['lookbooks'] = array();
		
		$this->load->model('tool/image');

		$lookbook_total = $this->model_extension_so_theme_module_so_lookbook->getTotalLookBook();

		$results = $this->model_extension_so_theme_module_so_lookbook->getLookBook();

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 300, 150);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 300, 150);
			}

			$data['lookbooks'][] = array(
				'lookbook_id' => $result['lookbook_id'],
				'name' => $result['name'],
				'image' => $image,
				'status' => $result['status']
			);
		}

		$data['selected'] = array();
		$data['position'] = array();
		if (isset($this->request->get['slide_id'])) {
			$lookbook_slide_items = $this->model_extension_so_theme_module_so_lookbook->getLookBookSlideItem($this->request->get['slide_id']);
			if (!empty($lookbook_slide_items)) {
				foreach ($lookbook_slide_items as $lsi) {
					$data['selected'][] = $lsi['lookbook_id'];
					$data['position'][$lsi['lookbook_id']] = $lsi['position'];
				}
			}
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_lookbook/lookbook_slider_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/so_theme/module/so_lookbook_slider')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((strlen($this->request->post['title']) < 3) || (strlen($this->request->post['title']) > 64)) {
			$this->error['title'] = $this->language->get('error_title');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/so_theme/module/so_lookbook_slider')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}