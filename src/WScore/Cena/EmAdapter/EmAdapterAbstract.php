<?php
namespace WScore\Cena\EmAdapter;

use WScore\Cena\EntityMap;

abstract class EmAdapterAbstract implements EmAdapterInterface
{
    /*
     * constants for keeping the validation info in entities.
     */
    const IS_VALID_NAME = 'isValid';
    const ERROR_NAME    = 'error';

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
}