<?php
/**
 * Image Resize Class (PHP 8+ safe)
 * Supports: JPG, PNG, GIF
 */

class instantappy_resize
{
    private GdImage $image;
    private int $width;
    private int $height;
    private ?GdImage $imageResized = null;

    /**
     * Constructor
     */
  public function __construct( string $fileName ) {

	if ( ! extension_loaded( 'gd' ) ) {
		throw new Exception(
			esc_html__( 'GD extension is not enabled.', 'wapppress-builds-android-app-for-website' )
		);
	}

	if ( ! file_exists( $fileName ) ) {
		//error_log( 'Instantappy resize: missing file ' . $fileName );

		throw new Exception(
			esc_html__( 'Invalid image file provided.', 'wapppress-builds-android-app-for-website' )
		);
	}

	$image = $this->openImage( $fileName );

	if ( ! $image instanceof GdImage ) {
		throw new Exception(
			esc_html__( 'Unable to open image file.', 'wapppress-builds-android-app-for-website' )
		);
	}

	$this->image  = $image;
	$this->width  = imagesx( $this->image );
	$this->height = imagesy( $this->image );
}


    /**
     * Open image by extension
     */
    private function openImage(string $file): GdImage|false
    {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                return imagecreatefromjpeg($file);

            case 'png':
                $img = imagecreatefrompng($file);
                if ($img !== false) {
                    imagealphablending($img, false);
                    imagesavealpha($img, true);
                }
                return $img;

            case 'gif':
                return imagecreatefromgif($file);

            default:
                return false;
        }
    }

    /**
     * Resize image
     */
    public function resizeImage(int $newWidth, int $newHeight, string $option = 'auto'): void
    {
        $dimensions = $this->getDimensions($newWidth, $newHeight, $option);

        $optimalWidth  = (int) round($dimensions['optimalWidth']);
        $optimalHeight = (int) round($dimensions['optimalHeight']);

        $this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);

        // Preserve transparency
        imagealphablending($this->imageResized, false);
        imagesavealpha($this->imageResized, true);
        $transparent = imagecolorallocatealpha($this->imageResized, 0, 0, 0, 127);
        imagefill($this->imageResized, 0, 0, $transparent);

        imagecopyresampled(
            $this->imageResized,
            $this->image,
            0, 0, 0, 0,
            $optimalWidth,
            $optimalHeight,
            $this->width,
            $this->height
        );

        if ($option === 'crop') {
            $this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);
        }
    }

    /**
     * Get resize dimensions
     */
    private function getDimensions(int $newWidth, int $newHeight, string $option): array
    {
        return match ($option) {
            'exact' => [
                'optimalWidth'  => $newWidth,
                'optimalHeight' => $newHeight
            ],
            'portrait' => [
                'optimalWidth'  => ($this->width / $this->height) * $newHeight,
                'optimalHeight' => $newHeight
            ],
            'landscape' => [
                'optimalWidth'  => $newWidth,
                'optimalHeight' => ($this->height / $this->width) * $newWidth
            ],
            'crop' => $this->getOptimalCrop($newWidth, $newHeight),
            default => $this->getSizeByAuto($newWidth, $newHeight),
        };
    }

    /**
     * Auto resize logic
     */
    private function getSizeByAuto(int $newWidth, int $newHeight): array
    {
        if ($this->height < $this->width) {
            return [
                'optimalWidth'  => $newWidth,
                'optimalHeight' => ($this->height / $this->width) * $newWidth
            ];
        }

        if ($this->height > $this->width) {
            return [
                'optimalWidth'  => ($this->width / $this->height) * $newHeight,
                'optimalHeight' => $newHeight
            ];
        }

        return [
            'optimalWidth'  => $newWidth,
            'optimalHeight' => $newHeight
        ];
    }

    /**
     * Crop calculations
     */
    private function getOptimalCrop(int $newWidth, int $newHeight): array
    {
        $heightRatio = $this->height / $newHeight;
        $widthRatio  = $this->width  / $newWidth;
        $optimalRatio = min($heightRatio, $widthRatio);

        return [
            'optimalWidth'  => $this->width  / $optimalRatio,
            'optimalHeight' => $this->height / $optimalRatio
        ];
    }

    /**
     * Crop image
     */
    private function crop(int $optimalWidth, int $optimalHeight, int $newWidth, int $newHeight): void
    {
        $cropStartX = (int)(($optimalWidth  - $newWidth)  / 2);
        $cropStartY = (int)(($optimalHeight - $newHeight) / 2);

        $crop = imagecreatetruecolor($newWidth, $newHeight);

        imagealphablending($crop, false);
        imagesavealpha($crop, true);
        $transparent = imagecolorallocatealpha($crop, 0, 0, 0, 127);
        imagefill($crop, 0, 0, $transparent);

        imagecopyresampled(
            $crop,
            $this->imageResized,
            0, 0,
            $cropStartX, $cropStartY,
            $newWidth, $newHeight,
            $newWidth, $newHeight
        );

        imagedestroy($this->imageResized);
        $this->imageResized = $crop;
    }

    /**
     * Save resized image
     */
    public function saveImage(string $savePath, int $imageQuality = 100): void
    {
        $extension = strtolower(pathinfo($savePath, PATHINFO_EXTENSION));

        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($this->imageResized, $savePath, $imageQuality);
                break;

            case 'png':
                $pngQuality = 9 - round(($imageQuality / 100) * 9);
                imagepng($this->imageResized, $savePath, (int)$pngQuality);
                break;

            case 'gif':
                imagegif($this->imageResized, $savePath);
                break;

            default:
                throw new Exception('Unsupported output image type.');
        }

        imagedestroy($this->imageResized);
    }
}
