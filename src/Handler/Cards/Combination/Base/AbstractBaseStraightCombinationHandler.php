<?php

namespace App\Handler\Cards\Combination\Base;

use App\Enum\Card as EnumCard;
use App\ValueObject\Card;

class AbstractBaseStraightCombinationHandler extends AbstractBaseCombinationHandler
{
    public function rollbackAceValues(Card ...$cards): void
    {
        // Rollback Ace values if straights not found
        foreach ($cards as $card) {
            if ($card->getName() === EnumCard::Ace->name && $card->getValue() === 1) {
                $card->setValue(EnumCard::Ace->value);
            }
        }
    }

    protected function getAllStraightCombinations(array $cards, int $length = 5)
    {
        // Шаг 1: Группировка по значениям и сбор типов и мастей
        $valueMap = $this->groupCardsByValue($cards);
        // Получение отсортированных уникальных значений
        $uniqueValues = array_keys($valueMap);
        $straights    = [];

        // Шаг 2: Поиск всех возможных последовательных последовательностей
        for ($i = 0; $i <= count($uniqueValues) - $length; $i++) {
            $currentSequence = array_slice($uniqueValues, $i, $length);
            // Проверка, что последовательность действительно подряд идущая
            $isConsecutive = true;
            for ($j = 0; $j < $length - 1; $j++) {
                if ($currentSequence[$j] - 1 !== $currentSequence[$j + 1]) {
                    $isConsecutive = false;
                    break;
                }
            }

            if (!$isConsecutive) {
                continue;
            }

            // Шаг 3: Генерация всех комбинаций типов и мастей для текущей последовательности
            $typeSuitList = [];
            foreach ($currentSequence as $value) {
                $typeSuitList[] = $valueMap[$value];
            }

            $typeSuitCombinations = $this->cartesianCard($typeSuitList);
            foreach ($typeSuitCombinations as $typeSuitCombinationCard) {
                // Формируем комбинацию как массив объектов с 'value', 'type' и 'suit'
                $combination = [];
                for ($k = 0; $k < $length; $k++) {
                    $combination[] = $typeSuitCombinationCard[$k];
                }
                $straights[] = $combination;
            }
        }

        // Шаг 4: Фильтрация уникальных комбинаций
        $uniqueStraights = [];
        $seenSignatures  = [];

        foreach ($straights as $straight) {
            // Создаем подпись комбинации на основе типов и мастей
            $signature = '';
            foreach ($straight as $item) {
                $signature .= $item->__toString();
            }
            if (!in_array($signature, $seenSignatures, true)) {
                $seenSignatures[]  = $signature;
                $uniqueStraights[] = $straight;
            }
        }

        return $uniqueStraights;
    }

    // Вспомогательная функция для вычисления декартова произведения массивов
    protected function cartesianCard($typeSuitList)
    {
        $result = [[]];
        foreach ($typeSuitList as $cards) {
            $tmp = [];
            foreach ($result as $resultItem) {
                foreach ($cards as $card) {
                    $tmp[] = array_merge($resultItem, [$card]);
                }
            }
            $result = $tmp;
        }

        return $result;
    }
}
