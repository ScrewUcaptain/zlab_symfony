<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Song;
use App\Entity\User;
use App\Entity\Playlist;
use App\Utils\CustomSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/playlist')]
class PlaylistController extends AbstractController
{
	#[Route('/', name: 'user_playlists')]
	public function getUserPlaylist(EntityManagerInterface $em): Response
	{
		$userId = $this->getUser()->getUserIdentifier();
		if (!$userId) {
			return $this->redirectToRoute('app_login');
		}
		/** @var User $user */
		$user = $em->getRepository(User::class)->findOneBy(['email' => $userId]);
		if (!$user || $this->getUser() !== $user) {
			return $this->redirectToRoute('app_home');
		}
		$playlists = $user->getPlaylists();
		return $this->render('playlist/index.html.twig', [
			'palylists' => $playlists,
		]);
	}

	#[Route('/all', name: 'all_playlists')]
	public function getAllPlaylists(EntityManagerInterface $em, CustomSerializer $serializer)
	{
		$playlists = $em->getRepository(Playlist::class)->findBy(["isPublic" => true], ['likes' => 'DESC']);

		$test = $serializer->objectsToArray($playlists, ['author']);

		return new JsonResponse([
			'success' => true,
			'playlists' => $test
		]);
	}

	#[Route('/tag/{tag}', name: 'tag_playlists')]
	public function getPlaylistsTag(Tag $tag, EntityManagerInterface $em, CustomSerializer $serializer)
	{
		$playlists = $em->getRepository(Playlist::class)->findByTag($tag);

		$test = $serializer->objectsToArray($playlists, ['tags', 'author']);

		return new JsonResponse([
			'success' => true,
			'playlists' => $test
		]);
	}

	#[Route('/{playlist}', name: 'playlist')]
	public function getPlaylist(Playlist $playlist): Response
	{
		if (
			!$playlist->isPublic() &&
			$playlist->getAuthor() !== $this->getUser() &&
			!$this->isGranted('ROLE_ADMIN')
		) {
			return $this->redirectToRoute('app_home');
		}
		return $this->render('playlist/index.html.twig', [
			'playlist' => $playlist,
		]);
	}

	#[Route('/user/playlists', name: 'user_playlists')]
	public function getUserPlaylists(EntityManagerInterface $em, Playlist $playlist): Response
	{
		$userId = $this->getUser()->getUserIdentifier();
		if (!$userId) {
			return $this->redirectToRoute('app_login');
		}
		/** @var User $user */
		$user = $em->getRepository(User::class)->findOneBy(['email' => $userId]);
		if (!$user || $this->getUser() !== $user) {
			return $this->redirectToRoute('app_home');
		}
		$playlists = $user->getPlaylists();
		return $this->render('playlist/index.html.twig', [
			'playlist' => $playlist,
		]);
	}

	#[Route('/{playlist}/add', name: 'add_song_playlist')]
	public function addSongToPlaylist(Request $request, Playlist $playlist): Response
	{
		$body = json_decode($request->getContent(), true);
		$songName = htmlspecialchars($body['song'], ENT_QUOTES | ENT_HTML5);
		dd($songName);
		$song = new Song();
		$song->setName($body['song']);
		return $this->render('playlist/index.html.twig', [
			'playlist' => $playlist,
		]);
	}
}
