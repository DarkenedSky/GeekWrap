<?php

namespace GeekWrap;

/**
 * An abstract class for wrapping a Thing on the Board Game Geek / RPG Geek / VideoGameGeek website.
 * 
 * Uses __get / __set magic methods for tracking object properties.
 * 
 * Each class that extends this class should be named for the type in the Geek API, such as 
 * "BoardGamePublisher".
 * 
 * @author Matt Holden (mholden@darkenedsky.com)
 * @since 1.00
 *
 */
abstract class GeekThing
{
    /** Whether or not the object is loaded from BGG - lazy loading is possible for Things. */
    protected $isLoaded = false;

    /** The URL for this Thing. */
    private $apiURL;

    /** The Thing ID on the Geek site. */
    private $id;

    /** Data from the XML */
    private $data = [ ];

    /** 
     * Construct a GeekThing. Does not load the data from the API unless $this->load() is called.
     */
    public function __construct(string $urlBase, int $id)
    {
        $this->apiURL = str_replace($urlBase, "{id}", trim(strval($id)));
        $this->id = $id;
        $this->isLoaded = false;
    } 

    /** 
     * Get the Thing's ID
     * @return int This Thing's ID.
     */
    public function id() : int
    {
        return $this->id;
    }

    /** 
     * Access a piece of the data. 
     * @param $field string Field name to get.
     * @return mixed The value of the field.
     */
    public function __get(string $field)
    {
        return $this->data[$field];
    }

    /** 
     * Set a piece of the data. 
     * @param $field string Field name to set.
     * @param $value mixed The value of the field.
     */
    public function __set(string $field, $value)
    {
        $this->data[$field] = html_entity_decode($value);
    }

    /** 
     * Get the API endpoint for this Thing.
     * @return string the API endpoint associated with this Thing.
     */
    public function getURL() : string
    {
        return $this->apiURL;
    }

    /**
     * Get the data for this Thing from the API.
     * @return SimpleXMLElement the XML root element that was loaded.
     * @throws GeekTimeoutException if the API call timed out.
     * @throws NotFoundException if the Thing doesn't exist.
     */
    protected function getXML() : SimpleXMLElement
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_URL, $this->getURL());
        curl_setopt($ch, CURLOPT_SSLVERSION,3);
        $result = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($responseCode == 404) {
            throw new NotFoundException();
        }
        if ($responseCode == 204) {
            throw new TimeoutException();
        }

        $element = simplexml_load_string($result);
        return $element;
    }

    /** 
     * Check if we have loaded the Thing yet.
     * @return bool true if we have loaded the thing.
     */
    public function isLoaded() : bool
    {
        return $this->isLoaded;
    }

    /**
     * Try to load a Thing from the API.
     * 
     * @return GeekThing $this
     * @throws GeekTimeoutException if the API call timed out.
     * @throws NotFoundException if the Thing doesn't exist.
     */
    public abstract function load() : self;

}
