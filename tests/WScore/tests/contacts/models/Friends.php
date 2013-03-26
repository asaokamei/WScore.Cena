<?php
namespace WSTests\DataMapper\models;

use \WScore\DataMapper\Model;

class Friends extends Model
{
    protected $table = 'friend';
    
    protected $id_name = 'friend_id';

    const GENDER_MALE   = 'M';
    const GENDER_FEMALE = 'F';
    const GENDER_NONE   = 'N';

    public function __construct()
    {
        parent::__construct();
        $csv = file_get_contents( __DIR__ . '/friends.csv' );
        $this->property->prepare( $csv );
        $this->setGenderChoice();
    }

    public function setupTable()
    {
        $table = $this->table;
        $sql = "DROP TABLE IF EXISTS {$table}";
        $this->persistence->query()->dbAccess()->execSQL( $sql );
        $sql = "
            CREATE TABLE {$table} (
              friend_id    SERIAL,
              friend_name  text    NOT NULL,
              gender       char(1) NOT NULL,
              friend_bday  date,
              friend_tel   text    NOT NULL,
              new_dt_friend   datetime,
              mod_dt_friend   datetime,
              constraint friend_pkey PRIMARY KEY (
                friend_id
              )
            )
        ";
        $this->persistence->query()->dbAccess()->execSQL( $sql );
    }

    /**
     * @param int $idx
     * @return array
     */
    function getFriendData( $idx=1 )
    {
        $gender = array( 'M', 'F' );
        $gender = $gender[ $idx % 2 ];
        $day    = 10 + $idx;
        $data = array(
            'friend_name' => 'friend #' . $idx,
            'gender'      => $gender,
            'friend_bday' => '1989-02-' . $day,
            'friend_tel'  => '03-123456-' . $idx,
        );
        return $data;
    }

    public function setGenderChoice( $all=false )
    {
        $this->property->selectors[ 'gender' ][ 'choice' ] = array(
            array( self::GENDER_NONE, 'not sure' ),
            array( self::GENDER_MALE, 'male' ),
        );
        if( $all ) {
            $this->property->selectors[ 'gender' ][ 'choice' ][] = array( self::GENDER_FEMALE, 'female' );
        }
    }
}