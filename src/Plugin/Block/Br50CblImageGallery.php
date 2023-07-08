<?php

namespace Drupal\br50_cfg\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Branch 50 custom block image gallery.
 *
 * @Block(
 *   id = "br50_cbl_image_gallery",
 *   admin_label = @Translation("Image gallery"),
 * )
 */
class Br50CblImageGallery extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a BlockComponentRenderArray object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entity_type_manager,
  ) {

    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition
  ) {

    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Get the media ids from the config.
    $mids = $this->configuration['images'];

    // Break apart media ids into array.
    $mids = explode(',', $mids);

    // Step through each media id and get the info
    // about the image.
    foreach ($mids as $mid) {

      // Load the media entity.
      $media = $this->entityTypeManager
        ->getStorage('media')
        ->load($mid);

      // Get the image sources.
      $sources = _br50_theme_prepare_responsive_image($media, 'ris_br50_responsive');

      // Store the info about the image.
      $images[] = [
        'alt' => $sources['alt'],
        'sources' => $sources['responsive_sources'],
        'img_element' => $sources['img_element']['#uri'],
        'caption' => $media->field_br50_image_caption->value,
      ];
    }

    // Load values in build array.
    $build = [
      '#theme' => 'br50_block_image_gallery',
      '#images' => $images,
    ];

    // Return the build array.
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    // The media library for image.
    $form['images'] = [
      '#type' => 'media_library',
      '#allowed_bundles' => ['image'],
      '#cardinality' => -1,
      '#title' => $this->t('Upload your images'),
      '#default_value' => $this->configuration['images'] ?? '',
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {

    // Get the values from the form state.
    $values = $form_state->getValues();

    // Set the values that are needed and required.
    $this->configuration['images'] = $values['images'];
  }

}
