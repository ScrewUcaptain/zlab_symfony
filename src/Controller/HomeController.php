<?php

namespace App\Controller;

use App\Entity\Playlist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
	#[Route('/', name: 'app_home')]
	public function index(EntityManagerInterface $em): Response
	{
		$playlists = $em->getRepository(Playlist::class)->findBy([], ['likes' => 'DESC'], 10);
		return $this->render('home/index.html.twig', [
			'playlists' => $playlists,
		]);
	}
}
