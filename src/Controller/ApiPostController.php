<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Service\EntityUpdater;
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
     * Get post
     */
    #[Route('/api/posts/{id}', name: 'api_get_post', defaults: ['id' => false], methods: ['GET'])]
    public function getPost(false|int $id, PostRepository $postRepository): JsonResponse
    {
        $posts = $id ? $postRepository->find($id) : $postRepository->findAll();

        return $this->json($posts, 200, [], ['groups' => 'public']);
    }

    /**
     * Add post
     */
    #[Route('/api/posts', name: 'api_add_post', methods: ['POST'])]
    public function addPost(Request $request, SerializerInterface $serializer, EntityManagerInterface $em,
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

    /**
     * Update post
     */
    #[Route('/api/posts/{id}', name: 'api_update_post', defaults: ['id' => false], methods: ['PUT'])]
    public function updatePost(false|int $id, Request $request, PostRepository $postRepository,
                               EntityManagerInterface $em, EntityUpdater $updater,
                               ValidatorInterface $validator): JsonResponse
    {
        try {
            $posts = $id ? $postRepository->findBy(['id' => $id]) : $postRepository->findAll();
            $posts = $updater->update($posts, json_decode($request->getContent(), true));
            $errors = $validator->validate($posts);

            if(count($errors)) {
                return $this->json($errors, 400);
            }

            foreach($posts as $post) {
                $em->persist($post);
            }
            $em->flush();

            return $this->json($posts, 200, [], ['groups' => 'public']);
        } catch (NotEncodableValueException $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Delete post
     */
    #[Route('/api/posts/{id}', name: 'api_delete_post', defaults: ['id' => false], methods: ['DELETE'])]
    public function deletePost(false|int $id, PostRepository $postRepository, EntityManagerInterface $em): JsonResponse
    {
        $posts = $id ? $postRepository->findBy(['id' => $id]) : $postRepository->findAll();

        if(!$posts) {
            $this->createNotFoundException('Nothing to delete.');
        }

        foreach($posts as $post) {
           $em->remove($post);
        }
        $em->flush();

        return $this->json([
           'message' => 'Post deleted.'
        ]);
    }
}
