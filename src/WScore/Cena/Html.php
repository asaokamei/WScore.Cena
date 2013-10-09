<?php
namespace WScore\Cena;

use \WScore\DataMapper\Entity\Collection;
use WScore\Html\Elements;

/**
 * Class CenaManager
 * @package WScore\Cena
 *
 * @cacheable
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
     * @Inject
     * @var \WScore\Cena\EmAdapter\EmaWScore
     */
    public $ema;

    /**
     * @param \WScore\Html\Elements $html
     * @param object                $entity
     * @param string                $type
     * @return void
     */
    public function populateFormName( $html, $entity, $type='prop' )
    {
        if( ! $html instanceof Elements ) return;
        $cenaId = $this->ema->getCenaIdByEntity( $entity );
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
                $form->_attributes[ 'name' ] = $format;
                if( $name ) {
                    $form->_attributes[ 'name' ] .= '[' . $name . ']' . $post;
                }
            }
        };
        $html->_walk( $makeCena, 'name' );
    }

    /**
     * creates a hidden tag for a relation (HasOne or HasRefs).
     *
     * @param string                               $name
     * @param object                               $entity
     * @param array|object            $targets
     * @return \WScore\Html\Elements
     */
    public function composeHiddenLinks( $name, $entity, $targets )
    {
        /** @var $hideDivs \WScore\Html\Elements */
        $hideDivs = $this->forms->elements->div();
        if( !$this->ema->isCollection( $targets ) ) {
            $targets = array( $targets );
        }
        elseif( empty( $targets ) ) return $hideDivs;

        foreach( $targets as $target ) {
            $tag = $this->composeHiddenLink( $name, $target );
            $hideDivs->_contain( $tag );
        }
        $this->populateFormName( $hideDivs, $entity, 'link' );
        return $hideDivs;
    }

    /**
     * @param string $name
     * @param object $entity
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
     * @param object                               $entity
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
            $cenaId = $this->construct->appendHeader(
                $this->ema->getCenaIdByEntity( $lst )
            );
            $links[] = array( $cenaId, $lst[ $display ] );
        }
        $selected = array();
        if( !empty( $targets ) )
            foreach( $targets as $tgt ) {
                $selected[] = $this->construct->appendHeader(
                    $this->ema->getCenaIdByEntity( $tgt )
                );
            }
        /** @var $select Elements */
        $select = $this->forms->$select( $name, $links, $selected, array( 'multiple'=>true ) );
        $this->populateFormName( $select, $entity, 'link' );
        return $select;
    }

    /**
     * @param object   $entity
     * @return Elements
     */
    public function composeDeleteCheck( $entity )
    {
        $tag = $this->forms->input( 'checkbox', '', '1' );
        $this->populateFormName( $tag, $entity, 'del' );
        if( $this->ema->isDeleted( $entity ) ) {
            $tag->checked( true );
        }
        return $tag;
    }

    /**
     * @param object   $entity
     * @return Elements
     */
    public function composeDeleteSelect( $entity )
    {
        $links  = array();
        $selected = array();
        if( $this->ema->isRetrieved( $entity ) ) {
            $links[] = array( '0', 'edit' );
            $links[] = array( '1', 'delete' );
            if( $this->ema->isDeleted( $entity ) ) {
                $selected[] = '1';
            }
        } else {
            $links[] = array( '0', 'new' );
        }
        $select = $this->forms->select( '', $links, $selected );
        $this->populateFormName( $select, $entity, 'del' );
        return $select;
    }

    /**
     * @param $name
     * @param $target
     * @return Elements
     */
    public function composeHiddenLink( $name, $target )
    {
        $targetCenaId = $this->construct->appendHeader(
            $this->ema->getCenaIdByEntity( $target )
        );
        $tag = $this->forms->input( 'hidden', $name, $targetCenaId )->_setMultipleName();
        return $tag;
    }
}