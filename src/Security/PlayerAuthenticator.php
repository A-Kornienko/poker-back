<?php

namespace App\Security;

use App\Enum\UserRole;
use App\Exception\ResponseException;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\{Passport, SelfValidatingPassport};

class PlayerAuthenticator extends AbstractAuthenticator
{
    private UserService $userService;

    private UserRepository $userRepository;

    public function __construct(
        UserService $userService, 
        UserRepository $userRepository
    ) {
        $this->userService    = $userService;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws ResponseException|\Doctrine\DBAL\Exception
     */
    public function supports(Request $request): ?bool
    {
        // check if user table exists
        if(!$this->userRepository->getEntityManager()
            ->getConnection()
            ->createSchemaManager()
            ->tablesExist(
                //get table name
                $this->userRepository
                    ->getEntityManager()
                    ->getClassMetadata($this->userRepository->getClassName())
                    ->getTableName()
            )
        ) {
            return false;
        }

        global $main_user;

        return (bool) $main_user;
    }

    /**
     * @throws ResponseException
     */
    public function authenticate(Request $request): Passport
    {
        global $main_user, $language;

        $userId = $main_user->user_info['id'];
        $user   = $this->userRepository->findOneBy([
            'externalId' => $userId, 
            'role' => UserRole::Player->value 
        ]);

        if (!$user) {
            $data               = $main_user->user_info;
            $data['externalId'] = $main_user->user_info['id'];
            $user               = $this->userService->create($data);
        }

        // Updating balance and last login logic
        $user->setLastLogin(time())
            ->setLanguage($language);
        $this->userService->save($user);

        return new SelfValidatingPassport(new UserBadge($user->getId()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
