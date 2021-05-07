/**
 * SliderFlex - Class
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
* Это функция-конструктор SliderFlex().
*
* Данный конструктор инициализирует обьект
*/
function SliderFlex(params)
{
    if(!params){
        return;
    }
    try{
        // Получим опции для обьекта
        if(params.container){
            
            var container = $('.' + params.container);
            if(container.size()){
                container.flexslider(params.options);
            }
            
        }
    } catch (ex) {
        if (ex instanceof Error) { // Это экземпляр Error или подкласса?
            BSA.Sys.onFailure(ex.name + ": " + ex.message);
        }

    } finally {
    }
}

    
// Статический метод класса, выполняемый при загрузки окна броузера
// создаются обьекты класса, экземпляры их
// заносяться в список экземпляров
// пр. {SliderFlex: [new SliderFlex(), ... ,new SliderFlex()]}
SliderFlex.RegRunOnLoad = function() {
    
    // Получим параметры для создания обьекта
    var params = BSA.ScriptParams['SliderFlex'];
    // Ф-ия создания обьектов по их параметрам
    var createObject = function(param){
        var sliderFlex = BSA.ScriptInstances['SliderFlex'];
        if(sliderFlex){
            sliderFlex.push(new SliderFlex(param));
        }else{
            BSA.ScriptInstances['SliderFlex'] = [new SliderFlex(param)];
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
runOnLoad(SliderFlex.RegRunOnLoad);
