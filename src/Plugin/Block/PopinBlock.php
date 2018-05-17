<?php

namespace Drupal\popin\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Image\Image;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

/**
 * Provides a 'PopinBlock' block.
 *
 * @Block(
 *  id = "popin_block",
 *  admin_label = @Translation("Popin block"),
 * )
 */
class PopinBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::config('popin.popinconfig')->getRawData();
    $build = [
      '#cache' => [
        'tags' => ['popin'],
      ]
    ];
    if($config['enabled'] !== 1) {
      return $build;
    }
    $image_style = ImageStyle::load('popin');
    $image = File::load($config['image'][0]);
    $config['image'] = $image_style->buildUrl($image->getFileUri());

    dpm($config);

    $build['popin_block']['#theme'] = 'popin_block';
    $build['popin_block']['#config'] = $config;

    return $build;
  }

}
