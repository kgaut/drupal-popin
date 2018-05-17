<?php

namespace Drupal\popin\Plugin\Block;

use Drupal\Core\Block\BlockBase;

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
    $build = [];
    $build['popin_block']['#theme'] = 'popin_block';

    return $build;
  }

}
