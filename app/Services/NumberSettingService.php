<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NumberSettingService
{
    const RESET_MONTHLY = 'monthly';
    const RESET_YEARLY = 'yearly';

    const PART_TEXT = 'text';
    const PART_YEAR = 'year';
    const PART_MONTH = 'month';
    const PART_DAY = 'day';
    const PART_COUNTER = 'counter';

    public static function generate($entity) {
        $numberSettings = config('number-setting.settings');
        $numberSetting = collect($numberSettings)->where('entity',$entity)->first();
        if(empty($numberSetting)){
            return self::getNewId($entity);
        }

        $parts = collect($numberSetting['parts'])->sortBy('sequence')->values()->all();
        $counterDigit = 0;
        $digitBeforeCounter = 0;
        $generatedNoArray = [];
        $queryNo = '';
        if(empty($date)){
            $date = date('Y-m-d');
        }
        $date = date('Y-m-d',strtotime($date));
        foreach($parts as $part){
            if(!in_array(null,$generatedNoArray) && $part['type'] != 'counter'){
                $digitBeforeCounter += strlen($part['format']);
            }
            switch ($part['type']){
                case self::PART_TEXT:
                    $generatedNoArray[] = $part['format'];
                    $queryNo .= str_replace("_","\\_",$part['format']);
                    break;
                case self::PART_YEAR:
                    $dateText = date($part['format'], strtotime($date));
                    $generatedNoArray[] = $dateText;

                    if(empty($numberSetting['reset_type'])){
                        $dateText = str_repeat('_',strlen($dateText));
                    }
                    $queryNo .= $dateText;
                    break;
                case self::PART_MONTH:
                    $dateText = date($part['format'], strtotime($date));
                    $generatedNoArray[] = $dateText;

                    if(empty($numberSetting['reset_type']) || $numberSetting['reset_type'] == self::RESET_YEARLY){
                        $dateText = str_repeat('_',strlen($dateText));
                    }
                    $queryNo .= $dateText;
                    break;
                case self::PART_DAY:
                    $dateText = date($part['format'], strtotime($date));
                    $generatedNoArray[] = $dateText;

                    if(empty($numberSetting['reset_type']) ||
                        $numberSetting['reset_type'] == self::RESET_YEARLY ||
                        $numberSetting['reset_type'] == self::RESET_MONTHLY){
                        $dateText = str_repeat('_',strlen($dateText));
                    }
                    $queryNo .= $dateText;
                    break;
                case self::PART_COUNTER:
                    $generatedNoArray[] = null;
                    $queryNo .= str_repeat('_',$part['format']);
                    $counterDigit = $part['format'];
                    break;
            }
        }

        $columnName = config('number-setting.column_name');
        $subjectNos = ($entity)::where($columnName,'like',$queryNo)
            ->withTrashed()->orderBy($columnName)->pluck($columnName)->all();

        $existingNos = array_map(function($subjectNo) use ($generatedNoArray,$counterDigit,$digitBeforeCounter){
            $counterIndex = array_search(null,$generatedNoArray);
            if($counterIndex == 0){
                return intval(substr($subjectNo,0,$counterDigit));
            } else if($counterIndex+1 == count($generatedNoArray)){
                return intval(substr($subjectNo,$counterDigit*-1));
            } else {
                return intval(substr($subjectNo,$digitBeforeCounter,$counterDigit));
            }
        },$subjectNos);
        sort($existingNos);
        if(empty($existingNos)){
            $newCounter = 1;
        } else {
            $idealNos = range($existingNos[0], $existingNos[count($existingNos)-1]);
            $suggestedNos = array_values(array_diff($idealNos, $existingNos));
            $newCounter = empty($suggestedNos) ? ($existingNos[(count($existingNos)-1)] + 1) : $suggestedNos[0];
        }
        $newCounter = str_pad($newCounter, $counterDigit, "0", STR_PAD_LEFT);
        $generatedNoArray[array_search(null, $generatedNoArray)] = $newCounter;
        return implode('',$generatedNoArray);
    }

    public static function getNewId($entity) {
        $tableName = Str::snake(Str::plural($entity));
        return (DB::select("show table status like '".$tableName."'"))[0]->Auto_increment;
    }
}
