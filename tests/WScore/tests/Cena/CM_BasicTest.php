<?php
namespace WScore\tests\Cena;

use WScore\DataMapper\Entity\EntityInterface;
use WScore\DataMapper\Entity\EntityAbstract;

require( __DIR__ . '/../../../autoloader.php' );

class CM_BasicTest extends \PHPUnit_Framework_TestCase
{
    /** @var mixed */
    public static $config = 'dsn=mysql:dbname=test_WScore username=admin password=admin';

    /** @var \WScore\DataMapper\EntityManager */
    public $em;

    /** @var \WScore\Cena\CenaManager */
    public $cm;

    public $friendEntity = '\WScore\tests\contacts\entities\friend';

    // +----------------------------------------------------------------------+
    static function setUpBeforeClass()
    {
        /** @var $container \WScore\DiContainer\Container */
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
    }

    function test_DataIO_returns_Object()
    {
        $data   = $this->getFriendData(1);
        $friend = $this->em->newEntity( $this->friendEntity, $data );
        $cena   = $this->cm->applyCenaIO( $friend );
        $this->assertEquals( 'WScore\Cena\Role\CenaIO', get_class( $cena ) );
    }

    function test_getFormName_returns_expected_string()
    {
        $data   = $this->getFriendData(1);
        $friend = $this->em->newEntity( $this->friendEntity, $data );
        $cenaID = $friend->getCenaId();
        $formName = $this->cm->getFormName( $cenaID, 'prop', 'test' );
        $expected = 'Cena[' . implode( '][', explode( '.', $cenaID ) ) . '][prop][test]';
        $this->assertEquals( $expected, $formName );
    }

    function test_getDataForCenaId_returns_all_data()
    {
        $info = 'this is info';
        $data = array(
            $this->cm->construct->cena => $info
        );
        $found = $this->cm->getDataForCenaId( $data );
        $this->assertEquals( $info, $found );
    }

    function test_getDataForCenaId_returns_cenaData()
    {
        $data   = $this->getFriendData(1);
        $friend = $this->em->newEntity( $this->friendEntity, $data );
        $cenaID = $friend->getCenaId();
        list( $model, $type, $id ) = explode( '.', $cenaID );
        $info = 'this is info';
        $data = array(
            $this->cm->construct->cena => array(
                $model => array(
                    $type => array(
                        $id => $info
                    )
                )
            )
        );
        $found = $this->cm->getDataForCenaId( $data, $cenaID );
        $this->assertEquals( $info, $found );
    }

    function test_getEntity_returns_new_entity()
    {
        $entity = $this->cm->getEntity( $this->friendEntity, EntityAbstract::_ID_TYPE_VIRTUAL, 10 );
        $cenaID = $entity->getCenaId();
        $expect = implode( '.', array( $entity->getModelName(true),  EntityAbstract::_ID_TYPE_VIRTUAL, 10 ) );
        $this->assertEquals( $expect, $cenaID );
    }

    function test_getCenaEntity_without_Cena()
    {
        $cena = 'Friends.0.5';
        $this->cm->useEntity( $this->friendEntity );
        $entity = $this->cm->getCenaEntity( $cena );
        $this->assertEquals( $cena, $entity->getCenaId() );
    }

    function test_getCenaEntity_with_Cena()
    {
        $cena = 'Cena.Friends.0.6';
        $this->cm->useEntity( $this->friendEntity );
        $entity = $this->cm->getCenaEntity( $cena );
        $this->assertEquals( $cena, 'Cena.' . $entity->getCenaId() );
    }

    function test_getCenaEntity_array()
    {
        $cena = array( 'Friends.0.7', 'Friends.0.8' );
        $this->cm->useEntity( $this->friendEntity );
        $entity = $this->cm->getCenaEntity( $cena );
        $this->assertTrue( is_array( $entity ) );
        $this->assertEquals( $cena[0], $entity[0]->getCenaId() );
        $this->assertEquals( $cena[1], $entity[1]->getCenaId() );
    }

    function test_from_system_entity()
    {
        $data   = $this->getFriendData(1);
        $friend = $this->em->newEntity( $this->friendEntity, $data );
        $this->em->saveEntity( $friend );

        $this->cm->useEntity( $this->friendEntity );
        $entity = $this->cm->getEntity( 'Friends', EntityInterface::_ID_TYPE_SYSTEM, 1 );
        foreach( $data as $key => $val ) {
            $this->assertEquals( $val, $entity->$key );
        }
    }
}