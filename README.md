# Popin
Primary hosting : https://www.drupal.org/project/popin

## About this module
This module allows you to display a popin (lightbox) on your website.

The popin content is composed of :
  - Title
  - Subtitle
  - Image
  - Wysiwyg Area
  - Link

All are optionals.

You can customize the fields order by editing the provided template (block-popin.html.twig).

You can configure the visibility of the popin by date, or globally.

The popin will be displayed once per user session. Update the popin configuration form will reset this setting.

## Installation

You can download this module with composer, and enable it like any other module.

After that, head to blocks structure configuration to add the « Popin block » to a region (footer for example).

(You can use block visibility settings if you want to display the popin only on the frontpage)

Finally you can configure the popin content and visibility on /admin/content/popin.


