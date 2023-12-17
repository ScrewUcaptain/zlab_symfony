<?php

namespace App\Entity;

use App\Repository\SongRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SongRepository::class)]
class Song
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	private ?string $name = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $url = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $year = null;

	#[ORM\Column(length: 255)]
	private ?string $artist = null;

	#[ORM\ManyToMany(targetEntity: Playlist::class, mappedBy: 'songs')]
	private Collection $playlists;

	public function __construct()
	{
		$this->playlists = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}

	public function getArtist(): ?string
	{
		return $this->artist;
	}

	public function setArtist(string $artist): static
	{
		$this->artist = $artist;

		return $this;
	}

	public function getUrl(): ?string
	{
		return $this->url;
	}

	public function setUrl(?string $url): static
	{
		$this->url = $url;

		return $this;
	}

	public function getYear(): ?string
	{
		return $this->year;
	}

	public function setYear(?string $year): static
	{
		$this->year = $year;

		return $this;
	}

	/**
	 * @return Collection<int, Playlist>
	 */
	public function getPlaylists(): Collection
	{
		return $this->playlists;
	}

	public function addPlaylist(Playlist $playlist): static
	{
		if (!$this->playlists->contains($playlist)) {
			$this->playlists->add($playlist);
			$playlist->addSong($this);
		}

		return $this;
	}

	public function removePlaylist(Playlist $playlist): static
	{
		if ($this->playlists->removeElement($playlist)) {
			$playlist->removeSong($this);
		}

		return $this;
	}
}
