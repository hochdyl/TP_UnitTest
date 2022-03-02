<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
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
    public function getPosts(PostRepository $postRepository): JsonResponse
    {
        $posts = $postRepository->findAll();

        return $this->json($posts, 200, [], ['groups' => 'public']);
    }

    /**
     * Add a new post
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
     * TODO : Update all posts
     */
    #[Route('/api/posts', name: 'api_update_posts', methods: ['PUT'])]
    public function updatePosts(PostRepository $postRepository): JsonResponse
    {
        return false;
    }

    /**
     * Delete all posts
     */
    #[Route('/api/posts', name: 'api_delete_posts', methods: ['DELETE'])]
    public function deletePosts(PostRepository $postRepository, EntityManagerInterface $em): JsonResponse
    {
        $posts = $postRepository->findAll();
        foreach($posts as $post) {
           $em->remove($post);
        }
        $em->flush();

        return $this->json([
           'message' => 'All posts have been deleted successfully.'
        ]);
    }

    /**
     * Get a post
     */
    #[Route('/api/posts/{id}', name: 'api_get_post', methods: ['GET'])]
    public function getPost($id, PostRepository $postRepository): JsonResponse
    {
        try {
            $post = $postRepository->findOneBy(['id' => $id]);

            if(!$post) {
                throw new EntityNotFoundException('Post not found.');
            }

            return $this->json($post, 200, [], ['groups' => 'public']);
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * TODO : Update a post
     */
    #[Route('/api/posts/{id}', name: 'api_update_post', methods: ['PUT'])]
    public function updatePost($id, PostRepository $postRepository): JsonResponse
    {
        return false;
    }

    /**
     * Delete a post
     */
    #[Route('/api/posts/{id}', name: 'api_delete_post', methods: ['DELETE'])]
    public function deletePost($id, PostRepository $postRepository, EntityManagerInterface $em): JsonResponse
    {
        try {
            $post = $postRepository->findOneBy(['id' => $id]);

            if(!$post) {
                throw new EntityNotFoundException('Post not found.');
            }
            $em->remove($post);
            $em->flush();

            return $this->json([
                'message' => 'The post has been deleted successfully.'
            ]);
        } catch (EntityNotFoundException $e) {
            return $this->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }
}
