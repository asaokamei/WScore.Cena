<?php
namespace WScore\Cena\EmAdapter;

use WScore\Cena\EmAdapter\EmAdapterInterface;
use WScore\Cena\EntityMap;
use WScore\DataMapper\Entity\EntityInterface;
use WScore\Selector\ElementAbstract;
use WScore\Selector\ElementItemizedAbstract;

/**
 * Class EmaWScore
 *
 * @package WScore\Cena\EmAdapter
 *
 * @singleton
 * 
 */
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
     * @var EntityMap
     */
    public $entityMap;

    /**
     * @param EntityMap $map
     * @return mixed|void
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
     * @param string $model
     * @param string $type
     * @param string $id
     * @return mixed|EntityInterface
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
     * @param string $cenaId
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

    /**
     * @param EntityInterface $entity
     * @return string|void
     */
    public function getCenaIdByEntity( $entity )
    {
        return  $entity->getCenaId();
    }

    /**
     * @param EntityInterface $entity
     * @param array $data
     * @return mixed|void
     */
    public function loadData( $entity, $data )
    {
        $model = $this->em->getModel( $entity );
        $data = $model->protect( $data );
        foreach( $data as $key => $value ) {
            $entity[ $key ] = $value;
        }
    }

    /**
     * @param EntityInterface $entity
     * @param string $name
     * @param EntityInterface $target
     * @return mixed|void
     */
    public function relate( $entity, $name, $target )
    {
        $this->em->relation( $entity, $name )->set( $target );
    }

    /**
     * @param EntityInterface $entity
     * @param string $key
     * @return ElementAbstract|ElementItemizedAbstract
     */
    public function getSelector( $entity, $key )
    {
        $model = $this->em->getModel( $entity );
        return $model->getSelector( $key );
    }

    /**
     * mark an $entity object as delete.
     *
     * @param EntityInterface $entity
     * @return bool
     */
    public function isDeleted( $entity )
    {
        return $entity->toDelete();
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function isRetrieved( $entity )
    {
        return $entity->isIdPermanent();
    }

    /**
     * @param object|EntityInterface $object
     * @return bool|mixed
     */
    public function isCollection( $object ) {
        if( $object instanceof EntityInterface ) {
            return false;
        }
        return true;
    }
}