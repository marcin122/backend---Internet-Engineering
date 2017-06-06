<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2017-03-21
 * Time: 21:23
 */

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\View;

use AppBundle\Document\Author;
use AppBundle\Form\AuthorType;

/**
 * @Route("author")
 */
class AuthorController extends FOSRestController
{
    /**
     * @Get("/", name="author_index")
     * @View(serializerGroups={"author"})
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction(Request $request) {
        $dm = $this->getDocumentManager();
        $author = $dm->getRepository('AppBundle:Author')->findAll();
        return $author;
    }

    private function getDocumentManager(){
        return $this->get('doctrine_mongodb')->getManager();
    }

    /**
     * @Get("/get/{id}", name="author_get")
     * @View(serializerGroups={"author", "oneAuthorManyBooks", "book"})
     * @Security("has_role('ROLE_USER')")
     */
    public function getAction(Request $request, string $id)
    {
        $dm = $this->getDocumentManager();
        $author = $dm->getRepository('AppBundle:Author')->find($id);
        if (!$author) {
            throw $this->createNotFoundException('author not found');
        }
        return $author;
    }

    /**
     * @Put("/create", name="author_create")
     * @Security("has_role('ROLE_ADMIN')")
     * @View()
     */
    public function createAction(Request $request){
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $dm = $this->getDocumentManager();
            $dm->persist($author);
            $dm->flush();
            return $author;
        }
        return $form;
    }

    /**
     * @Post("/update/{id}", name="author_update")
     * @Security("has_role('ROLE_ADMIN')")
     * @View()
     */
    public function upadteAction(Request $request,string $id){
        $dm=$this->getDocumentManager();

        $author=$dm->getRepository('AppBundle:Author')->find($id);

        $form=$this->createForm(AuthorType::class,$author);
        $form->submit($request->request->all());

        if($form->isValid()){
            $dm->persist($author);
            $dm->flush();
            return $author;
        }
        return $form;
    }

    /**
     * @Delete("/delete/{id}", name="author_delete")
     * @Security("has_role('ROLE_ADMIN')")
     * @View()
     */
    public function deleteAction(Request $request,string $id){
        $dm=$this->getDocumentManager();

        $author=$dm->getRepository('AppBundle:Author')->find($id);
        if(!$author) {
            throw $this->createNotFoundException('Selected author does not exist');
        }
        $dm->remove($author);
        $dm->flush();
        return ["status"=>"OK"];
    }

    /**
     * @Get("/search/{name}", name="author_search")
     * @Security("has_role('ROLE_USER')")
     */
    public function searchAction(Request $request,string $name)
    {
        $dm = $this->getDocumentManager();

//         option 1
//        $author=$dm->getRepository('AppBundle:author')->findBy(['title'=>$title]);
//
//        option 2
//        $author=$dm->createQueryBuilder('AppBundle:author')
//            ->field('title')
//            ->equals($title)
//            ->getQuery()
//            ->toArray();

//        option 3
        $author = $dm->createQueryBuilder('AppBundle:Author')
            ->field('name')
            ->equals(new \MongoRegex('/.*' . $name . '.*/i'))
            ->getQuery()
            ->toArray();

        if (!$author)
            throw $this->createNotFoundException('Author not found');

        return $author;
    }
}