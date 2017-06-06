<?php
/**
 * Created by PhpStorm.
 * User: PC
 * Date: 2017-03-22
 * Time: 22:44
 */

namespace AppBundle\Document;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 */
class User extends BaseUser
{
    /**
     * @ODM\Id(strategy="AUTO")
     */
    protected $id;

    /**
     * @ODM\Field(type="string", nullable=true)
     */
    protected $realName;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getRealName()
    {
        return $this->realName;
    }

    /**
     * @param mixed $realName
     */
    public function setRealName($realName)
    {
        $this->realName = $realName;
    }

}