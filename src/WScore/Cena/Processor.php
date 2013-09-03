<?php
namespace WScore\Cena;

use WScore\DataMapper\Entity\EntityAbstract;

class Processor
{
    /** @var \WScore\Cena\CenaManager */
    protected $cm;

    /** @var array */
    protected $source = array();

    /**
     */
    public function __construct() {}

    /**
     * @param \WScore\Cena\CenaManager $cm
     */
    public function setCenaManager( $cm ) {
        $this->cm = $cm;
    }
    /**
     * @param $data
     * @return $this
     */
    public function with( $data ) {
        $this->source = $data;
        return $this;
    }

    /**
     * process cena post data from html form.
     * 
     * @return bool
     */
    public function posts()
    {
        $source = $this->source[ $this->cm->cena ];
        $data   = array();
        foreach( $source as $entityName => $types ) {
            foreach( $types as $type => $ids ) {
                foreach( $ids as $id => $info ) {
                    $cenaID = $this->cm->construct->construct( $entityName, $type, $id );
                    $data[ $cenaID ] = $info;
                    if( $type == EntityAbstract::_ID_TYPE_VIRTUAL && $id > EntityAbstract::$_id_for_new ) {
                        // keep up with the largest *new* id.
                        EntityAbstract::$_id_for_new = $id+1;
                    }
                }
            }
        }
        return $this->process( $data );
    }

    /**
     * @param array $data
     * @return bool
     */
    public function process( $data )
    {
        $isValid = true;
        $role = $this->cm->getCenaIO();
        foreach( $data as $cenaID => $info )
        {
            $entity = $this->cm->getCenaEntity( $cenaID );
            $role->register( $entity );
            $role->load( $info );
            $isValid &= $role->validate();
        }
        return $isValid;
    }

    /**
     * removes data from input source (post-data) for new entities 
     * that does not have necessary value. 
     * 
     * @param string $entity
     * @param string $name
     * @param null $type
     * @return $this
     */
    public function clean( $entity, $name, $type=null )
    {
        if( !$type ) $type = EntityAbstract::_ID_TYPE_VIRTUAL;
        $entity = $this->cm->getEntityShortNameFromClass( $entity );
        $cena  = $this->cm->cena;
        if( !isset( $this->source[ $cena ] ) ) return $this;
        if( !isset( $this->source[ $cena ][ $entity ] ) ) return $this;
        if( !isset( $this->source[ $cena ][ $entity ][ $type ] ) ) return $this;
        foreach( $this->source[ $cena ][ $entity ][ $type ] as $id => $data ) {
            if( !isset( $data[ 'prop' ][ $name ] ) || empty( $data[ 'prop' ][ $name ] ) ) {
                unset( $this->source[ $cena ][ $entity ][ $type ][ $id ] );
            }
        }
        if( empty( $this->source[ $cena ][ $entity ][ $type ] ) ) {
            unset( $this->source[ $cena ][ $entity ][ $type ] );
        }
        return $this;
    }
}