<?php
class PartageModel extends Model
{
    private function fetchAllByQuery($sql, $params = [])
    {
        $this->query($sql);
        foreach ($params as $key => $value) {
            $this->bind($key, $value);
        }
        $this->execute();
        return $this->getResults();
    }

    public function export()
    {
        $idProcessus = (int) $_GET['id'];
        $params = [":idProcessus" => $idProcessus];

        $processus = $this->fetchAllByQuery("SELECT * FROM Processus WHERE idProcessus = :idProcessus", $params);
        $etapes = $this->fetchAllByQuery("SELECT * FROM Etape WHERE idProcessus = :idProcessus", $params);
        $bacs = $this->fetchAllByQuery("SELECT * FROM Bac WHERE idProcessus = :idProcessus", $params);
        $images = $this->fetchAllByQuery("SELECT * FROM Image WHERE idImage IN (
                SELECT idImage FROM Processus WHERE idProcessus = :idProcessus
                UNION
                SELECT idImage FROM Etape WHERE idProcessus = :idProcessus
            )", $params);

        $imagesById = array_column($images, null, 'idImage');
        foreach ($imagesById as $id => $img) {
            $imagesById[$id] = [
                'nomFichier' => $img['nomFichier'],
                'typeMIME' => $img['typeMIME'],
                'tailleImage' => $img['tailleImage'],
                'contenu' => base64_encode($img['contenuBlob'])
            ];
        }

        $bacsByNumero = array_column($bacs, null, 'numeroBac');

        $processusExport = $processus[0];
        if ($processusExport['idImage'] && isset($imagesById[$processusExport['idImage']])) {
            $processusExport['image'] = $imagesById[$processusExport['idImage']];
        }

        $processusExport['bacs'] = array_values($bacsByNumero);

        $etapesExport = array_map(function ($etape) use ($bacsByNumero, $imagesById) {
            if (isset($bacsByNumero[$etape['idBac']])) {
                $etape['bac'] = $bacsByNumero[$etape['idBac']];
            }

            if ($etape['idImage'] && isset($imagesById[$etape['idImage']])) {
                $etape['image'] = $imagesById[$etape['idImage']];
            }

            return $etape;
        }, $etapes);

        $processusExport['etapes'] = $etapesExport;

        return json_encode($processusExport, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
?>
