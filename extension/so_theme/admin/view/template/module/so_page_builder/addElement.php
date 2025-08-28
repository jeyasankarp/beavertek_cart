<?php 
namespace Opencart\Admin\Controller\Extension\SoTheme\Module;
Class AddElementShortcodes{
	public static function yt_shortcodes_FormElement($shortcode,$name,$desc,$content,$language,$database){
		$contentParent = (is_array($content) ? $content[0]['cparent'] : array());
		$contentChild = (is_array($content) ? $content[0]['cchild'] : array());

		$multipleLanguage = '';

		foreach($database['languages'] as $language_)
		{
			$multipleLanguage_[] = $language_['language_id'];
		}
		$multipleLanguage = implode(',',$multipleLanguage_);
		$contentParent['language'] = $multipleLanguage;
		$shortcode = strtolower($shortcode);
		$html = '';
		require_once (DIR_EXTENSION.'so_theme/admin/view/template/module/so_page_builder/shortcodes/'.$shortcode.'/config.php');

		$name_shortcodes = 'YT_Shortcode_'.$shortcode.'_config';
		switch ($name_shortcodes) {
			case "YT_Shortcode_countdown_config":
			    $field_form = YT_Shortcode_countdown_config::get_config($language,$contentParent);
			break;
			case "YT_Shortcode_accordion_config":
			    $field_form = YT_Shortcode_accordion_config::get_config($language,$contentParent);
			break;
			case "YT_Shortcode_accordion_item_config":
			    $field_form = YT_Shortcode_accordion_item_config::get_config($language,$contentParent);
			break;		
			case "YT_Shortcode_content_slider_config":
			    $field_form = YT_Shortcode_content_slider_config::get_config($language,$contentParent);
			break;		
			case "YT_Shortcode_fancy_text_config":
			    $field_form = YT_Shortcode_fancy_text_config::get_config($language,$contentParent);
			break;	
			case "YT_Shortcode_gallery_config":
			    $field_form = YT_Shortcode_gallery_config::get_config($language,$contentParent);
			break;				
			case "YT_Shortcode_image_carousel_config":
			    $field_form = YT_Shortcode_image_carousel_config::get_config($language,$contentParent);
			break;						
			case "YT_Shortcode_points_config":
			    $field_form = YT_Shortcode_points_config::get_config($language,$contentParent);
			break;			
			case "YT_Shortcode_promotion_box_config":
			    $field_form = YT_Shortcode_promotion_box_config::get_config($language,$contentParent);
			break;			
			case "YT_Shortcode_tabs_config":
			    $field_form = YT_Shortcode_tabs_config::get_config($language,$contentParent);
			break;		
			case "YT_Shortcode_box_config":
			    $field_form = YT_Shortcode_box_config::get_config($language,$contentParent);
			break;	
			case "YT_Shortcode_feature_box_config":
			    $field_form = YT_Shortcode_feature_box_config::get_config($language,$contentParent);
			break;			
			case "YT_Shortcode_google_map_config":
			    $field_form = YT_Shortcode_google_map_config::get_config($language,$contentParent);
			break;			
			case "YT_Shortcode_lightbox_config":
			    $field_form = YT_Shortcode_lightbox_config::get_config($language,$contentParent);
			break;				
			case "YT_Shortcode_pricing_tables_config":
			    $field_form = YT_Shortcode_pricing_tables_config::get_config($language,$contentParent);
			break;		
			case "YT_Shortcode_skills_config":
			    $field_form = YT_Shortcode_skills_config::get_config($language,$contentParent);
			break;	
			case "YT_Shortcode_testimonial_config":
			    $field_form = YT_Shortcode_testimonial_config::get_config($language,$contentParent);
			break;		
			case "YT_Shortcode_contact_form_config":
			    $field_form = YT_Shortcode_contact_form_config::get_config($language,$contentParent);
			break;					
			case "YT_Shortcode_counter_config":
			    $field_form = YT_Shortcode_counter_config::get_config($language,$contentParent);
			break;			
			case "YT_Shortcode_flickr_config":
			    $field_form = YT_Shortcode_flickr_config::get_config($language,$contentParent);
			break;	
			case "YT_Shortcode_html_config":
			    $field_form = YT_Shortcode_html_config::get_config($language,$contentParent);
			break;		
			case "YT_Shortcode_livicon_config":
			    $field_form = YT_Shortcode_livicon_config::get_config($language,$contentParent);
			break;	
			case "YT_Shortcode_product_carousel_config":
			    $field_form = YT_Shortcode_product_carousel_config::get_config($language,$contentParent);
			break;	
			case "YT_Shortcode_social_icon_config":
			    $field_form = YT_Shortcode_social_icon_config::get_config($language,$contentParent);
			break;			
	
		}

		if($content == null){
			$html .= '<div id="yt-generator-breadcrumbs"><a href="javascript:void(0);" class="yt-generator-home" title="Click to return to the shortcodes list">'.$language->get('shortcode_all_shortcode').'</a> → <span>'.$name.'</span> <small class="alignright">'.$desc.'</small><div class="yt-generator-clear"></div></div>';
		}
		$html .= '<div class="yt_shortcodes_wrap_form_element">';
		$html .= '<div class="yt_shortcodes_parent_form_element" data-shortcodes="'.$shortcode.'">';
		foreach($field_form as $index => $field){
			if(!isset($field['type'])){
				$field['type'] ="text";
			}
			$data_group ='';
			if(isset($field['group'])){
				$data_group = 'data-group="'.$field['group'].'"';
			}else{
				$data_group = 'data-group="all"';
			}
			if(isset($field['child']) && count($field['child']) > 0){
				$class_field = 'field-group-'.(count($field['child'])+1);
				$html .='<div class="yt-generator-field-group" '.$data_group.'>';
					$html .='<div class="yt-generator-field-container '.$class_field.' yt-field-type-'.$field['type'].' yt-generator-skip" data-default="'.htmlentities($field['default'], ENT_QUOTES, 'UTF-8').'" data-name="'.$index.'" data-type="'.$field['type'].'" data-language="'.$multipleLanguage.'" '.$data_group.'>
					<h5>'.$field['name'].'</h5>';
					$html .= YT_Field_Shortcodes::formField($index,$database,$field);
					$html .='<div class="yt-generator-attr-desc">'.$field['desc'].'</div>';
					$html .= '</div>'; /* .yt-generator-field-container*/
					foreach($field['child'] as $index__ => $field__){
						if(!isset($field__['type'])){
							$field__['type'] ="text";
						}
						if(isset($field__['group'])){
							$data_group_son = 'data-group="'.$field__['group'].'"';
						}else{
							$data_group_son = 'data-group="all"';
						}
						$html .='<div class="yt-generator-field-container '.$class_field.' yt-field-type-'.$field__['type'].' yt-generator-skip" data-default="'.$field__['default'].'" data-name="'.$index__.'" data-type="'.$field__['type'].'" data-language="'.$multipleLanguage.'" '.$data_group_son.'>
						<h5>'.$field__['name'].'</h5>';
						$html .= YT_Field_Shortcodes::formField($index__,$field__,$database);
						$html .='<div class="yt-generator-attr-desc">'.$field__['desc'].'</div>';
						$html .= '</div>';/* .yt-generator-field-container*/
					}
				$html .='</div>';/* .yt-generator-field-group*/
			}else{
				$html .='<div class="yt-generator-field-container field-group-1 yt-field-type-'.$field['type'].' yt-generator-skip" data-default="'.htmlentities($field['default'], ENT_QUOTES, 'UTF-8').'" data-name="'.$index.'" data-type="'.$field['type'].'" data-language="'.$multipleLanguage.'" '.$data_group.'>
				<h5>'.$field['name'].'</h5>';
				$html .= YT_Field_Shortcodes::formField($index,$database,$field);
				if($index != "content"){
					$html .='<div class="yt-generator-attr-desc">'.$field['desc'].'</div>';
				}
				$html .= '</div>';/* .yt-generator-field-container*/
			}
		}
		
		$html .= '</div>';/* .yt_shortcodes_parent_form_element */
		
		/*Shortcode Item  */
		
		if(file_exists((DIR_EXTENSION.'so_page_builder/admin/view/template/module/so_page_builder/shortcodes/'.$shortcode.'_item/config.php'))){
			require_once (DIR_EXTENSION.'so_page_builder/admin/view/template/module/so_page_builder/shortcodes/'.$shortcode.'_item/config.php');
		
			$name_son = 'YT_Shortcode_'.$shortcode.'_item_config';
			
			$html .= '<input type="button" class="yt_btn yt_btn-info yt_shortcodes_add_element" value="'.$database['language']->get('shortcode_add').' '.ucfirst($name).' '.$database['language']->get('shortcode_item').'">';
			$html .= '	<div class="yt_shortcodes_son_form_element">';
			/* Edit => add N element*/
			if(is_array($content)){
				$item = 1;
				$database['dem'] = 1;
				foreach($content[0]['cchild'] as $child => $value){
					$html .= '<div class="yt_shortcodes_son_wrap">';
					$html .= '<h3 class="yt_shortcodes_son_button">'.ucfirst($name).' '.$database['language']->get('shortcode_item').' '.$item.'</h3>';
					$html .= '<div class="element-tool">';
					$html .= '<div data-icontitle="'.$database['language']->get('text_java_sortItem').'" data-placement="top"  class="element-sort element-icon"><i class="fas fa-arrows-alt"></i></div>';
					$html .= '<div data-icontitle="'.$database['language']->get('text_java_deleteItem').'" data-placement="top"  class="element-delete element-icon"><i class="fas fa-trash-alt"></i></div>';
					$html .= '</div>';/* End element-tool*/
					$html .= ' 	<div class="yt_shortcodes_wrap_form">';
					$value['language'] = $multipleLanguage;
					
					switch ($name_son) {
						case "YT_Shortcode_accordion_item_config":
							$field_form_son = YT_Shortcode_accordion_item_config::get_config($language,$value);
						break;	
						case "YT_Shortcode_content_slider_item_config":
							$field_form_son = YT_Shortcode_content_slider_item_config::get_config($language,$value);
						break;			
						case "YT_Shortcode_gallery_item_config":
							$field_form_son = YT_Shortcode_gallery_item_config::get_config($language,$value);
						break;		
						case "YT_Shortcode_points_item_config":
							$field_form_son = YT_Shortcode_points_item_config::get_config($language,$value);
						break;		
						case "YT_Shortcode_tabs_item_config":
							$field_form_son = YT_Shortcode_tabs_item_config::get_config($language,$value);
						break;		
						case "YT_Shortcode_pricing_tables_item_config":
							$field_form_son = YT_Shortcode_pricing_tables_item_config::get_config($language,$value);
						break;	
						case "YT_Shortcode_skills_item_config":
							$field_form_son = YT_Shortcode_skills_item_config::get_config($language,$value);
						break;	
						case "YT_Shortcode_testimonial_item_config":
							$field_form_son = YT_Shortcode_testimonial_item_config::get_config($language,$value);
						break;		
						case "YT_Shortcode_counter_item_config":
							$field_form_son = YT_Shortcode_counter_item_config::get_config($language,$value);
						break;				
						case "YT_Shortcode_social_icon_item_config":
							$field_form_son = YT_Shortcode_social_icon_item_config::get_config($language,$value);
						break;							
					}					
					
					
					foreach($field_form_son as $index => $field){
						if(!isset($field['type'])){
							$field['type'] ="text";
						}
						$html .= '<div class="yt-generator-field-container_son">';
							if(isset($field['child']) && count($field['child']) > 0){
								if(isset($field['group'])){
									$data_group = 'data-group="'.$field['group'].'"';
								}else{
									$data_group = 'data-group="all"';
								}
								$class_field1 = 'field-group-'.(count($field['child'])+1);
								$html .='<div class="yt-generator-field-group" '.$data_group.'>';
									$html .='<div class="yt-generator-field-container '.$class_field1.' yt-field-type-'.$field['type'].' yt-generator-skip" data-default="'.htmlentities($field['default'], ENT_QUOTES, 'UTF-8').'" data-name="'.$index.'" data-type="'.$field['type'].'" data-language="'.$multipleLanguage.'" '.$data_group.'>
								<h5>'.$field['name'].'</h5>';
									$html .= YT_Field_Shortcodes::formField($index,$database,$field);
									$html .='<div class="yt-generator-attr-desc">'.$field['desc'].'</div></div>';
									foreach($field['child'] as $index__ => $field__){
										if(!isset($field__['type']))
										{
											$field__['type'] = 'text';
										}
										$data_group ='';
										if(isset($field__['group'])){
											$data_group = 'data-group="'.$field__['group'].'"';
										}else{
											$data_group = 'data-group="all"';
										}
										$html .='<div class="yt-generator-field-container '.$class_field1.' yt-field-type-'.$field__['type'].' yt-generator-skip" data-default="'.$field__['default'].'" data-name="'.$index__.'" data-type="'.$field__['type'].'" data-language="'.$multipleLanguage.'" '.$data_group.'>
										<h5>'.$field__['name'].'</h5>';
										$html .= YT_Field_Shortcodes::formField($index__,$field__,$database);
										if($index != "content")
										{
											$html .='<div class="yt-generator-attr-desc">'.$field__['desc'].'</div>';
										}
										$html .= '</div>';
									}
								$html .='</div>';
							}else{
								$data_group ='';
								if(isset($field['group'])){
									$data_group = 'data-group="'.$field['group'].'"';
								}else{
									$data_group = 'data-group="all"';
								}
								$html .='<div class="yt-generator-field-container field-group-1 yt-field-type-'.$field['type'].' yt-generator-skip" data-default="'.htmlentities($field['default']).'" data-name="'.$index.'" data-type="'.$field['type'].'" data-language="'.$multipleLanguage.'" '.$data_group.'>
								<h5>'.$field['name'].'</h5>';
								$html .= YT_Field_Shortcodes::formField($index,$database,$field);
								if($index != "content")
								{
									$html .='<div class="yt-generator-attr-desc">'.$field['desc'].'</div>';
								}
								$html .= '</div>';/* .yt-generator-field-container */
							}
						$html .= '</div>';/* .yt-generator-field-container_son */
					}
					$html .= '</div>';/* .yt_shortcodes_wrap_form */
					$html .= '</div>';/* .yt_shortcodes_son_wrap */
					$item++;
					$database['dem']++;
				}/* End foreach child*/
			}else{
				$database['dem'] = 1;
				$html .= '	<div class="yt_shortcodes_son_wrap">';
				$html .= '<h3 class="yt_shortcodes_son_button" data-active="active">'.ucfirst($name).' '.$database['language']->get('shortcode_item').' '.'1</h3>';
				$html .= '<div class="element-tool">';
				$html .= '<div data-icontitle="'.$database['language']->get('text_java_sortItem').'" data-placement="top"  class="element-sort element-icon"><i class="fas fa-arrows-alt"></i></div>';
				$html .= '<div data-icontitle="'.$database['language']->get('text_java_deleteItem').'" data-placement="top"  class="element-delete element-icon"><i class="fas fa-trash-alt"></i></div>';
				$html .= '</div>';/* End element-tool*/
				$html .= ' 	<div class="yt_shortcodes_wrap_form">';
				$contentChild['language'] = $multipleLanguage;
				
				switch ($name_son) {
					case "YT_Shortcode_accordion_item_config":
						$field_form_son = YT_Shortcode_accordion_item_config::get_config($language,$contentChild);
					break;			
					case "YT_Shortcode_content_slider_item_config":
						$field_form_son = YT_Shortcode_content_slider_item_config::get_config($language,$contentChild);
					break;		
					case "YT_Shortcode_gallery_item_config":
						$field_form_son = YT_Shortcode_gallery_item_config::get_config($language,$contentChild);
					break;	
					case "YT_Shortcode_points_item_config":
						$field_form_son = YT_Shortcode_points_item_config::get_config($language,$contentChild);
					break;
					case "YT_Shortcode_tabs_item_config":
						$field_form_son = YT_Shortcode_tabs_item_config::get_config($language,$contentChild);
					break;			
					case "YT_Shortcode_pricing_tables_item_config":
						$field_form_son = YT_Shortcode_pricing_tables_item_config::get_config($language,$contentChild);
					break;	
					case "YT_Shortcode_skills_item_config":
						$field_form_son = YT_Shortcode_skills_item_config::get_config($language,$contentChild);
					break;		
					case "YT_Shortcode_testimonial_item_config":
						$field_form_son = YT_Shortcode_testimonial_item_config::get_config($language,$contentChild);
					break;	
					case "YT_Shortcode_counter_item_config":
						$field_form_son = YT_Shortcode_counter_item_config::get_config($language,$contentChild);
					break;						
					case "YT_Shortcode_social_icon_item_config":
						$field_form_son = YT_Shortcode_social_icon_item_config::get_config($language,$contentChild);
					break;					
					
				}				

			
				foreach($field_form_son as $index => $field){
					if(!isset($field['type'])){
						$field['type'] ="text";
					}
					$html .= '<div class="yt-generator-field-container_son">';
						if(isset($field['child']) && count($field['child']) > 0){
							if(isset($field['group'])){
								$data_group = 'data-group="'.$field['group'].'"';
							}else{
								$data_group = 'data-group="all"';
							}
							$class_field1 = 'field-group-'.(count($field['child'])+1);
							$html .='<div class="yt-generator-field-group" '.$data_group.'>';
								$html .='<div class="yt-generator-field-container '.$class_field1.' yt-field-type-'.$field['type'].' yt-generator-skip" data-default="'.htmlentities($field['default'], ENT_QUOTES, 'UTF-8').'" data-name="'.$index.'" data-type="'.$field['type'].'" data-language="'.$multipleLanguage.'" '.$data_group.'>
							<h5>'.$field['name'].'</h5>';
								$html .= YT_Field_Shortcodes::formField($index,$database,$field);
								$html .='<div class="yt-generator-attr-desc">'.$field['desc'].'</div></div>';
								foreach($field['child'] as $index__ => $field__){
									if(!isset($field__['type']))
									{
										$field__['type'] = 'text';
									}
									$data_group ='';
									if(isset($field__['group'])){
										$data_group = 'data-group="'.$field__['group'].'"';
									}else{
										$data_group = 'data-group="all"';
									}
									$html .='<div class="yt-generator-field-container '.$class_field1.' yt-field-type-'.$field__['type'].' yt-generator-skip" data-default="'.$field__['default'].'" data-name="'.$index__.'" data-type="'.$field__['type'].'" data-language="'.$multipleLanguage.'" '.$data_group.'>
									<h5>'.$field__['name'].'</h5>';
									$html .= YT_Field_Shortcodes::formField($index__,$field__,$database);
									if($index != "content")
									{
										$html .='<div class="yt-generator-attr-desc">'.$field__['desc'].'</div>';
									}
									$html .= '</div>';
								}
							$html .='</div>';
						}else{
							$data_group ='';
							if(isset($field['group'])){
								$data_group = 'data-group="'.$field['group'].'"';
							}else{
								$data_group = 'data-group="all"';
							}
							$html .='<div class="yt-generator-field-container field-group-1 yt-field-type-'.$field['type'].' yt-generator-skip" data-default="'.htmlentities($field['default']).'" data-name="'.$index.'" data-type="'.$field['type'].'" data-language="'.$multipleLanguage.'" '.$data_group.'>
							<h5>'.$field['name'].'</h5>';
							$html .= YT_Field_Shortcodes::formField($index,$database,$field);
							if($index != "content"){
								$html .='<div class="yt-generator-attr-desc">'.$field['desc'].'</div>';
							}
							$html .= '</div>';/* .yt-generator-field-container */
						}
					$html .= '</div>';/* .yt-generator-field-container_son */
					$database['dem']++;
				}
				$html .= '</div>';/* .yt_shortcodes_wrap_form */
				$html .= '</div>';/* .yt_shortcodes_son_wrap */
			}
		}/* file_exists Shortcode ITem*/
		$html .= '</div>';/* .yt_shortcodes_son_form_element */
		$html .= '</div>';/* .yt_shortcodes_wrap_form_element */
		$html .= '<script>
		jQuery(document).ready(function($){
			$(".yt_shortcodes_son_wrap").first().find("#yt-generator-attr-content").attr("id","yt-generator-attr-content-0");
			function updateNameElement(){
				var itemElement = $(".yt_shortcodes_son_button").length;
				var strShortcode = $(".yt_shortcodes_son_button").first().text();
				strShortcode = strShortcode.replace(/[0-9]/g, "");
				var i =1;
				$(".yt_shortcodes_son_button").each(function (){
					$(this).text(strShortcode+" "+i);
					i++;
				});
			}
			function deleteElement($this){
				$(".element-delete",$this).click( function(){
					if( confirm("Are you sure to delete ?") ){
						$(this).parent().parent(".yt_shortcodes_son_wrap").remove();
						if($(".yt_shortcodes_son_button").length == 1)
						{
							$(".element-delete.element-icon,.element-sort.element-icon").css("display","none");
						}
						updateNameElement();
					}
				});
			}
			
			var activeInput = $(".yt_shortcodes_son_button").length;
			if(activeInput > 1)
			{
				$("#yt-generator-attr-item_active").html("");
				for(var mi=1; mi<=activeInput;mi++)
				{
					$("#yt-generator-attr-item_active").append("<option value="+(mi)+">"+(mi)+"</option>");
				}	$(".yt_shortcodes_son_form_element").children().children(".yt_shortcodes_wrap_form").slideUp();
				$(".yt_shortcodes_son_form_element .yt_shortcodes_son_wrap:last-child .yt_shortcodes_son_button").attr("data-active","active");
				$(".yt_shortcodes_son_form_element .yt_shortcodes_son_wrap:last-child .yt_shortcodes_wrap_form").slideDown();
				$(".yt_shortcodes_son_form_element").sortable({
					handle:".element-sort",
				});
				$(".element-delete.element-icon,.element-sort.element-icon").css("display","block");
				$(".yt_shortcodes_son_wrap").each(function(){
					deleteElement($(this));
				});
			}else{
				$(".element-delete.element-icon,.element-sort.element-icon").css("display","none");
			}
			$(".yt_shortcodes_add_element").click(function(){
				$(".slider").html("");
				$(".slider").remove();
				var dem = $(".yt_shortcodes_son_button").length;
				$(".yt_shortcodes_son_button").attr("data-active","");
				$(".yt_shortcodes_son_button").parent().find(".yt_shortcodes_wrap_form").slideUp();
				$(".yt_shortcodes_son_form_element").append("<div class=\'yt_shortcodes_son_wrap\'></div>");
				$(".yt_shortcodes_son_form_element .yt_shortcodes_son_wrap").last().append("<h3 class=\"yt_shortcodes_son_button\" data-active=\"active\" \">'.ucfirst($name).' '.$database['language']->get('shortcode_item').' "+(dem+1)+"</h3><div class=\"element-tool\"><div data-icontitle=\"'.$database['language']->get('text_java_sortItem').'\" data-placement=\"top\"  class=\"element-sort element-icon\"><i class=\"fas fa-arrows-alt\"></i></div><div data-icontitle=\"'.$database['language']->get('text_java_deleteItem').'\" data-placement=\"top\"  class=\"element-delete element-icon\"><i class=\"fas fa-trash-alt\"></i></div></div> ");
				var html = $(".yt_shortcodes_wrap_form").html();
				$(".yt_shortcodes_son_form_element .yt_shortcodes_son_wrap").last().append("<div class=\'yt_shortcodes_wrap_form\'>"+html+"</div>");
				$(".yt_shortcodes_wrap_form").last().find("#yt-generator-attr-icon_size-slider").attr("id","yt-generator-attr-icon_size-"+(dem+1)+"-slider");
				$(".yt_shortcodes_wrap_form").last().find("#yt-generator-attr-icon_size").attr("name","icon_size-"+(dem+1)+"");
				$(".yt_shortcodes_wrap_form").last().find("#yt-generator-attr-icon_size").attr("id","yt-generator-attr-icon_size-"+(dem+1)+"");
				$(".yt_shortcodes_wrap_form").last().find("#yt-generator-attr-icon").attr("id","yt-generator-attr-icon-"+(dem+1)+"");
				$(".yt_shortcodes_son_wrap").last().find("#yt-generator-attr-content-0").attr("id","yt-generator-attr-content-"+dem+"");
				
				$(".yt_shortcodes_son_wrap").last().find("#yt-generator-attr-active").attr("id","yt-generator-attr-active-"+dem+"");
				$(".yt_shortcodes_son_wrap").last().find("#yt-generator-attr-active").attr("name","active-"+dem+"");
				$(".yt_shortcodes_wrap_form").last().find(".thumb-image").attr("id","thumb-image-"+(dem+1)+"");
				$(".yt_shortcodes_wrap_form").last().find(".thumb-image").attr("data-oc-thumb","#thumb-image"+(dem+1)+"");			
				$(".yt_shortcodes_wrap_form").last().find(".img-class").attr("id","thumb-image"+(dem+1)+"");
				$(".yt_shortcodes_wrap_form").last().find(".uploadimage").attr("id","uploadimage"+(dem+1)+"");
				$(".yt_shortcodes_wrap_form").last().find(".btn-primary").attr("data-oc-target","#uploadimage"+(dem+1)+"");
				$(".yt_shortcodes_wrap_form").last().find(".btn-warning").attr("data-oc-target","#uploadimage"+(dem+1)+"");
				$(".yt_shortcodes_wrap_form").last().find(".btn-primary").attr("data-oc-thumb","#thumb-image"+(dem+1)+"");
				$(".yt_shortcodes_wrap_form").last().find(".btn-warning").attr("data-oc-thumb","#thumb-image"+(dem+1)+"");		
                $(".yt_shortcodes_wrap_form").last().find(".thumb-image").attr("data-oc-target","#uploadimage"+(dem+1)+"");		
				
				$(".yt_shortcodes_wrap_form").last().find(".tab-pane").each(function(){
					$(this).find(".note-editor").last().remove(); /*remove note-editor repeat */
				});
				$("#yt-generator-attr-item_active").html("");
				for(var mi=1; mi<=dem+1;mi++)
				{
					$("#yt-generator-attr-item_active").append("<option value="+(mi)+">"+(mi)+"</option>");
				}
				$(".yt_shortcodes_son_form_element").sortable({
					handle:".element-sort",
				});	
				deleteElement($(".yt_shortcodes_son_wrap").last());
				$(".element-delete.element-icon,.element-sort.element-icon").css("display","block");
			});	
		});</script>';
		return $html;
	}	
}
?>