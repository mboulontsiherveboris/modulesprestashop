<?php

/**
 * Get Free Shipping PrestaShop module.
 *
 * @package   gmgetfreeshipping
 * @author    Dariusz Tryba (contact@greenmousestudio.com)
 * @copyright Copyright (c) Green Mouse Studio (http://www.greenmousestudio.com)
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_'))
    exit;

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class GMGetFreeShipping extends Module implements WidgetInterface {

    public function __construct() {
        $this->name = 'gmgetfreeshipping';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'herve boris';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Get free shipping');
        $this->description = $this->l('Display Spend another X to get free shipping message');
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

        $this->templateFile = 'module:gmgetfreeshipping/views/templates/hook/gmgetfreeshipping.tpl';
        //$this->controllers = array('ajax');
    }

    public function getContent() {
        $output = '';
        $output .= '<p class="alert alert-warning">{widget name=\'' . $this->name . '\'}</p>';
        if (Tools::isSubmit('submitGmGetFreeShipping')) {
            $freeShipping = (int) Tools::getValue('GM_FREE_SHIPPING');
            Configuration::updateValue('GM_FREE_SHIPPING', $freeShipping);
            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }
        return $output . $this->renderForm() . $this->context->smarty->fetch($this->local_path . 'views/templates/admin/gms.tpl');
    }

    public function install() {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        Configuration::updateValue('GM_FREE_SHIPPING', 0);
        return parent::install() && $this->registerHook('displayShoppingCartFooter') && $this->registerHook('header');
    }

    public function uninstall() {
        if (!Configuration::deleteByName('GM_FREE_SHIPPING') || !parent::uninstall())
            return false;
        return true;
    }

    public function renderForm() {
        $fieldsForm = array(
            'form' => array(
               'legend' => array(
                   'title' => $this->l('Settings'),
                   'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Free shipping starts at'),
                        'name' => 'GM_FREE_SHIPPING',
                        'class' => 'fixed-width-md',
                        'desc' => $this->l('Set to 0 to use global setting, currently set to: ') . Configuration::get('PS_SHIPPING_FREE_PRICE'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = (int) Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitGmGetFreeShipping';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fieldsForm));
    }

    public function getConfigFieldsValues() {
        return array(
            'GM_FREE_SHIPPING' => Tools::getValue('GM_FREE_SHIPPING', Configuration::get('GM_FREE_SHIPPING')),
        );
    }

    public function hookHeader() {
        $this->context->controller->registerStylesheet('gmgetfreeshipping-css',
                'modules/' . $this->name . '/views/css/gmgetfreeshipping.css',
                [
                    'media' => 'all',
                    'priority' => 200,
        ]);
        if ($this->context->controller->php_self === 'cart') {
            $this->context->controller->registerJavascript('gmgetfreeshipping-js',
                    'modules/' . $this->name . '/views/js/gmgetfreeshipping.js',
                    [
                        'position' => 'bottom',
                        'priority' => 150
            ]);
        }
    }

    public function renderWidget($hookName, array $configuration) {
        $variables = $this->getWidgetVariables($hookName, $configuration);
        if (empty($variables)) {
            return false;
        }
        $this->smarty->assign($variables);
        return $this->fetch($this->templateFile);
    }

    public function getWidgetVariables($hookName, array $configuration) {
        $freeShipping = (int) Configuration::get('GM_FREE_SHIPPING');
        if (!$freeShipping) {
            $freeShipping = Configuration::get('PS_SHIPPING_FREE_PRICE');
        }
        if ($freeShipping) {
            $freeShippingPrice = Tools::convertPrice($freeShipping);
            $cart = $this->context->cart;
            $totalWithoutShipping = $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING);
            $remainingToSpend = $freeShippingPrice - $totalWithoutShipping;
            return array(
                'free_phipping_price' => $freeShippingPrice,
                'total_without_shipping' => $totalWithoutShipping,
                'remaining_to_spend' => $remainingToSpend,
                'refresh_url' => $this->context->link->getModuleLink('gmgetfreeshipping', 'ajax', array(), null, null,
                        null, true),
            );
        }
        return array();
    }

}
