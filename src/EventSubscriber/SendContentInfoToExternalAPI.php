<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Events\Content\PublishVersionEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SendContentInfoToExternalAPI implements EventSubscriberInterface
{
    private HttpClientInterface $client;

    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [PublishVersionEvent::class => ['sendInfoToAPI', 20]];
    }

    public function sendInfoToAPI(PublishVersionEvent $event)
    {
        $content = $event->getContent();
        $versionInfo = $event->getVersionInfo();

        $contentTypeIdentifier = $content->getContentType()->identifier;
        $versionLanguageCode = $versionInfo->initialLanguageCode;

        // don't do anything if this is not an 'article' or if the version is not English
        if (($contentTypeIdentifier === 'article') && ($versionLanguageCode == 'eng-GB')) {
            $body = [
                'name' => $content->getName(),
                'contentId' => $content->id,
                'locationId' => $content->contentInfo->mainLocationId,
                'languageCode'=> $content->contentInfo->mainLanguageCode
            ];

            try {
                $this->client->request(
                    'POST',
                    'http://example.com/api/foo',
                    [
                        'headers' => [
                            'Accept' => 'application/json'
                        ],
                        'body' => $body
                    ]
                );
            } catch (ClientExceptionInterface $exception) {
                print $exception->getMessage();die;
                $this->logger->error($exception->getMessage());
            }
        }
    }
}
