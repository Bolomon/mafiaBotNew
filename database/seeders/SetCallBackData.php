<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SetCallBackData extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        \App\Models\CallBackData::create(
            [
                'entity' => \App\Services\ButtonCallbacks\FaqCallback::class,
                'data' => json_encode([
                    'text' => trim('
📍Как записаться на игру?
В нашем боте есть кнопка «Расписание» там ты можешь выбрать для себя игру и день для записи 🗒️
📍Что, если я опаздываю?
Опоздание на 5-10 минут допустимо, мы подождем вас и посадим за стол, но если вы опаздываете на более долгий срок, предупредите ведущего и присоединитесь к следующей игре или после перерыва. Но лучше вообще не опаздывать ☺️
📍Я записан на игру, но не смогу приехать, что делать?
Вы можете отменить запись за два и более часа до игры. В таком случае оплата вернется к вам на счет в полном объеме, также если вы покупали абонемент. 
Если вы отменяете запись на игру менее чем за два часа, оплаченные деньги или абонемент не возвращаются.
📍Что за абонемент и как его использовать?
У нас есть абонемент на 5 игр,
он позволяет записаться и оплатить игру дистанционно, воспользовавшись скидкой. 
Без скидки 5 игр стоят от 75 до 100 лари.
При покупке абонемента абсолютно все игры для вас будут стоить 15 лари, то есть не более 75 лари. 
Также есть два больших абонемента на месяц и полгода. Чем больше срок, тем больше выгода. 
Пользоваться ими очень просто. При выборе оплаты, нажмите «оплатить абонементом»
📍Я ни разу не играл, я могу прийти?
Однозначно! Наши ведущие объяснят правила и ответят на все вопросы.
📍Можно ли прийти без записи?
Нет, запись обязательна, так как количество мест за столом ограничено. 
📍Я записан на игру, но решил взять с собой друга, ему нужно записаться?
Да, если ваш друг тоже будет играть, ему обязательно нужно записаться через нашего бота 
📍Можно ли снимать процесс игры?
Мы всегда рады, если вы выкладываете фото и видео с наших игр, но во время самой игры лучше не отвлекаться и погрузиться в процесс целиком
📍У меня возникли проблемы с ботом, куда обратиться? 
В описании бота есть ссылка на наш ТГ канал и Чат к нему. Администратор и участники чата помогут вам разобраться. Не стесняйтесь обратиться к нам🫶
                    ')
                ])
            ]
        );


        \App\Models\CallBackData::create(
            [
                'entity' => \App\Services\ButtonCallbacks\PaymentCallback::class,
                'data' => json_encode([
                    'text' => trim('
Привет! Мы уже работаем над возможностью онлайн оплаты, а пока принимаем наличные и оплату на карту на месте. 
Благодарим за терпение 🫶
Вот наши цены:
Мафия 15 лари
Бункер 15 лари
Кешфлоу 20 лари
Абонемент на 5 игр - 75 лари(действует месяц с момента покупки) 
Абонемент безлимит на месяц 150 лари (начинает действие в день покупки) 
Абонемент безлимит на полгода - 800 лари( полгода с момента покупки абонемента) 
Подарочный сертификат на суммы 50,100,150,200 лари( срок действия год с момента покупки )
                    ')
                ])
            ]
        );

//         \App\Models\CallBackData::create(
//             [
//                 'entity' => \App\Services\ButtonCallbacks\ScheduleCallback::class,
//                 'data' => json_encode([
//                     'text' => trim('
// Пятница 
// 19:00 🎭Мафия
// 22:00 🎭Мафия

// Суббота 
// 19:00 🎭Мафия 
// 22:00 🎭Мафия

// 🗓 Воскресенье 
// 17:00 Волейбол пляжный 
// 20:00 Бункер
//                     ')
//                 ])
//             ]
//         );
    }
}