<?php

namespace Drupal\premium_theme_helper\Plugin\Layout;

use Drupal\Core\Form\FormStateInterface;
use Drupal\layout_builder\Plugin\Layout\MultiWidthLayoutBase;

/**
 * Handle global settings for all layouts.
 *
 * @package Drupal\premium_theme_helper\Plugin\Layout
 */
class BaseLayout extends MultiWidthLayoutBase {

  /**
   * {@inheritDoc}
   */
  protected function getWidthOptions(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form = parent::buildConfigurationForm($form, $form_state);

    $form['color_theme'] = [
      '#type' => 'styles',
      '#collection' => 'color_theme',
      '#multiple' => FALSE,
      '#title' => $this->t('Color theme'),
      '#required' => FALSE,
      '#default_value' => $this->configuration['color_theme'] ?? 'none',
      '#description' => $this->t('Choose color theme this layout.'),
    ];

    $form['column_spacing_top'] = [
      '#type' => 'select',
      '#multiple' => FALSE,
      '#title' => $this->t('Column spacing top'),
      '#options' => [
        'none' => $this->t('None'),
        'small' => $this->t('Small'),
        'medium' => $this->t('Medium'),
        'large' => $this->t('Large'),
      ],
      '#default_value' => $this->configuration['column_spacing_top'] ?? 'medium',
      '#description' => $this->t('Choose column spacing layout.'),
    ];

    $form['column_spacing_bottom'] = [
      '#type' => 'select',
      '#multiple' => FALSE,
      '#title' => $this->t('Column spacing bottom'),
      '#options' => [
        'none' => $this->t('None'),
        'small' => $this->t('Small'),
        'medium' => $this->t('Medium'),
        'large' => $this->t('Large'),
      ],
      '#default_value' => $this->configuration['column_spacing_bottom'] ?? 'medium',
      '#description' => $this->t('Choose column spacing for this layout.'),
    ];

    $form['section_bg_graphics'] = [
      '#type' => 'select',
      '#multiple' => FALSE,
      '#title' => $this->t('Section background graphics'),
      '#options' => [
        'none' => $this->t('None'),
        'duck' => $this->t('Duck'),
        'fish' => $this->t('Fish'),
        'birds' => $this->t('Birds'),
        'branches' => $this->t('Branches'),
        'dragonfly' => $this->t('Dragonfly'),
        'sea' => $this->t('Sea'),
        'helicopters' => $this->t('Helicopters'),
        'deer' => $this->t('Deer'),
        'vortex' => $this->t('Vortex'),
        'rainbow' => $this->t('Rainbow'),
        'reeds' => $this->t('Reeds'),
        'clouds' => $this->t('Clouds'),
        'butterflies' => $this->t('Butterflies'),
        'wind' => $this->t('Wind'),
      ],
      '#default_value' => $this->configuration['section_bg_graphics'] ?? 'none',
      '#description' => $this->t('Choose section background graphics.'),
      '#weight' => 10,
    ];

    $form['section_bg_graphics_position'] = [
      '#type' => 'select',
      '#multiple' => FALSE,
      '#title' => $this->t('Section background graphics position'),
      '#options' => [
        'right' => $this->t('Right'),
        'left' => $this->t('Left'),
      ],
      '#default_value' => $this->configuration['section_bg_graphics_position'] ?? 'right',
      '#description' => $this->t('Choose section background graphics position.'),
      '#weight' => 11,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    parent::submitConfigurationForm($form, $form_state);
    $this->configuration['color_theme'] = $form_state->getValue('color_theme');
    $this->configuration['column_spacing_top'] = $form_state->getValue('column_spacing_top');
    $this->configuration['column_spacing_bottom'] = $form_state->getValue('column_spacing_bottom');
    $this->configuration['column_show_graphic_top'] = $form_state->getValue('column_show_graphic_top');
    $this->configuration['column_show_graphic_bottom'] = $form_state->getValue('column_show_graphic_bottom');
    $this->configuration['section_bg_graphics'] = $form_state->getValue('section_bg_graphics');
    $this->configuration['section_bg_graphics_position'] = $form_state->getValue('section_bg_graphics_position');
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $configuration = parent::defaultConfiguration();
    $configuration['color_theme'] = 'none';
    $configuration['column_spacing_top'] = 'medium';
    $configuration['column_spacing_bottom'] = 'medium';
    $configuration['column_show_graphic_top'] = 'medium';
    $configuration['column_show_graphic_bottom'] = 'medium';
    $configuration['section_bg_graphics'] = 'none';
    $configuration['section_bg_graphics_position'] = 'right';
    return $configuration;
  }

}
