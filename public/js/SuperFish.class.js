/**
 * SuperFish - Class
 *
 * Класс создания меню
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
* Это функция-конструктор SuperFish().
*
* Данный конструктор инициализирует обьект
*/
function SuperFish(params)
{
    if(!params){
        return;
    }
    
    try{
        // Получим опции для графиков - StockChart
        if(params.container){
            // initialise Superfish 
            $('.' + params.container).superfish();
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
// пр. {SuperFish: [new SuperFish(), ... ,new SuperFish()]}
SuperFish.RegRunOnLoad = function() {
    
    // Получим параметры для создания обьекта
    var params = BSA.ScriptParams['SuperFish'];
    // Ф-ия создания обьектов по их параметрам
    var createObject = function(param){
        var superFish = BSA.ScriptInstances['SuperFish'];
        if(superFish){
            superFish.push(new SuperFish(param));
        }else{
            BSA.ScriptInstances['SuperFish'] = [new SuperFish(param)];
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
runOnLoad(SuperFish.RegRunOnLoad);
