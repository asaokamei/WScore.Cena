<?php
namespace WScore\tests\Cena;

use \WScore\Cena\Construct;
use WScore\tests\contacts\models\Contacts;

require( __DIR__ . '/../../../autoloader.php' );

class Construct_Test extends \PHPUnit_Framework_TestCase
{
    /** @var \WScore\Cena\Construct */
    public $con;
    
    function setUp()
    {
        $this->con = new Construct();
    }
    
    function test_unCompose()
    {
        $id   = 'Cena.friend.model.id';
        $unId = $this->con->unCompose( $id );
        $this->assertNotEquals( $id, $unId );
        $this->assertEquals( 'friend.model.id', $unId );
    }
    function test_unCompose_ignores_non_cenaID()
    {
        $id   = 'Not.Cena.ID';
        $unId = $this->con->unCompose( $id );
        $this->assertEquals( $id, $unId );
    }
}