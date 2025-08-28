<?php
namespace Opencart\Admin\Controller\Tool;

class ProductImport extends \Opencart\System\Engine\Controller {
    public function index(): void {
        $this->load->language('tool/product_import');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('tool/product_import');

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->files['import']) && is_uploaded_file($this->request->files['import']['tmp_name'])) {
            $file = $this->request->files['import']['tmp_name'];
            $this->session->data['success'] = $this->model_tool_product_import->importCSV($file);
            $this->response->redirect($this->url->link('tool/product_import', 'user_token=' . $this->session->data['user_token']));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['entry_file'] = $this->language->get('entry_file');
        $data['button_import'] = $this->language->get('button_import');
        $data['action'] = $this->url->link('tool/product_import', 'user_token=' . $this->session->data['user_token']);
        $data['user_token'] = $this->session->data['user_token'];
        $data['success'] = $this->session->data['success'] ?? '';
        unset($this->session->data['success']);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('tool/product_import', $data));
    }
}
