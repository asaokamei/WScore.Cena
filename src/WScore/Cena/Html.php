<?php
namespace WScore\Cena;

use \WScore\DataMapper\Entity\EntityInterface;
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
     * @param string            $cenaId
     * @param string            $type
     * @return void
     */
    public function populateFormName( $html, $cenaId, $type='prop' )
    {
        if( ! $html instanceof Elements ) return;
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
     * @param string $name
     * @param \WScore\DataMapper\Entity\Collection $targets
     * @return \WScore\Html\Elements
     */
    public function composeHiddenLinks( $name, $targets )
    {
        /** @var $hideDivs \WScore\Html\Elements */
        $hideDivs = $this->forms->elements->div();
        if( empty( $targets ) ) return $hideDivs;

        foreach( $targets as $target ) {

            /** @var $target \WScore\DataMapper\Entity\EntityInterface */
            $cenaId = $this->construct->compose( $target->getCenaId() );
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
     * @param \WScore\DataMapper\Entity\Collection $targets
     * @param \WScore\DataMapper\Entity\Collection $lists
     * @param string                               $display
     * @param string                               $select
     * @return \WScore\Html\Elements
     */
    public function composeLinks( $name, $targets, $lists, $display, $select='select' )
    {
        $links = array();
        foreach( $lists as $entity ) {
            /** @var $entity EntityInterface */
            $cenaId = $this->construct->compose( $entity->getCenaId() );
            $links[] = array( $cenaId, $entity[ $display ] );
        }
        $selected = array();
        if( !empty( $targets ) )
            foreach( $targets as $tgt ) {
                /** @var $tgt EntityInterface */
                $selected[] = $this->construct->compose( $tgt->getCenaId() );
            }
        /** @var $select Elements */
        $select = $this->forms->$select( $name, $links, $selected );
        $select->_setMultipleName();
        $this->populateFormName( $select, 'link' );
        return $select;
    }
}