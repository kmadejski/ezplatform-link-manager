<?php

namespace EzSystems\EzPlatformLinkManagerBundle\Command;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\URLChecker\URLCheckerInterface;
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
        $query = new URLQuery();
        $query->filter = new Criterion\MatchAll();
        $query->limit = -1;

        /** @var URLCheckerInterface $urlChecker */
        $urlChecker = $this->getContainer()->get('ezpublish.url_checker');
        $urlChecker->check($query);
    }
}
