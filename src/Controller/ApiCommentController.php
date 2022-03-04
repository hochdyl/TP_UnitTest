<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use App\Service\EntityUpdater;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiCommentController extends AbstractController
{
    /**
     * Method : GET
     * - api/comment              : Get all comments.
     * - api/comment/{comment_id} : Get a specific comment.
     */
    #[Route('/api/comment/{comment_id}', name: 'api_get_comment', defaults: ['comment_id' => false], methods: ['GET'])]
    public function getComment(false|int $comment_id, CommentRepository $commentRepository): JsonResponse
    {
        try {
            $comments = $comment_id ?
                $commentRepository->find($comment_id) :
                $commentRepository->findAll();

            if (!$comments && $comment_id) {
                throw $this->createNotFoundException('Comment not found.');
            }

            return $this->json($comments, 200, [], ['groups' => 'public']);

        } catch (NotFoundHttpException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Method : POST
     * - api/comment/{post_id} : Add a new comment to a specific post.
     *
     * Expected JSON : "content"
     */
    #[Route('/api/comment/{post_id}', name: 'api_add_comment', methods: ['POST'])]
    public function addComment(int $post_id, Request $request, PostRepository $postRepository,
                               SerializerInterface $serializer, EntityManagerInterface $em,
                               ValidatorInterface $validator): JsonResponse
    {
        try {
            $post = $postRepository->find($post_id);

            if (!$post) {
                throw $this->createNotFoundException('Post not found.');
            }

            $comment = $serializer->deserialize($request->getContent(), Comment::class, 'json');
            $comment->setPost($post);
            $errors = $validator->validate($comment);

            if (count($errors)) {
                return $this->json($errors, 400);
            }

            $em->persist($comment);
            $em->flush($comment);

            return $this->json($post, 201, [], ['groups' => 'public']);

        } catch (NotFoundHttpException|NotEncodableValueException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Method : PUT
     * - api/post/{comment_id} : Update a specific comment.
     *
     * Expected JSON : (optional) "content"
     */
    #[Route('/api/comment/{comment_id}', name: 'api_update_comment', methods: ['PUT'])]
    public function updatePost(int $comment_id, Request $request, CommentRepository $commentRepository,
                               EntityManagerInterface $em, EntityUpdater $updater,
                               ValidatorInterface $validator): JsonResponse
    {
        try {
            $comment = $commentRepository->find($comment_id);

            if (!$comment) {
                throw $this->createNotFoundException('Comment not found.');
            }

            $data = json_decode($request->getContent(), true);

            if (!$data) {
                throw new Exception('Request content is empty or wrongly formatted.');
            }

            $comment = $updater->update($comment, $data);
            $errors = $validator->validate($comment);

            if (count($errors)) {
                return $this->json($errors, 400);
            }

            $em->persist($comment);
            $em->flush();

            return $this->json($comment, 200, [], ['groups' => 'public']);

        } catch (NotFoundHttpException|Exception|NotEncodableValueException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Method : DELETE
     * - api/comment              : Delete all comment.
     * - api/comment/{comment_id} : Delete a specific comment.
     */
    #[Route('/api/comment/{comment_id}', name: 'api_delete_comment', defaults: ['comment_id' => false],
            methods: ['DELETE'])]
    public function deleteComment(false|int $comment_id, CommentRepository $commentRepository,
                                  EntityManagerInterface $em): JsonResponse
    {
        try {
            $comments = $comment_id ?
                $commentRepository->findBy(['id' => $comment_id]) :
                $commentRepository->findAll();

            if (!$comments && $comment_id) {
                throw $this->createNotFoundException('Comment not found.');
            }

            foreach ($comments as $comment) {
                $em->remove($comment);
            }
            $em->flush();

            return $this->json(['message' => 'Comment deleted.']);

        } catch (NotFoundHttpException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        }
    }
}
