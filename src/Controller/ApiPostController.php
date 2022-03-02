<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiPostController extends AbstractController
{
    /**
     * Get all posts
     */
    #[Route('/api/posts', name: 'api_get_posts', methods: ['GET'])]
    public function get(PostRepository $postRepository): JsonResponse
    {
        $posts = $postRepository->findAll();

        return $this->json($posts, 200, [], ['groups' => 'public']);
    }

    /**
     * Add a new post
     */
    #[Route('/api/posts', name: 'api_add_post', methods: ['POST'])]
    public function add(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
                        ValidatorInterface $validator): JsonResponse
    {
        try {
            $post = $serializer->deserialize($request->getContent(), Post::class, 'json');
            $errors = $validator->validate($post);

            if(count($errors)) {
                return $this->json($errors, 400);
            }

            $em->persist($post);
            $em->flush($post);

            return $this->json($post, 201, [], ['groups' => 'public']);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
