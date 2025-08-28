<?php
namespace Opencart\Admin\Controller\Extension\SoTheme\Module;
class SoMakeModelYear extends \Opencart\System\Engine\Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/so_theme/module/so_make_model_year');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/review','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/review');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_review->addReview($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_product'])) {
				$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

			$this->response->redirect($this->url->link('catalog/review', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('catalog/review','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/review');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_review->editReview($this->request->get['review_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_product'])) {
				$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

			$this->response->redirect($this->url->link('catalog/review', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('catalog/review','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/review');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $review_id) {
				$this->model_catalog_review->deleteReview($review_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_product'])) {
				$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_author'])) {
				$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

			$this->response->redirect($this->url->link('catalog/review', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/so_theme/module/so_make_model_year', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);

		$data['mmy'] = array();

		$results = array('make'=>'Make', 'model'=>'Model', 'engine'=>'Engine', 'year'=>'Year');

		foreach ($results as $key => $result) {
			$data['mmy'][] = array(
				'name'       => $result,
				'edit'       => $this->url->link('extension/so_theme/module/so_make_model_year|'.$key, 'user_token=' . $this->session->data['user_token'], true)
			);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_make_model_year/list', $data));
	}

	public function make() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/so_theme/module/so_make_model_year');

		$this->getMakeList();
	}

	public function model() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/so_theme/module/so_make_model_year');

		$this->getModelList();
	}

	public function engine() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/so_theme/module/so_make_model_year');

		$this->getEngineList();
	}

	public function year() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/so_theme/module/so_make_model_year');

		$this->getYearList();
	}

	public function add_make() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/so_theme/module/so_make_model_year');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateMakeForm()) {
			$make_id = $this->model_extension_so_theme_module_so_make_model_year->addMake($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->post['save_stay']) && $this->request->post['save_stay'] == 1) {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year|edit_make', 'user_token=' . $this->session->data['user_token'] . '&make_id='.$make_id, true));
			}
			else {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year|make', 'user_token=' . $this->session->data['user_token'], true));
			}
		}

		$this->getMakeForm();
	}

	public function add_model() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/so_theme/module/so_make_model_year');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateModelForm()) {
			$model_id = $this->model_extension_so_theme_module_so_make_model_year->addModel($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->post['save_stay']) && $this->request->post['save_stay'] == 1) {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year|edit_model', 'user_token=' . $this->session->data['user_token'] . '&model_id='.$model_id, true));
			}
			else {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year|model', 'user_token=' . $this->session->data['user_token'], true));
			}
		}

		$this->getModelForm();
	}

	public function add_engine() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/so_theme/module/so_make_model_year');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateEngineForm()) {
			$engine_id = $this->model_extension_so_theme_module_so_make_model_year->addEngine($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->post['save_stay']) && $this->request->post['save_stay'] == 1) {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year|edit_engine', 'user_token=' . $this->session->data['user_token'] . '&engine_id='.$engine_id, true));
			}
			else {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year|engine', 'user_token=' . $this->session->data['user_token'], true));
			}
		}

		$this->getEngineForm();
	}

	public function add_year() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/so_theme/module/so_make_model_year');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateYearForm()) {
			$year_id = $this->model_extension_so_theme_module_so_make_model_year->addYear($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->post['save_stay']) && $this->request->post['save_stay'] == 1) {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year|edit_year', 'user_token=' . $this->session->data['user_token'] . '&year_id='.$year_id, true));
			}
			else {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year|year', 'user_token=' . $this->session->data['user_token'], true));
			}
		}

		$this->getYearForm();
	}

	public function edit_make() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/so_theme/module/so_make_model_year');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateMakeForm()) {
			$this->model_extension_so_theme_module_so_make_model_year->editMake($this->request->get['make_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->post['save_stay']) && $this->request->post['save_stay'] == 1) {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year|edit_make', 'user_token=' . $this->session->data['user_token'] . '&make_id='.$this->request->get['make_id'], true));
			}
			else {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year|make', 'user_token=' . $this->session->data['user_token'], true));
			}
		}

		$this->getMakeForm();
	}

	public function edit_model() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/so_theme/module/so_make_model_year');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateModelForm()) {
			$this->model_extension_so_theme_module_so_make_model_year->editModel($this->request->get['model_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->post['save_stay']) && $this->request->post['save_stay'] == 1) {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year|edit_model', 'user_token=' . $this->session->data['user_token'] . '&model_id='.$this->request->get['model_id'], true));
			}
			else {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year|model', 'user_token=' . $this->session->data['user_token'], true));
			}
		}

		$this->getModelForm();
	}

	public function edit_engine() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/so_theme/module/so_make_model_year');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateEngineForm()) {
			$this->model_extension_so_theme_module_so_make_model_year->editEngine($this->request->get['engine_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->post['save_stay']) && $this->request->post['save_stay'] == 1) {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year|edit_engine', 'user_token=' . $this->session->data['user_token'] . '&engine_id='.$this->request->get['engine_id'], true));
			}
			else {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year|engine', 'user_token=' . $this->session->data['user_token'], true));
			}
		}

		$this->getEngineForm();
	}

	public function edit_year() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/so_theme/module/so_make_model_year');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateYearForm()) {
			$this->model_extension_so_theme_module_so_make_model_year->editYear($this->request->get['year_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			if (isset($this->request->post['save_stay']) && $this->request->post['save_stay'] == 1) {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year|edit_year', 'user_token=' . $this->session->data['user_token'] . '&year_id='.$this->request->get['year_id'], true));
			}
			else {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year|year', 'user_token=' . $this->session->data['user_token'], true));
			}
		}

		$this->getYearForm();
	}

	public function delete_make() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/so_theme/module/so_make_model_year');

		if (isset($this->request->post['selected']) && $this->validateDeleteMake()) {
			foreach ($this->request->post['selected'] as $make_id) {
				$this->model_extension_so_theme_module_so_make_model_year->deleteMake($make_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year|make', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getMakeList();
	}

	public function delete_model() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/so_theme/module/so_make_model_year');

		if (isset($this->request->post['selected']) && $this->validateDeleteModel()) {
			foreach ($this->request->post['selected'] as $model_id) {
				$this->model_extension_so_theme_module_so_make_model_year->deleteModel($model_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year/model', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getModelList();
	}

	public function delete_engine() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/so_theme/module/so_make_model_year');

		if (isset($this->request->post['selected']) && $this->validateDeleteEngine()) {
			foreach ($this->request->post['selected'] as $engine_id) {
				$this->model_extension_so_theme_module_so_make_model_year->deleteEngine($engine_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year/engine', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getEngineList();
	}

	public function delete_year() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/so_theme/module/so_make_model_year');

		if (isset($this->request->post['selected']) && $this->validateDeleteYear()) {
			foreach ($this->request->post['selected'] as $year_id) {
				$this->model_extension_so_theme_module_so_make_model_year->deleteYear($year_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/so_theme/module/so_make_model_year/year', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getYearList();
	}

	protected function getMakeList() {
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/so_theme/module/so_make_model_year', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('extension/so_theme/module/so_make_model_year|add_make', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('extension/so_theme/module/so_make_model_year|delete_make', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('extension/so_theme/module/so_make_model_year', 'user_token=' . $this->session->data['user_token'], true);

		$data['makes'] = array();

		$results = $this->model_extension_so_theme_module_so_make_model_year->getMakes();
		foreach ($results as $result) {
			$data['makes'][] = array(
				'make_id'	=> $result['make_id'],
				'make_name'	=> $result['make_name'],
				'edit'		=> $this->url->link('extension/so_theme/module/so_make_model_year|edit_make', 'user_token=' . $this->session->data['user_token']. '&make_id=' . $result['make_id'], true)
			);
		}

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

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_make_model_year/make_list', $data));
	}

	protected function getModelList() {
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/so_theme/module/so_make_model_year', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('extension/so_theme/module/so_make_model_year|add_model', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('extension/so_theme/module/so_make_model_year|delete_model', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('extension/so_theme/module/so_make_model_year', 'user_token=' . $this->session->data['user_token'], true);

		$data['models'] = array();

		$results = $this->model_extension_so_theme_module_so_make_model_year->getModels();
		foreach ($results as $result) {
			$make_info = $this->model_extension_so_theme_module_so_make_model_year->getMake($result['make_id']);
			$data['models'][] = array(
				'model_id'		=> $result['model_id'],
				'model_name' 	=> $result['model_name'],
				'make_name' 	=> $make_info['make_name'],
				'edit'			=> $this->url->link('extension/so_theme/module/so_make_model_year|edit_model', 'user_token=' . $this->session->data['user_token']. '&model_id=' . $result['model_id'], true)
			);
		}

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

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_make_model_year/model_list', $data));
	}

	protected function getEngineList() {
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/so_theme/module/so_make_model_year', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('extension/so_theme/module/so_make_model_year|add_engine', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('extension/so_theme/module/so_make_model_year|delete_engine', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('extension/so_theme/module/so_make_model_year', 'user_token=' . $this->session->data['user_token'], true);

		$data['engines'] = array();

		$results = $this->model_extension_so_theme_module_so_make_model_year->getEngines();
		foreach ($results as $result) {
			$model_info = $this->model_extension_so_theme_module_so_make_model_year->getModel($result['model_id']);
			$make_info = $this->model_extension_so_theme_module_so_make_model_year->getMake($result['make_id']);
			$data['engines'][] = array(
				'engine_id'		=> $result['engine_id'],
				'engine_name' 	=> $result['engine_name'],
				'model_name' 	=> $model_info['model_name'],
				'make_name' 	=> $make_info['make_name'],
				'edit'			=> $this->url->link('extension/so_theme/module/so_make_model_year|edit_engine', 'user_token=' . $this->session->data['user_token']. '&engine_id=' . $result['engine_id'], true)
			);
		}

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

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_make_model_year/engine_list', $data));
	}

	protected function getYearList() {
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/so_theme/module/so_make_model_year', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('extension/so_theme/module/so_make_model_year|add_year', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('extension/so_theme/module/so_make_model_year|delete_year', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('extension/so_theme/module/so_make_model_year', 'user_token=' . $this->session->data['user_token'], true);

		$data['years'] = array();

		$results = $this->model_extension_so_theme_module_so_make_model_year->getYears();
		foreach ($results as $result) {
			$engine_info = $this->model_extension_so_theme_module_so_make_model_year->getEngine($result['engine_id']);
			$model_info = $this->model_extension_so_theme_module_so_make_model_year->getModel($result['model_id']);
			$make_info = $this->model_extension_so_theme_module_so_make_model_year->getMake($result['make_id']);
			$data['years'][] = array(
				'year_id'		=> $result['year_id'],
				'year_name' 	=> $result['year_name'],
				'engine_name' 	=> $engine_info['engine_name'],
				'model_name' 	=> $model_info['model_name'],
				'make_name' 	=> $make_info['make_name'],
				'edit'			=> $this->url->link('extension/so_theme/module/so_make_model_year|edit_year', 'user_token=' . $this->session->data['user_token']. '&year_id=' . $result['year_id'], true)
			);
		}

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

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_make_model_year/year_list', $data));
	}

	protected function getMakeForm() {
		$data['text_form'] = !isset($this->request->get['make_id']) ? $this->language->get('text_add_make') : $this->language->get('text_edit_make');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/so_theme/module/so_make_model_year', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['make_id'])) {
			$data['action'] = $this->url->link('extension/so_theme/module/so_make_model_year|add_make', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/so_theme/module/so_make_model_year|edit_make', 'user_token=' . $this->session->data['user_token'] . '&make_id=' . $this->request->get['make_id'], true);
		}

		$data['cancel'] = $this->url->link('extension/so_theme/module/so_make_model_year|make', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->request->get['make_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$make_info = $this->model_extension_so_theme_module_so_make_model_year->getMake($this->request->get['make_id']);
		}

		if (isset($this->request->post['make_name'])) {
			$data['make_name'] = $this->request->post['make_name'];
		} elseif (!empty($make_info)) {
			$data['make_name'] = $make_info['make_name'];
		} else {
			$data['make_name'] = '';
		}

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_make_model_year/make_form', $data));
	}

	protected function getModelForm() {
		$data['text_form'] = !isset($this->request->get['model_id']) ? $this->language->get('text_add_model') : $this->language->get('text_edit_model');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['make'])) {
			$data['error_make'] = $this->error['make'];
		} else {
			$data['error_make'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/so_theme/module/so_make_model_year', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['model_id'])) {
			$data['action'] = $this->url->link('extension/so_theme/module/so_make_model_year|add_model', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/so_theme/module/so_make_model_year|edit_model', 'user_token=' . $this->session->data['user_token'] . '&model_id=' . $this->request->get['model_id'], true);
		}

		$data['cancel'] = $this->url->link('extension/so_theme/module/so_make_model_year|model', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->request->get['model_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$model_info = $this->model_extension_so_theme_module_so_make_model_year->getModel($this->request->get['model_id']);
		}

		if (isset($this->request->post['model_name'])) {
			$data['model_name'] = $this->request->post['model_name'];
		} elseif (!empty($model_info)) {
			$data['model_name'] = $model_info['model_name'];
		} else {
			$data['model_name'] = '';
		}

		if (isset($this->request->post['make_id'])) {
			$data['make_id'] = $this->request->post['make_id'];
		} elseif (!empty($model_info)) {
			$data['make_id'] = $model_info['make_id'];
		} else {
			$data['make_id'] = '';
		}

		$data['makes'] = $this->model_extension_so_theme_module_so_make_model_year->getMakes();

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_make_model_year/model_form', $data));
	}

	protected function getEngineForm() {
		$data['text_form'] = !isset($this->request->get['engine_id']) ? $this->language->get('text_add_engine') : $this->language->get('text_edit_engine');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['model'])) {
			$data['error_model'] = $this->error['model'];
		} else {
			$data['error_model'] = '';
		}

		if (isset($this->error['make'])) {
			$data['error_make'] = $this->error['make'];
		} else {
			$data['error_make'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/so_theme/module/so_make_model_year', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['engine_id'])) {
			$data['action'] = $this->url->link('extension/so_theme/module/so_make_model_year|add_engine', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/so_theme/module/so_make_model_year|edit_engine', 'user_token=' . $this->session->data['user_token'] . '&engine_id=' . $this->request->get['engine_id'], true);
		}

		$data['cancel'] = $this->url->link('extension/so_theme/module/so_make_model_year|engine', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->request->get['engine_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$engine_info = $this->model_extension_so_theme_module_so_make_model_year->getEngine($this->request->get['engine_id']);
		}

		if (isset($this->request->post['engine_name'])) {
			$data['engine_name'] = $this->request->post['engine_name'];
		} elseif (!empty($engine_info)) {
			$data['engine_name'] = $engine_info['engine_name'];
		} else {
			$data['engine_name'] = '';
		}

		if (isset($this->request->post['model_id'])) {
			$data['model_id'] = $this->request->post['model_id'];
		} elseif (!empty($engine_info)) {
			$data['model_id'] = $engine_info['model_id'];
		} else {
			$data['model_id'] = '';
		}

		if (isset($this->request->post['make_id'])) {
			$data['make_id'] = $this->request->post['make_id'];
		} elseif (!empty($engine_info)) {
			$data['make_id'] = $engine_info['make_id'];
		} else {
			$data['make_id'] = '';
		}

		$data['make_info'] = '';
		if ($data['model_id'] != '') {
			$model_info = $this->model_extension_so_theme_module_so_make_model_year->getModel($data['model_id']);
			if (!empty($model_info)) {
				$data['make_info'] = $this->model_extension_so_theme_module_so_make_model_year->getMake($model_info['make_id']);
			}
		}

		$data['models'] = $this->model_extension_so_theme_module_so_make_model_year->getModels();
		$data['makes'] = $this->model_extension_so_theme_module_so_make_model_year->getMakes();

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_make_model_year/engine_form', $data));
	}

	protected function getYearForm() {
		$data['text_form'] = !isset($this->request->get['year_id']) ? $this->language->get('text_add_year') : $this->language->get('text_edit_year');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['engine'])) {
			$data['error_engine'] = $this->error['engine'];
		} else {
			$data['error_engine'] = '';
		}

		if (isset($this->error['model'])) {
			$data['error_model'] = $this->error['model'];
		} else {
			$data['error_model'] = '';
		}

		if (isset($this->error['make'])) {
			$data['error_make'] = $this->error['make'];
		} else {
			$data['error_make'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/so_theme/module/so_make_model_year', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['year_id'])) {
			$data['action'] = $this->url->link('extension/so_theme/module/so_make_model_year|add_year', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/so_theme/module/so_make_model_year|edit_year', 'user_token=' . $this->session->data['user_token'] . '&year_id=' . $this->request->get['year_id'], true);
		}

		$data['cancel'] = $this->url->link('extension/so_theme/module/so_make_model_year|year', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->request->get['year_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$year_info = $this->model_extension_so_theme_module_so_make_model_year->getYear($this->request->get['year_id']);
		}

		if (isset($this->request->post['year_name'])) {
			$data['year_name'] = $this->request->post['year_name'];
		} elseif (!empty($year_info)) {
			$data['year_name'] = $year_info['year_name'];
		} else {
			$data['year_name'] = '';
		}

		if (isset($this->request->post['engine_id'])) {
			$data['engine_id'] = $this->request->post['engine_id'];
		} elseif (!empty($year_info)) {
			$data['engine_id'] = $year_info['engine_id'];
		} else {
			$data['engine_id'] = '';
		}

		if (isset($this->request->post['model_id'])) {
			$data['model_id'] = $this->request->post['model_id'];
		} elseif (!empty($year_info)) {
			$data['model_id'] = $year_info['model_id'];
		} else {
			$data['model_id'] = '';
		}

		$data['model_info'] = '';
		if ($data['model_id'] != '') {
			$data['model_info'] = $this->model_extension_so_theme_module_so_make_model_year->getModel($data['model_id']);
		}

		if (isset($this->request->post['make_id'])) {
			$data['make_id'] = $this->request->post['make_id'];
		} elseif (!empty($year_info)) {
			$data['make_id'] = $year_info['make_id'];
		} else {
			$data['make_id'] = '';
		}

		$data['make_info'] = '';
		if ($data['make_id'] != '') {
			$data['make_info'] = $this->model_extension_so_theme_module_so_make_model_year->getMake($data['make_id']);
		}

		$data['engines'] = $this->model_extension_so_theme_module_so_make_model_year->getEngines();

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_make_model_year/year_form', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['review_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['product'])) {
			$data['error_product'] = $this->error['product'];
		} else {
			$data['error_product'] = '';
		}

		if (isset($this->error['author'])) {
			$data['error_author'] = $this->error['author'];
		} else {
			$data['error_author'] = '';
		}

		if (isset($this->error['text'])) {
			$data['error_text'] = $this->error['text'];
		} else {
			$data['error_text'] = '';
		}

		if (isset($this->error['rating'])) {
			$data['error_rating'] = $this->error['rating'];
		} else {
			$data['error_rating'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_product'])) {
			$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_author'])) {
			$url .= '&filter_author=' . urlencode(html_entity_decode($this->request->get['filter_author'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
			'href' => $this->url->link('catalog/review', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['review_id'])) {
			$data['action'] = $this->url->link('catalog/review/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/review/edit', 'user_token=' . $this->session->data['user_token'] . '&review_id=' . $this->request->get['review_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/review', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['review_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$review_info = $this->model_catalog_review->getReview($this->request->get['review_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];
		
		$this->load->model('catalog/product');

		if (isset($this->request->post['product_id'])) {
			$data['product_id'] = $this->request->post['product_id'];
		} elseif (!empty($review_info)) {
			$data['product_id'] = $review_info['product_id'];
		} else {
			$data['product_id'] = '';
		}

		if (isset($this->request->post['product'])) {
			$data['product'] = $this->request->post['product'];
		} elseif (!empty($review_info)) {
			$data['product'] = $review_info['product'];
		} else {
			$data['product'] = '';
		}

		if (isset($this->request->post['author'])) {
			$data['author'] = $this->request->post['author'];
		} elseif (!empty($review_info)) {
			$data['author'] = $review_info['author'];
		} else {
			$data['author'] = '';
		}

		if (isset($this->request->post['text'])) {
			$data['text'] = $this->request->post['text'];
		} elseif (!empty($review_info)) {
			$data['text'] = $review_info['text'];
		} else {
			$data['text'] = '';
		}

		if (isset($this->request->post['rating'])) {
			$data['rating'] = $this->request->post['rating'];
		} elseif (!empty($review_info)) {
			$data['rating'] = $review_info['rating'];
		} else {
			$data['rating'] = '';
		}

		if (isset($this->request->post['date_added'])) {
			$data['date_added'] = $this->request->post['date_added'];
		} elseif (!empty($review_info)) {
			$data['date_added'] = ($review_info['date_added'] != '0000-00-00 00:00' ? $review_info['date_added'] : '');
		} else {
			$data['date_added'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($review_info)) {
			$data['status'] = $review_info['status'];
		} else {
			$data['status'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/review_form', $data));
	}

	protected function validateMakeForm() {
		if (!$this->user->hasPermission('modify', 'extension/so_theme/module/so_make_model_year')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['make_name']) {
			$this->error['name'] = $this->language->get('error_make_name');
		}

		return !$this->error;
	}

	protected function validateModelForm() {
		if (!$this->user->hasPermission('modify', 'extension/so_theme/module/so_make_model_year')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['model_name']) {
			$this->error['name'] = $this->language->get('error_model_name');
		}

		if (!$this->request->post['make_id']) {
			$this->error['make'] = $this->language->get('error_make');
		}

		return !$this->error;
	}

	protected function validateEngineForm() {
		if (!$this->user->hasPermission('modify', 'extension/so_theme/module/so_make_model_year')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['engine_name']) {
			$this->error['name'] = $this->language->get('error_engine_name');
		}

		if (!$this->request->post['model_id']) {
			$this->error['model'] = $this->language->get('error_model');
		}

		if (!$this->request->post['make_id']) {
			$this->error['make'] = $this->language->get('error_make');
		}

		return !$this->error;
	}

	protected function validateYearForm() {
		if (!$this->user->hasPermission('modify', 'extension/so_theme/module/so_make_model_year')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['year_name']) {
			$this->error['name'] = $this->language->get('error_year_name');
		}

		if (!$this->request->post['engine_id']) {
			$this->error['engine'] = $this->language->get('error_engine');
		}

		if (!$this->request->post['model_id']) {
			$this->error['model'] = $this->language->get('error_model');
		}

		if (!$this->request->post['make_id']) {
			$this->error['make'] = $this->language->get('error_make');
		}

		return !$this->error;
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/review')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['product_id']) {
			$this->error['product'] = $this->language->get('error_product');
		}

		if ((utf8_strlen($this->request->post['author']) < 3) || (utf8_strlen($this->request->post['author']) > 64)) {
			$this->error['author'] = $this->language->get('error_author');
		}

		if (utf8_strlen($this->request->post['text']) < 1) {
			$this->error['text'] = $this->language->get('error_text');
		}

		if (!isset($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
			$this->error['rating'] = $this->language->get('error_rating');
		}

		return !$this->error;
	}

	protected function validateDeleteMake() {
		if (!$this->user->hasPermission('modify', 'extension/so_theme/module/so_make_model_year')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDeleteModel() {
		if (!$this->user->hasPermission('modify', 'extension/so_theme/module/so_make_model_year')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDeleteEngine() {
		if (!$this->user->hasPermission('modify', 'extension/so_theme/module/so_make_model_year')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDeleteYear() {
		if (!$this->user->hasPermission('modify', 'extension/so_theme/module/so_make_model_year')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/review')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function get_make() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$html = '';
		$html .= '<option value="">'.$this->language->get('entry_select_make').'</option>';
		if (isset($this->request->post['model_id'])) {
			$this->load->model('extension/so_theme/module/so_make_model_year');
			$model_info = $this->model_extension_so_theme_module_so_make_model_year->getModel($this->request->post['model_id']);
			if (!empty($model_info)) {
				$make_info = $this->model_extension_so_theme_module_so_make_model_year->getMake($model_info['make_id']);
				$html .= '<option value="'.$make_info['make_id'].'">'.$make_info['make_name'].'</option>';
			}
		}

		echo $html;
		die();
	}

	public function get_model() {
		$this->load->language('extension/so_theme/module/so_make_model_year','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));

		$html = '';
		$html .= '<option value="">'.$this->language->get('entry_select_model').'</option>';
		if (isset($this->request->post['engine_id'])) {
			$this->load->model('extension/so_theme/module/so_make_model_year');
			$engine_info = $this->model_extension_so_theme_module_so_make_model_year->getEngine($this->request->post['engine_id']);
			if (!empty($engine_info)) {
				$model_info = $this->model_extension_so_theme_module_so_make_model_year->getModel($engine_info['model_id']);
				$html .= '<option value="'.$model_info['model_id'].'">'.$model_info['model_name'].'</option>';
			}
		}

		echo $html;
		die();
	}
}