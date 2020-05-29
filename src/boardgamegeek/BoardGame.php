<?php

namespace GeekWrap;

/** 
 * GeekThing subclass for board games.
 * 
 * @author Matt Holden (mholden@darkenedsky.com)
 * @since 1.00
 */
 class BoardGame extends GeekThing
 {
    // various types of links...
    private $publishers = [];

    /** 
     * Construct a (lazy loaded) BoardGame.
     * @param $id int the Thing ID.
     */
     public function __construct(int $id) 
     {
        parent::__construct("https://www.boardgamegeek.com/xmlapi2/thing?id={id}", $id);
     }

     /**
     * Try to load a Thing from the API.
     * 
     * @return GeekThing $this
     * @throws GeekTimeoutException if the API call timed out.
     * @throws NotFoundException if the Thing doesn't exist.
     */
    public function load() : self
    {
        // Nothing to do
        if ($this->isLoaded()) { 
            return $this;
        }
        $xml = $this->getXML();

        if ($xml->xpath("/items/item/thumbnail")) {
            $this->thumbnail = new Image($xml->xpath("/items/item/thumbnail")->__toString());
        }
        $this->images = [];
        for ($i = 0; $i < count($xml->xpath("/items/item/image")); $i++) {
            $this->images[] = new Image($xml->xpath("/items/item/image")[$i]->__toString());
        }
        
        if ($xml->xpath("/items/item/description")) {
            $this->description = $xml->xpath("/items/item/description")->__toString();
        }

        $this->names = [];
        if ($xml->xpath("/items/item/name[@type='primary']") {
            $this->names[] = html_entity_decode($xml->xpath("/items/item/name[@type='primary']/@value")[0]);
        }  
        for ($i = 0; $i < count($xml->xpath("/items/item/name[@type='alternate']")); $i++) {
            $this->names[] = html_entity_decode($xml->xpath("/items/item/name[@type='primary']/@value")[$i]);
        }      
        if ($xml->xpath("/items/item/minplayers/@value")) {
            $this->minplayers = ((array)$xml->xpath("/items/item/minplayers/@value")[0])["@attributes"]["value"];
        }
        if ($xml->xpath("/items/item/maxplayers/@value")) {
            $this->maxplayers = ((array)$xml->xpath("/items/item/maxplayers/@value")[0])["@attributes"]["value"];
        }
        
        for ($i = 0; $i < count($xml->xpath("/items/item/link[@type='boardgamepublisher']")); $i++) {
            $thing = (array)$xml->xpath("/items/item/link[@type='boardgamepublisher']")[$i];
            $this->publishers[] = new BoardGamePublisher($thing["@attributes"]["id"], $thing["@attributes"]["value"]);
        }

        $this->isLoaded = true;
        return $this;
    }
 }