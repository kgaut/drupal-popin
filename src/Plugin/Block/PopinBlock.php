<?php

namespace Drupal\popin\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\user\PrivateTempStoreFactory;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Provides a 'PopinBlock' block.
 *
 * @Block(
 *  id = "popin_block",
 *  admin_label = @Translation("Popin block"),
 * )
 */
class PopinBlock extends BlockBase implements ContainerFactoryPluginInterface  {

  protected $session;

  /**
   * Drupal\Core\Session\SessionManagerInterface definition.
   *
   * @var \Drupal\Core\Session\SessionManagerInterface
   */
  protected $sessionManager;

  /**
   * Drupal\Core\Session\AccountProxyInterface definition.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new testBkco object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, PrivateTempStoreFactory $user_private_tempstore, SessionManagerInterface $session_manager,  AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->sessionManager = $session_manager;
    $this->session = \Drupal::request()->getSession();
    $this->currentUser = $current_user;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('user.private_tempstore'),
      $container->get('session_manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    if (!isset($_SESSION['session_started']) && \Drupal::currentUser()->isAnonymous()) {
      $_SESSION['session_started'] = TRUE;
      $this->session->start();
    }
    $config = \Drupal::config('popin.popinconfig')->getRawData();
    $build = [
      '#cache' => [
        'tags' => ['popin'],
        'context' => ['user', 'session', 'cookie:popin'],
      ],
    ];

    if(!isset($config['enabled']) || $config['enabled'] !== 1) {
      return $build;
    }
    if($this->session->get('popin', NULL) !== NULL && (int) $this->session->get('popin') === (int) $config['cookie_random']) {
      return $build;
    }

    $image_style = ImageStyle::load('popin');
    if ($image = File::load($config['image'][0])) {
      $config['image'] = $image_style->buildUrl($image->getFileUri());
    }

    $build['popin_block']['#theme'] = 'popin_block';
    $build['popin_block']['#config'] = $config;
    $build['#cache'] = ['max-age' => 0];
    $this->session->set('popin', $config['cookie_random']);

    return $build;
  }

}
