<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
	#[ORM\Id]
               	#[ORM\GeneratedValue]
               	#[ORM\Column]
               	private ?int $id = null;

	#[ORM\Column(length: 180, unique: true)]
               	private ?string $email = null;

	#[ORM\Column]
               	private array $roles = [];

	/**
	 * @var string The hashed password
	 */
	#[ORM\Column]
               	private ?string $password = null;

	#[ORM\Column(length: 255)]
               	private ?string $firstname = null;

	#[ORM\Column(length: 255)]
               	private ?string $lastname = null;

	#[ORM\Column(length: 255, nullable: true)]
               	private ?string $thumbnail = null;

	#[ORM\OneToMany(mappedBy: 'author', targetEntity: Playlist::class, orphanRemoval: true)]
               	private Collection $playlists;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

	public function __construct()
               	{
               		$this->playlists = new ArrayCollection();
               	}

	public function getId(): ?int
               	{
               		return $this->id;
               	}

	public function getEmail(): ?string
               	{
               		return $this->email;
               	}

	public function setEmail(string $email): static
               	{
               		$this->email = $email;
               
               		return $this;
               	}

	/**
	 * A visual identifier that represents this user.
	 *
	 * @see UserInterface
	 */
	public function getUserIdentifier(): string
               	{
               		return (string) $this->email;
               	}

	/**
	 * @see UserInterface
	 */
	public function getRoles(): array
               	{
               		$roles = $this->roles;
               		// guarantee every user at least has ROLE_USER
               		$roles[] = 'ROLE_USER';
               
               		return array_unique($roles);
               	}

	public function setRoles(array $roles): static
               	{
               		$this->roles = $roles;
               
               		return $this;
               	}

	/**
	 * @see PasswordAuthenticatedUserInterface
	 */
	public function getPassword(): string
               	{
               		return $this->password;
               	}

	public function setPassword(string $password): static
               	{
               		$this->password = $password;
               
               		return $this;
               	}

	/**
	 * @see UserInterface
	 */
	public function eraseCredentials(): void
               	{
               		// If you store any temporary, sensitive data on the user, clear it here
               		// $this->plainPassword = null;
               	}

	public function getFirstname(): ?string
               	{
               		return $this->firstname;
               	}

	public function setFirstname(string $firstname): static
               	{
               		$this->firstname = $firstname;
               
               		return $this;
               	}

	public function getLastname(): ?string
               	{
               		return $this->lastname;
               	}

	public function setLastname(string $lastname): static
               	{
               		$this->lastname = $lastname;
               
               		return $this;
               	}

	public function getThumbnail(): ?string
               	{
               		return $this->thumbnail;
               	}

	public function setThumbnail(?string $thumbnail): static
               	{
               		$this->thumbnail = $thumbnail;
               
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
               			$playlist->setAuthor($this);
               		}
               
               		return $this;
               	}

	public function removePlaylist(Playlist $playlist): static
               	{
               		if ($this->playlists->removeElement($playlist)) {
               			// set the owning side to null (unless already changed)
               			if ($playlist->getAuthor() === $this) {
               				$playlist->setAuthor(null);
               			}
               		}
               
               		return $this;
               	}

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
