
<?php

require '/vendor/autoload.php'; // Ensure Cloudinary SDK is autoloaded

use Cloudinary\Cloudinary;
use Dotenv\Dotenv;

class ImageUploader
{
    private $cloudinary;

    public function __construct()
    {
        // Load .env file using an absolute path
        $dotenv = Dotenv::createImmutable('C:\xampp\htdocs\Plantwep-webshop-2');
        $dotenv->load();

        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => $_ENV['CLOUDINARY_CLOUD_NAME'],
                'api_key' => $_ENV['CLOUDINARY_API_KEY'],
                'api_secret' => $_ENV['CLOUDINARY_API_SECRET'],
            ]
        ]);
    }

    public function uploadImage($file)
    {
        if ($_SESSION['role'] !== 1) {
            throw new Exception('Geen toestemming om afbeeldingen te uploaden');
        }

        // Validate file is an image
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception('Bestand is geen afbeelding');
        }

        // Validate file size (5MB max)
        if ($file["size"] > 5000000) {
            throw new Exception('Sorry, het bestand is te groot');
        }

        // Upload to Cloudinary
        try {
            $uploadResult = $this->cloudinary->uploadApi()->upload(
                $file["tmp_name"],
                ['folder' => 'uploads']
            );
            return $uploadResult['secure_url']; // Return the Cloudinary URL
        } catch (Exception $e) {
            throw new Exception('Upload to Cloudinary failed: ' . $e->getMessage());
        }
    }
}
?>
