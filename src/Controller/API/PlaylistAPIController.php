<?php

namespace App\Controller\API;

use App\Entity\Playlist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/playlist')]
class PlaylistAPIController extends AbstractController
{
	//#[Route('/playlists', name: 'user_playlists', methods: ['GET'])]
	//public function getUserPlaylists(EntityManagerInterface $em): Response
	//{
	//	$playlists = $em->getRepository(Playlist::class)->findBy(['author' => $this->getUser()]);
	//	return $this->json([
	//		'success' => true,
	//		'playlists' => $playlists,
	//	]);
	//}

	#[Route('/playlists/trends', name: 'user_playlists', methods: ['GET'])]
	public function getTrendsPlaylist(EntityManagerInterface $em): Response
	{
		$playlists = $em->getRepository(Playlist::class)->findBy([], ['likes' => 'DESC'], 10);
		return $this->json([
			'success' => true,
			'playlists' => $playlists,
		]);
	}

	#[Route("/playlist/{playlist}", name: "get_playlist", methods: ["GET"])]
	public function getPlaylist(Playlist $playlist)
	{
		if (!$playlist->isPublic() && $playlist->getAuthor() !== $this->getUser()) {
			return $this->json([
				"success" => false,
				"message" => "Unauthorized access"
			], 403);
		}
		return $this->json([
			"success" => true,
			"playlist" => $playlist
		], 200);
	}

	#[Route("/playlist", name: "create_playlist", methods: ["POST"])]
	public function createPlaylist(Request $request)
	{
		return $this->json([]);
	}

	#[Route("/playlist/{playlist}", name: "delete_playlist", methods: ["DELETE"])]
	public function deletePlaylist(Playlist $playlist, EntityManagerInterface $em)
	{
		if ($playlist->getAuthor() !== $this->getUser()) {
			return $this->json([
				"success" => false,
				"message" => "Unauthorized access"
			], 403);
		}
		$em->remove($playlist);
		$em->flush();

		return $this->json([
			"success" => true,
			"message" => "Playlist deleted"
		], 200);
	}

	#[Route("/playlist/{playlist}", name: "update_playlist", methods: ["PUT"])]
	public function updatePlaylist(EntityManagerInterface $em)
	{
		return $this->json([]);
	}
}
