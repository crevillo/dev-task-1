<?php

namespace App\EventSubscriber;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Events\Content\PublishVersionEvent;
use eZ\Publish\API\Repository\LanguageService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddFrenchTranslation implements EventSubscriberInterface
{
    private ContentService $contentService;

    private LanguageService $languageService;

    public function __construct(ContentService $contentService, LanguageService $languageService)
    {
        $this->contentService = $contentService;
        $this->languageService = $languageService;
    }

    public static function getSubscribedEvents()
    {
        return [PublishVersionEvent::class => ['createFrenchTranslation', 0]];
    }

    public function createFrenchTranslation(PublishVersionEvent $event)
    {
        $content = $event->getContent();
        $versionInfo = $event->getVersionInfo();

        $contentTypeIdentifier = $content->getContentType()->identifier;
        $versionLanguageCode = $versionInfo->initialLanguageCode;

        $newVersionLanguage = $this->languageService->loadLanguage('fre-FR');
        // don't do anything if this is not an 'article' or if the version is not English
        if (($contentTypeIdentifier === 'article') && ($versionLanguageCode == 'eng-GB')) {
            $this->contentService->createContentDraft(
                $content->contentInfo,
                $versionInfo,
                null,
                $newVersionLanguage
            );
        }
    }
}
