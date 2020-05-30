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
    ///////////// Matt D start /////////////
        private $catagories = [];
        private $mechanics = [];
        private $designers = [];
        private $families = [];
        private $expansions = [];
        private $compilations = [];
        private $implementations = [];
        private $artists = [];
        // TODO: 'polls' - language_dependence, suggested_playerage, suggested_numplayers
    ///////////// Matt D end /////////////

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
            // NOTE: Setting the 0th ?
            $this->names[] = html_entity_decode($xml->xpath("/items/item/name[@type='primary']/@value")[0]);
        }  
        for ($i = 0; $i < count($xml->xpath("/items/item/name[@type='alternate']")); $i++) {
            // NOTE: Is this pushing to names or resetting the primary on the i=0 iteration?
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

        ///////////// Matt D start /////////////
            if ($xml->xpath("/items/item/playingtime/@value")) {
                $this->playingtime = ((array)$xml->xpath("/items/item/playingtime/@value")[0])["@attributes"]["value"];
            }
            if ($xml->xpath("/items/item/minplaytime/@value")) {
                $this->minplaytime = ((array)$xml->xpath("/items/item/minplaytime/@value")[0])["@attributes"]["value"];
            }
            if ($xml->xpath("/items/item/maxplayers/@value")) {
                $this->maxplayers = ((array)$xml->xpath("/items/item/maxplayers/@value")[0])["@attributes"]["value"];
            }
            if ($xml->xpath("/items/item/yearpublished/@value")) {
                $this->yearpublished = ((array)$xml->xpath("/items/item/yearpublished/@value")[0])["@attributes"]["value"];
            }
            if ($xml->xpath("/items/item/minage/@value")) {
                $this->minage = ((array)$xml->xpath("/items/item/minage/@value")[0])["@attributes"]["value"];
            }
            for ($i = 0; $i < count($xml->xpath("/items/item/link[@type='boardgamecategory']")); $i++) {
                $thing = (array)$xml->xpath("/items/item/link[@type='boardgamecategory']")[$i];
                $this->categories[] = new BoardGameCategory($thing["@attributes"]["id"], $thing["@attributes"]["value"]);
            }
            for ($i = 0; $i < count($xml->xpath("/items/item/link[@type='boardgamemechanic']")); $i++) {
                $thing = (array)$xml->xpath("/items/item/link[@type='boardgamemechanic']")[$i];
                $this->mechanics[] = new BoardGameMechanic($thing["@attributes"]["id"], $thing["@attributes"]["value"]);
            }
            for ($i = 0; $i < count($xml->xpath("/items/item/link[@type='boardgamedesigner']")); $i++) {
                $thing = (array)$xml->xpath("/items/item/link[@type='boardgamedesigner']")[$i];
                $this->designers[] = new BoardGameDesigner($thing["@attributes"]["id"], $thing["@attributes"]["value"]);
            }
            for ($i = 0; $i < count($xml->xpath("/items/item/link[@type='boardgamefamily']")); $i++) {
                $thing = (array)$xml->xpath("/items/item/link[@type='boardgamefamily']")[$i];
                $this->families[] = new BoardGameFamily($thing["@attributes"]["id"], $thing["@attributes"]["value"]);
            }
            for ($i = 0; $i < count($xml->xpath("/items/item/link[@type='boardgameexpansion']")); $i++) {
                $thing = (array)$xml->xpath("/items/item/link[@type='boardgameexpansion']")[$i];
                $this->expansions[] = new BoardGameExpansion($thing["@attributes"]["id"], $thing["@attributes"]["value"]);
            }
            for ($i = 0; $i < count($xml->xpath("/items/item/link[@type='boardgamecompilation']")); $i++) {
                $thing = (array)$xml->xpath("/items/item/link[@type='boardgamecompilation']")[$i];
                $this->compilations[] = new BoardGameCompilation($thing["@attributes"]["id"], $thing["@attributes"]["value"]);
            }
            for ($i = 0; $i < count($xml->xpath("/items/item/link[@type='boardgameimplementation']")); $i++) {
                $thing = (array)$xml->xpath("/items/item/link[@type='boardgameimplementation']")[$i];
                $this->implementations[] = new BoardGameImplementation($thing["@attributes"]["id"], $thing["@attributes"]["value"]);
            }
            for ($i = 0; $i < count($xml->xpath("/items/item/link[@type='boardgameartist']")); $i++) {
                $thing = (array)$xml->xpath("/items/item/link[@type='boardgameartist']")[$i];
                $this->artists[] = new BoardGameArtist($thing["@attributes"]["id"], $thing["@attributes"]["value"]);
            }
        ///////////// Matt D end /////////////

        $this->isLoaded = true;
        return $this;
    }
 }