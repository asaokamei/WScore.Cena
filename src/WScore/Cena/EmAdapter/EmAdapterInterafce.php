<?php
namespace WScore\Cena\EmAdapter;

interface EmAdapterInterface
{
    public function getEntityByCenaId( $cenaId );
    
    public function getCenaIdByEntity( $entity );

    public function property( $entity, $key, $value );
    
    public function relate( $entity, $target );

    public function getForm( $entity, $key );
    
    public function load( $entity, $data );
}