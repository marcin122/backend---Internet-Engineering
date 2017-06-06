<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2017-03-15
 * Time: 21:31
 */

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;

/**
 * @Route("test")
 */
class TestController extends FOSRestController
{
    /**
     * @Get("/", name="test_index")
     * @View()
     */
    public function indexAction(Request $request)
    {
        $data = ["key" => 5];
        return ($data);
    }

    /**
     * @Get("/add/{a}/{b}", name="test_add")
     * @View()
     */
    public function addAction(Request $request, int $a, int $b)
    {
        $res = $a + $b;
        $data = ["a" => $a, "b" => $b, "c" => $res];
        return ($data);
    }
}