<?php
namespace Opencart\Catalog\Controller\Extension\SoTheme\Event;
class SoFilterShopBy extends \Opencart\System\Engine\Controller {

	public function text_after(&$route, &$args, &$output){
          foreach($args['products'] as $product) {
			$yt ='<div class="col">'.$product.'</div>';  
            $yt1 ='<div class="col product-layout">'.$product.'</div>';
			$output = str_replace($yt,$yt1,$output);
          }

	}
}