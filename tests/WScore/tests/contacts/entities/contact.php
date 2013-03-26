<?php
namespace WSTests\DataMapper\entities;

use \WScore\DataMapper\Entity\EntityAbstract;

class contact extends EntityAbstract
{
    static $_modelName = '\WSTests\DataMapper\models\Contacts';
    public $contact_id;
    public $friend_id;
    public $info;
    public $type;
    public $new_dt_contact;
    public $mod_dt_contact;
    public $friend;
}
