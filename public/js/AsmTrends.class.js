/**
 * AsmTrends - Class
 *
 * Класс для работы с трендами
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
 * Это функция-конструктор AsmTrends().
 *
 * Данный конструктор инициализирует тренды
 */
function AsmTrends(params)
{
    // Перечень позиций, для получения данных
    this.$tags = [];
    // Получим экземпляр класса - ChartBox
    this.$chartBox = BSA.ScriptInstances['ChartBox'];
    this.$chartBox = this.$chartBox[0];
    // Последнее время обновления данных
    this.$lastUpdateTime = 0;
    this.$lastUpdateTime2 = 0;
    // ID ф-ии setInterval
    this.$intervalID = 0;
    // Текущее кол. ошибок
    this.$numberErrors = 0;
    // Параметры инициализации
    if (params) {
        // Запомнить контейнер
        this.$container = params.container;
        // Период обновления данных
        this.$update_period = params.update_period;
        // допустимое кол. ошибок
        this.$allowedNumberErrors = params.allowed_number_errors;
        // допустимый процент выхода за диапазон от шкалы измерения
        this.$allowablePercentageOutOfRange = params.allowable_percentage_out_of_range;
        // Инициализация активных закладок аккордиона
        this.iniAccordionTabs();
        // Установим события открытия закладок аккордиона
        this.iniAccordionClickTab();
    }
}

//======== Ф-ИИ ИНИЦИАЛИЗАЦИИ ========//

/**
 * Данная функция - метод iniAccordionTabs() объекта AsmTrends.
 * обеспечивает инициализацию всех доступных закладок аккордиона
 * для первого запроса данных всех тегов в аккордионе
 */
AsmTrends.prototype.iniAccordionTabs = function () {
    var self = this;
    var idsAccordion;
    //-------------------------------------------
    // Найдем активную закладку с идентификатором
    idsAccordion = $('#' + this.$container + ' div.accordion-inner');
    idsAccordion.each(function () {
        var tag = $(this).attr('id');
        // Проверим есть ли такой тег в опциях для трендов
        // если есть, то добавим его в общий список трендов
        if (tag && self.$chartBox.$chartObjects[tag] && $.inArray(tag, self.$tags) == -1) {
            self.$tags.push(tag);
        }
    });
    this.getUpdateData(this.$tags);
}


/**
 * Данная функция - метод iniAccordionClickTab() объекта AsmTrends.
 * обеспечивает инициализацию обработчиков при нажатии закладок аккордиона
 */
AsmTrends.prototype.iniAccordionClickTab = function () {
    var self = this;
    var accordionContents, chart;
    //------------------------
    // Установим события открытия закладок аккордиона
    accordionContents = $('#' + this.$container + ' div.accordion-body');
    accordionContents.each(function () {
        var accordionContent = $(this).attr('id');
        var tag = $('#' + accordionContent + ' div.accordion-inner').eq(0).attr('id');
        $('#' + accordionContent).on('show', function () {
            // Получим обьект тренда
            chart = self.$chartBox.$chartObjects[tag].chart;
            if (!chart) {
                self.getSeriesData([tag]);
            }
        })
        $('#' + accordionContent).on('hide', function () {
            // Закроем окно инф.
            self.hideTagPopover(tag);
        })
    });
}

/**
 * Данная функция - метод iniPopovers() объекта AsmTrends.
 * обеспечивает инициализацию подсказок
 */
AsmTrends.prototype.iniTagPopovers = function (chartTag) {
    var self = this;
    var tagOptions, elValue;
    //-------------------------
    // Получим обьект тренда
    tagOptions = this.$chartBox.$chartObjects[chartTag].tag_options;

    // Найдем элемент для отображения текущего значения даных
    elValue = $('#' + chartTag).parent().parent().find('span.tag-value').eq(0);

    // Назначим опции для окна инф.
    var options = {
        placement: 'bottom',
        html: true,
        trigger: 'manual',
        title: '<center><strong>Параметры сигнализации</strong></center>',
        content: BSA.Sys.tagPopover_html({
            max: (tagOptions.scale_max === null) ? 'не установлен' : tagOptions.scale_max,
            min: (tagOptions.scale_min === null) ? 'не установлен' : tagOptions.scale_min,
            hh: (tagOptions.blocking_max === null) ? 'не установлен' : tagOptions.blocking_max,
            ll: (tagOptions.blocking_min === null) ? 'не установлен' : tagOptions.blocking_min,
            h: (tagOptions.signal_max === null) ? 'не установлен' : tagOptions.signal_max,
            l: (tagOptions.signal_min === null) ? 'не установлен' : tagOptions.signal_min
        })
    };

    elValue.popover(options);

    elValue.click(function () {
        var $this = $(this);
        var chart;
        //--------------------
        // Откроем закладку аккордиона
        self.showTagСollapse(chartTag);
        chart = self.$chartBox.$chartObjects[chartTag].chart;
        if (chart) {
            $this.popover('toggle');
            if (!$this.next(".popover").size()) {
                $this.next(".popover").eq(0).css({
                    "top": "15px",
                    "left": "50%"
                })
            }
        }
        return false;
    });
}


//======== Ф-ИИ ПОЛУЧЕНИЯ ДАННЫХ ========//

/**
 * Данная функция - метод getSeriesData() объекта AsmTrends.
 * обеспечивает запрос к историческим данным доступных трендов
 * 
 * @param chartTags array
 */
AsmTrends.prototype.getSeriesData = function (chartTags) {
    var tagOptions;
    //-----------------------
    // Покажем сообщение ожидания
    $.each(chartTags, function (i, chartTag) {
        var html = BSA.Sys.waitLoading_html({
            msg: BSA.lb.getMsg('msgWaitLoadingData')
        });
        $('#' + chartTag).html(html);
    })
    $('p.wait-loading-data').show();

    // Сделаем ajax запрос к серверу
    $.ajax({
        url: BSA.lb.getMsg('urlBase') + '/index.php/asm/charts', //trends,
        type: 'POST',
        data: {
            series: true,
            tags: chartTags.join(),
            nnDB: chartTags[0][1]
        },
        context: this,
        success: function (jsonData) {
            var self = this;
            var chart;
            //-----------------
            if (BSA.Sys.checkAjaxData(jsonData)) {
                //                $nnDB = jsonData['nnDB'];
                //                alert("nnDB: " + $nnDB);
                // Создадим тренды
                $.each(chartTags, function (i, chartTag) {
                    //--- Обновим опции для тренда ---
                    var chartType = self.$chartBox.$chartObjects[chartTag].type;
                    var chartOptions = self.$chartBox.$chartObjects[chartTag].chart_options;

                    // Получим опции тега
                    tagOptions = self.$chartBox.$chartObjects[chartTag].tag_options;

                    if (chartType == 'StockChart') {
                        // Установим титл тренда
                        chartOptions.title.text = tagOptions['value_title'];
                        // Установим един.изм. значения тренда
                        chartOptions.yAxis.title.text = tagOptions['value_unit'];
                        // Установим название серии
                        chartOptions.series[0].name = tagOptions['value_alias'];
                        // Установим данные серии
                        chartOptions.series[0].data = jsonData.data[chartTag];
                        // Создадим обьект графика 
                        // и поместим его в список графиков обьекта - СhartBox
                        chart = new Highcharts.StockChart(chartOptions);
                        self.$chartBox.$chartObjects[chartTag].chart = chart;
                    } else if (chartType == 'Highcharts') {
                        // Установим титл тренда
                        chartOptions.title.text = tagOptions['value_title'];
                        // Установим един.изм. значения тренда
                        chartOptions.yAxis.title.text = tagOptions['value_unit'];
                        // Установим название серии
                        chartOptions.series[0].name = tagOptions['value_alias'];
                        // Установим данные серии
                        chartOptions.series[0].data = jsonData.data[chartTag];
                        // Создадим обьект графика 
                        // и поместим его в список графиков обьекта - СhartBox
                        chart = new Highcharts.Chart(chartOptions);
                        self.$chartBox.$chartObjects[chartTag].chart = chart;
                    }
                });
            }
            // Удалим сообщения ожидания
            $('p.wait-loading-data').empty();
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            BSA.Sys.getAjaxError(XMLHttpRequest, textStatus, errorThrown);
        },
        cache: false
    });
}

/**
 * Данная функция - метод getUpdateData() объекта AsmTrends.
 * обеспечивает обновление данных доступных трендов
 * 
 */
AsmTrends.prototype.getUpdateData = function (tags) {
    // Сделаем ajax запрос к серверу
    $.ajax({
        url: BSA.lb.getMsg('urlBase') + '/index.php/asm/charts', // trends
        type: 'POST',
        data: {
            update: true,
            time: this.$lastUpdateTime,
            time2: this.$lastUpdateTime2,
            tags: this.$tags.join()  
        },
        context: this,
        success: function (jsonData) {
            var self = this;
            var chart, series, tagData, value;
            var x, y;
            //-----------------
            if (BSA.Sys.checkAjaxData(jsonData)) {
                // Проверим наличие обновления данных
                if (!tags) {
                    // Данные с сервера не получены, ошибок = ошибок + 1
                    if (BSA.Utility.isArray(jsonData.data)) {
                        self.$numberErrors++;
                    }
                    // Если jsonData.data = Array, то данные с сервера не получены
                    // иначе jsonData.data = Object
                    if (BSA.Utility.isArray(jsonData.data) && self.$numberErrors > self.$allowedNumberErrors) {
                        // Выведем сообщение об ошибке
                        BSA.Sys.onFailure({
                            class_message: 'alert-info',
                            messages: [
                            "<strong><em>Ошибка обновления данных с сервера!</em></strong>",
                            "Текущие данные с сервера не были получены.",
                            "Последнее время обновления: " + BSA.Utility.dateFormat(null, self.$lastUpdateTime * 1000)
                            ]
                        }, 10000);
                    }
                    // Данные с сервера получены, обнулим кол. ошибок
                    if (!BSA.Utility.isArray(jsonData.data)) {
                        self.$numberErrors = 0;
                    }
                }
                // Обновим данные для тренда
                $.each(self.$tags, function (i, chartTag) {
                    // Первый цикл получения данных
                    if (tags && jsonData.config && jsonData.config[chartTag]) {
                        var jsonDataConfig = jsonData.config[chartTag];
                        // Установим опции тега
                        var tagOptions = {};
                        tagOptions.value_title = jsonDataConfig['comment'] ? jsonDataConfig['comment'] : '';
                        tagOptions.value_unit = jsonDataConfig['value_unit'];
                        tagOptions.value_alias = jsonDataConfig['name_alias'];
                        tagOptions.scale_min = jsonDataConfig['scale_min'] * 1;
                        tagOptions.scale_max = jsonDataConfig['scale_max'] * 1;
                        tagOptions.signal_min = (jsonDataConfig['signal_min'] === null) ? null : jsonDataConfig['signal_min'] * 1;
                        tagOptions.signal_max = (jsonDataConfig['signal_max'] === null) ? null : jsonDataConfig['signal_max'] * 1;
                        tagOptions.blocking_min = (jsonDataConfig['blocking_min'] === null) ? null : jsonDataConfig['blocking_min'] * 1;
                        tagOptions.blocking_max = (jsonDataConfig['blocking_max'] === null) ? null : jsonDataConfig['blocking_max'] * 1;
                        self.$chartBox.$chartObjects[chartTag].tag_options = tagOptions;
                        // Инициализация окна инф.
                        self.iniTagPopovers(chartTag);
                    }
                    // Получим обновленные данные с сервера
                    tagData = (jsonData.data) ? jsonData.data[chartTag] : null;
                    if (tagData) {
                        // Получим обьект тренда
                        chart = self.$chartBox.$chartObjects[chartTag].chart;
                        if (chart) {
                            // Получим серию данных
                            series = chart.series[0];
                            // Добавим данные в тренд
                            $.each(tagData, function (i, data) {
                                x = data[0];
                                y = data[1];
                                value = y * 1;
                                series.addPoint([x, y], true, true);
                            })
                        } else {
                            value = tagData[0][1] * 1;
                        }
                        // Отобразим значение данных
                        self.showValue(chartTag, value);
                    }
                });
                // Запустим цикл обновления данных
                if (jsonData.time) {
                    self.$lastUpdateTime = jsonData.time;
                }
                if (jsonData.time2) {
                    self.$lastUpdateTime2 = jsonData.time2;
                }
                if (!self.$intervalID) {
                    self.$intervalID = setInterval($.proxy(self.getUpdateData, self), self.$update_period);
                }
            }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
            BSA.Sys.getAjaxError(XMLHttpRequest, textStatus, errorThrown);
        },
        cache: false
    });
}

//======== ВСПОМОГАТЕЛЬНЫЕ Ф-ИИ ========//

/**
 * Данная функция - метод hideTagPopover() объекта AsmTrends.
 * обеспечивает закрытие окна инф.
 */
AsmTrends.prototype.hideTagPopover = function (chartTag) {
    // Найдем элемент для отображения последнего значения даных
    var elValue = $('#' + chartTag).parent().parent().find('span.tag-value').eq(0);
    if (elValue.next(".popover").size()) {
        elValue.popover('hide');
    }
}

/**
 * Данная функция - метод showTagСollapse() объекта AsmTrends.
 * обеспечивает открытия аккордиона
 */
AsmTrends.prototype.showTagСollapse = function (chartTag) {
    // Найдем элемент для отображения последнего значения даных
    var idСollapse = $('#' + chartTag).parent(".accordion-body").eq(0).attr('id');
    if ($('#' + idСollapse).size()) {
        $('#' + idСollapse).collapse('show');
    }
}

/**
 * Данная функция - метод showValue() объекта AsmTrends.
 * обеспечивает отображения значения графика
 * 
 */
AsmTrends.prototype.showValue = function (chartTag, value) {
    var tagOptions, valueUnit, elValue, formatValue;
    //------------------------
    // Отформатируем значение
    formatValue = Highcharts.numberFormat(value, 2);
    tagOptions = this.$chartBox.$chartObjects[chartTag].tag_options;
    // Найдем единицы измерения значения
    valueUnit = tagOptions['value_unit'];
    // Найдем элемент для отображения последнего значения даных
    elValue = $('#' + chartTag).parent().parent().find('span.tag-value').eq(0);
    // Получим результат проверки превышения диапазона
    var result = this.checkValueRange(chartTag, value);
    elValue.removeClass();
    elValue.addClass("tag-value " + result.formatClass);
    if (result.class_message) {
        BSA.Sys.messagebox_write(result.class_message, result.messages);
    }
    // Отобразим значение данных
    elValue.html(formatValue + ' ' + valueUnit);
}

/**
 * Данная функция - метод checkValueRange() объекта AsmTrends.
 * обеспечивает проверку диапазона полученного значения
 * 
 */
AsmTrends.prototype.checkValueRange = function (chartTag, value) {
    var tagOptions_, formatValue, result = {};
    var tagOptions = {};
    var allowablePercent = this.$allowablePercentageOutOfRange;
    var scale_min, scale_max, scale_delta;
    //---------------------------------------
    // Отформатируем значение
    formatValue = Highcharts.numberFormat(value, 4);
    // Получим опции для тега
    tagOptions_ = this.$chartBox.$chartObjects[chartTag].tag_options;
    // Получим допустимый диапазон шкалы
    scale_min = tagOptions_['scale_min'];
    scale_max = tagOptions_['scale_max'];
    scale_delta = scale_max - scale_min;
    scale_min = scale_min - (scale_delta * allowablePercent / 100);
    scale_max = scale_max + (scale_delta * allowablePercent / 100);
    tagOptions.scale_min = scale_min;
    tagOptions.scale_max = scale_max;
    tagOptions.signal_min = (tagOptions_['signal_min'] === null) ? scale_min : tagOptions_['signal_min'];
    tagOptions.signal_max = (tagOptions_['signal_max'] === null) ? scale_max : tagOptions_['signal_max'];
    tagOptions.blocking_min = (tagOptions_['blocking_min'] === null) ? scale_min : tagOptions_['blocking_min'];
    tagOptions.blocking_max = (tagOptions_['blocking_max'] === null) ? scale_max : tagOptions_['blocking_max'];
    // Проверка на превышение диапазона
    if (value >= tagOptions['scale_min'] && value <= tagOptions['scale_max']) {
        if (value >= tagOptions['blocking_min'] && value <= tagOptions['blocking_max']) {
            if (value >= tagOptions['signal_min'] && value <= tagOptions['signal_max']) {
                result.formatClass = 'text-success';
            } else {
                result.formatClass = 'text-warning';
                result.messages = ['<strong>' + tagOptions_['value_title'] + ' - превышение диапазона сигнализации!</strong>',
                'Заданный диапазон сигнализации: (' + tagOptions['signal_min'] + '...' + tagOptions['signal_max'] + ') ' + tagOptions_['value_unit'],
                'Полученное значение: ' + formatValue + ' ' + tagOptions_['value_unit']];
                result.class_message = 'alert-block';
            }
        } else {
            result.formatClass = 'text-error';
            result.messages = ['<strong>' + tagOptions_['value_title'] + ' - превышение аварийного диапазона сигнализации!</strong>',
            'Заданный аварийный диапазон сигнализации: (' + tagOptions['blocking_min'] + '...' + tagOptions['blocking_max'] + ') ' + tagOptions_['value_unit'],
            'Полученное значение: ' + formatValue + ' ' + tagOptions_['value_unit']];
            result.class_message = 'alert-error';
        }
    } else {
        result.formatClass = 'text-error';
        result.messages = ['<strong>' + tagOptions_['value_title'] + ' - выход за диапазон!</strong>',
        'Заданный диапазон (шкала) сигнала: (' + tagOptions['scale_min'] + '...' + tagOptions['scale_max'] + ') ' + tagOptions_['value_unit'],
        'Полученное значение: ' + formatValue + ' ' + tagOptions_['value_unit']];
        result.class_message = 'alert-error';
    }
    return result;
}

// Статический метод класса, выполняемый при загрузки окна броузера
// создаются обьекты класса, экземпляры их
// заносяться в список экземпляров
// пр. {AsmTrends: [new AsmTrends(), ... ,new AsmTrends()]}
AsmTrends.RegRunOnLoad = function () {
    // Получим параметры для создания обьекта
    var params = BSA.ScriptParams['AsmTrends'];
    // Ф-ия создания обьектов по их параметрам
    var createObject = function (param) {
        var asmTrends = BSA.ScriptInstances['AsmTrends'];
        if (asmTrends) {
            asmTrends.push(new AsmTrends(param));
        } else {
            BSA.ScriptInstances['AsmTrends'] = [new AsmTrends(param)];
        }
    }
    // Создание обьектов
    if (params) {
        $.each(params, function (i, param) {
            createObject(param);
        });
    } else {
        createObject();
    }
}
runOnLoad(AsmTrends.RegRunOnLoad);
