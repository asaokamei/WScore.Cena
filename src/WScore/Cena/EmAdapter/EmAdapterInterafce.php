<?php
namespace WScore\Cena\EmAdapter;

use WScore\Cena\EntityMap;

interface EmAdapterInterface
{
    /**
     * @param EntityMap $map
     * @return mixed
     */
    public function setEntityMap( $map );

    /**
     * @return mixed
     */
    public function em();
    
    /**
     * fetch a entity from database or forge a new object.
     * should use getEntityByCenaId, instead.
     *
     * @param string $model
     * @param string $type
     * @param string $id
     * @return mixed
     */
    public function fetchEntity( $model, $type, $id );

    /**
     * get an entity from entity manager collection. 
     * 
     * @param string $cenaId
     * @return object
     */
    public function getEntityByCenaId( $cenaId );

    /**
     * get CenaID from an entity object.
     *
     * @param object $entity
     * @return string
     */
    public function getCenaIdByEntity( $entity );

    /**
     * populate an entity with array data.
     *
     * @param object $entity
     * @param array $data
     * @return mixed
     */
    public function loadData( $entity, $data );

    /**
     * relate $entity with $target object by $name relation.
     *
     * @param object $entity
     * @param string $name
     * @param object $target
     * @return mixed
     */
    public function relate( $entity, $name, $target );

    /**
     * get a selector object for presentation of the $key of $entity object.
     *
     * @param object $entity
     * @param string $key
     * @return mixed
     */
    public function getSelector( $entity, $key );
}