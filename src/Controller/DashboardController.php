<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Entity\Song;
use App\Entity\User;
use App\Form\TagType;
use App\Form\SongType;
use App\Form\UserType;
use App\Entity\Playlist;
use App\Form\PlaylistType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/dashboard', name: 'admin_')]
class DashboardController extends AbstractController
{

    private function getNowDate(){
        $now = new \DateTime();
        $now->setTimezone(new \DateTimeZone('Europe/Paris'));
        return $now;
    }

    #[Route('/', name: 'dashboard')]
	public function dashboard(EntityManagerInterface $em): Response
	{
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

    #[Route('/update-user/{user}', name: 'update_user')]
    public function updateUser(EntityManagerInterface $em, User $user, Request $request, SluggerInterface $slugger)
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $thumbnail = $form->get('thumbnail')->getData();
            if($thumbnail) {
                $originalFilename = pathinfo($thumbnail->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$thumbnail->guessExtension();
                try {
                    $thumbnail->move(
                        $this->getParameter('thumbnails'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // handle exception if something happens during file upload
                }
                $user->setThumbnail($newFilename);
            } 
            // else {
            //     $user->setThumbnail('default.png');
            // }
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('form/update-user.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/add-song', name: 'add_song')]
    public function addSong(Request $request, EntityManagerInterface $em)
    {
        $song = new Song();
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $song = $form->getData();
            $em->persist($song);
            $em->flush();
            return $this->redirectToRoute('admin_songs');
        }

        return $this->render('form/add-song.html.twig', [
            'form' => $form
        ]);
    }
    #[Route('/update-song/{song}', name: 'update_song')]
    public function updateSong(Request $request, EntityManagerInterface $em, Song $song)
    {
        $form = $this->createForm(SongType::class, $song);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $song = $form->getData();
            $em->persist($song);
            $em->flush();
            return $this->redirectToRoute('admin_songs');
        }

        return $this->render('form/update-song.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/add-tag', name: 'add_tag')]
    public function addTag(Request $request, EntityManagerInterface $em)
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $tag = $form->getData();
            $tag->setCreatedAt($this->getNowDate());
            $em->persist($tag);
            $em->flush();
            return $this->redirectToRoute('admin_tags');
        }

        return $this->render('form/add-tag.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/update-tag/{tag}', name: 'update_tag')]
    public function updateTag(Request $request, EntityManagerInterface $em, Tag $tag)
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $tag = $form->getData();
            $em->persist($tag);
            $em->flush();
            return $this->redirectToRoute('admin_tags');
        }
        return $this->render('form/update-tag.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/add-playlist', name: 'add_playlist')]
    public function addPlaylist(Request $request, EntityManagerInterface $em, SluggerInterface $slugger)
    {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $playlist = $form->getData();

            $cover = $form->get('cover')->getData();
			if ($cover) {
				$originalFilename = pathinfo($cover->getClientOriginalName(), PATHINFO_FILENAME);
				$safeFilename = $slugger->slug($originalFilename);
				$newFilename = $safeFilename . '-' . uniqid() . '.' . $cover->guessExtension();
				try {
					$cover->move(
						$this->getParameter('covers'),
						$newFilename
					);
				} catch (FileException $e) {
					// handle exceptions
				}
				$playlist->setCover($newFilename);
			}
            $playlist->setAuthor($this->getUser());
            $playlist->setCreatedAt($this->getNowDate());
            $playlist->setUpdatedAt($this->getNowDate());
            $playlist->setLikes(0);
            $em->persist($playlist);
            $em->flush();
            return $this->redirectToRoute('admin_playlists');
        }

        return $this->render('form/add-playlist.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/update-playlist/{playlist}', name: 'update_playlist')]
    public function updatePlaylist(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, Playlist $playlist)
    {
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $playlist = $form->getData();

            $cover = $form->get('cover')->getData();
			if ($cover && $cover != $playlist->getCover()) {
				$originalFilename = pathinfo($cover->getClientOriginalName(), PATHINFO_FILENAME);
				$safeFilename = $slugger->slug($originalFilename);
				$newFilename = $safeFilename . '-' . uniqid() . '.' . $cover->guessExtension();
				try {
					$cover->move(
						$this->getParameter('covers'),
						$newFilename
					);
				} catch (FileException $e) {
					// handle exceptions
				}
				$playlist->setCover($newFilename);
			}
            $playlist->setUpdatedAt($this->getNowDate());
            $em->persist($playlist);
            $em->flush();
            return $this->redirectToRoute('admin_playlists');
        }

        return $this->render('form/update-playlist.html.twig', [
            'form' => $form,
            'playlist' => $playlist
        ]);
    }

    #[Route('/delete-song', name: 'delete_song')]
    public function deleteSong(Request $request, EntityManagerInterface $em){
        $songId = $request->get('songId');
        $song = $em->getRepository(Song::class)->find($songId);
        $em->remove($song);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
    #[Route('/delete-playlist', name: 'delete_playlist')]
    public function deletePlaylist(Request $request, EntityManagerInterface $em){
        $playlistId = $request->get('playlistId');
        $playlist = $em->getRepository(Playlist::class)->find($playlistId);
        $em->remove($playlist);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
    #[Route('/delete-user', name: 'delete_user')]
    public function deleteUser(Request $request, EntityManagerInterface $em){
        $userId = $request->get('userId');
        $user = $em->getRepository(User::class)->find($userId);
        $em->remove($user);
        $em->flush();
        return new JsonResponse(['success' => true]);
    }

    #[Route('/delete-tag', name: 'delete_tag')]
    public function deleteTag(Request $request, EntityManagerInterface $em){
        $tagId = $request->get('tagId');
        $tag = $em->getRepository(Tag::class)->find($tagId);
        $em->remove($tag);
        $em->flush();
        return new JsonResponse(['success' => true]);
    }
}
