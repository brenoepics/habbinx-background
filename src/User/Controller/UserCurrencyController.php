<?php
/**
 * Ares (https://ares.to)
 *
 * @license https://gitlab.com/arescms/ares-backend/LICENSE (MIT License)
 */

namespace Ares\User\Controller;

use Ares\Framework\Controller\BaseController;
use Ares\Framework\Exception\ValidationException;
use Ares\Framework\Service\ValidationService;
use Ares\User\Exception\UserCurrencyException;
use Ares\User\Service\Currency\UpdateCurrencyService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class UserCurrencyController
 *
 * @package Ares\User\Controller
 */
class UserCurrencyController extends BaseController
{
    /**
     * UserCurrencyController constructor.
     *
     * @param   ValidationService      $validationService
     * @param   UpdateCurrencyService  $updateCurrencyService
     */
    public function __construct(
        private ValidationService $validationService,
        private UpdateCurrencyService $updateCurrencyService
    ) {}

    /**
     * Updates user currency by given data.
     *
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     * @throws UserCurrencyException
     * @throws ValidationException
     */
    public function update(Request $request, Response $response)
    {
        /** @var array $parsedData */
        $parsedData = $request->getParsedBody();

        $this->validationService->validate($parsedData, [
            'user_id' => 'required',
            'type'    => 'required',
            'amount'  => 'required'
        ]);

        $this->updateCurrencyService->execute(
            (int) $parsedData['user_id'],
            (int) $parsedData['type'],
            (int) $parsedData['amount']
        );

        return $this->respond(
            $response,
            response()
                ->setData(true)
        );
    }
}
