<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Language::insert([
            [
                'code'              => 'ru',
                'name'              => 'russian',
                'localization_json' => '{
                  "header": {
                    "currency": "Валюта",
                    "login": "вход"
                  },
                  "messages": {
                    "success": {
                      "contactUsMessageSent": "ваше сообщение успешно отправлено"
                    },
                    "warning": {
                      "signOut": "Вы действительно хотите выйти?"
                    }
                  },
                  "buttons": {
                    "send": "Отправить",
                    "start_learning": "начать обучение",
                    "watch_promo_video": "Смотреть промо ролик",
                    "more_about_course": "подробнее о курсе",
                    "get_free_lesson": "получить бесплатный урок",
                    "all_news": "все новости",
                    "read_more": "Читать дальше",
                    "terms_of_use": "пользовательским соглашением",
                    "privacy_policy": "политикой конфиденциальности",
                    "close": "закрыть",
                    "cancel": "Отмена",
                    "complete": "Завершить",
                    "refuseFromBanner": "Отказаться от рекламы",
                    "pay": "Оплатить",
                    "saveChanges": "Сохранить изменения",
                    "addCard": "Добавить карту",
                    "yes": "да",
                    "no": "нет"
                  },
                  "home": {
                    "title": "Language to go",
                    "header_title": "Изучайте иностранные языки в удобное время",
                    "header_sub_title": "Новая автоматизированная онлайн-школа",
                    "header_info": "Достигайте своих целей с помощью авторских игровых методик от носителей языка, которые превращают обучение в развлечение",
                    "want_to_learn": "Хочу говорить на",
                    "available_courses": "Доступные курсы",
                    "about_title": "Про Language to go",
                    "about_sub_title": "Online школа иностранных языков",
                    "about_text": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod suscipit turpis, condimentum rhoncus mauris molestie eget. Pellentesque dapibus mi vitae nulla lacinia tempor. Nam varius faucibus odio, sit amet tristique tortor imperdiet sagittis. Nullam dolor erat, semper blandit orci non, dictum dapibus ex. Maecenas et lacus diam. Phasellus molestie facilisis consectetur. Fusce euismod diam quis lectus laoreet luctus. Fusce tellus leo, euismod ut leo et, tincidunt accumsan neque. Curabitur condimentum finibus pulvinar. Sed quis dui diam. Aliquam rhoncus odio ut mauris ullamcorper consectetur viverra nec lectus. Cras sit amet lectus justo.",
                    "about_more": "подробнее о нас",
                    "announcement_title": "Анонсы курсов",
                    "news_title": "Новости",
                    "news_sub_title": "Будьте в курсе наших событий. Следите за нашими новостями",
                    "seo_text": "Seo Text Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod suscipit turpis, condimentum rhoncus mauris molestie eget."
                  },
                  "benefits": {
                    "title": "Преимущества Language to go",
                    "sub_title": "Место для подзаголовка блока",
                    "convenient_training_format": "Удобный формат обучения",
                    "convenient_training_format_text": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod suscipit turpis, condimentum rhoncus mauris molestie eget. Pellentesque dapibus mi vitae nulla lacinia tempor.",
                    "communication_with_other_students": "Общение с другими учащимися",
                    "communication_with_other_students_text": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod suscipit turpis, condimentum rhoncus mauris molestie eget. Pellentesque dapibus mi vitae nulla lacinia tempor.",
                    "interesting_tasks": "Общение с другими учащимися",
                    "interesting_tasks_text": "Общение с другими учащимися",
                    "fast_results": "Быстрый результат",
                    "fast_results_text": "Быстрый результат"
                  },
                  "how_work": {
                    "title": "Как это работает?",
                    "sub_title": "Несколько простых шагов для изучения иностранного языка",
                    "registration": "Регистрация",
                    "registration_text": "Вы регистрируетесь на ресурсе Language to go",
                    "free_lessons": "Бесплатные уроки",
                    "free_lessons_text": "После регистрации Вы получаете достук к бесплатным урокам ко всем языковым курсам",
                    "training": "Обучение",
                    "training_text": "Вы получили доступ к обучению. Вас ждут интересные уроки и задания.",
                    "select_language": "Выбор языка",
                    "select_language_text": "Вы выбираете какой язык хотите изучать на нашем ресурсе",
                    "payment": "Оплата",
                    "payment_text": "Вы выбираете оптимальный для вас пакет услуг и оплачиваете курс обучения",
                    "take_first_step": "сделать первый шаг"
                  },
                  "about": {
                    "title": "О нас"
                  },
                  "courses": {
                    "title": "Курсы"
                  },
                  "interesting": {
                    "title": "Интересное"
                  },
                  "news": {
                    "title": "Новости"
                  },
                  "videos": {
                    "title": "Видео"
                  },
                  "reviews": {
                    "title": "Что говорят о L2G?",
                    "sub_title": "За что пользователи любят Languages to Go",
                    "all_reviews": "все отзывы"
                  },
                  "faq": {
                    "title": "FAQ"
                  },
                  "contacts": {
                    "title": "Контакты",
                    "have_questions_or_advice": "Есть вопросы или хотите получить консультацию?",
                    "leave_your_contacts": "Оставьте свои контакты и наш менеджер свяжется с вами в ближайшее время",
                    "form_placeholder_name": "Ваше имя",
                    "form_placeholder_phone": "Телефон",
                    "form_placeholder_email": "E-mail",
                    "form_placeholder_message": "Текст сообщения",
                    "form_I_agree": "Отправляя заявку, я соглашаюсь с",
                    "form_and": "и",
                    "form_site": "сайта",
                    "form_field_required": "Это поле обязательно к заполнению.",
                    "form_btn_send": "отправить заявку"
                  },
                  "notifications": {
                    "messages": {
                      "course_payed": "Вы оплатили курс",
                      "course_not_payed": "Возникла ошибка при оплате курса",
                      "birthday": "Поздравляем вас, коллега, со светлым праздником — днем вашего рождения! Желаем жить в мире и радости, не знать горя и переживаний, всегда ценить то, что имеете, и достигать того, чего хотите! Пусть сбываются самые заветные мечты, будет крепким здоровье! Пусть обязательно каждый миг вашей жизни согревает настоящая любовь! Успехов и удачи в делах, карьерного роста и уважения!"
                    }
                  }
                }',
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        ]);
    }
}
