<?php
/**
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Gsadditionalinfos extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'gsadditionalinfos';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'kolqta';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('gsadditionalinfos');
        $this->description = $this->l('Opis - gsadditionalinfos');
		
		// С цел ако се преработва модула след инсталация
        if (!Configuration::get('GS_ADDITIONAL_INFOS_NBR_PR_TOP_PRODUCT')) {
			Configuration::updateValue('GS_ADDITIONAL_INFOS_NBR_PR_TOP_PRODUCT', '');
		}
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('GSADDITIONALINFOS_LIVE_MODE', false);
        Configuration::updateValue('GS_ADDITIONAL_INFOS_NBR_PR_BRAND', '');
        Configuration::updateValue('GS_ADDITIONAL_INFOS_NBR_PR_TOP_PRODUCT', '');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('actionProductListOverride') &&
            $this->registerHook('displayFooter') &&
            $this->registerHook('displayFooterProduct') &&
            $this->registerHook('displayHome') &&
            $this->registerHook('displayProductListFunctionalButtons');
    }

    public function uninstall()
    {
        Configuration::deleteByName('GSADDITIONALINFOS_LIVE_MODE');
        Configuration::deleteByName('GS_ADDITIONAL_INFOS_NBR_PR_BRAND');
        Configuration::deleteByName('GS_ADDITIONAL_INFOS_NBR_PR_TOP_PRODUCT');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitGsadditionalinfosModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->renderForm();
        $output .= $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        // $this->context->controller->addJqueryUI('ui.autocomplete');
		
        return $output;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitGsadditionalinfosModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'GSADDITIONALINFOS_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-file-text"></i>',
                        'desc' => $this->l('Брой продукти от марка:'),
                        'name' => 'GS_ADDITIONAL_INFOS_NBR_PR_BRAND',
                        'label' => $this->l('Mark'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-search"></i>',
                        'desc' => $this->l('Топ продукт в сайта:'),
                        'name' => 'GS_ADDITIONAL_INFOS_NBR_PR_TOP_PRODUCT',
                        'label' => $this->l('Top product'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'GSADDITIONALINFOS_LIVE_MODE' => Configuration::get('GSADDITIONALINFOS_LIVE_MODE', true),
            'GS_ADDITIONAL_INFOS_NBR_PR_BRAND' => Configuration::get('GS_ADDITIONAL_INFOS_NBR_PR_BRAND', 'contact@prestashop.com'),
            'GS_ADDITIONAL_INFOS_NBR_PR_TOP_PRODUCT' => Configuration::get('GS_ADDITIONAL_INFOS_NBR_PR_TOP_PRODUCT', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookActionProductListOverride()
    {
        /* Place your code here. */
    }

    public function hookDisplayFooter()
    {
        /* Place your code here. */
    }

    public function hookDisplayFooterProduct()
    {
        /* Place your code here. */
    }

    public function hookDisplayHome()
    {
        /* Place your code here. */
    }

    public function hookDisplayProductListFunctionalButtons()
    {
        /* Place your code here. */
    }
}
