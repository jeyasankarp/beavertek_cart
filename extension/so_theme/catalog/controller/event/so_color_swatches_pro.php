<?php
namespace Opencart\Catalog\Controller\Extension\SoTheme\Event;
class SoColorSwatchesPro extends \Opencart\System\Engine\Controller {
	public function controller_before(&$route, &$data){
		$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_color_swatches_pro/css/style.css');
	}
	public function controller_after(string &$route, array &$args, mixed &$output): void {}
	
	public function model_before(&$route, &$data){}
	
	public function model_after(string &$route, array &$data, mixed &$output): void {}
	
	public function text_before(&$route, &$args){	

        $product_info = $this->model_catalog_product->getProduct($args['product_id']);
	    
		if($product_info) {
			$this->load->model('extension/so_theme/module/so_color_swatches_pro');
				
			$args['option_data'] = array();
			if ($this->config->get('module_so_color_swatches_pro_status') && $this->config->get('module_so_color_swatches_pro_enable_product_page')) {
						$args['width_product_page'] = $this->config->get('module_so_color_swatches_pro_width_product_page');
						if ($args['width_product_page'] == 0) {
							$args['width_product_page'] = 20;
						}
						$args['height_product_page'] = $this->config->get('module_so_color_swatches_pro_height_product_page');
						if ($args['height_product_page'] == 0) {
							$args['height_product_page'] = 20;
						}
						$args['colorswatch_type'] = $this->config->get('module_so_color_swatches_pro_type');
	 
						$option_selected = $this->config->get('module_so_color_swatches_pro_option');
						$product_option = $this->model_extension_so_theme_module_so_color_swatches_pro->getProductOptionsByOptionId($args['product_id'], $option_selected);
						if ($product_option) {
							$args['product_option_id'] = $product_option['product_option_id'];
						}
						$args['option_selected'] = $option_selected;
		
						$options = $this->model_extension_so_theme_module_so_color_swatches_pro->getProductOptions($args['product_id']);
							
						foreach ($options as $option) {
							$product_option_value_data = array();					
							foreach ($option['product_option_value'] as $option_value) {
								if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
									$p_image = $this->model_extension_so_theme_module_so_color_swatches_pro->getProductImages($args['product_id'], $option_value['option_value_id']);
									
									if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
										$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
									} else {
										$price = false;
									}								
									
									
									if (isset($p_image['image']) && $p_image['image']) {
										$pimage = $this->model_tool_image->resize($p_image['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
										$p_thumbimage = $this->model_tool_image->resize($p_image['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
									} else {
										$pimage = '';
										$p_thumbimage = '';
									}
									if (isset($p_image['product_image_id']) && $p_image['product_image_id']) {
										$product_image_id = $p_image['product_image_id'];
									}
									else {
										$product_image_id = '';
									}
									$product_option_value_data[] = array(
										'product_option_value_id' => $option_value['product_option_value_id'],
										'option_value_id'         => $option_value['option_value_id'],
										'name'                    => $option_value['name'],
										'image'                   => $this->model_tool_image->resize($option_value['image'], $args['width_product_page'], $args['height_product_page']),
										'price'                   => $price,
										'price_prefix'            => $option_value['price_prefix'],
										'color_image'             => $pimage,
										'color_thumb_image'       => $p_thumbimage,
										'product_image_id'        => $product_image_id
									);
								}
							}
							$args['option_data'][] = array(
								'product_option_id'    => $option['product_option_id'],
								'product_option_value' => $product_option_value_data,
								'option_id'            => $option['option_id'],
								'name'                 => $option['name'],
								'type'                 => $option['type'],
								'value'                => $option['value'],
								'required'             => $option['required']
							);
						}
			}
			$args['option_data'] = array_shift($args['option_data']);				
		}
				
	}	
	
	public function text_after(&$route, &$args, &$output){}

	public function text_thumb_after(&$route, &$args, &$output){	
		$yt ='<div class="content">';
        if($args['option_data'] && isset($args['option_data']['product_option_value']) && $args['option_data']['product_option_value']):
                $yt .='<ul id="so-colorswatch-selector-thumb'.$args['product_id'].'" class="so-colorswatch-productpage-icons text-center">';
					foreach($args['option_data']['product_option_value'] as $option_value) {
					$yt .='<li class="option-item">
									<a class="" 
										data-product-option-value-id="'.$option_value['product_option_value_id'].'" 
										data-option-value-id="'.$option_value['option_value_id'].'" 
										data-color-image="'.$option_value['color_image'].'" 
										data-color-thumb-image="'.$option_value['color_thumb_image'].'" 
										style="width: '.$args['width_product_page'].'px; height: '.$args['height_product_page'].'px; background-image: url('.$option_value['image'].')">
									</a>
								</li>';
					}
               $yt .='<li class="selected-option"><span></span></li>
                </ul>
				<script type="text/javascript">
				
					var ProductOptionId = '.$args["product_option_id"].';
					var $window_width = $(window).width();
					jQuery(document).ready(function($) {							
							var default_image = $(".product-thumb'.$args['product_id'].' .image img").attr("src");

                            $("#input-option'.$args["product_option_id"].'").parent().hide();
                            $("#input-option'.$args["product_option_id"].' option").each(function(){
                              var text = $(this).text().replace(/\s{2,}/g, " ");
                              var val = $(this).attr("value");
                              $("#so-colorswatch-selector-thumb'.$args['product_id'].' li a").each(function(index, el){
                                if($(el).data("product-option-value-id")== val){
                                  $(el).attr("title", text);
                                }
                              })
                            });';
				if ($args['colorswatch_type'] == 'click') {
                $yt .='     $("#so-colorswatch-selector-thumb'.$args['product_id'].' li.option-item").click(function(e){
                            e.preventDefault();
                            var option_value_id = $(this).children("a").data("product-option-value-id");
                            var option_id = $(this).children("a").data("option-value-id");	
                            if ($(this).hasClass("checked")) {
                                        $("#so-colorswatch-selector-thumb'.$args['product_id'].' li.option-item").removeClass("checked");
                                        $(this).removeClass("checked");
                                        $("#input-option'.$args["product_option_id"].'").val("").trigger("change");
                                        $("#so-colorswatch-selector-thumb'.$args['product_id'].' li.selected-option > span").html("");

										$(".product-thumb'.$args['product_id'].' .image img").attr("src", default_image);
	
                            }
                            else {
                                        $("#so-colorswatch-selector-thumb'.$args['product_id'].' li.option-item").removeClass("checked");
                                        $(this).removeClass("checked").addClass("checked");
                                        $("#input-option'.$args["product_option_id"].'").val(option_value_id).trigger("change");
                                        $("#so-colorswatch-selector-thumb'.$args['product_id'].' li.selected-option > span").html($(this).children("a").attr("title"));
                                        
                                        if ($(this).children("a").data("color-image") != "") {
                                            
											$(".product-thumb'.$args['product_id'].' .image img").attr("src", $(this).children("a").data("color-image"));

                                        }
                                        else {
											$(".product-thumb'.$args['product_id'].' .image img").attr("src", default_image);
                                        }

                                        $("#thumb-slider  a.thumbnail").removeClass("active");
                                    }
                                })';							
                } else {
					$yt .='
                                if ($window_width > 1199) {
                                    $("#so-colorswatch-selector-thumb'.$args['product_id'].' li.option-item").hover(function(e){
                                        e.preventDefault();
                                        var option_value_id = $(this).children("a").data("product-option-value-id");
                                        var option_id = $(this).children("a").data("option-value-id");
                                        
                                        $("#so-colorswatch-selector-thumb'.$args['product_id'].' li.option-item").removeClass("checked");
                                        if ($(this).hasClass("checked")) {
                                            $(this).removeClass("checked");
                                            $("#input-option'.$args["product_option_id"].'").val("").trigger("change");
											
											$(".product-thumb'.$args['product_id'].' .image img").attr("src", default_image);

                                        }
                                        else {
                                            $(this).removeClass("checked").addClass("checked");
                                            $("#input-option'.$args["product_option_id"].'").val(option_value_id).trigger("change");
                                            $("#so-colorswatch-selector-thumb'.$args['product_id'].' li.selected-option > span").html($(this).children("a").attr("title"));
                                            
                                            if ($(this).children("a").data("color-image") != "") {
												$(".product-thumb'.$args['product_id'].' .image img").attr("src", $(this).children("a").data("color-image"));
	   
	                                        }
	                                        else {
												$(".product-thumb'.$args['product_id'].' .image img").attr("src", default_image);
	                                        }
	                                         $("#thumb-slider a.thumbnail").removeClass("active");
                                        }
                                    });
                                }
                                else {
                                    $(document).on("click", "#so-colorswatch-selector-thumb'.$args['product_id'].' li.option-item", function(e){
                                        e.preventDefault();
                                        var option_value_id = $(this).children("a").data("product-option-value-id");
                                        var option_id = $(this).children("a").data("option-value-id");
            
                                        $("#so-colorswatch-selector-thumb'.$args['product_id'].' li.option-item").removeClass("checked");
                                        if ($(this).hasClass("checked")) {
                                            $(this).removeClass("checked");
                                            $("#input-option'.$args["product_option_id"].'").val("").trigger("change");
											
											$(".product-thumb'.$args['product_id'].' .image img").attr("src", default_image);
											
                                            $(".large-image > img").attr("src", default_image);
                                           	$(".large-image > img").attr("data-src", default_data_src);
											$(".large-image > img").attr("data-zoom-image", default_zoom_image);

											$(".zoomContainer").remove();
											zoomImage.removeData("elevateZoom");
											zoomImage.attr("src", default_image);
											zoomImage.data("zoom-image", default_zoom_image);
											zoomImage.elevateZoom(zoomConfig);
											
                                        }
                                        else {
                                            $(this).removeClass("checked").addClass("checked");
                                            $("#input-option'.$args["product_option_id"].'").val(option_value_id).trigger("change");
                                            $("#so-colorswatch-selector-thumb'.$args['product_id'].' li.selected-option > span").html($(this).children("a").attr("title"));
                                            
                                            if ($(this).children("a").data("color-image") != "") {
												
												$(".product-thumb'.$args['product_id'].' .image img").attr("src", $(this).children("a").data("color-image"));
												
	                                            $(".large-image img").attr("src", $(this).children("a").data("color-image"));

												$(".zoomContainer").remove();
												zoomImage.removeData("elevateZoom");
												zoomImage.attr("src", $(this).children("a").data("color-image"));
												zoomImage.data("zoom-image", $(this).children("a").data("color-image"));
												zoomImage.elevateZoom(zoomConfig);
												
	                                        }
	                                        else {
												
												$(".product-thumb'.$args['product_id'].' .image img").attr("src", default_image);
												
	                                            $(".large-image > img").attr("src", default_image);
												$(".large-image > img").attr("data-src", default_data_src);
												$(".large-image > img").attr("data-zoom-image", default_zoom_image);

												$(".zoomContainer").remove();
												zoomImage.removeData("elevateZoom");
												zoomImage.attr("src", default_image);
												zoomImage.data("zoom-image", default_zoom_image);
												zoomImage.elevateZoom(zoomConfig);
												
	                                        }
	                                         $("#thumb-slider a.thumbnail").removeClass("active");
                                        }
                                    })
                                }					
					';
				}
							
				$yt .='	})	
				</script>
				';
				endif;
		
		$output = str_replace('<div class="content">',$yt,$output);
		
		$output = str_replace('<div class="product-thumb">','<div class="product-thumb product-thumb'.$args['product_id'].'">',$output);
	}
}