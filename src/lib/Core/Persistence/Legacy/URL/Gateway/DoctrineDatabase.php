<?php

namespace EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Gateway;

use EzSystems\EzPlatformLinkManager\API\Repository\Values\Query\Criterion;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Gateway;
use EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriteriaConverter;
use EzSystems\EzPlatformLinkManager\SPI\Persistence\URL\URL;
use eZ\Publish\Core\Persistence\Database\DatabaseHandler;
use PDO;

class DoctrineDatabase extends Gateway
{
    const URL_TABLE = 'ezurl';
    const URL_LINK_TABLE = 'ezurl_object_link';

    const COLUMN_ID = 'id';
    const COLUMN_URL = 'url';
    const COLUMN_ORIGINAL_URL_MD5 = 'original_url_md5';
    const COLUMN_IS_VALID = 'is_valid';
    const COLUMN_LAST_CHECKED = 'last_checked';
    const COLUMN_MODIFIED = 'modified';
    const COLUMN_CREATED = 'created';

    /**
     * Database handler.
     *
     * @param \eZ\Publish\Core\Persistence\Database\DatabaseHandler $dbHandler
     */
    protected $handler;

    /**
     * Criteria converter.
     *
     * @var \EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriteriaConverter
     */
    protected $criteriaConverter;

    /**
     * Creates a new Doctrine database Section Gateway.
     *
     * @param \eZ\Publish\Core\Persistence\Database\DatabaseHandler $dbHandler
     * @param \EzSystems\EzPlatformLinkManager\Core\Persistence\Legacy\URL\Query\CriteriaConverter $criteriaConverter
     */
    public function __construct(DatabaseHandler $dbHandler, CriteriaConverter $criteriaConverter)
    {
        $this->handler = $dbHandler;
        $this->criteriaConverter = $criteriaConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function find(Criterion $criterion, $offset, $limit, $doCount = true)
    {
        $count = $doCount ? $this->doCount($criterion) : null;
        if (!$doCount && $limit === 0) {
            throw new \RuntimeException('Invalid query, can not disable count and request 0 items at the same time');
        }

        if ($limit === 0 || ($count !== null && $count <= $offset)) {
            return [
                'count' => $count,
                'rows' => [],
            ];
        }

        $query = $this->createSelectQuery();
        $query->where($this->criteriaConverter->convertCriteria($query, $criterion));
        $query->limit($limit > 0 ? $limit : PHP_INT_MAX, $offset);

        $statement = $query->prepare();
        $statement->execute();

        return [
            'count' => $count,
            'rows' => $statement->fetchAll(PDO::FETCH_ASSOC),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getRelatedContentIds($id)
    {
        $query = $this->handler->createSelectQuery();
        $query->selectDistinct(
            $this->handler->quoteColumn('id', 'ezcontentobject')
        )->from(
            $this->handler->quoteTable('ezcontentobject')
        )->innerJoin(
            $this->handler->quoteTable('ezcontentobject_attribute'),
            $query->expr->lAnd(
                $query->expr->eq(
                    $this->handler->quoteColumn('id', 'ezcontentobject'),
                    $this->handler->quoteColumn(
                        'contentobject_id',
                        'ezcontentobject_attribute'
                    )
                ),
                $query->expr->eq(
                    $this->handler->quoteColumn('current_version', 'ezcontentobject'),
                    $this->handler->quoteColumn('version', 'ezcontentobject_attribute')
                )
            )
        )->innerJoin(
            $this->handler->quoteTable(self::URL_LINK_TABLE),
            $query->expr->lAnd(
                $query->expr->eq(
                    $this->handler->quoteColumn('id', 'ezcontentobject_attribute'),
                    $this->handler->quoteColumn('contentobject_attribute_id', self::URL_LINK_TABLE)
                ),
                $query->expr->eq(
                    $this->handler->quoteColumn('version', 'ezcontentobject_attribute'),
                    $this->handler->quoteColumn('contentobject_attribute_version', self::URL_LINK_TABLE)
                )
            )
        )->where(
            $query->expr->eq(
                $this->handler->quoteColumn('url_id', self::URL_LINK_TABLE),
                $query->bindValue($id, null, PDO::PARAM_INT)
            )
        );

        $statement = $query->prepare();
        $statement->execute();

        return array_column($statement->fetchAll(PDO::FETCH_ASSOC), 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function insertUrl(URL $url)
    {
        $query = $this->handler->createInsertQuery();
        $query->insertInto(
            $this->handler->quoteTable(self::URL_TABLE)
        )->set(
            $this->handler->quoteColumn(self::COLUMN_URL),
            $query->bindValue($url->url)
        )->set(
            $this->handler->quoteColumn(self::COLUMN_ORIGINAL_URL_MD5),
            $query->bindValue($url->originalUrlMd5)
        )->set(
            $this->handler->quoteColumn(self::COLUMN_CREATED),
            $query->bindValue($url->created, null, PDO::PARAM_INT)
        )->set(
            $this->handler->quoteColumn(self::COLUMN_MODIFIED),
            $query->bindValue($url->modified, null, PDO::PARAM_INT)
        );

        $query->prepare()->execute();

        return $this->handler->lastInsertId(
            $this->handler->getSequenceName(self::URL_TABLE, 'id')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function updateUrl(URL $url)
    {
        $query = $this->handler->createUpdateQuery();
        $query->update(
            $this->handler->quoteTable(self::URL_TABLE)
        )->set(
            $this->handler->quoteColumn(self::COLUMN_URL),
            $query->bindValue($url->url)
        )->set(
            $this->handler->quoteColumn(self::COLUMN_ORIGINAL_URL_MD5),
            $query->bindValue($url->originalUrlMd5)
        )->set(
            $this->handler->quoteColumn(self::COLUMN_MODIFIED),
            $query->bindValue($url->modified, null, PDO::PARAM_INT)
        )->set(
            $this->handler->quoteColumn(self::COLUMN_IS_VALID),
            $query->bindValue($url->isValid, null, PDO::PARAM_BOOL)
        )->set(
            $this->handler->quoteColumn(self::COLUMN_LAST_CHECKED),
            $query->bindValue($url->lastChecked, null, PDO::PARAM_INT)
        )->where(
            $query->expr->eq(
                $this->handler->quoteColumn(self::COLUMN_ID),
                $query->bindValue($url->id, null, PDO::PARAM_INT)
            )
        );

        $query->prepare()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function loadUrlData($id)
    {
        $query = $this->createSelectQuery();
        $query->where(
            $query->expr->eq(
                $this->handler->quoteColumn(self::COLUMN_ID),
                $query->bindValue($id, null, PDO::PARAM_INT)
            )
        );

        $statement = $query->prepare();
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * {@inheritdoc}
     */
    protected function doCount(Criterion $criterion)
    {
        $columnName = $this->handler->quoteColumn(self::COLUMN_ID, self::URL_TABLE);

        $query = $this->handler->createSelectQuery();
        $query
            ->select("COUNT(DISTINCT $columnName)")
            ->from($this->handler->quoteTable(self::URL_TABLE))
            ->where($this->criteriaConverter->convertCriteria($query, $criterion));

        $statement = $query->prepare();
        $statement->execute();

        return (int)$statement->fetchColumn();
    }

    /**
     * Creates a Url find query.
     *
     * @return \eZ\Publish\Core\Persistence\Database\SelectQuery
     */
    protected function createSelectQuery()
    {
        $query = $this->handler->createSelectQuery();
        $query->select(
            $this->handler->quoteColumn(self::COLUMN_ID),
            $this->handler->quoteColumn(self::COLUMN_URL),
            $this->handler->quoteColumn(self::COLUMN_ORIGINAL_URL_MD5),
            $this->handler->quoteColumn(self::COLUMN_IS_VALID),
            $this->handler->quoteColumn(self::COLUMN_LAST_CHECKED),
            $this->handler->quoteColumn(self::COLUMN_CREATED),
            $this->handler->quoteColumn(self::COLUMN_MODIFIED)
        )->from(
            $this->handler->quoteTable(self::URL_TABLE)
        );

        return $query;
    }
}
