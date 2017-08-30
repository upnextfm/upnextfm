<?php
namespace AppBundle\Service;

use Aws\S3\S3Client;
use Psr\Log\LoggerAwareTrait;
use InvalidArgumentException;

class UploadService
{
  use LoggerAwareTrait;

  /**
   * @var S3Client
   */
  protected $s3;

  /**
   * @var array
   */
  protected $buckets = [];

  /**
   * @var string
   */
  protected $uploadRootURL;

  /**
   * @param S3Client $s3
   * @param array $buckets
   * @param string $uploadRootURL
   */
  public function __construct(S3Client $s3, array $buckets, $uploadRootURL)
  {
    $this->s3            = $s3;
    $this->buckets       = $buckets;
    $this->uploadRootURL = $uploadRootURL;
  }

  /**
   * @param string $source
   * @param string $dest
   * @param string $bucket
   * @return string
   */
  public function upload($source, $dest, $bucket = "uploads")
  {
    return $this->uploadData(fopen($source, "r"), $dest, $bucket);
  }

  /**
   * @param string $data
   * @param string $dest
   * @param string $bucket
   * @return string
   */
  public function uploadData($data, $dest, $bucket = "uploads")
  {
    $dest = trim($dest, '/ ');
    if (!isset($this->buckets[$bucket])) {
      throw new InvalidArgumentException(sprintf(
        'Invalid upload bucket "%s".',
        $bucket
      ));
    }

    $this->s3->putObject([
      "Bucket" => $this->buckets[$bucket],
      "Key"    => $dest,
      "Body"   => $data,
      "ACL"    => "public-read",
    ]);

    return sprintf('%s/%s/%s', $this->uploadRootURL, $this->buckets[$bucket], $dest);
  }
}
