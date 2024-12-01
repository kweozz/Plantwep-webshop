
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
        //file type met strtolower: strttolower is een functie die een string omzet naar kleine letters, PATH INFO EXTENSION is een functie die de extensie van een bestand retourneert hhtps://www.php.net/manual/en/function.pathinfo.php
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Controleer of bestand een afbeelding is, met functie getimagesize: https://www.php.net/manual/en/function.getimagesize.php
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception('Bestand is geen afbeelding');
        }

        // controleer bestandsgrootte
        if ($file["size"] > 5000000) {
            throw new Exception('Sorry, het bestand is te groot');
        }

        // allowed formats gemaakt 
        $allowedFormats = ["jpg", "png", "jpeg", "gif"];
        if (!in_array($imageFileType, $allowedFormats)) {
            throw new Exception('Sorry, alleen JPG, JPEG, PNG en GIF-bestanden zijn toegestaan');
        }

        // Controleer of bestand al bestaat
        if (file_exists($targetFile)) {
            throw new Exception('Sorry, bestand bestaat al');
        }

        // Probeer bestand te uploaden, move_uploaded_file is een functie die een geüpload bestand verplaatst naar een nieuwe locatie
        if (!move_uploaded_file($file["tmp_name"], $targetFile)) {
            throw new Exception('Sorry, er is een fout opgetreden bij het uploaden van het bestand');
        }

        return basename($file["name"]);
    }
}
?>