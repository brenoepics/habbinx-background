<?php
/**
 * Ares (https://ares.to)
 *
 * @license https://gitlab.com/arescms/ares-backend/LICENSE (MIT License)
 */

namespace Ares\Rcon\Service;

use Ares\Framework\Exception\DataObjectManagerException;
use Ares\Framework\Interfaces\CustomResponseInterface;
use Ares\Rcon\Entity\Rcon;
use Ares\Rcon\Exception\RconException;
use Ares\Rcon\Repository\RconRepository;

/**
 * Class CreateRconCommandService
 *
 * @package Ares\Rcon\Service
 */
class CreateRconCommandService
{
    /**
     * CreateRconCommandService constructor.
     *
     * @param RconRepository $rconRepository
     */
    public function __construct(
        private RconRepository $rconRepository
    ) {}

    /**
     * @param array $data
     *
     * @return CustomResponseInterface
     * @throws RconException
     * @throws DataObjectManagerException
     */
    public function execute(array $data): CustomResponseInterface
    {
        /** @var Rcon $existingCommand */
        $existingCommand = $this->rconRepository->get($data['command'], 'command');

        if ($existingCommand) {
            throw new RconException(__('There is already an existing Command'));
        }

        $command = $this->getNewCommand($data);

        /** @var Rcon $command */
        $command = $this->rconRepository->save($command);

        return response()
            ->setData($command);
    }

    /**
     * @param array $data
     *
     * @return Rcon
     */
    private function getNewCommand(array $data): Rcon
    {
        $rconCommand = new Rcon();

        return $rconCommand
            ->setCommand($data['command'])
            ->setTitle($data['title'])
            ->setDescription($data['description']);
    }
}
