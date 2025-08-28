<?php
namespace Opencart\Catalog\Model\Extension\SoTheme\Module;
class SoHomeSlider extends \Opencart\System\Engine\Model {
	
	public function getListSlider($data = array()) {
		$query = "SELECT * FROM " . DB_PREFIX . "so_homeslider WHERE module_id = ".$data['moduleid']." ORDER BY position";
		$query = $this->db->query( $query );
		$row = $query->rows;
		
		$data = array();
		foreach ($query->rows as $item) {
			$sql = "SELECT * FROM " . DB_PREFIX . "so_homeslider_description WHERE jusv = ".$item['id']." ";
			$sql = $this->db->query( $sql );
			foreach ($sql->rows as $value) {
				array_push($data,$value);
			}
            
		}

	 	return $data;		
	}
}