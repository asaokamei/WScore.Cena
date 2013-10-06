<?php
namespace WScore\Cena\EmAdapter;

interface EmAdapterInterface
{
    public function em();
    
    /**
     * fetch a entity from database or forge a new object.
     * 
     * @param $model
     * @param $type
     * @param $id
     * @return mixed
     */
    public function fetchEntity( $model, $type, $id );

    /**
     * get an entity from entity manager collection. 
     * 
     * @param $cenaId
     * @return mixed
     */
    public function getEntityByCenaId( $cenaId );
    
    public function getCenaIdByEntity( $entity );

    public function property( $entity, $key, $value );
    
    public function relate( $entity, $target );

    public function getForm( $entity, $key );
    
    public function load( $entity, $data );
}