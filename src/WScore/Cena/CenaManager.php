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

    /**
     * @Inject
     * @var \WScore\Cena\EntityMap
     */
    public $entityMap;

    /**
     * @Inject
     * @var \WScore\Cena\Construct
     */
    public $construct;

    /**
     * @Inject
     * @var \WScore\Cena\EmAdapter\EmaWScore
     */
    public $ema;
    
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
        $this->ema->setEntityMap( $this->entityMap );
    }

    /**
     * @return EmAdapter\EmaWScore
     */
    public function ema() {
        return $this->ema;
    }
    /**
     * @return \WScore\DataMapper\EntityManager
     */
    public function em() {
        return $this->ema->em();
    }
    
    /**
     * @param string|EntityInterface $entity
     */
    public function useEntity( $entity )
    {
        $this->entityMap->useEntity( $entity );
    }

    /**
     * @param $entity
     * @return bool|int|string
     */
    public function getEntityShortNameFromClass( $entity ) 
    {
        return $this->entityMap->getEntityShortNameFromClass( $entity );
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
        $cenaId = $this->construct->removeHeader( $cenaId );
        return $entity = $this->ema->getEntityByCenaId( $cenaId );
    }

    /**
     * @param string $entityName
     * @param string $type
     * @param string $id
     * @return EntityInterface
     */
    public function getEntity( $entityName, $type, $id )
    {
        return $this->ema->fetchEntity( $entityName, $type, $id );
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