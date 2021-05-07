/**
 * CarouselThumbs - Class
 *
 * Класс для отображения миниатюрных фото
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
* Это функция-конструктор CarouselThumbs().
*
* Данный конструктор инициализирует обьект
*/
function CarouselThumbs(params)
{
    if(!params){
        return;
    }
    
    //---- Инициализация карусели ----
    if($('#' + params.container).size()){
        $('#' + params.container).carousel({
            interval: 10000
        });
    }
}

    
// Статический метод класса, выполняемый при загрузки окна броузера
// создаются обьекты класса, экземпляры их
// заносяться в список экземпляров
// пр. {CarouselThumbs: [new CarouselThumbs(), ... ,new CarouselThumbs()]}
CarouselThumbs.RegRunOnLoad = function() {
    
    // Получим параметры для создания обьекта
    var params = BSA.ScriptParams['CarouselThumbs'];
    // Ф-ия создания обьектов по их параметрам
    var createObject = function(param){
        var carousel = BSA.ScriptInstances['CarouselThumbs'];
        if(carousel){
            carousel.push(new CarouselThumbs(param));
        }else{
            BSA.ScriptInstances['CarouselThumbs'] = [new CarouselThumbs(param)];
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
runOnLoad(CarouselThumbs.RegRunOnLoad);
