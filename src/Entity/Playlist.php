<?php

namespace App\Entity;

use App\Repository\PlaylistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlaylistRepository::class)]
class Playlist
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	private ?string $name = null;

	#[ORM\Column(type: Types::ARRAY)]
	private array $songs = [];

	#[ORM\Column]
	private ?bool $isPublic = null;

	#[ORM\Column]
	private ?int $likes = null;

	#[ORM\Column(length: 255)]
	private ?string $cover = null;

	#[ORM\Column(type: Types::DATETIME_MUTABLE)]
	private ?\DateTimeInterface $createdAt = null;

	#[ORM\Column(type: Types::DATETIME_MUTABLE)]
	private ?\DateTimeInterface $updatedAt = null;

	#[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'playlists')]
	private Collection $tags;

	#[ORM\ManyToOne(inversedBy: 'playlists')]
	#[ORM\JoinColumn(nullable: false)]
	private ?User $author = null;

	public function __construct()
	{
		$this->tags = new ArrayCollection();
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

	public function getSongs(): array
	{
		return $this->songs;
	}

	public function setSongs(array $songs): static
	{
		$this->songs = $songs;

		return $this;
	}

	public function isPublic(): ?bool
	{
		return $this->isPublic;
	}

	public function setIsPublic(bool $isPublic): static
	{
		$this->isPublic = $isPublic;

		return $this;
	}

	public function getLikes(): ?int
	{
		return $this->likes;
	}

	public function setLikes(int $likes): static
	{
		$this->likes = $likes;

		return $this;
	}

	public function getCover(): ?string
	{
		return $this->cover;
	}

	public function setCover(string $cover): static
	{
		$this->cover = $cover;

		return $this;
	}

	public function getCreatedAt(): ?\DateTimeInterface
	{
		return $this->createdAt;
	}

	public function setCreatedAt(\DateTimeInterface $createdAt): static
	{
		$this->createdAt = $createdAt;

		return $this;
	}

	public function getUpdatedAt(): ?\DateTimeInterface
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(\DateTimeInterface $updatedAt): static
	{
		$this->updatedAt = $updatedAt;

		return $this;
	}

	/**
	 * @return Collection<int, Tag>
	 */
	public function getTags(): Collection
	{
		return $this->tags;
	}

	public function addTag(Tag $tag): static
	{
		if (!$this->tags->contains($tag)) {
			$this->tags->add($tag);
		}

		return $this;
	}

	public function removeTag(Tag $tag): static
	{
		$this->tags->removeElement($tag);

		return $this;
	}

	public function getAuthor(): ?User
	{
		return $this->author;
	}

	public function setAuthor(?User $author): static
	{
		$this->author = $author;

		return $this;
	}
}
