<?php
namespace WScore\Cena\Role;

use WScore\Html\Elements;

class CenaIO //extends DataIO
{
    /** @var object */
    public $entity;

    /** @var string */
    public $htmlType = 'html';

    /**
     * @Inject
     * @var \WScore\Cena\CenaManager
     */
    public $cena;

    /**
     * @Inject
     * @var \WScore\Cena\Html
     */
    public $html;

    // +----------------------------------------------------------------------+
    /**
     * @param object    $entity
     */
    public function register( $entity ) {
        $this->entity = $entity;
    }

    /**
     * @return object
     */
    public function retrieve() {
        return $this->entity;
    }

    /**
     * @return null|string
     */
    public function getId() {
        return $this->cena->ema()->getId( $this->entity );
    }

    /**
     * @return string
     */
    public function getIdName() {
        return $this->cena->ema()->getIdName( $this->entity );
    }

    // +----------------------------------------------------------------------+
    //  Presentation
    // +----------------------------------------------------------------------+
    /**
     * @param null|string $type
     * @return string
     */
    public function setHtmlType( $type=null ) {
        if( isset( $type ) ) $this->htmlType = $type;
        return $this->htmlType;
    }

    public function form( $name ) {
        return $this->cena->ema()->getSelector( $this->entity, $name );
    }

    /**
     * pops value of the $name (property name).
     * returns html-safe value if html_type is 'html',
     * returns html form element if html_type is 'form'.
     *
     * CenaIO returns form with cena-formatted name such as
     *    name="Cena[model][get][id]"
     *
     * @param string $name
     * @param null   $html_type
     * @return mixed
     */
    public function popHtml( $name, $html_type=null )
    {
        if( !$html_type ) $html_type = $this->htmlType;
        $form  = $this->form( $name );
        $value = isset( $this->entity->$name ) ? $this->entity->$name: '';
        $html  = $form->popHtml( $html_type, $value );
        $this->populateFormName( $html );
        return $html;
    }

    /**
     * @param array  $data
     * @param string $method
     * @return $this
     */
    public function load( $data=array(), $method='set' )
    {
        if( empty( $data ) ) $data = $_POST;
        if( isset( $data[ 'del' ] ) && $data[ 'del' ] ) {
            // delete this entity. 
            $this->entity->toDelete( true );
        }
        $this->loadData( $data );
        $this->loadLink( $data, $method );
        return $this;
    }
    
    /**
     * @param array       $data
     * @return self
     */
    public function loadData( $data=array() )
    {
        if( empty( $data ) ) $data = $_POST;
        $data = $this->cena->getDataForCenaId( $data, $this->entity->getCenaId() );
        $this->cena->ema()->loadData( $this->entity, $data[ 'prop' ] );
        return $this;
    }

    /**
     * @param string $name
     * @param object|object[] $targets
     */
    public function relate( $name, $targets )
    {
        $this->cena->ema()->relate( $this->entity, $name, $targets );
    }

    /**
     * @param array  $data
     * @param string $method
     * @return self
     */
    public function loadLink( $data=array(), $method='set' )
    {
        if( empty( $data ) ) $data = $_POST;
        $data = $this->cena->getDataForCenaId( $data, $this->entity->getCenaId() );
        if( empty( $data[ 'link' ] ) ) return $this;
        foreach( $data[ 'link' ] as $name => $link ) {
            $target = $this->cena->getCenaEntity( $link );
            $this->relate( $name, $target );
        }
        return $this;
    }

    /**
     * @param \WScore\Html\Tags $html
     * @param string            $type
     * @return void
     */
    protected function populateFormName( $html, $type='prop' )
    {
        if( ! $html instanceof Elements ) return;
        $this->html->populateFormName( $html, $this->entity, $type );
    }

    /**
     * creates a hidden tag for a relation (HasOne or HasRefs).
     *
     * @param string $name
     * @return \WScore\Html\Elements
     */
    public function popLinkHidden( $name )
    {
        $targets  = $this->entity->$name;
        return $this->html->composeHiddenLinks( $name, $this->entity, $targets );
    }

    /**
     * @param $name
     * @return Elements
     */
    public function popEmptyLink( $name ) {
        return $this->html->composeEmptyHiddenLink( $name, $this->entity );
    }

    /**
     * creates a select box for a relation (many-to-many).
     *
     * @param string                               $name
     * @param \WScore\DataMapper\Entity\Collection $lists
     * @param string                               $display
     * @param string                               $select
     * @return \WScore\Html\Elements
     */
    public function popLinkSelect( $name, $lists, $display, $select='select' )
    {
        $targets = $this->entity->$name;
        return $this->html->composeLinks( $this->entity, $name, $targets, $lists, $display, $select );
    }

    /**
     * @return Elements
     */
    public function popDeleteCheck()
    {
        $check = $this->html->composeDeleteCheck( $this->entity );
        return $check;
    }
    
    public function popDeleteSelect()
    {
        $select = $this->html->composeDeleteSelect( $this->entity );
        return $select;
    }

    // +----------------------------------------------------------------------+
    //  validation
    // +----------------------------------------------------------------------+
    /**
     * validates the entity's property values.
     *
     * @return bool
     */
    public function validate()
    {
        return $this->cena->ema()->validate( $this->entity );
    }

    /**
     * @return bool|null
     */
    public function isValid() {
        return $this->cena->ema()->isValid( $this->entity );
    }

    /**
     * resets valid state of the entity.
     * @return self
     */
    public function resetValid() {
        $this->cena->ema()->resetValid( $this->entity );
        return $this;
    }

    /**
     * returns error message if any.
     *
     * @param $key
     * @return mixed
     */
    public function getError( $key ) {
        return $this->cena->ema()->getError( $this->entity, $key );
    }

    /**
     * @param $key
     * @return bool
     */
    public function isError( $key ) {
        return $this->cena->ema()->isError( $this->entity, $key );
    }
}