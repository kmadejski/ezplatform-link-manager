<?php

namespace EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Gateway;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Gateway;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URL;
use Doctrine\DBAL\DBALException;
use PDOException;
use RuntimeException;

class ExceptionConversion extends Gateway
{
    /**
     * The wrapped gateway.
     *
     * @var Gateway
     */
    protected $innerGateway;

    /**
     * ExceptionConversion constructor.
     *
     * @param \EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Gateway $innerGateway
     */
    public function __construct(Gateway $innerGateway)
    {
        $this->innerGateway = $innerGateway;
    }

    /**
     * {@inheritdoc}
     */
    public function updateUrl(URL $url)
    {
        try {
            return $this->innerGateway->updateUrl($url);
        } catch (DBALException $e) {
            throw new RuntimeException('Database error', 0, $e);
        } catch (PDOException $e) {
            throw new RuntimeException('Database error', 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function find(Criterion $criterion, $offset, $limit, array $sortClauses = [], $doCount = true)
    {
        try {
            return $this->innerGateway->find($criterion, $offset, $limit, $sortClauses, $doCount);
        } catch (DBALException $e) {
            throw new RuntimeException('Database error', 0, $e);
        } catch (PDOException $e) {
            throw new RuntimeException('Database error', 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function loadUrlData($id)
    {
        try {
            return $this->innerGateway->loadUrlData($id);
        } catch (DBALException $e) {
            throw new RuntimeException('Database error', 0, $e);
        } catch (PDOException $e) {
            throw new RuntimeException('Database error', 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function loadUrlDataByUrl($url)
    {
        try {
            return $this->innerGateway->loadUrlDataByUrl($url);
        } catch (DBALException $e) {
            throw new RuntimeException('Database error', 0, $e);
        } catch (PDOException $e) {
            throw new RuntimeException('Database error', 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRelatedContentIds($id)
    {
        try {
            return $this->innerGateway->getRelatedContentIds($id);
        } catch (DBALException $e) {
            throw new RuntimeException('Database error', 0, $e);
        } catch (PDOException $e) {
            throw new RuntimeException('Database error', 0, $e);
        }
    }
}
