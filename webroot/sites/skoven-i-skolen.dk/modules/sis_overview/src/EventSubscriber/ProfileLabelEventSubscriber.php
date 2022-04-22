<?php

namespace Drupal\sis_overview\EventSubscriber;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\profile\Event\ProfileEvents;
use Drupal\profile\Event\ProfileLabelEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProfileLabelEventSubscriber implements EventSubscriberInterface {

  use StringTranslationTrait;

  public static function getSubscribedEvents() {
    return [
      ProfileEvents::PROFILE_LABEL => 'profileLabel'
    ];
  }

  /**
   * Subscribe to the user login event dispatched
   *
   * @param ProfileLabelEvent $event
   *
   * @return ProfileLabelEvent
   */
  public function profileLabel( ProfileLabelEvent $event )
  {
    $event->setLabel($this->t('@owner', [
      '@owner' => $event->getProfile()->get('field_organization_address')->organization,
    ]));
    return $event;
  }
}
