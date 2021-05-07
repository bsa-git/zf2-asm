/**
 * TouchTouch - Class
 *
 * Класс для отображения слайд шоу
 *
 * JavaScript
 *
 *
 * @author  Бескоровайный Сергей <bs261257@gmail.com>
 * @copyright  2013 Бескоровайный Сергей
 * @license    BSD
 * @version    1.00.00
 * @link       http://my-site.com/web
 * 
 */
   
       
/**
* Это функция-конструктор TouchTouch().
*
* Данный конструктор инициализирует обьект
*/
function TouchTouch(params)
{
    // Получим опции для обьекта
    if(params.container){
        // Initialize the gallery
        var els = $('.' + params.container + ' a');
        var size = els.size();
        if(size > 0){
            els.touchTouch();
        }
    }
}

    
// Статический метод класса, выполняемый при загрузки окна броузера
// создаются обьекты класса, экземпляры их
// заносяться в список экземпляров
// пр. {TouchTouch: [new TouchTouch(), ... ,new TouchTouch()]}
TouchTouch.RegRunOnLoad = function() {
    
    // Получим параметры для создания обьекта
    var params = BSA.ScriptParams['TouchTouch'];
    // Ф-ия создания обьектов по их параметрам
    var createObject = function(param){
        var touchTouch = BSA.ScriptInstances['TouchTouch'];
        if(touchTouch){
            touchTouch.push(new TouchTouch(param));
        }else{
            BSA.ScriptInstances['TouchTouch'] = [new TouchTouch(param)];
        }
    }
    // Создание обьектов
    if(params){
        $.each( params, function(i, param){ 
            createObject(param);
        });
    }else{
        createObject();
    }
}
runOnLoad(TouchTouch.RegRunOnLoad);
