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

class Dsothersalsobought extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'dsothersalsobought';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Dark-Side.pro';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('DS: Customers also buy');
        $this->description = $this->l('This module display a product list in the product page with products with also bought with current product');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayFooterProduct');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        return;
    }



    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
        $this->context->controller->addCSS($this->_path.'/views/css/owl.carousel.min.css');
        $this->context->controller->addCSS($this->_path.'/views/css/owl.theme.default.min.css');
        $this->context->controller->addJS($this->_path.'/views/js/owl.carousel.min.js');


        $this->context->controller->registerJavascript(4, $this->_path.'/views/js/front.js'); 
        $this->context->controller->registerJavascript(2, $this->_path.'/views/js/owl.carousel.min.js');    
        $this->context->controller->registerStylesheet(1, $this->_path.'/views/css/front.css');
        $this->context->controller->registerStylesheet(2, $this->_path.'/views/css/owl.carousel.min.css');
        $this->context->controller->registerStylesheet(3, $this->_path.'/views/css/owl.theme.default.min.css');
    }

    public function hookDisplayFooterProduct($params)
    {
        $id_product = $params['product']['id'];
        $id_lang = $this->context->cookie->id_lang;
        $productIds = $this->getProductIds($id_product);
        $products = array();

        foreach ($productIds as $product) {
            $productId = $product['product_id'];
            $productDetails = $this->getProductDetails($productId, $id_lang);
            $productName = $productDetails->name;
            $productLink = $this->context->link->getProductLink($productId);
            $image = Product::getCover($productId);
            $imageurl = $this->context->link->getImageLink($productDetails->link_rewrite, $image['id_image'], 'home_default');

            $productPriceNetto = Product::getPriceStatic($productId, false);
            $productPriceBrutto = Product::getPriceStatic($productId, true);

            array_push($products, [
                'product_name' => $productName, 
                'product_image' => $imageurl, 
                'product_id' => $productId, 
                'product_price_netto' => Tools::displayPrice($productPriceNetto), 
                'product_price_brutto' => Tools::displayPrice($productPriceBrutto),
                'product_link' => $productLink
                ]
            );
        }

        $this->context->smarty->assign('products', $products);

        return $this->display(__FILE__, 'displayFooterProduct.tpl');
    }

    protected function getProductIds(int $id_product): array
    {
        $db = \Db::getInstance();
        $sql = 'SELECT a.product_id, count(a.product_id) cnt
        FROM (
            SELECT DISTINCT product_id, id_order 
            FROM '._DB_PREFIX_.'order_detail 
            WHERE id_order IN (
                SELECT id_order
                FROM '._DB_PREFIX_.'order_detail 
                WHERE product_id = '.$id_product.')
            AND product_id <> '.$id_product.'
            ) a
        JOIN '._DB_PREFIX_.'product b on a.product_id = b.id_product
        GROUP BY a.product_id
        UNION
        SELECT id_product product_id, 0 cnt
        FROM `'._DB_PREFIX_.'product` a
        left JOIN (
            SELECT a.product_id
            FROM (
                SELECT DISTINCT product_id
                FROM '._DB_PREFIX_.'order_detail 
                WHERE id_order IN (
                    SELECT id_order
                    FROM '._DB_PREFIX_.'order_detail 
                    WHERE product_id = '.$id_product.')
                AND product_id <> '.$id_product.'
                ) a
        ) b on b.product_id = a.id_product
        WHERE id_category_default = (
            SELECT id_category_default
            FROM `'._DB_PREFIX_.'product` WHERE id_product = '.$id_product.')
            AND id_product <> '.$id_product.' AND b.product_id IS NULL
        order by cnt desc
        limit 8';
        $result = $db->executeS($sql);

        return $result;
    }

    protected function getProductDetails(int $id_product, int $id_lang): Product
    {
        $product = new Product($id_product, false, $id_lang);

        return $product;
    }
}
