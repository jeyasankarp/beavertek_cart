<?php
namespace Opencart\Admin\Controller\Extension\SoTheme\Event;
class SoColorSwatchesPro extends \Opencart\System\Engine\Controller {
	public function controller_before(&$route, &$data){}
	
	public function controller_after(string &$route, array &$args, mixed &$output): void {}
	
	public function model_before(&$route, &$data){}
	
    public function checkProductImageColumnExist() {
          $sql = "SHOW COLUMNS FROM " .DB_PREFIX. "product_image LIKE 'default_of_color'";
          $query = $this->db->query($sql);
          return count($query->rows)>0 ? true : false;
    }	
	
	public function model_after(string &$route, array &$data, mixed &$output): void {
	
        $this->db->query("DELETE FROM `" . DB_PREFIX . "product_image` WHERE `product_id` = '" . (int)$data[0] . "'");
		if (isset($data[1]['product_image'])) {
			foreach ($data[1]['product_image'] as $product_image) {
                if ($this->checkProductImageColumnExist()) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$data[0] . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "', default_of_color = '". (int)$product_image['default_of_color'] ."'");
                }
                else {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$data[0] . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
                }		
		    }
		}
	}
	
	public function text_before(&$route, &$args){		
 	       $this->load->language('extension/so_theme/module/so_color_swatches_pro','',isset($this->request->cookie['language'])?$this->request->cookie['language']:$this->config->get('config_language'));
           $args['entry_default_image_color'] = $this->language->get('entry_default_image_color');	
		   $args['entry_color'] = $this->language->get('entry_color');	
		   $this->load->model('extension/so_theme/module/so_color_swatches_pro');
		   $args['color_swatch']   = $this->model_extension_so_theme_module_so_color_swatches_pro->getColorSwatch();
		   
		   $product_images = $this->model_catalog_product->getImages($args['product_id']);	  	
  
		   foreach ($product_images as $key => $product_image) {   	   
			    if(isset($product_image['default_of_color'])){
					$default_of_color = $product_image['default_of_color'];
			    }else{
				 	$default_of_color = array();
			    } 		   
				$args['product_images'][$key]['default_of_color'] = $default_of_color;   
		   }	   
	}	
	
	public function text_after(&$route, &$args, &$output){
     
	   $output = str_replace('<th>'.$args['entry_sort_order'].'</th>', '<th>'.$args['entry_default_image_color'].'</th><th>'.$args['entry_sort_order'].'</th>', $output);
	   
	   $output = str_replace('<td colspan="2"></td>', '<td colspan="3"></td>', $output);
	   
	   $output .= "
                <script type='text/javascript'><!--
                    jQuery(document).ready(function($){
                        $('.default-color-selector').change(function(){
                            var value = $(this).val();
                            $('.default-color-selector').not(this).each(function(){
                                if($(this).val()==value)
                                    $(this).val('0');
                            });
                        })
                    });
                //--></script>	   
	   ";
 
	   $image_row = 0;  
	   foreach($args['product_images'] as $product_image) {
		   
	     $yt ='
           <td class="text-right">
                    <select name="product_image['.$image_row.'][default_of_color]" class="default-color-selector">
                        <option value="0">----------</option>
                        '; 
						foreach($args['color_swatch'] as $color):
                            if ($color['option_value_id'] == $product_image['default_of_color']):
                                $yt .='<option value="'.$color['option_value_id'].'" selected="selected">'.$color['name'].'</option>';
                            else:
                                $yt .='<option value="'. $color['option_value_id'].'">'.$color['name'] .'</option>';
                            endif;
						endforeach;
                    $yt .='</select>
           </td><td><input type="text" name="product_image['.$image_row.'][sort_order]" value="'.$product_image['sort_order'].'" placeholder="'.$args['entry_sort_order'].'" class="form-control"/></td>';			   
		   
		   $output = str_replace('<td><input type="text" name="product_image['.$image_row.'][sort_order]" value="'.$product_image['sort_order'].'" placeholder="'.$args['entry_sort_order'].'" class="form-control"/></td>', $yt, $output);
		   $image_row = $image_row + 1;
	   }
	   
	   
	   
	   $yt1  = "html += '  </div></td>';
                html += '  <td class=\"text-start\">';
                html += '  <select name=\"product_image[".$image_row."][default_of_color]\" class=\"default-color-selector\">';
                html += '  <option value=\"0\">----------</option>'; ";
                foreach($args['color_swatch'] as $color):
       $yt1 .= "  html += '  <option value=\"".$color['option_value_id'] ."\">".$color['name'] ."</option>';";
                endforeach;
       $yt1 .= " html += '  </select>';
                html += '  </td>';
       ";	    
	   
	   $output = str_replace("html += '  </div></td>'",$yt1,$output);
	   

	}
}