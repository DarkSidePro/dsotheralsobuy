{*
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
*}
<div class="container-fluid card card-body p-1 mt-1">
    <h2 class='h2'>{l s='Customers who bought this product also bought' mod='dsothersalsobought'}</h2>
    <div class="owl-carousel owl-theme">
        {foreach $products as $product}
            <div class="item product--item">
                <article class="product-miniature js-product-miniature" data-id-product="{$product.product_id}" data-id-product-attribute="0" itemscope="" itemtype="http://schema.org/Product">
                    <div class="thumbnail-container">
                        <a href="{$product.product_link}" class="thumbnail product-thumbnail">
                            <img src="{$product.product_image}" alt="{$product.product_name|truncate:30:'...'}" data-full-size-image-url="{$product.product_image}">
                        </a>
                        <div class="product-description">
                            <h1 class="h3 product-title" itemprop="name">
                                <a href="{$product.product_link}">
                                    {$product.product_name|truncate:30:'...'}
                                </a>
                            </h1>
                            <div class="product-price-and-shipping">
                                <span class="sr-only">Cena</span>
                                {$product.product_price_netto}
                                <br>
                                <span class="netto">{$product.product_price_brutto} {l s='Brutto' mod='dsothersalsobought'}</span>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        {/foreach}
    </div>
</div>