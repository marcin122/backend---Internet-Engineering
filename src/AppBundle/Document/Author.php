<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2017-03-21
 * Time: 21:20
 */

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JMS\Serializer\Annotation as Serializer;
use MongoId;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ODM\Document
 */
class Author
{
    /**
     * @var MongoId $id
     * @ODM\Id(strategy="AUTO")
     */
    protected $id;


    /**
     * @var string
     * @Serializer\Groups({"author"})
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * @var Book[]|ArrayCollection
     * @Serializer\Groups({"oneAuthorManyBooks"})
     * @ODM\ReferenceMany(targetDocument="Book", mappedBy="author")
     */
    protected $books;

    public function __construct()
    {
        $this->books=new \Doctrine\Common\Collections\ArrayCollection();
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return Book[]|ArrayCollection
     */
    public function getBooks()
    {
        return $this->books;
    }

    /**
     * @param Book[]|ArrayCollection $books
     */
    public function setBooks($books)
    {
        $this->books = $books;
    }

}