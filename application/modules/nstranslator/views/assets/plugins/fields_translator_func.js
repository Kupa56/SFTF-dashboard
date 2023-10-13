
    $('#enable_html').remove();

    $.fn.localized = function() {

        let lang = this.attr('data-current-lang');

        let result_json = {};
        try {
            result_json = JSON.parse(this.val());
        }catch (e) {

        }

        if(!result_json.hasOwnProperty(lang)){
            result_json[lang] = this.val();
        }

        if(result_json.hasOwnProperty(lang)){
            let localizedContent = result_json[lang];
            this.val(localizedContent);
        }

        return this;
    };

    $.fn.saveLocalizedContent = function() {

        let $origSelc = $('[name='+this.attr('data-field')+']');

        let lang = this.attr('data-current-lang');

        let result_json = {};

        if(IsJsonString($origSelc.val())){
            result_json = JSON.parse($origSelc.val());
            result_json[lang] = this.val();
        }else{
            result_json[lang] = this.val()
        }

        //empty the field
        this.val("");

        $origSelc.val(JSON.stringify(result_json));

        return this;
    };

    function IsJsonString(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

    $.fn.copyContent = function() {

        let $origSelc = $('[name='+this.attr('data-field')+']');
        let lang = this.attr('data-current-lang');

        let result_json = {};
        try {
            result_json = JSON.parse($origSelc.val());
        }catch (e) {

        }

        if(result_json.hasOwnProperty(lang)){
            this.val(result_json[lang]);
        }


        return this;
    };


    $.fn.copyContent_Wysihtml5 = function() {

        let attr = this.attr('data-field');
        let $origSelc = $('[name='+attr+']');
        let lang = this.attr('data-current-lang');

        if(typeof attr == "undefined")
            return;

        let result_json = {};
        try {
            result_json = JSON.parse($origSelc.val());
        }catch (e) {

        }

        if(result_json.hasOwnProperty(lang)){
            this.html(result_json[lang]);
        }else{
            this.html("");
        }

        console.log(result_json);

        return this;
    };




    var decodeEntities = (function() {
        // this prevents any overhead from creating the object each time
        var element = document.createElement('div');

        function decodeHTMLEntities (str) {
            if(str && typeof str === 'string') {
                // strip script/html tags
                str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
                str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
                element.innerHTML = str;
                str = element.textContent;
                element.textContent = '';
            }

            return str;
        }

        return decodeHTMLEntities;
    })();


    function buildLanguageSelector(attrValue){
        let languages = get_languages();
        let html = "<select data-field='"+attrValue+"'>";
        for (let key in languages)
            html = html+"<option value='"+languages[key]+"'>"+languages[key].toUpperCase()+"</option>";
        return  html+"</select>"
    }

    //setup fields

    $( "input[data-field-translator=true], textarea[data-field-translator=true]" ).each(function( index ) {

        let languages = get_languages();
        let default_language = languages[0];

        let $this = $(this);

        let new_input_html = "";

        if ($this.is("input")) {

            let attr_class = $this.attr('class');
            let attr_name = $this.attr('name');
            let attr_type = $this.attr('type');
            let attr_placeholder = $this.attr('placeholder');

            if(typeof attr_placeholder === "undefined")
                attr_placeholder = "";

            let attr_style = $this.attr('style');

            if(typeof attr_style === "undefined"){
                attr_style = "";
            }

            //build custom input
            new_input_html = "<input type=\""+attr_type+"\" " +
                "placeholder='"+attr_placeholder+"' " +
                "class='field-translator field-translator-"+attr_name+" "+attr_class+"' " +
                "name='field-translator-"+attr_name+"' " +
                "data-field='"+attr_name+"'"+
                "data-current-lang='"+default_language+"'"+
                "style='"+attr_style+"'>";


            new_input_html =  new_input_html + buildLanguageSelector(attr_name);
            $this.after("<div class='field-translator-container'>"+new_input_html+"</div>").hide();
            $('.field-translator-'+attr_name).attr("data-current-lang",default_language).val($this.val()).localized();


        } else if ($this.is("textarea")) {

            let attr_class = $this.attr('class');
            let attr_name = $this.attr('name');
            let attr_placeholder = $this.attr('placeholder');
            let attr_style = $this.attr('style');

            if(typeof attr_placeholder === "undefined")
                attr_placeholder = "";

            if(typeof attr_style === "undefined"){
                attr_style = "";
            }

            new_input_html = "<textarea " +
                "placeholder='"+attr_placeholder+"' " +
                "class='field-translator field-translator-"+attr_name+" "+attr_class+"' " +
                "name='field-translator-"+attr_name+"'" +
                "data-field='"+attr_name+"'"+
                "data-current-lang='"+default_language+"'"+
                "style='"+attr_style+"'></textarea>";

            new_input_html =  new_input_html + buildLanguageSelector(attr_name);
            $this.after("<div class='field-translator-container'>"+new_input_html+"</div>").hide();

            $('.field-translator-'+attr_name).attr("data-current-lang",default_language).val($this.val()).localized();
            $('.field-translator-'+attr_name).wysihtml5({
                "image": false,
                "link": false,
                "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
                "emphasis": true, //Italics, bold, etc. Default true
                "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
                "html": false, //Button which allows you to edit the generated HTML. Default false
                "color": false, //Button to change color of font
                events: {
                    load: function() {

                        $('.wysihtml5-sandbox').contents().find('body'+'.field-translator-'+attr_name).on("keyup",function(event) {
                            let currentLang = $(this).attr("data-current-lang");
                            let attr = $(this).attr("data-field");
                            $('.field-translator-'+attr).saveLocalizedContent()
                                .attr('data-current-lang',currentLang).copyContent();

                        }).attr("data-field",attr_name).attr("data-current-lang",default_language);
                    }

                }
            });


        }

    });



    $('.field-translator-container select').on('change',function () {

        var $this = $(this);
        let synced_value = $this.val();

        $( ".field-translator-container select" ).each(function( index ) {
            let val = $(this).val();
            if(val !== synced_value){
                $(this).val(synced_value).trigger('change');
            }
        });

        let attr = $this.attr('data-field');


        //switch input language translate it
        $('.field-translator-container .field-translator-'+attr).saveLocalizedContent()
            .attr('data-current-lang',synced_value).copyContent();

        //switch textarea (using wysihtml5) language translate it
        $('.wysihtml5-sandbox').contents().find('body'+'.field-translator-'+attr).attr("data-current-lang",synced_value).copyContent_Wysihtml5();

    });

    $('.field-translator').keyup(function () {
        let currentLang = $(this).attr("data-current-lang");
        let attr = $(this).attr("data-field");
        $('.field-translator-container .field-translator-'+attr).saveLocalizedContent()
            .attr('data-current-lang',currentLang).copyContent();
    });


    function get_languages(){

        var this_js_script = $('script[data-languages]'); // or better regexp to get the file name..
        var languages = this_js_script.attr('data-languages');
        if (typeof languages === "undefined" ) {
             languages = ["en"];
        }else{
            languages = languages.split(',');
        }

        return languages;
    }




