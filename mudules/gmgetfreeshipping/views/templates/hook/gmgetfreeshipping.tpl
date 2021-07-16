{*
* Get Free Shipping PrestaShop module.
*
* @package   gmgetfreeshipping
* @author    Dariusz Tryba (contact@greenmousestudio.com)
* @copyright Copyright (c) Green Mouse Studio (http://www.greenmousestudio.com)
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
<div class="block-get-free-shipping gmgetfreeshipping" data-refresh-url="{$refresh_url}" {if $remaining_to_spend <= 0} style="display: none;" {/if}>
   {if $remaining_to_spend > 0}
      <p><strong>
            {l s='Spend another' mod='gmgetfreeshipping'}
            {Tools::displayPrice($remaining_to_spend)}
            {l s='to get free shipping for your order!' mod='gmgetfreeshipping'}
         </strong></p>
      {/if}
</div>
