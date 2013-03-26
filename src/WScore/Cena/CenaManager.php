<?php
namespace WScore\Cena;

use WScore\DataMapper\Entity\EntityInterface;

class CenaManager
{
    public $cena = 'Cena';

    public $connector = '.';

    public $models = array();
    
    /** 
     * @Inject
     * @var \WScore\DataMapper\EntityManager 
     */
    protected $em;

    /**
     * @Inject
     * @var \WScore\DataMapper\RoleManager 
     */
    protected $role;


    /**
     * @param string $cenaId
     * @return \WScore\DataMapper\EntityManager[]
     */
    public function getCenaEntity( $cenaId )
    {
        if( is_array( $cenaId ) ) {
            $entities = array();
            foreach( $cenaId as $cId ) {
                $entities[] = $this->getCenaEntity( $cId );
            }
            return $entities;
        }
        $list = explode( $this->connector, $cenaId );
        if( $list[0] == $this->cena ) array_shift( $list );
        if( count( $list ) < 3 ) return null;
        return $this->getEntity( $list[0], $list[1], $list[2] );
    }

    /**
     * @param string $model
     * @param string $type
     * @param string $id
     * @return EntityInterface
     */
    public function getEntity( $model, $type, $id )
    {
        if( isset( $this->models[ $model ] ) ) $model = $this->models[ $model ];
        if( $type == EntityInterface::_ID_TYPE_VIRTUAL ) {
            return $this->em->newEntity( $model, $id );
        }
        return $this->em->fetch( $model, $id );
    }

    // +----------------------------------------------------------------------+
    //  utility methods. 
    // +----------------------------------------------------------------------+
    /**
     * returns cena-formatted name for form elements.
     *
     * @param string  $cenaId
     * @param string  $type
     * @param null    $name
     * @return string
     */
    public function getFormName( $cenaId, $type='prop', $name=null )
    {
        $cena = explode( $this->connector, $cenaId );
        $formName = $this->cena . '[' . implode( '][', $cena ) . "][{$type}]";
        if( $name ) $formName .= "[{$name}]";
        return $formName;
    }

    /**
     * @param array  $data
     * @param string $cenaId
     * @return array
     */
    public function getDataForCenaId( $data, $cenaId=null )
    {
        // the data is not in Cena format. 
        // return the data as is. 
        if( !isset( $data[ $this->cena ] ) ) return $data;
        // OK, got Cena formatted data. 
        $data = $data[ $this->cena ];
        if( !$cenaId ) return $data;

        // get data for a specific cenaID. 
        $cena = explode( '.', $cenaId );
        foreach( $cena as $item ) {
            if( !isset( $data[ $item ] ) ) return array();
            $data = $data[ $item ];
        }
        return $data;
    }

    /**
     * @param EntityInterface $entity
     * @return \WScore\Cena\Role\CenaIO
     */
    public function DataIO( $entity )
    {
        return $this->role->applyRole( $entity, '\WScore\Cena\Role\CenaIO' );
    }
}