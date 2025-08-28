<?php
namespace Opencart\Catalog\Controller\Extension\SoTheme\Module;
class SoHtmlContent extends \Opencart\System\Engine\Controller {	
	var $id_for_content=0;

	public function index($setting) {
	
		if (isset($setting['module_description'][$this->config->get('config_language_id')])) {
			$data['heading_title'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['title'], ENT_QUOTES, 'UTF-8');
			
			$data['class_suffix'] 			= $setting['class_suffix'];
			$data['store_layout'] 			= $setting['store_layout'];

			$data['countdown'] = '';
			if ($setting['show_countdown']) {
				$this->document->addScript('extension/so_theme/catalog/view/javascript/so_html_content/js/script.js');
				$this->document->addStyle('extension/so_theme/catalog/view/javascript/so_html_content/css/style.css');

				$data['countdown'] = $this->getCountdown($setting);
			}

			// caching
			$use_cache = (int)$setting['use_cache'];
			$cache_time = (int)$setting['cache_time'];
			$folder_cache = DIR_CACHE.'so/HtmlContent/';
			if(!file_exists($folder_cache))
				mkdir ($folder_cache, 0777, true);
			if (!class_exists('Cache_Lite'))
			    require_once (DIR_EXTENSION . 'so_theme/system/library/so/html_content/Cache_Lite/Lite.php');

			$options = array(
				'cacheDir' => $folder_cache,
				'lifeTime' => $cache_time
			);
			$Cache_Lite = new \Cache_Lite($options);
			if ($use_cache){
				$cacheid = (object)(md5( serialize(array($this->config->get('config_language'), $this->session->data['currency'], $setting))));
				$_data = $Cache_Lite->get($cacheid);	
				if (!$_data) {
					$data['html'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['description'], ENT_QUOTES, 'UTF-8');

					$_data = $this->load->view('extension/so_theme/module/so_html_content/'.$data['store_layout'], $data);
					$Cache_Lite->save($_data);
					return  $_data;
				} else {
					return  $_data;
				}
			}else{
				if(file_exists($folder_cache)) $Cache_Lite->_cleanDir($folder_cache);

				$data['html'] = html_entity_decode($setting['module_description'][$this->config->get('config_language_id')]['description'], ENT_QUOTES, 'UTF-8');

				return $this->load->view('extension/so_theme/module/so_html_content/'.$data['store_layout'], $data);
			}
			
		}
	}

	function getCountdown($setting) {
		$this->load->language('extension/so_theme/module/so_html_content','',$this->config->get('config_language_catalog'));

		$curent_value = array(
			"text_for_day" 			=> $this->language->get('text_for_day'),
			"text_for_hour"			=> $this->language->get('text_for_hour'),
			"text_for_minute"		=> $this->language->get('text_for_minute'),
			"text_for_second"		=> $this->language->get('text_for_second'),
			"start_time"			=> mktime (date("H"), date("i"), date("s"),date("n"), date("j"),date("Y")),
			"countdown_type"		=> isset($setting['countdown_type']) ? $setting['countdown_type'] : 'time',
			"end_date"				=> isset($setting['date_end']) ? date('d-m-Y H:i', strtotime($setting['date_end'])) : date('d-m-Y 23:59'),
			"end_time"				=> $setting['countdownday'].','.$setting['countdownhour'].','.$setting['countdownminute'],
			"content"				=> ''
		);

		$this->id_for_content++;
		
		if (isset($curent_value['countdown_type']) && $curent_value['countdown_type'] == 'date'){
			$end_date = explode(' ', $curent_value['end_date']);
			$end_date_only_date = explode('-',$end_date[0]);
			$end_date_hour = explode(':', $end_date[1]);
			$curent_time = mktime($end_date_hour['0'], $end_date_hour[1], 0, $end_date_only_date[1], $end_date_only_date[0], $end_date_only_date[2]);
			$time_diferent = $curent_time - mktime(date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));
		}else{
			$time_experit = explode(',', $curent_value['end_time']);
			$end_date = date('m-d-Y', ((int)$time_experit[0]*24*3600+(int)+$time_experit[1]*3600+(int)$time_experit[2]*60 + time()));
			$end_date_only_date = explode('-', $end_date);
			$curent_time = mktime ($time_experit[1], $time_experit[2], 0, $end_date_only_date[0], $end_date_only_date[1], $end_date_only_date[2]);
			$time_diferent = $curent_time - mktime (date("H"), date("i"), date("s"), date("n"), date("j"), date("Y"));
		}

		$day_left = (int)($time_diferent/(3600*24));
		$hours_left = (int)(($time_diferent-$day_left*24*3600)/(3600));
		$minuts_left = (int)(($time_diferent-$day_left*24*3600 - $hours_left*3600)/(60));
		$seconds_left = (int)(($time_diferent - $day_left*24*3600 - $hours_left*3600 - $minuts_left*60));	
		if (strlen("".$day_left)>0 && strlen("".$day_left)<2)
			$day_left='0'.$day_left;
		if (strlen("".$hours_left)>0 && strlen("".$hours_left)<2)
			$hours_left='0'.$hours_left;
		if (strlen("".$minuts_left)>0 && strlen("".$minuts_left)<2)
			$minuts_left='0'.$minuts_left;
		if (strlen("".$seconds_left)>0 && strlen("".$seconds_left)<2)
			$seconds_left='0'.$seconds_left;	

		$output_html = '';
		$output_html .= '<div class="so_content_countdown" id="main_countdown_'.$this->id_for_content.'">';
		$output_html.='<div class="so_countdown">
				<span class="element_container"><span class="days time_left">'.$day_left.'</span><span class="time_description">'.$curent_value['text_for_day'].'</span></span>
				<span class="element_container"><span class="hours time_left">'.$hours_left.'</span><span class="time_description">'.$curent_value['text_for_hour'].'</span></span>
				<span class="element_container"><span class="minutes time_left">'.$minuts_left.'</span><span class="time_description">'.$curent_value['text_for_minute'].'</span></span>
				<span class="element_container"><span class="seconds time_left">'.$seconds_left.'</span><span class="time_description">'.$curent_value['text_for_second'].'</span></span>
			</div>';
		$output_html .='</div>';
		$output_html.='<script>'.$this->countdown_javascript($curent_value).'</script>';

		return $output_html;
	}

	function countdown_javascript($parametrs_for_countedown) {
		$output_js='';
		
		if (isset($parametrs_for_countedown['countdown_type']) && $parametrs_for_countedown['countdown_type']=='date'){
			$end_date=explode(' ',$parametrs_for_countedown['end_date']);
			$end_date_only_date=explode('-',$end_date[0]);
			$end_date_hour=explode(':',$end_date[1]);
			$curent_time=mktime ($end_date_hour['0'], $end_date_hour[1],0,$end_date_only_date[1], $end_date_only_date[0],$end_date_only_date[2]);
			$time_diferent=$curent_time - mktime (date("H"), date("i"), date("s"),date("n"), date("j"),date("Y"));
		}else{
			$time_experit = explode(',', $parametrs_for_countedown['end_time']);
			$end_date = date('m-d-Y', ((int)$time_experit[0]*24*3600+(int)+$time_experit[1]*3600+(int)$time_experit[2]*60 + time()));
			$end_date_only_date = explode('-', $end_date);
			$curent_time = mktime ($time_experit[1], $time_experit[2], 0, $end_date_only_date[0], $end_date_only_date[1], $end_date_only_date[2]);
			$time_diferent = $curent_time - mktime (date("H"), date("i"), date("s"),date("n"), date("j"),date("Y"));			
		}	
		$day_left=(int)($time_diferent/(3600*24));
		$hours_left=(int)(($time_diferent-$day_left*24*3600)/(3600));
		$minuts_left=(int)(($time_diferent-$day_left*24*3600-$hours_left*3600)/(60));
		$seconds_left=(int)(($time_diferent-$day_left*24*3600-$hours_left*3600 - $minuts_left*60));	
		if(strlen("".$day_left)>0 && strlen("".$day_left)<2)
			$day_left='0'.$day_left;
		if(strlen("".$hours_left)>0 && strlen("".$hours_left)<2)
			$hours_left='0'.$hours_left;
		if(strlen("".$minuts_left)>0 && strlen("".$minuts_left)<2)
			$minuts_left='0'.$minuts_left;
		if(strlen("".$seconds_left)>0 && strlen("".$seconds_left)<2)
			$seconds_left='0'.$seconds_left;		 
		
		$output_js.="
			jQuery(document).ready(function(){
				".((($day_left<=0 && $hours_left<=0 && $minuts_left<=0 && $seconds_left<=0)) ? "jQuery('#main_countdown_".$this->id_for_content." .so_countdown').html('')" : "setInterval(function(){countdown_timer('main_countdown_".$this->id_for_content."');}, 1000)")."
			});
		";

		return $output_js;
	}
}
