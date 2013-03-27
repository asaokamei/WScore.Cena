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

    function getContactData( $idx=1 )
    {
        /** @var $model \WSTests\DataMapper\models\Contacts */
        $model = $this->em->getModel( $this->contactEntity );
        return $model->makeContact( $idx );
    }
    
    function buildCenaData( $cenaID, $data ) {
        list( $model, $type, $id ) = explode( '.', $cenaID );
        $input  = array(
            'Cena' => array(
                $model => array(
                    $type => array(
                        $id => $data
                    )
                )
            )
        );
        return $input;
    }
    // +----------------------------------------------------------------------+
    function test_basic_classes()
    {
        $this->assertEquals( 'WScore\Cena\CenaManager', get_class( $this->cm ) );
        $data   = $this->getFriendData(1);
        $friend = $this->em->newEntity( $this->friendEntity, $data );
        $cena   = $this->cm->applyCenaIO( $friend );
        $this->assertEquals( 'WScore\Cena\Role\CenaIO', get_class( $cena ) );
    }

    function test_form_new_entity()
    {
        $data   = $this->getFriendData(1);
        $friend = $this->em->newEntity( $this->friendEntity, $data );
        $cena   = $this->cm->applyCenaIO( $friend );
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
        $cena   = $this->cm->applyCenaIO( $friend );
        $data   = $this->getFriendData(1);
        $input  = $this->buildCenaData( $cenaID, array( 'prop' => $data ) );
        $cena->loadData( $input );
        foreach( $data as $key => $val ) {
            $this->assertEquals( $val, $cena->entity->$key );
        }
    }

    function test_loadLink()
    {
        $data   = $this->getFriendData(1);
        $friend = $this->em->newEntity( $this->friendEntity, $data );
        $cenaID = $friend->getCenaId();
        $cena   = $this->cm->applyCenaIO( $friend );

        $contactID = 'Cena.Contacts.0.5';
        $input  = $this->buildCenaData( $cenaID, array( 'link' => array( 'contacts' => $contactID ) ) );
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
        $cena   = $this->cm->applyCenaIO( $friend );

        $contactID = 'Cena.Contacts.0.5';
        $input  = $this->buildCenaData( $cenaID, array(
            'prop' => $data,
            'link' => array( 'contacts' => $contactID ) ) 
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
    
    function test_popHiddenLink()
    {
        $friend  = $this->em->newEntity( $this->friendEntity );
        $contact = $this->em->newEntity( $this->contactEntity );
        $contact2= $this->em->newEntity( $this->contactEntity );
        $this->em->relation( $friend, 'contacts' )->set( $contact );
        $this->em->relation( $friend, 'contacts' )->set( $contact2 );
        $cenaIO = $this->cm->applyCenaIO( $friend );

        $form  = $cenaIO->popLinkHidden( 'contacts' );
        $this->assertEquals( 'WScore\Html\Elements', get_class( $form ) );
        $html  = (string) $form;
        $name  = $this->cm->getFormName( $friend->getCenaId(), 'link', 'contacts' );
        $value = $contact->getCenaId();
        $this->assertContains( "name=\"{$name}[]\"", $html );
        $this->assertContains( "value=\"Cena.{$value}\"", $html );
        $value = $contact2->getCenaId();
        $this->assertContains( "value=\"Cena.{$value}\"", $html );
    }
    
    function test_popSelectLink()
    {
        $friend  = $this->em->newEntity( $this->friendEntity );
        $contact = $this->em->newEntity( $this->contactEntity, $this->getContactData(1) );
        $contact2= $this->em->newEntity( $this->contactEntity, $this->getContactData(2) );
        $contact3= $this->em->newEntity( $this->contactEntity, $this->getContactData(3) );
        $this->em->relation( $friend, 'contacts' )->set( $contact );
        $this->em->relation( $friend, 'contacts' )->set( $contact2 );
        $cenaIO = $this->cm->applyCenaIO( $friend );

        $contacts = array( $contact, $contact2, $contact3 );
        $form  = $cenaIO->popLinkSelect( 'contacts', $contacts, 'info' );
        /** @var $contact \WScore\tests\contacts\entities\contact */
        /** @var $entity  \WScore\tests\contacts\entities\friend */
        $this->assertEquals( 'WScore\Html\Elements', get_class( $form ) );

        // check if returned an Elements object. 
        $html  = (string) $form;
        $name  = $this->cm->getFormName( $friend->getCenaId(), 'link', 'contacts' );
        
        // check if these name/values are included in the html. 
        $this->assertContains( "name=\"{$name}[]\"", $html );
        $value = $contact->getCenaId();
        $this->assertContains( "value=\"Cena.{$value}\"", $html );
        $value = $contact2->getCenaId();
        $this->assertContains( "value=\"Cena.{$value}\"", $html );
        $value = $contact3->getCenaId();
        $this->assertContains( "value=\"Cena.{$value}\"", $html );
        
        // check for each line.
        $lines = explode( "\n", $html );
        $line  = $lines[0];
        $this->assertContains( "<select ", $line );
        $this->assertContains( " multiple=\"multiple\"", $line );
        $this->assertContains( " name=\"{$name}[]\"", $line );

        // check for second line: option for contact
        $line  = $lines[1];
        $this->assertContains( "<option ", $line );
        $value = $contact->getCenaId();
        $this->assertContains( " value=\"Cena.{$value}\"", $line );
        $this->assertContains( " selected=\"selected\"", $line );
        $this->assertContains( ">my contact#1<", $line );

        // check for third line: option for contact2
        $line  = $lines[2];
        $this->assertContains( "<option ", $line );
        $value = $contact2->getCenaId();
        $this->assertContains( " value=\"Cena.{$value}\"", $line );
        $this->assertContains( " selected=\"selected\"", $line );
        $this->assertContains( ">my contact#2<", $line );

        // check for 4th line: option for contact3
        $line  = $lines[3];
        $this->assertContains( "<option ", $line );
        $value = $contact3->getCenaId();
        $this->assertContains( " value=\"Cena.{$value}\"", $line );
        $this->assertNotContains( " selected=\"selected\"", $line );
        $this->assertContains( ">my contact#3<", $line );
    }

    function test_popSelectLink_checked()
    {
        $friend  = $this->em->newEntity( $this->friendEntity );
        $contact = $this->em->newEntity( $this->contactEntity, $this->getContactData(1) );
        $contact2= $this->em->newEntity( $this->contactEntity, $this->getContactData(2) );
        $contact3= $this->em->newEntity( $this->contactEntity, $this->getContactData(3) );
        $this->em->relation( $friend, 'contacts' )->set( $contact );
        $this->em->relation( $friend, 'contacts' )->set( $contact2 );
        $cenaIO = $this->cm->applyCenaIO( $friend );

        $contacts = array( $contact, $contact2, $contact3 );
        $form  = $cenaIO->popLinkSelect( 'contacts', $contacts, 'info', 'checkList' );
        /** @var $contact \WScore\tests\contacts\entities\contact */
        /** @var $entity  \WScore\tests\contacts\entities\friend */
        $this->assertEquals( 'WScore\Html\Elements', get_class( $form ) );

        // check if returned an Elements object.
        $html  = (string) $form;
        $name  = $this->cm->getFormName( $friend->getCenaId(), 'link', 'contacts' );

        // check if these name/values are included in the html.
        $this->assertContains( "name=\"{$name}[]\"", $html );
        $value = $contact->getCenaId();
        $this->assertContains( "value=\"Cena.{$value}\"", $html );
        $value = $contact2->getCenaId();
        $this->assertContains( "value=\"Cena.{$value}\"", $html );
        $value = $contact3->getCenaId();
        $this->assertContains( "value=\"Cena.{$value}\"", $html );

        // check for each line.
        $lines = explode( "\n", $html );
        $line  = $lines[0];
        $this->assertContains( "<div ", $line );

        // check for second line: option for contact
        $line  = $lines[1];
        $this->assertContains( "<input type=\"checkbox\" ", $line );
        $value = $contact->getCenaId();
        $this->assertContains( " value=\"Cena.{$value}\"", $line );
        $this->assertContains( " checked=\"checked\"", $line );
        $this->assertContains( ">my contact#1<", $line );

        // check for third line: option for contact2
        $line  = $lines[2];
        $this->assertContains( "<input type=\"checkbox\" ", $line );
        $value = $contact2->getCenaId();
        $this->assertContains( " value=\"Cena.{$value}\"", $line );
        $this->assertContains( " checked=\"checked\"", $line );
        $this->assertContains( ">my contact#2<", $line );

        // check for 4th line: option for contact3
        $line  = $lines[3];
        $this->assertContains( "<input type=\"checkbox\" ", $line );
        $value = $contact3->getCenaId();
        $this->assertContains( " value=\"Cena.{$value}\"", $line );
        $this->assertNotContains( " checked=\"checked\"", $line );
        $this->assertContains( ">my contact#3<", $line );
    }

}
