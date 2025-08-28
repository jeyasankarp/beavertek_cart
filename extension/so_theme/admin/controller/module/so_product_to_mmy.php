<?php
namespace Opencart\Admin\Controller\Extension\SoTheme\Module;
class SoProductToMMY extends \Opencart\System\Engine\Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/product','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));
		$this->load->language('extension/so_theme/module/so_product_to_mmy','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		$this->getList();
	}

	public function edit() {
		$this->load->language('extension/so_theme/module/so_product_to_mmy');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');
		$this->load->model('extension/so_theme/module/so_make_model_year');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_extension_so_theme_module_so_make_model_year->editProductToMmy($this->request->get['product_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->post['save_stay']) && $this->request->post['save_stay'] == 1) {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_product_to_mmy|edit', 'user_token=' . $this->session->data['user_token'] .'&product_id='.$this->request->get['product_id'] . $url, true));
			}
			else {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_product_to_mmy', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
		}

		$this->getForm();
	}

	protected function getList() {
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);

		$data['products'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
		);

		$this->load->model('tool/image');

		$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

		$results = $this->model_catalog_product->getProducts($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'model'      => $result['model'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       => $this->url->link('extension/so_theme/module/so_product_to_mmy|edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true)
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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
		$data['sort_status'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $product_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('extension/so_theme/module/so_product_to_mmy', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);		
		

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_model'] = $filter_model;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_product_to_mmy/product_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/so_theme/module/so_product_to_mmy', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['product_id'])) {
			$data['action'] = $this->url->link('extension/so_theme/module/so_product_to_mmy|add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/so_theme/module/so_product_to_mmy|edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $this->request->get['product_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/so_theme/module/so_product_to_mmy', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['product_description'])) {
			$data['product_description'] = $this->request->post['product_description'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_description'] = $this->model_catalog_product->getDescriptions($this->request->get['product_id']);
		} else {
			$data['product_description'] = array();
		}

		if (isset($this->request->post['product_to_mmy'])) {
			$product_to_mmy = $this->request->post['product_to_mmy'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_to_mmy = $this->model_extension_so_theme_module_so_make_model_year->getProductToMmy($this->request->get['product_id']);
		} else {
			$product_to_mmy = array();
		}

		$data['product_to_mmy'] = array();

		foreach ($product_to_mmy as $item) {
			$data['product_to_mmy'][] = array(
				'product_id' 	 => $item['product_id'],
				'make_id'        => $item['make_id'],
				'model_id'       => $item['model_id'],
				'engine_id'      => $item['engine_id'],
				'year_id'      	 => $item['year_id']
			);
		}

		$data['makes'] = $this->model_extension_so_theme_module_so_make_model_year->getMakes();
		$data['models'] = $this->model_extension_so_theme_module_so_make_model_year->getModels();
		$data['engines'] = $this->model_extension_so_theme_module_so_make_model_year->getEngines();
		$data['years'] = $this->model_extension_so_theme_module_so_make_model_year->getYears();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_product_to_mmy/product_form', $data));
	}

	public function getModel() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$html = '';
		$html .= '<option value="">'.$this->language->get('entry_select_model').'</option>';
		if (isset($this->request->post['make_id']) && !empty($this->request->post['make_id'])) {
			$this->load->model('extension/so_theme/module/so_make_model_year');
			$models = $this->model_extension_so_theme_module_so_make_model_year->getModels(array('make_id'=>$this->request->post['make_id']));
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
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$html = '';
		$html .= '<option value="">'.$this->language->get('entry_select_engine').'</option>';
		if (isset($this->request->post['model_id']) && !empty($this->request->post['model_id'])) {
			$this->load->model('extension/so_theme/module/so_make_model_year');
			$engines = $this->model_extension_so_theme_module_so_make_model_year->getEngines(array('model_id'=>$this->request->post['model_id']));
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
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$html = '';
		$html .= '<option value="">'.$this->language->get('entry_select_year').'</option>';
		if (isset($this->request->post['engine_id']) && !empty($this->request->post['engine_id'])) {
			$this->load->model('extension/so_theme/module/so_make_model_year');
			$years = $this->model_extension_so_theme_module_so_make_model_year->getYears(array('engine_id'=>$this->request->post['engine_id']));
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