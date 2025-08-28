<?php
namespace Opencart\Catalog\Controller\Extension\SoTheme\Soconfig;
class Cart extends \Opencart\System\Engine\Controller {
	public function index() {
		$this->load->language('common/cart','',$this->config->get('config_language_catalog'));
		$this->load->language('extension/so_theme/soconfig/cart','',$this->config->get('config_language_catalog'));

		// Totals
		$this->load->model('setting/extension');

		$totals = array();
		$taxes = $this->cart->getTaxes();
		$total = 0;

		// Because __call can not keep var references so we put them into an array.


		$this->load->model('tool/image');
		$this->load->model('tool/upload');
		$this->load->model('catalog/product');
		
		$data['product'] = array();
		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('checkout/cart');

		if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
			($this->model_checkout_cart->getTotals)($totals, $taxes, $total);
		}

		$data['text_subtotal'] = sprintf($this->language->get('text_subtotal'), $this->currency->format($total, $this->session->data['currency']), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0));		
	

		foreach ($this->cart->getProducts() as $product) {
			if ($product['product_id'] == $product_id) {
				
				if ($product['image']) {
					$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
				} else {
					$image = '';
				}

				// Display prices
				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$unit_price = $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'));
					
					$price = $this->currency->format($unit_price, $this->session->data['currency']);
					$total = $this->currency->format($unit_price * $product['quantity'], $this->session->data['currency']);
				} else {
					$price = false;
					$total = false;
				}
				
				$data['product']= array(
					'thumb'     => $image,
					'name'      => $product['name'],
					'model'     => $product['model'],
					'quantity'  => $product['quantity'],
					'price'     => $price,
					'total'     => $total,
					'href'        => $this->url->link('product/product', 'language=' . $this->config->get('config_language') . '&product_id=' . $product['product_id'])
				);
				
			}
		}

		// Gift Voucher
		$data['vouchers'] = array();

		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$data['vouchers'][] = array(
					'key'         => $key,
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $this->session->data['currency'])
				);
			}
		}

		$data['totals'] = array();

		foreach ($totals as $total) {
			$data['totals'][] = array(
				'title' => $total['title'],
				'text'  => $this->currency->format($total['value'], $this->session->data['currency']),
			);
		}
		
		$data['cart'] = $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language'));
		$data['checkout'] = $this->url->link('checkout/checkout', 'language=' . $this->config->get('config_language'));

		return $this->load->view('extension/so_theme/soconfig/quickcart', $data);
	}
	
	public function info() {
		$this->response->setOutput($this->index());
	}
	
	
	public function add(): void {
		$this->load->language('checkout/cart','',$this->config->get('config_language_catalog'));

		$json = [];

		if (isset($this->request->post['product_id'])) {
			$product_id = (int)$this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['quantity'])) {
			$quantity = (int)$this->request->post['quantity'];
		} else {
			$quantity = 1;
		}

		if (isset($this->request->post['option'])) {
			$option = array_filter($this->request->post['option']);
		} else {
			$option = [];
		}

		if (isset($this->request->post['subscription_plan_id'])) {
			$subscription_plan_id = (int)$this->request->post['subscription_plan_id'];
		} else {
			$subscription_plan_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			// If variant get master product
			if ($product_info['master_id']) {
				$product_id = $product_info['master_id'];
			}

			// Merge variant code with options
			foreach ($product_info['variant'] as $key => $value) {
				$option[$key] = $value;
			}

			// Validate options
			$product_options = $this->model_catalog_product->getOptions($product_id);

			foreach ($product_options as $product_option) {
				if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
					$json['error']['option_' . $product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
				}
			}

			// Validate subscription products
			$subscriptions = $this->model_catalog_product->getSubscriptions($product_id);

			if ($subscriptions) {
				$subscription_plan_ids = [];

				foreach ($subscriptions as $subscription) {
					$subscription_plan_ids[] = $subscription['subscription_plan_id'];
				}

				if (!in_array($subscription_plan_id, $subscription_plan_ids)) {
					$json['error']['subscription'] = $this->language->get('error_subscription');
				}
			}
		} else {
			$json['error']['warning'] = $this->language->get('error_product');
		}

		if (!$json) {
			$this->cart->add($product_id, $quantity, $option, $subscription_plan_id);

			$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'language=' . $this->config->get('config_language') . '&product_id=' . $product_id), $product_info['name'], $this->url->link('checkout/cart', 'language=' . $this->config->get('config_language')));

			// Unset all shipping and payment methods
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_methods']);
		} else {
			$json['redirect'] = $this->url->link('product/product', 'language=' . $this->config->get('config_language') . '&product_id=' . $this->request->post['product_id'], true);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function edit() {
		$this->load->language('checkout/cart','',$this->config->get('config_language_catalog'));

		$json = array();

		// Update
		if (!empty($this->request->post['quantity'])) {
			foreach ($this->request->post['quantity'] as $key => $value) {
				$this->cart->update($key, $value);
			}

			$this->session->data['success'] = $this->language->get('text_remove');

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['reward']);

			$this->response->redirect($this->url->link('checkout/cart'));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function remove() {
		$this->load->language('checkout/cart','',$this->config->get('config_language_catalog'));

		$json = array();

		// Remove
		if (isset($this->request->post['key'])) {
			$this->cart->remove($this->request->post['key']);

			unset($this->session->data['vouchers'][$this->request->post['key']]);

			$json['success'] = $this->language->get('text_remove');

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['reward']);

			// Totals
			$this->load->model('setting/extension');

			$totals = array();
			$taxes = $this->cart->getTaxes();
			$total = 0;

			// Because __call can not keep var references so we put them into an array. 			
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);

			$json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total, $this->session->data['currency']));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
