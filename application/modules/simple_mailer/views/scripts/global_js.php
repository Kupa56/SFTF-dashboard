<script>
    /*$(".html-editor").wysihtml5({
        "image": false
    });*/
</script>

<script src="<?=TEMPLATE_SKIN_URL."/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.js"?>"></script>
<script>

    $("textarea.html-editor").wysihtml5({
        "image": false,
        "link": false,
        "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
        "emphasis": true, //Italics, bold, etc. Default true
        "lists": true, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
        "html": false, //Button which allows you to edit the generated HTML. Default false
        "color": false //Button to change color of font
    });

</script>


