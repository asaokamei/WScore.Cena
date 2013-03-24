<?php
namespace WScore\Cena;

class CenaManager
{
    public $cena = 'Cena';

    public $connector = '.';

    /** 
     * @Inject
     * @var \WScore\DataMapper\EntityManager 
     */
    protected $em;

    /**
     * @Inject
     * @var \WScore\DataMapper\RoleManager 
     */
    protected $role;

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
    public function getFormName( $cenaId, $type='prop', $name=null )
    {
        $cena = explode( $this->connector, $cenaId );
        $formName = $this->cena . '[' . implode( '][', $cena ) . "][{$type}]";
        if( $name ) $formName .= "[{$name}]";
        return $formName;
    }

    /**
     * @param \WScore\DataMapper\Entity\EntityInterface $entity
     * @return \WScore\DataMapper\Role\RoleInterface
     */
    public function DataIO( $entity )
    {
        return $this->role->applyRole( $entity, '\WScore\Cena\Role\CenaIO' );
    }
}