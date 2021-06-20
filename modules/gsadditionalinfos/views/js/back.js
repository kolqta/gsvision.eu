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
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/
$( document ).ready(function() {
	var url = $( "#module_form" ).prop('action')+'&autocomplete=1';
	$( "#GS_ADDITIONAL_INFOS_NBR_PR_TOP_PRODUCT" ).autocomplete({
		source: function (request, response) {
			$.ajax({
				type: "POST",
				url: url+'&s='+request.term+'&c='+$( "#GS_ADDITIONAL_INFOS_NBR_PR_CATEGORY" ).val(),
				// data: {c:"variable2_data"},
				success: response,
				dataType: 'json',
				minLength: 2,
				delay: 100
			})
			// source: url,
			// minLength: 2,
			// select: function( event, ui ) {
				// log( "Selected: " + ui.item.value + " aka " + ui.item.id );
			// }
		}
	});
} );