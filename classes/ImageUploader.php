<?php

require __DIR__ . '/../vendor/autoload.php';

// Use the Configuration and UploadApi classes
use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// Configure an instance of your Cloudinary cloud
Configuration::instance('cloudinary://275734831993742:AjBbLTBeOkpRREDfFRqEJUTqUf4@dqivw031o?secure=true');

class ImageUploader
{
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
            $uploadResult = (new UploadApi())->upload($file['tmp_name'], [
                'folder' => 'uploads/', // Optional: Specify folder in Cloudinary
                'public_id' => pathinfo($file["name"], PATHINFO_FILENAME),
                'overwrite' => true,
                'resource_type' => 'image',
            ]);
            return $uploadResult['secure_url']; // Return the Cloudinary URL
        } catch (Exception $e) {
            throw new Exception('Upload to Cloudinary failed: ' . $e->getMessage());
        }
    }
}
?>
