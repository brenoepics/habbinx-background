<?php declare(strict_types=1);
/**
 * Ares (https://ares.to)
 *
 * @license https://gitlab.com/arescms/ares-backend/LICENSE (MIT License)
 */

namespace Ares\User\Repository;

use Ares\Framework\Repository\BaseRepository;
use Ares\User\Entity\UserOfTheWeek;

/**
 * Class UserOfTheWeekRepository
 *
 * @package Ares\User\Repository
 */
class UserOfTheWeekRepository extends BaseRepository
{
    /** @var string */
    protected string $cachePrefix = 'ARES_UOTW_';

    /** @var string */
    protected string $cacheCollectionPrefix = 'ARES_UOTW_COLLECTION_';

    /** @var string */
    protected string $entity = UserOfTheWeek::class;
}
