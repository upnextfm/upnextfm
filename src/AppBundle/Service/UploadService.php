<?php
namespace AppBundle\Service;

use Symfony\Component\Security\Core\User\UserInterface;
use Psr\Log\LoggerAwareTrait;
use AppBundle\Entity\Upload;
use AppBundle\Entity\UploadRepository;
use AppBundle\Entity\User;
use Aws\S3\S3Client;
use InvalidArgumentException;

class UploadService
{
    use LoggerAwareTrait;

  /**
   * @var S3Client
   */
    protected $s3;

  /**
   * @var UploadRepository
   */
    protected $uploadRepository;

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
   * @param UploadRepository $uploadRepository
   * @param array $buckets
   * @param string $uploadRootURL
   */
    public function __construct(S3Client $s3, UploadRepository $uploadRepository, array $buckets, $uploadRootURL)
    {
        $this->s3               = $s3;
        $this->uploadRepository = $uploadRepository;
        $this->buckets          = $buckets;
        $this->uploadRootURL    = $uploadRootURL;
    }

  /**
   * @param string $source
   * @param string $dest
   * @param UserInterface $user
   * @param string $mime
   * @param string $bucket
   * @return string
   */
    public function upload($source, $dest, UserInterface $user, $mime, $bucket = "uploads")
    {
        return $this->uploadData(
            fopen($source, "r"),
            $dest,
            $user,
            $mime,
            $bucket
        );
    }

  /**
   * @param string $data
   * @param string $dest
   * @param UserInterface|User $user
   * @param string $mime
   * @param string $bucket
   * @return string
   */
    public function uploadData($data, $dest, UserInterface $user, $mime, $bucket = "uploads")
    {
        $dest = trim($dest, '/ ');
        if (!isset($this->buckets[$bucket])) {
            throw new InvalidArgumentException(sprintf(
                'Invalid upload bucket "%s".',
                $bucket
            ));
        }

        if (is_resource($data)) {
            $stat = fstat($data);
            $size = $stat["size"];
        } elseif (is_string($data)) {
            $size = strlen($data);
        } else {
            throw new InvalidArgumentException("Upload data must be resource or string.");
        }

        $resp = $this->s3->putObject([
        "Bucket"      => $this->buckets[$bucket],
        "Key"         => $dest,
        "Body"        => $data,
        "ContentType" => $mime,
        "ACL"         => "public-read",
        ]);

        $upload = new Upload();
        $upload->setUser($user);
        $upload->setSize($size);
        $upload->setMime($mime);
        $upload->setPath($dest);
        $this->uploadRepository->persistAndFlush($upload);

        return $resp->get("ObjectURL");
    }
}
