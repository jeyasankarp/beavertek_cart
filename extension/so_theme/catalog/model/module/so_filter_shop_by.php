<?php 
namespace Opencart\Catalog\Model\Extension\SoTheme\Module;
class SoFilterShopBy extends \Opencart\System\Engine\Model {
	
	/**
	 * @var array
	 */
	protected array $statement = [];

	/**
	 * @param \Opencart\System\Engine\Registry $registry
	 */
	public function __construct(\Opencart\System\Engine\Registry $registry) {
		$this->registry = $registry;

		// Storing some sub queries so that we are not typing them out multiple times.
		$this->statement['discount'] = "(SELECT `pd2`.`price` FROM `" . DB_PREFIX . "product_discount` `pd2` WHERE `pd2`.`product_id` = `p`.`product_id` AND `pd2`.`customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "'AND `pd2`.`quantity` = '1' AND ((`pd2`.`date_start` = '0000-00-00' OR `pd2`.`date_start` < NOW()) AND (`pd2`.`date_end` = '0000-00-00' OR `pd2`.`date_end` > NOW())) ORDER BY `pd2`.`priority` ASC, `pd2`.`price` ASC LIMIT 1) AS `discount`";
		$this->statement['special'] = "(SELECT `ps`.`price` FROM `" . DB_PREFIX . "product_discount` `ps` WHERE `ps`.`product_id` = `p`.`product_id` AND `ps`.`customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((`ps`.`date_start` = '0000-00-00' OR `ps`.`date_start` < NOW()) AND (`ps`.`date_end` = '0000-00-00' OR `ps`.`date_end` > NOW())) ORDER BY `ps`.`priority` ASC, `ps`.`price` ASC LIMIT 1) AS `special`";
		$this->statement['reward'] = "(SELECT `pr`.`points` FROM `" . DB_PREFIX . "product_reward` `pr` WHERE `pr`.`product_id` = `p`.`product_id` AND `pr`.`customer_group_id` = '" . (int)$this->config->get('config_customer_group_id') . "') AS `reward`";
		$this->statement['review'] = "(SELECT COUNT(*) FROM `" . DB_PREFIX . "review` `r` WHERE `r`.`product_id` = `p`.`product_id` AND `r`.`status` = '1' GROUP BY `r`.`product_id`) AS `reviews`";
	}
	
	public function getAllOptions($product_id){
		$product_id = implode(",",array_map('intval',$product_id));
		$sql = "SELECT DISTINCT pov.option_value_id, od.name AS option_name, o.type AS option_type, ov.image AS opt_value_image, ov.sort_order AS attribute_sort, ovd.name AS opt_value_name, o.option_id 
				FROM ".DB_PREFIX."product_option_value AS pov 
				LEFT JOIN ".DB_PREFIX."option_value AS ov ON pov.option_id = ov.option_id AND pov.option_value_id = ov.option_value_id 
				LEFT JOIN ".DB_PREFIX."option_value_description AS ovd ON pov.option_id = ovd.option_id AND pov.option_value_id = ovd.option_value_id 
				LEFT JOIN ".DB_PREFIX."product_option AS po ON pov.option_id = po.option_id AND pov.product_id = po.product_id 
				LEFT JOIN ".DB_PREFIX."option_description AS od ON pov.option_id = od.option_id 
				LEFT JOIN ".DB_PREFIX."option AS o ON pov.option_id = o.option_id 
				WHERE pov.product_id IN (".$product_id.") 
					AND po.required = '1' 
					AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
					AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' 
				ORDER BY o.sort_order, ov.sort_order";
		$query = $this->db->query($sql);
		$option_data = array();
		foreach($query->rows as $item)
		{
			$getProduct_opt = $this->db->query("SELECT pov.product_id FROM ".DB_PREFIX."product_option_value AS pov LEFT JOIN ".DB_PREFIX."product_description AS pd ON pov.product_id = pd.product_id WHERE pov.option_value_id ='".$item['option_value_id']."' AND pov.product_id IN (".$product_id.") AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
			$product_arr_id = array();
			foreach($getProduct_opt->rows as $result)
			{
				$product_arr_id[] 	= $result['product_id'];
			}
			if($item['opt_value_image'] == "")
			{
				$item['opt_value_image'] = "no_image.png";
			}
			$option_data[$item['option_name'].'_'.$item['option_type'].'_'.$this->convertNameToParam($item['option_name']).'_'.$item['option_id']][] = array(
				'option_id' 			=> $item['option_id'],
				'opt_value_id' 			=> $item['option_value_id'],
				'opt_value_image' 		=> $this->model_tool_image->resize($item['opt_value_image'], 20, 20),
				'opt_value_name'  		=> $item['opt_value_name'],
				'opt_value_value'  		=> "disp_opt_".$this->convertNameToParam($item['opt_value_name']),
				'opt_count_product'  	=> count($getProduct_opt->rows),
				'opt_list_product'  	=> implode(",",$product_arr_id),
				'att_sort'				=> $item['attribute_sort']
			);
		}
		
		return $option_data;
	}
	
	public function getAllAttributes($product_id){
		$product_id = implode(",",array_map('intval',$product_id));
		$sql = "SELECT DISTINCT pa.attribute_id, pa.language_id AS att_language_id, ad.name AS attribute_name, agd.name AS att_group_name, agd.attribute_group_id AS attribute_group_id, a.sort_order AS attribute_sort FROM ".DB_PREFIX."product_attribute AS pa LEFT JOIN ".DB_PREFIX."attribute AS a ON pa.attribute_id = a.attribute_id LEFT JOIN ".DB_PREFIX."attribute_description AS ad ON a.attribute_id = ad.attribute_id LEFT JOIN ".DB_PREFIX."attribute_group AS ag ON a.attribute_group_id = ag.attribute_group_id LEFT JOIN ".DB_PREFIX."attribute_group_description AS agd ON ag.attribute_group_id = agd.attribute_group_id WHERE pa.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.product_id IN (".$product_id.") AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ag.sort_order";
		$query = $this->db->query($sql);
		$attribute_data = array();
		foreach($query->rows as $item)
		{
			$getProduct_att = $this->db->query("SELECT pa.product_id FROM ".DB_PREFIX."product_attribute AS pa WHERE pa.attribute_id ='".$item['attribute_id']."' AND pa.product_id IN (".$product_id.") AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "'");
			$product_arr_id = array();
			foreach($getProduct_att->rows as $result)
			{
				$product_arr_id[] = $result['product_id'];
			}
			$attribute_data[$item['att_group_name'].'_'.$this->convertNameToParam($item['att_group_name']).'_'.$item['attribute_group_id']][] = array(
				'att_value_id' 				=> $item['attribute_id'],
				'att_value_name' 			=> $item['attribute_name'],
				'att_value_value'  			=> "disp_att_id_".$item['attribute_id'],
				'att_count_product'  		=> count($getProduct_att->rows),
				'att_list_product'  		=> implode(",",$product_arr_id),
				'att_sort' 					=> $item['attribute_sort']
			);
		}

		foreach ($attribute_data as $key => $attr_data) {
			$attr_data = $this->array_sort_by_column('att_sort',$attr_data);
			$attribute_data[$key]	= $attr_data;
		}

		return $attribute_data;
	}
	
	public function getProducts($opt_value_id,$att_value_id,$manu_value_id,$text_search,$minPrice,$maxPrice,$subcate_value_id,$category_id,$condition_search, $sort_by, $order_by, $p_start, $p_limit){
		$sql = "SELECT DISTINCT * , p.product_id,". $this->statement['discount'] . ", " . $this->statement['special'] . ", " . $this->statement['reward'] . ", " . $this->statement['review'];

		$sql .= "FROM ".DB_PREFIX."product AS p 
				LEFT JOIN ".DB_PREFIX."product_description AS pd ON p.product_id = pd.product_id 
				LEFT JOIN ".DB_PREFIX."product_to_category AS pc ON p.product_id = pc.product_id";		
		$sql .= "\n LEFT JOIN " . DB_PREFIX . "product_to_store AS p2s ON (p.product_id = p2s.product_id)";
		
		if (isset($opt_value_id) && !empty($opt_value_id)) {
			if (isset($condition_search) && !empty($condition_search) && $condition_search == 'OR') {
				$sql .= "\n LEFT JOIN ".DB_PREFIX."product_option_value AS pov ON (p.product_id = pov.product_id)";
			}
		}

		$sql .= "\n WHERE p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND";
		$sql .= "\npd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND";
		$sql .= "\n p.status = 1 AND";
		
        $sql .= " p.price>='" . $minPrice. "'";	
		$sql .= " AND p.price<='" . $maxPrice. "' AND ";	

		if (isset($opt_value_id) && !empty($opt_value_id)) {
			if (isset($condition_search) && !empty($condition_search)) {
				if ($condition_search == 'AND') {
					$tmp = explode(',', $opt_value_id);
					foreach ($tmp as $ovid) {
						$sql .= "\nEXISTS (SELECT * FROM `".DB_PREFIX."product_option_value` pov WHERE p.product_id = pov.product_id AND pov.option_value_id IN (".$ovid.") ) AND ";
					}
				}
				else {
					$sql .= "\npov.option_value_id IN (".$opt_value_id.") AND";
				}
			}
		}
		if (isset($att_value_id) && !empty($att_value_id)) {
			$tmp = explode(',', $att_value_id);
			foreach ($tmp as $avid) {
				$sql .= "\nEXISTS (SELECT * FROM `".DB_PREFIX."product_attribute` pa WHERE p.product_id = pa.product_id AND pa.attribute_id = ".$avid." ) AND ";
			}
		}
		if (isset($manu_value_id) && !empty($manu_value_id)) {
			$sql .= "\np.manufacturer_id IN (".$manu_value_id.") AND";
		}
		
		if($text_search != "")
		{
			$sql .= "\n(LCASE(pd.name) LIKE '%".strtolower($this->db->escape($text_search))."%' OR LCASE(p.model) = '" . $this->db->escape(strtolower($text_search)) . "' OR LCASE(p.sku) = '" . $this->db->escape(strtolower($text_search)) . "' OR LCASE(p.upc) = '" . $this->db->escape(strtolower($text_search)) . "' OR LCASE(p.ean) = '" . $this->db->escape(strtolower($text_search)) . "' OR LCASE(p.jan) = '" . $this->db->escape(strtolower($text_search)) . "' OR LCASE(p.isbn) = '" . $this->db->escape(strtolower($text_search)) . "' OR LCASE(p.mpn) = '" . $this->db->escape(strtolower($text_search)) . "') AND";
		}

		if($subcate_value_id != "")
		{
			$category_id = $subcate_value_id;
		}
			
		$sql .= "\n pc.category_id = '".$category_id."'";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.quantity',
			'p.price',
			'rating',
			'p.sort_order',
			'p.date_added'
		);

		if (isset($sort_by) && in_array($sort_by, $sort_data)) {
			if ($sort_by == 'pd.name' || $sort_by == 'p.model') {
				$sql .= " ORDER BY LCASE(" . $sort_by . ")";
			} elseif ($sort_by == 'p.price') {
				$sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
			} else {
				$sql .= " ORDER BY " . $sort_by;
			}
		} else {
			$sql .= " ORDER BY p.sort_order";
		}

		if (isset($order_by) && ($order_by == 'DESC')) {
			$sql .= " DESC, LCASE(pd.name) DESC";
		} else {
			$sql .= " ASC, LCASE(pd.name) ASC";
		}

		if (isset($p_start) || isset($p_limit)) {
			if ($p_start < 0) {
				$p_start = 0;
			}

			if ($p_limit < 1) {
				$p_limit = 20;
			}

			$sql .= " LIMIT " . (int)$p_start . "," . (int)$p_limit;
		}
		
		$product_data = (array)$this->cache->get('product.' . md5($sql));

		if (!$product_data) {
			$query = $this->db->query($sql);

			$product_data = $query->rows;

			$this->cache->set('product.' . md5($sql), $product_data);
		}		

		return $product_data;
	}

	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total";

		if (!empty($data['category_id'])) {
			$sql .= " FROM " . DB_PREFIX . "product_to_category p2c";
			$sql .= " LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)";			
		} else {
			$sql .= " FROM " . DB_PREFIX . "product p";
		}
		
		if (isset($data['opt_value_id']) && !empty($data['opt_value_id'])) {
			if (isset($data['condition_search']) && !empty($data['condition_search']) && $data['condition_search'] == 'OR') {
				$sql .= "LEFT JOIN ".DB_PREFIX."product_option_value AS pov ON p.product_id = pov.product_id";
			}
		}		

		$sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ";

		if (isset($data['opt_value_id']) && !empty($data['opt_value_id'])) {
			if (isset($data['condition_search']) && !empty($data['condition_search'])) {
				if ($data['condition_search'] == 'AND') {
					$tmp = explode(',', $data['opt_value_id']);
					foreach ($tmp as $ovid) {
						$sql .= "\nEXISTS (SELECT * FROM `".DB_PREFIX."product_option_value` pov WHERE p.product_id = pov.product_id AND pov.option_value_id IN (".$ovid.") ) AND ";
					}
				}
				else {
					$sql .= "\npov.option_value_id IN (".$data['opt_value_id'].") AND";
				}
			}
		}
		
        $sql .= " p.price>='" . $data['minPrice']. "'";	
		$sql .= " AND p.price<='" . $data['maxPrice']. "' AND ";		

		if (isset($data['att_value_id']) && !empty($data['att_value_id'])) {
			$tmp = explode(',', $data['att_value_id']);
			foreach ($tmp as $avid) {
				$sql .= "\nEXISTS (SELECT * FROM `".DB_PREFIX."product_attribute` pa WHERE p.product_id = pa.product_id AND pa.attribute_id = ".$avid." ) AND ";
			}
		}
		if (isset($data['manu_value_id']) && !empty($data['manu_value_id'])) {
			$sql .= "\np.manufacturer_id IN (".$data['manu_value_id'].") AND";
		}
		
		if($data['text_search'] != "")
		{
			$sql .= "\n(LCASE(pd.name) LIKE '%".strtolower($this->db->escape($data['text_search']))."%' OR LCASE(p.model) = '" . $this->db->escape(strtolower($data['text_search'])) . "' OR LCASE(p.sku) = '" . $this->db->escape(strtolower($data['text_search'])) . "' OR LCASE(p.upc) = '" . $this->db->escape(strtolower($data['text_search'])) . "' OR LCASE(p.ean) = '" . $this->db->escape(strtolower($data['text_search'])) . "' OR LCASE(p.jan) = '" . $this->db->escape(strtolower($data['text_search'])) . "' OR LCASE(p.isbn) = '" . $this->db->escape(strtolower($data['text_search'])) . "' OR LCASE(p.mpn) = '" . $this->db->escape(strtolower($data['text_search'])) . "') AND";
		}

		if($data['subcate_value_id'] != "")
		{
			$data['category_id'] = $data['subcate_value_id'];
		}

		if (!empty($data['category_id'])) {
			$sql .= " p2c.category_id = '" . (int)$data['category_id'] . "'";
		}

		$query = $this->db->query($sql);
		return $query->row['total'];	
	}
	
	public function getAllProducts($category_id){

		$sql = "SELECT DISTINCT `p`.`tax_class_id` , `p`.`price` , `p`.`product_id`, " . $this->statement['discount'] . ", " . $this->statement['special'] . " ";

		$sql .= " FROM `" . DB_PREFIX . "category_to_store` `c2s`";

		$sql .= " LEFT JOIN `" . DB_PREFIX . "category_path` `cp` ON (`cp`.`category_id` = `c2s`.`category_id` AND `c2s`.`store_id` = '" . (int)$this->config->get('config_store_id') . "') LEFT JOIN `" . DB_PREFIX . "product_to_category` `p2c` ON (`p2c`.`category_id` = `cp`.`category_id`)";

		$sql .= " LEFT JOIN `" . DB_PREFIX . "product_to_store` `p2s` ON (`p2s`.`product_id` = `p2c`.`product_id` AND `p2s`.`store_id` = '" . (int)$this->config->get('config_store_id') . "')";

		$sql .= " LEFT JOIN `" . DB_PREFIX . "product` `p` ON (`p`.`product_id` = `p2s`.`product_id` AND `p`.`status` = '1' AND `p`.`date_available` <= NOW())";

		$sql .= " LEFT JOIN `" . DB_PREFIX . "product_description` `pd` ON (`p`.`product_id` = `pd`.`product_id`) WHERE `pd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "'";
      
	    $sql .= " AND `cp`.`path_id` = '" . (int)$category_id . "'";

		$product_data = (array)$this->cache->get('product.' . md5($sql));

		if (!$product_data) {
			$query = $this->db->query($sql);

			$product_data = $query->rows;

			$this->cache->set('product.' . md5($sql), $product_data);
		}
			
		return $product_data;
	}
	
	public function getProductsOrderByPridce($product_arr){
		foreach($product_arr as $result)
		{
			$data = $this->model_catalog_product->getProduct($result);
			$price = $this->tax->calculate($data['price'], $data['tax_class_id'], $this->config->get('config_tax'));
			if ((float)$data['special']) {
				$price = $this->tax->calculate($data['special'], $data['tax_class_id'], $this->config->get('config_tax'));
			}
			$data['price_soFilter'] = $price;
			$product_data[] = $data;
		}
		
		return $product_data;
	}

	public function getAttributes($data = array()) {
		$sql = "SELECT *, (SELECT agd.name FROM " . DB_PREFIX . "attribute_group_description agd WHERE agd.attribute_group_id = a.attribute_group_id AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS attribute_group FROM " . DB_PREFIX . "attribute a LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND ad.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_attribute_group_id'])) {
			$sql .= " AND a.attribute_group_id = '" . $this->db->escape($data['filter_attribute_group_id']) . "'";
		}

		$sort_data = array(
			'ad.name',
			'attribute_group',
			'a.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY attribute_group, ad.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

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
	
	public function getOptions(){
		$type = "'radio','checkbox','select','image'";
		$sql = 'SELECT o.*, od.name AS option_name FROM '.DB_PREFIX.'option AS o LEFT JOIN '.DB_PREFIX.'option_description AS od ON o.option_id = od.option_id WHERE o.type IN ('.$type.') AND od.language_id = "'.(int)$this->config->get('config_language_id').'" ORDER BY o.sort_order' ;
		$query = $this->db->query($sql);
		return $query->rows;
	}

	public function getAllManufacturerId($product_id) {
		$product_id = implode(",",array_map('intval',$product_id));
		$sql = "SELECT DISTINCT p.manufacturer_id, m.name AS manu_name, m.image AS manu_image, m.sort_order AS attribute_sort FROM " . DB_PREFIX . "product AS p LEFT JOIN ".DB_PREFIX."manufacturer AS m ON p.manufacturer_id = m.manufacturer_id WHERE p.product_id IN (" .$product_id. ") AND p.manufacturer_id != '0'";
		$query = $this->db->query($sql);
		$manu_data = array();
		foreach($query->rows as $item)
		{
			$getProduct_manu = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product AS p LEFT JOIN ".DB_PREFIX."product_description AS pd ON p.product_id = pd.product_id WHERE manufacturer_id = '" . (int)$item['manufacturer_id'] . "' AND p.product_id IN (".$product_id.") AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
			$product_arr_id = array();
			foreach($getProduct_manu->rows as $result)
			{
				$product_arr_id[] = $result['product_id'];
			}
			if ($item['manu_image']) {
				$image = $this->model_tool_image->resize($item['manu_image'], 20, 20);
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', 20, 20);
			}
			if($image == null)
			{
				$image = $this->model_tool_image->resize('placeholder.png', 20, 20);
			}
			$manu_data[] = array(
				'manu_value_id' 		=> $item['manufacturer_id'],
				'manu_value_image' 		=> $image,
				'manu_value_name'  		=> $item['manu_name'],
				'manu_count_product'  	=> count($getProduct_manu->rows),
				'manu_list_product'  	=> implode(",",$product_arr_id),
				'att_sort' 				=> $item['attribute_sort']
			);
		}
		$manu_data = $this->array_sort_by_column('att_sort',$manu_data);
		return $manu_data;
	}
	
	public function getManufacturers($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "manufacturer";

		if (!empty($data['filter_name'])) {
			$sql .= " WHERE name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'name',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

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

	public function getAllSubCategory($categoryId)
	{
		$sql = "SELECT c.category_id,c.image AS cate_image, cd.name AS cate_name FROM ".DB_PREFIX."category AS c LEFT JOIN ".DB_PREFIX."category_description AS cd ON c.category_id = cd.category_id LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '".$categoryId."' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c.status = 1 AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
		$query = $this->db->query($sql);
		$subcategory_data = array();
		$getProduct_subcate = array();
		foreach($query->rows as $item)
		{
			$getProduct_subcate = $this->db->query("SELECT pc.product_id FROM ".DB_PREFIX."product_to_category AS pc LEFT JOIN " . DB_PREFIX . "product p ON (pc.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (pc.product_id = p2s.product_id) WHERE pc.category_id = '".$item['category_id']."' AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p.status = 1");
			$product_arr_id = array();
			foreach($getProduct_subcate->rows as $result)
			{
				$product_arr_id[] = $result['product_id'];
			}
			if ($item['cate_image']) {
				$image = $this->model_tool_image->resize($item['cate_image'], 20, 20);
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', 20, 20);
			}
			if($image == null)
			{
				$image = $this->model_tool_image->resize('placeholder.png', 20, 20);
			}
			$subcategory_data[] = array(
				'subcate_id' 			=> $item['category_id'],
				'subcate_image' 		=> $image,
				'subcate_name'  		=> $item['cate_name'],
				'subcate_value'  		=> "disp_opt_".$this->convertNameToParam($item['cate_name']),
				'subcate_count_product' => count($getProduct_subcate->rows),
				'subcate_list_product'  => implode(',',$product_arr_id)
			);
		}
		return $subcategory_data;
	}
	
	public function convertNameToParam($string) {
		//Lower case everything
		$string = strtolower($string);
		//Make alphanumeric (removes all other characters)
		$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
		//Clean up multiple dashes or whitespaces
		$string = preg_replace("/[\s-]+/", " ", $string);
		//Convert whitespaces and underscore to dash
		$string = preg_replace("/[\s_]/", "-", $string);
		return $string;
	}

	public function array_sort_by_column($col,&$arr = null,  $dir = SORT_ASC) {	
		if (!is_null($arr)) {
			$sort_col = array();
			foreach ($arr as $key=> $row) {
				$sort_col[$key] = $arr[$key][$col];
			}
			
			array_multisort($sort_col, $dir, $arr);

			return $arr;
		}
	}
}
?>