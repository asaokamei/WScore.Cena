<?php
namespace WScore\Cena;

/**
 * Class CenaManager
 * @package WScore\Cena
 */
class Construct
{
    public $cena = 'Cena';

    public $connector = '.';

    /**
     * composes CenaID for external transfer, i.e. for html form and json.
     * just add 'Cena.' at the front. means 'model.type.id' -> 'Cena.model.type.id'.
     *
     * @param string $cenaId
     * @return string
     */
    public function compose( $cenaId )
    {
        if( substr( $cenaId, strlen( $this->cena )+1 ) !== $this->cena . $this->connector ) {
            $cenaId = $this->cena . $this->connector . $cenaId;
        }
        return $cenaId;
    }
    
    public function unCompose( $cenaId )
    {
        $head = $this->cena . $this->connector;
        if( substr( $cenaId, 0, strlen( $head ) ) === $head ) {
            $cenaId = substr( $cenaId, strlen( $head ) );
        }
        return $cenaId;
    }
    /**
     * de-compose cenaID into model, type, and id.
     * example: decompose( 'Cena.model.type.id' ) will return [ 'model', 'type', 'id' ].
     *
     * @param $cenaId
     * @return array
     */
    public function decompose( $cenaId )
    {
        $list = explode( $this->connector, $cenaId );
        if( $list[0] == $this->cena ) array_shift( $list );
        return $list;
    }

    public function construct( $model, $type, $id ) {
        return implode( $this->connector, array( $model, $type, $id ) );
    }

    /**
     * returns cena-formatted name for form elements.
     *
     * @param string  $cenaId
     * @param string  $type
     * @param null    $name
     * @return string
     */
    public function composeFormName( $cenaId, $type='prop', $name=null )
    {
        $cena = $this->decompose( $cenaId );
        $formName = $this->cena . '[' . implode( '][', $cena ) . "][{$type}]";
        if( $name ) $formName .= "[{$name}]";
        return $formName;
    }

    /**
     * extract data for Cena, or for CenaID.
     * usage: extractData( $_POST, 'Cena.model.1.10' );
     *
     * @param array  $data
     * @param string $cenaId
     * @return array
     */
    public function extractData( $data, $cenaId=null )
    {
        // the data is not in Cena format.
        // return the data as is.
        if( !isset( $data[ $this->cena ] ) ) return $data;
        // OK, got Cena formatted data.
        $data = $data[ $this->cena ];
        if( !$cenaId ) return $data;

        // get data for a specific cenaID.
        $cena = $this->decompose( $cenaId );
        foreach( $cena as $item ) {
            if( !isset( $data[ $item ] ) ) return array();
            $data = $data[ $item ];
        }
        return $data;
    }
}