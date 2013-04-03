<?php
namespace WScore\Cena;

use \WScore\DataMapper\Entity\EntityInterface;
use \WScore\DataMapper\Entity\Collection;
use WScore\Html\Elements;

/**
 * Class CenaManager
 * @package WScore\Cena
 */
class Html
{
    /**
     * @Inject
     * @var \WScore\Cena\Construct
     */
    public $construct;

    /**
     * @Inject
     * @var \WScore\Html\Forms
     */
    public $forms;

    /**
     * @param \WScore\Html\Elements $html
     * @param EntityInterface       $entity
     * @param string                $type
     * @return void
     */
    public function populateFormName( $html, $entity, $type='prop' )
    {
        if( ! $html instanceof Elements ) return;
        $cenaId = $entity->getCenaId();
        $format = $this->construct->composeFormName( $cenaId, $type );
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
     * @param string                               $name
     * @param EntityInterface                      $entity
     * @param Collection|EntityInterface           $targets
     * @return \WScore\Html\Elements
     */
    public function composeHiddenLinks( $name, $entity, $targets )
    {
        /** @var $hideDivs \WScore\Html\Elements */
        $hideDivs = $this->forms->elements->div();
        if( $targets instanceof EntityInterface ) {
            $targets = array( $targets );
        }
        elseif( empty( $targets ) ) return $hideDivs;

        foreach( $targets as $target ) {

            /** @var $target \WScore\DataMapper\Entity\EntityInterface */
            $targetCenaId = $this->construct->compose( $target->getCenaId() );
            $tag = $this->forms->input( 'hidden', $name, $targetCenaId )->_setMultipleName();
            $hideDivs->_contain( $tag );
        }
        $this->populateFormName( $hideDivs, $entity, 'link' );
        return $hideDivs;
    }

    /**
     * @param string $name
     * @param EntityInterface $entity
     * @return \WScore\Html\Elements Elements
     */
    public function composeEmptyHiddenLink( $name, $entity )
    {
        $tag = $this->forms->input( 'hidden', $name, '' )->_setMultipleName();
        $this->populateFormName( $tag, $entity, 'link' );
        return $tag;
    }

    /**
     * creates a select box for a relation (many-to-many).
     *
     * @param EntityInterface                      $entity
     * @param string                               $name
     * @param \WScore\DataMapper\Entity\Collection $targets
     * @param \WScore\DataMapper\Entity\Collection $lists
     * @param string                               $display
     * @param string                               $select
     * @return \WScore\Html\Elements
     */
    public function composeLinks( $entity, $name, $targets, $lists, $display, $select='select' )
    {
        $links = array();
        foreach( $lists as $lst ) {
            /** @var $lst EntityInterface */
            $cenaId = $this->construct->compose( $lst->getCenaId() );
            $links[] = array( $cenaId, $lst[ $display ] );
        }
        $selected = array();
        if( !empty( $targets ) )
            foreach( $targets as $tgt ) {
                /** @var $tgt EntityInterface */
                $selected[] = $this->construct->compose( $tgt->getCenaId() );
            }
        /** @var $select Elements */
        $select = $this->forms->$select( $name, $links, $selected, array( 'multiple'=>true ) );
        $this->populateFormName( $select, $entity, 'link' );
        return $select;
    }
}