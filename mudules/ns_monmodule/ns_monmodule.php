<?php
 
 if (!defined('_PS_VERSION_')) {
    exit;
}
 
class Ns_MonModule extends Module
{

    public function __construct()
    {
        $this->name = 'ns_monmodule';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Boris herve';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;
 
        parent::__construct();
 
        $this->displayName = $this->l('Module des condition d"utilisation  ');
        $this->description = $this->l('module affiche les condition d"utilisation du site');
 
        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module ?');
 
        if (!Configuration::get('NS_MONMODULE_PAGENAME')) {
            $this->warning = $this->l('Aucun nom fourni');
        }
    }   
    public function install()
{
    if (Shop::isFeatureActive()) {
        Shop::setContext(Shop::CONTEXT_ALL);
    }
 
    if (!parent::install() ||
        !$this->registerHook('leftColumn') ||
        !$this->registerHook('header') ||
        !Configuration::updateValue('NS_MONMODULE_PAGENAME', 'Mentions légales')
    ) {
        return false;
    }
 
    return true;
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
     
        if (!parent::install() ||
            !$this->registerHook('leftColumn') ||
            !$this->registerHook('header') ||
            !Configuration::updateValue('NS_MONMODULE_PAGENAME', 'Mentions légales')
        ) {
            return false;
        }
     
        return true;
    }
}
public function uninstall()
{
    if (!parent::uninstall() ||
        !Configuration::deleteByName('NS_MONMODULE_PAGENAME')
    ) {
        return false;
    }
 
    return true;
} 
public function getContent()
{
    $output = null;
 
    if (Tools::isSubmit('btnSubmit')) {
        $pageName = strval(Tools::getValue('NS_MONMODULE_PAGENAME'));
 
        if (
            !$pageName||
            empty($pageName)
        ) {
            $output .= $this->displayError($this->l('Invalid Configuration value'));
        } else {
            Configuration::updateValue('NS_MONMODULE_PAGENAME', $pageName);
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
    }
 
    return $output.$this->displayForm();
}
public function displayForm()
{
    // Récupère la langue par défaut
    $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');
  
    // Initialise les champs du formulaire dans un tableau
    $form = array(
    'form' => array(
        'legend' => array(
            'title' => $this->l('Settings'),
        ),
        'input' => array(
            array(
                'type' => 'text',
                'label' => $this->l('Configuration value'),
                'name' => 'NS_MONMODULE_PAGENAME',
                'size' => 20,
                'required' => true
            )
        ),
        'submit' => array(
            'title' => $this->l('Save'),
            'name'  => 'btnSubmit'
        )
    ),
);
  
    $helper = new HelperForm();
  
    // Module, token et currentIndex
    $helper->module = $this;
    $helper->name_controller = $this->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
  
    // Langue
    $helper->default_form_language = $defaultLang;
  
    // Charge la valeur de NS_MONMODULE_PAGENAME depuis la base
    $helper->fields_value['NS_MONMODULE_PAGENAME'] = Configuration::get('NS_MONMODULE_PAGENAME');
  
    return $helper->generateForm(array($form));   
}
public function hookDisplayLeftColumn($params)
{
    $this->context->smarty->assign([
        'ns_page_name' => Configuration::get('NS_MONMODULE_PAGENAME'),
        'ns_page_link' => $this->context->link->getModuleLink('ns_monmodule', 'display')
      ]);
 
      return $this->display(__FILE__, 'ns_monmodule.tpl');
}
public function hookDisplayHeader()
{
    $this->context->controller->registerStylesheet(
        'ns_monmodule',
        $this->_path.'views/css/ns_monmodule.css',
        ['server' => 'remote', 'position' => 'head', 'priority' => 150]
    );
}
    
}
    