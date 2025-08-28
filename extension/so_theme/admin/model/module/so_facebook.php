<?php
namespace Opencart\Admin\Model\Extension\SoTheme\Module;
class SoFacebook extends \Opencart\System\Engine\Model {	
	public function getModuleId() {
		$sql = " SHOW TABLE STATUS LIKE '" . DB_PREFIX . "module'" ;
		$query = $this->db->query($sql);
		return $query->rows;
	}
}
?>