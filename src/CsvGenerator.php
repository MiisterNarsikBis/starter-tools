<?php

/**
 * @author Benoit Farges <benoit@profil-web.fr>
 *
 * Class CsvGenerator
 */
class CsvGenerator
{
    /**
     * @param $lstObjet
     * @param $header
     * @param $filename
     */
    public function exportCSV($lstObjet,$header,$filename)
    {
        $csv = array();
        $csv[] = $header;
        $i = 1;

            foreach($lstObjet as $valeur) {
                $tab = array($valeur);
                $tab = implode('¤',$tab);
                $tab = str_replace("\r" , ' ' , $tab);
                $tab = str_replace("\n" , ' ' , $tab);
                $tab = str_replace("\t" , ' ' , $tab);
                $tab = str_replace("\f" , ' ' , $tab);
                $tab = str_replace("\v" , ' ' , $tab);
                $tab = str_replace("  " , ' ' , $tab);
                $tab = str_replace(" ," , ',' , $tab);
                $tab = str_replace("µ" , "\n" , $tab);
                $tab = str_replace("\n".'"' , '"' , $tab);
                $tab = str_replace(';' , ',' , $tab);
                $tab = str_replace('¤' , ';' , $tab);
                $valeur = $tab;
                $csv[$i][] = $valeur;
                $i++;
            }

        header('Content-Type: text/csv;');
        header('Content-Disposition: attachment; filename="'.$filename.'.csv";');
        header('Pragma: no-cache');

        foreach ($csv as $val){
            echo utf8_decode(implode(";", $val)."\n");
        }

        exit();
    }

}