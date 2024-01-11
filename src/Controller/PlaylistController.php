<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Song;
use App\Entity\User;
use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Form\PlaylistUserType;
use App\Utils\CustomSerializer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

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
	public function getAllPlaylists(EntityManagerInterface $em, CustomSerializer $serializer): Response
	{
		$playlists = $em->getRepository(Playlist::class)->findAll();
		$playlists = $serializer->objectsToArray($playlists, ['tags', 'author']);

		return new JsonResponse([
			'success' => true,
			'playlists' => $playlists
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

	#[Route('/new', name: 'new_playlist')]
	public function newPlaylist(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
	{
		$body = json_decode($request->getContent(), true);
		$name = htmlspecialchars($body['name'], ENT_QUOTES | ENT_HTML5);
		$tags = $body['tags'];
		$privacy = $body['privacy'];
		$playlist = new Playlist();
		$playlist->setName($name);
		$playlist->setAuthor($this->getUser());
		$playlist->setIsPublic(!$privacy);
		$playlist->setLikes(0);
		$playlist->setCreatedAt(new \DateTime());
		$playlist->setUpdatedAt(new \DateTime());
		foreach ($tags as $tagId) {
			$tag = $em->getRepository(Tag::class)->findOneBy(['id' => $tagId]);
			$playlist->addTag($tag);
		}
		$em->persist($playlist);
		$em->flush();

		return new JsonResponse([
			'success' => true,
		]);
	}

	#[Route('/{playlist}', name: 'playlist')]
	public function getPlaylist(Playlist $playlist): Response
	{
		if (
			!$playlist->isPublic() &&
			!$this->isGranted('ROLE_ADMIN') &&
			$playlist->getAuthor() !== $this->getUser() 
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
		// $playlists = $user->getPlaylists();
		return $this->render('playlist/index.html.twig', [
			'playlist' => $playlist,
		]);
	}

	#[Route('/{playlist}/add', name: 'add_song_playlist')]
	public function addSongToPlaylist(Request $request, EntityManagerInterface $em, Playlist $playlist): Response
	{
		$body = json_decode($request->getContent(), true);
		$songName = htmlspecialchars($body['song'], ENT_QUOTES | ENT_HTML5);
		$artist = htmlspecialchars($body['artist'], ENT_QUOTES | ENT_HTML5);
		$year = htmlspecialchars($body['year'], ENT_QUOTES | ENT_HTML5);
		$url = htmlspecialchars($body['url'], ENT_QUOTES | ENT_HTML5);
		$song = new Song();
		$song->setName($songName);
		$song->setArtist($artist);
		$song->setYear($year);
		$song->setUrl($url);
		$em->persist($song);
		$playlist->addSong($song);
		$em->persist($playlist);
		$em->flush();

		return $this->render('playlist/index.html.twig', [
			'playlist' => $playlist,
		]);
	}

	#[Route('/{playlist}/delete', name: 'delete_user_playlist')]
	public function deletePlaylist(EntityManagerInterface $em, Playlist $playlist): Response
	{
		$RequestingUser = $this->getUser();
		if ($RequestingUser !== $playlist->getAuthor() && !$this->isGranted('ROLE_SUPER_ADMIN')) {
			return $this->redirectToRoute('app_home');
		}
		$em->remove($playlist);
		$em->flush();

		return new JsonResponse([
			'success' => true,
		]);
	}

	#[Route('/{playlist}/update', name: 'playlist_update')]
	public function updatePlaylist(EntityManagerInterface $em, Request $request, Playlist $playlist): Response
	{
		$RequestingUser = $this->getUser();
		if ($RequestingUser !== $playlist->getAuthor() && !$this->isGranted('ROLE_SUPER_ADMIN')) {
			return $this->redirectToRoute('app_home');
		}

		$form = $this->createForm(PlaylistUserType::class, $playlist);
		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$playlist = $form->getData();
			$name = $form->get('name')->getData();
			$playlist->setName(htmlspecialchars($name, ENT_QUOTES | ENT_HTML5));
			$playlist->setUpdatedAt(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
			$em->persist($playlist);
			$em->flush();
			return $this->redirectToRoute('app_lab');
		}
		$em->persist($playlist);
		$em->flush();
		return $this->render('form/update-playlist-user.html.twig', [
			'form' => $form->createView(),
			'playlist' => $playlist,
		]);
	}
}
