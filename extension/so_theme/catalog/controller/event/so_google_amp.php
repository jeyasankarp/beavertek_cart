<?php
namespace Opencart\Catalog\Controller\Extension\SoTheme\Event;
class SoGoogleAmp extends \Opencart\System\Engine\Controller {
	public function so_product_before(&$route, &$data){
		
		if($this->config->get('so_google_amp_status')){
			$this->document->addLink($this->url->link('extension/amp/product', 'product_id=' . $this->request->get['product_id']), 'amphtml');
			if (isset($this->request->get['addcart'])) {
				$this->cart->add($this->request->get['product_id']);
				$this->response->redirect($this->url->link('checkout/cart'));
			}
		}		
	}
}