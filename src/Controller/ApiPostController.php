<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiPostController extends AbstractController
{
    #[Route('/api/post', name: 'api_post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, NormalizerInterface $normalizer): JsonResponse
    {
        $posts = $postRepository->findAll();
        $normalizers = [new GetSetMethodNormalizer()];
        $serializer = new Serializer($normalizers, []);
        $normalizedPosts = $serializer->normalize($posts, '', ['groups' => 'public']);

        return new JsonResponse($normalizedPosts);
    }
}
