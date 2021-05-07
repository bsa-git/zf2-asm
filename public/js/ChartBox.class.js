/**
 * ChartBox - Class
 *
 * Класс для работы с графиками
 * highcharts, highstock
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
* Это функция-конструктор ChartBox().
*
* Данный конструктор инициализирует графики
*/
function ChartBox(params)
{
    var self = this;
    
    // Перечень созданных трендов
    this.$chartObjects = {};
    // Получим язык сайта
    this.$language = BSA.lb.getMsg('language');
    
    // Установим глобальные опции графиков
    this.$lang = LangBox.Localization[this.$language];
    
//    var value = BSA.Utility.arrayMax([23,45,1,33]);
//    
//    BSA.Utility.setOptions({lang: this.$lang});
//    value = BSA.Utility.getOptions();  
//     
//    value = BSA.Utility.numberFormat(1232344.56567);
//    
//    value = new Date();
//    var timestamp = value.getTime();
//    timestamp = 1386757093;
//    timestamp = timestamp * 1000;
//    value = BSA.Utility.dateFormat(null,timestamp);
//    value = 44.56567;// 
//    var valueFormat = BSA.Utility.formatSingle('%.2f', value);
//    valueFormat = BSA.Utility.format('Значение ПИ — примерно {:.2f}.', value);
    
    
    Highcharts.setOptions({
        global: {
		useUTC: false
	},
        lang: this.$lang
    });
    
    // Получим опции для графиков - StockChart
    if(params.StockChart){
        $.each(params.StockChart, function() { 
            var container = this.chart.renderTo;
            
            // Если существует такой контейнер в DOM
            // то определим опции для графиков
            if($('#' + container).size()){
                self.$chartObjects[container] = {
                    type: 'StockChart',
                    chart_options: {},
                    tag_options: {},
                    chart: null
                };
                $.extend(self.$chartObjects[container].chart_options, self.getDefaultsOptions('StockChart'), this);
            }
        }); 

    }
    
    // Получим опции для графиков - Highcharts
    if(params.Highcharts){
        $.each(params.Highcharts, function() { 
            var container = this.chart.renderTo;
            
            // Если существует такой контейнер в DOM
            // то определим опции для графиков
            if($('#' + container).size()){
                self.$chartObjects[container] = {
                    type: 'Highcharts',
                    chart_options: {},
                    tag_options: {},
                    chart: null
                };
                $.extend(self.$chartObjects[container].chart_options, self.getDefaultsOptions('Highcharts'), this);
            }
        }); 

    }
}
/**
* Данная функция - метод store() объекта ChartBox.
*
* 
* @param int myarg
* 
*/
ChartBox.prototype.getDefaultsOptions = function(type){
    
    var options = {
        StockChart: {
            chart : {
                renderTo: 'container'
            },
                
            rangeSelector: {
                buttons: [
                {
                    count: 360,
                    type: 'minute',
                    text: '6H'
                }, 
                {
                    count: 720,
                    type: 'minute',
                    text: '12H'
                }, {
                    count: 1440,
                    type: 'minute',
                    text: '24H'
                },
                {
                    type: 'all',
                    text: this.$lang['rangeSelector'][0]
                }],
                inputEnabled: true,
                selected: 0
            },
            
            title : {
                text : 'Chart title'
            },
            yAxis: {
                title: {
                    text: 'kg/sm2'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                formatter: function() {
                    var seriesName = this.points[0].series.name;
                    var yAxisUnit = this.points[0].series.yAxis.userOptions.title.text;
                    return Highcharts.dateFormat('%d.%m.%y %H:%M:%S', this.x) +'<br/>'+
                    '<b>'+ seriesName +'</b>='+
                    Highcharts.numberFormat(this.y, 2) + ' ' + yAxisUnit;
                }
            },
                
            exporting: {
                enabled: true
            },
                
            series : [{
                name : 'Series Name',
                data : []
            }]
        },
        Highcharts: {
            chart: {
                renderTo: 'container',
                marginRight: 10
            },
            title: {
                text: 'Chart title'
            },
            xAxis: {
                type: 'datetime',
                tickPixelInterval: 150
            },
            yAxis: {
                title: {
                    text: 'кг/см2'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                formatter: function() {
                    return Highcharts.dateFormat('%d.%m.%y %H:%M:%S', this.x) +'<br/>'+
                    '<b>'+ this.series.name +'</b>='+
                    Highcharts.numberFormat(this.y, 2);
                }
            },
            legend: {
                enabled: false
            },
            exporting: {
                enabled: true
            },
            series: [{
                name: 'Series Name',
                data: []
            }]
        }
    }
    
    return options[type];

}
    
// Статический метод класса, выполняемый при загрузки окна броузера
// создаются обьекты класса, экземпляры их
// заносяться в список экземпляров
// пр. {ChartBox: [new LangBox(), ... ,new LangBox()]}
ChartBox.RegRunOnLoad = function() {
    
    // Получим параметры для создания обьекта
    var params = BSA.ScriptParams['ChartBox'];
    // Ф-ия создания обьектов по их параметрам
    var createObject = function(param){
        var chartBox = BSA.ScriptInstances['ChartBox'];
        if(chartBox){
            chartBox.push(new ChartBox(param));
        }else{
            BSA.ScriptInstances['ChartBox'] = [new ChartBox(param)];
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
runOnLoad(ChartBox.RegRunOnLoad);
