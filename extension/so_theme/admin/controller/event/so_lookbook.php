<?php
namespace Opencart\Admin\Controller\Extension\SoTheme\Event;
class SoLookbook extends \Opencart\System\Engine\Controller {
    public function so_menu_before(&$route, &$data) {
        // So LookBook
		$this->load->language('extension/so_theme/module/so_lookbook');
        $advanced_search = array();

                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_lookbook')) {      
                    $advanced_search[] = array(
                        'name'     => $this->language->get('text_so_lookbook_manage'),
                        'href'     => $this->url->link('extension/so_theme/module/so_lookbook', 'user_token=' . $this->session->data['user_token'], true),
                        'children' => array()       
                    );                  
                }

                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_lookbook_slider')) {      
                    $advanced_search[] = array(
                        'name'     => $this->language->get('text_so_lookbook_slider_manage'),
                        'href'     => $this->url->link('extension/so_theme/module/so_lookbook_slider', 'user_token=' . $this->session->data['user_token'], true),
                        'children' => array()       
                    );                  
                }

                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_lookbook_config')) {
                    $advanced_search[] = array(
                        'name'     => $this->language->get('text_so_lookbook_config'),
                        'href'     => $this->url->link('extension/so_theme/module/so_lookbook_config', 'user_token=' . $this->session->data['user_token'], true),
                        'children' => array()       
                    );                  
                }

                if ($advanced_search) {
                    $data['menus'][] = array(
                        'name'     => $this->language->get('text_so_lookbook'),
						'icon'     => 'fas fa-cog',
                        'href'     => '',
                        'children' => $advanced_search
                    );
                }
				
				
		
    }		
}