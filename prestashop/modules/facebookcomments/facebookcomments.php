<?php

class facebookcomments extends Module {
	function __construct(){
		$this->name = 'facebookcomments';
		$this->tab = 'social_networks';
		$this->version = '1.4.2.0';
        $this->author= 'mypresta.eu';
        $this->dir = '/modules/facebookcomments/';
		parent::__construct();
        $this->trusted();        
		$this->displayName = $this->l('Facebook Comments');
		$this->description = $this->l('An easiest way to add facebook comments plugin for your prestashop store');
        $this->mkey="freelicense";       
        if (@file_exists('../modules/'.$this->name.'/key.php'))
            @require_once ('../modules/'.$this->name.'/key.php');
        else if (@file_exists(dirname(__FILE__) . $this->name.'/key.php'))
            @require_once (dirname(__FILE__) . $this->name.'/key.php');
        else if (@file_exists('modules/'.$this->name.'/key.php'))
            @require_once ('modules/'.$this->name.'/key.php');                        
        $this->checkforupdates();
	}

    function checkforupdates(){
            if (isset($_GET['controller']) OR isset($_GET['tab'])){
                if (Configuration::get('update_'.$this->name) < (date("U")>86400)){
                    $actual_version = facebookcommentsUpdate::verify($this->name,$this->mkey,$this->version);
                }
                if (facebookcommentsUpdate::version($this->version)<facebookcommentsUpdate::version(Configuration::get('updatev_'.$this->name))){
                    $this->warning=$this->l('New version available, check MyPresta.eu for more informations');
                }
            }
        }
        
        
        function trusted(){
            if (_PS_VERSION_ >= "1.6.0.8"){
                if (isset($_GET['controller'])){
                    if ($_GET['controller']=="AdminModules"){
                        if (defined('self::CACHE_FILE_TRUSTED_MODULES_LIST')==true){
                            $context = Context::getContext();
                    		$theme = new Theme($context->shop->id_theme);
                    		// Save the 2 arrays into XML files
                    		$trusted_xml = new SimpleXMLElement('<modules_list/>');
                    		$trusted_xml->addAttribute('theme', $theme->name);
                    		$modules = $trusted_xml->addChild('modules');
                    		$modules->addAttribute('type', 'trusted');
                    		$module = $modules->addChild('module');
                    		$module->addAttribute('name', $this->name);
                            $success = file_put_contents( _PS_ROOT_DIR_.self::CACHE_FILE_TRUSTED_MODULES_LIST, $trusted_xml->asXML());
                            if (defined('self::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST')==true){
                                file_put_contents(_PS_ROOT_DIR_.self::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST,'<name><![CDATA['.$this->name.']]></name>');
                            }
                        }
                    }
                }
            }
        } 
        
        
        
        
	function install(){
        if (parent::install() == false
            OR !Configuration::updateValue('update_'.$this->name,'0')
            OR !$this->registerHook('header')
            OR !$this->registerHook('productTab')
            OR !$this->registerHook('productTabContent')
            OR !$this->registerHook('productFooter')
            OR !$this->registerHook('rightColumn')
            OR !$this->registerHook('leftColumn')
            OR !Configuration::updateValue('fcbc_where','1')
            //OR !Configuration::updateValue('fcbc_url','http://mypresta.eu/')
            OR !Configuration::updateValue('fcbc_width','535')
            OR !Configuration::updateValue('fcbc_nbp','5')
            OR !Configuration::updateValue('fcbc_scheme','light')
            OR !Configuration::updateValue('fcbc_langarray',$this->installconfiguration())
            OR !Configuration::updateValue('fcbc_admins','100001227592471')
            OR !Configuration::updateValue('fcbc_appid','')
        ){
            return false;
        }
	return true;
	}
    
    public function installconfiguration(){
        $fcbc_langarray="";
        foreach (Language::getLanguages(false) AS $key=>$value){
            $fcbc_langarray[$key]='en_GB';
        }
        return $fcbc_langarray;
    }

    public function getconf(){
        $array['fcbc_where']=Configuration::get('fcbc_where');
        $array['fcbc_url']=Configuration::get('fcbc_url');
        $array['fcbc_width']=Configuration::get('fcbc_width');
        $array['fcbc_nbp']=Configuration::get('fcbc_nbp');
        $array['fcbc_scheme']=Configuration::get('fcbc_scheme');
        if ($this->psversion()==5 || $this->psversion()==6){
            $array['fcbc_lang']=Configuration::get('fcbc_langarray', $this->context->language->id);
        } else {
            global $cookie;
            $array['fcbc_lang']=Configuration::get('fcbc_langarray', $cookie->id_lang);
        }
        $array['fcbc_admins']=Configuration::get('fcbc_admins');
        $array['fcbc_appid']=Configuration::get('fcbc_appid');
        return $array;
    }
    
	public function psversion() {
		$version=_PS_VERSION_;
		$exp=$explode=explode(".",$version);
		return $exp[1];
	}
    
    public function advert(){
        return '<iframe src="//apps.facepages.eu/somestuff/whatsgoingon.html" width="100%" height="150" border="0" style="border:none;"></iframe>';
    }
    
    public function getContent(){
        $output="";
        if (Tools::isSubmit('submit_settings')){
            Configuration::updatevalue('fcbc_where',$_POST['fcbc_where']);
            //Configuration::updatevalue('fcbc_url',$_POST['fcbc_url']);
            Configuration::updatevalue('fcbc_width',$_POST['fcbc_width']);
            Configuration::updatevalue('fcbc_nbp',$_POST['fcbc_nbp']);
            Configuration::updatevalue('fcbc_scheme',$_POST['fcbc_scheme']);
            Configuration::updatevalue('fcbc_langarray',$_POST['fcbc_langarray']);
            Configuration::updatevalue('fcbc_admins',$_POST['fcbc_admins']);
            Configuration::updatevalue('fcbc_appid',$_POST['fcbc_appid']);
            $output.='<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Settings Saved').'" />'.$this->l('Settings Saved').'</div>';
        }      
        return $output.$this->displayForm();
    }
        
	public function displayForm(){
	   $var=$this->getconf();
       
       $fcbcwhere1="";
       $fcbcwhere2="";
       $fcbcwhere3="";
       $fcbcwhere4="";
       $fcbcscheme1="";
       $fcbcscheme2="";
       if ($var['fcbc_where']=="1"){$fcbcwhere1="checked=\"yes\"";}
       if ($var['fcbc_where']=="2"){$fcbcwhere2="checked=\"yes\"";}
       if ($var['fcbc_where']=="3"){$fcbcwhere3="checked=\"yes\"";}
       if ($var['fcbc_where']=="4"){$fcbcwhere4="checked=\"yes\"";}
       if ($var['fcbc_scheme']=="dark"){$fcbcscheme2="selected=\"yes\"";}
       if ($var['fcbc_scheme']=="light"){$fcbcscheme1="selected=\"yes\"";}
       $languages = Language::getLanguages(false);
       $id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
       $langiso="";
       foreach ($languages as $language){
        $langiso.='<div id="header_fcbc_langarray_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $id_lang_default ? 'block' : 'none').';float: left;">
        <input type="text" id="fcbc_langarray_'.$language['id_lang'].'" name="fcbc_langarray['.$language['id_lang'].']" value="'.Configuration::get('fcbc_langarray',$language['id_lang']).'">
        </div>';
       }
       $langiso.=$this->displayFlags($languages, $id_lang_default, 'header_fcbc_langarray', 'header_fcbc_langarray', true);
              
            $form='<div id="module_block_settings">        
            <fieldset id="fieldset_module_block_settings">
                <legend style="display:inline-block;"><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
                <form action="'.$_SERVER['REQUEST_URI'].'" method="post">
                    <label>'.$this->l('Product Tabs').'</label>
                    <div class="margin-form">
                        <input type="radio" name="fcbc_where" value="1" '.$fcbcwhere1.'/>
                    </div>
                    
                    <label>'.$this->l('Product Footer').'</label>
                    <div class="margin-form">
                        <input type="radio" name="fcbc_where" value="2" '.$fcbcwhere2.'/>
                    </div>
                    
                    <label>'.$this->l('Right Column').'</label>
                    <div class="margin-form">
                        <input type="radio" name="fcbc_where" value="3" '.$fcbcwhere3.'/>
                    </div>

                    <label>'.$this->l('Left Column').'</label>
                    <div class="margin-form">
                        <input type="radio" name="fcbc_where" value="4" '.$fcbcwhere4.'/>
                    </div>                    
                  
                    <label>'.$this->l('Comments feed width').'</label>
                    <div class="margin-form">
                        <input type="text" name="fcbc_width" value="'.$var['fcbc_width'].'"/>
                    </div> 
                    <label>'.$this->l('Number of comments').'</label>
                    <div class="margin-form">
                        <input type="text" name="fcbc_nbp" value="'.$var['fcbc_nbp'].'"/>
                    </div>
                    
                    <div style="clear:both; display:block;">
                    <label>'.$this->l('Color scheme').'</label>
                    <div class="margin-form">
                        <select name="fcbc_scheme"/>
                            <option value="light" '.$fcbcscheme1.'>'.$this->l('light').'</option>
                            <option value="dark" '.$fcbcscheme2.'>'.$this->l('dark').'</option>
                        </select>
                    </div>
                    </div>
                    
                    <div style="clear:both; display:block;">
                    <label>'.$this->l('Language').'</label><p class="small" ><a href="http://mypresta.eu/en/art/know-how/facebook-list-of-local-language-codes.html" target="_blank">'.$this->l('read more about language codes').'</a></p>
                    <div class="margin-form" >
                        '.$langiso.' 
                    </div>   
                    </div>    
                    
                    <div style="clear:both; display:block; margin-top:25px;">                               
                    <label>'.$this->l('Admins').'</label>
                    <div class="margin-form">
                        <input type="text" name="fcbc_admins" value="'.$var['fcbc_admins'].'"/>
                        <p class="clear">'.$this->l('Separate all admin IDs by commas').'</p>
                    </div>
                    </div>
                    
                    <label>'.$this->l('APP id').'</label>
                    <div class="margin-form">
                        <input type="text" name="fcbc_appid" value="'.$var['fcbc_appid'].'"/>
                        <p class="clear">'.$this->l('You can use own facebook app').'</p>
                    </div>                                                                                                                                
                    <center>
                    <input type="submit" name="submit_settings" value="'.$this->l('Save Settings').'" class="button" />
                    <br/><br/><br/><br/>
                    '.$this->l('').'
                    
                    </center>
                </form>
            </fieldset><div style="diplay:block; clear:both; margin-bottom:5px;">
		</div>
        <div style="float:left; text-align:left; display:inline-block; margin-top:5px;">'.$this->l('like us on Facebook').'</br><iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ffacebook.com%2Fmypresta&amp;send=false&amp;layout=button_count&amp;width=120&amp;show_faces=true&amp;font=verdana&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=276212249177933" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:120px; height:21px; margin-top:10px;" allowtransparency="true"></iframe>
        </div>
        <div style="float:left; text-align:left; display:inline-block; margin-top:5px;">
        <form target="_blank" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="margin-top:15px;">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="CRTHNBD2U8KPW">
<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">
</form> 
        </div>
        '.'<div style="float:right; text-align:right; display:inline-block; margin-top:5px; font-size:10px;">
        '.$this->l('Proudly developed by').' <a href="http://mypresta.eu" style="font-weight:bold; color:#B73737">MyPresta<font style="color:black;">.eu</font></a>
        </div>
            </div>';
            
        return $this->advert().$form;
	}    
	
	function hookheader($params){
        $var=$this->getconf();
        global $smarty;
        $smarty->assign('var', $var);
        return $this->display(__FILE__, 'header.tpl');
	}
    
    function hookProductFooter($params){
        $var=$this->getconf();
        global $smarty;
        $smarty->assign('var', $var);
        if ($var['fcbc_where']==2){
    		return $this->display(__FILE__, 'productfooter.tpl');
        }
    }   

    function hookRightColumn($params){
        $var=$this->getconf();
        if ($var['fcbc_where']==3){
            if (isset($_GET['id_product']) && isset($_GET['controller'])){
               if ($_GET['controller']=='product'){
                    global $smarty;
                    $smarty->assign('var', $var);
                    return $this->display(__FILE__, 'productfooter.tpl');
               }
            }
        }
    }

    function hookLeftColumn($params){
        $var=$this->getconf();
        if ($var['fcbc_where']==4){
            if (isset($_GET['id_product']) && isset($_GET['controller'])){
               if ($_GET['controller']=='product'){
                    global $smarty;
                    $smarty->assign('var', $var);
                    return $this->display(__FILE__, 'productfooter.tpl');
               }
            }
        }
    }         
    
    function hookProductTab($params){
        $var=$this->getconf();
        if ($var['fcbc_where']==1){
    		global $smarty;
    		$smarty->assign('ms_tabs', intval(Configuration::get('PS_TAB_PAGES')));
            $smarty->assign('psversion',$this->psversion());
            $smarty->assign('var', $var);
            if ($this->psversion()==6){
                return $this->display(__FILE__, 'tab16.tpl');
            } else {
                return $this->display(__FILE__, 'tab.tpl');
            }
        }
	}
    
    public function hookProductTabContent($params){
        $var=$this->getconf();
        if ($var['fcbc_where']==1){
    		global $smarty;
    		$smarty->assign('ms_tabs', Configuration::get('PS_TAB_PAGES'));
            $smarty->assign('var', $var);
            if ($this->psversion()!=6){
    		  return ($this->display(__FILE__, '/tabcontents.tpl'));
            }
        }
	}
}

class facebookcommentsUpdate extends facebookcomments {  
    public static function version($version){
        $version=(int)str_replace(".","",$version);
        if (strlen($version)==3){$version=(int)$version."0";}
        if (strlen($version)==2){$version=(int)$version."00";}
        if (strlen($version)==1){$version=(int)$version."000";}
        if (strlen($version)==0){$version=(int)$version."0000";}
        return (int)$version;
    }
    
    public static function encrypt($string){
        return base64_encode($string);
    }
    
    public static function verify($module,$key,$version){
        if (ini_get("allow_url_fopen")) {
             if (function_exists("file_get_contents")){
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module='.$module."&version=".self::encrypt($version)."&lic=$key&u=".self::encrypt(_PS_BASE_URL_.__PS_BASE_URI__));
             }
        }
        Configuration::updateValue("update_".$module,date("U"));
        Configuration::updateValue("updatev_".$module,$actual_version); 
        return $actual_version;
    }
}
?>