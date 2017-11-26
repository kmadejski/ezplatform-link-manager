<?php

namespace EzSystems\EzPlatformLinkManager\URLChecker\Handler;

use EzSystems\EzPlatformLinkManager\URLChecker\URLHandlerInterface;

class MailToHandler implements URLHandlerInterface
{
    const MAILTO_PATTERN = '/^mailto:(.+)@([^?]+)(\\?.*)?$/';

    /**
     * {@inheritdoc}
     */
    public function validate(array $urls, callable $doUpdateStatus)
    {
        foreach ($urls as $url) {
            if (preg_match(self::MAILTO_PATTERN, $url->url, $matches)) {
                $host = trim($matches[2]);

                $doUpdateStatus($url, checkdnsrr($host, 'MX'));
            }
        }
    }
}
