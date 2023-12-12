<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/playlist')]
class PlaylistController extends AbstractController
{
	#[Route('/{userId}', name: 'user_playlists')]
	public function getUserPlaylist($userId, EntityManagerInterface $em): Response
	{
		/** @var User $user */
		$user = $em->getRepository(User::class)->find($userId);
		if (!$user || $this->getUser() !== $user) {
			return $this->redirectToRoute('app_home');
		}
		$playlists = $user->getPlaylists();
		return $this->render('playlist/index.html.twig', [
			'palylists' => $playlists,
		]);
	}
}
