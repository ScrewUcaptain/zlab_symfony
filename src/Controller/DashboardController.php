<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Song;
use App\Entity\User;
use App\Entity\Playlist;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/dashboard', name: 'admin_')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'dashboard')]
	public function dashboard(#[CurrentUser] User $user, EntityManagerInterface $em): Response
	{

		if (!$this->isGranted('ROLE_ADMIN')) {
			return $this->redirectToRoute('app_home');
		}
        $nbrUsers = $em->getRepository(User::class)->count([]);
        $nbrPlaylists =  $em->getRepository(Playlist::class)->count([]);
        $nbrSongs = $em->getRepository(Song::class)->count([]);
        $nbrTags = $em->getRepository(Tag::class)->count([]);
        $mostLikedPlaylist = $em->getRepository(Playlist::class)->findOneBy([], ['likes' => 'DESC']);

		return $this->render('dashboard/index.html.twig', [
            'nbrUsers' => $nbrUsers,
            'nbrPlaylists' => $nbrPlaylists,
            'nbrSongs' => $nbrSongs,
            'nbrTags' => $nbrTags,
            'mostLikedPlaylist' => $mostLikedPlaylist,
		]);
	}

    #[Route('/users', name: 'users')]
    public function dashboardUsers(EntityManagerInterface $em)
    {
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('dashboard/users.html.twig', [
            'users' => $users,
        ]); 
    }
    #[Route('/playlists', name: 'playlists')]
    public function dashboardPlaylists(EntityManagerInterface $em)
    {
        $playlists = $em->getRepository(Playlist::class)->findAll();

        return $this->render('dashboard/playlists.html.twig', [
            'playlists' => $playlists,
        ]); 
    }
    #[Route('/songs', name: 'songs')]
    public function dashboardSongs(EntityManagerInterface $em)
    {
        $songs = $em->getRepository(Song::class)->findAll();

        return $this->render('dashboard/songs.html.twig', [
            'songs' => $songs,
        ]); 
    }
    #[Route('/tags', name: 'tags')]
    public function dashboardTags(EntityManagerInterface $em)
    {
        $tags = $em->getRepository(Tag::class)->findAll();

        return $this->render('dashboard/tags.html.twig', [
            'tags' => $tags,
        ]); 
    }
}
