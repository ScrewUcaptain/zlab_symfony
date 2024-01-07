<?php

namespace App\Controller;

use App\Entity\Playlist;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
	#[Route('/', name: 'app_home')]
	public function index(EntityManagerInterface $em): Response
	{
		$playlists = $em->getRepository(Playlist::class)->findBy(['isPublic' => true], ['likes' => 'DESC'], 10);
		//dd($playlists);
		return $this->render('home/index.html.twig', [
			'playlists' => $playlists,
			'page' => "home",
		]);
	}

	#[Route('/browse', name: 'app_browse')]
	public function browse(EntityManagerInterface $em): Response
	{
		$tags = $em->getRepository(Tag::class)->findBy([], ['createdAt' => 'ASC']);

		return $this->render('playlist/browse.html.twig', [
			'tags' => $tags,
			'page' => "browse",
		]);
	}

	#[Route('/lab', name: 'app_lab')]
	public function lab(EntityManagerInterface $em): Response
	{
		$user = $this->getUser();
		if (!$user) {
			return $this->redirectToRoute('app_login');
		} 
		$playlists = $em->getRepository(Playlist::class)->findBy(
			['author' => $user], ['name' => 'ASC']);
		$tags = $em->getRepository(Tag::class)->findAll();
	
		return $this->render('playlist/lab.html.twig', [
			'user' => $user,
			'playlists' => $playlists,
			'tags' => $tags,
			'page' => "lab",
		]);
	}
}
