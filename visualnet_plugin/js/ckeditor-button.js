/**
 * @file
 * CKEditor Block functionality.
 */

(function ($, Drupal, drupalSettings)
{
	'use strict';
	Drupal.behaviors.ckeditorButton = {

		attach: function (context, settings)
		{
			var container = jQuery ('.ckeditor-button');

			if (container.length == 0) {
				return;
			}

			var a = container.find('a');

			if (a.length > 0) {

				container.css('cursor', 'pointer');
				container.on('click', function ()
				{
					window.location = a.attr('href');

					return false;
				});
			}

		}
	};
}) (jQuery, Drupal, drupalSettings);
