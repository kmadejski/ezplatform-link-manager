<?php

namespace EzSystems\EzPlatformLinkManager\URLChecker;

interface URLHandlerInterface
{
    /**
     * Validates given list of URLs.
     *
     * @param \EzSystems\EzPlatformLinkManager\API\Repository\Values\URL[] $urls
     * @param callable $doUpdateStatus Callable executed to update URL status
     */
    public function validate(array $urls, callable $doUpdateStatus);
}
