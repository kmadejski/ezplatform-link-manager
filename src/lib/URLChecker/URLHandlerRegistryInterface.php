<?php

namespace EzSystems\EzPlatformLinkManager\URLChecker;

interface URLHandlerRegistryInterface
{
    /**
     * Adds scheme handler.
     *
     * @param string $scheme
     * @param URLHandlerInterface $handler
     */
    public function addHandler($scheme, URLHandlerInterface $handler);

    /**
     * Is scheme supported ?
     *
     * @param string $scheme
     * @return bool
     */
    public function supported($scheme);

    /**
     * Returns handler for scheme.
     *
     * @param string $scheme
     * @return URLHandlerInterface
     * @throw \InvalidArgumentException When scheme isn't supported
     */
    public function getHandler($scheme);
}
