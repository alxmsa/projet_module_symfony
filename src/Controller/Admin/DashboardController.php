<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class DashboardController extends AbstractController
{
    #[Route('', name: 'admin_dashboard', methods: ['GET'])]
    public function index(
        UserRepository $userRepository,
        PostRepository $postRepository,
        CommentRepository $commentRepository
    ): Response {

        $totalUsers = $userRepository->count([]);
        $pendingUsers = $userRepository->count(['isActive' => false]);
        $activeUsers = $userRepository->count(['isActive' => true]);

        $totalPosts = $postRepository->count([]);
        $totalComments = $commentRepository->count([]);

        return $this->render('admin/dashboard/index.html.twig', [
            'totalUsers' => $totalUsers,
            'pendingUsers' => $pendingUsers,
            'activeUsers' => $activeUsers,
            'totalPosts' => $totalPosts,
            'totalComments' => $totalComments,
        ]);
    }
}