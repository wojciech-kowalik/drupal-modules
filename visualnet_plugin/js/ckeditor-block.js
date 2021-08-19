/**
 * @file
 * CKEditor Block functionality.
 */

(function ($, Drupal, drupalSettings)
{
	'use strict';

	Drupal.behaviors.ckeditorBlock = {

		attach: function (context, settings)
		{
			var container = jQuery ('.ckeditor-block');

			if(container.length == 0){
				return;
			}

			var elements = container.find('div');

			elements.each(function(){

				var a = jQuery(this).find('a');

				if(a.length > 0){

					jQuery(this).css('cursor', 'pointer');
					jQuery(this).on('click', function(){
						window.location = a.attr('href');
						return false;
					});
				}
			});

			if(elements.length % 2){
				jQuery (elements[elements.length - 1])
						.removeClass('col-xs-6')
						.addClass('col-xs-12');
			}


		}
	};
}) (jQuery, Drupal, drupalSettings);
