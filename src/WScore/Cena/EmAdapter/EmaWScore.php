<?php
namespace WScore\Cena\EmAdapter;

use WScore\Cena\EmAdapter\EmAdapterInterface;
use WScore\DataMapper\Entity\EntityInterface;

class EmaWScore implements EmAdapterInterface
{
    /**
     * @Inject
     * @var \WScore\DataMapper\EntityManager
     */
    public $em;
    
    /**
     * @Inject
     * @var \WScore\Cena\Construct
     */
    public $construct;

    /**
     * @var \WScore\Cena\EntityMap
     */
    public $entityMap;

    /**
     * @param \WScore\Cena\EntityMap $map
     */
    public function setEntityMap( $map ) {
        $this->entityMap = $map;
    }
    /**
     * @return \WScore\DataMapper\EntityManager
     */
    public function em() {
        return $this->em;
    }

    /**
     * @param $model
     * @param $type
     * @param $id
     * @return EntityInterface
     */
    public function fetchEntity( $model, $type, $id )
    {
        $model = $this->entityMap->getEntityName( $model );
        if( $type == EntityInterface::_ID_TYPE_VIRTUAL ) {
            return $this->em->newEntity( $model, array(), $id );
        }
        $collection = $this->em->fetch( $model, $id );
        return $collection[0];
    }

    /**
     * @param $cenaId
     * @return bool|EntityInterface
     */
    public function getEntityByCenaId( $cenaId )
    {
        if( $entity = $this->em->getByCenaId( $cenaId ) ) {
            return $entity;
        }
        $list = $this->construct->decompose( $cenaId );
        if( count( $list ) < 3 ) return null;
        return $this->fetchEntity( $list[0], $list[1], $list[2] );
    }

    public function getCenaIdByEntity( $entity )
    {
        // TODO: Implement getCenaIdByEntity() method.
    }

    public function property( $entity, $key, $value )
    {
        // TODO: Implement property() method.
    }

    public function relate( $entity, $target )
    {
        // TODO: Implement relate() method.
    }

    public function getForm( $entity, $key )
    {
        // TODO: Implement getForm() method.
    }

    public function load( $entity, $data )
    {
        // TODO: Implement load() method.
    }
}