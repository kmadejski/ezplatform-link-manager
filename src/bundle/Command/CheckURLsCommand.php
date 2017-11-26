<?php

namespace EzSystems\EzPlatformLinkManagerBundle\Command;

use EzSystems\EzPlatformLinkManager\URLChecker\URLCheckerInterface;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\MatchAll;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckURLsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('ezplatform:check-urls');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var URLCheckerInterface $urlChecker */
        $urlChecker = $this->getContainer()->get('ezpublish.url_checker');
        $urlChecker->check(new MatchAll());
    }
}
