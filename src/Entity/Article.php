<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;
/**
 * @Hateoas\Relation(
 *  "self",
 *      href= @Hateoas\Route(
 *      "app_article_show",
 *      parameters={"id" = "expr(object.getId())"},
 *      absolute= true
 * )
 * )
 * 
 * @Hateoas\Relation(
 *  "modify",
 *  href= @Hateoas\Route(
 *      "app_article_update",
 *      parameters= {"id" = "expr(object.getId())"}
 *  )
 * )
 * @Hateoas\Relation(
 *  "delete",
 *  href= @Hateoas\Route(
 *      "app_article_delete",
 *      parameters= {"id" = "expr(object.getId())"}
 *  )
 * )
 * 
 * @Hateoas\Relation(
 *  "author",
 *  embedded = @Hateoas\Embedded("expr(object.getAuthor())")
 * )
 * 
 * @Hateoas\Relation(
 *  "weather",
 *  embedded = @Hateoas\Embedded("expr(service('App\\Weather\\Weather').getCurrent())")
 * )
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(groups={"Create"})
     * @Serializer\Since("1.0")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Serializer\Since("1.0")
     * @Assert\NotBlank(groups={"Create"})
     */
    private $content;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\since("2.0")
     */
    private $shortDescription;

    /**
     * @ORM\ManyToOne(targetEntity=Author::class, inversedBy="articles")
     */
    private $author;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }
}
