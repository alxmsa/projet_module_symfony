<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/users')]
final class UserAdminController extends AbstractController
{
    #[Route('', name: 'admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user_admin/index.html.twig', [
            'users' => $userRepository->findBy([], ['id' => 'DESC']),
        ]);
    }

    #[Route('/{id}/toggle-active', name: 'admin_user_toggle_active', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function toggleActive(User $user, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('toggle_active_'.$user->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        if ($user === $this->getUser()) {
            $this->addFlash('warning', 'You cannot disable your own account.');
            return $this->redirectToRoute('admin_user_index');
        }

        $user->setIsActive(!$user->isActive());
        $em->flush();

        $this->addFlash('success', 'User status updated.');
        return $this->redirectToRoute('admin_user_index');
    }
}