<?php
namespace Opencart\Admin\Model\Extension\SoTheme\Module;
class SoAdvancedSearch extends \Opencart\System\Engine\Model {	
	
	public function install() {
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".DB_PREFIX."so_make` (
			  `make_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `make_name` varchar(255) NOT NULL,
			  PRIMARY KEY (`make_id`)
			) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
		");
		$this->db->query("INSERT INTO ".DB_PREFIX."so_make VALUES ('1', 'Acura'),('2', 'BMW'),('3', 'Chevrolet'),('4', 'GMC'),('5', 'Honda'),('6', 'John deere'),('7', 'Kawasaky'),
			('8', 'Lexus'),('9', 'mazda'),('10', 'Mercedes'),('11', 'nuko'),('12', 'Opel'),('13', 'Yamaha')
		");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".DB_PREFIX."so_model` (
			  	`model_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  	`make_id` int(11) NOT NULL,
			  	`model_name` varchar(255) NOT NULL,
			  	PRIMARY KEY (`model_id`)
			) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
		");

		$this->db->query("
			INSERT INTO ".DB_PREFIX."so_model VALUES ('1', '2', '1000 Rocket'),
			('2', '10', '6090 '),
			('3', '5', 'Accord'),
			('4', '12', 'Ascona A'),
			('5', '12', 'Ascona B'),
			('6', '12', 'Ascona C'),
			('7', '2', 'BMW')
		");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".DB_PREFIX."so_engine` (
			  	`engine_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  	`model_id` int(11) NOT NULL,
			  	`make_id` int(11) NOT NULL,
			  	`engine_name` varchar(255) NOT NULL,
			  	PRIMARY KEY (`engine_id`)
			) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
		");

		$this->db->query("
			INSERT INTO ".DB_PREFIX."so_engine VALUES ('1', '3', '5', '3.0'),
			('2', '3', '5', '2.0')
		");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".DB_PREFIX."so_year` (
			  	`year_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  	`engine_id` int(11) NOT NULL,
			  	`model_id` int(11) NOT NULL,
			  	`make_id` int(11) NOT NULL,
			  	`year_name` varchar(255) NOT NULL,
			  	PRIMARY KEY (`year_id`)
			) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
		");

		$this->db->query("
			INSERT INTO ".DB_PREFIX."so_year VALUES ('1', '1', '3', '5', '2015'),
			('2', '1', '3', '5', '2016'),
			('3', '1', '3', '5', '2017'),
			('4', '1', '3', '5', '2018')
		");

		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".DB_PREFIX."so_product_to_mmy` (
			  	`product_mmy_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  	`product_id` int(11) NOT NULL,
			  	`make_id` int(11) NOT NULL,
			  	`model_id` int(11) NOT NULL,
			  	`engine_id` int(11) NOT NULL,
			  	`year_id` int(11) NOT NULL,
			  	PRIMARY KEY (`product_mmy_id`)
			) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
		");

		$this->db->query("
			INSERT INTO ".DB_PREFIX."so_product_to_mmy VALUES ('4', '41', '5', '3', '1', '1'),
			('5', '41', '2', '1', '0', '0'),
			('6', '41', '8', '0', '0', '0'),
			('7', '42', '5', '3', '2', '0')
		");

		$this->load->model('setting/module');
		$this->model_setting_module->deleteModulesByCode('so_theme.so_advanced_search');
		$module_data = array(
			'name' 		=> 'So Advanced Search',
			'status'	=> 1
		);
		$this->model_setting_module->addModule('so_theme.so_advanced_search', $module_data);
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."so_make`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."so_model`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."so_engine`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."so_year`");
		$this->db->query("DROP TABLE IF EXISTS `".DB_PREFIX."so_product_to_mmy`");

		$this->load->model('setting/module');
		$this->model_setting_module->deleteModulesByCode('so_theme.so_advanced_search');
	}
}