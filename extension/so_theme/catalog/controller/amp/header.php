<?php
class ControllerExtensionAmpHeader extends Controller {
	public function index() {
		// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');

		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
		
		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');
		
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');
		
		/*=======Show Seo Amp =======*/
		$this->load->model('catalog/product');
		$this->load->language('common/header');
		
		//Get theme_directory
		$data['theme_directory'] = 'default';
		
		$data['name']             = $this->config->get('config_name');
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['home'] = $this->url->link('common/home');
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['text_home'] = $this->language->get('text_home');
		
		$data['base']      = $server;
		$data['favicon']   = $server . 'image/' . $this->config->get('config_icon');
		$data['categories'] = array();
		$categories = $this->model_catalog_category->getCategories(0);
		foreach ($categories as $category) {
			$children_data = array();
			$children = $this->model_catalog_category->getCategories($category['category_id']);
			foreach ($children as $child) {
				$filter_data = array(
					'filter_category_id' => $child['category_id'],
					'filter_sub_category' => true
				);
				
				$children_data[] = array(
					'name' => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
					'href' => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
				);
			}
			
			// Level 1
			$data['categories'][] = array(
				'name' => $category['name'],
				'children' => $children_data,
				'column' => $category['column'] ? $category['column'] : 1,
				'href' => $this->url->link('product/category', 'path=' . $category['category_id'])
			);
		   
		}
		/*=======Show admin Config =======*/
		$data['status'] = $this->config->get('so_google_amp_status');
		$data['logoWidth'] = $this->config->get('so_google_amp_logowidth');
		$data['logoHeight'] = $this->config->get('so_google_amp_logoheight');
		$data['mainWidth'] = $this->config->get('so_google_amp_thumbwidth');
		$data['mainHeight'] = $this->config->get('so_google_amp_thumbheight');
		$data['relatedProduct'] = $this->config->get('so_google_amp_relatedproduct');
		$data['linkColor'] = $this->config->get('so_google_amp_linkcolor');
		$data['bgheader'] = $this->config->get('so_google_amp_headerbg');
		$data['bgButton'] = $this->config->get('so_google_amp_buttonbg');
		$data['google_amp_id'] = $this->config->get('so_google_amp_analytics');
		
		/*=======Show product=======*/
		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
			$product_info = $this->model_catalog_product->getProduct($product_id);
			$data['product_info'] = $this->model_catalog_product->getProduct($product_id);
			
			if ($product_info) {
				
				$data['meta_title']       = $product_info['meta_title'];
				$data['meta_description'] = $product_info['meta_description'];
				$data['meta_keyword']     = $product_info['meta_keyword'];
				$data['heading_title']    = $product_info['name'];
				$data['canonical'] = $this->url->link('product/product', 'product_id=' . $this->request->get['product_id']);
				$data['current']   = $this->url->link('product/amp/product', 'product_id=' . $this->request->get['product_id']);
				if ($product_info['image']) {
					$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
				} else {
					$data['thumb'] = '';
				}
				
			}
		} else {
			$product_id = 0;
		}
		
		/*=======Show Category=======*/
		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = (int)$path_id;
				} else {
					$path .= '_' . (int)$path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

			}
			$category_info = $this->model_catalog_category->getCategory($category_id);
			$data['meta_title']       = $category_info['meta_title'];
			$data['meta_description'] = $category_info['meta_description'];
			$data['meta_keyword']     = $category_info['meta_keyword'];
			$data['heading_title']    = $category_info['name'];
			$data['canonical'] = $this->url->link('product/category', 'product_id=' . $category_id);
			$data['current']   = $this->url->link('product/amp/category', 'product_id=' . $category_id);

			if ($category_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height'));
			} else {
				$data['thumb'] = '';
			}

			$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
		} else {
			$category_id = 0;
		}
		
		return $this->load->view('amp/header', $data);
	}
}
