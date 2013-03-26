<?php
namespace WSTests\DataMapper\entities;

use \WScore\DataMapper\Entity\EntityAbstract;

class friend extends EntityAbstract
{
    static $_modelName = '\WSTests\DataMapper\models\Friends';
    
    public $friend_id;
    public $friend_name;
    public $gender;
    public $friend_bday;
    public $friend_tel;
    public $tag_id;
    public $new_dt_friend;
    public $mod_dt_friend;
    public $contacts;
}