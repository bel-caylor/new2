<?php
/**
 * @license GPL-2.0-only
 *
 * Modified by kadencewp on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */ declare(strict_types=1);

namespace KadenceWP\KadenceBlocks\StellarWP\ProphecyMonorepo\ImageDownloader;

use KadenceWP\KadenceBlocks\Psr\Log\LoggerInterface;
use KadenceWP\KadenceBlocks\StellarWP\ProphecyMonorepo\ImageDownloader\Exceptions\ImageDownloadException;
use KadenceWP\KadenceBlocks\StellarWP\ProphecyMonorepo\ImageDownloader\Models\DownloadedImage;
use KadenceWP\KadenceBlocks\StellarWP\ProphecyMonorepo\ImageDownloader\Models\ResponseAdapter;
use KadenceWP\KadenceBlocks\StellarWP\ProphecyMonorepo\ImageDownloader\Sanitization\FileNameSanitizer;
use KadenceWP\KadenceBlocks\Symfony\Component\Filesystem\Filesystem;
use KadenceWP\KadenceBlocks\Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final class ImageDownloader
{
	/**
	 * @readonly
	 *
	 * @var \KadenceWP\KadenceBlocks\Symfony\Contracts\HttpClient\HttpClientInterface
	 */
	private $client;
	/**
	 * @readonly
	 *
	 * @var \KadenceWP\KadenceBlocks\Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;
	/**
	 * @readonly
	 *
	 * @var \KadenceWP\KadenceBlocks\Psr\Log\LoggerInterface
	 */
	private $logger;
	/**
	 * @readonly
	 *
	 * @var \KadenceWP\KadenceBlocks\StellarWP\ProphecyMonorepo\ImageDownloader\FileNameProcessor
	 */
	private $file;
	/**
	 * @readonly
	 *
	 * @var \KadenceWP\KadenceBlocks\StellarWP\ProphecyMonorepo\ImageDownloader\Sanitization\FileNameSanitizer
	 */
	private $sanitizer;
	/**
	 * @readonly
	 *
	 * @var int
	 */
	private $batch_size;
	/**
	 * @readonly
	 *
	 * @var bool
	 */
	private $seo_names = true;
	/**
	 * A collection of successfully downloaded images.
	 *
	 * @var array<int, array<string, DownloadedImage>>
	 */
	private $collected = [];

	/**
	 * A collection of nested exceptions.
	 *
	 * @var ImageDownloadException[]
	 */
	private $errors = [];

	public function __construct(HttpClientInterface $client, Filesystem $filesystem, LoggerInterface $logger, FileNameProcessor $file, FileNameSanitizer $sanitizer, int $batch_size, bool $seo_names = true) {
		$this->client     = $client;
		$this->filesystem = $filesystem;
		$this->logger     = $logger;
		$this->file       = $file;
		$this->sanitizer  = $sanitizer;
		$this->batch_size = $batch_size;
		$this->seo_names  = $seo_names;
	}

	/**
	 * Download a collection of images to a path on the server.
	 *
	 * @param array<array{
	 *     collection_slug: string,
	 *     image_type: string,
	 *     images: array<int, array{
	 *          id: int,
	 *          width: int,
	 *          height: int,
	 *          alt: string,
	 *          url: string,
	 *          photographer: string,
	 *          photographer_url: string,
	 *          avg_color: string,
	 *          sizes: non-empty-array<int,array{name: string, src: string}>
	 *     }>
	 *    }>         $image_response The response from our GraphQL images Query.
	 * @param string $path The directory to save images in.
	 *
	 * @throws ImageDownloadException|\Throwable
	 *
	 * @return array<int, array<string, DownloadedImage>> A list of arrays, indexed by their thumbnail size name.
	 */
	public function download(array $image_response, string $path = '/tmp/prophecy-images'): array {
		$responses = [];
		$batch     = $total = 0;

		$this->logger->notice(sprintf('Building requests in batches of %d...', $this->batch_size));

		foreach ($image_response as $collection) {
			foreach ($collection['images'] as $key => $image) {
				foreach ($image['sizes'] as $size) {
					// Use the alt tag as an SEO friendly filename. Prefix with the Pexels ID to ensure it remains unique.
					$name     = $this->seo_names ? $image['alt'] . '-' . $image['id'] : '';
					$filename = $this->sanitizer->sanitize($this->file->build_image_file_name($size['src'], $collection['image_type'], $name));

					try {
						$responses[] = new ResponseAdapter(
							$image['id'],
							$image['width'],
							$image['height'],
							$filename,
							$size['name'],
							$key,
							$image['alt'],
							$image['url'],
							$image['photographer'],
							$image['photographer_url'],
							$this->client->request('GET', $size['src'])
						);
					} catch (Throwable $e) {
						$this->errors[] = new ImageDownloadException(sprintf('The response contains an error for image: %s', $size['src']), 1, $e);
						$this->logger->error($e->getMessage(), $e->getTrace());
					}

					$batch++;

					if ($batch >= $this->batch_size) {
						$this->logger->info(sprintf('Batch sized reached. Downloading %d images...', count($responses)));
						$this->processResponses($responses, $path);
						$total += $this->batch_size;
						$this->logger->info(sprintf('Total processed: %d', $total));

						$responses = [];
						$batch     = 0;
					}
				}
			}

			if (! empty($responses)) {
				$this->logger->info(sprintf('Processing remaining responses: %d', count($responses)));
				$this->processResponses($responses, $path);
			}
		}

		if ($this->errors) {
			throw new ImageDownloadException('Some errors were detected when downloading images.', 1, array_pop($this->errors));
		}

		return $this->collected;
	}

	/**
	 * Process a batch of responses.
	 *
	 * @param ResponseAdapter[] $responses
	 *
	 * @throws \Throwable
	 */
	private function processResponses(array $responses, string $path): void {
		foreach ($responses as $response) {
			$ext           = pathinfo($response->filename, PATHINFO_EXTENSION);
			$file_location = $path . '/' . md5($response->filename . $response->size . $response->id) . ".$ext";

			try {
				$data = $response->response->getContent();

				// Save the file, so we can use getimagesize() to read the dimensions.
				$this->filesystem->dumpFile($file_location, $data);
				$save_path = $this->file->format_file_path_for_wordpress($file_location, $response);

				// Multiple thumbnail sizes can end up with the exact same file name.
				if ($this->filesystem->exists($save_path)) {
					$this->logger->warning(sprintf('Image already exists: %s', $save_path));
					$this->filesystem->remove($file_location);
				} else {
					// Rename the file to the same format as WordPress.
					$this->filesystem->rename($file_location, $save_path);
				}
			} catch (Throwable $e) {
				$this->errors[] = new ImageDownloadException(sprintf('Failed to save image: %s', $file_location), 1, $e);
				$this->logger->error($e->getMessage(), $e->getTrace());
				$this->filesystem->remove($file_location);
				continue;
			}

			$this->collected[$response->key][$response->size] = new DownloadedImage($response->id, $response->width, $response->height, $save_path, $response->size, $response->alt, $response->url, $response->photographer, $response->photographer_url);
		}
	}
}
