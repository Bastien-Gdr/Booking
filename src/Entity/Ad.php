<?php

namespace App\Entity;

use App\Entity\Image;
use Cocur\Slugify\Slugify;
use App\Repository\AdRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: AdRepository::class)]
#[ORM\HasLifecycleCallbacks]
/**
 * @UniqueEntity(
 * fields={"title"},
 * message="Ce titre est déjà utilisé pour une autre annonce"
 * )
 */

class Ad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    /**
     *@Assert\Length(min=10,max=255,minMessage="Le titre doit faire plus de 10 catractères",maxMessage="Le message doit faire moins de 255 caractères")
     */
    private $title;

    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    #[ORM\Column(type: 'float')]
    private $price;

    /**
     *@Assert\Length(min=30,minMessage="Merci de mettre au moins 30 caractères")
     */
    #[ORM\Column(type: 'text')]
    private $introduction;

    /**
     *@Assert\Length(min=100,minMessage="Merci de mettre au moins 100 caractères")
     */
    #[ORM\Column(type: 'text')]
    private $content;

    #[ORM\Column(type: 'string', length: 255)]
    private $coverImage;

    #[ORM\Column(type: 'integer')]
    private $rooms;

    #[ORM\OneToMany(mappedBy: 'ad', targetEntity: Image::class,orphanRemoval:true)]
    private $images;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'ads')]
    #[ORM\JoinColumn(nullable: false)]
    private $author;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    /**
     * Creation d'une fonction pour permettre d'initialiser le slug (avant la persistence/enregistrement et avant la maj)
     */
#[ORM\PrePersist]
#[ORM\PreUpdate]

    public function initializeSlug(){
        if(empty($this->slug)){
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->title);

        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) {
                $image->setAd(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
