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
     * saves entities to database.
     */
    public function save();
    
    /**
     * fetch a entity from database or forge a new object.
     * should use getEntityByCenaId, instead.
     *
     * @param string $class
     * @param string $type
     * @param string $id
     * @return mixed
     */
    public function fetchEntity( $class, $type, $id );

    /**
     * @param       $class
     * @param array $data
     * @return object
     */
    public function newEntity( $class, $data=array() );


    /**
     * @param $class
     * @param $id
     * @return null|object
     */
    public function findEntity( $class, $id );

    /**
     * @param object $entity
     * @return mixed
     */
    public function getId( $entity );

    /**
     * @param object $entity
     * @return mixed
     */
    public function getIdName( $entity );

    /**
     * returns if the $entity object is marked as delete.
     * 
     * @param object $entity
     * @return mixed
     */
    public function isDeleted( $entity );

    /**
     * returns if the $entity object is retrieved from data base. 
     * 
     * @param $entity
     * @return mixed
     */
    public function isRetrieved( $entity );

    /**
     * returns if the $object is a collection of entities or not. 
     * 
     * @param object $object
     * @return mixed
     */
    public function isCollection( $object );
    
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
}