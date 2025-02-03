<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class SpamFinder
{
    private $logger;
    private $requestStack;
    private $spamWords;

    public function __construct(LoggerInterface $logger, RequestStack $requestStack, array $spamWords = ['aaaaa', 'sdfsdf'])
    {
        $this->logger = $logger;
        $this->requestStack = $requestStack;
        $this->spamWords = $spamWords;
    }

    public function isSpam(string $text): bool
    {
        foreach ($this->spamWords as $word) {
            if (strpos($text, $word) !== false) {
                $this->logSpam($text);
                return true;
            }
        }
        return false;
    }

    private function logSpam(string $message)
    {
        $request = $this->requestStack->getCurrentRequest();
        $ip = $request ? $request->getClientIp() : 'unknown';
        $this->logger->info("Spam detected: $message from IP: $ip");
    }
}