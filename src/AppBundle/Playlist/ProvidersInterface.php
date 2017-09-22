<?php
/**
 * Created by PhpStorm.
 *
 * User: sean
 * Date: 8/29/17
 * Time: 12:16 PM
 */
namespace AppBundle\Playlist;

interface ProvidersInterface
{
  /**
   * @param string $provider
   * @return bool
   */
    public static function isValidProvider($provider);

  /**
   * @param $mediaURL
   * @return array|null
   */
    public function parseURL($mediaURL);
}
