<?php
// class comprenant des fonctions d'affichange de données d'une vue améliorée
namespace App\DevTools;


class EchoDebug
{
    public static function echoArray(array $array, int $dim)
    {
        //on défini la tabulation suivant le nombre de dimension
        $tab = "' margin: 0; padding-left:".$dim * 16 . "px; font-family: monospace;
        '";

        foreach($array as $key => $value){
            echo "<div style=$tab>";
            //on afffiche le type de la variable et sa taille dans un span en italique
            if(is_array($value)){
                echo "<details><summary><span style='color: #000000dd; font-size: 1em;'><b>$key : </b></span><span style='color: green; font-size: 0.8em; margin: 0; padding: 0;'>" . gettype($value) . " | Taille : " . count($value) . "</span></summary>";
                EchoDebug::echoArray($value, $dim + 1);
                echo "</details>";
            } elseif (is_object($value)){
                echo "<details><summary><span style='color: #000000dd; font-size: 1em;'><b>$key : </b></span><span style='color: green; font-size: 0.8em; margin: 0; padding: 0;'>" . gettype($value) . "</span></summary>";
                echo "<pre>";
                echo "<span style='color: #000000dd; font-size: 1.1em;'><b>Object : </b></span>";
                echo "<span style='color: green; font-size: 0.8em; margin: 0; padding: 0;'>" . get_class($value) . "</span>";
                //on converti l'objet en tableau pour l'afficher
                EchoDebug::echoArray((array) $value, $dim + 1);
                echo "</pre>";
                echo "</details>";
            }
            else {
                echo "<span style='color: #000000dd; font-size: 1.1em;'><b>$key : </b></span>";
                echo "<span style='color: green; font-size: 0.8em; margin: 0; padding: 0;'>" . gettype($value) . "</span>";
                echo "<span style='color: #000000dd; font-size: 1em; margin: 0; padding: 0;'>  $value</span>";
            }
            echo "</div>";
        }
    }
    public static function xDebug($data)
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        $file = $trace['file'];
        $line = $trace['line'];
        //on structure le retour dans un div pour une meilleur lisibilité avec un 100vw
        echo "<div style='width:98vw; background-color: #00000015; padding: 12px; box-shadow: 0 0 10px #00000033; border-radius: 10px; border: 1px solid #dddddd33;'>";
        echo "<pre>";
        // on titre le retour avec le numéro de la ligne et du fichier lue actuellement
        echo "<div style='padding: 8px; width: 100%; margin: 0;* border-bottom: 1px solid #000000;'>";
        echo "<h5 style='color: #000000; font-size: 1.2em; margin: 4px; padding: 0;'>Debug : " . $file. " : " . $line . "</h5>";
        echo "</div>";
        // on affiche le contenu de la variable
        if(is_array($data)){
            EchoDebug::echoArray($data, 0);
        }  elseif (is_object($data)){
            echo "<pre>";
            echo "<span style='color: #000000dd; font-size: 1.1em;'><b>Object : </b></span>";
            echo "<span style='color: green; font-size: 0.8em; margin: 0; padding: 0;'>" . get_class($data) . "</span>";
            //on converti l'objet en tableau pour l'afficher
            EchoDebug::echoArray((array) $data, 0);
            echo "</pre>";
        }
        else {
            echo "<pre>";
            echo "<span style='color: #000000dd; font-size: 1.1em;'><b>Variable : </b></span>";
            echo "<span style='color: green; font-size: 0.8em; margin: 0; padding: 0;'>" . gettype($data) . "</span>";
            echo "<span style='color: #000000dd; font-size: 1em; margin: 0; padding: 0;'>  $data</span>";
            echo "</pre>";
        }
        echo "</pre>";
        echo "</div>";

    }
}