<?php

namespace GeekWrap;

/** 
 * Wrapper class for images from the Geek API.
 * 
 * @author Matt Holden (mholden@darkenedsky.com)
 * @since 1.00
 */
 class Image
 {
    /** URL of the image. */
    private $url;

    /**
     * Construct an Image.
     * @param $url string URL of the image.
     */
     public function __construct(string $url)
     {
        $this->url = html_entity_decode($url);
     }

     /**
      * Accessor for the image URL.
      * @return string The image URL.
      */
      public function getURL() : string
      {
        return $this->url;
      }

      /** 
       * Download the image and put it in the specified folder. 
       *
       * @param $folder string Folder on the local filestructure to put the image in.
       * @param $name string|null Name to give the file. If null, keep its current name.
       * @throws Exception if something goes wrong
       */
       public function download(string $folder, ?string $filename = null)
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

            // Default to the current filename
            if ($filename === null) {
                $tokens = explode("/", $this->getURL());
                for ($i = count($tokens)-1, $i >= 0; $i--) {
                    if ($tokens[$i] !== "") {
                        $filename = $tokens[$i];
                        break;
                    }
                }
            }

            $path = $folder;
            if (substr($path, strlen($path)-1, 1) !== "/") {
                $path .= "/";
            }
            $path .= $filename;

            $file = fopen($path);
            fwrite($file, $result);
            fclose($file);
       }
 }