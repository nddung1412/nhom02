<?php

if (!defined('_PS_VERSION_'))
	exit;

class Fbcomment extends Module
{
        public function __construct()
	{
		$this->name = 'fbcomment';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'Prestakit';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Facebook Comments');
		$this->description = $this->l('Allows to add Facebook Comments box to the product page.');
	}
        
        public function install()
	{
		return Configuration::updateValue('FACEBOOK_COMMENTS_APPID', '')
                        && Configuration::updateValue('FACEBOOK_COMMENTS_ADMIDS', '')
                        && Configuration::updateValue('FACEBOOK_COMMENTS_COLORSCHEME', 'light')
                        && Configuration::updateValue('FACEBOOK_COMMENTS_LOCALE', 'en_US') 
                        && Configuration::updateValue('FACEBOOK_COMMENTS_QTY', '10')
                        && Configuration::updateValue('FACEBOOK_COMMENTS_WIDTH', '470')
                        && parent::install() 
                        && $this->registerHook('displayTop') 
                        && $this->registerHook('productFooter')
                        && $this->registerHook('header');
	}
        
	public function hookHeader($params)
	{
                $id_product = (int)Tools::getValue('id_product');
                
                if ($id_product) {
                    
                        // don't output fb metatags if facebooktags module is installed
                        if (Module::isInstalled('facebooktags') && Module::isEnabled('facebooktags'))
                                return;
                
                        $this->smarty->assign(array(
                                'appid' => Configuration::get('FACEBOOK_COMMENTS_APPID'),
                                'admids' => Configuration::get('FACEBOOK_COMMENTS_ADMIDS')
                        ));

                        return $this->display(__FILE__, 'facebooktags.tpl');
                }
	}        
        
        public function hookDisplayTop($params)
	{
                $id_product = (int)Tools::getValue('id_product');
                
                if ($id_product) {
                    
                        // don't output js code if facebooktags module is installed
                        if (Module::isInstalled('facebooktags') && Module::isEnabled('facebooktags'))
                                return;
                    
                        return $this->displayFacebookJs();
                }
        }
        
	public function hookProductFooter($params)
	{
		$id_product = (int)Tools::getValue('id_product');

		if ($id_product)
		{		
			return $this->displayCommentBox();
		}
	}        
        
        
	public function getContent()
	{
                $output = '<h2>'.$this->displayName.'</h2>';
                
		if (Tools::isSubmit('submitFbComments'))
		{
                        // Validate application id (obligatory)
			$appid = Tools::getValue('appid');
                        
			if ($appid && !self::isFacebookId($appid))
				$errors[] = $this->l('Invalid or empty Facebook application ID');
			else
                                Configuration::updateValue('FACEBOOK_COMMENTS_APPID', $appid);
                        
                        // Validate administrators ids (non obligatory)
                        $admids = Tools::getValue('admids');
                        
                        if ($admids && !self::isAdminIds($admids)) {
                                $errors[] = $this->l('Invalid facebook administrators IDs');
                        } else
                                Configuration::updateValue('FACEBOOK_COMMENTS_ADMIDS', $admids);                        
                        
                        // Validate width (obligatory)
                        $width = Tools::getValue('width');
                                                
                        if (!Validate::isInt($width) || $width < 400) {
                                $errors[] = $this->l('Incorrect or empty width');
                        } else {
                                Configuration::updateValue('FACEBOOK_COMMENTS_WIDTH', $width);
                        }        
                        
                        // Validate qty
                        $qty = Tools::getValue('qty');
                        
                        if (!Validate::isInt($qty) || $qty < 1) {
                                $errors[] = $this->l('Incorrect or empty number of comments');
                        } else {
                                Configuration::updateValue('FACEBOOK_COMMENTS_QTY', $qty);
                        } 
                        
                        // Update configs without validation
                        Configuration::updateValue('FACEBOOK_COMMENTS_COLORSCHEME', Tools::getValue('scheme'));
                        Configuration::updateValue('FACEBOOK_COMMENTS_LOCALE', Tools::getValue('locale'));
                        
			if (isset($errors) AND sizeof($errors))
				$output .= $this->displayError(implode('<br />', $errors));
			else
				$output .= $this->displayConfirmation($this->l('Settings updated'));
                        
		}
		return $output.$this->displayForm();
	} 
        
	public function displayForm()
	{
                $scheme = Tools::safeOutput(Tools::getValue('scheme', Configuration::get('FACEBOOK_COMMENTS_COLORSCHEME')));
                
		$output = '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
				<p>'.$this->l('Add developer ID from your Facebook developer account').'</p><br />
				<label>'.$this->l('Facebook application ID').'</label>
				<div class="margin-form">
					<input type="text" size="20" name="appid" value="'.Tools::safeOutput(Tools::getValue('appid', Configuration::get('FACEBOOK_COMMENTS_APPID'))).'" />
					<p class="clear">'.$this->l('Facebook application ID from your Facebook developer account').'</p>
                                </div>
                                <label>'.$this->l('Facebook administrators ID').'</label>
                                <div class="margin-form">
					<input type="text" size="20" name="admids" value="'.Tools::safeOutput(Tools::getValue('admids', Configuration::get('FACEBOOK_COMMENTS_ADMIDS'))).'" />
					<p class="clear">'.$this->l('Facebook IDs of administrators, separate with commas').'</p>
                                </div>
                                <div class="margin-form">'.$this->langSelector().'</div>
                                <label>'.$this->l('Width of the plugin').'</label>
                                <div class="margin-form">
                                        <input type="text" size="20" name="width" value="'.Tools::safeOutput(Tools::getValue('width', Configuration::get('FACEBOOK_COMMENTS_WIDTH'))).'" />
                                        <p class="clear">'.$this->l('Minimal recommended width is 400px').'</p>    
                                </div>
                                <label>'.$this->l('Color scheme').'</label>
                                <div class="margin-form">
                                        <select name="scheme">
                                                <option '.(($scheme == 'light') ? 'selected' : '').' value="light">light</option>
                                                <option '.(($scheme == 'dark') ? 'selected' : '').' value="dark">dark</option>
                                        </select>
                                </div>
                                <label>'.$this->l('Number of displayed comments').'</label>
                                <div class="margin-form">
                                        <input type="text" size="20" name="qty" value="'.Tools::safeOutput(Tools::getValue('qty', Configuration::get('FACEBOOK_COMMENTS_QTY'))).'" />
                                        <p class="clear">'.$this->l('Minimal value is 1').'</p>    
                                </div>                                
				<center><input type="submit" name="submitFbComments" value="'.$this->l('Save').'" class="button" /></center>
			</fieldset>
		</form>';
		return $output;
	}
        
        public function langSelector() {
            
                // read langs from xml
                $xml = simplexml_load_file(dirname(__FILE__).'/FacebookLocales.xml');
                $selector = '<select name="locale">';
                
                foreach($xml->children() as $locale) {
                        $code = $locale->codes->code->standard->representation;
                        $selector .= '<option '.(($code == Configuration::get('FACEBOOK_COMMENTS_LOCALE'))?'selected':'').' value="'.$code.'">';
                        $selector .= $locale->englishName;
                        $selector .= '</option>';
                }
                
                $selector .= '</select>';
                
                return $selector;
        }
        
        private function displayCommentBox() {
            
               $this->smarty->assign(array(
                        'num_posts' => Configuration::get('FACEBOOK_COMMENTS_QTY'),
                        'colorscheme' => Configuration::get('FACEBOOK_COMMENTS_COLORSCHEME'),
                        'width' => Configuration::get('FACEBOOK_COMMENTS_WIDTH')
                ));
            
                return $this->display(__FILE__, 'fbcomment.tpl');
        }
        
        private function displayFacebookJs() {
            
                $this->smarty->assign(array(
                        'appid' => Configuration::get('FACEBOOK_COMMENTS_APPID'),
                        'locale' => Configuration::get('FACEBOOK_COMMENTS_LOCALE')
                ));

                return $this->display(__FILE__, 'facebookcode.tpl');
        }
        
        private static function isAdminIds($string)
	{
		return preg_match('/^[0-9,]*$/', $string);
	}
        
        private static function isFacebookId($string)
	{
		return preg_match('/^[0-9]*$/', $string);
	}           
}
?>