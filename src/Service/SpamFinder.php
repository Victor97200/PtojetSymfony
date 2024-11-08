<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SpamFinder
{
    private array $spamKeywords;

    private LoggerInterface $logger;

    private RequestStack $requestStack;

    public function __construct(array $spamKeywords, LoggerInterface $logger, RequestStack $requestStack)
    {
        $this->spamKeywords = $spamKeywords;
        $this->logger = $logger;
        $this->requestStack = $requestStack;
    }

    public function isSpam(string $text): bool
    {
        $spamDetected = false;
        foreach ($this->spamKeywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                $spamDetected = true;
                break;
            }
        }

        if ($spamDetected) {
            $clientIP = $this->requestStack->getCurrentRequest() ? $this->requestStack->getCurrentRequest()->getClientIp() : 'undefined IP';
            $this->logger->info('Spam detected', ['message' => $text, 'ip' => $clientIP]);
        }

        return $spamDetected;
    }
}