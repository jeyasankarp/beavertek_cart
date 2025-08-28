<?php
namespace Opencart\Catalog\Model\Extension\SoTheme\Module;
class SoLatestBlog extends \Opencart\System\Engine\Model {
	
	public function getArticles(array $data = []): array {
		$sql = "SELECT * FROM `" . DB_PREFIX . "article` `a` LEFT JOIN `" . DB_PREFIX . "article_description` `ad` ON (`a`.`article_id` = `ad`.`article_id`) LEFT JOIN `" . DB_PREFIX . "article_to_store` `a2s` ON (`a`.`article_id` = `a2s`.`article_id`) WHERE `ad`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' AND `a2s`.`store_id` = '" . (int)$this->config->get('config_store_id') . "' AND `a`.`status` = '1'";

		if (!empty($data['filter_search'])) {
			$sql .= " AND (";

			$implode = [];

			$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_search'])));
			$words = array_filter($words);

			foreach ($words as $word) {
				$implode[] = "`ad`.`name` LIKE '" . $this->db->escape('%' . $word . '%') . "'";
			}

			if ($implode) {
				$sql .= " (" . implode(" OR ", $implode) . ")";
			}

			$sql .= " OR `ad`.`description` LIKE '" . $this->db->escape('%' . (string)$data['filter_search'] . '%') . "'";

			$implode = [];

			foreach ($words as $word) {
				$implode[] = "`ad`.`tag` LIKE '" . $this->db->escape('%' . $word . '%') . "'";
			}

			if ($implode) {
				$sql .= " OR (" . implode(" OR ", $implode) . ")";
			}

			$sql .= ")";
		}

		if (!empty($data['filter_topic_id'])) {
			
				$sql .= " AND `a`.`topic_id` IN (" . implode(',', $data['filter_topic_id']) . ")";			

		}

		if (!empty($data['filter_author'])) {
			$sql .= " AND `a`.`author` = '" . $this->db->escape($data['filter_author']) . "'";
		}

		$sort_data = [
			'rating',
			'date_added'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY `a`.`" . $data['sort'] . "`";
		} else {
			$sql .= " ORDER BY `a`.`date_added`";
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

		$key = md5($sql);

		$article_data = $this->cache->get('article.' . $key);

		if (!$article_data) {
			$query = $this->db->query($sql);

			$article_data = $query->rows;

			$this->cache->set('article.' . $key, $article_data);
		}

		return $article_data;
	}
}