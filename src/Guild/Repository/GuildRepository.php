<?php
/**
 * Ares (https://ares.to)
 *
 * @license https://gitlab.com/arescms/ares-backend/LICENSE (MIT License)
 */

namespace Ares\Guild\Repository;

use Ares\Framework\Interfaces\SearchCriteriaInterface;
use Ares\Framework\Repository\BaseRepository;
use Ares\Guild\Entity\Guild;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Jhg\DoctrinePagination\Collection\PaginatedArrayCollection;
use Phpfastcache\Exceptions\PhpfastcacheSimpleCacheException;
use Psr\Cache\InvalidArgumentException;

/**
 * Class GuildRepository
 *
 * @package Ares\Guild\Repository
 */
class GuildRepository extends BaseRepository
{
    /** @var string */
    private const CACHE_PREFIX = 'ARES_GUILD_';

    /** @var string */
    private const CACHE_COLLECTION_PREFIX = 'ARES_GUILD_COLLECTION_';

    /** @var string */
    protected string $entity = Guild::class;

    /**
     * Get object by id.
     *
     * @param int  $id
     *
     * @param bool $cachedEntity
     *
     * @return Guild|null
     * @throws InvalidArgumentException
     * @throws PhpfastcacheSimpleCacheException
     */
    public function get(int $id, bool $cachedEntity = true): ?object
    {
        $entity = $this->cacheService->get(self::CACHE_PREFIX . $id);

        if ($entity && $cachedEntity) {
            return unserialize($entity);
        }

        $entity = $this->find($id);

        $this->cacheService->set(self::CACHE_PREFIX . $id, serialize($entity));

        return $entity;
    }

    /**
     * @param object $model
     *
     * @return object
     * @throws InvalidArgumentException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws PhpfastcacheSimpleCacheException
     */
    public function save(object $model): object
    {
        $this->getEntityManager()->persist($model);
        $this->getEntityManager()->flush();

        $this->cacheService->set(self::CACHE_COLLECTION_PREFIX . $model->getId(), serialize($model));

        return $model;
    }

    /**
     * @param  object  $model
     *
     * @return Guild
     * @throws ORMException
     * @throws PhpfastcacheSimpleCacheException
     * @throws OptimisticLockException|InvalidArgumentException
     */
    public function update(object $model): object
    {
        $this->getEntityManager()->flush();

        $this->cacheService->set(self::CACHE_PREFIX . $model->getId(), serialize($model));

        return $model;
    }

    public function profileGuilds(SearchCriteriaInterface $searchCriteria)
    {
        return $this->createPaginatedQueryBuilder()
            ->addPagination($searchCriteria->getPage(), $searchCriteria->getLimit())
            ->select('g.id, g.name, g.description, g.badge')
            ->from('Ares\Guild\Entity\Guild', 'g')
            ->join(
                'Ares\Guild\Entity\GuildMember',
                'm',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'g.id = m.guild'
            )
            ->getQuery()
            ->getResult();
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @param bool                    $cachedEntity
     *
     * @return array|object[]
     * @throws InvalidArgumentException
     * @throws PhpfastcacheSimpleCacheException
     */
    public function getList(SearchCriteriaInterface $searchCriteria, bool $cachedEntity = true)
    {
        $cacheKey = $searchCriteria->getCacheKey();

        $collection = $this->cacheService->get(self::CACHE_COLLECTION_PREFIX . $cacheKey);

        if ($collection && $cachedEntity) {
            return unserialize($collection);
        }

        $collection = $this->findBy(
            $searchCriteria->getFilters(),
            $searchCriteria->getOrders(),
            $searchCriteria->getLimit(),
            $searchCriteria->getOffset()
        );

        $this->cacheService->set(self::CACHE_COLLECTION_PREFIX . $cacheKey, serialize($collection));

        return $collection;
    }

    /**
     * Delete object by id.
     *
     * @param   int  $id
     *
     * @return bool
     * @throws InvalidArgumentException
     * @throws ORMException
     * @throws PhpfastcacheSimpleCacheException
     * @throws OptimisticLockException
     */
    public function delete(int $id): bool
    {
        $model = $this->get($id);

        if (!$model) {
            return false;
        }

        $this->getEntityManager()->remove($model);
        $this->getEntityManager()->flush();

        $this->cacheService->delete(self::CACHE_PREFIX . $id);

        return true;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @param bool                    $cachedEntity
     *
     * @return PaginatedArrayCollection
     * @throws InvalidArgumentException
     * @throws PhpfastcacheSimpleCacheException
     */
    public function paginate(SearchCriteriaInterface $searchCriteria, bool $cachedEntity = true): PaginatedArrayCollection
    {
        $cacheKey = $searchCriteria->getCacheKey();

        $collection = $this->cacheService->get(self::CACHE_COLLECTION_PREFIX . $cacheKey);

        if ($collection && $cachedEntity) {
            return unserialize($collection);
        }

        $collection = $this->findPageBy(
            $searchCriteria->getPage(),
            $searchCriteria->getLimit(),
            $searchCriteria->getFilters(),
            $searchCriteria->getOrders()
        );

        $this->cacheService->set(self::CACHE_COLLECTION_PREFIX . $cacheKey, serialize($collection));

        return $collection;
    }
}
