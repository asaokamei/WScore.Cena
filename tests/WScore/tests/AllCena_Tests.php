<?php

    ini_set( 'display_errors', 1 );
    error_reporting( E_ALL );

class AllCena_Tests
{
    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite( 'all tests for WScore\'s Cena' );
        $suite->addTestFile( __DIR__ . '/Cena/CM_BasicTest.php' );
        $suite->addTestFile( __DIR__ . '/Cena/CenaIO_Test.php' );
        return $suite;
    }

}
