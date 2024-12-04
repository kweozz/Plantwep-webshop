
<?php
class ImageUploader
{
    public function uploadImage($file)
    {
        if ($_SESSION['role'] !== 1) {
            throw new Exception('Geen toestemming om afbeeldingen te uploaden');
        }
    
        $targetDir = __DIR__ . "/../images/uploads/";
        $targetFile = $targetDir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
        // Controleer of bestand een afbeelding is
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception('Bestand is geen afbeelding');
        }
    
        // Controleer bestandsgrootte
        if ($file["size"] > 5000000) {
            throw new Exception('Sorry, het bestand is te groot');
        }
    
        // Allowed formats
        $allowedFormats = ["jpg", "png", "jpeg", "gif"];
        if (!in_array($imageFileType, $allowedFormats)) {
            throw new Exception('Sorry, alleen JPG, JPEG, PNG en GIF-bestanden zijn toegestaan');
        }
    
        // Controleer of bestand al bestaat
        if (file_exists($targetFile)) {
            throw new Exception('Sorry, bestand bestaat al');
        }
    
        // Try to upload the file
        if (!move_uploaded_file($file["tmp_name"], $targetFile)) {
            throw new Exception('Sorry, er is een fout opgetreden bij het uploaden van het bestand');
        }
    
        // Return the relative URL path of the image
        return 'images/uploads/' . basename($file["name"]);
    }
    
}

?>