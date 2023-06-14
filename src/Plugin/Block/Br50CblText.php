<?php

namespace Drupal\br50_cfg\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Bracn 50 custom text block.
 *
 *  @Block(
 *    id = "uw_cbl_text",
 *    admin_label = @Translation("Text"),
 *  )
 */
class Br50CblText extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Get the block config.
    $config = $this->configuration['text'];

    // Load values in build array.
    $build = [
      '#theme' => 'br50_block_text',
      '#text' => [
        '#type' => 'processed_text',
        '#format' => $config['format'],
        '#text' => $config['value'],
      ],
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    // The text element.
    $form['text'] = [
      '#type' => 'text_format',
      '#format' => 'full_html',
      '#allowed_formats' => ['full_html'],
      '#title' => $this->t('Text'),
      '#required' => TRUE,
      '#rows' => 7,
      '#default_value' => $this->configuration['text']['value'],
      '#weight' => '1',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {

    // Get the values from the form state.
    $values = $form_state->getValues();

    // Save the config for the text block.
    $this->configuration['text'] = $values['text'];
  }

}

