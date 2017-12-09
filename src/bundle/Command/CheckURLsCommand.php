<?php

namespace EzSystems\EzPlatformLinkManagerBundle\Command;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\SortClause;
use EzSystems\EzPlatformLinkManager\API\Repository\Values\URLQuery;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CheckURLsCommand extends ContainerAwareCommand
{
    const DEFAULT_ITERATION_COUNT = 50;

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('ezplatform:check-urls');
        $this->setDescription('Checks validity of external URLs');
        $this->addOption(
            'iteration-count',
            'c',
            InputOption::VALUE_OPTIONAL,
            'Number of urls to be checked in a single iteration, for avoiding using too much memory',
            self::DEFAULT_ITERATION_COUNT
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $limit = $input->getOption('iteration-count');
        if (!is_numeric($limit) || (int)$limit < 1) {
            throw new RuntimeException("'--iteration-count' option should be > 0, got '{$limit}'");
        }

        $repository = $this->getContainer()->get('ezpublish.api.repository');

        $query = new URLQuery();
        $query->filter = new Criterion\VisibleOnly();
        $query->sortClauses = [
            new SortClause\URL(),
        ];
        $query->offset = 0;
        $query->limit = $limit;

        $totalCount = $this->getTotalCount(clone $query);

        $progress = new ProgressBar($output, $totalCount);
        $progress->start();
        while ($query->offset < $totalCount) {
            $repository->sudo(function () use ($query) {
                $this->getUrlHandler()->check($query);
            });

            $progress->advance(min($limit, $totalCount - $query->offset));
            $query->offset += $limit;
        }
        $progress->finish();
    }

    private function getTotalCount(URLQuery $query)
    {
        $repository = $this->getContainer()->get('ezpublish.api.repository');
        $urlService = $this->getContainer()->get('ezpublish.api.service.url');

        $query->limit = 0;

        return $repository->sudo(function () use ($query, $urlService) {
            return $urlService->findUrls($query)->count;
        });
    }

    /**
     * @return \EzSystems\EzPlatformLinkManager\URLChecker\URLCheckerInterface
     */
    private function getUrlHandler()
    {
        return $this->getContainer()->get('ezpublish.url_checker');
    }
}
