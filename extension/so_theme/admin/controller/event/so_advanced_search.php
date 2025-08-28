<?php
namespace Opencart\Admin\Controller\Extension\SoTheme\Event;
class SoAdvancedSearch extends \Opencart\System\Engine\Controller {

    public function so_menu_before(&$route, &$data) {
        // So Advanced Search
		$this->load->language('extension/so_theme/module/so_advanced_search','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));
        $advanced_search = array();

                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_make_model_year')) {      
                    $advanced_search[] = array(
                        'name'     => $this->language->get('text_so_make_model_year'),
                        'href'     => $this->url->link('extension/so_theme/module/so_make_model_year', 'user_token=' . $this->session->data['user_token'], true),
                        'children' => array()       
                    );                  
                }

                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_make_model_year')) {      
                    $advanced_search[] = array(
                        'name'     => $this->language->get('text_so_product_to_mmy'),
                        'href'     => $this->url->link('extension/so_theme/module/so_product_to_mmy', 'user_token=' . $this->session->data['user_token'], true),
                        'children' => array()       
                    );                  
                }

                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_advanced_search')) {
                    $advanced_search[] = array(
                        'name'     => $this->language->get('text_so_advanced_search_configuration'),
                        'href'     => $this->url->link('extension/so_theme/module/so_advanced_search', 'user_token=' . $this->session->data['user_token'], true),
                        'children' => array()       
                    );                  
                }

                if ($advanced_search) {
                    $data['menus'][] = array(
                        'name'     => $this->language->get('text_so_advanced_search'),
						'icon'     => 'fas fa-cog',
                        'href'     => '',
                        'children' => $advanced_search
                    );
                }
		
    }	

}