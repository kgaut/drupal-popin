<?php

namespace Drupal\popin\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\file\Entity\File;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigManagerInterface;

/**
 * Class PopinConfigForm.
 */
class PopinConfigForm extends ConfigFormBase {

  /**
   * Constructs a new PopinConfigForm object.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    parent::__construct($config_factory);
  }

  public static function create(ContainerInterface $container) {
    return new static($container->get('config.factory'));
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'popin.popinconfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'popin_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('popin.popinconfig');

    $form['basic'] = [
      '#type' => 'details',
      '#title' => $this->t('Popin display configuration'),
      '#open' => TRUE,
    ];

    $form['basic']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Popin enabled ?'),
      '#default_value' => $config->get('enabled'),
    ];
    $form['basic']['datestart'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Start date for popin display'),
      '#description' => $this->t('If left empty, the popin will be displayed without limit'),
      '#default_value' => $config->get('datestart') ? DrupalDateTime::createFromTimestamp($config->get('datestart')) : NULL,
    ];
    $form['basic']['dateend'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Ending date for popin display'),
      '#description' => $this->t('If left empty, the popin will be displayed without limit'),
      '#default_value' => $config->get('dateend') ? DrupalDateTime::createFromTimestamp($config->get('dateend')) : NULL,
    ];

    $form['content'] = [
      '#type' => 'details',
      '#title' => $this->t('Popin content'),
      '#open' => TRUE,
    ];

    $form['content']['image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Popin image'),
      '#upload_location' => 'public://popin/',
      '#upload_validators' => [
        'file_validate_extensions' => ['gif png jpg jpeg'],
      ],
      '#default_value' => $config->get('image'),
    ];

    $form['content']['titre'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#description' => $this->t('Popin title'),
      '#maxlength' => 128,
      '#size' => 64,
      '#default_value' => $config->get('titre'),
    ];

    $form['content']['sous_titre'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subtitle'),
      '#maxlength' => 128,
      '#size' => 64,
      '#default_value' => $config->get('sous_titre'),
    ];

    $form['content']['description'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Description'),
      '#default_value' => $config->get('description'),
    ];

    $form['content']['texte_cta'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Button text'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('texte_cta'),
    ];

    $form['content']['lien_cta'] = [
      '#type' => 'url',
      '#title' => $this->t('Button link'),
      '#default_value' => $config->get('lien_cta'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $image = $form_state->getValue('image');
    if(isset($image[0])) {
      $file = File::load($image[0]);
      if ($file && $file->isTemporary()) {
        $file->setPermanent();
        \Drupal::service('file.usage')->add($file, 'popin', 'user', \Drupal::currentUser()->id());
        $file->save();
      }
    }
    $dateStart = $form_state->getValue('datestart');
    $dateEnd = $form_state->getValue('dateend');
    $this->config('popin.popinconfig')
      ->set('enabled', $form_state->getValue('enabled'))
      ->set('image', $form_state->getValue('image'))
      ->set('titre', $form_state->getValue('titre'))
      ->set('sous_titre', $form_state->getValue('sous_titre'))
      ->set('description', $form_state->getValue('description')['value'])
      ->set('texte_cta', $form_state->getValue('texte_cta'))
      ->set('cookie_random', random_int(0, 10000))
      ->set('lien_cta', $form_state->getValue('lien_cta'))
      ->set('dateend', $form_state->getValue('dateend'))
      ->set('datestart', $dateStart ? $dateStart->format('U') : NULL)
      ->set('dateend', $dateEnd ? $dateEnd->format('U') : NULL)
      ->save();

    Cache::invalidateTags(['popin']);
  }

}
