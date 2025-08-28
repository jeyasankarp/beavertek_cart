<?php
namespace Opencart\Admin\Controller\Extension\SoTheme\Event;
class SoSoconfig extends \Opencart\System\Engine\Controller {
	
 	public function checkProductOptionColumnExist() {
		$sql = "SHOW COLUMNS FROM " .DB_PREFIX. "product_description LIKE 'video'";
		$query = $this->db->query($sql);
		return count($query->rows)>0 ? true : false;
	}   
	
	public function model_product_feature_descriptions_after(string &$route, array &$data, mixed &$output): void {

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_description` WHERE `product_id` = '" . (int)$data[0] . "'");
			
		foreach ($query->rows as $result) {
			
			$product_video = array();
			$product_tabtitle = array();
			$product_tabproduct = array();
			if(isset($result['video']))$product_video = $result['video'];
			if(isset($result['tab_title']))$product_tabtitle = $result['tab_title'];
			if(isset($result['html_product_tab']))$product_tabproduct = $result['html_product_tab'];			
			
			$output[$result['language_id']] = [
				'name'             => $result['name'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => $result['tag'],			
				'video'               => $product_video,
				'tab_title'          => $product_tabtitle,
				'html_product_tab'    => $product_tabproduct, 
			];
		}	
	}
	
	public function model_product_feature_after(string &$route, array &$data, mixed &$output): void {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "product_description` WHERE `product_id` = '" . (int)$data[0] . "'");
        foreach ($data[1]['product_description'] as $language_id => $value) {		
			if ($this->checkProductOptionColumnExist()) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET `product_id` = '" . (int)$data[0] . "', `language_id` = '" . (int)$language_id . "', `name` = '" . $this->db->escape($value['name']) . "', `description` = '" . $this->db->escape($value['description']) . "', `tag` = '" . $this->db->escape($value['tag']) . "', `meta_title` = '" . $this->db->escape($value['meta_title']) . "', `meta_description` = '" . $this->db->escape($value['meta_description']) . "', `meta_keyword` = '" . $this->db->escape($value['meta_keyword']) . "', `video` = '" . $this->db->escape($value['video']) . "', `tab_title` = '" . $this->db->escape($value['tab_title']) . "', `html_product_tab` = '" . $this->db->escape($value['html_product_tab']) .  "'");
			}
			else {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_description` SET `product_id` = '" . (int)$data[0] . "', `language_id` = '" . (int)$language_id . "', `name` = '" . $this->db->escape($value['name']) . "', `description` = '" . $this->db->escape($value['description']) . "', `tag` = '" . $this->db->escape($value['tag']) . "', `meta_title` = '" . $this->db->escape($value['meta_title']) . "', `meta_description` = '" . $this->db->escape($value['meta_description']) . "', `meta_keyword` = '" . $this->db->escape($value['meta_keyword']) . "'");
			}	
		}
	}	
	
	public function product_feature_before(&$route, &$data){
		$this->load->language('extension/so_theme/module/backend_product_options','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));
		$product_video = array();
		$product_tabtitle = array();
		
		$product = $this->model_catalog_product->getProduct($data['product_id']);
		
		$checkProductOptionColumn = "SHOW COLUMNS FROM " .DB_PREFIX. "product_description LIKE 'video'";
		$querycheckProductOption = $this->db->query($checkProductOptionColumn);
		$data['checkProductOption']= !empty($querycheckProductOption->rows) ? true : false;
	
		if(isset($product['video'])){$product_video = $product['video'];}
		if(isset($product['tab_title'])){$product_tabtitle = $product['tab_title'];}	
	
	}	
	
	public function product_feature_list_before(&$route, &$data){
		foreach($data['products'] as $key => $item) {
			$product = $this->model_catalog_product->getProduct($item['product_id']);
			$checkProductOptionColumn = "SHOW COLUMNS FROM " .DB_PREFIX. "product_description LIKE 'video'";
			$querycheckProductOption = $this->db->query($checkProductOptionColumn);
			$data['checkProductOption']= !empty($querycheckProductOption->rows) ? true : false;
		
			if(isset($product['video'])){$data['products'][$key]['video'] = $product['video'];}
			if(isset($product['tab_title'])){$data['products'][$key]['tab_title'] = $product['tab_title'];}			
		}		
	}
	
	public function product_feature_list_after(string &$route, array &$data, mixed &$output): void {

		foreach($data['products'] as $item) {
			$yt2='<td>'.$item['name']; 
			if(isset($item['video']) and $item['tab_title']){ $yt2 .=' <span class="badge text-bg-success">SO Feature</span>';
			}
            $output = str_replace('<td>'.$item['name'],$yt2,$output);
		}
	}
	
	public function product_feature_form_after(string &$route, array &$data, mixed &$output): void {
        if($data['checkProductOption']) {
			$output = str_replace('<li class="nav-item"><a href="#tab-data" data-bs-toggle="tab" class="nav-link">','<li class="nav-item"><a href="#tab-soproduct" data-bs-toggle="tab" class="nav-link">'.$data['tab_feature'].'</a></li><li class="nav-item"><a href="#tab-data" data-bs-toggle="tab" class="nav-link">
		',$output);
	    
			$yt2 ='
			 <div class="tab-pane" id="tab-soproduct">
              <ul class="nav nav-tabs" id="solanguage">';
                foreach($data['languages'] as $key => $language ) {
                   $yt2 .='<li><a href="#solanguage'.$language['language_id'].'" data-bs-toggle="tab" class="nav-link ';if ($key === array_key_first($data['languages'])){$yt2 .='active';}$yt2 .='"><img src="language/'.$language['code'].'/'.$language['code'].'.png" title="'.$language['name'].'" /> '.$language['name'].'</a></li>';
                }
              $yt2 .='</ul>
              <div class="tab-content">';
                foreach($data['languages'] as $key => $language ) {				
                $yt2 .='<div class="tab-pane ';if ($key === array_key_first($data['languages'])){$yt2 .='active';}$yt2 .='" id="solanguage'.$language['language_id'].'">
                    <div class="row mb-3">
                        <label class="col-sm-2 control-label" for="input-video'.$language['language_id'].'">
                            <strong style="color:red">NEW! </strong>
                            <span data-toggle="tooltip" title="" data-original-title="Enter full video thumbnail link on Product Page"> '.$data['entry_video_link'].' </span>
                            <div style="font-weight:normal;">
                                (e.g. https://www.youtube.com/watch?v=Wdtw_A5FDGs)
                            </div>
                        </label>
                        <div class="col-sm-10">
                          <input type="text" name="product_description['.$language['language_id'].'][video]" value="';if(isset($data['product_description'][$language['language_id']])) { $yt2 .=$data['product_description'][$language['language_id']]['video'];}$yt2 .='" placeholder="'.$data['entry_video_link'].'" id="input-video'.$language['language_id'].'" class="form-control" />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 control-label" >
                            <strong style="color:red">NEW! </strong>
                            <span data-toggle="tooltip" title="" data-original-title="Enter title for custom tab on Product Page"> '.$data['entry_custom_tab_title'].' </span>
                        </label>

                        <div class="col-sm-10">
                          <input type="text" name="product_description['.$language['language_id'].'][tab_title]" value="';if(isset($data['product_description'][$language['language_id']])) { $yt2 .= $data['product_description'][$language['language_id']]['tab_title'];} $yt2 .='" placeholder="'.$data['entry_custom_tab_title'].'" id="input-tab-title'.$language['language_id'].'" class="form-control" />
                         
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 control-label" >
                            <strong style="color:red">NEW! </strong>
                            <span data-toggle="tooltip" title="" data-original-title="Enter any html content for custom tab on Product Page">
                               '.$data['entry_description_custom_tab'].'</span>
                        </label>

                        <div class="col-sm-10">
                          <textarea name="product_description['.$language['language_id'].'][html_product_tab]" placeholder="'.$data['entry_description'].'" id="input-html_product_tab'.$language['language_id'].'" data-oc-toggle="ckeditor" data-lang="'. $data['ckeditor'] .'" class="form-control">';if(isset($data['product_description'][$language['language_id']])) { $yt2 .= $data['product_description'][$language['language_id']]['html_product_tab'] ;}$yt2 .='</textarea>
                        </div>
                    </div>
                  
             
                </div>';
                }
            $yt2 .='</div>
            </div>';		
		    $yt2 .='<div id="tab-data" class="tab-pane">';
			
			$output = str_replace('<div id="tab-data" class="tab-pane">',$yt2,$output);
		}
		
		
	}	
	
	public function so_menu_before(&$route, &$data){
				$magentech = array();
				if ($this->user->hasPermission('access', 'extension/so_theme/module/soconfig')) {		
					$magentech[] = array(
						'name'	   => 'So Themes Config',
						'href'     => $this->url->link('extension/so_theme/module/soconfig', 'user_token=' . $this->session->data['user_token'], true),
						'children' => array()
					);
				}
				if ($this->user->hasPermission('access', 'extension/so_theme/module/somobile')) {		
					$magentech[] = array(
						'name'	   => 'So Mobile',
						'href'     => $this->url->link('extension/so_theme/module/somobile', 'user_token=' . $this->session->data['user_token'], true),
						'children' => array()
					);
				}
				if ($this->user->hasPermission('access', 'extension/so_theme/module/so_page_builder')) {		
					$magentech[] = array(
						'name'	   => 'So Page Builder',
						'href'     => $this->url->link('extension/so_theme/module/so_page_builder', 'user_token=' . $this->session->data['user_token'], true),
						'children' => array()
					);
				}
				if ($this->user->hasPermission('access', 'extension/so_theme/module/so_megamenu')) {		
					$magentech[] = array(
						'name'	   => 'So Mega Menu',
						'href'     => $this->url->link('extension/so_theme/module/so_megamenu', 'user_token=' . $this->session->data['user_token'], true),
						'children' => array()
					);
				}
				if ($this->user->hasPermission('access', 'extension/so_theme/module/so_searchpro')) {    
                  $magentech[] = array(
                    'name'     => 'So Search Pro',
                    'href'     => $this->url->link('extension/so_theme/module/so_searchpro', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_html_content')) {   
                  $magentech[] = array(
                    'name'     => 'So Html Content',
                    'href'     => $this->url->link('extension/so_theme/module/so_html_content', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_home_slider')) {    
                  $magentech[] = array(
                    'name'     => 'So Home Slider',
                    'href'     => $this->url->link('extension/so_theme/module/so_home_slider', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
                /*if ($this->user->hasPermission('access', 'extension/so_theme/module/so_filter_shop_by')) {   
                  $magentech[] = array(
                    'name'     => 'So Filter Shop By',
                    'href'     => $this->url->link('extension/so_theme/module/so_filter_shop_by', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }*/
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_newletter_custom_popup')) {   
                  $magentech[] = array(
                    'name'     => 'So Newletter',
                    'href'     => $this->url->link('extension/so_theme/module/so_newletter_custom_popup', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_brands')) {   
                  $magentech[] = array(
                    'name'     => 'So Brands',
                    'href'     => $this->url->link('extension/so_theme/module/so_brands', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }				
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_deals')) {    
                  $magentech[] = array(
                    'name'     => 'So Deals',
                    'href'     => $this->url->link('extension/so_theme/module/so_deals', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_extra_slider')) {   
                  $magentech[] = array(
                    'name'     => 'So Extra Slider',
                    'href'     => $this->url->link('extension/so_theme/module/so_extra_slider', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_sociallogin')) {    
                  $magentech[] = array(
                    'name'     => 'So Social Login',
                    'href'     => $this->url->link('extension/so_theme/module/so_sociallogin', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_listing_tabs')) {   
                  $magentech[] = array(
                    'name'     => 'So Listing Tabs',
                    'href'     => $this->url->link('extension/so_theme/module/so_listing_tabs', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_categories')) {   
                  $magentech[] = array(
                    'name'     => 'So Categories',
                    'href'     => $this->url->link('extension/so_theme/module/so_categories', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_popular_tags')) {    
                  $magentech[] = array(
                    'name'     => 'So Popular Tags',
                    'href'     => $this->url->link('extension/so_theme/module/so_popular_tags', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_quickview')) {    
                  $magentech[] = array(
                    'name'     => 'So Quickview',
                    'href'     => $this->url->link('extension/so_theme/module/so_quickview', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_onepagecheckout')) {    
                  $magentech[] = array(
                    'name'     => 'So Onepage Checkout',
                    'href'     => $this->url->link('extension/so_theme/module/so_onepagecheckout', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_latest_blog')) {    
                  $magentech[] = array(
                    'name'     => 'So Latest Blog',
                    'href'     => $this->url->link('extension/so_theme/module/so_latest_blog', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_instagram_gallery')) {    
                  $magentech[] = array(
                    'name'     => 'So Instagram Gallery',
                    'href'     => $this->url->link('extension/so_theme/module/so_instagram_gallery', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_countdown')) {    
                  $magentech[] = array(
                    'name'     => 'So Countdown Popup',
                    'href'     => $this->url->link('extension/so_theme/module/so_countdown', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_super_category')) {    
                  $magentech[] = array(
                    'name'     => 'So Super Category',
                    'href'     => $this->url->link('extension/so_theme/module/so_super_category', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
				if ($this->user->hasPermission('access', 'extension/so_theme/module/so_call_for_price')) {    
                  $magentech[] = array(
                    'name'     => 'So Call For Price',
                    'href'     => $this->url->link('extension/so_theme/module/so_call_for_price', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_facebook_message')) {    
                  $magentech[] = array(
                    'name'     => 'So Facebook Message',
                    'href'     => $this->url->link('extension/so_theme/module/so_facebook_message', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
                if ($this->user->hasPermission('access', 'extension/so_theme/module/so_facebook')) {    
                  $magentech[] = array(
                    'name'     => 'So Facebook',
                    'href'     => $this->url->link('extension/so_theme/module/so_facebook', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
				if ($this->user->hasPermission('access', 'extension/so_theme/module/so_basic_products')) {    
                  $magentech[] = array(
                    'name'     => 'So Basic Products',
                    'href'     => $this->url->link('extension/so_theme/module/so_basic_products', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }
				if ($this->user->hasPermission('access', 'extension/so_theme/module/so_category_slider')) {    
                  $magentech[] = array(
                    'name'     => 'So Category Slider',
                    'href'     => $this->url->link('extension/so_theme/module/so_category_slider', 'user_token=' . $this->session->data['user_token'], true),
                    'children' => array()
                  );
                }		
				if ($magentech) {					
					$data['menus'][] = array(
						'id'       => 'menu-magentech',
						'icon'	   => 'fa fa-cubes', 
						'name'	   => 'OpenCartWorks',
						'href'     => '',
						'children' => $magentech
					);		
				}
				
	}
	
	public function so_menu_after(string &$route, array &$data, mixed &$output): void {}
	
	public function so_controller_layout_before(&$route, &$data){
        if(!defined('DIR_SOCONFIG')) define('DIR_SOCONFIG','../extension/so_theme/admin/view/template/soconfig/');
		$this->document->addStyle(DIR_SOCONFIG.'asset/css/select2.min.css');
		$this->document->addScript(DIR_SOCONFIG.'asset/js/select2.min.js');			
		$direction = $this->language->get('direction');			
        if ($direction != 'rtl') $this->document->addStyle(DIR_SOCONFIG.'asset/css/theme.css');
		else $this->document->addStyle(DIR_SOCONFIG.'asset/css/theme-rtl.css');	
	}
	
	public function so_layout_before(&$route, &$data){
		require_once (DIR_EXTENSION.'so_theme/admin/view/template/soconfig/class/so_field.php');		
		$fields = new \So_Fields($data); 
		$data['fields'] = $fields; 		
		$route = 'extension/so_theme/soconfig/layout_form';
	}
	public function so_layout_after(&$route, &$data){
	}	
	
}