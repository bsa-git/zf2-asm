/**
 * CarouselBanners - Class
 *
 * Класс для отображения банеров
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
* Это функция-конструктор CarouselBanners().
*
* Данный конструктор инициализирует обьект
*/
function CarouselBanners(params)
{
    if(!params){
        return;
    }
    
    //---- Инициализация карусели ----
    if($('#' + params.container).size()){
        var elCarousel = $('#' + params.container);
        elCarousel.carousel('cycle');
            
        var elLeft = $('#' + params.container + ' a.left').eq(0);
        var elRight = $('#' + params.container + ' a.right').eq(0);
        // Предыдущий слайд
        elLeft.click(function(){
            elCarousel.carousel('prev');
            return false;
        });
        // Следующий слайд
        elRight.click(function(){
            elCarousel.carousel('next');
            return false;
        });
    }
}

    
// Статический метод класса, выполняемый при загрузки окна броузера
// создаются обьекты класса, экземпляры их
// заносяться в список экземпляров
// пр. {CarouselBanners: [new CarouselBanners(), ... ,new CarouselBanners()]}
CarouselBanners.RegRunOnLoad = function() {
    
    // Получим параметры для создания обьекта
    var params = BSA.ScriptParams['CarouselBanners'];
    // Ф-ия создания обьектов по их параметрам
    var createObject = function(param){
        var carousel = BSA.ScriptInstances['CarouselBanners'];
        if(carousel){
            carousel.push(new CarouselBanners(param));
        }else{
            BSA.ScriptInstances['CarouselBanners'] = [new CarouselBanners(param)];
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
runOnLoad(CarouselBanners.RegRunOnLoad);
