<?php
namespace WScore\tests\Cena;

require( __DIR__ . '/../../../autoloader.php' );

class CenaIO_Test extends \PHPUnit_Framework_TestCase
{
    /** @var mixed */
    public static $config = 'dsn=mysql:dbname=test_WScore username=admin password=admin';

    /** @var \WScore\DataMapper\EntityManager */
    public $em;

    /** @var \WScore\Cena\CenaManager */
    public $cm;

    public $friendEntity = '\WScore\tests\contacts\entities\friend';
    public $contactEntity = '\WScore\tests\contacts\entities\contact';

    // +----------------------------------------------------------------------+
    static function setUpBeforeClass()
    {
        /** @var $container \WScore\DiContainer\Container */
        /** @noinspection PhpIncludeInspection */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $container->set( '\Pdo', self::$config );
        /** @var $friend \WScore\tests\contacts\models\Friends */
        $friend = $container->get( '\WScore\tests\contacts\models\Friends' );
        $friend->setupTable();
        class_exists( '\WScore\DataMapper\Entity\EntityAbstract' );
        class_exists( '\WSTests\DataMapper\models\Friends' );
        class_exists( '\WSTests\DataMapper\entities\friend' );
    }

    /**
     *
     */
    function setUp()
    {
        /** @var $container \WScore\DiContainer\Container */
        /** @noinspection PhpIncludeInspection */
        $container = include( VENDOR_DIRECTORY . 'wscore/dicontainer/scripts/instance.php' );
        $container->set( 'ContainerInterface', $container );
        $container->set( '\Pdo', self::$config );
        // set up persistence model
        $this->em = $container->get( '\WScore\DataMapper\EntityManager' );
        $this->cm = $container->get( '\WScore\Cena\CenaManager' );
    }

    /**
     * @param int $idx
     * @return array
     */
    function getFriendData( $idx=1 )
    {
        /** @var $model \WSTests\DataMapper\models\Friends */
        $model = $this->em->getModel( $this->friendEntity );
        return $model->getFriendData( $idx );
    }

    // +----------------------------------------------------------------------+
    function test_basic_classes()
    {
        $this->assertEquals( 'WScore\Cena\CenaManager', get_class( $this->cm ) );
        $data   = $this->getFriendData(1);
        $friend = $this->em->newEntity( $this->friendEntity, $data );
        $cena   = $this->cm->DataIO( $friend );
        $this->assertEquals( 'WScore\Cena\Role\CenaIO', get_class( $cena ) );
    }

    function test_form_new_entity()
    {
        $data   = $this->getFriendData(1);
        $friend = $this->em->newEntity( $this->friendEntity, $data );
        $cena   = $this->cm->DataIO( $friend );
        // check if name is returned correctly.
        $name   = $cena->popHtml( 'friend_name' );
        $this->assertEquals( $data[ 'friend_name' ], $name );
        // check form has that cena id thing in it.
        $form   = $cena->popHtml( 'friend_name', 'form' );
        $this->assertEquals( 'WScore\Html\Elements', get_class( $form ) );
        // check html form.
        $html   = (string) $form;
        $cenaID = $friend->getCenaId();
        $cenaID = $this->cm->getFormName( $cenaID, 'prop', 'friend_name' );
        $this->assertContains( "value=\"{$name}\"", $html );
        $this->assertContains( "name=\"{$cenaID}\"", $html );
    }

    function test_loadParent()
    {
        $friend = $this->em->newEntity( $this->friendEntity );
        $cenaID = $friend->getCenaId();
        $cena   = $this->cm->DataIO( $friend );
        $data   = $this->getFriendData(1);
        list( $model, $type, $id ) = explode( '.', $cenaID );
        $input  = array(
            'Cena' => array(
                $model => array(
                    $type => array(
                        $id => array(
                            'prop' => $data
                        )
                    )
                )
            )
        );
        $cena->loadParent( $input );
        foreach( $data as $key => $val ) {
            $this->assertEquals( $val, $cena->entity->$key );
        }
    }

    function test_loadLink()
    {
        $data   = $this->getFriendData(1);
        $friend = $this->em->newEntity( $this->friendEntity, $data );
        $cenaID = $friend->getCenaId();
        $cena   = $this->cm->DataIO( $friend );

        list( $model, $type, $id ) = explode( '.', $cenaID );
        $contactID = 'Cena.Contacts.0.5';
        $input  = array(
            'Cena' => array(
                $model => array(
                    $type => array(
                        $id => array(
                            'link' => array( 'contacts' => $contactID )
                        )
                    )
                )
            )
        );
        $this->cm->useEntity( $this->friendEntity );
        $this->cm->useEntity( $this->contactEntity );
        $cena->loadLink( $input );

        /** @var $contact \WScore\tests\contacts\entities\contact */
        /** @var $entity  \WScore\tests\contacts\entities\friend */
        $entity  = $cena->entity;
        $contact = $entity->contacts[0];
        $this->assertEquals( $contactID, 'Cena.' . $contact->getCenaId() );
    }
    
    function test_load()
    {
        $data   = $this->getFriendData(1);
        $friend = $this->em->newEntity( $this->friendEntity );
        $cenaID = $friend->getCenaId();
        $cena   = $this->cm->DataIO( $friend );

        list( $model, $type, $id ) = explode( '.', $cenaID );
        $contactID = 'Cena.Contacts.0.5';
        $input  = array(
            'Cena' => array(
                $model => array(
                    $type => array(
                        $id => array(
                            'prop' => $data,
                            'link' => array( 'contacts' => $contactID )
                        )
                    )
                )
            )
        );
        $this->cm->useEntity( $this->friendEntity );
        $this->cm->useEntity( $this->contactEntity );
        $cena->load( $input );

        /** @var $contact \WScore\tests\contacts\entities\contact */
        /** @var $entity  \WScore\tests\contacts\entities\friend */
        $entity  = $cena->entity;
        $contact = $entity->contacts[0];
        $this->assertEquals( $contactID, 'Cena.' . $contact->getCenaId() );
        foreach( $data as $key => $val ) {
            $this->assertEquals( $val, $cena->entity->$key );
        }

    }
}
