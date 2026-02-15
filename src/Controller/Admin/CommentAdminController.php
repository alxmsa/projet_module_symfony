<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/comments')]
final class CommentAdminController extends AbstractController
{
    #[Route('', name: 'admin_comment_index', methods: ['GET'])]
    public function index(CommentRepository $commentRepository): Response
    {
        // filtrage direct sur pending :
        // $comments = $commentRepository->findBy(['status' => 'pending'], ['createdAt' => 'DESC']);

        $comments = $commentRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('admin/comment/index.html.twig', [
            'comments' => $comments,
        ]);
    }

    #[Route('/{id}/approve', name: 'admin_comment_approve', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function approve(Comment $comment, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('comment_action_'.$comment->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $comment->setStatus('approved');
        $em->flush();

        $this->addFlash('success', 'Comment approved.');
        return $this->redirectToRoute('admin_comment_index');
    }

    #[Route('/{id}/reject', name: 'admin_comment_reject', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function reject(Comment $comment, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('comment_action_'.$comment->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $comment->setStatus('rejected');
        $em->flush();

        $this->addFlash('success', 'Comment rejected.');
        return $this->redirectToRoute('admin_comment_index');
    }
}