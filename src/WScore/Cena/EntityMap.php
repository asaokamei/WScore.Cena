<?php
namespace WScore\Cena;

class EntityMap
{
    /**
     * @var array
     */
    public $entityMap = array();

    /**
     * @param string|object $entity
     */
    public function useEntity( $entity )
    {
        $short = $entity;
        if( strpos( $entity, '\\' ) !== false ) {
            $short = substr( $entity, strrpos( $entity, '\\' )+1 );
        }
        $this->entityMap[ $short ] = $entity;
    }

    /**
     * @param string $short
     * @return string
     */
    public function getEntityName( $short ) {
        return ( isset( $this->entityMap[ $short ] ) ) ?  $this->entityMap[ $short ] : $short;
    }
    
    /**
     * @param $entity
     * @return bool|int|string
     */
    public function getEntityShortNameFromClass( $entity ) 
    {
        foreach( $this->entityMap as $short => $class ) {
            if( $entity === $class ) return $short;
        }
        return $entity;
    }

}