<?php

namespace Drupal\br50_cfg\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Branch 50 custom remote video.
 *
 * @Block(
 *   id = "br50_cbl_remote_video",
 *   admin_label = @Translation("Remote video"),
 * )
 */
class Br50CblRemoteVideo extends BlockBase implements ContainerFactoryPluginInterface {

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

    // Load the media entity from the image selected.
    $video = $this->entityTypeManager->getStorage('media')->load($this->configuration['video']);

    // Load values in build array.
    $build = [
      '#theme' => 'br50_block_remote_video',
      '#video' => $this->entityTypeManager->getViewBuilder('media')->view($video, 'default'),
    ];

    // Return the build array.
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    // The media library for video.
    $form['video'] = [
      '#type' => 'media_library',
      '#allowed_bundles' => ['remote_video'],
      '#title' => $this->t('Upload your video'),
      '#default_value' => $this->configuration['video'] ?? '',
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
    $this->configuration['video'] = $values['video'];
  }

}
