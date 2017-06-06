<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2017-03-16
 * Time: 17:08
 */

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\View;

use AppBundle\Document\Book;
use AppBundle\Form\BookType;

/**
 * @Route("book")
 */
class BookController extends FOSRestController
{
    /**
     * @Get("/", name="book_index")
     * @View(serializerGroups={"book", "author"})
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction(Request $request) {
        $dm = $this->getDocumentManager();
        $books = $dm->getRepository('AppBundle:Book')->findAll();
        return $books;
    }

    private function getDocumentManager(){
        return $this->get('doctrine_mongodb')->getManager();
    }

    /**
     * @Get("/get/{id}", name="book_get")
     * @Security("has_role('ROLE_USER')")
     * @View()
     */
    public function getAction(Request $request, string $id)
    {
        $dm = $this->getDocumentManager();
        $book = $dm->getRepository('AppBundle:Book')->find($id);
        if (!$book) {
            throw $this->createNotFoundException('Book not found');
        }
        return $book;
    }

    /**
     * @Put("/create", name="book_create")
     * @Security("has_role('ROLE_ADMIN')")
     * @View()
     */
    public function createAction(Request $request){
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $dm = $this->getDocumentManager();
            $dm->persist($book);
            $dm->flush();
            return $book;
        }
        return $form;
    }

    /**
     * @Post("/update/{id}", name="book_update")
     * @Security("has_role('ROLE_ADMIN')")
     * @View()
     */
    public function upadteAction(Request $request, $id){
        $dm=$this->getDocumentManager();

        $book=$dm->getRepository('AppBundle:Book')->find($id);

        $form=$this->createForm(BookType::class,$book);
        $form->submit($request->request->all());

        if($form->isValid()){
            $author=$dm->getRepository('AppBundle:Author')->find($book->getAuthor()->getId());

            $dm->persist($book);
            $dm->flush();
            return $book;
        }
        return $form;
    }

    /**
     * @Delete("/{id}", name="book_delete")
     * @Security("has_role('ROLE_ADMIN')")
     * @View()
     */
    public function deleteAction(Request $request, $id){
        $dm=$this->getDocumentManager();

        $book=$dm->getRepository('AppBundle:Book')->find($id);
        if(!$book) {
            throw $this->createNotFoundException('Selected book does not exist');
        }
        $dm->remove($book);
        $dm->flush();
        return ["status"=>"OK"];
    }

    /**
     * @Get("/search/{title}", name="book_search")
     * @Security("has_role('ROLE_USER')")
     */
    public function searchAction(Request $request, $title){
        $dm=$this->getDocumentManager();

//         option 1
//        $book=$dm->getRepository('AppBundle:Book')->findBy(['title'=>$title]);
//
//        option 2
//        $book=$dm->createQueryBuilder('AppBundle:Book')
//            ->field('title')
//            ->equals($title)
//            ->getQuery()
//            ->toArray();

//        option 3
        $book=$dm->createQueryBuilder('AppBundle:Book')
            ->field('title')
            ->equals(new \MongoRegex('/.*'.$title.'.*/i'))
            ->getQuery()
            ->toArray();

        if(!$book)
            throw $this->createNotFoundException('Book not found');

        return $book;
    }
}