/**
 * DivBox - Class
 *
 * Класс для отображения видео и фото
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
* Это функция-конструктор DivBox().
*
* Данный конструктор инициализирует обьект
*/
function DivBox(params)
{
    if(!params){
        return;
    }
    
    this.$params = params;
    this.iniDivBox();
}

DivBox.prototype.iniDivBox = function(){
    if(this.$params){
        var caption = (this.$params.caption)? this.$params.caption: false;
        var els = $('a.' + this.$params.container);
        if(els.size()){
            els.divbox(
            {
                caption: caption,
                path: BSA.lb.getMsg('urlBase') + "/js/media-divbox/players/",
                languages: LangBox.Localization[BSA.lb.getMsg('language')]['divbox']
            });
        }
    }
}

    
// Статический метод класса, выполняемый при загрузки окна броузера
// создаются обьекты класса, экземпляры их
// заносяться в список экземпляров
// пр. {DivBox: [new DivBox(), ... ,new DivBox()]}
DivBox.RegRunOnLoad = function() {
    
    // Получим параметры для создания обьекта
    var params = BSA.ScriptParams['DivBox'];
    // Ф-ия создания обьектов по их параметрам
    var createObject = function(param){
        var divBox = BSA.ScriptInstances['DivBox'];
        if(divBox){
            divBox.push(new DivBox(param));
        }else{
            BSA.ScriptInstances['DivBox'] = [new DivBox(param)];
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
runOnLoad(DivBox.RegRunOnLoad);
