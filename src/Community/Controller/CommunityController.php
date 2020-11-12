<?php
/**
 * Ares (https://ares.to)
 *
 * @license https://gitlab.com/arescms/ares-backend/LICENSE (MIT License)
 */

namespace Ares\Community\Controller;

use Ares\Article\Repository\ArticleRepository;
use Ares\Framework\Controller\BaseController;
use Ares\Framework\Exception\DataObjectManagerException;
use Ares\Guild\Repository\GuildRepository;
use Ares\Room\Repository\RoomRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class CommunityController
 *
 * @package Ares\Community\Controller
 */
class CommunityController extends BaseController
{
    /**
     * CommunityController constructor.
     *
     * @param   GuildRepository    $guildRepository
     * @param   RoomRepository     $roomRepository
     * @param   ArticleRepository  $articleRepository
     */
    public function __construct(
        private GuildRepository $guildRepository,
        private RoomRepository $roomRepository,
        private ArticleRepository $articleRepository
    ) {}

    /**
     * Searches with term in groups, rooms and news.
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     * @throws DataObjectManagerException
     */
    public function searchRooms(Request $request, Response $response, array $args): Response
    {
        /** @var string $term */
        $term = $args['term'];

        /** @var int $page */
        $page = $args['page'];

        /** @var int $resultPerPage */
        $resultPerPage = $args['rpp'];

        $rooms = $this->roomRepository
            ->searchRooms(
                $term,
                (int) $page,
                (int) $resultPerPage
            );

        return $this->respond(
            $response,
            response()
                ->setData($rooms)
        );
    }

    /**
     * Searches with term in groups, rooms and news.
     *
     * @param Request  $request
     * @param Response $response
     * @param array    $args
     *
     * @return Response
     * @throws DataObjectManagerException
     */
    public function searchGuilds(Request $request, Response $response, array $args): Response
    {
        /** @var string $term */
        $term = $args['term'];

        /** @var int $page */
        $page = $args['page'];

        /** @var int $resultPerPage */
        $resultPerPage = $args['rpp'];

        $guilds = $this->guildRepository
            ->searchGuilds(
                $term,
                (int) $page,
                (int) $resultPerPage
            );

        return $this->respond(
            $response,
            response()
                ->setData($guilds)
        );
    }

    /**
     * Searches with term in groups, rooms and news.
     *
     * @param Request     $request
     * @param Response    $response
     * @param             $args
     *
     * @return Response
     * @throws DataObjectManagerException
     */
    public function searchArticles(Request $request, Response $response, array $args): Response
    {
        /** @var string $term */
        $term = $args['term'];

        /** @var int $page */
        $page = $args['page'];

        /** @var int $resultPerPage */
        $resultPerPage = $args['rpp'];

        $articles = $this->articleRepository
            ->searchArticles(
                $term,
                (int) $page,
                (int) $resultPerPage
            );

        return $this->respond(
            $response,
            response()
                ->setData($articles)
        );
    }
}
