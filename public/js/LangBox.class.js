/**
 * LangBox - Class
 *
 * Класс для локализации сообщений
 *
 * JavaScript
 *
 * Copyright (c) 2011 Бескоровайный Сергей
 *
 * @author     Бескоровайный Сергей <bs261257@gmail.com>
 * @copyright  2011 Бескоровайный Сергей
 * @license    BSD
 * @version    1.00.00
 * @link       http://my-site.com/web
 */

// Инициализация обьекта
function LangBox()
{
    var self = this;
    //------------
    //Сообщения
    //this.urlBase = $('base_url').innerHTML;//Базовый путь к ресурсам
    //this.msgErrorRetrieveDataFromUrl = $('msg-error-retrieve-data-from-url').innerHTML;

    var msgs = $('div.msg-box p');
    if(msgs.size()){
        msgs.each(function() {
            var id, msg;
            var p = $(this);
            id = p.attr('id');
            msg = p.text();
            self[id] = msg;
        });
    }
}

LangBox.prototype.getMsg = function(messageId, options) {
    var result = "";
    options = options || {};
    //-----------------------
    if (this[messageId]) {
        var msg = this[messageId];
        result = $.tmpl(msg, options);;
    }
    return result;
}

// Глобальные опции для Highcharts
LangBox.Localization = {
    en: {
        contextButtonTitle: 'Chart context menu',
        decimalPoint: '.',
        downloadJPEG: 'Download JPEG image',
        downloadPDF: 'Download PDF document',
        downloadPNG: 'Download PNG image',
        downloadSVG: 'Download SVG vector image',
        loading: 'Loading...',
        months: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        numericSymbols: ['k', 'M', 'G', 'T', 'P', 'E'],
        printChart: 'Print chart',
        rangeSelectorFrom: 'From',
        rangeSelectorTo: 'To',
        rangeSelectorZoom: 'Zoom',
        resetZoom: 'Reset zoom',
        resetZoomTitle: 'Reset zoom level 1:1',
        shortMonths: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        thousandsSep: ',',
        weekdays: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
        rangeSelector: ['All'],
        divbox:{
            btn_close: 'Close',
            btn_next: 'Next',
            btn_prev: 'Prev',
            click_full_image: 'Click on here to view full image',
            error_not_youtube: 'This is not a youtube link',
            error_not_vimeo: 'This is not a vimeo link',
            error_cannot_load: "We can't load this page\nError: "
        }
    },
    ru: {
        contextButtonTitle: 'Контекстное меню графика',
        decimalPoint: ',',
        downloadJPEG: 'Загрузить график в JPEG формате',
        downloadPDF: 'Загрузить график в PDF формате',
        downloadPNG: 'Загрузить график в PNG формате',
        downloadSVG: 'Загрузить график в SVG векторном формате',
        loading: 'Загружается...',
        months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
        numericSymbols: [' тыс.', 'миллион', 'биллион', 'триллион', 'квадрильон', 'нониллион'],
        printChart: 'PrintChart',//'Печать графика',
        rangeSelectorFrom: 'От',
        rangeSelectorTo: 'До',
        rangeSelectorZoom: 'Диапазон',
        resetZoom: 'Сбросить диапазон',
        resetZoomTitle: 'Сброс масштаба 1:1',
        shortMonths: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
        thousandsSep: ' ',
        weekdays: ['Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота', 'Воскресенье'],
        rangeSelector: ['Весь'],
        divbox:{
            btn_close: 'Закрыть',
            btn_next: 'Следующий',
            btn_prev: 'Предыдущий',
            click_full_image: 'Щелкните здесь для просмотра полного изображения',
            error_not_youtube: 'Это не ссылка на YouTube',
            error_not_vimeo: 'Это не ссылка Vimeo',
            error_cannot_load: "Мы не можем загрузить эту страницу\nОшибка: "
        }
    }
}


// Ф-ия, выполняемая при загрузки окна броузера
// создаются обьекты класса, экземпляры их
// заносяться в список экземпляров
// пр. {LangBox: [new LangBox(), ... ,new LangBox()]}
LangBox.RegRunOnLoad = function() {

    BSA.lb = new LangBox();
    var langBox = BSA.ScriptInstances['LangBox'];
    if(langBox){
        langBox.push(BSA.lb);
    }else{
        BSA.ScriptInstances['LangBox'] = [BSA.lb];
    }
}
runOnLoad(LangBox.RegRunOnLoad);