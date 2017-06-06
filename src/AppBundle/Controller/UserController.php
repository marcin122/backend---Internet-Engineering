<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2017-03-24
 * Time: 18:57
 */

namespace AppBundle\Controller;

use AppBundle\Document\User;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @Route("user")
 */
class UserController extends FOSRestController
{
    /**
     * @Get("/info")
     * @View()
     */
    public function infoAction(Request $request){
        $user=$this->getUser();
        return [
            'id'=>$user->getId(),
            'username'=>$user->getUsername(),
            'email'=>$user->getEmail(),
            'last login'=>$user->getLastlogin(),
            'roles'=>$user->getRoles()
        ];
    }

    /**
     * @Put("/register")
     * @View()
     */
    public function registerAction(Request $request){
        $userManager=$this->get('fos_user.user_manager');
        $data=$request->request->all();

        if($this->validateUserName($data)){
            $user=$userManager->createUser();
            $user->setUsername($data['username']);
            $user->setEmail($data['email']);
            $user->setPlainPassword($data['password']);
            $user->setEnabled(true);
            $user->addRole("ROLE_USER");

            if(isset($data['ralname'])) {
                $user->setRealName($data['realname']);
            }
            else{
                $user->setRealName(null);
            }

            $userManager->updateUser($user);
            return ["token"=>$this->get('lexik_jwt_authentication.jwt_manager')->create($user)];
        }
        throw new HttpException(500,"unknow error");
    }

    private function validateUserName($data){

        $userManager=$this->get('fos_user.user_manager');
        $user=$userManager->findUserByEmail($data['email']);

        if($user){
            throw new HttpException(409,"User with the same emali already exist");
        }

        $user=$userManager->findUserByUsername($data['username']);
        if($user){
            throw new HttpException(409,"User with the same username already exist");
        }

        if(!$this->validateEmail($data)){
            throw new HttpException(422,"Email address is not correct");
        }

        if(!$this->validatePassword($data)){
            throw new HttpException(422, "Password is not correct");
        }
        return true;
    }

    private function validateEmail($data){
        $constraints=array(
            new Email(),
            new NotBlank(),
        );

        $errors=$this->get('validator')->validate($data['email'],$constraints);
        if(count($errors)){
            return false;
        }
        return true;
    }

    private function validatePassword($data){
        $constraint=new Length(['min'=>6]);
        $errors=$this->get('validator')->validate($data['password'],$constraint);
        if(count($errors)){
            return false;
        }
        return true;
    }

    /**
     * @Post("/update_password")
     * @View()
     */
    public function updatePasswordAction(Request $request){
        $userManager=$this->get('fos_user.user_manager');
        $user=$this->getUser();
        $data=$request->request->all();

        if(isset($data['password'])){
            if(!$this->validatePassword($data)){
                throw new HttpException(422, "Password is not correct");
            }
            else{
                $user->setPlainPassword($data['password']);
            }
        }
        else
            throw new HttpException(422,"Password is not contain in JSON");

        $userManager->updateUser($user);
        return ["message"=>"Password updated"];
    }

    /**
     * @Post("/update_email")
     * @View()
     */
    public function updateEmailAction(Request $request){
        $userManager=$this->get('fos_user.user_manager');
        $user=$this->getUser();
        $data=$request->request->all();

        if(isset($data['email'])) {
            if (!$this->validateEmail($data)) {
                throw new HttpException(422, "Email address is not correct");
            }
            else{
                $user->setEmail($data['email']);
            }
        }
        else
            throw new HttpException(422, "Email address is not contain in JSON");

        if(isset($data['realname'])){
            $user->setRealName($data['realname']);
        }

        $userManager->updateUser($user);
        return ["message"=>"Email updated"];
    }
}