<?php
namespace WScore\tests\contacts\entities;

use \WScore\DataMapper\Entity\EntityAbstract;

class contact extends EntityAbstract
{
    static $_modelName = '\WScore\tests\contacts\models\Contacts';
    public $contact_id;
    public $friend_id;
    public $info;
    public $type;
    public $new_dt_contact;
    public $mod_dt_contact;
    public $friend;
}
