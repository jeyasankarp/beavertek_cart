<?php
namespace Opencart\Admin\Model\Extension\SoTheme\Module;
class SoLookbook extends \Opencart\System\Engine\Model {	
	function getTotalLookBook ($data = array()) {
		$sql = "SELECT COUNT(DISTINCT sl.lookbook_id) AS total FROM " . DB_PREFIX . "so_lookbook sl";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	function getLookBook ($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "so_lookbook sl";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	function deleteLookBook($lookbook_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "so_lookbook WHERE lookbook_id = '" . (int)$lookbook_id . "'");
	}

	function getLookBookInfo($lookbook_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "so_lookbook sl WHERE sl.lookbook_id = " . (int)$lookbook_id);

		return $query->row;
	}

	function getTotalLookBookSlider($data = array()) {
		$sql = "SELECT COUNT(DISTINCT sls.slide_id) AS total FROM " . DB_PREFIX . "so_lookbook_slide sls";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	function getLookBookSlider($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "so_lookbook_slide sls";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	function getLookBookSliderInfo($slide_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "so_lookbook_slide sls WHERE sls.slide_id = " . (int)$slide_id);

		return $query->row;
	}

	function addLookBook($data = array()) {
		$sql = "INSERT INTO " . DB_PREFIX . "so_lookbook 
				SET name = '" . $this->db->escape($data['name']) . "',
					image = '" . $this->db->escape($data['image']) . "',
					pins = '" . $data['pins'] . "',
					status = '" . (int)$data['status'] . "'
				";

		$this->db->query($sql);

		$lookbook_id = $this->db->getLastId();

		return $lookbook_id;
	}

	function editLookBook($lookbook_id, $data) {
		$sql = "UPDATE " . DB_PREFIX . "so_lookbook 
				SET name = '" . $this->db->escape($data['name']) . "',
					image = '" . $this->db->escape($data['image']) . "',
					pins = '" . $data['pins'] . "',
					status = '" . (int)$data['status'] . "'
				WHERE lookbook_id = '".(int)$lookbook_id."'
				";

		$this->db->query($sql);
	}

	function addLookBookSlider($data = array()) {
		$sql = "INSERT INTO " . DB_PREFIX . "so_lookbook_slide 
				SET `title` = '" . $this->db->escape($data['title']) . "',
					`custom_class` = '" . $this->db->escape($data['custom_class']) . "',
					`auto_play` = '" . $data['auto_play'] . "',
					`auto_play_timeout` = '" . $data['auto_play_timeout'] . "',
					`stop_auto` = '" . $data['stop_auto'] . "',
					`navigation` = '" . $data['navigation'] . "',
					`pagination` = '" . $data['pagination'] . "',
					`loop` = '" . $data['loop'] . "',
					`next_image` = '" . $data['next_image'] . "',
					`prev_image` = '" . $data['prev_image'] . "',
					`status` = '" . (int)$data['status'] . "'
				";

		$this->db->query($sql);

		$slide_id = $this->db->getLastId();

		if (isset($data['lookbook']) && is_array($data['lookbook'])) {
			foreach ($data['lookbook'] as $key => $lookbook_item) {
				$sql = "INSERT INTO " . DB_PREFIX . "so_lookbook_slide_items 
					SET `slide_id` = '" . $slide_id . "',
						`lookbook_id` = '" . (int)$lookbook_item['lookbook_id'] . "',
						`position` = '" . (int)$lookbook_item['position'] . "'
					";

				$this->db->query($sql);
			}
		}

		$module_data = array(
			'name'	=> $this->db->escape($data['title']),
			'slide_id'	=> $slide_id,
			'status'	=> $data['status']
		);
		$this->db->query("INSERT INTO `" . DB_PREFIX . "module` SET `name` = '" . $this->db->escape($module_data['name']) . "', `code` = 'so_theme.so_lookbook_slider', `setting` = '" . $this->db->escape(json_encode($module_data)) . "'");
		
		return $slide_id;
	}

	public function editLookBookSlider($slide_id, $data) {
		$sql = "UPDATE " . DB_PREFIX . "so_lookbook_slide 
				SET `title` = '" . $this->db->escape($data['title']) . "',
					`custom_class` = '" . $this->db->escape($data['custom_class']) . "',
					`auto_play` = '" . $data['auto_play'] . "',
					`auto_play_timeout` = '" . $data['auto_play_timeout'] . "',
					`stop_auto` = '" . $data['stop_auto'] . "',
					`navigation` = '" . $data['navigation'] . "',
					`pagination` = '" . $data['pagination'] . "',
					`loop` = '" . $data['loop'] . "',
					`next_image` = '" . $data['next_image'] . "',
					`prev_image` = '" . $data['prev_image'] . "',
					`status` = '" . $data['status'] . "'
				WHERE `slide_id` = '" . (int)$slide_id . "'
				";

		$this->db->query($sql);

		if (isset($data['lookbook']) && is_array($data['lookbook'])) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "so_lookbook_slide_items WHERE `slide_id` = '" . (int)$slide_id . "'");
			foreach ($data['lookbook'] as $key => $lookbook_item) {
				$sql = "INSERT INTO " . DB_PREFIX . "so_lookbook_slide_items 
					SET `slide_id` = '" . $slide_id . "',
						`lookbook_id` = '" . (int)$lookbook_item['lookbook_id'] . "',
						`position` = '" . (int)$lookbook_item['position'] . "'
					";

				$this->db->query($sql);
			}
		}

		$module_data = array(
			'name'	=> $this->db->escape($data['title']),
			'slide_id'	=> (int)$slide_id,
			'status'	=> $data['status']
		);
		$this->db->query("UPDATE `" . DB_PREFIX . "module` SET `name` = '" . $this->db->escape($module_data['name']) . "', `setting` = '" . $this->db->escape(json_encode($module_data)) . "' WHERE `setting` REGEXP '\"slide_id\":$slide_id'");
	}

	function deleteLookBookSlider($slide_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "so_lookbook_slide_items WHERE `slide_id` = '" . (int)$slide_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "module WHERE `setting` REGEXP '\"slide_id\":$slide_id'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "so_lookbook_slide WHERE slide_id = '" . (int)$slide_id . "'");
	}

	public function getProductBySku($term) {
		$sql = "SELECT p.product_id, p.model FROM " . DB_PREFIX . "product p WHERE p.model LIKE '" . $term . "%' AND p.status = 1";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getProductBySku2($model) {
		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.model = '" . $model . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() LIMIT 1");

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
				'mpn'              => $query->row['mpn'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'stock_status'     => $query->row['stock_status'],
				'image'            => $query->row['image'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
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

	public function getLookBookSlideItem($slide_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "so_lookbook_slide_items WHERE `slide_id` = '" . (int)$slide_id . "'";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getSlider() {

	}

	function install() {
		$product_lookbook_id = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "product LIKE 'lookbook_id'");
        if (!$product_lookbook_id->num_rows) {
            $this->db->query("ALTER TABLE " . DB_PREFIX . "product ADD lookbook_id INT COLLATE utf8_bin NOT NULL AFTER `date_modified`");
        }

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "so_lookbook` (
				`lookbook_id` INT(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(255) NOT NULL,
				`image` varchar(255) NOT NULL,
				`pins` mediumtext NOT NULL,
				`status` smallint(6) NOT NULL,
				PRIMARY KEY (`lookbook_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "so_lookbook_slide` (
				`slide_id` INT(11) NOT NULL AUTO_INCREMENT,
				`title` varchar(255) NOT NULL,
				`custom_class` varchar(255) NOT NULL,
				`auto_play` smallint(6) NOT NULL,
				`auto_play_timeout` varchar(255) NOT NULL,
				`stop_auto` smallint(6) NOT NULL,
				`navigation` smallint(6) NOT NULL,
				`pagination` smallint(6) NOT NULL,
				`loop` smallint(6) NOT NULL,
				`next_image` varchar(255) NOT NULL,
				`prev_image` varchar(255) NOT NULL,
				`status` smallint(6) NOT NULL,
				PRIMARY KEY (`slide_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");

        $this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "so_lookbook_slide_items` (
				`item_id` INT(11) NOT NULL AUTO_INCREMENT,
				`slide_id` int(11) NOT NULL,
				`lookbook_id` int(11) NOT NULL,
				`position` int(11) NOT NULL,
				PRIMARY KEY (`item_id`, `slide_id`, `lookbook_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
		");
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "so_lookbook_slide_items`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "so_lookbook_slide`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "so_lookbook`");
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` DROP IF EXISTS lookbook_id");
	}
}