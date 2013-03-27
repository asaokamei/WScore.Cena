<?php
namespace WScore\Cena\Role;

use \WScore\DataMapper\Entity\EntityInterface;
use WScore\DataMapper\Role\DataIO;
use WScore\Html\Elements;

class CenaIO extends DataIO
{
    /**
     * @Inject
     * @var \WScore\Cena\CenaManager
     */
    public $cena;

    /**
     * @Inject
     * @var \WScore\Html\Forms
     */
    public $forms;

    /**
     * pops value of the $name (property name).
     * returns html-safe value if html_type is 'html',
     * returns html form element if html_type is 'form'.
     *
     * Cenatar returns form with cena-formatted name such as
     *    name="Cena[model][get][id]"
     *
     * @param string $name
     * @param null   $html_type
     * @return mixed
     */
    public function popHtml( $name, $html_type=null )
    {
        $html = parent::popHtml( $name, $html_type );
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
        parent::load( $data[ 'prop' ] );
        return $this;
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
            $this->em->relation( $this->entity, $name )->$method( $target );
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
        $cenaId = $this->entity->getCenaId();
        $format = $this->cena->getFormName( $cenaId, $type );
        $makeCena = function( $form ) use( $format ) {
            /** @var $tags Elements */
            if( isset( $form->_attributes[ 'name' ] ) ) {
                $name = $form->_attributes[ 'name' ];
                $post = '';
                if( substr( $name, -2 ) == '[]' ) {
                    $name = substr( $name, 0, -2 );
                    $post = '[]';
                }
                $form->_attributes[ 'name' ] = $format . '[' . $name . ']' . $post;
            }
        };
        $html->_walk( $makeCena, 'name' );
    }

    /**
     * creates a hidden tag for a relation (HasOne or HasRefs).
     *
     * @param string $name
     * @return \WScore\Html\Elements
     */
    public function popLinkHidden( $name )
    {
        /** @var $targets \WScore\DataMapper\Entity\Collection */
        /** @var $hideDivs \WScore\Html\Elements */
        $targets  = $this->entity->$name;
        $hideDivs = $this->forms->elements->div();
        if( empty( $targets ) ) return $hideDivs;
        
        foreach( $targets as $target ) {
            
            /** @var $target \WScore\DataMapper\Entity\EntityInterface */
            $cenaId = $this->cena->cena . $this->cena->connector . $target->getCenaId();
            $tag = $this->forms->input( 'hidden', $name, $cenaId )->_setMultipleName();
            $this->populateFormName( $tag, 'link' );
            $hideDivs->_contain( $tag );
        }
        return $hideDivs;
    }

    /**
     * creates a select box for a relation (many-to-many).
     *
     * @param string                               $name
     * @param \WScore\DataMapper\Entity\Collection $lists
     * @param string                               $display
     * @return \WScore\Html\Elements
     */
    public function popLinkSelect( $name, $lists, $display, $select='select' )
    {
        $links = array();
        foreach( $lists as $entity ) {
            /** @var $entity EntityInterface */
            $cenaId = $this->cena->cena . $this->cena->connector . $entity->getCenaId();
            $links[] = array( $cenaId, $entity[ $display ] );
        }
        $targets = $this->entity->$name;
        $selected = array();
        if( !empty( $targets ) )
            foreach( $targets as $tgt ) {
                /** @var $tgt EntityInterface */
                $selected[] = $this->cena->cena . $this->cena->connector . $tgt->getCenaId();
            }
        $select = $this->forms->$select( $name, $links, $selected, array( 'multiple'=>true ) );
        $this->populateFormName( $select, 'link' );
        return $select;
    }

}