/**
 * @file
 * Visualnet social widget functionality.
 */

(function ($, Drupal, drupalSettings)
{
	'use strict';

	Drupal.behaviors.widgetSocialMedia = {

		attach: function (context, settings)
		{
			$ ('.social-link .link a').once().on('click', function (e)
			{
				window.open(
					$ (this).data('url'),
					'shareWindow',
					'height=480, width=550, top=' + ($ (window).height() / 2 - 275) + ', left=' + ($ (window).width() / 2 - 225) + ', ' +
					'toolbar=0, location=0, menubar=0, directories=0, scrollbars=0, status=0, titlebar=0, location=0'
				);

				e.preventDefault();
			});

		}
	};

}) (jQuery, Drupal, drupalSettings);