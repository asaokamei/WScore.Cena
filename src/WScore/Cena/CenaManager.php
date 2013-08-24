<?php
namespace WScore\Cena;

use WScore\DataMapper\Entity\EntityInterface;
use WScore\DataMapper\Role\RoleInterface;

/**
 * Class CenaManager
 * @package WScore\Cena
 *
 * @singleton
 */
class CenaManager
{
    public $cena;

    public $connector;

    public $models = array();

    /**
     * @Inject
     * @var \WScore\Cena\Construct
     */
    public $construct;

    /** 
     * @Inject
     * @var \WScore\DataMapper\EntityManager 
     */
    public $em;

    /**
     * @Inject
     * @var \WScore\DataMapper\RoleManager 
     */
    public $role;

    /**
     * @Inject
     * @var \WScore\Cena\Processor
     */
    public $processor;

    /**
     */
    public function __construct()
    {
        $this->cena = $this->construct->cena;
        $this->connector = $this->construct->connector;
        $this->processor->setCenaManager( $this );
    }

    /**
     * @return \WScore\DataMapper\EntityManager
     */
    public function em() {
        return $this->em;
    }
    
    /**
     * @param string|EntityInterface $entity
     */
    public function useEntity( $entity )
    {
        $short = $entity;
        if( strpos( $entity, '\\' ) !== false ) {
            $short = substr( $entity, strrpos( $entity, '\\' )+1 );
        }
        $this->models[ $short ] = $entity;
    }

    /**
     * @param $entity
     * @return bool|int|string
     */
    public function getModelFromEntityClass( $entity ) {
        foreach( $this->models as $model => $class ) {
            if( $entity === $class ) return $model;
        }
        return $entity;
    }

    /**
     * @param string $cenaId
     * @return EntityInterface|EntityInterface[]
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
        $cenaId = $this->construct->unCompose( $cenaId );
        if( $entity = $this->em->getByCenaId( $cenaId ) ) {
            return $entity;
        }
        $list = $this->construct->decompose( $cenaId );
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
            return $this->em->newEntity( $model, array(), $id );
        }
        $collection = $this->em->fetch( $model, $id );
        return $collection[0];
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
    public function getFormName( $cenaId, $type='prop', $name=null ) {
        return $this->construct->composeFormName( $cenaId, $type, $name );
    }

    /**
     * @param array  $data
     * @param string $cenaId
     * @return array
     */
    public function getDataForCenaId( $data, $cenaId=null ) {
        return $this->construct->extractData( $data, $cenaId );
    }

    /**
     * @param EntityInterface|RoleInterface $entity
     * @return \WScore\Cena\Role\CenaIO
     */
    public function applyCenaIO( $entity )
    {
        return $this->role->applyRole( $entity, '\WScore\Cena\Role\CenaIO' );
    }

    /**
     * @return \WScore\Cena\Role\CenaIO
     */
    public function getCenaIO() {
        return $this->role->getRole( '\WScore\Cena\Role\CenaIO' );
    }
}