<?php

namespace Drupal\popin\Form;

use Drupal\Core\Cache\Cache;
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
    $form['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Popin Activée ?'),
      '#default_value' => $config->get('enabled'),
    ];
    $form['image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Image'),
      '#upload_location' => 'public://popin/',
      '#upload_validators' => [
        'file_validate_extensions' => ['gif png jpg jpeg'],
      ],
      '#default_value' => $config->get('image'),
    ];
    $form['titre'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Titre'),
      '#description' => $this->t('Titre de la popin'),
      '#maxlength' => 128,
      '#size' => 64,
      '#default_value' => $config->get('titre'),
    ];
    $form['sous_titre'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Sous-titre'),
      '#description' => $this->t('Sous-titre de la popin'),
      '#maxlength' => 128,
      '#size' => 64,
      '#default_value' => $config->get('sous_titre'),
    ];
    $form['description'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Description'),
      '#default_value' => $config->get('description'),
    ];
    $form['texte_cta'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Texte CTA'),
      '#description' => $this->t('Sera affiché sur le bouton'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('texte_cta'),
    ];
    $form['lien_cta'] = [
      '#type' => 'url',
      '#title' => $this->t('Lien CTA'),
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
    $this->config('popin.popinconfig')
      ->set('enabled', $form_state->getValue('enabled'))
      ->set('image', $form_state->getValue('image'))
      ->set('titre', $form_state->getValue('titre'))
      ->set('sous_titre', $form_state->getValue('sous_titre'))
      ->set('description', $form_state->getValue('description')['value'])
      ->set('texte_cta', $form_state->getValue('texte_cta'))
      ->set('lien_cta', $form_state->getValue('lien_cta'))
      ->save();

    Cache::invalidateTags(['popin']);
  }

}
