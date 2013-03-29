<?php
namespace WScore\Cena;

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
}