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

    public function posts()
    {
        $source = $this->source[ $this->cm->cena ];
        $data   = array();
        foreach( $source as $model => $types ) {
            foreach( $types as $type => $ids ) {
                foreach( $ids as $id => $info ) {
                    $cenaID = $this->cm->construct->construct( $model, $type, $id );
                    $data[ $cenaID ] = $info;
                }
            }
        }
        $this->process( $data );
    }

    /**
     * @param array $data
     */
    public function process( $data )
    {
        foreach( $data as $cenaID => $info )
        {
            $entity = $this->cm->getCenaEntity( $cenaID );
            $role   = $this->cm->applyCenaIO( $entity );
            $role->load( $info );
        }
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
        $model = $this->cm->getModelFromEntityClass( $entity );
        $cena  = $this->cm->cena;
        if( !isset( $this->source[ $cena ] ) ) return;
        if( !isset( $this->source[ $cena ][ $model ] ) ) return;
        if( !isset( $this->source[ $cena ][ $model ][ $type ] ) ) return;
        foreach( $this->source[ $cena ][ $model ][ $type ] as $id => $data ) {
            if( !isset( $data[ 'prop' ][ $name ] ) || empty( $data[ 'prop' ][ $name ] ) ) {
                unset( $this->source[ $cena ][ $model ][ $type ][ $id ] );
            }
        }
        if( empty( $this->source[ $cena ][ $model ][ $type ] ) ) {
            unset( $this->source[ $cena ][ $model ][ $type ] );
        }
        return $this;
    }
}