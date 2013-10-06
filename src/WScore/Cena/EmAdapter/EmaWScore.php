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
        return $this->em->getByCenaId( $cenaId );
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