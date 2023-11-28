
var $sport5 = $("#posdoc_programasp");

$(document).ready(function(){

    $sport5.change(function(){

        var $form5 = $(this).closest('form');

        // Simulate form data, but only include the selected sport value.
        var data5 = {};
        data5[$sport5.attr("name")] = $sport5.val();
        // Submit data via AJAX to the form's action path.

        $('#posdoc_programa').attr('readonly', true);

        if ($sport5.val()==='Otro') {
            $.ajax({
                url: $form5.attr('action'),
                type: $form5.attr('method'),
                data: data5,
                success: function (html) {

                    $("#posdoc_programa").replaceWith(
                        // ... with the returned one from the AJAX response.
                        $(html).find("#posdoc_programa")

                        //$("select:first").replaceWith("Hello world!"
                    );

                    $('#posdoc_programa').attr('readonly', false);

                    // Position field now displays the appropriate positions.
                }
            });
        }
    });
});