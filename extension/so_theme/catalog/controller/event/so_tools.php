<?php
namespace Opencart\Catalog\Controller\Extension\SoTheme\Event;
class SoTools extends \Opencart\System\Engine\Controller {
	public function recentProducts(string &$route, array &$args, mixed &$output): void {	
			    $so_recent_products = array();
			    if (isset($this->request->cookie['sorecentproduct'])) {
				    $so_recent_products = explode(',', $this->request->cookie['sorecentproduct']);
			    } else if (isset($this->session->data['sorecentproduct'])) {
				    $so_recent_products = $this->session->data['sorecentproduct'];
			    }
			    
    			if (isset($this->request->get['route']) && $this->request->get['route'] == 'product/product') {
    				$product_id = $this->request->get['product_id'];   			
    				$so_recent_products = array_diff($so_recent_products, array($product_id));					
					
    				array_unshift($so_recent_products, $product_id);
    				setcookie('sorecentproduct', implode(',', $so_recent_products), time() + 60 * 60 * 24 * 30, '/', $this->request->server['HTTP_HOST']);
    				if (!isset($this->session->data['sorecentproduct']) || $this->session->data['sorecentproduct'] != $so_recent_products) {
    					$this->session->data['sorecentproduct'] = $so_recent_products;
    				}
						
    			}		
	}
}