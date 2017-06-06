<?php

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Groups;
use MongoId;

/**
 *@ODM\Document
 */
class Book
{
    /**
     * @var MongoId $id
     * @ODM\Id(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Groups({"book"})
     * @ODM\Field(type="string")
     */
    protected $title;

    /**
     * @var Author
     * @Groups({"book"})
     * @ODM\ReferenceOne(targetDocument="Author", inversedBy="books")
     */
    protected $author;

    /**
     * @return Author
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param Author $author
     */
    public function setAuthor(Author $author)
    {
        $this->author = $author;
    }

    /**
     * @return MongoId
     */

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param MongoId $id
     */
    public function setId(MongoId $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

}
