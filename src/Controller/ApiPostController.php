<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Service\EntityUpdater;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiPostController extends AbstractController
{
    /**
     * Get post
     */
    #[Route('/api/post/{post_id}', name: 'api_get_post', defaults: ['post_id' => false], methods: ['GET'])]
    public function getPost(false|int $post_id, PostRepository $postRepository): JsonResponse
    {
        try {
            $posts = $post_id ? $postRepository->find($post_id) : $postRepository->findAll();

            if(!$posts && $post_id) {
                throw $this->createNotFoundException('Post not found.');
            }

            return $this->json($posts, 200, [], ['groups' => 'public']);
        } catch (NotFoundHttpException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Add post
     */
    #[Route('/api/post', name: 'api_add_post', methods: ['POST'])]
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
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Update post
     */
    #[Route('/api/post/{post_id}', name: 'api_update_post', methods: ['PUT'])]
    public function updatePost(int $post_id, Request $request, PostRepository $postRepository,
                               EntityManagerInterface $em, EntityUpdater $updater,
                               ValidatorInterface $validator): JsonResponse
    {
        try {
            $post = $postRepository->find($post_id);

            if(!$post) {
                throw $this->createNotFoundException('Post not found.');
            }
        } catch (NotFoundHttpException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }

        try {
            $post = $updater->update($post, json_decode($request->getContent(), true));
            $errors = $validator->validate($post);

            if(count($errors)) {
                return $this->json($errors, 400);
            }

            $em->persist($post);
            $em->flush();

            return $this->json($post, 200, [], ['groups' => 'public']);
        } catch (NotEncodableValueException $e ) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Delete post
     */
    #[Route('/api/post/{post_id}', name: 'api_delete_post', defaults: ['post_id' => false], methods: ['DELETE'])]
    public function deletePost(false|int $post_id, PostRepository $postRepository,
                               EntityManagerInterface $em): JsonResponse
    {
        try {
            $posts = $post_id ? $postRepository->findBy(['id' => $post_id]) : $postRepository->findAll();

            if(!$posts && $post_id) {
                throw $this->createNotFoundException('Post not found.');
            }

            foreach($posts as $post) {
                $em->remove($post);
            }
            $em->flush();

            return $this->json(['message' => 'Post deleted.']);
        } catch (NotFoundHttpException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}
