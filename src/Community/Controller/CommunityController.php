<?php
/**
 * Ares (https://ares.to)
 *
 * @license https://gitlab.com/arescms/ares-backend/LICENSE (MIT License)
 */

namespace Ares\Community\Controller;

use Ares\Article\Repository\ArticleRepository;
use Ares\Framework\Controller\BaseController;
use Ares\Framework\Model\Adapter\DoctrineSearchCriteria;
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
     * @var DoctrineSearchCriteria
     */
    private DoctrineSearchCriteria $doctrineSearchCriteria;

    /**
     * @var GuildRepository
     */
    private GuildRepository $guildRepository;

    /**
     * @var RoomRepository
     */
    private RoomRepository $roomRepository;

    /**
     * @var ArticleRepository
     */
    private ArticleRepository $articleRepository;

    /**
     * CommunityController constructor.
     *
     * @param DoctrineSearchCriteria $doctrineSearchCriteria
     * @param GuildRepository $guildRepository
     * @param RoomRepository $roomRepository
     * @param ArticleRepository $articleRepository
     */
    public function __construct(
        DoctrineSearchCriteria $doctrineSearchCriteria,
        GuildRepository $guildRepository,
        RoomRepository $roomRepository,
        ArticleRepository $articleRepository
    ) {
        $this->doctrineSearchCriteria = $doctrineSearchCriteria;
        $this->guildRepository = $guildRepository;
        $this->roomRepository = $roomRepository;
        $this->articleRepository = $articleRepository;
    }

    /**
     * Searchs with term in groups, rooms and news.
     *
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return Response
     */
    public function search(Request $request, Response $response, $args)
    {
        /** @var string $id */
        $term = (string) $args['term'];
        $results = [];

        $results['guilds'] = $this->guildRepository->searchGuilds($term);
        $results['rooms'] = $this->roomRepository->searchRooms($term);
        $results['articles'] = $this->articleRepository->searchArticles($term);

        return $this->respond(
            $response,
            response()->setData($results)
        );
    }
}
