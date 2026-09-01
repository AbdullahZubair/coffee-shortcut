<?php

declare(strict_types=1);

namespace Drupal\coffee_shortcut\Hook;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Attaches the configurable Coffee shortcut behavior.
 */
final class CoffeeShortcutHooks {

  public function __construct(
    private readonly AccountProxyInterface $currentUser,
    private readonly ConfigFactoryInterface $configFactory,
  ) {}

  /**
   * Implements hook_page_attachments().
   *
   * Attaches the shortcut script for the same users Coffee itself is
   * available to, and passes this site's chosen shortcut settings to it.
   * Coffee's own file is never modified, so this keeps working across
   * Coffee updates.
   */
  #[Hook('page_attachments')]
  public function pageAttachments(array &$attachments): void {
    if (!$this->currentUser->hasPermission('access coffee')) {
      return;
    }

    $config = $this->configFactory->get('coffee_shortcut.settings');
    $attachments['#attached']['library'][] = 'coffee_shortcut/shortcut';
    $attachments['#attached']['drupalSettings']['coffeeShortcut'] = [
      'blockDefault' => (bool) $config->get('block_default_shortcut'),
      'custom' => $config->get('custom_shortcut'),
    ];
    $attachments['#cache']['tags'] = Cache::mergeTags(
      $attachments['#cache']['tags'] ?? [],
      $config->getCacheTags()
    );
  }

}
