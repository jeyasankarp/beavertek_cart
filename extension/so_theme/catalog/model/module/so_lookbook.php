<?php
namespace Opencart\Catalog\Model\Extension\SoTheme\Module;
class SoLookBook extends \Opencart\System\Engine\Model {	
	protected array $statement = [];
	public function __construct(\Opencart\System\Engine\Registry $registry) {
		$this->registry = $registry;

		// Storing some sub queries so that we are not typing them out multiple times.
		$this->statement['discount'] = "(SELECT (CASE WHEN `pd2`.`type` = 'P' THEN (`pd2`.`price` * (`p`.`price` / 100)) WHEN `pd2`.`type` = 'S' THEN (`p`.`price` - `pd2`.`price`) ELSE `pd2`.`price` END) FROM `" . DB_PREFIX . "product_discount` `pd2` WHERE `pd2`.`product_id` = `p`.`product_id` AND `pd2`.`customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "' AND `pd2`.`quantity` = '1' AND `pd2`.`special` = '0' AND ((`pd2`.`date_start` = '0000-00-00' OR `pd2`.`date_start` < NOW()) AND (`pd2`.`date_end` = '0000-00-00' OR `pd2`.`date_end` > NOW())) ORDER BY `pd2`.`priority` ASC, `pd2`.`price` ASC LIMIT 1) AS `discount`";
		$this->statement['special'] = "(SELECT (CASE WHEN `ps`.`type` = 'P' THEN (`ps`.`price` * (`p`.`price` / 100)) WHEN `ps`.`type` = 'S' THEN (`p`.`price` - `ps`.`price`) ELSE `ps`.`price` END) FROM `" . DB_PREFIX . "product_discount` `ps` WHERE `ps`.`product_id` = `p`.`product_id` AND `ps`.`customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "' AND `ps`.`quantity` = '1' AND `ps`.`special` = '1' AND ((`ps`.`date_start` = '0000-00-00' OR `ps`.`date_start` < NOW()) AND (`ps`.`date_end` = '0000-00-00' OR `ps`.`date_end` > NOW())) ORDER BY `ps`.`priority` ASC, `ps`.`price` ASC LIMIT 1) AS `special`";
		$this->statement['reward'] = "(SELECT `pr`.`points` FROM `" . DB_PREFIX . "product_reward` `pr` WHERE `pr`.`product_id` = `p`.`product_id` AND `pr`.`customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "') AS `reward`";
		$this->statement['review'] = "(SELECT COUNT(*) FROM `" . DB_PREFIX . "review` `r` WHERE `r`.`product_id` = `p`.`product_id` AND `r`.`status` = '1' GROUP BY `r`.`product_id`) AS `reviews`";
	}

	public function getSliderById($slide_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "so_lookbook_slide sls WHERE sls.slide_id = " . (int)$slide_id);

		return $query->row;
	}

	public function getLookBookBySlideId($slide_id) {
		$sql = "SELECT sl.* FROM " . DB_PREFIX . "so_lookbook sl 
				LEFT JOIN " .DB_PREFIX . "so_lookbook_slide_items slsi ON slsi.lookbook_id = sl.lookbook_id 
				LEFT JOIN " .DB_PREFIX . "so_lookbook_slide sls ON sls.slide_id = slsi.slide_id 
				WHERE sl.status = '1' AND sls.slide_id = '".(int)$slide_id."' ORDER BY slsi.position ASC";
				
		$query = $this->db->query($sql);

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$base_url = $this->config->get('config_ssl');
		} else {
			$base_url = $this->config->get('config_url');
		}

		$lookbook_data = array();
		foreach ($query->rows as $result) {
			$lookbook_data[] = array(
				'lookbook_id'	    => $result['lookbook_id'],
				'lookbook_name'  	=> $result['name'],
				'image'			    => $base_url.'image/'.$result['image'],
				'pinHtml'		    => $this->getPinHtml($result['lookbook_id']),
				'pinHtmlMobile'		=> $this->pinHtmlMobile($result['lookbook_id'])
			);
		}
		return $lookbook_data;
	}

	public function getLookBook($lookbook_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "so_lookbook WHERE lookbook_id = " . (int)$lookbook_id);

		return $query->row;
	}
	
	public function pinHtmlMobile($lookbook_id) {
		$this->load->language('extension/so_theme/module/so_lookbook_slider');
		$lookbook = $this->getLookBook($lookbook_id);
		$pins = $lookbook['pins'];
		$arrPin = json_decode($pins, true);
		$html = '';
		$width = $this->config->get('module_lookbook_pin_width');
		$height = $this->config->get('module_lookbook_pin_height');
		$background = $this->config->get('module_lookbook_pin_background');
		$color = $this->config->get('module_lookbook_pin_text');
		$productImageWidth = $this->config->get('module_lookbook_popup_image_width');
		$productImageHeight = $this->config->get('module_lookbook_popup_image_height');
		$radius = round($width/2);

		if(count($arrPin)>0) {
			$this->load->model('tool/image');

			foreach($arrPin as $pin) {
				$imgWidth = $pin['imgW'];
				$imgHeight = $pin['imgH'];
				$top = $pin['top'];
				$left = $pin['left'];
				$leftPercent = ($left * 100)/$imgWidth;
				$topPercent = ($top * 100)/$imgHeight;

				$html .= '<div class="pin__type_mobile row" >';

				$html .= '<div class="col-xs-4 text-center"><div class="pin-label_mobile">'. $pin['label'] .'</div></div>';

				if(trim($pin['custom_text'])!='') {
					if(trim($pin['custom_label'])!=''){
						$pinTitle = $pin['custom_label']; 
					}elseif($product = $this->getProductInfo(false, $pin['text'])){
						$pinTitle = $product['name'];
					}
					$html .= '<div class="pin__title_mobile col-xs-7">'.$pinTitle.'</div>';
				}
				else {
					if ($product = $this->getProductInfo(false, $pin['text'])) {
						$href = $this->url->link('product/product', '&product_id='.$product['product_id'], true);
						// Product Name - Tooltip
						$html .= '<div class="col-xs-7 popup__content_mobile" >';
						$html .= '<div class="popup__content popup__content--product ">';
						// Product Image
						if ($product['image']) {
							$productImageUrl = $this->model_tool_image->resize($product['image'], $productImageWidth, $productImageHeight);
						}
						else {
							$productImageUrl = $this->model_tool_image->resize('placeholder.png', $productImageWidth, $productImageHeight);
						}
						$html .= '<a href="'.$href.'"><img src="'.$productImageUrl.'" alt="" /></a>';

						// Product Name
						$html .= '<h4><a href="'.$href.'">'.$product['name'].'</a></h4>';

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

						if ($this->config->get('config_tax')) {
							$tax = $this->currency->format((float)$product['special'] ? $product['special'] : $product['price'], $this->session->data['currency']);
						} else {
							$tax = false;
						}

						if ($price) {
							$html .= '<p class="price">';
							if (!$special) {
								$html .= $price;
							}
							else {
								$html .= '<span class="price-new">'.$special.'</span> <span class="price-old">'.$price.'</span>';
							}
							if ($tax) {
								$html .= '<span class="price-tax">'.$this->language->get('text_tax').' '.$tax.'</span>';
							}
							$html .= '</p>';
						}

						// Links
						$html .= '<div><a href="'.$href.'">'.$this->language->get('text_detail').'</a>';

						// Add Cart
						$html .= '<button type="button" class="action tocart primary" onclick="cart.add(\''.$product['product_id'].'\', \''.$product['minimum'].'\');"><i class="fa fa-shopping-cart"></i> <span class="hidden-xs hidden-sm hidden-md">'.$this->language->get('button_cart').'</span></button>';

						$html .= '</div></div></div>';
					}
				}

				$html .= '</div>';
			}
		}
		
		return $html;
	}	

	public function getPinHtml($lookbook_id) {
		$this->load->language('extension/so_theme/module/so_lookbook_slider');
		$lookbook = $this->getLookBook($lookbook_id);
		
		$pins = $lookbook['pins'];
		$arrPin = json_decode($pins, true);
		$html = '';
		$width = $this->config->get('module_lookbook_pin_width');
		$height = $this->config->get('module_lookbook_pin_height');
		$background = $this->config->get('module_lookbook_pin_background');
		$color = $this->config->get('module_lookbook_pin_text');
		$productImageWidth = $this->config->get('module_lookbook_popup_image_width');
		$productImageHeight = $this->config->get('module_lookbook_popup_image_height');
		$radius = round($width/2);

		if(is_array($arrPin) && count($arrPin)>0) {
			$this->load->model('tool/image');

			foreach($arrPin as $pin) {
				$imgWidth = $pin['imgW'];
				$imgHeight = $pin['imgH'];
				$top = $pin['top'];
				$left = $pin['left'];
				$leftPercent = ($left * 100)/$imgWidth;
				$topPercent = ($top * 100)/$imgHeight;

				$html .= '<div class="pin__type pin__type--area" style="width:'. $pin['width'] .'px; height:'. $pin['height'] .'px; background:#'. $background .'; color:#'. $color .'; -webkit-border-radius:'. $radius .'px; -moz-border-radius:'. $radius .'px; border-radius:'. $radius .'px; line-height:'. $height .'px; left:'. $leftPercent .'%; top:'. $topPercent .'%">';

				$html .= '<span class="pin-label">'. $pin['label'] .'</span>';

				if(trim($pin['custom_text'])!='') {
					if(trim($pin['custom_label'])!=''){
						$pinTitle = $pin['custom_label']; 
					}elseif($product = $this->getProductInfo(false, $pin['text'])){
						$pinTitle = $product['name'];
					}
					$html .= '<div class="pin__title">'.$pinTitle.'</div>';
					$html .= '<div class="pin__popup pin__popup--'.$pin['position'].' pin__popup--fade pin__popup_text_content" style="width:'.($productImageWidth + 30).'px"><div class="popup__title">'.$pinTitle.'</div><div class="popup__content">'.$pin['custom_text'].'</div></div>';
				}
				else {
					if ($product = $this->getProductInfo(false, $pin['text'])) {
						$href = $this->url->link('product/product', '&product_id='.$product['product_id'], true);
						// Product Name - Tooltip
						$html .= '<div class="pin__title">'.$product['name'].'</div>';
						$html .= '<div class="pin__popup pin__popup--'.$pin['position'].' pin__popup--fade" style="width:'. (int)($productImageWidth + 30) .'px">';
						$html .= '<form method="post" data-oc-toggle="ajax" data-oc-load="'.$this->url->link('common/cart.info', 'language=' . $this->config->get('config_language')).'" data-oc-target="#header-cart">';
						$html .= '<div class="popup__content popup__content--product">';
						// Product Image
						if ($product['image']) {
							$productImageUrl = $this->model_tool_image->resize($product['image'], $productImageWidth, $productImageHeight);
						}
						else {
							$productImageUrl = $this->model_tool_image->resize('placeholder.png', $productImageWidth, $productImageHeight);
						}
						$html .= '<a href="'.$href.'"><img src="'.$productImageUrl.'" alt="" /></a>';

						// Product Name
						$html .= '<h4><a href="'.$href.'">'.$product['name'].'</a></h4>';

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

						if ($this->config->get('config_tax')) {
							$tax = $this->currency->format((float)$product['special'] ? $product['special'] : $product['price'], $this->session->data['currency']);
						} else {
							$tax = false;
						}

						if ($price) {
							$html .= '<p class="price">';
							if (!$special) {
								$html .= $price;
							}
							else {
								$html .= '<span class="price-new">'.$special.'</span> <span class="price-old">'.$price.'</span>';
							}
							if ($tax) {
								$html .= '<span class="price-tax">'.$this->language->get('text_tax').' '.$tax.'</span>';
							}
							$html .= '</p>';
						}

						// Links
						
						$html .= '<div><a href="'.$href.'">'.$this->language->get('text_detail').'</a>';

						// Add Cart
						$html .= '<button type="submit" data-bs-toggle="tooltip" formaction="'.$this->url->link('checkout/cart.add', 'language=' . $this->config->get('config_language')).'" class="action tocart primary"><i class="fa fa-shopping-cart"></i> <span class="hidden-xs hidden-sm hidden-md">'.$this->language->get('button_cart').'</span></button>';
						$html .= '<input type="hidden" name="product_id" value="'.$product['product_id'].'">';
						$html .= '<input type="hidden" name="quantity" value="'.$product['minimum'].'">';
						$html .= '</div></div></form></div>';
						
					}
				}

				$html .= '</div>';
			}
		}


		return $html;
	}

	public function getProductInfo($product_id, $model) {
		if ($product_id != false) {
			$sql = "SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_discount ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
		}
		else {
			$sql = "SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_discount ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.model = '" . $model . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
		}
		
		$query = $this->db->query($sql);

		if ($query->num_rows) {
			return array(
				'product_id'       => $query->row['product_id'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'meta_title'       => $query->row['meta_title'],
				'meta_description' => $query->row['meta_description'],
				'meta_keyword'     => $query->row['meta_keyword'],
				'tag'              => $query->row['tag'],
				'model'            => $query->row['model'],
				'lookbook_id'      => $query->row['lookbook_id'],
				'mpn'              => $query->row['mpn'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'stock_status'     => $query->row['stock_status'],
				'image'            => $query->row['image'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
				'special'          => $query->row['special'],
				'reward'           => $query->row['reward'],
				'points'           => $query->row['points'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'date_available'   => $query->row['date_available'],
				'weight'           => $query->row['weight'],
				'weight_class_id'  => $query->row['weight_class_id'],
				'length'           => $query->row['length'],
				'width'            => $query->row['width'],
				'height'           => $query->row['height'],
				'length_class_id'  => $query->row['length_class_id'],
				'subtract'         => $query->row['subtract'],
				'rating'           => $query->row['rating'],
				'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
				'minimum'          => $query->row['minimum'],
				'sort_order'       => $query->row['sort_order'],
				'status'           => $query->row['status'],
				'date_added'       => $query->row['date_added'],
				'date_modified'    => $query->row['date_modified'],
			);
		} else {
			return false;
		}
	}
}