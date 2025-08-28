<?php
namespace Opencart\Admin\Model\Extension\SoTheme\Module;
class SoMakeModelYear extends \Opencart\System\Engine\Model {	

	public function getMakes($data = array()) {
		$sql = "SELECT * FROM `".DB_PREFIX."so_make` ORDER BY `make_name` ASC";
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getMake($make_id) {
		$sql = "SELECT * FROM `".DB_PREFIX."so_make` WHERE make_id = '".(int)$make_id."'";
		$query = $this->db->query($sql);

		return $query->row;
	}

	public function addMake($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "so_make SET make_name = '" . $this->db->escape($data['make_name']) . "'");

		$make_id = $this->db->getLastId();

		return $make_id;
	}

	public function editMake($make_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "so_make SET make_name = '" . $this->db->escape($data['make_name']) . "' WHERE make_id = '".(int)$make_id."'");
	}

	public function deleteMake($make_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "so_make WHERE make_id = '" . (int)$make_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "so_model WHERE make_id = '" . (int)$make_id . "'");
	}

	public function getModels($data = array()) {
		$sql = "SELECT * FROM `".DB_PREFIX."so_model`";
		if (isset($data['make_id']) && !empty($data['make_id'])) {
			$sql .= " WHERE make_id = '".(int)$data['make_id']."'";
		}
		$sql .= " ORDER BY model_name ASC";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getModel($model_id) {
		$sql = "SELECT * FROM `".DB_PREFIX."so_model` WHERE model_id = '".(int)$model_id."'";
		$query = $this->db->query($sql);

		return $query->row;
	}

	public function addModel($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "so_model SET model_name = '" . $this->db->escape($data['model_name']) . "', make_id = '".(int)$data['make_id']."'");

		$model_id = $this->db->getLastId();

		return $model_id;
	}

	public function editModel($model_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "so_model SET model_name = '" . $this->db->escape($data['model_name']) . "', make_id = '".(int)$data['make_id']."' WHERE model_id = '".(int)$model_id."'");
	}

	public function deleteModel($model_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "so_model WHERE model_id = '" . (int)$model_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "so_engine WHERE model_id = '" . (int)$model_id . "'");
	}

	public function getEngines($data = array()) {
		$sql = "SELECT * FROM `".DB_PREFIX."so_engine`";
		if (isset($data['model_id']) && !empty($data['model_id'])) {
			$sql .= " WHERE model_id = '".(int)$data['model_id']."'";
		}
		$sql .= " ORDER BY `engine_name` ASC";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getEngine($engine_id) {
		$sql = "SELECT * FROM `".DB_PREFIX."so_engine` WHERE engine_id = '".(int)$engine_id."'";
		$query = $this->db->query($sql);

		return $query->row;
	}

	public function addEngine($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "so_engine SET engine_name = '" . $this->db->escape($data['engine_name']) . "', model_id = '".(int)$data['model_id']."', make_id = '".(int)$data['make_id']."'");

		$engine_id = $this->db->getLastId();

		return $engine_id;
	}

	public function editEngine($engine_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "so_engine SET engine_name = '" . $this->db->escape($data['engine_name']) . "', model_id = '".(int)$data['model_id']."', make_id = '".(int)$data['make_id']."' WHERE engine_id = '".(int)$engine_id."'");
	}

	public function deleteEngine($engine_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "so_engine WHERE engine_id = '" . (int)$engine_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "so_year WHERE engine_id = '" . (int)$engine_id . "'");
	}

	public function getYears($data = array()) {
		$sql = "SELECT * FROM `".DB_PREFIX."so_year`";
		if (isset($data['engine_id']) && !empty($data['engine_id'])) {
			$sql .= " WHERE engine_id = '".(int)$data['engine_id']."'";
		}
		$sql .= " ORDER BY `year_name` ASC";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getYear($year_id) {
		$sql = "SELECT * FROM `".DB_PREFIX."so_year` WHERE year_id = '".(int)$year_id."'";
		$query = $this->db->query($sql);

		return $query->row;
	}

	public function addYear($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "so_year SET year_name = '" . $this->db->escape($data['year_name']) . "', engine_id = '".(int)$data['engine_id']."', model_id = '".(int)$data['model_id']."', make_id = '".(int)$data['make_id']."'");

		$year_id = $this->db->getLastId();

		return $year_id;
	}

	public function editYear($year_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "so_year SET year_name = '" . $this->db->escape($data['year_name']) . "', engine_id = '".(int)$data['engine_id']."', model_id = '".(int)$data['model_id']."', make_id = '".(int)$data['make_id']."' WHERE year_id = '".(int)$year_id."'");
	}

	public function deleteYear($year_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "so_year WHERE year_id = '" . (int)$year_id . "'");
	}

	public function editProductToMmy($product_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "so_product_to_mmy WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_to_mmy'])) {
			foreach ($data['product_to_mmy'] as $product) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "so_product_to_mmy SET product_id = '" . (int)$product_id . "', make_id = '" . (int)$product['make_id'] . "', model_id = '" . (int)$product['model_id'] . "', engine_id = '" . (int)$product['engine_id'] . "', year_id = '" . (int)$product['year_id'] . "'");
			}
		}
	}

	public function getProductToMmy($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "so_product_to_mmy WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}
}