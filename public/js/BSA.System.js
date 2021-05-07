/**
 * BSA.Sys - обьект системных ф-ий
 *
 * С помощью обьекта вы можете:
 *  - выводить сообщения и ошибки
 *  - обеспечивает функциональность ProgressBar (индикацию степени выполнения задания)
 *  - назначает события для AJAX запросов и вывод результатов выполнения этих запросов
 *  - Работать с частями адреса URL
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

BSA.Sys = {
    
    // Глобальные установки
    settings: {
        classToggleDisplayDiv: "toggle-display-div",
        classPrintThis: "print-this", 
        urlPrintAjax: "/index.php/index/print", 
        classWaitLoadingData: "wait-loading-data",
        message_box: 'container-message',
        idLog:  'logo2',
        advanced_menu: 'advanced'
    },
    
    
    //============= Функция инициализации ====//
    
    // Ф-ия инициализации скрипта
    init: function ()
    {
        var self = BSA.Sys;
        
        // Инициализация карусели
        self.initCarousel();
        
        // Показать/Скрыть DIV контейнер
        self.initToggleDiv();
        
        // Инициализация печати
        self.initPrint();
        
        // Инициализация шаблона
        self.initTemplate();
        
        // Ф-ия инициализации расширенного меню
        self.initAdvancedMenu();
        
    },
    
    // Ф-ия инициализации расширенного меню
    initAdvancedMenu: function ()
    {
        var self = BSA.Sys;
        //----------------
        var advancedMenu = $("#" + self.settings.advanced_menu);
        if(advancedMenu.size()){
            var trigger = $("#" + self.settings.advanced_menu + " span.trigger").eq(0);
            var menu = $('header nav').eq(0);
            var log = $("#" + self.settings.idLog);
            
            // Покажем меню
            if(log.size()){
                log.show(); 
            };
            
            // Назначим события скрыть/показать меню
            trigger.toggle(
                function () {
                    advancedMenu.removeClass("closed");
                    if(log.size()){
                        log.addClass("view-panel");
                    }
                    if(menu.size()){
                        menu.addClass("view-panel");
                    }
                },
                function () {
                    advancedMenu.addClass("closed");
                    if(log.size()){
                        log.removeClass("view-panel");
                    }
                    if(menu.size()){
                        menu.removeClass("view-panel");
                    }
                }
                );
        }
    },
    
    initCarousel: function ()
    {
        //---- Работа с каруселью ----
        if($('#' + 'home-carousel').size()){
            var elCarousel = $('#' + 'home-carousel');
            elCarousel.carousel('cycle');
            
            var elLeft = $('#' + 'home-carousel' + ' a.left').eq(0);
            var elRight = $('#' + 'home-carousel' + ' a.right').eq(0);
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
        
        
        //---- Инициализация myCarousel -----
        if($('#myCarousel').size()){
            $('#myCarousel').carousel({
                interval: 10000
            });
        }
        
    },
    
    initToggleDiv: function ()
    {
        var self = BSA.Sys;
        //---- Показать/Скрыть DIV контейнер -----
        var toggleElements = $('.' + self.settings.classToggleDisplayDiv);
        toggleElements.each(function(){
            var $el = $(this);
            $el.click(BxUtils.toggleDisplayDiv);
        });
    },
    
    initPrint: function ()
    {
        var self = BSA.Sys;
        
        //---- Распечатать -----
        var printElements = $('.' + self.settings.classPrintThis);
        printElements.each(function(){
            var $el = $(this);
            $el.click(function(event){
                var print_css = '<style type="text/css">.'  + self.settings.classPrintThis + ' {display: none}</style>'
                var div, content;
                //-----------------
                var $this = $(this);
                var id = $this.attr("href");
                if(id.indexOf("://") == -1 ){// This id no URL
                    div = $('#' + id);
                    if(div.size()){
                        content = div.html();
                        self.printIt(content, print_css);
                    }else{
                        self.onFailure({
                            class_message: 'alert-info',
                            messages: [
                            '<strong><em>' + BSA.lb.getMsg('msgErrPrint') + '</strong></em>',
                            BSA.lb.getMsg('msgFailedGetResourceUrl', {
                                url:sURL
                            })
                            ]
                        });
                    }
                }else{// This id is URL
                    // Элемент ожидания сделаем видимым
                    $this.next("img." + self.settings.classWaitLoadingData).eq(0).show();
                    // Распечатаем страницу для URL=id
                    self.printPageAjax(id);
                }
                return false;
            });
        });
    },
    
    initTemplate: function ()
    {
        /** Ф-ия обработки простого шаблона
         *
         *   <div id="results-tmpl">
         *     <p>Correct: {correct}</p>
         *     <p>Wrong: {wrong}</p>
         *   </div>
         *   
         *   $("#container").html($.tmpl($("#results-tmpl"), {correct : "1"}));
         **/ 
        $.tmpl = function(template, data) {
            var result, strTemplate;
            //------------------
            if(typeof template === 'string'){
                strTemplate = template
            }else{
                strTemplate = template.html();
            }
            result = strTemplate.replace(/\{([\w\.]*)\}/g, function (str, key) {
                var keys = key.split("."); 
                var value = data[keys.shift()];
                
                $.each(keys, function () {
                    value = value[this];
                });
                return (value === null || value === undefined) ? "" : ($.isArray(value) ? value.join('') : value);
            });
            return result; 
        };
    },
    
    //====== Функции для работы HTML ====//
    
    /**
     * Ф-ия переключения видимости блока - DIV
     */
    toggleDisplayDiv: function(event)
    {
        var toggleTexts = [];
        //-------------------
        event.preventDefault();
        
        var $this = $(this);
        if($this.attr("toggleText")){
            toggleTexts = $this.attr("toggleText").split('#');
        }
        
        var idDiv = $this.attr("href");
        var div = $(idDiv);
        div.toggle(1000, function(){
            var index = Number(div.is( ":visible" ));
            if(toggleTexts.length > 0){
                $this.text(toggleTexts[index]);
            }
        });
    },
    
    //====== Функции печати ====//
    
    /**
     * Напечатать что-то -  printThis
     */
    printIt: function(printThis, print_css) {
        var win;
        //-------------------------
        
        // Откроем окно
        win = window.open();
            
        // Откроем документ
        win.document.open();
        // Установим фокус
        win.focus();
        
        // Если нет URL, то запишем
        // содержание документа
        if(printThis.indexOf("</html>") == -1 ){// This id no <html>
            win.document.write('<html><head>'+print_css+'</head><body>');
            win.document.write(printThis);
            win.document.write('<'+'/body'+'><'+'/html'+'>');
        }else{
            win.document.write(printThis);
        }
        
        win.document.close();
        // Напечатаем документ
        win.print();
        win.close();
    },
    
    closePrint: function () {
        document.body.removeChild(this.__container__);
    },

    setPrint: function () {
        var self = BSA.Sys;
        //------------------------------------
        try{
            this.contentWindow.__container__ = this;
            this.contentWindow.onbeforeunload = self.closePrint;
            this.contentWindow.onafterprint = self.closePrint;
            this.contentWindow.print();
        } catch (ex) {
            if (ex instanceof Error) { // Это экземпляр Error или подкласса?
                self.onFailure(ex.name + ": " + ex.message);// , 10000
            }

        } finally {

        }
    },
    
    // Этот способ печати можно использовать если ресурс печати
    // находиться в том же домене что и сам сайт!!!!
    printPage: function (sURL) {
        var self = BSA.Sys;
        //------------------------------------
        var oHiddFrame = document.createElement("iframe");
        oHiddFrame.onload = self.setPrint;
        oHiddFrame.style.visibility = "hidden";
        oHiddFrame.style.position = "fixed";
        oHiddFrame.style.right = "0";
        oHiddFrame.style.bottom = "0";
        oHiddFrame.src = sURL;
        document.body.appendChild(oHiddFrame);
    },
    
    // Этот способ печати можно использовать если ресурс печати
    // не находиться в том же домене что и сам сайт!!!!
    printPageAjax: function(sURL){
        
        // wait-loading-data
        //var classPrintThis = this.settings.classPrintThis;
        
        
        // Сделаем ajax запрос к серверу
        $.ajax({
            url: BSA.lb.getMsg('urlBase') + this.settings.urlPrintAjax,
            type: 'POST',
            data: {
                url: sURL
            },
            dataType: 'html',
            context: this,
            success: function(html) {
                var self = this;
                //-----------------
                
                // Элемент ожидания сделаем невидимым
                $("img." + this.settings.classWaitLoadingData).hide();
                
                if(html){
                    self.printIt(html);
                }else{
                    self.onFailure({
                        class_message: 'alert-info',
                        messages: [
                        '<strong><em>' + BSA.lb.getMsg('msgErrPrint') + '</strong></em>',
                        BSA.lb.getMsg('msgFailedGetResourceUrl', {
                            url:sURL
                        })
                        ]
                    });
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown){
                BSA.Sys.getAjaxError(XMLHttpRequest, textStatus, errorThrown);
            },
            cache: false
        });
    },
    
    //====== Функции вывода сообщений ====//
    
    // Отобразим сообщения
    messagebox_write: function(class_message, messages, delay_clear){
        var htmlBox, htmlMessages = "", titleMessages = "", strMessage = "";
        var params = {};
        //---------------------
    
        // Если отсутсвует контейнер, то выйти
        var message_box = $('#' + this.settings.message_box);
        if (! message_box.size()){
            return;
        }
        
        // Очистить предыдущее сообщение
        $(".alert").alert('close');

        //Подготовим сообщение
        $.each( messages, function(i, message){ 
            strMessage = message.replace(/&lt;/g, "<");
            strMessage = strMessage.replace(/&gt;/g, ">");
            htmlMessages = htmlMessages + strMessage + '<br />';
        });
        
        // Получим титл сообщений
        titleMessages = BSA.lb.getMsg(class_message);
        
        // Получим html сообщения
        params.class_message = class_message;
        params.title = titleMessages;
        params.message = htmlMessages;

        htmlBox = this.messagebox_html(params);

        // Добавим сообщение на страницу
        message_box.html(htmlBox);

        //Сделаем видимым сообщение
        message_box.show();
        
        // Если есть задержка на удаления сообщения
        // то удалим его через - delay_clear
        // при этом назначим ф-ии свой - 'this' 
        // вместо стандартного - "windows"
        if(delay_clear){
            setTimeout($.proxy(this.messagebox_delay_clear,this), delay_clear);// $.proxy(messagebox_delay_clear,this)
        }
    },
    
    // Очистим сообщения с задержкой
    messagebox_delay_clear: function(delay){
        var msgBox = $('#' + this.settings.message_box);
        if(!delay){
            delay = 1000;
        }
        if (msgBox.size()) {
            msgBox.hide(delay, function() {
                $(".alert").alert('close');
            });
        }
    },
    
    
    
    // HTML сообщения
    messagebox_html: function(params){
        var html = $.tmpl(''
            + '<div class="alert {class_message} fade in">'
            + '<button type="button" class="close" data-dismiss="alert">×</button>'
            + '<center><h4 class="alert-heading">{title}!</h4></center>'
            + '{message}'
            + '</div>',
            params);
            
        return html;
    },
    
    // HTML сообщение - ожидания загрузки
    waitLoading_html: function(params){
        var html = $.tmpl(''
            + '<center>'
            + '<strong>'
            + '<p class="text-info ' +  this.settings.classWaitLoadingData + '" style="display: none">'
            + '{msg}'
            + '<img src="' + BSA.lb.getMsg('urlBase') + '/img/system/loading.gif" />'
            + '</p>'
            + '</strong>'
            + '</center>',
            params);
            
        return html;
    },
    
    // HTML содержание окна информации
    tagPopover_html: function(params){
        var html = $.tmpl(''
            + '<dl>'
            + '<dt></dt>'
            + '<dd><pre><strong class="text-success">MAX</strong>  {max}</pre></dd>'
            + '<dd><pre><strong class="text-error">HH</strong>   {hh}</pre></dd>'
            + '<dd><pre><strong class="text-warning">H</strong>    {h}</pre></dd>'
            + '<dd><pre><strong class="text-warning">L</strong>    {l}</pre></dd>'
            + '<dd><pre><strong class="text-error">LL</strong>   {ll}</pre></dd>'
            + '<dd><pre><strong class="text-success">MIN</strong>  {min}</pre></dd>'
            //            + '<label class="checkbox"> <input class="deblock" type="checkbox"> Отключить сигнализацию</label>'
            + '</dl>',
            params);
            
        return html;
    },
    
    //====== Функции Ajax ====//
    
    // Проверим данные, полученные через Ajax
    checkAjaxData: function(data){
        if(typeof data === 'string' || data.class_message || data.unexpected_message){
            if(data.unexpected_message){
                this.onFailure(data.unexpected_message);
            }else{
                this.onFailure(data);
            }
            return false;
        }else{
            return true;
        }
    },
    
    // Проверим данные , полученные через Ajax
    getAjaxError: function(XMLHttpRequest, textStatus, errorThrown){
        var messages = [
        '<strong><em>' + errorThrown + '</em></strong>', 
        'Error status: ' + textStatus,
        XMLHttpRequest.responseText];
        BSA.Sys.messagebox_write('alert-error', messages);
    },
    
    
    
    //====== Функции обработки ошибок ====//
    
    // Обработка ошибок
    onFailure : function(message, delay_clear) {
        var msgs;
        if(message.class_message){
            msgs = message.messages;
            this.messagebox_write(message.class_message, msgs, delay_clear);
        }else{
            this.messagebox_write('alert-error',[message], delay_clear);
        }

    }
}

// Регистрация ф-ии
runOnLoad(BSA.Sys.init);