<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Repository\CommentRepository;

final class ArticleController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): RedirectResponse
    {
        return $this->redirectToRoute('article_index');
    }

    #[Route('/articles', name: 'article_index', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findBy([], ['publishedAt' => 'DESC']);

        return $this->render('article/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/articles/{id}', name: 'article_show', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function show(Post $post, Request $request, EntityManagerInterface $em, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('ROLE_USER');

            $comment->setAuthor($this->getUser());
            $comment->setPost($post);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setStatus('pending');

            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('article_show', ['id' => $post->getId()]);
        }

        $approvedComments = $commentRepository->findBy(
            ['post' => $post, 'status' => 'approved'],
            ['createdAt' => 'DESC']
        );

        return $this->render('article/show.html.twig', [
            'post' => $post,
            'commentForm' => $form->createView(),
            'comments' => $approvedComments,
        ]);
    }
}