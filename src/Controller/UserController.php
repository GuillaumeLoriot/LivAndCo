<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Serializer;
use App\Entity\User;

final class UserController extends AbstractController
{   
    public function __construct(readonly private NormalizerInterface $normalizer)
    {
        
    }
    #[Route('/api/me', name: 'api_get_current_user', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        /** @var User $user */
         $user = $this->getUser();
        return new JsonResponse(!$user ? ['error' => 'User not found'] : $this->normalizer->normalize($user, null, ["groups" => ["user:read:item"]]), !$user ? 401:200);
    
}
}