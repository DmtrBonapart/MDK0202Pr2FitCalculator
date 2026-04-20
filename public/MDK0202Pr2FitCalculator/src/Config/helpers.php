<?php
//вспомогательные функции проекта

//считаю возраст в годах из даты рождения
function calcAge(string $birthDate): int {
    if (empty($birthDate)) return 25;
    $birth = new DateTime($birthDate);
    $now   = new DateTime();
    return (int)$now->diff($birth)->y;
}

//возраст для расчётов: если есть birth_date — считаю по нему, иначе беру age из БД
function getAgeFromUserdata(array $ud): int {
    $bd = (string)($ud['birth_date'] ?? '');
    if ($bd !== '') return calcAge($bd);
    $age = (int)($ud['age'] ?? 0);
    if ($age > 0 && $age < 120) return $age;
    return 25;
}

//считаю суточную норму калорий по формуле Миффлина-Сан Жеора
//и корректирую в зависимости от цели
function calcDailyCalories(array $ud): array {
    $weight   = (float)($ud['current_weight'] ?? 70);
    $height   = (float)($ud['height']         ?? 170);
    $age      = getAgeFromUserdata($ud);
    $gender   = $ud['gender']                  ?? 'male';
    $activity = (int)($ud['activity_level']    ?? 1);
    $target   = (float)($ud['target_weight']   ?? $weight);

    //базовый обмен веществ
    if ($gender === 'male') {
        $bmr = 10*$weight + 6.25*$height - 5*$age + 5;
    } else {
        $bmr = 10*$weight + 6.25*$height - 5*$age - 161;
    }

    //коэффициенты активности
    $actMap = [1=>1.2, 2=>1.375, 3=>1.55, 4=>1.725, 5=>1.9];
    $tdee   = round($bmr * ($actMap[$activity] ?? 1.2));

    //корректировка цели
    if ($target < $weight - 1)      { $calories = $tdee - 300; } //похудение
    elseif ($target > $weight + 1)  { $calories = $tdee + 300; } //набор
    else                             { $calories = $tdee;        } //поддержание

    //белки: 1.8г на кг, жиры: 25% от калорий, углеводы: остаток
    $protein = round($weight * 1.8);
    $fat     = round($calories * 0.25 / 9);
    $carbs   = round(($calories - $protein*4 - $fat*9) / 4);

    return [
        'calories' => max(1200, $calories),
        'protein'  => $protein,
        'fat'      => $fat,
        'carbs'    => max(0, $carbs),
        'tdee'     => $tdee,
    ];
}

//получаю аватар пользователя как base64 или путь к заглушке
function getUserAvatar(array $user): string {
    return '../Images/nonAvatar.jpg';
}

//форматирую дату дд/мм/гггг
function formatDateRu(string $date): string {
    if (empty($date)) return date('d/m/Y');
    [$y, $m, $d] = explode('-', $date);
    return "$d/$m/$y";
}

//экранирую для безопасного вывода в HTML
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
