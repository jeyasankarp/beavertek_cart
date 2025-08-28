<?php
namespace Opencart\Admin\Model\Extension\SoTheme\Module\Soconfig;
 class Setting extends \Opencart\System\Engine\Model {

	private static $INDEX_LIST = array(
		'product.model',
		'url_alias.query',
		'url_alias.keyword',
	);	 
	 
	public function createTableSoconfig(){
		$this->db->query('CREATE TABLE IF NOT EXISTS `' . DB_PREFIX . 'soconfig` (
          id int(11) auto_increment,
          `store_id` int(11) NOT NULL DEFAULT 0,
          `key` varchar(255) NOT NULL,
          `value` mediumtext NOT NULL,
          `serialized` tinyint(1) NOT NULL,
		   PRIMARY KEY(id)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8;');
	}
	
	public function getSetting($store_id = 0) {
		$setting_data = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "soconfig WHERE store_id = '" . (int)$store_id . "' AND `key` != 'mobile_general'");
		foreach ($query->rows as $result) {
				$setting_data[$result['key']] = json_decode($result['value'], true);
		}

		return $setting_data;
	}

	public function indexes($add_indexes = false) {
		$query = $this->db->query("
			SELECT * 
			FROM 
				INFORMATION_SCHEMA.TABLES 
			WHERE 
				TABLE_SCHEMA = '{$this->db->escape(DB_DATABASE)}'
				AND TABLE_TYPE = 'BASE TABLE'
				AND TABLE_NAME LIKE '{$this->db->escape(DB_PREFIX)}%'
		");

		$tables_indexes = array();

		foreach ($query->rows as $table) {
			$indexes = $this->getTableIndexes($table['TABLE_NAME']);
			$columns = $this->getTableColumns($table['TABLE_NAME']);

			foreach ($columns as $column) {
				if ($this->canIndex($table['TABLE_NAME'] . '.' . $column) && !in_array($column, $indexes)) {
					if ($add_indexes) {
						$this->addIndex($table['TABLE_NAME'], $column);
					}

					$tables_indexes[] = $table['TABLE_NAME'] . '.' . $column;
				}
			}

		}

		return $tables_indexes;
	}
	
	private function getTableIndexes($table_name) {
		$query = $this->db->query("
			SELECT * 
			FROM INFORMATION_SCHEMA.STATISTICS 
			WHERE TABLE_SCHEMA = '{$this->db->escape(DB_DATABASE)}'
			AND TABLE_NAME = '{$this->db->escape($table_name)}'
		");

		$indexes = array();

		foreach ($query->rows as $index) {
			$indexes[] = $index['COLUMN_NAME'];
		}

		return $indexes;
	}

	private function getTableColumns($table_name) {
		$query = $this->db->query("
			SELECT * 
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE 
				TABLE_SCHEMA = '{$this->db->escape(DB_DATABASE)}' 
				AND TABLE_NAME = '{$this->db->escape($table_name)}'
				AND LCASE(DATA_TYPE) NOT IN ('blob', 'text', 'longtext')
		");

		$columns = array();

		foreach ($query->rows as $column) {
			$columns[] = $column['COLUMN_NAME'];
		}

		return $columns;
	}

	private function canIndex($column) {
		if (substr($column, -3) === '_id') {
			return true;
		}

		if (in_array($column, static::$INDEX_LIST)) {
			return true;
		}

		return false;
	}

	private function addIndex($table, $column) {
		ob_start();

		$this->db->query("ALTER TABLE `{$this->db->escape($table)}` ADD INDEX (`{$this->db->escape($column)}`)");

		$buf = ob_get_contents();

		ob_clean();

		if (strpos($buf, 'Error: ALTER') !== false) {
			throw new Exception('Your MySQL user may not have ALTER privilege.');
		}
	}	

	public function editSetting( $data, $store_id = 0) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "soconfig` WHERE store_id = '" . (int)$store_id . "' AND `key` != 'mobile_general' ");
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "soconfig SET store_id = '" . (int)$store_id . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(json_encode($value, true)) . "', serialized = '1'");
			}
		}
	}

	public function deleteSetting() {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "soconfig`");
	}
	

	public function editMobile($data, $store_id = 0) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "soconfig` WHERE store_id = '" . (int)$store_id . "' AND `key` = 'mobile_general'");
		//var_dump($data);die();
		foreach ($data as $key => $value) {
			if (is_array($value) && $key =='mobile_general' ) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "soconfig SET store_id = '". (int)$store_id."', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(json_encode($value, true)) . "', serialized = '1'");

			}
		}
	}

	public function getMobile( $store_id = 0) {
		$setting_data = array();
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "soconfig WHERE store_id = '" . (int)$store_id . "' AND `key` = 'mobile_general'");
		foreach ($query->rows as $result) {
			$setting_data[$result['key']] = json_decode($result['value'], true);

		}
		return $setting_data;
	}

	public function addEvent(string $code, string $description, string $trigger, string $action, bool $status = true, int $sort_order = 0): int { 
	    $this->db->query("INSERT INTO " . DB_PREFIX . "event SET `code` = '" . $this->db->escape($code) . "', description = '" . $this->db->escape($description) . "', `trigger` = '" . $this->db->escape($trigger) . "', `action` = '" . $this->db->escape($action) . "', `status` = '" . (int)$status . "', `sort_order` = '" . (int)$sort_order . "'"); 
	    return $this->db->getLastId(); 
	}	
	
}
