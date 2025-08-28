<?php
namespace Opencart\Admin\Controller\Extension\SoTheme\Module;
require_once (DIR_EXTENSION.'so_theme/system/library/so_lookbook/Uploadedfilexhr.php');
require_once (DIR_EXTENSION.'so_theme/system/library/so_lookbook/Uploadedfileform.php');

class SoLookbook extends \Opencart\System\Engine\Controller {
	protected $uploader;
	protected $uploadXhr;
	protected $uploadfileForm;

	private $error = array();

	private $allowedExtensions = array();
	private $sizeLimit = 10485760;
	private $min_image_width = 0;
    private $min_image_height = 0;
    private $max_image_width = 0;
    private $max_image_height = 0;
	private $filemodel;

	public $base_url;

	public function __construct($registry) {
		parent::__construct($registry);

		$uploadXhr = new Uploadedfilexhr();
		$uploadfileForm = new Uploadedfileform();
		$this->uploadXhr = $uploadXhr;
		$this->uploadfileForm = $uploadfileForm;

		$sizeLimit = $this->config->get('module_lookbook_max_upload_filesize');
		$this->min_image_width = $this->config->get('module_lookbook_min_image_width');
		$this->min_image_height = $this->config->get('module_lookbook_min_image_height');
		$this->max_image_width = $this->config->get('module_lookbook_max_image_width');
		$this->max_image_height = $this->config->get('module_lookbook_max_image_height');
		$allowed_extensions = explode(',', $this->config->get('module_lookbook_allowed_extensions'));
                   
        $this->allowedExtensions = array_map("strtolower", $allowed_extensions);
        if ($sizeLimit>0) $this->sizeLimit = $sizeLimit;      

		if (isset($_GET['qqfile'])) {
            $this->filemodel = $this->uploadXhr;
        } elseif (isset($_FILES['qqfile'])) {
            $this->filemodel = $this->uploadfileForm;
        } else {
            $this->filemodel = false; 
        }

        $this->base_url = HTTP_CATALOG;
	}
	
	public function index() {
		$this->load->language('extension/so_theme/module/so_lookbook');
		$this->load->model('setting/module');
		$this->load->model('extension/so_theme/module/so_lookbook');
		
		$this->document->setTitle($this->language->get('heading_title'));

		$this->getList();
	}

	public function add() {
		$this->load->language('extension/so_theme/module/so_lookbook');
		$this->load->model('extension/so_theme/module/so_lookbook');
		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->request->post['pins'] = htmlspecialchars_decode($this->request->post['pins'], ENT_COMPAT);
			$lookbook_id = $this->model_extension_so_theme_module_so_lookbook->addLookBook($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if(isset($this->request->post['save_stay']) and $this->request->post['save_stay']=1) {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_lookbook|edit', 'user_token=' . $this->session->data['user_token'] . '&lookbook_id='.$lookbook_id, true));
			}
			else {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_lookbook', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/so_theme/module/so_lookbook');
		$this->load->model('extension/so_theme/module/so_lookbook');
		$this->document->setTitle($this->language->get('heading_title'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->request->post['pins'] = htmlspecialchars_decode($this->request->post['pins'], ENT_COMPAT);
			$this->model_extension_so_theme_module_so_lookbook->editLookBook($this->request->get['lookbook_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if(isset($this->request->post['save_stay']) and $this->request->post['save_stay']=1) {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_lookbook|edit', 'user_token=' . $this->session->data['user_token'] . '&lookbook_id='.$this->request->get['lookbook_id'], true));
			}
			else {
				$this->response->redirect($this->url->link('extension/so_theme/module/so_lookbook', 'user_token=' . $this->session->data['user_token'] . $url, true));
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
			'href' => $this->url->link('extension/so_theme/module/so_lookbook', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('extension/so_theme/module/so_lookbook|add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/so_theme/module/so_lookbook|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['lookbooks'] = array();
		$filter_data = array(
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
		);

		$this->load->model('tool/image');

		$lookbook_total = $this->model_extension_so_theme_module_so_lookbook->getTotalLookBook($filter_data);

		$results = $this->model_extension_so_theme_module_so_lookbook->getLookBook($filter_data);

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
				'status' => $result['status'],
				'edit'	=> $this->url->link('extension/so_theme/module/so_lookbook|edit', 'user_token=' . $this->session->data['user_token'] . '&lookbook_id=' . $result['lookbook_id'] . $url, true)
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
		
		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $lookbook_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('extension/so_theme/module/so_lookbook', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true)
		]);			

		$data['results'] = sprintf($this->language->get('text_pagination'), ($lookbook_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($lookbook_total - $this->config->get('config_pagination_admin'))) ? $lookbook_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $lookbook_total, ceil($lookbook_total / $this->config->get('config_pagination_admin')));

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_lookbook/lookbook_list', $data));
	}

	public function delete() {
		$this->load->language('extension/so_theme/module/so_lookbook');
		$this->load->model('extension/so_theme/module/so_lookbook');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $lookbook_id) {
				$this->model_extension_so_theme_module_so_lookbook->deleteLookBook($lookbook_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/so_theme/module/so_lookbook', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getForm() {
		$this->document->addScript('../extension/so_theme/admin/view/javascript/so_lookbook/js/fileuploader.js');
		$this->document->addScript('../extension/so_theme/admin/view/javascript/so_lookbook/js/jquery-ui.js');
		$this->document->addScript('../extension/so_theme/admin/view/javascript/so_lookbook/js/jquery.annotate.js');
		$this->document->addScript('../extension/so_theme/admin/view/javascript/so_lookbook/js/json2.min.js');		
        $this->document->addStyle('../extension/so_theme/admin/view/javascript/so_lookbook/css/lookbook.css');
        $this->document->addStyle('../extension/so_theme/admin/view/javascript/so_lookbook/css/styles.css');

        $this->load->model('setting/setting');

        $data['text_form'] = !isset($this->request->get['lookbook_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['image'])) {
			$data['error_image'] = $this->error['image'];
		} else {
			$data['error_image'] = '';
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
			'href' => $this->url->link('extension/so_theme/module/so_lookbook', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['lookbook_id'])) {
			$data['action'] = $this->url->link('extension/so_theme/module/so_lookbook|add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/so_theme/module/so_lookbook|edit', 'user_token=' . $this->session->data['user_token'] . '&lookbook_id=' . $this->request->get['lookbook_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('extension/so_theme/module/so_lookbook', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['action_upload'] = str_replace('&amp;', '&', $this->url->link('extension/so_theme/module/so_lookbook|upload', 'user_token=' . $this->session->data['user_token'], true));

		$data['check_product_url'] = str_replace('&amp;', '&', $this->url->link('extension/so_theme/module/so_lookbook|checkproduct', 'user_token=' . $this->session->data['user_token'], true));
		$data['load_product_url'] = str_replace('&amp;', '&', $this->url->link('extension/so_theme/module/so_lookbook|loadproduct', 'user_token=' . $this->session->data['user_token'], true));

		if (isset($this->request->get['lookbook_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$lookbook_info = $this->model_extension_so_theme_module_so_lookbook->getLookBookInfo($this->request->get['lookbook_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($lookbook_info)) {
			$data['name'] = $lookbook_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
			$data['fullImage'] = '';
		} elseif (!empty($lookbook_info) && is_file(DIR_IMAGE.$lookbook_info['image'])) {
			$data['image'] = $lookbook_info['image'];
			$data['fullImage'] = $this->base_url.'image/'.$lookbook_info['image'];
		} else {
			$data['image'] = '';
			$data['fullImage'] = '';
		}

		if (isset($this->request->post['pins'])) {
			$data['pins'] = $this->request->post['pins'];
		} elseif (!empty($lookbook_info) && $lookbook_info['pins'] != 'null') {
			$data['pins'] = htmlspecialchars_decode($lookbook_info['pins'], ENT_COMPAT);
			// $data['pins'] = $lookbook_info['pins'];
		} else {
			$data['pins'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($lookbook_info)) {
			$data['status'] = $lookbook_info['status'];
		} else {
			$data['status'] = true;
		}

		$data['pin_default'] = $this->config->get('module_lookbook_pin_default');
		$data['pin_width'] = $this->config->get('module_lookbook_pin_width');
		$data['pin_height'] = $this->config->get('module_lookbook_pin_height');
		$data['allowed_extensions_config'] = $this->config->get('module_lookbook_allowed_extensions');
		$data['allowed_extensions'] = implode('","',explode(',', $this->config->get('module_lookbook_allowed_extensions')));
		$data['sizeLimit'] = $this->config->get('module_lookbook_max_upload_filesize');
		$data['background'] = $this->config->get('module_lookbook_pin_background');
		$data['color'] = $this->config->get('module_lookbook_pin_text');

		$data['radius'] = !empty($data['pin_width'])?round($data['pin_width']/2):'';

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/so_theme/module/so_lookbook/lookbook_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/so_theme/module/so_lookbook')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/so_theme/module/so_lookbook')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function upload() {
		$upload_dir = DIR_IMAGE.'catalog/so_lookbook/';

		$config_check = $this->checkServerSettings();

		if($config_check === true){
		   $result = $this->handleUpload($upload_dir); 
		} 
		else
		{
			$result = $config_check;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($result));
	}

	function parse_size($size) {
		$unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
		$size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
		if ($unit) {
		// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
			return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
		}
		else {
			return round($size);
		}
	}

	public function checkServerSettings(){    
        $postSize = $this->parse_size($this->toBytes(ini_get('post_max_size')));
        $uploadSize = $this->parse_size($this->toBytes(ini_get('upload_max_filesize')));        
        
        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';             
            return array('error' => "increase post_max_size and upload_max_filesize to $size");    
        }

        if ($this->max_image_width < $this->min_image_width || $this->max_image_height < $this->min_image_height){            
            return array('error' => 'File was not uploaded. Minimal image width (height) can\'t be greater then maximal. Please, check settings.');    
        }

        return true;
    }

	private function toBytes($str){
        $val = trim($str);
        $val = (float)($val);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;        
        }
        return $val;
    }

	function handleUpload($uploadDirectory, $replaceOldFile = FALSE) {
		if (!is_dir($uploadDirectory)) mkdir($uploadDirectory, 0777, true);

		if (!$this->filemodel){
            return array('error' => 'No files were uploaded.');
        }

        $size = $this->filemodel->getSize();
        if ($size == 0) {
            return array('error' => 'File is empty');
        }

        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }

        $pathinfo = pathinfo($this->filemodel->getName());
        $filename = $pathinfo['filename'];
        //$filename = md5(uniqid());
        $filename = uniqid();
        $ext = $pathinfo['extension'];

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }

        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }

        if ($this->filemodel->save($uploadDirectory . $filename . '.' . $ext)) {
        	$imgPathFull = $uploadDirectory . $filename . '.' . $ext;
        	$dimensions = $this->getImageDimensions($imgPathFull);

        	if ($this->min_image_width!=0 && $this->min_image_height!=0) {
				if ($dimensions['width'] < $this->min_image_width || $dimensions['height'] < $this->min_image_height) {
				   @unlink($imgPathFull);
				   return array('error'=> 'Uploaded file dimensions are less than those specified in the configuration.');
				}
		 	}

		 	if ($this->max_image_width!=0 && $this->max_image_height!=0) {
		 		if ($dimensions['width'] > $this->max_image_width || $dimensions['height'] > $this->max_image_height) {
		 			@unlink($imgPathFull);
				   	return array('error'=> 'Uploaded file dimensions are greater than those specified in the configuration.');
			   	}
		 	}
        	return array('base_url'=>$this->base_url, 'success'=>true, 'filename'=>$filename . '.' . $ext, 'dimensions' => $dimensions);
        }
        else {
            return array('error'=> 'Could not save uploaded file.' . 'The upload was cancelled, or server error encountered');
        }
	}

	public function getImageDimensions($img_path){
		list($width, $height) = getimagesize($img_path);
		$result = array('width'=>$width, 'height'=>$height);
        return $result;
    }

    public function checkproduct() {
    	$product_id = 0;
    	$model = $this->request->post['text'];
    	$defaultPinText = '+';
    	$labelPost = $this->request->post['label'];
    	if($model!=''){
    		$this->load->model('extension/so_theme/module/so_lookbook');
    		$product = $this->model_extension_so_theme_module_so_lookbook->getProductBySku2($model);
    		$product_id = $product['product_id'];
    		$status = $product['status'];
    		$result['label'] = 0;

    		if ($this->config->get('module_lookbook_pin_price')){
    			// Product Prices
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$product['special']) {
					$special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($special) {
					$final_price = $special;
				}
				else {
					$final_price = $price;
				}

				$price = strip_tags($final_price);
				$price = str_replace('.00','', $price);
				$result['label'] = $price;

				if($labelPost != ''){
					if ($this->config->get('module_lookbook_pin_price') && ($labelPost != $price)){
						$result['label'] = $labelPost;
					}
				}
			}else{
				if($labelPost!=''){
					$result['label'] = $labelPost;
				}else{
					$result['label'] = $defaultPinText;
				}
			}
    	}

    	if ($product_id) {
			if ($status==1) {
			  $result['status'] = 1;
			} else {
			  $result['status'] = "is disabled";  
			}
		} else {
			$result['status'] = "doesn't exists"; 
			if ($labelPost!=''){
				$result['label'] = $labelPost;
			}else{
				$result['label'] = $defaultPinText;
			}
		}

    	$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($result));
    }

    public function loadproduct() {
    	$this->load->model('extension/so_theme/module/so_lookbook');

    	$q=$this->request->get['term'];

    	$responseData = array();
    	$results = $this->model_extension_so_theme_module_so_lookbook->getProductBySku($q);
    	foreach ($results as $product) {
    		$responseData[] = array(
    			'id'	=> $product['product_id'],
    			'label'	=> $product['model'],
    			'value'	=> $product['model']
    		);
    	}
    	
    	$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($responseData));
    }

	function install() {
		$this->setupEvent();
		$this->load->model('extension/so_theme/module/so_lookbook');
		$this->model_extension_so_theme_module_so_lookbook->install();
	}
	
    private function setupEvent() {
        $this->load->model('setting/event');

        $this->removeEvent();

		$dataAdmin = array(
		   'code' => 'so_lookbook',
		   'description' => 'so_lookbook',
		   'trigger' => 'admin/view/common/column_left/before',
		   'action' => 'extension/so_theme/event/so_lookbook.so_menu_before',
		   'status' => 1,
		   'sort_order' => 1,
		);

	    $this->model_setting_event->addEvent($dataAdmin);
    }		

	function uninstall() {
		$this->load->model('setting/event');
		$this->removeEvent();
		$this->load->model('extension/so_theme/module/so_lookbook');
		$this->model_extension_so_theme_module_so_lookbook->uninstall();
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('module_lookbook');
		$this->load->model('setting/module');
		$this->model_setting_module->deleteModulesByCode('so_lookbook_slider');
	}
	
    private function removeEvent() {
        $this->load->model('setting/event');
        $this->model_setting_event->deleteEventByCode('so_lookbook');
    }		
}