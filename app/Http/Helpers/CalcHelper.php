<?php

class CalcHelper {

    public static function calculate_credit($periods = 12, $amount, $percent, $type, $initial_fee = 0, $fees = [])
    {
        if($initial_fee != 0){
            $amount = $amount - $amount * $initial_fee/100;
        }
        $percentPerMonth = intval($percent) / 12 / 100;
        if($percentPerMonth == 0) $percentPerMonth = 1;
        $dates = [];
        for ($i = 0; $i < $periods; $i++) {

            $date = new \DateTime();
            $interval = new \DateInterval('P' . $i . 'M');
            $date->add($interval);
            $dates[] = $date->format('d.m.Y');
        }

        if ($percent == 0 || $percent == null){
            $ppm = number_format($amount/$periods, 2, '.', ' ');
            return [
                'ppm' => [$ppm],
                'procentAmount' => 0,
                'month' => $periods,
                'dates' => $dates,
            ];
        }

        //для аннуитетного
        if ($type == 1) {

            $ppm = $amount * $percentPerMonth / (1 - 1 / (1 + $percentPerMonth) ** $periods);
            $ppm_arr = [];
            for ($i = 1; $i <= $periods; $i++) {
                $number = round($ppm, 2);
                $ppm_arr[] = number_format($number, 2, '.', ' ');
            }
            $procentAmount = $ppm * $periods - $amount;
            $procentAmount = round($procentAmount, 2);
            $procentAmount = number_format($procentAmount, 2, '.', ' ');
            return [
                'ppm' => $ppm_arr,
                'procentAmount' => $procentAmount,
                'month' => $periods,
                'dates' => $dates,
            ];
        }

        //для дифференцированного
        else {

            $procentAmount = round($amount * $percentPerMonth * ($periods + 1) / 2, 2);
            $ppm = [];
            for ($i = 1; $i <= $periods; $i++) {
                $ppm[] = round($amount / $periods + $amount * ($periods - $i + 1) * $percentPerMonth / $periods, 2);
            }
            return [
                'ppm' => $ppm,
                'procentAmount' => $procentAmount,
                'month' => $periods,
                'dates' => $dates,
            ];
        }
    }
}