<?php
/**
 * Ares (https://ares.to)
 *
 * @license https://gitlab.com/arescms/ares-backend/LICENSE (MIT License)
 */

namespace Ares\Rcon\Service;

use Ares\Framework\Interfaces\CustomResponseInterface;
use Ares\Rcon\Exception\RconException;
use Ares\Rcon\Model\Rcon;
use Ares\Rcon\Repository\RconRepository;
use Ares\Role\Exception\RoleException;
use Ares\Role\Service\CheckAccessService;
use Doctrine\ORM\Query\QueryException;
use JsonException;

/**
 * Class ExecuteRconCommandService
 *
 * @package Ares\Rcon\Service
 */
class ExecuteRconCommandService
{
    /**
     * @var RconRepository
     */
    private RconRepository $rconRepository;

    /**
     * @var Rcon
     */
    private Rcon $rcon;

    /**
     * @var CheckAccessService
     */
    private CheckAccessService $checkAccessService;

    /**
     * ExecuteRconCommandService constructor.
     *
     * @param RconRepository     $rconRepository
     * @param Rcon               $rcon
     * @param CheckAccessService $checkAccessService
     */
    public function __construct(
        RconRepository $rconRepository,
        Rcon $rcon,
        CheckAccessService $checkAccessService
    ) {
        $this->rconRepository = $rconRepository;
        $this->rcon = $rcon;
        $this->checkAccessService = $checkAccessService;
    }

    /**
     * @param int   $userId
     * @param array $data
     *
     * @return CustomResponseInterface
     * @throws JsonException
     * @throws QueryException
     * @throws RconException
     * @throws RoleException
     */
    public function execute(int $userId, array $data): CustomResponseInterface
    {
        /** @var \Ares\Rcon\Entity\Rcon $existingCommand */
        $existingCommand = $this->rconRepository->getOneBy([
            'command' => $data['command']
        ]);

        if (!$existingCommand) {
            throw new RconException(__('Could not found the given command to execute'), 404);
        }

        // @Todo Can be refactored dont hate me pls
        if ($existingCommand->getPermission() === null) {
            $permissionName = null;
        } else {
            $permissionName = $existingCommand
                ->getPermission()
                ->getName();
        }

        $hasAccess = $this->checkAccessService->execute($userId, $permissionName);

        if (!$hasAccess) {
            throw new RoleException(__('You dont have the special rights to execute that action'));
        }

        $executeCommand = $this->rcon
            ->buildConnection()
            ->sendCommand(
                $this->rcon->getSocket(),
                $data['command'],
                $data['param'] ?? null,
                $data['value'] ?? null
            );

        return response()
            ->setData($executeCommand);
    }
}