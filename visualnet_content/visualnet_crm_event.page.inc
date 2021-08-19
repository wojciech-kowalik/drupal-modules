<?php

/**
 * @file
 * Contains visualnet_crm_event.page.inc.
 *
 * Page callback for Visualnet crm event entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Visualnet crm event templates.
 *
 * Default template: visualnet_crm_event.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_visualnet_crm_event(array &$variables) {
  // Fetch VisualnetCrmEvent Entity Object.
  $visualnet_crm_event = $variables['elements']['#visualnet_crm_event'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
