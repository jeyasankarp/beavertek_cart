<?php
namespace Opencart\Catalog\Controller\Extension\SoTheme\Event;

class SoSociallogin extends \Opencart\System\Engine\Controller {
	
	public function so_login_before(&$route, &$data) {
                $this->load->model('setting/setting');
                $this->load->model('tool/image');
                $setting = $this->model_setting_setting->getSetting('so_sociallogin');
                if (isset($setting['so_sociallogin_enable']) && $setting['so_sociallogin_enable'] && $this->config->get('so_sociallogin_enable')) {
						if(isset($this->session->data['route']))
						{
							$location = $this->url->link($this->session->data['route'], "", 'SSL');
						}
						else
						{
							$location = $this->url->link("account/account", "", 'SSL');
						}
						
						/* Facebook Library */
						require_once (DIR_EXTENSION.'so_theme/system/library/so_social/Facebook/autoload.php');
						
						/* Facebook  Login link code */
						$fb = new \Facebook\Facebook ([
							'app_id'                    => $setting['so_sociallogin_fbapikey'], 
							'app_secret'                => $setting['so_sociallogin_fbsecretapi'],
							'default_graph_version'     => 'v2.4',
						]);

						$helper = $fb->getRedirectLoginHelper();
                        if (isset($_GET['state'])) {
                            $helper->getPersistentDataHandler()->set('state', $_GET['state']);
                        }
						try {
							$accessToken = $helper->getAccessToken();
						} catch(Facebook\Exceptions\FacebookResponseException $e) {
							// When Graph returns an error
							echo 'Graph returned an error: ' . $e->getMessage();
							exit;
						} catch(Facebook\Exceptions\FacebookSDKException $e) {
							// When validation fails or other local issues
							echo 'Facebook SDK returned an error: ' . $e->getMessage();
							exit;
						}       
						
						$data['fblink'] = $helper->getLoginUrl($this->url->link('extension/so_theme/module/so_sociallogin.FacebookLogin', '', 'SSL'), array('public_profile','email'));
						/* Facebook  Login link code */
				
						/* Google Libery file inculde */
						require_once DIR_EXTENSION.'so_theme/system/library/so_social/src/Google_Client.php';
						require_once DIR_EXTENSION.'so_theme/system/library/so_social/src/contrib/Google_Oauth2Service.php';
						
						/* Google Login link code */
						$gClient = new \Google_Client();
						$gClient->setApplicationName($setting['so_sociallogin_googletitle']);
						$gClient->setClientId($setting['so_sociallogin_googleapikey']);
						$gClient->setClientSecret($setting['so_sociallogin_googlesecretapi']);
						$gClient->setRedirectUri($this->url->link('extension/so_theme/module/so_sociallogin.GoogleLogin', '', 'SSL'));
						$google_oauthV2 = new \Google_Oauth2Service($gClient);
						$data['googlelink']  = $gClient->createAuthUrl();
						
						/* Twitter Login */                     
						$data['twitlink'] =  $this->url->link('extension/so_theme/module/so_sociallogin.TwitterLogin', '', 'SSL');
						
						/* Linkedin Login */
						$data['linkdinlink'] = $this->url->link('extension/so_theme/module/so_sociallogin.LinkedinLogin', '', 'SSL');
						
						/* Get Image */
						$sociallogin_width = 130;
						$sociallogin_height = 35;
						if (isset($setting['so_sociallogin_width']) && is_numeric($setting['so_sociallogin_width'])) {
							$sociallogin_width = $setting['so_sociallogin_width'];
						}
						if (isset($setting['so_sociallogin_height']) && is_numeric($setting['so_sociallogin_height'])) {
							$sociallogin_height = $setting['so_sociallogin_height'];
						}
						if ($setting['so_sociallogin_fbimage']) {
							$fbicon = $this->model_tool_image->resize($setting['so_sociallogin_fbimage'], $sociallogin_width, $sociallogin_height);
						} else {
							$fbicon = $this->model_tool_image->resize('placeholder.png', $sociallogin_width, $sociallogin_height);
						}
							
						if ($setting['so_sociallogin_twitimage']) {
							$twiticon = $this->model_tool_image->resize($setting['so_sociallogin_twitimage'], $sociallogin_width, $sociallogin_height);
						} else {
							$twiticon = $this->model_tool_image->resize('placeholder.png', $sociallogin_width, $sociallogin_height);
						}
							
						if ($setting['so_sociallogin_googleimage']) {
							$googleicon = $this->model_tool_image->resize($setting['so_sociallogin_googleimage'], $sociallogin_width, $sociallogin_height);
						} else {
							$googleicon = $this->model_tool_image->resize('placeholder.png', $sociallogin_width, $sociallogin_height);
						}
						
						if ($setting['so_sociallogin_linkdinimage']) {
							$linkdinicon = $this->model_tool_image->resize($setting['so_sociallogin_linkdinimage'], $sociallogin_width, $sociallogin_height);
						} else {
							$linkdinicon = $this->model_tool_image->resize('placeholder.png', $sociallogin_width, $sociallogin_height);
						}
						
						$data['iconwidth']  = $sociallogin_width;
						$data['iconheight'] = $sociallogin_height;
						$data['status']     = $setting['so_sociallogin_enable'];
						$data['fbimage']    = $fbicon;
						$data['twitimage']  = $twiticon;
						$data['googleimage'] = $googleicon;
						$data['linkdinimage'] = $linkdinicon;
						
						$data['setting'] = $setting;	
	                }
	}
    public function so_login_after(&$route, &$data, &$output){

        
            $yt1 = '<button type="submit" class="btn btn-primary">'.$data['button_login'].'</button>';

		    if ($data['setting'] && $data['setting']['so_sociallogin_enable']) {
             $yt =  '<button type="submit" class="btn btn-primary">'.$data['button_login'].'</button>
			         <column id="column-login" class="col-sm-8 pull-right">
                        <div class="row">';
                            if ($data['error_warning']) {
             $yt .=             '<div class="alert alert-warning"><i class="fa fa-check-circle"></i>'.$data['error_warning'].'</div>';
                            }
             $yt .=     '<div class="social_login pull-right" id="so_sociallogin">';
                                if ($data['setting']['so_sociallogin_fbstatus']) {
                                    if ($data['setting']['so_sociallogin_button'] == 'image') {
             $yt .=                    '<a class="social-icon"  href="'.$data['fblink'].'">
                                            <img class="img-responsive" src="'.$data['fbimage'].'" 
                                                data-toggle="tooltip" alt="'.$data['setting.so_sociallogin_fbtitle'].'" 
                                                title="'.$data['setting']['so_sociallogin_fbtitle'].'"
                                            />
                                        </a>';
								    } else {
             $yt .=     '<a href="'.$data['fblink'].'" class="btn btn-social-icon btn-sm btn-facebook"><i class="fa fab fa-facebook fa-fw" aria-hidden="true"></i></a>';
                                    }
                                }
                                
                                if ($data['setting']['so_sociallogin_twitstatus']) {
                                    if ($data['setting']['so_sociallogin_button'] == 'image' ) {
                                $yt .= '<a class="social-icon"  href="'.$data['twitlink'].'">
                                            <img class="img-responsive" src="'.$data['twitimage'].'" 
                                                data-toggle="tooltip" alt="'.$data['setting']['so_sociallogin_twittertitle'].'" 
                                                title="'.$data['setting']['so_sociallogin_twittertitle'].'"
                                            />
                                        </a>';
								    } else {
                                 $yt .= '<a href="'.$data['twitlink'].'" class="btn btn-social-icon btn-sm btn-twitter"><i class="fa fab fa-twitter fa-fw" aria-hidden="true"></i></a>';
								    }
                                }
                                
                                if ($data['setting']['so_sociallogin_googlestatus']) {
                                    if ($data['setting']['so_sociallogin_button'] == 'image') {
                                $yt .=  '<a class="social-icon" href="'.$data['googlelink'].'">
                                            <img class="img-responsive" src="'.$data['googleimage'].'" 
                                                data-toggle="tooltip" alt="'.$data['setting']['so_sociallogin_googletitle'].'" 
                                                title="'.$data['setting']['so_sociallogin_googletitle'].'" 
                                            />
                                        </a>';
                                    } else {
                                $yt .=  '<a href="'.$data['googlelink'].'" class="btn btn-social-icon btn-sm btn-google-plus"><i class="fa fa-google fa-fw" aria-hidden="true"></i></a>';
                                    }
		                        }
                                
                                if ($data['setting']['so_sociallogin_linkstatus']) {
                                      if ($data['setting']['so_sociallogin_button'] == 'image') {
                                $yt .= '<a class="social-icon" href="'.$data['linkdinlink'].'">
                                            <img class="img-responsive" src="'.$data['linkdinimage'].'" 
                                                data-toggle="tooltip" alt="'.$data['setting']['so_sociallogin_linkedintitle'].'" 
                                                title="'.$data['setting']['so_sociallogin_linkedintitle'].'"
                                            />
                                        </a>';
                                      } else {
                                 $yt .= '<a href="'.$data['linkdinlink'].'" class="btn btn-social-icon btn-sm btn-linkdin"><i class="fa fa-linkedin fa-fw" aria-hidden="true"></i></a>';
								      }  
                                }
               $yt .=   '</div>
                        </div>
                    </column>';
		       }
		$output = str_replace($yt1,$yt,$output);
	}
	
	public function so_sociallogin_header_before(&$route, &$data) {
                
                $this->load->model('setting/setting');
                $this->load->model('tool/image');
                $setting = $this->model_setting_setting->getSetting('so_sociallogin');
                
                if (isset($setting['so_sociallogin_enable']) && $setting['so_sociallogin_enable'] && $this->config->get('so_sociallogin_enable')) {
                    if(isset($this->session->data['route']))
                    {
                        $location = $this->url->link($this->session->data['route'], "", 'SSL');
                    }
                    else
                    {
                        $location = $this->url->link("account/account", "", 'SSL');
                    }
                
                    /* Facebook Library */
                    require_once (DIR_EXTENSION.'so_theme/system/library/so_social/Facebook/autoload.php');
                    
                    $fb = new \Facebook\Facebook ([
                        'app_id'                    => $setting['so_sociallogin_fbapikey'], 
                        'app_secret'                => $setting['so_sociallogin_fbsecretapi'],
                        'default_graph_version'     => 'v2.4',
                    ]);

                    $helper = $fb->getRedirectLoginHelper();
                    if (isset($_GET['state'])) {
                        $helper->getPersistentDataHandler()->set('state', $_GET['state']);
                    }
                    try {
                        $accessToken = $helper->getAccessToken();
                    } catch(Facebook\Exceptions\FacebookResponseException $e) {
                        // When Graph returns an error
                        //echo 'Graph returned an error: ' . $e->getMessage();
                        //exit;
                    } catch(Facebook\Exceptions\FacebookSDKException $e) {
                        // When validation fails or other local issues
                        //echo 'Facebook SDK returned an error: ' . $e->getMessage();
                        //exit;
                    }       
                    
                    $data['fblink'] = $helper->getLoginUrl($this->url->link('extension/so_theme/module/so_sociallogin.FacebookLogin', '', 'SSL'), array('public_profile','email'));
                    /* Facebook  Login link code */
            
                    /* Google Libery file inculde */
                    require_once DIR_EXTENSION.'so_theme/system/library/so_social/src/Google_Client.php';
					require_once DIR_EXTENSION.'so_theme/system/library/so_social/src/contrib/Google_Oauth2Service.php';
                    
                    /* Google Login link code */
                    $gClient = new \Google_Client();
                    $gClient->setApplicationName($setting['so_sociallogin_googletitle']);
                    $gClient->setClientId($setting['so_sociallogin_googleapikey']);
                    $gClient->setClientSecret($setting['so_sociallogin_googlesecretapi']);
                    $gClient->setRedirectUri($this->url->link('extension/so_theme/module/so_sociallogin.GoogleLogin', '', 'SSL'));
                    $google_oauthV2 = new \Google_Oauth2Service($gClient);
                    $data['googlelink']  = $gClient->createAuthUrl();
                    
                    /* Twitter Login */                     
                    $data['twitlink'] =  $this->url->link('extension/so_theme/module/so_sociallogin.TwitterLogin', '', 'SSL');
                    
                    /* Linkedin Login */
                    $data['linkdinlink'] = $this->url->link('extension/so_theme/module/so_sociallogin.LinkedinLogin', '', 'SSL');
                    
                    /* Get Image */
                    $sociallogin_width = 130;
                    $sociallogin_height = 35;
                    if (isset($setting['so_sociallogin_width']) && is_numeric($setting['so_sociallogin_width'])) {
                        $sociallogin_width = $setting['so_sociallogin_width'];
                    }
                    if (isset($setting['so_sociallogin_height']) && is_numeric($setting['so_sociallogin_height'])) {
                        $sociallogin_height = $setting['so_sociallogin_height'];
                    }
                    if ($setting['so_sociallogin_fbimage']) {
                        $fbicon = $this->model_tool_image->resize($setting['so_sociallogin_fbimage'], $sociallogin_width, $sociallogin_height);
                    } else {
                        $fbicon = $this->model_tool_image->resize('placeholder.png', $sociallogin_width, $sociallogin_height);
                    }
                        
                    if ($setting['so_sociallogin_twitimage']) {
                        $twiticon = $this->model_tool_image->resize($setting['so_sociallogin_twitimage'], $sociallogin_width, $sociallogin_height);
                    } else {
                        $twiticon = $this->model_tool_image->resize('placeholder.png', $sociallogin_width, $sociallogin_height);
                    }
                        
                    if ($setting['so_sociallogin_googleimage']) {
                        $googleicon = $this->model_tool_image->resize($setting['so_sociallogin_googleimage'], $sociallogin_width, $sociallogin_height);
                    } else {
                        $googleicon = $this->model_tool_image->resize('placeholder.png', $sociallogin_width, $sociallogin_height);
                    }
                    
                    if ($setting['so_sociallogin_linkdinimage']) {
                        $linkdinicon = $this->model_tool_image->resize($setting['so_sociallogin_linkdinimage'], $sociallogin_width, $sociallogin_height);
                    } else {
                        $linkdinicon = $this->model_tool_image->resize('placeholder.png', $sociallogin_width, $sociallogin_height);
                    }
                    
                    $data['iconwidth']  = $sociallogin_width;
                    $data['iconheight'] = $sociallogin_height;
                    $data['status']     = $setting['so_sociallogin_enable'];
                    $data['fbimage']    = $fbicon;
                    $data['twitimage']  = $twiticon;
                    $data['googleimage'] = $googleicon;
                    $data['linkdinimage'] = $linkdinicon;
                    
                    $data['setting'] = $setting;
    
                    $this->load->language('extension/so_theme/module/so_sociallogin','',$this->config->get('config_language_catalog'));
                    $data['text_colregister']   = $this->language->get('text_colregister');
                    $data['text_create_account']   = $this->language->get('text_create_account');
                    $data['link_forgot_password']   = $this->url->link('account/forgotten', '', true);
                    $data['text_forgot_password']   = $this->language->get('text_forgot_password');
                    $data['text_title_popuplogin']   = $this->language->get('text_title_popuplogin');
                    $data['text_title_login_with_social']   = $this->language->get('text_title_login_with_social');
                }		
	}
	public function so_sociallogin_header_after(&$route, &$data, &$output){
	    if(!isset($this->session->data['login_token'])) {
	         $this->session->data['login_token'] = substr(bin2hex(openssl_random_pseudo_bytes(26)), 0, 26);
	    }
	    $data['login_custom'] =$this->url->link('account/login|login', 'language=' . $this->config->get('config_language') . '&login_token=' . $this->session->data['login_token']);
        if (!$this->customer->isLogged() && $data['setting'] && $data['setting']['so_sociallogin_enable'] && $data['setting']['so_sociallogin_popuplogin']) {
                $yt =  '<div class="modal fade in" id="so_sociallogin" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog  block-popup-login" role="document">
                        <div class=" modal-content">
                            <a href="javascript:void(0)" title="Close" class="close close-login fa fa-times-circle" data-bs-dismiss="modal"></a>
                            <div class="tt_popup_login"><strong>'.$data['text_title_popuplogin'].'</strong></div>
                            <div class="block-content">
                                <div class=" col-reg registered-account">
                                    <div class="block-content">
                                        <form class="form form-login" action="'.$data['login_custom'].'" method="post" id="login-form" data-oc-toggle="ajax">
                                            <fieldset class="fieldset login" data-hasrequired="* Required Fields">
                                                <div class="field email required email-input">
                                                    <div class="control">
                                                        <input name="email" value="" autocomplete="off" id="email" type="email" class="input-text" title="Email" placeholder="E-mail Address" />
                                                    </div>
                                                </div>
                                                <div class="field password required pass-input">
                                                    <div class="control">
                                                        <input name="password" type="password" autocomplete="off" class="input-text" id="pass" title="Password" placeholder="Password" />
                                                    </div>
                                                </div>';
                                             if($data['setting']['so_sociallogin_enable']):
													  $yt .=  '<div class=" form-group">
																			<label class="control-label">'.$data['text_title_login_with_social'].'</label>
																			<div>';
																			 if($data['setting']['so_sociallogin_googlestatus']):
																					if($data['setting']['so_sociallogin_button'] == 'icon'):
													  $yt .=  '<a href="'.$data['googlelink'].'" class="btn btn-social-icon btn-sm btn-google-plus"><i class="fa fa-google fa-fw" aria-hidden="true"></i></a>';
																					 else:
													   $yt .=  '<a class="social-icon" href="'.$data['googlelink'].'">
                                                                    <img class="img-responsive" src="'.$data['googleimage'].'" 
                                                                        data-toggle="tooltip" alt="'.$data['setting']['so_sociallogin_googletitle'].'" 
                                                                        title="'.$data['setting']['so_sociallogin_googletitle'].'" 
                                                                    />
                                                                </a>';
                                                             endif;
                                                        endif;
                                                        if($data['setting']['so_sociallogin_fbstatus']):
                                                            if($data['setting']['so_sociallogin_button'] == 'icon'):
                                                                 $yt .=  ' <a href="'.$data['fblink'].'" class="btn btn-social-icon btn-sm btn-facebook"><i class="fa fab fa-facebook fa-fw" aria-hidden="true"></i></a>';
                                                            else:
                                                                $yt .=  '<a href="'.$data['fblink'].'" class="social-icon">
                                                                    <img class="img-responsive" src="'.$data['fbimage'].'" 
                                                                        data-toggle="tooltip" alt="'.$data['setting']['so_sociallogin_fbtitle'].'" 
                                                                        title="'.$data['setting']['so_sociallogin_fbtitle'].'"
                                                                    />
                                                                </a>';
                                                            endif;
                                                        endif;
														if($data['setting']['so_sociallogin_twitstatus']):
                                                            if ($data['setting']['so_sociallogin_button'] == 'icon'):
                                                        $yt .=  '        <a href="'.$data['twitlink'].'" class="btn btn-social-icon btn-sm btn-twitter"><i class="fa fab fa-twitter fa-fw" aria-hidden="true"></i></a>';
                                                            else:
                                                        $yt .=  '        <a class="social-icon"  href="'.$data['twitlink'].'">
                                                                    <img class="img-responsive" src="'.$data['twitimage'].'" 
                                                                        data-toggle="tooltip" alt="'.$data['setting']['so_sociallogin_twittertitle'].'" 
                                                                        title="'.$data['setting']['so_sociallogin_twittertitle'].'"
                                                                    />
                                                                </a>';
                                                            endif;
                                                        endif;
														if($data['setting']['so_sociallogin_linkstatus']):
                                                            if ($data['setting']['so_sociallogin_button'] == 'icon'):
                                                        $yt .=  '        <a href="'.$data['linkdinlink'].'" class="btn btn-social-icon btn-sm btn-linkdin"><i class="fa fa-linkedin fa-fw" aria-hidden="true"></i></a>';
                                                            else:
                                                        $yt .=  '        <a class="social-icon"  href="'.$data['linkdinlink'].'">
                                                                    <img class="img-responsive" src="'.$data['linkdinimage'].'" 
                                                                        data-toggle="tooltip" alt="'.$data['setting']['so_sociallogin_linkedintitle'].'" 
                                                                        title="'.$data['setting']['so_sociallogin_linkedintitle'].'"
                                                                    />
                                                                </a>';
                                                            endif;
                                                        endif;
                                               $yt .=  '     </div>
                                                </div>';
                                                endif;
                                                $yt .=  '<div class="secondary ft-link-p"><a class="action remind" href="'.$data['link_forgot_password'].'"><span>'.$data['text_forgot_password'].'</span></a></div>
                                                <div class="actions-toolbar">
                                                    <div class="primary"><button type="submit" class="action login primary" name="send" id="send2"><span>'.$data['text_login'].'</span></button></div>
                                                </div>
                                            </fieldset>
                                        </form>
                                    </div>
                                </div>      
                                <div class="col-reg login-customer">
                                    '.$data['text_colregister'].'
                                    <a class="btn-reg-popup" title="'.$data['text_register'].'" href="'.$data['register'].'">'.$data['text_create_account'].'</a>
                                </div>
                                <div style="clear:both;"></div>
                            </div>
                            </div>    
                        </div>
                    </div>
                    <script type="text/javascript">
                        jQuery(document).ready(function($) {
                            var $window = $(window);
                            function checkWidth() {
                                var windowsize = $window.width();
                                if (windowsize > 767) {
                                    $("a[href*=\'account/login\']").click(function (e) {
                                        e.preventDefault();
										
                                        $("#so_sociallogin").modal("toggle");
                                    });
                                }
                            }
                            checkWidth();
                            $(window).resize(checkWidth);
                        });
                    </script>';
					
				$output = str_replace('<div id="socialLogin"></div>',$yt,$output);
		}
	}	
}