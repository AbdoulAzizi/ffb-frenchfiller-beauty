<?php

class checkColis{

    public static function check($order, $weights = array(), $widths = array(), $heights = array(), $lengths = array()){
        if ($order && is_numeric($order) && $order != 0) {
            $order = new Order((int) $order);
        }

        for($i = 0; $i < count($widths); $i++){
            if( (empty($widths[$i]) && empty($heights[$i]) && empty($lengths[$i])) || ( !empty($widths[$i]) && !empty($heights[$i]) && !empty($lengths[$i]))){
                continue;
            }
            else{
                return json_encode(array('error' => 10, "message" =>  "Données manquantes pour une ou plusieurs dimensions du colis n° ".($i+1)." (Commande n° ".$order->id." )"));
            }
        }

        // CHECK WEIGHT
        if(!empty($weights)){
            if(Chronopost::isRelais($order->id_carrier)) {
                foreach ($weights as $i => $weight){
                    if(!is_numeric($weight) && !empty($weight)){
                        return json_encode(array('error' => 99, "message" => "Valeur(s) non valable(s). Vérifiez le poids et les dimensions du colis n° ".($i+1)." (Commande n° ".$order->id." )"));
                    }
                    if ($weight > 20) {
                        return json_encode(array('error' => 1, "message" => "Le poids du colis n° ".($i+1)." doit être inférieur ou égal à 20KG (Relais) (Commande n° ".$order->id." )"));
                    }
                }

            }
            else{
                foreach ($weights as $i => $weight){
                    if(!is_numeric($weight) && !empty($weight)){
                        return json_encode(array('error' => 99, "message" => "Valeur(s) non valable(s). Vérifiez le poids et les dimensions du colis n° ".($i+1)." (Commande n° ".$order->id." )"));
                    }
                    if($weight > 30){
                        return json_encode(array('error' => 2, "message" => "Le poids du colis n° ".($i+1)." doit être inférieur ou égal à 30KG (Commande n° ".$order->id." )"));
                    }
                }
            }
        }

        // CHECK SIZE
        $maxSize = 150;
        $maxTotalSize = 300;
        if(Chronopost::isRelais($order->id_carrier)) {
            $maxSize = 100;
            $maxTotalSize = 250;
        }
        if( !empty($lengths) && !empty($widths) && !empty($heights)){
            for($i = 0; $i < count($lengths); $i++) {
                if(!is_numeric($lengths[$i]) && !empty($lengths[$i]) || !is_numeric($widths[$i]) && !empty($widths[$i]) || !is_numeric($heights[$i]) && !empty($heights[$i])){
                    return json_encode(array('error' => 99, "message" => "Valeur(s) non valable(s). Vérifiez le poids et les dimensions du colis n° ".($i+1)." (Commande n° ".$order->id.")"));
                }
                if ($heights[$i] > $maxSize || $widths[$i] > $maxSize || $lengths[$i] > $maxSize) {
                    return json_encode(array('error' => 11, "message" => "Une des dimensions du colis n° ".($i+1)." est supérieure à {$maxSize}cm (Commande n° ".$order->id.")"));
                }
                if (($lengths[$i] + 2 * $heights[$i] + 2 * $widths[$i]) > $maxTotalSize) {
                    return json_encode(array('error' => 21, "message" => "La taille du colis (L + 2*H + 2*l) n° ".($i+1)." est supérieure à {$maxTotalSize}cm (Commande n° ".$order->id.")"));
                }
            }
        }

        return json_encode(array('error' => 0, "message" => "OK"));
    }
}








